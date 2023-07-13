<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class drivers extends Model
{
  protected $fillable = ['name', 'viber_token', 'current_transport', 'deleted_at'];

  protected $attributes = [
        'deleted_at' => '1990-01-01'
  ];
}
