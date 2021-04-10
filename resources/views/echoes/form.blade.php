
  <div class="row">
    <div class="col-sm-6">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            {!! Form::hidden('date_hidden', '',) !!}
            {!! Html::decode(Form::label('date', __('label.form.date')."(YYYY-MM-DD) <small>*</small>")) !!}
            {!! Form::text('date', ((isset($echoes->date))? $echoes->date :  date('Y-m-d') ), ['class' => 'form-control datetimepicker-input '. (($errors->has("date"))? "is-invalid" : ""), 'id' => 'date_picker', 'data-toggle' => 'datetimepicker', 'data-target' => '#date_picker', 'placeholder' => 'date', 'autocomplete' => 'off', 'required']) !!}
            {!! $errors->first('date', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>      

        @if ($type != 'letter-form-the-hospital')
        <div class="col-sm-6">
          <div class="form-group">
            {!! Html::decode(Form::label('pt_diagnosis', __('label.form.echoes.pt_diagnosis'))) !!}
            {!! Form::text('pt_diagnosis', ((isset($echoes->pt_diagnosis))? $echoes->pt_diagnosis : '' ), ['class' => 'form-control '. (($errors->has("pt_diagnosis"))? "is-invalid" : ""),'placeholder' => 'patient diagnosis']) !!}
            {!! $errors->first('pt_diagnosis', '<div class="invalid-feedback">:message</div>') !!}
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="row justify-content-center">
              <div class="col-sm-4 text-center">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                  <div class="fileinput-new img-thumbnail" style="max-width: 100%;">
                    <img data-src="" src="/images/echoes/{{ ((isset($echoes->image))? $echoes->image : 'default.png' ) }}" alt="{{ Auth::user()->name }}">
                  </div>
                  <div class="fileinput-preview fileinput-exists img-thumbnail" style="max-width: 248px;"></div>
                  <div class="mt-2">
                    <span class="btn btn-outline-secondary btn-file">
                      <span class="fileinput-new">{{ __('label.buttons.select') }}</span>
                      <span class="fileinput-exists">{{ __('label.buttons.change') }}</span>
                      <input type="file" name="image" />
                    </span>
                    <a href="#" class="btn btn-outline-secondary fileinput-exists" data-dismiss="fileinput">{{ __('label.buttons.remove') }}</a>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <br />
            </div>
          </div>
        </div>
        @endif        
      </div>      
    </div>
    <div class="col-sm-6">
      @include('components.patient_form', ['pre_select_obj' => $pre_select_obj ?? null])  
    </div>

    <div class="col-sm-12">
      <div class="form-group">
        {!! Html::decode(Form::label('description', __('label.form.description') .'<small>*</small>')) !!}
      {!! Form::textarea('description', ((isset($echoes->description))? $echoes->description : $echo_default_description->description ), ['class' => 'form-control ','style' => 'height: 121px;', 'placeholder' => 'description', 'id' => 'my-editor', 'required']) !!}
      </div>
    </div>
    {{-- / .col --}}
  </div>



