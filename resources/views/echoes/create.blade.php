@extends('layouts.app')

@section('css')
	{{-- {{ Html::style('/css/echoes-print-style.css') }} --}}
	<style type="text/css">
	
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

	{!! Form::open(['url' => route('echoes.store', $type),'method' => 'post', 'enctype'=>'multipart/form-data', 'class' => 'mt-3']) !!}
	<div class="card-body">
		@include('echoes.form')

	</div>
	<!-- ./card-body -->
	
	<div class="card-footer text-muted text-center">
		@include('components.submit')
	</div>
	<!-- ./card-Footer -->
	{{ Form::close() }}

</div>
	
<br/>
<br/>
<br/>
<br/>

@endsection

@section('js')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
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