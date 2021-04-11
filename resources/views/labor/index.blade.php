@extends('layouts.app')

@section('css')
<link href="{{ asset('/css/daterangepicker.css') }}" rel="stylesheet">
<style type="text/css">
	/* .btn-print-labor{
		position: absolute;
		top: 5px;
		right: 35px;
	} */
	div.labor-detail-expanded{
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
				@can('Labor Report')
				<a href="{{route('labor.report')}}" class="btn btn-info btn-sm btn-flat"><i class="fa fa-file-alt"></i> &nbsp;{{ __('label.buttons.report', [ 'name' => Auth::user()->module() ]) }}</a>
				@endcan
				@can('Labor Create')
				<a href="{{route('labor.create')}}" class="btn btn-sm btn-success btn-flat"><i class="fa fa-plus"></i> &nbsp;{{ __('label.buttons.create') }}</a>
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
						{!! Html::decode(Form::label('date', __('label.form.labor.choose_date')." <small>*</small>")) !!}
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
							</div>
							<input type="text" class="form-control pull-right" id="dateRangePicker" autocomplete="off">
							<input type="hidden" class="form-control" value="" id="from">
							<input type="hidden" class="form-control" value="" id="to">
						</div>
					</div>
				</div>
			</div>
			
      <table class="table table-bordered dt-server expandable-table" width="100%" id="labor_table">
				<thead>
					<tr>
						<th class="text-center" width="10%">{!! __('module.table.labor.labor_number') !!}</th>
						<th class="text-center" width="10%">{!! __('module.table.date') !!}</th>
						<th class="text-center" width="10%">{!! __('label.form.labor.price') !!}</th>
						<th class="text-center">{!! __('module.table.labor.pt_name') !!}</th>
						<th class="text-center" width="10%">{!! __('module.table.labor.pt_phone') !!}</th>
						<th class="text-center" width="10%">{!! __('module.table.labor.pt_age') !!}</th>
						<th class="text-center" width="10%">{!! __('label.form.labor.pt_gender') !!}</th>
						<th width="12%" class="text-center">{!! __('module.table.action') !!}</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
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

		
		$('#dateRangePicker').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().startOf('month'),
        endDate  : moment().endOf('month')
      },
      function (start, end) {
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
				getDatatable($('#from').val(), $('#to').val(), $('#labor_number').val())
      }
    )
    // $('#dateRangePicker').val('');

	
		$('#from').val(moment().startOf('month').format('YYYY-MM-DD'));
		$('#to').val(moment().endOf('month').format('YYYY-MM-DD'));
		getDatatable($('#from').val(), $('#to').val(), $('#labor_number').val());


		$('#btn-search-filter').click(function () {
			if ($('#from').val()!='' && $('#to').val()!='') {
				getDatatable($('#from').val(), $('#to').val(), $('#labor_number').val())
			}
		});

		function getDatatable(from, to, labor_number) {
			// Load Data to datatable
			$('#labor_table').DataTable().destroy();
			dataTableLabor = $('#labor_table').DataTable({
				"language": (('{{ session('locale') }}' == 'en')? '' : datatableKH),
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
				order: [[0, "desc"]],
				ajax: {
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: '{{ route('labor.getDatatable') }}',
					data: { 'from' : from, 'to' : to, 'labor_number' : labor_number },
					type: 'post',
					dataSrc: function(json) { return json.data;  }
				},
				columns: [
					{data: 'labor_number', name: 'labor_number', className: 'text-center'},
					{data: 'date', name: 'date', className: 'text-center'},
					{data: 'formated_price', name: 'formated_price', className: 'text-center font-weight-bold'},
					{data: 'pt_name', name: 'pt_name'},
					{data: 'pt_phone', name: 'pt_phone'},
					{data: 'pt_age', name: 'pt_age', className: 'text-center'},
					{data: 'pt_gender', name: 'pt_gender', className: 'text-center'},
					{data: 'actions', name: 'actions', className: 'text-center', searchable: false, sortable: false}
				],
				rowCallback: function( row, data ) {

					$('td:eq(7)', row).html( `@Can("Labor Print")
						<button type="button" data-url="/labor/${ data.id }/print" class="btn btn-sm btn-flat btn-success btn-print-labor"><i class="fa fa-print"></i></button>
					@endCan 
					@Can("Labor Edit")
						<a href="/labor/${ data.id }/edit" class="btn btn-sm btn-flat btn-info"><i class="fa fa-pencil-alt"></i></a>
					@endCan 
					@Can("Labor Delete")
						<button type="button" class="btn btn-sm btn-flat btn-danger BtnDeleteConfirm" value="${ data.id }"><i class="fa fa-trash-alt"></i></button>
						<form action="/labor/${ data.id }/delete" id="form-item-${ data.id }" class="sr-only" method="POST" accept-charset="UTF-8">
							{{ csrf_field() }}
							<input type="hidden" name="_method" value="DELETE" />
							<input type="hidden" name="passwordDelete" value="" />
						</form>
					@endCan` );

				},
				"initComplete": function( settings, json ) {

					$('#labor_table [type="search"]').addClass('sr-only');
					$('#labor_table_search_input').remove();
					$('#labor_table_table_filter').append('<input type="text" class="form-control input-sm" id="labor_table_search_input">');
					$('#labor_table_search_input').keyup(function (e) {
						if (e.keyCode === 13) {
							$('#labor_table [type="search"]').val($(this).val()).keyup();
						}
					});

					setTimeout( function () {
						if ($('[name="txt_filter_search"]').val() != '' ) {
							$('#labor_table_search_input').val($('[name="txt_filter_search"]').val());
							$('#labor_table [type="search"]').val($('[name="txt_filter_search"]').val()).keyup();
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
										Swal.fire({
											icon: 'success',
											title: "{{ __('alert.swal.result.title.success') }}",
											confirmButtonText: "{{ __('alert.swal.button.yes') }}",
											timer: 1500
										})
										.then((result) => {
											$( "form" ).submit(function( event ) {
												$('button').attr('disabled','disabled');
											});
											$('[name="passwordDelete"]').val(password_confirm);
											$("#form-item-"+id).submit();
										})
									}else{
										Swal.fire({
											icon: 'warning',
											title: "{{ __('alert.swal.result.title.wrong',['name'=>'ពាក្យសម្ងាត់']) }}",
											confirmButtonText: "{{ __('alert.swal.button.yes') }}",
											timer: 2500
										})
										.then((result) => {
											$('#modal_confirm_delete').modal();
										})
									}
							});
						}else{
							Swal.fire({
								icon: 'warning',
								title: "{{ __('alert.swal.title.empty') }}",
								confirmButtonText: "{{ __('alert.swal.button.yes') }}",
								timer: 1500
							})
							.then((result) => {
								$('#modal_confirm_delete').modal();
							})
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
						$(".btn-print-labor").on("click", function (e) {
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