<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Labor;
use App\Models\LaborCategory;
use App\Models\LaborService;
use App\Models\LaborDetail;
use App\Models\Patient;
use App\Models\Service;
use App\Repositories\PatientRepository;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Component\GlobalComponent;
use Hash;
use Auth;
use DB;


class LaborRepository
{

	public function getDatatable($request)
	{
		
		$from = $request->from;
		$to = $request->to;
		$labor_number = $request->labor_number;
		$conditions = '';
		if ($labor_number!='') {
			$conditions = ' AND labor_number LIKE "%'. intval($labor_number) .'%"';
		}
		$labors = Labor::select('*', DB::raw("CONCAT('៛ ', FORMAT(price, 0)) as formated_price"))->whereBetween('date', [$from, $to])->orderBy('labor_number', 'asc')->get();

		return Datatables::of($labors)
			->editColumn('labor_number', function ($labor) {
				return str_pad($labor->labor_number, 6, "0", STR_PAD_LEFT);
			})
			->addColumn('actions', function () {
				$button = '';
				return $button;
			})
			->make(true);
	}

	public function getReport($request)
	{
		$from = $request->from;
		$to = $request->to;
		$tbody = '';
		$conditions = '';
		$pt_id = $request->pt_id;
		if ($pt_id!='') {
			$conditions = ' AND patient_id = '.$pt_id;
		}
		// $labors = Labor::whereBetween('date', [$from, $to])->orderBy('labor_number', 'asc')->get();
		$labors = Labor::whereRaw('date BETWEEN "'. $from .'" AND "'. $to .'"'. $conditions)->orderBy('labor_number', 'asc')->get();
		$total_patient = 0;
		$total_amount = 0;
		foreach ($labors as $key => $labor) {
			$total_patient++;
			$total_amount += $labor->price;

			$description = '';
			foreach ($labor->labor_details as $j => $labor_detail) {
				$description .= '<div>- '. $labor_detail->name .' : '. $labor_detail->result .' '. $labor_detail->service->unit .' ('. $labor_detail->service->ref_from .' - '. $labor_detail->service->ref_from .')</div>';
			}

			$tbody .= '<tr>
									<td class="text-center">'. str_pad($labor->labor_number, 6, "0", STR_PAD_LEFT) .'</td>
									<td class="text-center">'. date('d/M/Y', strtotime($labor->date)) .'</td>
									<td class="text-center font-weight-bold">៛ '. number_format($labor->price, 0) .'</td>
									<td>'. $labor->pt_name .'</td>
									<td class="text-center">'. $labor->pt_age .'</td>
									<td class="text-center">'. $labor->pt_gender .'</td>
									<td>'. $description .'</td>
								</tr>';
		}

		return response()->json([
			'tbody' => $tbody,
			'total_patient' => $total_patient .' នាក់',
			'total_amount' => '៛ ' . number_format($total_amount, 0),
		]);

	}

	public function getLaborServiceCheckList($request)
	{
		$service_check_list = '';
		$ids = [];
		if ($request->type == 1) {
			$ids = [1];
		} else if ($request->type == 2){
			$ids = [2,3];
		}
		$labor_categories = LaborCategory::whereIn('id', $ids)->orderBy('id','asc')->get();

		foreach ($labor_categories as $key => $labor_category) {
			$service_check_list .= '<div class="col-sm-12">
																<h4 class="py-2">'. $labor_category->name .'</h4>
															</div>';
			foreach ($labor_category->services as $key => $service) {
				$service_check_list .= '<div class="col-sm-4">
																	<div class="form-check mb-3">
																		<input class="minimal chb_service" type="checkbox" id="'. $service->id .'" value="'. $service->id .'">
																		<label class="form-check-label" for="'. $service->id .'">'. $service->name .'</label>
																	</div>
																</div>';
			}

		}
		return response()->json([
			'service_check_list' => $service_check_list,
		]);
	}

