<div class="row">

	<div class="col-sm-6">
		<div class="form-group">
			{!! Html::decode(Form::label('name', __('label.form.name') .' <small>*</small>')) !!}
			{!! Form::text('name', ((isset($labor_service->name))? $labor_service->name : '' ), ['class' => 'form-control '. (($errors->has("name"))? "is-invalid" : ""),'placeholder' => 'name', 'required']) !!}
			{!! $errors->first('name', '<div class="invalid-feedback">:message</div>') !!}
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
			{!! Html::decode(Form::label('category_id', __('label.form.labor_service.category') .' <small>*</small>')) !!}
			{!! Form::select('category_id', $categories, ((isset($labor_service->category_id))? $labor_service->category_id : '' ), ['class' => 'form-control select2 category_id','placeholder' => __('label.form.choose'), 'required']) !!}
		</div>
	</div>

	<div class="col-sm-3">
		<div class="form-group">
			{!! Html::decode(Form::label('default_value', __('label.form.labor_service.default_value') .' <small>*</small>')) !!}
			{!! Form::text('default_value', ((isset($labor_service->default_value))? $labor_service->default_value : '' ), ['class' => 'form-control is_number '. (($errors->has("default_value"))? "is-invalid" : ""),'placeholder' => 'default value']) !!}
			{!! $errors->first('default_value', '<div class="invalid-feedback">:message</div>') !!}
		</div>
	</div>

	<div class="col-sm-3">
		<div class="form-group">
			{!! Html::decode(Form::label('unit', __('label.form.labor_service.unit') .' <small>*</small>')) !!}
			{!! Form::text('unit', ((isset($labor_service->unit))? $labor_service->unit : '' ), ['class' => 'form-control '. (($errors->has("unit"))? "is-invalid" : ""),'placeholder' => 'unit']) !!}
			{!! $errors->first('unit', '<div class="invalid-feedback">:message</div>') !!}
		</div>
	</div>

	<div class="col-sm-3">
		<div class="form-group">
			{!! Html::decode(Form::label('ref_from', __('label.form.labor_service.ref_from') .' <small>*</small>')) !!}
			{!! Form::text('ref_from', ((isset($labor_service->ref_from))? $labor_service->ref_from : '' ), ['class' => 'form-control is_number '. (($errors->has("ref_from"))? "is-invalid" : ""),'placeholder' => 'ref from']) !!}
			{!! $errors->first('ref_from', '<div class="invalid-feedback">:message</div>') !!}
		</div>
	</div>

	<div class="col-sm-3">
		<div class="form-group">
			{!! Html::decode(Form::label('ref_to', __('label.form.labor_service.ref_to') .' <small>*</small>')) !!}
			{!! Form::text('ref_to', ((isset($labor_service->ref_to))? $labor_service->ref_to : '' ), ['class' => 'form-control is_number '. (($errors->has("ref_to"))? "is-invalid" : ""),'placeholder' => 'ref to']) !!}
			{!! $errors->first('ref_to', '<div class="invalid-feedback">:message</div>') !!}
		</div>
	</div>

	<div class="col-sm-12">
		<div class="form-group">
			{!! Html::decode(Form::label('description', __('label.form.description'))) !!}
			{!! Form::textarea('description', ((isset($labor_service->description))? $labor_service->description : '' ), ['class' => 'form-control ','style' => 'height: 121px;','placeholder' => 'description']) !!}
		</div>
	</div>
	{{-- / .col --}}

</div>
{{-- / .row --}}

