<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Component\GlobalComponent;
use Hash;
use Auth;


class PrescriptionRepository
{

	public function getDatatable($request)
	{
		
		$from = $request->from;
		$to = $request->to;
		$prescriptions = Prescription::whereBetween('date', [$from, $to])->orderBy('code', 'asc')->get();

		return Datatables::of($prescriptions)
			->editColumn('code', function ($prescription) {
				return str_pad($prescription->code, 6, "0", STR_PAD_LEFT);
			})
			->addColumn('actions', function () {
				$button = '';
				return $button;
			})
			->make(true);
	}

	public function get_edit_detail($id)
	{
		$inv_detail = PrescriptionDetail::find($id);
		$medicine = $inv_detail->medicine;
		return $inv_detail;
	}

	public function getPrescriptionPreview($id)
	{

		$no = 1;
		$total = 0;
		$prescription_detail = '';
		$tbody = '';

		$prescription = Prescription::find($id);

		$title = 'Prescription (PRE-' . str_pad($prescription->code, 6, "0", STR_PAD_LEFT) . ')';

		foreach ($prescription->prescription_details as $prescription_detail) {
			$total = ($prescription_detail->morning + $prescription_detail->afternoon + $prescription_detail->evening + $prescription_detail->night) * $prescription_detail->qty_days;
			$tbody .= '<tr>
									<td class="text-center">' . $no++ . '</td>
									<td>' . $prescription_detail->medicine_name . '</td>
									<td class="text-center">' . $prescription_detail->morning . '</td>
									<td class="text-center">' . $prescription_detail->afternoon . '</td>
									<td class="text-center">' . $prescription_detail->evening . '</td>
									<td class="text-center">' . $prescription_detail->night . '</td>
									<td class="text-center">' . $prescription_detail->qty_days . '</td>
									<td class="text-center">' . $total . '</td>
									<td class="text-center">' . $prescription_detail->medicine_usage . '</td>
									<td><small>' . $prescription_detail->description . '</small></td>
								</tr>';
		}

		if(empty($prescription->province)){ $prescription->province = new \stdClass(); $prescription->province->name = ''; }
		if(empty($prescription->district)){ $prescription->district = new \stdClass(); $prescription->district->name = ''; }
		$prescription_detail = '<section class="prescription-print" style="position: relative;">
														<table class="table-header" width="100%">
															<tr>
																<td rowspan="5" width="20%" style="padding: 10px;">
																	<img src="/images/setting/logo.png" alt="IMG">
																</td>
																<td class="text-center" style="padding: 5px 0;">
																	<h3 class="color_light_blue KHOSMoulLight">'. Auth::user()->setting()->clinic_name_kh .'</h3>
																</td>
															</tr>
															<tr>
																<td class="text-center" style="padding: 2px 0;">
																	<h3 class="color_light_blue roboto_b">'. Auth::user()->setting()->clinic_name_en .'</h3>
																</td>
															</tr>
															<tr>
																<td class="text-center" style="padding: 1px 0;">
																	<div class="color_light_blue">'. Auth::user()->setting()->description .'</div>
																</td>
															</tr>
															<tr>
																<td class="text-center" style="padding: 1px 0;">
																	<div class="color_light_blue">'. Auth::user()->setting()->address .'</div>
																</td>
															</tr>
															<tr>
																<td class="text-center" style="padding-bottom: 5px;">
																	<div class="color_light_blue">?????????????????????????????????: <b>'. Auth::user()->setting()->phone .'</b></div>
																</td>
															</tr>
														</table>
														<table class="table-information" width="100%" style="border-top: 2px solid #999; border-bottom: 2px solid #999; margin: 10px 0;">
															<tr>
																<td colspan="3">
																	<h5 class="text-center KHOSMoulLight" style="padding-top: 8px;">??????????????????????????????</h5>
																</td>
															</tr>
															<tr>
																<td>
																	?????????????????????????????????:<span class="date">'. date('d/m/Y', strtotime($prescription->date)) .'</span>
																</td>
																<td width="29%">
																	?????????????????????????????????:<span class="pt_no">'. str_pad($prescription->pt_no, 6, "0", STR_PAD_LEFT) .'</span>
																</td>
																<td width="29%">
																	????????????????????????????????????:<span class="code">'. $prescription->pt_diagnosis .'</span>
																</td>
															</tr>
															<tr>
																<td>
																	???????????????:<span class="pt_name">'. $prescription->pt_name .'</span>
																</td>
																<td>
																????????????:<span class="pt_age">'. $prescription->pt_age .'</span>
																</td>
																<td>
																	?????????:<span class="pt_gender">'. $prescription->pt_gender .'</span>
																</td>
															</tr>
															<tr>
																<td colspan="3">
																	???????????????????????????: <span class="pt_name">'. (($prescription->pt_village!='')? '????????????'.$prescription->pt_village : '') . (($prescription->pt_commune!='')? (($prescription->province->name=='?????????????????????')? ' ?????????????????????'.$prescription->pt_commune : ' ?????????'.$prescription->pt_commune) : '') . (($prescription->district->name!='')? (($prescription->province->name=='?????????????????????')? ' ????????????'.$prescription->district->name : ' ???????????????'.$prescription->district->name) : ''). (($prescription->province->name!='')? (($prescription->province->name=='?????????????????????')? ' ??????????????????????????????????????????'.$prescription->province->name : ' ???????????????'.$prescription->province->name) : '') .'</span>
																</td>
															</tr>
														</table>
														<table class="table-detail" width="100%">
															<thead>
																<th class="text-center" width="5%">???.???</th>
																<th class="text-center">??????????????????????????????</th>
																<th class="text-center" width="6%">???????????????</th>
																<th class="text-center" width="6%">????????????</th>
																<th class="text-center" width="6%">???????????????</th>
																<th class="text-center" width="6%">?????????</th>
																<th class="text-center" width="8%">???????????????????????????</th>
																<th class="text-center" width="6%">????????????</th>
																<th class="text-center" width="13%">???????????????????????????????????????</th>
																<th class="text-center" width="19%">??????????????????????????????</th>
															</thead>
															<tbody>
																'. $tbody .'
															</tbody>
														</table>
														<small class="remark">'. $prescription->remark .'</small>
														<br/>
														<div class="color_light_blue" style="text-decoration: underline; text-align: center; position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);">????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????</div>
														<table class="table-footer" style="position: absolute; bottom: 80px; " width="90%">
															<tr>
																<td></td>
																<td width="32%" class="text-center">
																	<div><b class="color_light_blue" style="font-size: 16px;">????????????????????????????????????????????????</b></div>
																	<div class="sign_box"></div>
																	<div style="color: blue;"><span class="color_blue KHOSMoulLight">???????????????. '. Auth::user()->setting()->sign_name_kh .'</span></div>
																</td>
															</tr>
														</table>
													</section>';
			

		return response()->json(['prescription_detail' => $prescription_detail, 'title' => $title]);
		// return $prescription_detail;

	}