	public function getCheckedServicesList($request)
	{
		$checked_services_list = '';
		$labor_services = LaborService::whereIn('id', $request->ids)->orderBy('category_id', 'asc')->get();
		$no = $request->no;
		$category_id = '';
		$category_list = '';

		foreach ($labor_services as $key => $service) {
			
			$reference = '';
			if ($service->ref_from == '' && $service->ref_to != '') {
				$reference = '(<'. $service->ref_to .' '. $service->unit .')';
			}else if($service->ref_from != '' && $service->ref_to ==''){
				$reference = '('. $service->ref_from .'> '. $service->unit .')';
			}else if($service->ref_from != '' && $service->ref_to!=''){
				$reference = '('. $service->ref_from .'-'. $service->ref_to .' '. $service->unit .')';
			}else{
				$reference = '';
			}

			$no++;
			if ($category_id != $service->category_id) {
				$category_id = $service->category_id;
				$checked_services_list .= '<tr>
																		<td colspan="6"><h6 class="text-center">'. $service->category->name .'</h6></td>
																	</tr>';
			}
			$category_id = $service->category_id;
			
			if ($service->name=='TH' || $service->name=='TO') {
				$checked_services_list .= '<tr class="labor_item" id="'. $no.'-'.$service->id .'">
																		<td class="text-center">'. $no .'</td>
																		<td>
																			<input type="hidden" name="service_id[]" value="'. $service->id .'">
																			<input type="hidden" name="service_name[]" value="'. $service->name .'">
																			<input type="hidden" name="category_id[]" value="'. $service->category_id .'">
																			'. $service->name .'
																		</td>
																		<td class="text-center">
																			<input type="text" name="result[]" class="form-control">
																		</td>
																		<td class="text-center">
																			<select name="unit[]" class="form-control">
																				<option value="">None</option>
																				<option value="Négatif">Négatif</option>
																				<option value="Positif">Positif</option>
																			</select>
																		</td>
																		<td class="text-center">
																			<input type="hidden" name="reference[]" value="'. $reference .'">
																			'. $reference .'
																		</td>
																		<td class="text-center">
																			<button type="button" onclick="removeCheckedService(\''. $no.'-'.$service->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																		</td>
																	</tr>';
			} else if ($service->unit=='Réaction') {
				$checked_services_list .= '<tr class="labor_item" id="'. $no.'-'.$service->id .'">
																		<td class="text-center">'. $no .'</td>
																		<td>
																			<input type="hidden" name="service_id[]" value="'. $service->id .'">
																			<input type="hidden" name="service_name[]" value="'. $service->name .'">
																			<input type="hidden" name="category_id[]" value="'. $service->category_id .'">
																			'. $service->name .'
																		</td>
																		<td class="text-center">
																			<input type="hidden" name="unit[]" value="">
																			'. $service->unit .'
																		</td>
																		<td class="text-center">
																			<select name="result[]" class="form-control">
																				<option value="Négatif">Négatif</option>
																				<option value="Positif">Positif</option>
																			</select>
																		</td>
																		<td class="text-center">
																			<input type="hidden" name="reference[]" value="'. $reference .'">
																			'. $reference .'
																		</td>
																		<td class="text-center">
																			<button type="button" onclick="removeCheckedService(\''. $no.'-'.$service->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																		</td>
																	</tr>';
			} else {
				$checked_services_list .= '<tr class="labor_item" id="'. $no.'-'.$service->id .'">
																		<td class="text-center">'. $no .'</td>
																		<td>
																			<input type="hidden" name="service_id[]" value="'. $service->id .'">
																			<input type="hidden" name="service_name[]" value="'. $service->name .'">
																			<input type="hidden" name="category_id[]" value="'. $service->category_id .'">
																			'. $service->name .'
																		</td>
																		<td class="text-center">
																			<input type="text" name="result[]" class="form-control">
																		</td>
																		<td class="text-center">
																			<input type="hidden" name="unit[]" value="">
																			'. $service->unit .'
																		</td>
																		<td class="text-center">
																			<input type="hidden" name="reference[]" value="'. $reference .'">
																			'. $reference .'
																		</td>
																		<td class="text-center">
																			<button type="button" onclick="removeCheckedService(\''. $no.'-'.$service->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																		</td>
																	</tr>';
			}
			

		}
		return response()->json([
			'checked_services_list' => $checked_services_list,
		]);
	}

