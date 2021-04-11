@extends('layouts.app')

@section('css')
<link href="{{ asset('/css/daterangepicker.css') }}" rel="stylesheet">
<style type="text/css">
	div.prescription-detail-expanded{
		position: relative;
	}
	.dt-server td{
		vertical-align: middle;
	}
</style>
@endsection

@section('content')
	<div class="card">
		<div class="card-header">
			<b>{!! Auth::user()->subModule() !!}</b>
			
			<div class="card-tools">
				@can('Prescription Create')
				<a href="{{route('prescription.create')}}" class="btn btn-sm btn-success btn-flat"><i class="fa fa-plus"></i> &nbsp;{{ __('label.buttons.create') }}</a>
				@endcan
			</div>

			<!-- Error Message -->
			@component('components.crud_alert')
			@endcomponent

		</div>
		<div class="card-body">
			<div class="row justify-content-center">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Html::decode(Form::label('date', __('label.form.prescription.choose_date')." <small>*</small>")) !!}
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
							</div>
							<input type="text" class="form-control pull-right bssDateRangePicker" autocomplete="off">
							<input type="hidden" class="form-control" id="from">
							<input type="hidden" class="form-control" id="to">
						</div>
					</div>
				</div>
			</div>
			
      		<table class="table table-bordered dt-server expandable-table" width="100%" id="prescription_table">
				<thead>
					<tr>
						{{-- <th class="text-center" width="30px"></th> --}}
						<th class="text-center" width="8%">{!! __('module.table.prescription.code') !!}</th>
						<th class="text-center" width="8%">{!! __('module.table.date') !!}</th>
						<th class="text-center" width="25%">{!! __('module.table.prescription.pt_name') !!}</th>
						<th class="text-center" width="10%">{!! __('module.table.prescription.pt_phone') !!}</th>
						<th width="12%" class="text-center">{!! __('module.table.action') !!}</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
    <span class="sr-only" id="deleteAlert" data-title="{{ __('alert.swal.title.delete', ['name' => Auth::user()->module()]) }}" data-text="{{ __('alert.swal.text.unrevertible') }}" data-btnyes="{{ __('alert.swal.button.yes') }}" data-btnno="{{ __('alert.swal.button.no') }}" data-rstitle="{{ __('alert.swal.result.title.success') }}" data-rstext="{{ __('alert.swal.result.text.delete') }}"> Delete Message </span>
	</div>

	{{-- Password Confirm modal --}}
	@component('components.confirm_password')@endcomponent
	
@endsection

