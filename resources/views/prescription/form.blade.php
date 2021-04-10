
  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::hidden('date_hidden', '',) !!}
            {!! Html::decode(Form::label('date', __('label.form.date')."(YYYY-MM-DD) <small>*</small>")) !!}
            {!! Form::text('date', ((isset($prescription->date))? $prescription->date : date('Y-m-d') ), ['class' => 'form-control datetimepicker-input '. (($errors->has("date"))? "is-invalid" : ""), 'id' => 'date_picker', 'data-toggle' => 'datetimepicker', 'data-target' => '#date_picker', 'placeholder' => 'date', 'autocomplete' => 'off', 'required']) !!}
            {!! $errors->first('date', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('code', __('label.form.prescription.code')." <small>*</small>")) !!}
            {!! Form::text('code', ((isset($prescription->code))? str_pad($prescription->code, 6, "0", STR_PAD_LEFT) : str_pad($code, 6, "0", STR_PAD_LEFT) ), ['class' => 'form-control is_integer '. (($errors->has("code"))? "is-invalid" : ""), 'placeholder' => 'prescription number', 'autocomplete' => 'off', 'readonly'=>'readonly', 'required']) !!}
            {!! $errors->first('code', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('pt_diagnosis', __('label.form.echoes.pt_diagnosis'))) !!}
            {!! Form::text('pt_diagnosis', ((isset($prescription->pt_diagnosis))? $prescription->pt_diagnosis : '' ), ['class' => 'form-control '. (($errors->has("pt_diagnosis"))? "is-invalid" : ""),'placeholder' => 'diagnosis']) !!}
            {!! $errors->first('pt_diagnosis', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        
        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('remark', __('label.form.remark'))) !!}
            {!! Form::textarea('remark', ((isset($prescription->remark))? $prescription->remark : '' ), ['class' => 'form-control ','style' => 'height: 121px;','placeholder' => 'remark']) !!}
          </div>
        </div>
    
      </div>
      
    </div>
    <div class="col-sm-6">
      @include('components.patient_form', ['pre_select_obj' => $pre_select_obj ?? null])    
    </div>
  </div>
  <datalist id="medicine_list">
    @foreach ($medicines as $m)
      <option value="{!! $m !!}">
    @endforeach
  </datalist>
  <datalist id="usage_list">
    @foreach ($usage as $u)
      <option value="{!! $u !!}">
    @endforeach
  </datalist>



