
  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::hidden('date_hidden', '',) !!}
            {!! Form::hidden('labor_type', $labor_type) !!}
            {!! Html::decode(Form::label('date', __('label.form.date')."(YYYY-MM-DD) <small>*</small>")) !!}
            {!! Form::text('date', ((isset($labor->date))? $labor->date :  date('Y-m-d') ), ['class' => 'form-control datetimepicker-input '. (($errors->has("date"))? "is-invalid" : ""), 'id' => 'date_picker', 'data-toggle' => 'datetimepicker', 'data-target' => '#date_picker', 'placeholder' => 'date', 'autocomplete' => 'off', 'required']) !!}
            {!! $errors->first('date', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Html::decode(Form::label('labor_number', __('label.form.labor.labor_number')." <small>*</small>")) !!}
            {!! Form::text('labor_number', ((isset($labor->labor_number))? str_pad($labor->labor_number, 6, "0", STR_PAD_LEFT) : str_pad($labor_number, 6, "0", STR_PAD_LEFT) ), ['class' => 'form-control is_integer '. (($errors->has("labor_number"))? "is-invalid" : ""), 'placeholder' => 'labor number', 'autocomplete' => 'off', 'readonly'=>'readonly', 'required']) !!}
            {!! $errors->first('labor_number', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Html::decode(Form::label('price', __('label.form.labor.price')." <small>*</small>")) !!}
            {!! Form::number('price', ($labor->price ?? 0), ['class' => 'form-control is_integer '. (($errors->has("price"))? "is-invalid" : ""), 'placeholder' => 'price', 'autocomplete' => 'off', 'required']) !!}
            {!! $errors->first('price', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('remark', __('label.form.remark'))) !!}
            {!! Form::textarea('remark', ((isset($labor->remark))? $labor->remark : '' ), ['class' => 'form-control '. (($errors->has("remark"))? "is-invalid" : ""),'style' => 'height: 121px;', 'placeholder' => 'remark']) !!}
          </div>
        </div>
    
      </div>
      
    </div>
    <div class="col-sm-6">
      @include('components.patient_form', ['pre_select_obj' => $pre_select_obj ?? null])
    </div>
  </div>
  <datalist id="service_list">
    @foreach ($services as $m)
      <option data-price="{!! $m->price !!}" data-description="{!! $m->description !!}" value="{!! $m->name !!}">
    @endforeach
  </datalist>