	public function code()
	{
		$prescription = Prescription::whereYear('date', date('Y'))->orderBy('code', 'desc')->first();
		return (($prescription === null) ? '000001' : $prescription->code + 1);
	}

	public function create($request)
	{
		$request->patient_id = GlobalComponent::GetPatientIdOrCreate($request);
		$prescription = Prescription::create(GlobalComponent::MergeRequestPatient($request, [
			'date' => $request->date,
			'code' => $request->code,
			'pt_diagnosis' => $request->pt_diagnosis,
			'remark' => $request->remark,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]));

		if (isset($request->medicine_name) && isset($request->medicine_usage)) {
			for ($i = 0; $i < count($request->medicine_name); $i++) {
				PrescriptionDetail::create([
					'medicine_name' => $request->medicine_name[$i],
					'medicine_usage' => $request->medicine_usage[$i],
					'morning' => $request->morning[$i] ?: 0,
					'afternoon' => $request->afternoon[$i] ?: 0,
					'evening' => $request->evening[$i] ?: 0,
					'night' => $request->night[$i] ?: 0,
					'qty_days' => $request->qty_days[$i] ?: 0,
					'description' => $request->description[$i],
					'index' => $i + 1,
					'medicine_id' => $this->get_medicine_id_or_create($request->medicine_name[$i]),
					'prescription_id' => $prescription->id,
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
				]);
			}
		}

		return $prescription;
	}
	public function prescriptionDetailStore($request)
	{

		$prescription = Prescription::find($request->prescription_id);
		$last_item = $prescription->prescription_details()->first();
		$index = (($last_item !== null) ? $last_item->index + 1 : 1);

		$prescription_detail = PrescriptionDetail::create([
												'medicine_name' => $request->medicine_name,
												'medicine_usage' => $request->medicine_usage,
												'morning' => $request->morning ?: 0,
												'afternoon' => $request->afternoon ?: 0,
												'evening' => $request->evening ?: 0,
												'night' => $request->night ?: 0,
												'qty_days' => $request->qty_days ?: 0,
												'description' => $request->description,
												'index' => $index,
												'medicine_id' => $request->medicine_id,
												'prescription_id' => $request->prescription_id,
												'created_by' => Auth::user()->id,
												'updated_by' => Auth::user()->id,
											]);

		$json = $this->getPrescriptionPreview($prescription_detail->prescription_id)->getData();

		return response()->json([
			'success'=>'success',
			'prescription_detail' => $prescription_detail,
			'prescription_preview' => $json->prescription_detail,
		]);

	}
	public function prescriptionDetailUpdate($request)
	{
		$prescription_detail = PrescriptionDetail::find($request->id);
		$prescription_detail->update([
			'medicine_name' => $request->medicine_name,
			'medicine_usage' => $request->medicine_usage,
			'morning' => $request->morning ?: 0,
			'afternoon' => $request->afternoon ?: 0,
			'evening' => $request->evening ?: 0,
			'night' => $request->night ?: 0,
			'qty_days' => $request->qty_days ?: 0,
			'description' => $request->description,
			'medicine_id' => $this->get_medicine_id_or_create($request->medicine_name),
			'updated_by' => Auth::user()->id,
		]);

		$json = $this->getPrescriptionPreview($prescription_detail->prescription_id)->getData();
		return response()->json([
			'success'=>'success',
			'prescription_detail' => $prescription_detail,
			'prescription_preview' => $json->prescription_detail,
		]);
	}

