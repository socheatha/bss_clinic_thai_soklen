@extends('layouts.app')

@section('css')
	<style type="text/css">
		.table td{
			vertical-align: middle;
		}
	</style>
@endsection

@section('content')
<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>
		<a href="{{$labor_type != 1 ? route('labor.create') . '?labor_type=1' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 1 ? 'active' : '' }}"><i class="fa fa-cubes"></i> &nbsp; {{ __('module.table.labor.create_label_1') }}</a>
		<a href="{{$labor_type != 2 ? route('labor.create') . '?labor_type=2' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 2 ? 'active' : '' }}"><i class="fa fa-cube"></i> &nbsp; {{ __('module.table.labor.create_label_2') }}</a>
		<a href="{{$labor_type != 3 ? route('labor.create') . '?labor_type=3' : '#' }}" class="btn btn-info btn-sm btn-flat {{ $labor_type == 3 ? 'active' : '' }}"><i class="fa fa-file"></i> &nbsp; {{ __('module.table.labor.create_label_3') }}</a>
		<div class="card-tools">
			@can('Labor Report')
			<a href="{{route('labor.report')}}" class="btn btn-info btn-sm btn-flat"><i class="fa fa-file-alt"></i> &nbsp;{{ __('label.buttons.report', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
			@can('Labor Index')
			<a href="{{route('labor.index')}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('labor.store'),'id' => 'submitForm','method' => 'post','class' => 'mt-3', 'autocomplete' => 'off']) !!}
	<div class="card-body">
		@include('labor.form')
		@if(in_array($labor_type, [1, 2]))
			<div class="card card-outline card-primary mt-4">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fas fa-list"></i>&nbsp;
						{{ __('alert.modal.title.labor_detail') }}
					</h3>
					@if($labor_type == 1 || $labor_type == 2)
						<div class="card-tools">
							<button type="button" class="btn btn-flat btn-sm btn-success btn-prevent-submit" id="btn_add_service"><i class="fa fa-plus"></i> {!! __('label.buttons.add_item') !!}</button>
						</div>
					@endif
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					@if($labor_type == 1 || $labor_type == 2)
						<table class="table table-bordered" width="100%">
							<thead>
								<tr>
									<th width="60px">{!! __('module.table.no') !!}</th>
									<th>{!! __('module.table.name') !!}</th>
									<th width="200px">{!! __('module.table.labor.result') !!}</th>
									<th width="200px">{!! __('module.table.labor_service.unit') !!}</th>
									<th width="200px">{!! __('module.table.labor_service.reference') !!}</th>
									<th width="90px">{!! __('module.table.action') !!}</th>
								</tr>
							</thead>
							<tbody class="item_list">
							</tbody>
						</table>
					@endif
				</div>			
				<!-- /.card-body -->
			</div>
		@endif
	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>

@include('labor.modal')

@endsection

@section('js')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">

	$('#btn_add_service').click(function () {
		$('#create_labor_item_modal').modal();
		getLaborServiceCheckList();
	});

	function getLaborServiceCheckList() {
		$('#check_all_service').iCheck('uncheck');
		$.ajax({
			url: "{{ route('labor.getLaborServiceCheckList') }}",
			method: 'post',
			data: {
				type: '{{ $labor_type }}',
			},
			success: function (data) {
				$('.service_check_list').html(data.service_check_list);
				
				$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
					checkboxClass: 'icheckbox_minimal-blue',
					radioClass   : 'iradio_minimal-blue'
				})
				$('#check_all_service').on('ifChecked', function (event) {
					$('.chb_service').iCheck('check');
					triggeredByChild = false;
				});
				$('#check_all_service').on('ifUnchecked', function (event) {
					if (!triggeredByChild) {
						$('.chb_service').iCheck('uncheck');
					}
					triggeredByChild = false;
				});
				// Removed the checked state from "All" if any checkbox is unchecked
				$('.chb_service').on('ifUnchecked', function (event) {
					triggeredByChild = true;
					$('#check_all_service').iCheck('uncheck');
				});
				$('.chb_service').on('ifChecked', function (event) {
					if ($('.chb_service').filter(':checked').length == $('.chb_service').length) {
						$('#check_all_service').iCheck('check');
					}
				});
			}
		});
	}
	
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_minimal-blue',
		radioClass   : 'iradio_minimal-blue'
	})
	$('#check_all_service').on('ifChecked', function (event) {
		$('.chb_service').iCheck('check');
		triggeredByChild = false;
	});
	$('#check_all_service').on('ifUnchecked', function (event) {
		if (!triggeredByChild) {
			$('.chb_service').iCheck('uncheck');
		}
		triggeredByChild = false;
	});
	// Removed the checked state from "All" if any checkbox is unchecked
	$('.chb_service').on('ifUnchecked', function (event) {
		triggeredByChild = true;
		$('#check_all_service').iCheck('uncheck');
	});
	$('.chb_service').on('ifChecked', function (event) {
		if ($('.chb_service').filter(':checked').length == $('.chb_service').length) {
			$('#check_all_service').iCheck('check');
		}
	});

	$('.btn-prevent-submit').click(function (event) {
		event.preventDefault();
	});

	$('#btn_add_item').click(function (event) {
		event.preventDefault();
		$(this).prop('disabled', true);

		setTimeout(() => {
			$(this).prop('disabled', false);
		}, 2000);

		var service_ids = [];
		var n = $( ".labor_item" ).length;
		$( ".chb_service" ).each(function( index ) {
			if ($(this).is(':checked')) {
				service_ids.push($(this).val());
			}
		});

		if (service_ids.length != 0) {
			$.ajax({
				url: "{{ route('labor.getCheckedServicesList') }}",
				method: 'post',
				data: {
					ids: service_ids,
					no: n,
				},
				success: function (data) {
					$('.item_list').append(data.checked_services_list);
					$('#check_all_service').iCheck('uncheck');
					$('#category_id').val('').trigger('change');
					$('#service_check_list').html('');
					$('#create_labor_item_modal').modal('hide');
					$(".is_number").keyup(function () {
						isNumber($(this))
					});
				}
			});
		}

	});

	function removeCheckedService(id) {
		$('#'+id).remove();
	}

	$(".select2_pagination").change(function () {
		$('[name="txt_search_field"]').val($('.select2-search__field').val());
	});
	
	function select2_search (term) {
		$(".select2_pagination").select2('open');
		var $search = $(".select2_pagination").data('select2').dropdown.$search || $(".select2_pagination").data('select2').selection.$search;
		$search.val(term);
		$search.trigger('keyup');
	}

	$(".select2_pagination").select2({
		theme: 'bootstrap4',
		placeholder: "{{ __('label.form.choose') }}",
		allowClear: true,
		ajax: {
			url: "{{ route('patient.getSelect2Items') }}",
			method: 'post',
			dataType: 'json',
			data: function(params) {
				return {
						term: params.term || '',
						page: params.page || 1
				}
			},
			cache: true
		}
	});	

	function load_service_info(id, _this){
		_this = $(_this);
		let value = _this.val();
		if($('option[value="'+value+'"]').data('price')) $('#input-price-'+id).val($('option[value="'+value+'"]').data('price'));
		if($('option[value="'+value+'"]').data('description')) $('#input-description-'+id).val($('option[value="'+value+'"]').data('description'));
	}
</script>
@endsection