	public function getLaborDetail($id)
	{
		
		$labor_detail_list = '';
		$labor = Labor::find($id);
		$category_id = '';
		foreach ($labor->labor_details as $order => $labor_detail) {
			
			$reference = '';
			if ($labor_detail->service->ref_from == '' && $labor_detail->service->ref_to != '') {
				$reference = '(<'. $labor_detail->service->ref_to .' '. $labor_detail->service->unit .')';
			}else if($labor_detail->service->ref_from != '' && $labor_detail->service->ref_to ==''){
				$reference = '('. $labor_detail->service->ref_from .'> '. $labor_detail->service->unit .')';
			}else if($labor_detail->service->ref_from != '' && $labor_detail->service->ref_to!=''){
				$reference = '('. $labor_detail->service->ref_from .'-'. $labor_detail->service->ref_to .' '. $labor_detail->service->unit .')';
			}else{
				$reference = '';
			}

			if ($category_id != $labor_detail->service->category_id) {
				$labor_detail_list .= '<tr>
																		<td colspan="6"><h6 class="text-center">'. $labor_detail->service->category->name .'</h6></td>
																	</tr>';
			}
			$category_id = $labor_detail->service->category_id;

			if ($labor_detail->name=='TH' || $labor_detail->name=='TO'){
				$labor_detail_list .= '<tr class="labor_item" id="'. $labor_detail->result .'">
																<td class="text-center">'. ++$order .'</td>
																<td>
																	<input type="hidden" name="labor_detail_ids[]" value="'. $labor_detail->id .'">
																	<input type="hidden" name="category_id[]" value="'. $labor_detail->category_id .'">
																	'. $labor_detail->name .'
																</td>
																<td class="text-center">
																	<input type="text" name="result[]" value="'. $labor_detail->result .'" class="form-control"/>
																</td>
																<td class="text-center">
																	<select name="unit[]" class="form-control">
																		<option value="" '. (($labor_detail->unit == '')? 'selected' : '') .'>None</option>
																		<option value="Négatif" '. (($labor_detail->unit == 'Négatif')? 'selected' : '') .'>Négatif</option>
																		<option value="Positif" '. (($labor_detail->unit == 'Positif')? 'selected' : '') .'>Positif</option>
																	</select>
																</td>
																<td class="text-center">
																	'. $reference .'
																</td>
																<td class="text-center">
																	<button type="button" onclick="deleteLaborDetail(\''. $labor_detail->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																</td>
															</tr>';
			}elseif ($labor_detail->service->unit=='Réaction'){
				$labor_detail_list .= '<tr class="labor_item" id="'. $labor_detail->result .'">
																<td class="text-center">'. ++$order .'</td>
																<td>
																	<input type="hidden" name="labor_detail_ids[]" value="'. $labor_detail->id .'">
																	<input type="hidden" name="category_id[]" value="'. $labor_detail->category_id .'">
																	'. $labor_detail->name .'
																</td>
																<td class="text-center">
																	<input type="hidden" name="unit[]" value="">
																	'. $labor_detail->service->unit .'
																</td>
																<td class="text-center">
																	<select name="result[]" class="form-control">
																		<option value="Négatif" '. (($labor_detail->result == 'Négatif')? 'selected' : '') .'>Négatif</option>
																		<option value="Positif" '. (($labor_detail->result == 'Positif')? 'selected' : '') .'>Positif</option>
																	</select>
																</td>
																<td class="text-center">
																	'. $reference .'
																</td>
																<td class="text-center">
																	<button type="button" onclick="deleteLaborDetail(\''. $labor_detail->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																</td>
															</tr>';
			}else{
				$labor_detail_list .= '<tr class="labor_item" id="'. $labor_detail->result .'">
																	<td class="text-center">'. ++$order .'</td>
																	<td>
																		<input type="hidden" name="labor_detail_ids[]" value="'. $labor_detail->id .'">
																		<input type="hidden" name="category_id[]" value="'. $labor_detail->category_id .'">
																		'. $labor_detail->name .'
																	</td>
																	<td class="text-center">
																		<input type="text" name="result[]" value="'. $labor_detail->result .'" class="form-control"/>
																	</td>
																	<td class="text-center">
																		<input type="hidden" name="unit[]" value="">
																		'. $labor_detail->service->unit .'
																	</td>
																	<td class="text-center">
																		'. $reference .'
																	</td>
																	<td class="text-center">
																		<button type="button" onclick="deleteLaborDetail(\''. $labor_detail->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																	</td>
																</tr>';
			}
		}
		return $labor_detail_list;
	}

	public function storeAndGetLaborDetail($request)
	{

		$labor = Labor::find($request->labor_id);
		$labor_services = LaborService::select(DB::raw("id, name, category_id, unit, description, CONCAT(`ref_from`,' - ',`ref_to`) AS reference"))->whereIn('id', $request->service_ids)->get();
		foreach ($labor_services as $key => $service) {
			LaborDetail::create([
				'name' => $service->name,
				'unit' => '',
				'service_id' => $service->id,
				'category_id' => $service->category_id,
				'labor_id' => $labor->id,
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
			]);
		}


		$json = $this->getLaborPreview($request->labor_id)->getData();

		return response()->json([
			'labor_detail_list' => $this->getLaborDetail($request->labor_id),
			'labor_preview' => $json->labor_detail,
		]);
	}

