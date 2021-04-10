@extends('layouts.app')

@section('css')
	{{ Html::style('/css/invoice-print-style.css') }}
	<style type="text/css">
		.btn-print-echoes{
			top: -30px;
			right: 55px;
		}
	</style>
@endsection

@section('content')

<div class="card">
	<div class="card-header">
		<b>{!! Auth::user()->subModule() !!}</b>
		
		<div class="card-tools">
			@can('Echo Index')
			<a href="{{route('echoes.index', $type)}}" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-table"></i> &nbsp;{{ __('label.buttons.back_to_list', [ 'name' => Auth::user()->module() ]) }}</a>
			@endcan
		</div>

		<!-- Error Message -->
		@component('components.crud_alert')
		@endcomponent

	</div>

	{!! Form::open(['url' => route('echoes.update', [$type, $echoes->id]),'method' => 'post', 'enctype'=>'multipart/form-data','class' => 'mt-3']) !!}
	{!! Form::hidden('_method', 'PUT') !!}

	<div class="card-body">
		@include('echoes.form', ['pre_select_obj' => $echoes])
	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>

<div class="position-relative">
	@can("Echo Print")
		<button type="button" class="btn btn-flat btn-success position-absolute mr-9 mt-5 btn-print-echoes" data-url="{{ route('echoes.print', [$type, $echoes->id]) }}"><i class="fa fa-print"></i> {{ __("label.buttons.print") }}</button>
	@endCan
</div>

<div class="pb-2 print-preview">
	{!! $echoes_preview !!}
</div>

@include('components.confirm_password')


<span class="sr-only" id="deleteAlert" data-title="{{ __('alert.swal.title.delete', ['name' => Auth::user()->module()]) }}" data-text="{{ __('alert.swal.text.unrevertible') }}" data-btnyes="{{ __('alert.swal.button.yes') }}" data-btnno="{{ __('alert.swal.button.no') }}" data-rstitle="{{ __('alert.swal.result.title.success') }}" data-rstext="{{ __('alert.swal.result.text.delete') }}"> Delete Message </span>


@endsection

@section('js')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
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
		$(".btn-print-echoes").on("click", function (e) {
			var myUrl = $(this).attr('data-url');
			e.preventDefault();
			openPrintWindow(myUrl, "to_print");
		});
	});
	function select2_search (term) {
		$(".select2_pagination").select2('open');
		var $search = $(".select2_pagination").data('select2').dropdown.$search || $(".select2_pagination").data('select2').selection.$search;
		$search.val(term);
		$search.trigger('keyup');
	}

	$( document ).ready(function() {
		var data = [];
		$(".select2_pagination").each(function () {
			data.push({id:'{{ $echoes->patient_id }}', text:'PT-{{ str_pad($echoes->patient_id, 6, "0", STR_PAD_LEFT) }} :: {{ $echoes->patient->name }}'});
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
							term: params.term || '{{ $echoes->patient_id }}',
							page: params.page || 1
					}
				},
				cache: true
			}
		});
	});

	$('.select2_pagination').val('{{ $echoes->id }}').trigger('change')

	var editor = CKEDITOR.replace('my-editor', {
		height: '350',
		font_names: 'Calibrib Bold; Calibri Italic; Calibri; Roboto Regular; Roboto Bold; Khmer OS Battambang; Khmer OS Muol Light; Khmer OS Content; Khmer OS Kuolen;',
		toolbar: [
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'ExportPdf', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'insert', items: ['Table' ] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ]},
		]
	});

	$('#echo_default_description_id').change(function () {
		if ($(this).val()!='') {
			$.ajax({
				url: "{{ route('echo_default_description.getDetail') }}",
				type: 'post',
				data: {
					id : $(this).val()
				},
			})
			.done(function( result ) {
				editor.setData(result.echo_default_description.description);
			});
		}
	});
</script>
@endsection