<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transport extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

	protected $fillable = ['VAT', 'manufacturer', 'model', 'rlYear', 'category', 'status', 'driver_id', 'VIN', 'tchExpirationDate', 'user_id', 'deleted_at'];
    //
}