	public function get_edit_detail($id)
	{
		$labor_detail = LaborDetail::find($id);
		$service = $labor_detail->service;
		return $labor_detail;
	}

	public function getLaborPreview($id)
	{

		$no = 1;
		$category_id = 0;
		$labor_detail = '';
		$labor_detail_item_list = '';
		$labor_detail_item_list_th_to = '';
		$labor = Labor::find($id);
		$title = 'Labor ('. str_pad($labor->labor_number, 6, "0", STR_PAD_LEFT) .')';

		foreach ($labor->labor_details as $labor_detail) {

			if ($labor_detail->service->name=='TH' || $labor_detail->service->name=='TO') {
				
				$class_unit = '';
				if ($labor_detail->unit== 'positif' || $labor_detail->unit== 'Positif' || $labor_detail->unit== 'POSITIF') {
					$class_unit = 'color_red';
				}

				$class_result = '';
				if ($labor_detail->result== '1/160' || $labor_detail->result== '1/320') {
					$class_result = 'color_red';
				}

				$labor_detail_item_list_th_to .= '<tr>
																						<td width="2%"></td>
																						<td width="30%">-'. $labor_detail->name .'</td>
																						<td width="16%">: <b><span class="'. $class_result .'">'. $labor_detail->result .'</span></b></td>
																						<td width="12%" class="'. $class_unit .'">&nbsp;'. $labor_detail->unit .'</td>
																						<td width=""></td>
																					</tr>';

			}else if ($labor_detail->service->unit=='Réaction') {
				
				$class = '';
				if ($labor_detail->result== 'positif' || $labor_detail->result== 'Positif' || $labor_detail->result== 'POSITIF') {
					$class = 'color_red';
				}
				if ($labor->labor_type == '2') {

					if ($category_id != $labor_detail->service->category_id) {
						$labor_detail_item_list .= '<tr>
																					<td colspan="4"><h6 style="padding: 30px 0 8px 0;">'. $labor_detail->service->category->name .'</h6></td>
																				</tr>';
					}
					$category_id = $labor_detail->service->category_id;

					$labor_detail_item_list .= '<tr>
																				<td width="5%"></td>
																				<td width="30%">'. $labor_detail->name .'</td>
																				<td width="25%">: <b>'. $labor_detail->service->unit .'</b></td>
																				<td>&nbsp;<span class="'. $class .'">'. $labor_detail->result .'</span></td>
																			</tr>';
				} else {
					$labor_detail_item_list .= '<tr>
																				<td width="2%"></td>
																				<td width="30%">-'. $labor_detail->name .'</td>
																				<td width="16%">: <b>'. $labor_detail->service->unit .'</b></td>
																				<td width="12%">&nbsp;<span class="'. $class .'">'. $labor_detail->result .'</span></td>
																				<td width="">'. (($labor_detail->service->ref_from != '' && $labor_detail->service->ref_from!='')? '('. $labor_detail->service->ref_from .'-'. $labor_detail->service->ref_to .' '. $labor_detail->service->unit .')' : '') .'</td>
																			</tr>';
				}
				
			}else{
				
				$class = '';
				if (($labor_detail->service->ref_from == '' && $labor_detail->result >= $labor_detail->service->ref_to) || $labor_detail->result < $labor_detail->service->ref_from || $labor_detail->result > $labor_detail->service->ref_to) {
					$class = 'color_red';
				}

				$reference = '';
				if ($labor_detail->service->ref_from == '' && $labor_detail->service->ref_to != '') {
					$reference = '(<'. $labor_detail->service->ref_to .' '. $labor_detail->service->unit .')';
				}else if($labor_detail->service->ref_from != '' && $labor_detail->service->ref_to ==''){
					$reference = '('. $labor_detail->service->ref_from .'> '. $labor_detail->service->unit .')';
				}else if($labor_detail->service->ref_from != '' && $labor_detail->service->ref_to!=''){
					$reference = '('. $labor_detail->service->description . $labor_detail->service->ref_from .'-'. $labor_detail->service->ref_to .' '. $labor_detail->service->unit .')';
				}

				if ($labor->labor_type == '2') {

					if ($category_id != $labor_detail->service->category_id) {
						$labor_detail_item_list .= '<tr>
																					<td colspan="4"><h6 style="padding: 30px 0 8px 0;">'. $labor_detail->service->category->name .'</h6></td>
																				</tr>';
					}
					$category_id = $labor_detail->service->category_id;

					$labor_detail_item_list .= '<tr>
																				<td width="5%"></td>
																				<td width="30%">'. $labor_detail->name .'</td>
																				<td width="25%">: <b><span class="'. $class .'">'. $labor_detail->result .'</span></b> '. $labor_detail->service->unit .'</td>
																				<td>'. $reference .'</td>
																			</tr>';
				} else {
					$labor_detail_item_list .= '<tr>
																				<td width="2%"></td>
																				<td width="30%">-'. $labor_detail->name .'</td>
																				<td width="16%">: <b><span class="'. $class .'">'. $labor_detail->result .'</span></b></td>
																				<td width="12%">&nbsp;'. $labor_detail->service->unit .'</td>
																				<td width="">'. $reference .'</td>
																			</tr>';
				}
			}
		}
		if(empty($labor->province)){ $labor->province = new \stdClass(); $labor->province->name = ''; }
		if(empty($labor->district)){ $labor->district = new \stdClass(); $labor->district->name = ''; }


		$labor_detail = '<section class="labor-print" style="position: relative;">
											<table class="table-header" width="100%">
												<tr>
													<td rowspan="5" width="15%" style="padding: 10px;">
														<div style="position: absolute; left: 30px; top: 35px; width: 120px;">
															<img src="/images/setting/logo.png" alt="IMG">
														</div>
													</td>
													<td class="text-center" width="70%" style="padding: 5px 0;">
														<h3 class="KHOSMoulLight color_light_blue">'. Auth::user()->setting()->clinic_name_kh .'</h3>
													</td>
													<td width="15%" rowspan="5">
													</td>
												</tr>
												<tr>
													<td class="text-center" style="padding: 2px 0 8px 0;">
														<h3 class="roboto_b color_light_blue">'. Auth::user()->setting()->clinic_name_en .'</h3>
													</td>
												</tr>
												<tr>
													<td class="text-center" style="padding: 1px 0;">
														<div class="color_light_blue">'. Auth::user()->setting()->description .'</div>
													</td>
												</tr>
												<tr>
													<td class="text-center" style="padding: 1px 0;">
														<div class="color_light_blue" style="font-size: 13px;">'. Auth::user()->setting()->address .'</div>
													</td>
												</tr>
												<tr>
													<td class="text-center" style="padding-bottom: 5px;">
														<div class="color_light_blue">លេខទូរស័ព្ទ: '. Auth::user()->setting()->phone .'</div>
													</td>
												</tr>
											</table>
											<table class="table-information" width="100%" style="margin: 5px 0 15px 0;">
												<tr>
													<td colspan="5">
														<h5 class="text-center KHOSMoulLight" style="padding: 10px 0 10px 0;">លទ្ធផលពិនិត្យឈាម</h5>
													</td>
												</tr>
												<tr>
													<td width="28%">
														កាលបរិច្ឆេទ: <span>'. date('d/m/Y', strtotime($labor->date)) .'</span>
													</td>
													<td width="25%">
														ឈ្មោះ: <span class="pt_name">'. $labor->pt_name .'</span>
													</td>
													<td width="15%">
														អាយុ: <span class="pt_age">'. $labor->pt_age .'</span>
													</td>
													<td width="12%">
														ភេទ: <span class="pt_gender">'. $labor->pt_gender .'</span>
													</td>
													<td width="20%" style="padding-left: 25px;">
														លេខរៀង: <span class="labor_number">'. str_pad($labor->labor_number, 6, "0", STR_PAD_LEFT) .'</span>
													</td>
												</tr>
											</table>
											' . ($labor->labor_type == 2 ? ('<table width="100%">'. $labor_detail_item_list .'</table>') : '<div style="height: 14.3cm"></div>') . '
											
											' . ($labor->labor_type == 1 ? ('<small class="remark">'. $labor->remark .'</small>') : '') . '
											<br/>
											<div class="color_light_blue" style="text-align: center; text-decoration: underline; position: absolute; bottom: 25px; left: 50%; transform: translateX(-50%);">សូមយកលទ្ធផលពិនិត្យឈាមនេះមកវិញពេលមកពិនិត្យលើកក្រោយ</div>
											<table class="table-footer mt---5" width="100%">
												<tr>
													<td> ' . ($labor->labor_type == 1 ? '<div>Séro Ag Widal</div>
															<table width="100%">
																'. $labor_detail_item_list_th_to .'
																'. $labor_detail_item_list .'
															</table>' : '') .  															
													' </td>
													<td width="28%" class="text-center" style="position: absolute; right: 0px; bottom: 40px;">
														<div><strong class="color_light_blue" style="font-size: 16px;">Technicien</strong></div>
														<div class="sign_box"></div>
														<div><span class="KHOSMoulLight color_light_blue">គឹម ស្រ៊ុន</span></div>
													</td>
												</tr>
											</table>
										</section>';

		return response()->json(['labor_detail' => $labor_detail, 'title' => $title]);
		// return $labor_detail;

	}