@section('js')
	<script src="{{ asset('/js/daterangepicker.js') }}"></script>
	<script type="text/javascript">
    // $('#dateRangePicker').val('');

	
		getDatatable(moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));


		function getDatatable(from, to) {
			// Load Data to datatable
			$('#prescription_table').DataTable().destroy();
			dataTablePrescription = $('#prescription_table').DataTable({
				"language": (('{{ session('locale') }}' == 'en')? '' : datatableKH),
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
				order: [[0, "desc"]],
				ajax: {
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{ route('prescription.getDatatable') }}',
					data: { 'from' : from, 'to' : to },
					type: 'post',
					dataSrc: function(json) { return json.data;  }
				},
				columns: [
					// {data: 'row_detail', defaultContent: '<i class="fa fa-plus-circle text-primary"></i>', className: 'details-control text-center', searchable: false, sortable: false },
					{data: 'code', name: 'code', className: 'text-center'},
					{data: 'date', name: 'date', className: 'text-center'},
					{data: 'pt_name', name: 'pt_name'},
					{data: 'pt_phone', name: 'pt_phone'},
					{data: 'actions', name: 'actions', className: 'text-right', searchable: false, sortable: false}
				],
				rowCallback: function( row, data ) {

					$('td:eq(4)', row).html( `@Can("Prescription Edit")
						<button type="button" data-url="/prescription/${ data.id }/print" class="btn btn-sm btn-flat btn-success btn-print-prescription"><i class="fa fa-print"></i></button>
					@endCan 
					@Can("Prescription Edit")
						<a href="/prescription/${ data.id }/edit" class="btn btn-sm btn-flat btn-info"><i class="fa fa-pencil-alt"></i></a>
					@endCan 
					@Can("Prescription Delete")
						<button type="button" class="btn btn-sm btn-flat btn-danger BtnDeleteConfirm" value="${ data.id }"><i class="fa fa-trash-alt"></i></button>
						<form action="/prescription/${ data.id }/delete" id="form-item-${ data.id }" class="sr-only" method="POST" accept-charset="UTF-8">
							{{ csrf_field() }}
							<input type="hidden" name="_method" value="DELETE" />
							<input type="hidden" name="passwordDelete" value="" />
						</form>
					@endCan` );

				},
				"initComplete": function( settings, json ) {

					$('#prescription_table [type="search"]').addClass('sr-only');
					$('#prescription_table_search_input').remove();
					$('#prescription_table_table_filter').append('<input type="text" class="form-control input-sm" id="prescription_table_search_input">');
					$('#prescription_table_search_input').keyup(function (e) {
						if (e.keyCode === 13) {
							$('#prescription_table [type="search"]').val($(this).val()).keyup();
						}
					});

					setTimeout( function () {
						if ($('[name="txt_filter_search"]').val() != '' ) {
							$('#prescription_table_search_input').val($('[name="txt_filter_search"]').val());
							$('#prescription_table [type="search"]').val($('[name="txt_filter_search"]').val()).keyup();
							$('[name="txt_filter_search"]').val('');
						}

					}, 250);

				},
				"drawCallback": function( settings, json ) {
					$('.BtnDeleteConfirm').click(function () {
						$('#item_id').val($(this).val());
						$('#modal_confirm_delete').modal();
					});

					$('.submit_confirm_password').click(function () {
						var id = $('#item_id').val();
						var password_confirm = $('#password_confirm').val();
						$('[name="passwordDelete"]').val(password_confirm);
						if (password_confirm!='') {
							$.ajax({
								url: "{{ route('user.password_confirm') }}",
								type: 'post',
								data: {id:id, _token:'{{ csrf_token() }}', password_confirm:password_confirm},
							})
							.done(function( result ) {
								if(result == true){
									bss_swal_Success("{{ __('alert.swal.result.title.success') }}", "{{ __('alert.swal.button.yes') }}", () => {
										$( "form" ).submit(function( event ) {
											$('button').attr('disabled','disabled');
										});
										$('[name="passwordDelete"]').val(password_confirm);
										$("#form-item-"+id).submit();
									});
								}else{
									bss_swal_Warning("{{ __('alert.swal.result.title.wrong',['name'=>'ពាក្យសម្ងាត់']) }}", "{{ __('alert.swal.button.yes') }}", () => $('#modal_confirm_delete').modal());
								}
							});
						}else{
							bss_swal_Warning("{{ __('alert.swal.title.empty') }}", "{{ __('alert.swal.button.yes') }}", () => $('#modal_confirm_delete').modal());
						}
					});
					
					function openPrintWindow(url, name) {
						var printWindow = window.open(url, name, "width="+ screen.availWidth +",height="+ screen.availHeight +",_blank");
						var printAndClose = function () {
							if (printWindow.document.readyState == 'complete') {
								clearInterval(sched);
								printWindow.print();
								printWindow.close();
							}
						}  
							var sched = setInterval(printAndClose, 2000);
					};

					jQuery(document).ready(function ($) {
						$(".btn-print-prescription").on("click", function (e) {
							var myUrl = $(this).attr('data-url');
							e.preventDefault();
							openPrintWindow(myUrl, "to_print");
						});
					});
				}
			});
		}
	</script>
@endsection