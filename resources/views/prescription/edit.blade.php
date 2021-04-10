@extends('layouts.app')

@section('css')
{{ Html::style('/css/invoice-print-style.css') }}
<style type="text/css">
	.btn-print-prescription {
		top: -30px;
		right: 55px;
	}

	/* .item_list{
			padding: 20px;
			margin-top: 10px;
			background: #fff;
		}
		.prescription_item{
			margin-top: 10px;
		} */
</style>
@endsection

@section('content')

<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>

		<div class="card-tools">
			@can('Prescription Index')
			<a href="{{route('prescription.index')}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('prescription.update', $prescription->id),'method' => 'post','class' => 'mt-3']) !!}
	{!! Form::hidden('_method', 'PUT') !!}

	<div class="card-body">
		@include('prescription.form', ['pre_select_obj' => $prescription])

		<div class="card card-outline card-primary mt-4">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-list"></i>&nbsp;
					{{ __('alert.modal.title.prescription_detail') }}
				</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-flat btn-success" data-toggle="modal" data-target="#create_prescription_item_modal"><i class="fa fa-plus"></i> {!! __('label.buttons.add_medicine') !!}</button>
				</div>
			</div>
			<!-- /.card-header -->
			<div class="card-body item_list">
				@foreach ($prescription->prescription_details as $order => $prescription_detail)
				<div class="mb-2" id="{{ $prescription_detail->id }}">
					<div class="row">
						<div class="col-sm-2">
							<div class="form-group">
								{!! Html::decode(Form::label('medicine_name', __('label.form.prescription.medicine_name')."<small>*</small>")) !!}
								{!! Form::text('show_medicine_name', $prescription_detail->medicine_name, ['class' => 'form-control', 'id' => 'input-medicine_name-'. $prescription_detail->id,'placeholder' => 'name','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('morning', __('label.form.prescription.morning'))) !!}
								{!! Form::text('show_morning', $prescription_detail->morning, ['class' => 'form-control is_number', 'id' => 'input-morning-'. $prescription_detail->id,'min' => '0','placeholder' => 'morning','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('afternoon', __('label.form.prescription.afternoon'))) !!}
								{!! Form::text('show_afternoon', $prescription_detail->afternoon, ['class' => 'form-control is_number', 'id' => 'input-afternoon-'. $prescription_detail->id,'min' => '0','placeholder' => 'afternoon','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('evening', __('label.form.prescription.evening'))) !!}
								{!! Form::text('show_evening', $prescription_detail->evening, ['class' => 'form-control is_number', 'id' => 'input-evening-'. $prescription_detail->id,'min' => '0','placeholder' => 'evening','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('night', __('label.form.prescription.night'))) !!}
								{!! Form::text('show_night', $prescription_detail->night, ['class' => 'form-control is_number', 'id' => 'input-night-'. $prescription_detail->id,'min' => '0','placeholder' => 'night','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('qty_days', __('label.form.prescription.qty_days'))) !!}
								{!! Form::text('show_qty_days', $prescription_detail->qty_days, ['class' => 'form-control is_number', 'id' => 'input-qty_days-'. $prescription_detail->id,'min' => '0','placeholder' => 'qty_days','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('total', __('label.form.prescription.total'))) !!}
								{!! Form::text('show_total', (($prescription_detail->morning + $prescription_detail->afternoon + $prescription_detail->evening + $prescription_detail->night) * $prescription_detail->qty_days), ['class' => 'form-control is_number', 'id' => 'input-total-'. $prescription_detail->id,'min' => '0','placeholder' => 'total','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('medicine_usage', __('label.form.prescription.medicine_usage')."<small>*</small>")) !!}
								{!! Form::text('show_medicine_usage', $prescription_detail->medicine_usage, ['class' => 'form-control', 'id' => 'input-medicine_usage-'. $prescription_detail->id,'placeholder' => 'usage','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								{!! Html::decode(Form::label('description', __('label.form.description'))) !!}
								{!! Form::textarea('show_description', $prescription_detail->description, ['class' => 'form-control', 'id' => 'input-description-'. $prescription_detail->id,'placeholder' => 'description','style' => 'height: 38px','readonly']) !!}
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
								{!! Html::decode(Form::label('', __('label.buttons.action'))) !!}
								<div>
									<button class="btn btn-info btn-flat btn-prevent-submit" onclick="editPrescriptionDetail('{{ $prescription_detail->id }}')"><i class="fa fa-pencil-alt"></i></button>
									<button class="btn btn-danger btn-flat btn-prevent-submit" onclick="deletePrescriptionDetail({{ $prescription_detail->id }})"><i class="fa fa-trash-alt"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
			<!-- /.card-body -->
		</div>

	</div>
	<!-- ./card-body -->

	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>

<div class="position-relative">
	@can("Prescription Print")
	<button type="button" class="btn btn-flat btn-success position-absolute mr-9 mt-5 btn-print-prescription" data-url="{{ route('prescription.print', $prescription->id) }}"><i class="fa fa-print"></i> {{ __("label.buttons.print") }}</button>
	@endCan
</div>

<div class="pb-2 print-preview">
	{!! $prescription_preview !!}
</div>

<!-- Edit Prescription Item Modal -->
<div class="modal add fade" id="edit_prescription_item_modal">
	<div class="modal-dialog mw-100" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">{{ __('alert.modal.title.edit_prescription_item') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{!! Form::hidden('edit_item_id', '') !!}

				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_medicine_name', __('label.form.prescription.medicine_name')."<small>*</small>")) !!}
							{!! Form::text('edit_item_medicine_name', '', ['class' => 'form-control','placeholder' => 'name','required', 'list' => 'medicine_list']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_morning', __('label.form.prescription.morning'))) !!}
							{!! Form::number('edit_item_morning', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'morning']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_afternoon', __('label.form.prescription.afternoon'))) !!}
							{!! Form::number('edit_item_afternoon', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'afternoon']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_evening', __('label.form.prescription.evening'))) !!}
							{!! Form::number('edit_item_evening', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'evening']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_night', __('label.form.prescription.night'))) !!}
							{!! Form::number('edit_item_night', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'night']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_qty_days', __('label.form.prescription.qty_days'))) !!}
							{!! Form::number('edit_item_qty_days', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'qty_days']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_total', __('label.form.prescription.total'))) !!}
							{!! Form::number('edit_item_total', '0', ['class' => 'form-control is_number','min' => '0','placeholder' => 'total', 'readonly' => 'readonly']) !!}
						</div>
					</div>
					<div class="col-sm-1">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_medicine_usage', __('label.form.prescription.medicine_usage')."<small>*</small>")) !!}
							{!! Form::text('edit_item_medicine_usage', '', ['class' => 'form-control','placeholder' => 'usage','required', 'list' => 'usage_list']) !!}
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							{!! Html::decode(Form::label('edit_item_description', __('label.form.description'))) !!}
							{!! Form::textarea('edit_item_description', '', ['class' => 'form-control','placeholder' => 'description','style' => 'height: 38px']) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">{{ __('alert.swal.button.no') }}</button>
				<button type="button" class="btn btn-flat btn btn-success" id="btn_update_item" data-dismiss="modal">{{ __('alert.swal.button.yes') }}</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@include('components.confirm_password')

@include('prescription.modal')
<span class="sr-only" id="deleteAlert" data-title="{{ __('alert.swal.title.delete', ['name' => Auth::user()->module()]) }}" data-text="{{ __('alert.swal.text.unrevertible') }}" data-btnyes="{{ __('alert.swal.button.yes') }}" data-btnno="{{ __('alert.swal.button.no') }}" data-rstitle="{{ __('alert.swal.result.title.success') }}" data-rstext="{{ __('alert.swal.result.text.delete') }}"> Delete Message </span>


@endsection

@section('js')
<script type="text/javascript">
	$('.btn-prevent-submit').click(function(event) {
		event.preventDefault();
	});

	$(document).ready(function() {
		var data = [];
		$(".select2_pagination").each(function() {
			data.push({
				id: '{{ $prescription->patient_id }}',
				text: '{{ str_pad($prescription->patient_id, 6, "0", STR_PAD_LEFT) }} :: {{ (($prescription->patient_id != '
				')? $prescription->patient->name : '
				' )}}'
			});
		});
		$(".select2_pagination").select2({
			theme: 'bootstrap4',
			placeholder: "{{ __('label.form.choose') }}",
			allowClear: true,
			data: data,
			ajax: {
				url: "{{ route('patient.getSelect2Items') }}",
				method: 'post',
				dataType: 'json',
				data: function(params) {
					return {
						term: params.term || '{{ $prescription->patient_id }}',
						page: params.page || 1
					}
				},
				cache: true
			}
		});
	});

	$('.select2_pagination').val('{{ $prescription->id }}').trigger('change')

	$(function() {
		function openPrintWindow(url, name) {
			var printWindow = window.open(url, name, "width=" + screen.availWidth + ",height=" + screen.availHeight + ",_blank");
			var printAndClose = function() {
				if (printWindow.document.readyState == 'complete') {
					clearInterval(sched);
					printWindow.print();
					printWindow.close();
				}
			}
			var sched = setInterval(printAndClose, 2000);
		};

		jQuery(document).ready(function($) {
			$(".btn-print-prescription").on("click", function(e) {
				var myUrl = $(this).attr('data-url');
				// alert(myUrl);
				e.preventDefault();
				openPrintWindow(myUrl, "to_print");
			});
		});
	});

	function editPrescriptionDetail(id) {
		$.ajax({
				url: "{{ route('prescription.prescription_detail.getDetail') }}",
				type: 'post',
				data: {
					id: id
				},
			})
			.done(function(result) {
				$('[name="edit_item_medicine_name"]').val(result.prescription_detail.medicine_name);
				$('[name="edit_item_medicine_usage"]').val(result.prescription_detail.medicine_usage);
				$('[name="edit_item_morning"]').val(result.prescription_detail.morning);
				$('[name="edit_item_afternoon"]').val(result.prescription_detail.afternoon);
				$('[name="edit_item_evening"]').val(result.prescription_detail.evening);
				$('[name="edit_item_night"]').val(result.prescription_detail.night);
				$('[name="edit_item_qty_days"]').val(result.prescription_detail.qty_days);
				$('[name="edit_item_description"]').val(result.prescription_detail.description);
				$('[name="edit_item_id"]').val(result.prescription_detail.id);
				$('#edit_prescription_item_modal').modal('show');
			});
	};

	function deletePrescriptionDetail(id) {

		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: 'btn btn-success btn-flat ml-2 py-2 px-3',
				cancelButton: 'btn btn-danger btn-flat mr-2 py-2 px-3'
			},
			buttonsStyling: false
		})
		swalWithBootstrapButtons.fire({
			title: "{{ __('alert.swal.title.delete') }}",
			text: "{{ __('alert.swal.text.unrevertible') }}",
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: "{{ __('alert.swal.button.yes') }}",
			cancelButtonText: "{{ __('alert.swal.button.no') }}",
			reverseButtons: true
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: "{{ route('prescription.prescription_detail.deletePrescriptionDetail') }}",
					type: 'post',
					data: {
						id: id
					},
					success: function(data) {
						$('.print-preview').html(data.prescription_preview);
						Swal.fire({
							icon: 'success',
							title: "{{ __('alert.swal.result.title.save') }}",
							confirmButtonText: "{{ __('alert.swal.button.yes') }}",
							timer: 2500
						})
						$('#' + id).remove();
					}
				})
			}
		})
	};

	$('#btn_update_item').click(function() {
		$.ajax({
				url: "{{ route('prescription.prescription_detail.update') }}",
				type: 'post',
				data: {
					id: $('[name="edit_item_id"]').val(),
					medicine_name: $('[name="edit_item_medicine_name"]').val(),
					medicine_usage: $('[name="edit_item_medicine_usage"]').val(),
					morning: $('[name="edit_item_morning"]').val(),
					afternoon: $('[name="edit_item_afternoon"]').val(),
					evening: $('[name="edit_item_evening"]').val(),
					night: $('[name="edit_item_night"]').val(),
					qty_days: $('[name="edit_item_qty_days"]').val(),
					description: $('[name="edit_item_description"]').val()
				},
			})
			.done(function(data) {

				$('#edit_prescription_item_modal .invalid-feedback').remove();
				$('#edit_prescription_item_modal .form-control').removeClass('is-invalid');
				if (data.errors) {
					$.each(data.errors, function(key, value) {
						$('#edit_prescription_item_modal .prescription_item_' + key + ' input').addClass('is-invalid');
						$('#edit_prescription_item_modal .prescription_item_' + key).append('<span class="invalid-feedback">' + value + '</span>');
					});
					Swal.fire({
							icon: 'error',
							title: "{{ __('alert.swal.result.title.error') }}",
							confirmButtonText: "{{ __('alert.swal.button.yes') }}",
							timer: 1500
						})
						.then((result) => {
							if (result.value) {
								$('#edit_prescription_item_modal').modal('show');
							}
						})
				}
				if (data.success) {
					data.prescription_detail['total'] = $('[name="edit_item_total"]').val();

					$('[name="edit_item_medicine_name"]').val('');
					$('[name="edit_item_medicine_usage"]').val('');
					$('[name="edit_item_morning"]').val('');
					$('[name="edit_item_afternoon"]').val('');
					$('[name="edit_item_evening"]').val('');
					$('[name="edit_item_night"]').val('');
					$('[name="edit_item_qty_days"]').val('');
					$('[name="edit_item_description"]').val('');
					$('.print-preview').html(data.prescription_preview);
					$("#input-medicine_name-" + data.prescription_detail.id).val(data.prescription_detail.medicine_name);
					$("#input-medicine_usage-" + data.prescription_detail.id).val(data.prescription_detail.medicine_usage);
					$("#input-morning-" + data.prescription_detail.id).val(data.prescription_detail.morning);
					$("#input-afternoon-" + data.prescription_detail.id).val(data.prescription_detail.afternoon);
					$("#input-evening-" + data.prescription_detail.id).val(data.prescription_detail.evening);
					$("#input-night-" + data.prescription_detail.id).val(data.prescription_detail.night);
					$("#input-qty_days-" + data.prescription_detail.id).val(data.prescription_detail.qty_days);
					$("#input-total-" + data.prescription_detail.id).val(data.prescription_detail.total);
					$("#input-description-" + data.prescription_detail.id).val(data.prescription_detail.description);
					Swal.fire({
						icon: 'success',
						title: "{{ __('alert.swal.result.title.success') }}",
						confirmButtonText: "{{ __('alert.swal.button.yes') }}",
						timer: 1500
					})
					$('#modal_add_medicine').modal('hide');
				}
			});
	});

	$('#btn_add_item').click(function() {
		if ($('[name="item_medicine_name"]').val() != '' && $('[name="item_medicine_usage"]').val() != '') {

			$.ajax({
				url: "{{ route('prescription.prescription_detail.store') }}",
				method: 'post',
				data: {
					prescription_id: '{{ $prescription->id }}',
					medicine_name: $('[name="item_medicine_name"]').val(),
					medicine_usage: $('[name="item_medicine_usage"]').val(),
					morning: $('[name="item_morning"]').val(),
					afternoon: $('[name="item_afternoon"]').val(),
					evening: $('[name="item_evening"]').val(),
					night: $('[name="item_night"]').val(),
					qty_days: $('[name="item_qty_days"]').val(),
					description: $('[name="item_description"]').val(),
				},
				success: function(data) {
					$('.print-preview').html(data.prescription_preview);
					console.log(data.prescription_detail.description);
					data.prescription_detail['total'] = $('[name="item_total"]').val();
					$('.item_list').append(`<div class="prescription_item" id="${ data.prescription_detail.id }">
																			<div class="row">
																				<div class="col-sm-2">
																					<div class="form-group">
																						{!! Html::decode(Form::label('medicine_name', __('label.form.prescription.medicine_name')."<small>*</small>")) !!}
																						<input name="show_medicine_name" class="form-control" id="input-medicine_name-${ data.prescription_detail.id }" value="${ data.prescription_detail.medicine_name }" placeholder="name" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('morning', __('label.form.prescription.morning'))) !!}
																						<input name="show_morning" class="form-control" min="0" id="input-morning-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.morning=='null' || data.prescription_detail.morning==null || data.prescription_detail.morning=='' )? '': data.prescription_detail.morning) }" placeholder="morning" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('afternoon', __('label.form.prescription.afternoon'))) !!}
																						<input name="show_afternoon" class="form-control" min="0" id="input-afternoon-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.afternoon=='null' || data.prescription_detail.afternoon==null || data.prescription_detail.afternoon=='' )? '': data.prescription_detail.afternoon) }" placeholder="afternoon" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('evening', __('label.form.prescription.evening'))) !!}
																						<input name="show_evening" class="form-control is_number" min="0" id="input-evening-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.evening=='null' || data.prescription_detail.evening==null || data.prescription_detail.evening=='' )? '': data.prescription_detail.evening) }" placeholder="evening" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('night', __('label.form.prescription.night'))) !!}
																						<input name="show_night" class="form-control is_number" min="0" id="input-night-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.night=='null' || data.prescription_detail.night==null || data.prescription_detail.night=='' )? '': data.prescription_detail.night) }" placeholder="night" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('qty_days', __('label.form.prescription.qty_days'))) !!}
																						<input name="show_qty_days" class="form-control is_number" min="0" id="input-qty_days-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.qty_days=='null' || data.prescription_detail.qty_days==null || data.prescription_detail.qty_days=='' )? '': data.prescription_detail.qty_days) }" placeholder="qty_days" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('total', __('label.form.prescription.total'))) !!}
																						<input name="show_total" class="form-control is_number" min="0" id="input-total-${ data.prescription_detail.id }" value="${ ((data.prescription_detail.total=='null' || data.prescription_detail.total==null || data.prescription_detail.total=='' )? '': data.prescription_detail.total) }" placeholder="total" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('medicine_usage', __('label.form.prescription.medicine_usage')."<small>*</small>")) !!}
																						<input name="show_medicine_usage" class="form-control" id="input-medicine_usage-${ data.prescription_detail.id }" value="${ data.prescription_detail.medicine_usage }" placeholder="usage" readonly="" />
																					</div>
																				</div>
																				<div class="col-sm-2">
																					<div class="form-group">
																						{!! Html::decode(Form::label('description', __('label.form.description'))) !!}
																						<textarea name="show_description" class="form-control is_number" min="0" id="input-night-${ data.prescription_detail.id }" style="height: 38px" placeholder="description" readonly="" >${ ((data.prescription_detail.description=='null' || data.prescription_detail.description==null || data.prescription_detail.description=='')? '' : data.prescription_detail.description) }</textarea>
																					</div>
																				</div>
																				<div class="col-sm-1">
																					<div class="form-group">
																						{!! Html::decode(Form::label('', __('label.buttons.action'))) !!}
																						<div>
																							<button class="btn btn-info btn-flat btn-prevent-submit" onclick="editPrescriptionDetail('${ data.prescription_detail.id }')"><i class="fa fa-pencil-alt"></i></button>
																							<button class="btn btn-danger btn-flat btn-prevent-submit" onclick="deletePrescriptionDetail(${ data.prescription_detail.id })"><i class="fa fa-trash-alt"></i></button>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>`);

					$('.btn-prevent-submit').click(function(event) {
						event.preventDefault();
					});

					$('[name="item_medicine_name"]').val('');
					$('[name="item_medicine_usage"]').val('');
					$('[name="item_morning"]').val('0');
					$('[name="item_afternoon"]').val('0');
					$('[name="item_evening"]').val('0');
					$('[name="item_night"]').val('0');
					$('[name="item_description"]').val('');
					$('#create_prescription_item_modal').modal('hide');

					Swal.fire({
						icon: 'success',
						title: "{{ __('alert.swal.result.title.save') }}",
						confirmButtonText: "{{ __('alert.swal.button.yes') }}",
						timer: 1500
					})

					$('.empty_data_list').remove();

					$('.btn_remove_item').click(function() {
						$('.' + $(this).data('id')).remove();
					});

				}
			});

		} else {
			Swal.fire({
				icon: 'warning',
				title: "{{ __('alert.swal.title.empty_field') }}",
				text: "{{ __('alert.swal.text.empty_field') }}",
				confirmButtonText: "{{ __('alert.swal.button.yes') }}",
			})
		}
	});

	$(document).ready(function() {		
		$(document).on('mouseout change', '#item_morning, #item_afternoon, #item_evening, #item_night, #item_qty_days', function() {
			let _total = (bss_number($('#item_morning').val()) + bss_number($('#item_afternoon').val()) + bss_number($('#item_evening').val()) + bss_number($('#item_night').val())) * bss_number($('#item_qty_days').val());
			$('#item_total').val(_total);
		});

		$(document).on('mouseout change', '#edit_item_morning, #edit_item_afternoon, #edit_item_evening, #edit_item_night, #edit_item_qty_days', function() {
			let _total = (bss_number($('#edit_item_morning').val()) + bss_number($('#edit_item_afternoon').val()) + bss_number($('#edit_item_evening').val()) + bss_number($('#edit_item_night').val())) * bss_number($('#edit_item_qty_days').val());
			$('#edit_item_total').val(_total);
		});
	});
</script>
@endsection