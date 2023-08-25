<?php

namespace App\Http\Controllers;

use App\TransportStatusReport;
use App\Transport;
use App\Deletion;
use Spatie\MediaLibrary\Models\Media;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;

use Illuminate\Http\Request;

class jsonth
{
  public $src;
  public $thumbnail;

  function __construct($_src, $_thumb)
  {
    $this->src = $_src;
    $this->thumbnail = $_thumb;
  }
}

class TransportStatusReportController extends Controller
{

  public function filter(Request $req)
  {
    if ($req->VAT === "0") $tr = TransportStatusReport::orderBy('updated_at', 'desc')->get();
    else $tr = TransportStatusReport::where('VAT', $req->VAT)->orderBy('updated_at', 'desc')->get();
    if ($req->date != null) $tr = $tr->filter(function ($item) use ($req) {
      return stristr($item->created_at, $req->date);
    });

    foreach ($tr as $rep) $rep->populate();

    //DISABLED
    //filters didn't work because after the Pagination Integration, all reports was moved into data branch inside reports branch.
    // return response()->json(['reports' => $tr]);
    return response()->json($tr);
  }

  public function calendarFind(Request $req, $delbool)
  {
    $tr = TransportStatusReport::where('VAT', $req->VAT)->where('created_at', 'LIKE', $req->date . '%')->get()->first();
    if ($delbool == "true") {
      $newDel = Deletion::create();
      $media = TransportStatusReport::mediaFromId($tr->id);
      foreach ($media as $file) {
        $file->move($newDel);
      }
      $tr->delete();
      return response(200);
    }

    return response($tr);
  }

  public function image(Request $req)
  {
    $file = $req->file("file");
    $id = $req->reportID;
    $uuid = $req->uuid;

    $report = TransportStatusReport::find($id);
    $med = $report->addMedia($file->getRealPath())
      ->sanitizingFileName(function ($filename) {
        return $filename . ".file";
      })
      ->preservingOriginal()
      ->toMediaCollection($uuid);
    return response(200);
  }

  public function thumbs(Request $req)
  {
    $media = TransportStatusReport::mediaFromID($req->id);
    $json = collect();

    foreach ($media as $file) {
      $size = getimagesize($file->getPath('jpg'));
      // dd(getimagesize($file->getPath('jpg')));
      // $json[$key] = new jsonth($file->getUrl('jpg'), $file->getUrl('thumb'));
      // $img = Image::make($file->getUrl('jpg'));

      $json->push([
        'src' => $file->getUrl('jpg'),
        'w' => $size[0],
        'h' => $size[1],
        'thumbnail' => $file->getUrl('thumb')
      ]);
    }
    //return response(json_encode(array_values($json)));
    return response($json);

    //return response(json_encode(array_values($json)));
  }

