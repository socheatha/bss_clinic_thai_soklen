
  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::hidden('date_hidden', '',) !!}
            {!! Html::decode(Form::label('date', __('label.form.date')."(YYYY-MM-DD) <small>*</small>")) !!}
            {!! Form::text('date', ((isset($invoice->date))? $invoice->date :  date('Y-m-d') ), ['class' => 'form-control datetimepicker-input '. (($errors->has("date"))? "is-invalid" : ""), 'id' => 'date_picker', 'data-toggle' => 'datetimepicker', 'data-target' => '#date_picker', 'placeholder' => 'date', 'autocomplete' => 'off', 'required']) !!}
            {!! $errors->first('date', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
            {!! Html::decode(Form::label('inv_number', __('label.form.invoice.inv_number')." <small>*</small>")) !!}
            {!! Form::text('inv_number', ((isset($invoice->inv_number))? str_pad($invoice->inv_number, 6, "0", STR_PAD_LEFT) : str_pad($inv_number, 6, "0", STR_PAD_LEFT) ), ['class' => 'form-control is_integer '. (($errors->has("inv_number"))? "is-invalid" : ""), 'placeholder' => 'invoice number', 'autocomplete' => 'off', 'readonly'=>'readonly', 'required']) !!}
            {!! $errors->first('inv_number', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>

        <div class="col-sm-8">
          <div class="form-group">
            {!! Html::decode(Form::label('exchange_rate', __('label.form.invoice.exchange_rate')." <small>*</small>")) !!}
            {!! Form::text('exchange_rate', ((isset($invoice->rate))? $invoice->rate : '4000' ), ['class' => 'form-control is_integer '. (($errors->has("exchange_rate"))? "is-invalid" : ""), 'placeholder' => 'exchange rate', 'autocomplete' => 'off','required']) !!}
            {!! $errors->first('exchange_rate', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group text-center">
            {!! Html::decode(Form::label('status', __('label.form.invoice.status'))) !!}
            <div class="togglebutton mt-1">
              <label>
                {!! Form::checkbox('status',((isset($invoice->status))? $invoice->status : 1 ), ((isset($invoice->status))? (($invoice->status==1)? true : false ) : true)) !!}
                <span class="toggle toggle-success"></span>
              </label>
            </div>
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('pt_diagnosis', __('label.form.echoes.pt_diagnosis'))) !!}
            {!! Form::text('pt_diagnosis', ((isset($invoice->pt_diagnosis))? $invoice->pt_diagnosis : '' ), ['class' => 'form-control '. (($errors->has("pt_diagnosis"))? "is-invalid" : ""),'placeholder' => 'diagnosis']) !!}
            {!! $errors->first('pt_diagnosis', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>

        <div class="col-sm-12">
          <div class="form-group">
            {!! Html::decode(Form::label('remark', __('label.form.remark'))) !!}
            {!! Form::textarea('remark', ((isset($invoice->remark))? $invoice->remark : '' ), ['class' => 'form-control '. (($errors->has("remark"))? "is-invalid" : ""),'style' => 'height: 121px;', 'placeholder' => 'remark']) !!}
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



