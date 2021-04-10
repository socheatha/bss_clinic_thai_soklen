<?php

namespace App\Http\Controllers\Labor;

use App\Models\Medicine;
use App\Models\Service;
use App\Models\Patient;
use App\Models\Labor;
use App\Models\LaborDetail;
use App\Models\LaborCategory;
use App\Http\Requests\LaborRequest;
use App\Http\Requests\LaborDetailRequest;
use App\Http\Requests\LaborDetailUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\LaborRepository;
use App\Models\Province;
use App\Models\District;
use Auth;

class LaborController extends Controller
{
	
	protected $labor;

	public function __construct(LaborRepository $repository)
	{
		$this->labor = $repository;
	}

	public function getDatatable(Request $request)
	{
		return $this->labor->getDatatable($request);
	}

	public function getLaborServiceCheckList(Request $request)
	{
		return $this->labor->getLaborServiceCheckList($request);
	}

	public function getCheckedServicesList(Request $request)
	{
		return $this->labor->getCheckedServicesList($request);
	}

	public function index()
	{
		return view('labor.index');
	}


	public function create()
	{
		$this->data = [
			'provinces' => Province::getSelectData(),
			'districts' => [],
			'labor_number' => $this->labor->labor_number(),
			'services' => Service::select('id', 'name', 'price', 'description')->orderBy('name' ,'asc')->get(),
			'categories' => LaborCategory::getSelectData('id', 'name', '', 'id' ,'asc'),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
			'labor_type' => $_REQUEST['labor_type'] ?? 1,
		];
		return view('labor.create', $this->data);
	}


	public function store(LaborRequest $request)
	{
		
		$labor = $this->labor->create($request);
		if ($labor) {
			// Redirect
			return redirect()->route('labor.edit', $labor->id)
				->with('success', __('alert.crud.success.create', ['name' => Auth::user()->module()]) . $request->date);
		}
	}

	public function save_order(Request $request, Labor $labor)
	{
		if ($this->labor->save_order($request)) {
			// Redirect
			return redirect()->route('labor.edit', $labor->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]));
		}
	}

	public function show(Labor $labor)
	{
		//
	}

	
	public function getItemDetail(Request $request)
	{
		return response()->json([ 'labor_detail' => LaborDetail::find($request->id) ]);
	}

	public function get_edit_detail(Request $request)
	{
		return $this->labor->get_edit_detail($request->id);
	}

	public function deleteLaborDetail(Request $request)
	{
		return $this->labor->deleteLaborDetail($request);
	}
	
	public function storeAndGetLaborDetail (Request $request)
	{
		return $this->labor->storeAndGetLaborDetail($request);
	}

	public function getLaborPreview(Request $request)
	{
		return $this->labor->getLaborPreview($request->id);
	}

	public function labor_detail(Labor $labor)
	{
		$this->data = [
			'labor' => $labor,
			'services' => Service::getSelectService($labor->service_id),
			'labor_preview' => $this->labor->getLaborPreview($labor->id),
		];

		return view('labor.labor_detail', $this->data);
	}

	public function getReport(Request $request)
	{
		return $this->labor->getReport($request);
	}
	public function report ()
	{
		$this->data = [

		];
		return view('labor.report', $this->data);
	}

	public function edit(Labor $labor)
	{

		$this->data = [
			'labor' => $labor,
			'provinces' => Province::getSelectData(),
			'categories' => LaborCategory::getSelectData('id', 'name', '', 'id' ,'asc'),
			'districts' => $labor->pt_province_id && $labor->pt_district_id ? $labor->province->getSelectDistrict() : [],
			'medicines' => Medicine::getSelectData('id', 'name', '', 'name' ,'asc'),
			'services' => Service::select('id', 'name', 'price', 'description')->orderBy('name' ,'asc')->get(),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
			'labor_preview' => $this->labor->getLaborPreview($labor->id)->getData()->labor_detail,
			'labor_type' => $labor->labor_type,
		];

		return view('labor.edit', $this->data);
	}

	public function print(Labor $labor)
	{

		$this->data = [
			'labor' => $labor,
			'labor_preview' => $this->labor->getLaborPreview($labor->id)->getData()->labor_detail,
		];

		return view('labor.print', $this->data);
	}

	public function update(LaborRequest $request, Labor $labor)
	{
		if ($this->labor->update($request, $labor)) {

			// Redirect
			return redirect()->route('labor.edit', $labor->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]));
		}
	}

	public function status(Request $request)
	{
		return $this->labor->status($request);
	}


	public function destroy(Request $request, Labor $labor)
	{
		// Redirect
		return redirect()->route('labor.index')
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . str_pad(($this->labor->destroy($request, $labor)), 6, "0", STR_PAD_LEFT));
	}

	public function labor_detail_destroy(LaborDetail $labor_detail)
	{
		// Redirect
		return redirect()->route('labor.edit', $labor_detail->labor_id)
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . $this->labor->destroy_labor_detail($labor_detail));
	}
}