	public function get_medicine_id_or_create($medicine_name = ''){
		$medicine_name = trim($medicine_name);
		$medicine = Medicine::select(['id'])->where('name', $medicine_name)->first();
		if ($medicine != null) return $medicine->id;
		$created_medicine = Medicine::create(['name' => $medicine_name, 'created_by' => Auth::user()->id, 'updated_by' => Auth::user()->id]);
		return $created_medicine->id;
	}

	public function save_order($request)
	{
		$order = explode(',', $request->order_ids);
		$ids = explode(',', $request->item_ids);

		for ($i = 0; $i < count($ids); $i++) {
			$prescription_detail = PrescriptionDetail::find($ids[$i])
				->update([
					'index' => $order[$i],
					'updated_by' => Auth::user()->id,
				]);
		}
		return 'success';
	}

	public function update($request, $prescription)
	{
		$prescription->update(GlobalComponent::MergeRequestPatient($request, [
			'date' => $request->date,
			'code' => $request->code,
			'pt_diagnosis' => $request->pt_diagnosis,
			'remark' => $request->remark,
			'updated_by' => Auth::user()->id,
		]));
		return $prescription;
	}

	public function status($prescription, $status)
	{
		$prescription->update([
			'status' => $status,
		]);

		return $prescription;
	}

	public function destroy($request, $prescription)
	{
		if (Hash::check($request->passwordDelete, Auth::user()->password)) {
			$code = $prescription->code;
			if ($prescription->delete()) {
				PrescriptionDetail::where('prescription_id', $prescription->id)->delete();
				return $code;
			}
		} else {
			return false;
		}
	}

	public function destroy_prescription_detail($prescription_detail)
	{
		$code = $prescription_detail->prescription->code;
		if ($prescription_detail->delete()) {
				
			return $code;
		}
	}

	public function deletePrescriptionDetail($request)
	{
		$prescription_detail = PrescriptionDetail::find($request->id);
		$prescription_id = $prescription_detail->prescription_id;
		$prescription_detail->delete();
		$json = $this->getprescriptionPreview($prescription_id)->getData();

		return response()->json([
			'success'=>'success',
			'prescription_preview' => $json->prescription_detail,
		]);
	}
}
