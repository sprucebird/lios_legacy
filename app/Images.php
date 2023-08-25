<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
	protected $fillable = [
		'file_name', 'file_path', 'model', 'resource_id', 'type', 'file_extension'
	];
}
