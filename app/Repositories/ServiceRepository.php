<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Service;
use Auth;


class ServiceRepository
{


	public function getData()
	{
		return Service::all();
	}
	

	public function getDetail($request)
	{
		$service = Service::find($request->id);
		return response()->json([
			'service' => $service ,
		]);
	}

	public function create($request)
	{

		$service = Service::create([
			'name' => $request->name,
			'price' => $request->price,
			'description' => $request->description,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]);

		return $service;
	}


	public function update($request, $service)
	{

		return $service->update([
			'name' => $request->name,
			'price' => $request->price,
			'description' => $request->description,
			'updated_by' => Auth::user()->id,
		]);

	}

	public function destroy($service)
	{

		$name = $service->name;
		if($service->delete()){
			return $name ;
		}

	}

}