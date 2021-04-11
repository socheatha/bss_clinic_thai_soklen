<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Labor;

class LaborDetail extends Model
{
	protected $fillable = [
		'name', 'result', 'unit', 'description', 'labor_id', 'category_id', 'service_id', 'created_by', 'updated_by',
	];

	protected $table = 'labor_details';
	
	public function service(){
		return $this->belongsTo(LaborService::class,'service_id');
	}
	
	public function labor(){
		return $this->belongsTo(Labor::class,'labor_id');
	}
}
