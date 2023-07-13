<?php

namespace App\Http\Controllers;

use App\Transport;
use Illuminate\Http\Request;
use App\TransportStatusReport;
use Carbon\Carbon;

class TransportController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['transport' => Transport::all()]);
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

    public function except(Request $req) {
      $all = Transport::all();
      if($req->created_at == null) $date = Carbon::today('Europe/Vilnius');
      else $date = Carbon::create($req->created_at);
      $rep = TransportStatusReport::whereYear('created_at', '=', $date->year)->whereMonth('created_at', '=', $date->month)->whereDay('created_at', '=', $date->day)->get();
      foreach ($all as $key => $value) {
        foreach ($rep as $check) {
          if($value->VAT == $check->VAT) unset($all[$key]);
        }
      }
      return response()->json(['transport' => $all]);
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
            'manufacturer' => 'required',
        ]);
        Transport::create($request->all());
        return response()->json(['status' => 'OK']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(['transport' => Transport::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function edit(Transport $transport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $Transport = Transport::find($id);
        $Transport->VAT = $request->input('VAT');
        $Transport->manufacturer = $request->input('manufacturer');
        $Transport->model = $request->input('model');
        $Transport->save();
        return response()->json(['status' => 'OK']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transport  $transport
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Transport::find($id)->softDeletes();
    }
}
