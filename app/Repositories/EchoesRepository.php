<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Echoes;
use App\Models\Patient;
use App\Models\EchoDefaultDescription;
use Yajra\DataTables\Facades\DataTables;
use Hash;
use Auth;
use Image;
use File;


class EchoesRepository
{


	public function getDatatable($request, $type)
	{
		
		$from = $request->from;
		$to = $request->to;
		$echo_default_description = EchoDefaultDescription::where('slug', $type)->first();
		$echoess = Echoes::where('echo_default_description_id', $echo_default_description->id)->whereBetween('date', [$from, $to])->orderBy('date', 'asc')->get();

		return Datatables::of($echoess)
			->addColumn('actions', function () {
				$button = '';
				return $button;
			})
			->make(true);
	}

	public function getEchoesPreview($id)
	{

		$no = 1;
		$total = 0;
		$total_discount = 0;
		$grand_total = 0;
		$echoes_detail = '';
		$tbody = '';

		$echoes = Echoes::find($id);

		$title = 'Echoes (INV'. date('Y', strtotime($echoes->date)) .'-'.str_pad($echoes->inv_number, 6, "0", STR_PAD_LEFT) .')';
		$total_riel = number_format($total*$echoes->rate, 0);
		$total_discount_riel = number_format($total_discount*$echoes->rate, 0);
		$grand_total_riel = number_format($grand_total*$echoes->rate, 0);

		
		$gtt = explode(".", number_format($grand_total,2));
		$gtt_dollars = $gtt[0];
		$gtt_cents = $gtt[1];

		$grand_total_in_word = Auth::user()->num2khtext($gtt_dollars, false) . 'ដុល្លារ' . (($gtt_cents>0)? ' និង'. Auth::user()->num2khtext($gtt_cents, false) .'សេន' : '');
		$grand_total_riel_in_word = Auth::user()->num2khtext(round($grand_total*$echoes->rate, 0), false);

		if(empty($echoes->province)){ $echoes->province = new \stdClass(); $echoes->province->name = ''; }
		if(empty($echoes->district)){ $echoes->district = new \stdClass(); $echoes->district->name = ''; }
		
		if ($echoes->echo_default_description->slug == 'letter-form-the-hospital') {
			$echoes_detail = '<section class="echoes-print" style="position: relative;">
													<div class="KHOSMoulLight text-center" style=="font-size: 16px;">ព្រះរាជាណាចក្រកម្ពុជា</div>
													<div class="KHOSMoulLight text-center" style=="font-size: 16px;">ជាតិ   សាសនា    ព្រះមហាក្សត្រ</div>
													<table class="table-header" width="100%">
														<tr>
															<td  width="30%" class="text-center">
																<div style="width: 3cm; height: 3cm; margin: 0 auto;"><img src="/images/setting/logo.png" alt="IMG"></div>
																<div class="KHOSMoulLight" style="padding: 5px 0;">មន្ទីសុខាភិបាលខេត្តកំពង់ចាម</div>
																<div class="KHOSMoulLight">'. Auth::user()->setting()->clinic_name_kh .'</div>
															</td>
															<td width="30%" class="text-center">
															</td>
															<td width="40%" class="text-center">
																<br/>
																<div>'. Auth::user()->setting()->echo_address .'</div>
																<div style="padding: 5px 0;">Tel: '. Auth::user()->setting()->phone .'</div>
															</td>
														</tr>
													</table>
													<br/>
													<br/>	
													<div class="echo_description">
														'. $echoes->description .'
													</div>
												</section>';
		}else{
			$echoes_detail = '<section class="echoes-print" style="position: relative;">
													<table class="table-header" width="100%">
														<tr>
															<td width="40%">
																<div class="KHOSMoulLight"style="color: red;">'. Auth::user()->setting()->sign_name_kh .'</div>
																<div style="color: blue; font-weight: bold; text-transform: uppercase; padding: 5px 0;">'. Auth::user()->setting()->sign_name_en .'</div>
																<div>'. Auth::user()->setting()->echo_description .'</div>
															</td>
															<td  width="20%">
																<img src="/images/setting/logo.png" alt="IMG">
															</td>
															<td width="40%" class="text-center">
																<div>'. Auth::user()->setting()->echo_address .'</div>
																<div style="padding: 5px 0;">Tel: '. Auth::user()->setting()->phone .'</div>
															</td>
														</tr>
													</table>
													<table class="table-information" width="100%" style="border-top: 4px solid red; margin: 10px 0 6px 0;">
														<tr>
															<td colspan="3">
																<h5 class="text-center KHOSMoulLight" style="padding: 20px 0 10px; color: blue;">'. $echoes->echo_default_description->name .'</h5>
															</td>
														</tr>
														<tr>
															<td>
																ឈ្មោះ: <span class="pt_name">'. $echoes->pt_name .'</span>
															</td>
															<td>
																ភេទ: <span class="pt_gender">'. $echoes->pt_gender .'</span>
															</td>
															<td>
																អាយុ: <span class="pt_age">'. $echoes->pt_age .'</span>
															</td>
														</tr>
														<tr>
															<td colspan="3">
																អាសយដ្ឋាន: <span class="pt_name">'. (($echoes->pt_village!='')? 'ភូមិ'.$echoes->pt_village : '') . (($echoes->pt_commune!='')? (($echoes->province->name=='ភ្នំពេញ')? ' សង្កាត់'.$echoes->pt_commune : ' ឃុំ'.$echoes->pt_commune) : '') . (($echoes->district->name!='')? (($echoes->province->name=='ភ្នំពេញ')? ' ខណ្ឌ'.$echoes->district->name : ' ស្រុក'.$echoes->district->name) : ''). (($echoes->province->name!='')? (($echoes->province->name=='ភ្នំពេញ')? ' រាជធានីភ្នំពេញ'.$echoes->province->name : ' ខេត្ត'.$echoes->province->name) : '') .'</span>
															</td>
														</tr>
													</table>
													<div class="echo_description">
														<div style="margin-bottom: 10px;">
															រោគវិនិច្ឆ័យ: '. $echoes->pt_diagnosis .'
														</div>
														'. $echoes->description .'
													</div>
													<table class="table-detail" width="100%">
														<tr>
															<td width="70%" style="padding: 10px;">
																<img src="/images/echoes/'. $echoes->image .'" alt="IMG" height="300px">
															</td>
															<td>
																<div>Le. '. date('d-m-Y', strtotime($echoes->date)) .'</div>
																<br/>
																<br/>
																<br/>
																<br/>
																<br/>
																<br/>
																<br/>
																<div>'. Auth::user()->setting()->sign_name_en .'</div>
															</td>
														</tr>
													</table>
													<div class="color_red" style="color: red; text-decoration: underline; text-align: center; position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);">សូមយកវេជ្ជបញ្ជាមកវិញពេលមកពិនិត្យលើក្រោយ</div>
													<br/>
												</section>';
		}

		return response()->json(['echoes_detail' => $echoes_detail, 'title' => $title]);
		// return $echoes_detail;

	}

