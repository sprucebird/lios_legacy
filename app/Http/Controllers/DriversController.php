<?php

namespace App\Http\Controllers;

use App\drivers;
use App\Viber;
use Illuminate\Http\Request;

class DriversController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $drivers = Viber::all();
    foreach ($drivers as $driver) {
      $table = drivers::where("viber_token", $driver->viberId)->first();
      if ($table != null) {
        $driver->verified = true;
        $driver->name = $table->name;
        $driver->phone = $table->phone_number;
      } else $driver->verified = false;
    }
    return response()->json(['drivers' => $drivers]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
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
      'name' => 'required',
    ]);
    if (!$validatedData) return response()->json(['status' => 'VALIDATION']);
    $request->deleted_at = '2000-01-01';
    $new = drivers::create($request->all());
    return response()->json(['status' => 'OK']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\drivers  $drivers
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $driver = Viber::find($id);
    $table = drivers::where("viber_token", $driver->viberId)->first();
    if ($table != null) {
      $driver->name = $table->name;
      $driver->phone = $table->phone_number;
      $driver->id = $table->id;
    }
    return response()->json(['driver' => $driver]);
  }

  public function verify(Request $req)
  {
    $driver = new drivers;
    $driver->name = $req->name;
    $driver->viber_token = $req->viber;
    $driver->save();
    return response()->json(['status' => '200']);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\drivers  $drivers
   * @return \Illuminate\Http\Response
   */
  public function edit(drivers $drivers)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\drivers  $drivers
   * @return \Illuminate\Http\Response
   */
  public function update($id, Request $request)
  {
    $validatedData = $request->validate([
      'name' => 'required',
    ]);
    if (!$validatedData) return response()->json(['status' => 'VALIDATION']);
    $new = drivers::find($id);
    $new->name = $request->name;
    $new->phone_number = $request->phone;
    $new->save();
    return response()->json(['status' => 'OK']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\drivers  $drivers
   * @return \Illuminate\Http\Response
   */
  public function destroy(drivers $drivers)
  {
    //
  }

  public function errors(Request $req)
  {
    if ($req->err == null) {
      $dr = Viber::where("VAT", "######")->where("inProgress", 1)->get();
      return response($dr);
    } else {
      $sat = Viber::where("id", $req->err)->first();
      $sat->inProgress = 0;
      $sat->save();
      return response(200);
    }
  }
}