	public function labor_number()
	{
		$labor = Labor::whereYear('date', date('Y'))->orderBy('labor_number', 'desc')->first();
		return (($labor === null) ? '000001' : $labor->labor_number + 1);
	}

	public function create($request)
	{

		$request->patient_id = GlobalComponent::GetPatientIdOrCreate($request);
		$labor = Labor::create(GlobalComponent::MergeRequestPatient($request, [
			'date' => $request->date,
			'labor_number' => $request->labor_number,
			'price' => $request->price ?: 0,
			'labor_type' => $request->labor_type ?: 1,
			'simple_labor_detail' => $request->simple_labor_detail ?: '',
			'remark' => $request->remark,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]));
		if (isset($request->service_name) && isset($request->service_id)) {
			for ($i = 0; $i < count($request->service_name); $i++) {
				LaborDetail::create([
					'name' => $request->service_name[$i],
					'result' => $request->result[$i],
					'unit' => $request->unit[$i],
					'service_id' => $request->service_id[$i],
					'category_id' => $request->category_id[$i],
					'labor_id' => $labor->id,
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
				]);
			}
		}
		return $labor;
	}

	public function update($request, $labor)
	{
		$labor->update(GlobalComponent::MergeRequestPatient($request, [
			'date' => $request->date,
			'price' => $request->price ?: 0,
			'simple_labor_detail' => $request->simple_labor_detail ?: '',
			'remark' => $request->remark,
			'updated_by' => Auth::user()->id,
		]));
		if (isset($request->labor_detail_ids)) {
			for ($i = 0; $i < count($request->labor_detail_ids); $i++) {
				LaborDetail::find($request->labor_detail_ids[$i])->update([
					'result' => $request->result[$i],
					'unit' => $request->unit[$i],
					'updated_by' => Auth::user()->id,
				]);
			}
		}
		return $labor;
	}

