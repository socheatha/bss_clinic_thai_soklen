<?php

namespace App\Http\Controllers\Echoes;

use App\Http\Controllers\Controller;
use App\Http\Requests\EchoesRequest;
use App\Repositories\EchoesRepository;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Patient;
use App\Models\Echoes;
use App\Models\EchoDefaultDescription;
use App\Models\Province;
use App\Models\District;
use Auth;
use Hash;
use Validator;
use Artisan;

class EchoesController extends Controller
{
    
	protected $echoes;

	public function __construct(EchoesRepository $repository)
	{
			$this->echoes = $repository;
	}

	public function getDatatable(Request $request, $type)
	{
		return $this->echoes->getDatatable($request, $type);
	}

	public function index($type)
	{
		
		$echo_default_description = EchoDefaultDescription::where('slug', $type)->first();
		if ($echo_default_description != null) {
			$this->data = [
				'type' => $type,
				'echo_default_description' => $echo_default_description,
			];
	
			return view('echoes.index', $this->data);
		}
		
	}

	public function print($type, Echoes $echoes)
	{

		$this->data = [
			'echoes' => $echoes,
			'echoes_preview' => $this->echoes->getEchoesPreview($echoes->id)->getData()->echoes_detail,
		];

		return view('echoes.print', $this->data);
	}

	public function create($type)
	{
		$echo_default_description = EchoDefaultDescription::where('slug', $type)->first();
		if ($echo_default_description != null) {
			$this->data = [
				'provinces' => Province::getSelectData(),
				'districts' => [],
				'echo_default_description' => $echo_default_description,
				'type' => $type,
			];
			return view('echoes.create', $this->data);
		}
	}


	public function store(EchoesRequest $request, $type)
	{
		// Define Upload Image Path
		$path = public_path().'/images/echoes/';

		if ($request->file('image')) {
			$validator = Validator::make($request->all(), [
				'image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
			]);
			if ($validator->fails()) {
				// Redirect Back
				return back()->withErrors($validator)->withInput();
			}
		}
		$echoes = $this->echoes->create($request, $path, $type);
		if ($echoes){
			// Redirect
			return redirect()->route('echoes.edit', [$type, $echoes->id])
					->with('success', __('alert.crud.success.create', ['name' => Auth::user()->module()]));
		}
	}

	public function edit($type, Echoes $echoes)
	{

		$echo_default_description = EchoDefaultDescription::where('slug', $type)->first();
		if ($echo_default_description != null) {
			$this->data = [
				'provinces' => Province::getSelectData(),
				'districts' => $echoes->pt_province_id && $echoes->pt_district_id ? $echoes->province->getSelectDistrict() : []	,
				'echo_default_description' => $echo_default_description,
				'echoes_preview' => $this->echoes->getEchoesPreview($echoes->id)->getData()->echoes_detail,
				'echoes' => $echoes,
				'type' => $type,
			];
			return view('echoes.edit', $this->data);
		}
	}


	public function update(Request $request, $type, Echoes $echoes)
	{
		// Define Upload Image Path
		$path = public_path().'/images/echoes/';
		if ($this->echoes->update($request, $echoes, $path)){
			// Redirect
			return redirect()->route('echoes.edit', [$type, $echoes->id])
					->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]));
		}
	}


	public function destroy(Request $request, $type, Echoes $echoes)
	{
		// Define Upload Image Path
		$path = public_path().'/images/echoes/';
		// Redirect
		return redirect()->route('echoes.index', $type)
				->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . $this->echoes->destroy($request, $echoes, $path));
	}


}
