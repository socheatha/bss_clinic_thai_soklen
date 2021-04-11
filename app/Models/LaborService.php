<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class LaborService extends BaseModel
{


	protected $table = 'labor_services';

	protected $fillable = [
		'name', 'default_value', 'unit', 'ref_from', 'ref_to', 'description', 'category_id', 'created_by', 'updated_by',
	];


	public static function getSelectService($id = [])
	{
		$collection = parent::orderBy('name', 'asc')->orWhereIn('id', $id)->get();
		$items = [];
		foreach ($collection as $model) {
				$items[$model->id] = $model->name;
		}
		return $items;
	}

  
	public function category(){
		return $this->belongsTo(LaborCategory::class,'category_id');
	}


}