	public function create($request, $path, $type)
	{
		
		$patient_id = $request->patient_id;

		if (isset($request->patient_id) && $request->patient_id!='') {
			# code...
		}else{
			$patient = Patient::where('name', $request->pt_name)->first();

			if ($patient!=null) {
				$patient_id = $patient->id;
			}else{
				$created_patient = Patient::create([
					'name' => $request->pt_name,
					'age' => $request->pt_age,
					'gender' => (($request->pt_gender=='ប្រុស' || $request->pt_gender == 'male' || $request->pt_gender == 'Male')? '1' : '2'),
					'phone' => $request->pt_phone,
					'address_village' => $request->pt_village,
					'address_commune' => $request->pt_commune,
					'address_district_id' => $request->pt_district_id,
					'address_province_id' => $request->pt_province_id,
					'created_by' => Auth::user()->id,
					'updated_by' => Auth::user()->id,
				]);
				$patient_id = $created_patient->id;
			}
		}


		$echo_default_description = EchoDefaultDescription::where('slug', $type)->first();
		$echoes = Echoes::create([
			'date' => $request->date,
			'pt_age' => $request->pt_age,
			'pt_name' => $request->pt_name,
			'pt_gender' => $request->pt_gender,
			'pt_phone' => $request->pt_phone,
			'pt_village' => $request->pt_village,
			'pt_commune' => $request->pt_commune,
			'pt_district_id' => $request->pt_district_id,
			'pt_province_id' => $request->pt_province_id,
			'pt_diagnosis' => $request->pt_diagnosis,
			'description' => $request->description,
			'patient_id' => $patient_id,
			'echo_default_description_id' => $echo_default_description->id,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
		]);
		
		if ($request->file('image')) {
			$image = $request->file('image');
			$echoes_image = time() .'_'. $echoes->id .'.png';
			$img = Image::make($image->getRealPath())->save($path.$echoes_image);
			$echoes->update(['image'=>$echoes_image]);
		}

		return $echoes;
	}

	public function update($request, $echoes, $path)
	{
		$echoes->update([
			'date' => $request->date,
			'pt_age' => $request->pt_age,
			'pt_name' => $request->pt_name,
			'pt_gender' => $request->pt_gender,
			'pt_phone' => $request->pt_phone,
			'pt_village' => $request->pt_village,
			'pt_commune' => $request->pt_commune,
			'pt_district_id' => $request->pt_district_id,
			'pt_province_id' => $request->pt_province_id,
			'pt_diagnosis' => $request->pt_diagnosis,
			'description' => $request->description,
			'patient_id' => $request->patient_id,
			'updated_by' => Auth::user()->id,
		]);
		
		if ($request->file('image')) {
			$image = $request->file('image');
			$echoes_image = (($echoes->image!='default.png')? $echoes->image : time() .'_'. $echoes->id .'.png');
			$img = Image::make($image->getRealPath())->save($path.$echoes_image);
			$echoes->update(['image'=>$echoes_image]);
		}

		return $echoes;

	}

	public function destroy($request, $echoes, $path)
	{
    if (Hash::check($request->passwordDelete, Auth::user()->password)){
			
			$image = $echoes->image;
			if($echoes->delete()){

				if ($echoes->image!='default.png') {
					File::deleteDirectory($path.$image);
				}

				return $request->pt_name;
			}
    }else{
        return false;
    }
	}


}