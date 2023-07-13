<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Transport;
use App\Viber;
use App\Images;
use App\TransportStatusReport;

use Spatie\MediaLibrary\Models\Media;

use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function get_active_transport()
    {
        $transport = Transport::where('deleted_at', null)->get();
        $trCount = Transport::where('deleted_at', null)->count();
        return response()->json(['Transport' => $transport, 'count' => $trCount]);
    }
    public function get_active_reports_last_month($period)
    {
      $AtCount = TransportStatusReport::whereMonth('created_at', '=', Carbon::now()->subDays($period))->count();
      return response()->json(['count' => $AtCount]);
    }
    public function get_active_reports(Request $req)
    {

        $reports = TransportStatusReport::all();
        $viber = Viber::count();
        $media = Media::where('model_type', '!=', 'App\Deletion')->count();
        $AtCount = TransportStatusReport::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        if($req->input('type') == 2)
        {
          $tr = Transport::where('category', 3)->get();
        }else{
          $tr = Transport::where('category', 2)->get();
        }
        $months_output = collect();
        $vat_output = [];
        //Reports count by month
        foreach ($tr as $t) {
          for($i = 1; $i <= 11; $i++)
          {
            $num_string = "0" + (string)$i;
            $vat_output[$i-1] = DB::table('transport_status_reports')->whereMonth('created_at', '=', $num_string)->where('VAT', $t->VAT)->count();
          }
          $months_output->push([
            'name' => $t->VAT,
            'data' => $vat_output
          ]);
        }


        return response()->json(['Transport' => $months_output, 'count' => $AtCount, 'Viber' => $viber, 'media' => $media]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
