<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Setting;
use Route;
use Auth;

class User extends Authenticatable
{
	use Notifiable, HasRoles;

	protected $fillable = [
		'first_name', 'last_name', 'phone', 'gender', 'image', 'language', 'status', 'approval', 'email', 'password',
	];

	protected $hidden = [
		'password', 'remember_token',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	// public function role()
	// {
	// 	return $this->belongToMany(HasRoles::class, 'model');
	// }

	public function echoDefaultDescriptions(){
		$collection = EchoDefaultDescription::orderBy('id', 'asc');
		return $collection;
	}

	public function isApproved($user){

		if ($user->approval == 1) {
			return true;
		}
		
		return false;
	}


	public function setting(){
		$setting = Setting::find(1);
		return $setting;
	}
	
	public function sidebarActive(){

		$routename = explode('.', Route::currentRouteName());
		if (count($routename) > 4) {

			return $routename[3];

		}else if (count($routename) > 3) {

			return $routename[2];

		}else if (count($routename) > 2) {

			return $routename[1];

		}else{

			return $routename[0];

		}

	}


	
	public function module()
	{

		$routename = explode('.', Route::currentRouteName());
		if (count($routename) > 5) {
			return __('label.routename.' . $routename[4]);
		}else if (count($routename) > 4) {
			return __('label.routename.' . $routename[3]);
		}else if (count($routename) > 3) {
			return __('label.routename.' . $routename[2]);
		}else if (count($routename) > 2) {
			return __('label.routename.' . $routename[1]);
		}else{
			return __('label.routename.' . $routename[0]);
		}

	}

	
	public function breadCrumb()
	{

		$routename = explode('.', Route::currentRouteName());

		$i = 0;
		$li = '';
		$active = '';
		foreach ($routename as $key => $value) {
			// GET First
		  if ( ++$i === count($routename) ) {

		  	// Last Active
				if ($value == 'index') {
					$active .= __('label.breadcrumb.crud.index');
				}else if ($value == 'create') {
					$active .= __('label.breadcrumb.crud.create');
				}else if ($value == 'edit') {
					$active .= __('label.breadcrumb.crud.edit');
				}else if ($value == 'image') {
					$active .= __('label.breadcrumb.crud.image');
				}else{
					$active .= __('label.breadcrumb.routename.'. $value);
				}
				$li .= '<li class="breadcrumb-item active">'. $active .'</li>';

		  } else if( $key === 0 ) {
				if ($value == 'echoes') {
					
				}else{
					$li .= '<li class="breadcrumb-item"><a href="'. route($value.'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
				}
		  }else if ( count($routename) > 3) {
		  	// if length 3 Level deep
				$li .= '<li class="breadcrumb-item"><a href="'. route($routename[1].'.'.$routename[2].'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
		  }else if ( count($routename) > 4) {
		  	// if length 4 Level deep
				$li .= '<li class="breadcrumb-item"><a href="'. route($routename[1].'.'.$routename[2].'.'.$routename[3].'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
		  }else{
				if ($value!='basic_data') {
					// if length normal crud
					$li .= '<li class="breadcrumb-item"><a href="'. route($value.'.index') .'">'. __('label.breadcrumb.routename.'. $value) .'</a></li>';
				}
		  }
		  // End if
		}
		// End Foreach
		return $li;

	}

	
	public function subModule()
	{

		$routename = explode('.', Route::currentRouteName());

		$name = '';
		if ( count($routename) > 4 ) {

			$name = $routename[4];

		}else if ( count($routename) > 3 ) {

			$name = $routename[3];

		}else if ( count($routename) > 2 ) {

			$name = $routename[2];

		}else{

			$name = $routename[1];

		}
		
		if ($name == 'index') {
			$name = __('label.content.header.index');
		}else if ($name == 'create') {
			$name = __('label.content.header.create');
		}else if ($name == 'edit') {
			$name = __('label.content.header.edit');
		}else if ($name == 'image') {
			$name = __('label.content.header.image');
		}else if ($name == 'show') {
			$name = __('label.content.header.show');
		}else if ($name == 'pick_year') {
			$name = __('label.content.header.pick_year');
		}else if ($name == 'year') {
			$name = __('label.content.header.pick_year');
		}else if ($name == 'report') {
			$name = __('label.content.header.report');
		}else if ($name == 'month') {
			$name = __('label.content.header.month');

		}else if ($name == 'role') {
			$name = __('label.content.header.role');

		}else if ($name == 'password') {
			$name = __('label.content.header.password');

		}else if ($name == 'assign_permission') {
			$name = __('label.content.header.assign_permission');

		}else if ($name == 'edit_order') {
			$name = __('label.content.header.edit_order');

		}else if ($name == 'home') {
			$name = '';
		}else{

		}

		return $name;
	}
	
	public function allPermissions()
	{
		$permissions = Permission::orderBy('created_at', 'asc')->get();
		return $permissions;
	}

	
	public function decimalToWord($n)
	{
		$f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
		$nums = explode ('.', $n);
		$whole = $f->format($nums[0]);
		$str_dollar = (($nums[0]> 1 )? " dollars" : " dollar");
		if (count($nums) == 2) {
			$str_cent = (($nums[1]> 1)? " cents" : " cent");
			
			$fraction = $f->format($nums[1]);
			return $whole . $str_dollar . (($nums[1] > 0)? ' and '. $fraction . $str_cent : "");
		} else {
			return $whole . $str_dollar;
		}
	}

	
	//function for split uft8 character
	public function mb_str_split( $string ) { 
		//Split at all position not after the start: ^ 
		//and not before the end: $ 
		return preg_split('/(?<!^)(?!$)/u', $string ); 
	}		
	
	public function num2khtext($complete_char,$enableThousand){
		//remove left zeros
		$cleanStr = ltrim($complete_char, '0');	
		//split number/string to array
		$num_arr = $this->mb_str_split($cleanStr);	
		$translated=''; $addThousand=false;
		//string array
		$khNUMTxt = array('','មួយ','ពីរ','បី','បួន','ប្រាំ');
		$twoLetter = array('','ដប់','ម្ភៃ','សាមសិប','សែសិប','ហាសិប','ហុកសិប','ចិតសិប','ប៉ែតសិប','កៅសិប');
		$khNUMLev = array('','','','រយ','ពាន់','ម៉ឺន','សែន','លាន');
		$khnum = array('០','១','២','៣','៤','៥','៦','៧','៨','៩');
		//loop to check each number character
		foreach($num_arr as $key=>$value){
			//convert khmer number to latin number if found
			if(in_array($value,$khnum)){$value = array_search($value,$khnum);}
			//allow only number
			if(!is_numeric($value)){return '';}
			//check what pos the charactor in
			$pos = count($num_arr) - ($key);
			if($pos>count($khNUMLev)-1){$pos=($pos % count($khNUMLev))+2;}
			//enable or diable read in thousand
			if($enableThousand and ($pos == 5 or $pos == 6)){$pos = $pos-3;}
			//concatenate number as text
			if($pos==2){
				$translated .= $twoLetter[$value];
			}else{
				if($value>5){$translated .=  $khNUMTxt[5].$khNUMTxt[$value - 5];}else{$translated .= $khNUMTxt[$value];}
			}
			//work for thousand
			if($pos==2 or $pos == 3 or $pos == 4){
				if($value>0){$addThousand=true;}
			}
			//concatenate number level
			if($value>0 or ($pos==4 and $addThousand and $enableThousand) or $pos==7){
				$translated .= $khNUMLev[$pos];			
			}
			//make addthousand to default value (false)
			if($pos==4){$addThousand=false;}		
		}
		//return the complete number in text
		return $translated;
	}

}
