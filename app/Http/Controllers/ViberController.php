<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\TransportStatusReport;

use Viber\Bot;
use Viber\Api\Sender;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Viber;
use App\drivers;
use App\Transport;
use Viber\Client;

use App\Events\update;

use \App\Jobs\vtime;

class ViberController extends Controller
{
  public function __construct()
  {
    $this->middleware('api');
  }


  public function respond(Request $req)
  {
    $botSender = new Sender([
      'name' => 'Transporto būklė | LIOS',
    ]);

    $apiKey = config('viberbot.api-key');


    try {
      $bot = new Bot(['token' => $apiKey]);
      $bot
        ->onConversation(function ($event) use ($bot, $botSender) {
          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("Prisijungta.")
          );
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }
        }) //pokalbio pradzia
        ->onPicture(function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }


          $user = Viber::where('viberId', $event->getSender()->getId())->first();
          $user->name = drivers::where("viber_token", $user->viberId)->first()->name;
          if ($user->inProgress) {
            $rep = TransportStatusReport::where('VAT', $user->VAT)->where('created_at', 'LIKE', Carbon::today() . '%')->where('driver', $user->name)->first();
            if ($rep == null) {
              $user->inProgress = false;
              $user->save();
              $bot->getClient()->sendMessage(
                (new \Viber\Api\Message\Text())
                  ->setSender($botSender)
                  ->setReceiver($event->getSender()->getId())
                  ->setText("Įvyko sistemos klaida. Susisiekite su administracija.")
              );
              return;
            }
            $rep->addMediaFromUrl($event->getMessage()->getMedia())->toMediaCollection();
            $rep->updated_at = Carbon::now();
            $rep->save();
            $this->callReload();
          } else {
            $user->inProgress = true;
            $user->VAT = "######";
            $tr = new TransportStatusReport;
            $tr->status = 2;
            $tr->VAT = "######";
            $tr->driver = drivers::where("viber_token", $user->viberId)->first()->name;
            $tr->created_at = Carbon::today();
            $tr->save();
            $tr->addMediaFromUrl($event->getMessage()->getMedia())->toMediaCollection();
            $user->save();
            vtime::dispatch(
              $botSender,
              $event->getSender()->getId(),
              "Įveskite valstybinį numerį, norėdami baigti pildyti ataskaitą.",
              Viber::where('viberId', $event->getSender()->getId())->first()->updated_at,
              true,
              $tr->id
            )
              ->delay(now()->addMinutes(1));
            $this->callReload();
          }
        }) //kai bot'as gauna nuotrauka
        ->onText("/\b[a-zA-Z]{3}\d{3}\b|\b[a-zA-Z]{2}\d{3}\b/", function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }

          preg_match("/\b[a-zA-Z]{3}\d{3}\b|\b[a-zA-Z]{2}\d{3}\b/", $event->getMessage()->getText(), $matches);
          $vat = array_shift($matches);
          $user = Viber::where('viberId', $event->getSender()->getId())->first();
          $user->name = drivers::where("viber_token", $user->viberId)->first()->name;


          if ($user->inProgress && $user->VAT != "######" && $user->VAT == strtoupper($vat)) {
            $bot->getClient()->sendMessage(
              (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText("Pakartotinai valstybinio numerio vesti nereikia.")
            );
            return;
          } elseif ($user->VAT != strtoupper($vat) && $user->VAT != "######" && $user->inProgress) {
            $rep = TransportStatusReport::where('VAT', $user->VAT)->where('created_at', 'LIKE', Carbon::today() . '%')->where('driver', $user->name)->first();
            $rep->status = 0;
            $rep->save();
            $user->inProgress = false;
          }

          if (!$this->isvat($vat)) {
            $bot->getClient()->sendMessage(
              (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText("Valstybinis numeris " . strtoupper($vat) . " sistemoje nėra registruotas. Bandykite dar kartą arba susisiekite su administracija.")
            );
            $user->save();
            return;
          }

          // if(!$this->isdate($vat)) {
          //   $bot->getClient()->sendMessage(
          //     (new \Viber\Api\Message\Text())
          //     ->setSender($botSender)
          //     ->setReceiver($event->getSender()->getId())
          //     ->setText("Automobilio valstybiniu numeriu ".strtoupper($vat)." ataskaita šiandien jau registruota.")
          //   );
          //   $user->save();
          //   return;
          // }

          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("Rastas valstybinis numeris " . strtoupper($vat))
          );



          if ($user->inProgress) {
            $user->VAT = strtoupper($vat);

            $rep = TransportStatusReport::where('VAT', "######")->where('created_at', 'LIKE', Carbon::today() . '%')->where('driver', $user->name)->first();
            if ($rep == null) {
              $bot->getClient()->sendMessage(
                (new \Viber\Api\Message\Text())
                  ->setSender($botSender)
                  ->setReceiver($event->getSender()->getId())
                  ->setText("Įvyko sistemos klaida. Susisiekite su administracija.")
              );
              return;
            }

            $rep->VAT = $user->VAT;
            $user->inProgress = false;
            $rep->status = 0;



            $bot->getClient()->sendMessage(
              (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText("Jūsų ataskaita įrašyta! Ačiū.")
            );
            $rep->save();
            $user->save();
            $this->callReload();
          } else {
            $user->VAT = strtoupper($vat);
            $user->inProgress = true;

            $currentDriver = drivers::where('viber_token', $event->getSender()->getId())->get();
            if (empty($currentDriver)) {
              $newDriver = drivers::where('name', $user->name);
              $newDriver->name = $user->name;
              $newDriver->viber_token = $event->getSender()->getId();
              $newDriver->save();
            }
            $tr = new TransportStatusReport;
            $tr->VAT = $user->VAT;
            $tr->driver = drivers::where("viber_token", $user->viberId)->first()->name;
            $tr->status = 2;
            $tr->created_at = Carbon::today();
            $tr->save();
            $user->save();
            vtime::dispatch(
              $botSender,
              $event->getSender()->getId(),
              "Nuotraukų įkėlimo laikas baigėsi, jūsų ataskaita užpildyta.",
              Viber::where('viberId', $event->getSender()->getId())->first()->updated_at,
              false,
              $tr->id
            )
              ->delay(now()->addMinutes(5));
          }
          $this->callReload();

          return;
        }) //kai bot'as gauna zinute, kurioje yra valstybinis numeris
        ->onText("/^stop$/i", function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }

          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("STOP signalas gautas. Apie tai informuosime administraciją.")
          );
          $user = Viber::where('viberId', $event->getSender()->getId())->first();
          $user->inProgress = false;
          $user->updated_at = now();
          $user->save();
          $this->callReload();
        }) //kai bot'as gauna zinute su uzrasu STOP
        ->onText("/^\/\/.*?/", function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }

          $user = Viber::where('viberId', $event->getSender()->getId())->first();
          $user->name = drivers::where("viber_token", $user->viberId)->first()->name;

          if ($user->inProgress) {
            $tr = TransportStatusReport::where('driver', $user->name)->where('VAT', $user->VAT)->where('created_at', 'LIKE', '%' . Carbon::today()->format('Y-m-d') . '%')->first();
          } else {
            $bot->getClient()->sendMessage(
              (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($event->getSender()->getId())
                ->setText("Komentarą rašykite tik įkėlę nuotraukas arba įrašę valstybinį transporto priemonės numerį.")
            );
            return;
          }

          $tr->comments = explode('//', $event->getMessage()->getText(), 2)[1];
          $tr->save();
          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("Jūsų komentaras įrašytas.")
          );
          $this->callReload();
        }) //kai bot'as gauna kitoti teksta negu nurodyta atvejuose virsuje
        ->onText("/.*?/", function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }

          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("Jūsų žinutės nesupratome. Bandykite dar kartą. Valstybinis numeris turi būti atskirtas nuo kitų žodžių tarpais. PAVYZDYS: ABC123")
          );
        }) //kai bot'as gauna neatpazystama teksta
        ->run();
    } catch (Exception $e) {
      return response($e);
    }
    return response(200);
  }

  public function callReload(Request $req = null)
  {
    event(new update(" "));
    if ($req == null) {
      return;
    }
    return response(200);
  }

  private function unreg($event, $bot, $botSender)
  {
    $bot->getClient()->sendMessage(
      (new \Viber\Api\Message\Text())
        ->setSender($botSender)
        ->setReceiver($event->getSender()->getId())
        ->setText("Jūs esate nepatvirtintas vartotojas ir negalite siųsti ataskaitų. Prašome susisiekti su administracija.")
    );
  }

  public function setweb()
  {
    // $webhookUrl = 'https://salune.sprucebird.co/vapi/viber'; // for exmaple https://my.com/bot.php
    $webhookUrl = config('app.url');
    $webhookUrl .= "/vapi/viber";
    $apiKey = config('viberbot.api-key');
    try {
      $client = new Client(['token' => $apiKey]);
      $result = $client->setWebhook($webhookUrl);
      echo "Success!\n";
    } catch (Exception $e) {
      echo "Error: " . $e->getError() . "\n";
    }
  }

  public function setFuel()
  {
    // $webhookUrl = 'https://salune.sprucebird.co/vapi/viber'; // for exmaple https://my.com/bot.php
    $webhookUrl = config('app.url');
    $webhookUrl .= "/vapi/fuel";
    $apiKey = config('viberbot.api-key-fuel');
    try {
      $client = new Client(['token' => $apiKey]);
      $result = $client->setWebhook($webhookUrl);
      echo "Success!\n";
    } catch (Exception $e) {
      echo "Error: " . $e->getError() . "\n";
    }
  }


  public function fuelRespond(Request $req)
  {
    $botSender = new Sender([
      'name' => 'LIOS | WIP',
    ]);

    $apiKey = config('viberbot.api-key-fuel');

    try {
      $bot = new Bot(['token' => $apiKey]);
      $bot
        ->onConversation(function ($event) use ($bot, $botSender) {

          return (new \Viber\Api\Message\Text())
            ->setSender($botSender)
            ->setText("Can i help you?");
        })
        ->onText('|go .*|si', function ($event) use ($bot, $botSender) {
          if (!$this->checker($event)) {
            $this->unreg($event, $bot, $botSender);
            return;
          }
          $keyboard = $this->keygen();
          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText('Basic keyboard layout')
              ->setKeyboard($keyboard)
          );
        })
        ->onText('|btn-click|s', function ($event) use ($bot, $botSender) {
          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("You clicked the button!")
          );
        })
        ->onText('|btn-sec|s', function ($event) use ($bot, $botSender) {
          $keyboard = $this->keygen();
          $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
              ->setSender($botSender)
              ->setReceiver($event->getSender()->getId())
              ->setText("This is proof.")
              ->setKeyboard($keyboard)
          );
        })
        ->run();
    } catch (Exception $e) {
      // todo - log exceptions
    }
  }




  private function checker($event)
  {
    if (Viber::where('viberId', $event->getSender()->getId())->first() == null) {
      $new = new Viber;
      $new->viberId = $event->getSender()->getId();
      $new->name = $event->getSender()->getName();
      $new->save();
    }
    if (drivers::where('viber_token', $event->getSender()->getId())->first() == null) {
      return false;
    }
    return true;
  }

  private function isvat($vat)
  {
    if (Transport::whereRaw("UPPER(`VAT`) = '" . strtoupper($vat) . "'")->first() == null) {
      return false;
    } else {
      return true;
    }
  }

  private function isdate($vat)
  {
    $all = TransportStatusReport::whereRaw("UPPER(`VAT`) = '" . strtoupper($vat) . "'")->get();
    foreach ($all as $val) {
      if (Carbon::create($val->created_at->toDateTimeString())->format('Y-m-d') == Carbon::today()->format('Y-m-d')) {
        return false;
      }
    }
    return true;
  }

  private function keygen()
  {
    return (new \Viber\Api\Keyboard())
      ->setButtons([
        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('btn-click')
          ->setBgColor("#ffffff")
          ->setColumns(2)
          ->setImage("https://homepages.cae.wisc.edu/~ece533/images/airplane.png")
          ->setRows(2)
          ->setText("<font color=\"#000000\">Tap this button</font>"),

        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('share-phone')
          ->setBgColor("#242222")
          ->setColumns(2)
          ->setRows(2)
          ->setText("<font color=\"#ffffff\">Set phone number</font>"),
        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('btn-click')
          ->setBgColor("#ffffff")
          ->setColumns(2)
          ->setImage("https://homepages.cae.wisc.edu/~ece533/images/airplane.png")
          ->setRows(2)
          ->setText("<font color=\"#000000\">Tap this button</font>"),

        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('btn-sec')
          ->setBgColor("#333333")
          ->setColumns(2)
          ->setRows(2)
          ->setText("<font color=\"#ffffff\">Make it second!</font>"),

        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('btn-click')
          ->setBgColor("#ffffff")
          ->setColumns(2)
          ->setImage("https://homepages.cae.wisc.edu/~ece533/images/airplane.png")
          ->setRows(2)
          ->setText("<font color=\"#000000\">Tap this button</font>"),

        (new \Viber\Api\Keyboard\Button())
          ->setActionType('reply')
          ->setActionBody('btn-sec')
          ->setBgColor("#333333")
          ->setColumns(2)
          ->setRows(2)
          ->setText("<font color=\"#ffffff\">Make it second!</font>"),
      ]);
  }
}