	public function destroy($request, $labor)
	{
		if (Hash::check($request->passwordDelete, Auth::user()->password)) {
			$labor_number = $labor->labor_number;
			if ($labor->delete()) {
				return $labor_number;
			}
		} else {
			return false;
		}
	}

	public function deleteLaborDetail($request)
	{
		$labor_detail = LaborDetail::find($request->id);
		$labor_id = $labor_detail->labor_id;
		$labor_detail->delete();

		$json = $this->getLaborPreview($labor_id)->getData();

		$labor = Labor::find($labor_id);
		$labor_detail_list = '';
		foreach ($labor->labor_details as $order => $labor_detail) {
			$labor_detail_list .= '<tr class="labor_item" id="'. $labor_detail->result .'">
																<td class="text-center">'. ++$order .'</td>
																<td>
																	<input type="hidden" name="labor_detail_ids[]" value="'. $labor_detail->id .'">
																	'. $labor_detail->name .'
																</td>
																<td class="text-center">
																	<input type="text" name="result[]" value="'. $labor_detail->result .'" class="form-control"/>
																</td>
																<td class="text-center">
																	'. $labor_detail->service->unit .'
																</td>
																<td class="text-center">
																	'. $labor_detail->service->ref_from .' - '. $labor_detail->service->ref_from .'
																</td>
																<td class="text-center">
																	<button type="button" onclick="deleteLaborDetail(\''. $labor_detail->id .'\')" class="btn btn-sm btn-flat btn-danger"><i class="fa fa-trash-alt"></i></button>
																</td>
															</tr>';
			}

		return response()->json([
			'success'=>'success',
			'labor_preview' => $json->labor_detail,
			'labor_detail_list' => $this->getLaborDetail($labor_id),
		]);
	}

	public function get_service_id_or_create($name = '', $price = 0, $description = '')
	{
		$name = trim($name);
		$service = Service::where('name', $name)->first();

		if ($service != null) return $service->id;
		$created_service = Service::create([
			'name' => $name,
			'price' => $price,
			'description' => $description,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]);
		return $created_service->id;
	}
}
