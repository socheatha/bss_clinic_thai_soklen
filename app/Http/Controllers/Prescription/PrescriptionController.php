<?php

namespace App\Http\Controllers\Prescription;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Http\Requests\PrescriptionRequest;
use App\Http\Requests\PrescriptionDetailRequest;
use App\Http\Requests\PrescriptionDetailUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PrescriptionRepository;
use App\Models\Province;
use App\Models\District;
use App\Models\Usage;
use Auth;

class PrescriptionController extends Controller
{
	
	protected $prescription;

	public function __construct(PrescriptionRepository $repository)
	{
		$this->prescription = $repository;
	}

	public function getDatatable(Request $request)
	{
		return $this->prescription->getDatatable($request);
	}

	public function deletePrescriptionDetail(Request $request)
	{
		return $this->prescription->deletePrescriptionDetail($request);
	}

	public function index()
	{

		$prescriptions = Prescription::all();
		$prescription_description = '';
		foreach ($prescriptions as $i => $prescription) {
			foreach ($prescription->prescription_details as $j => $prescription_detial) {
				if ($prescription->prescription_details->count() > 1) {
					$prescription_description .= str_replace(' on ' . date('M-D', strtotime($prescription_detial->prescription->date)), "", strip_tags($prescription_detial->description, "</p>")) . ', ';
				} else {

					$prescription_description = str_replace(' on ' . date('M-D', strtotime($prescription_detial->prescription->date)), "", strip_tags($prescription_detial->description, "</p>"));
				}
			}
		}
		$from = '2020-05-01';
		$to = '2020-05-31';
		$prescription_ids = Prescription::whereBetween('date', [$from, $to])->select('id');
		$this->data = [
			'prescription_description' => $prescription_description,
			'prescription_details' => PrescriptionDetail::whereIn('medicine_id', ['59', '60', '61', '62', '63', '64', '65'])->whereIn('prescription_id', $prescription_ids)->get(),
		];

		return view('prescription.index', $this->data);
	}


	public function create()
	{
		$this->data = [
			'provinces' => Province::getSelectData(),
			'districts' => [],
			'code' => $this->prescription->code(),
			'medicines' => Medicine::getSelectData('id', 'name', '', 'name' ,'asc'),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
			'usage' => Usage::getSelectData('id', 'name', '', 'name' ,'asc'),
		];
		return view('prescription.create', $this->data);
	}


	public function store(PrescriptionRequest $request)
	{
		$prescription = $this->prescription->create($request);
		if ($prescription) {
			// Redirect
			return redirect()->route('prescription.edit', $prescription->id)
				->with('success', __('alert.crud.success.create', ['name' => Auth::user()->module()]) . $request->date);
		}
	}

	public function save_order(Request $request, Prescription $prescription)
	{
		if ($this->prescription->save_order($request)) {
			// Redirect
			return redirect()->route('prescription.edit', $prescription->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]) . str_pad($prescription->code, 6, "0", STR_PAD_LEFT));
		}
	}

	public function show(Prescription $prescription)
	{
		//
	}

	
	public function getItemDetail(Request $request)
	{
		return response()->json([ 'prescription_detail' => PrescriptionDetail::find($request->id) ]);
	}

	public function prescriptionDetailStore(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'medicine_name' => 'required',
			'medicine_usage' => 'required',
		]);
		
		if ($validator->fails())
		{
				return response()->json(['errors'=>$validator->errors()]);
		}
		return $this->prescription->prescriptionDetailStore($request);
	}

	public function prescriptionDetailUpdate(Request $request)
	{
		$validator = \Validator::make($request->all(), [
			'medicine_name' => 'required',
			'medicine_usage' => 'required',
		]);
		
		if ($validator->fails())
		{
				return response()->json(['errors'=>$validator->errors()]);
		}
		return $this->prescription->prescriptionDetailUpdate($request);
	}

	public function getPrescriptionPreview(Request $request)
	{
		return $this->prescription->getPrescriptionPreview($request->id);
	}

	public function prescription_detail(Prescription $prescription)
	{
		$this->data = [
			'prescription' => $prescription,
			'medicines' => Medicine::getSelectMedicine($prescription->medicine_id),
			'prescription_preview' => $this->prescription->getPrescriptionPreview($prescription->id),
		];

		return view('prescription.prescription_detail', $this->data);
	}


	public function edit(Prescription $prescription)
	{

		$this->data = [
			'provinces' => Province::getSelectData(),
			'districts' => $prescription->pt_province_id && $prescription->pt_district_id ? $prescription->province->getSelectDistrict() : [],
			'prescription' => $prescription,
			'medicines' => Medicine::getSelectData('id', 'name', '', 'name' ,'asc'),
			'patients' => Patient::getSelectData('id', 'name', '', 'name' ,'asc'),
			'prescription_preview' => $this->prescription->getPrescriptionPreview($prescription->id)->getData()->prescription_detail,
			'usage' => Usage::getSelectData('id', 'name', '', 'name' ,'asc'),
		];

		return view('prescription.edit', $this->data);
	}

	public function print(Prescription $prescription)
	{

		$this->data = [
			'prescription' => $prescription,
			'prescription_preview' => $this->prescription->getPrescriptionPreview($prescription->id)->getData()->prescription_detail,
		];

		return view('prescription.print', $this->data);
	}

	public function update(PrescriptionRequest $request, Prescription $prescription)
	{
		if ($this->prescription->update($request, $prescription)) {

			// Redirect
			return redirect()->route('prescription.edit', $prescription->id)
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]) . str_pad($request->code, 6, "0", STR_PAD_LEFT));
		}
	}

	public function status(Prescription $prescription, $status)
	{
		if ($this->prescription->status($prescription, $status)) {

			// Redirect
			return redirect()->route('prescription.index')
				->with('success', __('alert.crud.success.update', ['name' => Auth::user()->module()]) . str_pad($prescription->code, 6, "0", STR_PAD_LEFT));
		}
	}


	public function destroy(Request $request, Prescription $prescription)
	{
		// Redirect
		return redirect()->route('prescription.index')
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . str_pad(($this->prescription->destroy($request, $prescription)), 6, "0", STR_PAD_LEFT));
	}

	public function prescription_detail_destroy(PrescriptionDetail $prescription_detail)
	{
		// Redirect
		return redirect()->route('prescription.edit', $prescription_detail->prescription_id)
			->with('success', __('alert.crud.success.delete', ['name' => Auth::user()->module()]) . $this->prescription->destroy_prescription_detail($prescription_detail));
	}
}
