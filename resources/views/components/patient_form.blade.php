<div class="row">
  <div class="col-sm-12">
    <div class="form-group">
      {!! Html::decode(Form::label('patient_id', __('label.form.labor.patient'))) !!}
      {!! Form::select('patient_id', [], ((isset($pre_select_obj->patient_id))? $pre_select_obj->patient_id : '' ), ['class' => 'form-control select2_pagination patient_id','placeholder' => __('label.form.choose')]) !!}
    </div>
  </div>
  
  <div class="col-sm-12">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_name', __('label.form.labor.pt_name')." <small>*</small>")) !!}
      {!! Form::text('pt_name', ((isset($pre_select_obj->pt_name))? $pre_select_obj->pt_name : '' ), ['class' => 'form-control '. (($errors->has("pt_name"))? "is-invalid" : ""),'placeholder' => 'patient full name','required']) !!}
      {!! $errors->first('pt_name', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>

  <div class="col-sm-3">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_age', __('label.form.labor.pt_age'))) !!}
      {!! Form::text('pt_age', ((isset($pre_select_obj->pt_age))? $pre_select_obj->pt_age : '' ), ['class' => 'form-control '. (($errors->has("pt_age"))? "is-invalid" : ""),'placeholder' => 'patient age']) !!}
      {!! $errors->first('pt_age', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>
  
  <div class="col-sm-3">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_gender', __('label.form.labor.pt_gender'))) !!}
      {!! Form::select('pt_gender', ['ប្រុស' => 'ប្រុស', 'ស្រី' => 'ស្រី'], ((isset($pre_select_obj->pt_gender))? $pre_select_obj->pt_gender : '' ), ['class' => 'form-control custom-select']) !!}
      {!! $errors->first('pt_gender', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_phone', __('label.form.labor.pt_phone'))) !!}
      {!! Form::text('pt_phone', ((isset($pre_select_obj->pt_phone))? $pre_select_obj->pt_phone : '' ), ['class' => 'form-control '. (($errors->has("pt_phone"))? "is-invalid" : ""),'placeholder' => 'patient phone']) !!}
      {!! $errors->first('pt_phone', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      {!! Html::decode(Form::label('province_id', __('label.form.patient.province'))) !!}
      {!! Form::select('pt_province_id', $provinces, ((isset($pre_select_obj->pt_province_id))? $pre_select_obj->pt_province_id : '' ), ['class' => 'form-control select2 province_id', 'data-width'=>'100%', 'placeholder' => __('label.form.choose')]) !!}
    </div>
  </div>
  <div class="col-sm-6">
    <div class="form-group">
      {!! Html::decode(Form::label('district_id', __('label.form.patient.district'))) !!}
      {!! Form::select('pt_district_id', $districts, ((isset($pre_select_obj->pt_district_id))? $pre_select_obj->pt_district_id : '' ), ['class' => 'form-control select2 district_id','data-width'=>'100%', 'placeholder' => __('label.form.choose'), (($districts==[])? 'disabled' : '' )]) !!}
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_commune', __('label.form.patient.commune'))) !!}
      {!! Form::text('pt_commune', ((isset($pre_select_obj->pt_commune))? $pre_select_obj->pt_commune : '' ), ['class' => 'form-control '. (($errors->has("pt_commune"))? "is-invalid" : ""),'placeholder' => 'commune']) !!}
      {!! $errors->first('pt_commune', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>

  <div class="col-sm-6">
    <div class="form-group">
      {!! Html::decode(Form::label('pt_village', __('label.form.patient.village'))) !!}
      {!! Form::text('pt_village', ((isset($pre_select_obj->pt_village))? $pre_select_obj->pt_village : '' ), ['class' => 'form-control '. (($errors->has("pt_village"))? "is-invalid" : ""),'placeholder' => 'village']) !!}
      {!! $errors->first('pt_village', '<div class="invalid-feedback">:message</div>') !!}
    </div>
  </div>
</div>