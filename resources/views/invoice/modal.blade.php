<!-- New Invoice Item Modal -->
<div class="modal add fade" id="create_invoice_item_modal">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">{{ __('alert.modal.title.add_item') }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									{!! Html::decode(Form::label('item_service_id', __('label.form.invoice.service')." <small>*</small>")) !!}
									<div class="input-group">
										{!! Form::select('item_service_id', $services, '', ['class' => 'form-control select2 service','placeholder' => __('label.form.choose'),'required']) !!}
										<div class="input-group-append">
											<button type="button" class="btn btn-flat btn-info add_service"><i class="fa fa-plus-circle"></i></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									{!! Html::decode(Form::label('item_price', __('label.form.invoice.price')." <small>*</small>")) !!}
									{!! Form::text('item_price', '', ['class' => 'form-control','placeholder' => 'price','required']) !!}
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									{!! Html::decode(Form::label('item_qty', __('label.form.invoice.qty')." <small>*</small>")) !!}
									{!! Form::text('item_qty', '', ['class' => 'form-control','placeholder' => 'qauntity','required']) !!}
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							{!! Html::decode(Form::label('item_description', __('label.form.description')." <small>*</small>")) !!}
							{!! Form::textarea('item_description', '', ['class' => 'form-control','placeholder' => 'description','rows' => '2','required']) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-flat btn-danger" data-dismiss="modal">{{ __('alert.swal.button.no') }}</button>
				<button type="button" class="btn btn-flat btn btn-success" id="btn_add_item" data-dismiss="modal">{{ __('alert.swal.button.yes') }}</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->