  public function imgDelete(Request $req)
  {
    $id = $req->id;
    $uuid = $req->file["uuid"];
    $report = TransportStatusReport::find($id);
    $report->clearMediaCollection($uuid);
    return response(200);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function tableUpdate(Request $req)
  {
    $reports = TransportStatusReport::where('updated_at', '>', Carbon::parse($req->timestamp))->get();
    foreach ($reports as $report) $report->populate();
    return response()->json(['updated' => $reports, 'timestamp' => Carbon::parse($req->timestamp)]);
  }

  public function table()
  {
    // LIMITED SOLUTION: kolkas paginatinu kas 2000 (irasu DB yra apie 1500). 
    // Reiktu padaryti:
    // 0) sugalvoti, kaip optimizuoti sistemos krovimo greiti
    // 1) filtre option, kad leistu pasirinkti kiek irasu rodyti viename puslapyje
    // 2) pagination mygtukus apacioj, jeigu irasai netelpa pagal nurodyta skaiciu
    // 3) kalendoriuje turi atsirasti visi esame irasai, vadinasi kalendoriaus data turi buti imami is kitos funkcijos ir tas data turetu buti be nuotrauku
    // 4) kazkas yra blogai su thumbnails po atnaujinimo
    // INTERESTING 
    // nauju thumnails url sutampa su senu, bet kazkodel kai requestinama naujo tipo thumbnail, sistema redirectina i home page
    // I Š S I A I Š K I N T A 
    // nauju thumbnailu file gale nera zodzio 'XXXX-thumb', yra 'XXXX-jpg'. Itariu, kad conversionai yra tos pacios rezoliucijos
    // $reports = TransportStatusReport::orderBy('updated_at', 'desc')->paginate(100);
    $reports = TransportStatusReport::orderBy('created_at', 'desc')->paginate(100);


    //changing 'pending' status to 'ok'
    foreach ($reports as $report)
    {
      //tikrina, ar ataskaita turi priskirtas nuotraukas
      if($report->media != null)
      { 
        $seconds_elapsed = abs(strtotime(date("Y-m-d")) - strtotime($report->created_at));
        $days_elapsed = $seconds_elapsed / (60*60*24);
          if($days_elapsed > 1)
          {
            $report->status = 0;
            $report->save();
          }
      }
    }

    //$reports->orderBy('created_at', 'desc');


    foreach ($reports as $report) $report->populate();
    return response()->json(['reports' => $reports]);
    //return response()->json($reports);
  }

  public function calendar()
  {
    $reports = TransportStatusReport::orderBy('updated_at', 'desc')->paginate(2000);
    $calendarDATA = collect();
    foreach ($reports as $key => $report) {


      
      //generating data for front-end calendar
      if ($report->status != 0) continue;
      $calendarDATA->push(
        [
          'id' => $report->id,
          'title' => $report->VAT,
          'calendarId' => 1,
          'category' => 'allday',
          'dueDateClass' => '',
          'start' => $report->created_at,
          'end' => $report->created_at
        ]
      );
    }
    return response()->json(['calendar' => $calendarDATA]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  public function download(Request $req)
  {
    return TransportStatusReport::find($req->id)->downloadThisMedia();
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'VAT' => 'required|max:255',
      'created_at' => 'required|date',
      'driver' => 'required',
    ]);
    // $path = $request->file('avatar')->store(
    //     'avatars/'.$request->user()->id
    // );
    if ($request->id == -1) {
      $new = TransportStatusReport::create(["VAT" => $request->VAT, "driver" => $request->driver, "created_at" => $request->created_at, "comments", $request->comments]);
    } else {



      $new = TransportStatusReport::find($request->id);
      $new->driver = $request->driver;
      $new->created_at = $request->created_at;
      $new->VAT = $request->VAT;
      $new->comments = $request->comments;
      $new->save();
    }
    return response()->json(['status' => "OK", 'id' => $new->id]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\TransportStatusReport  $transportStatusReport
   * @return \Illuminate\Http\Response
   */
  public function show(TransportStatusReport $transportStatusReport)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\TransportStatusReport  $transportStatusReport
   * @return \Illuminate\Http\Response
   */
  public function edit(TransportStatusReport $transportStatusReport)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\TransportStatusReport  $transportStatusReport
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $validatedData = $request->validate([
      'driver' => 'required',
    ]);
    if (!$validatedData) return response()->json(['status' => 'FAILED']);

    $tr = TransportStatusReport::find($request->reportID);
    $tr->driver = $request->driver;
    $tr->comments = $request->comments;
    $tr->VAT = $request->VAT;
    $tr->save();
    return response()->json(['status' => 'OK']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\TransportStatusReport  $transportStatusReport
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $req)
  {
    $newDel = Deletion::create();
    if ($req->save) {
      $media = TransportStatusReport::mediaFromId($req->id);
      foreach ($media as $file) {
        $file->move($newDel);
      }
    }
    TransportStatusReport::find($req->id)->delete();
    return response(200);
  }

  public function test()
  {
    $all = TransportStatusReport::whereRaw("UPPER(`VAT`) = '" . strtoupper('hgf123') . "'")->get();
    foreach ($all as $val) {
      if (Carbon::create($val->created_at->toDateTimeString())->format('Y-m-d') == Carbon::today()->format('Y-m-d')) dd(false);
    }
    dd(true);
  }


  public function trag()
  {
    $link = file_get_contents("http://vps1.salune.sprucebird.lt/trag");
    $obj = json_decode($link);
    foreach ($obj as $key => $img) {
      if ($key >= 5) return;
      echo ($img->id);
      echo ("\n");
      $tr = TransportStatusReport::where('id', $img->id)->first();
      $tr->addMediaFromUrl($img->path)->toMediaCollection();
    }
    return response("Finished");
  }
}
