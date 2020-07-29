<script>
$(document).ready(function() {
	$("#is_hidden").click(function(){
		console.log($("#is_hidden").is(":checked"));
		if($("#is_hidden").is(":checked")){
			$("#access_code_div").removeClass("hidden");
		}else{
			$("#access_code_div").addClass("hidden");
		}
	});
});
</script>
<div role="dialog"  class="modal fade" style="display: none;">
   {!! Form::open(array('url' => route('postCreateTicket', array('event_id' => $event->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-ticket"></i>
                    @lang("ManageEvent.create_ticket")</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('title', trans("ManageEvent.ticket_title"), array('class'=>'control-label required')) !!}
                            {!!  Form::text('title', Input::old('title'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>trans("ManageEvent.ticket_title_placeholder")
                                        ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('price', trans("ManageEvent.ticket_price"), array('class'=>'control-label required')) !!}
                                    {!!  Form::text('price', Input::old('price'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>trans("ManageEvent.price_placeholder")
                                                ))  !!}


                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('quantity_available', trans("ManageEvent.quantity_available"), array('class'=>' control-label')) !!}
                                    {!!  Form::text('quantity_available', Input::old('quantity_available'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>trans("ManageEvent.quantity_available_placeholder")
                                                )
                                                )  !!}
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans("ManageEvent.ticket_description"), array('class'=>'control-label')) !!}
                            {!!  Form::text('description', Input::old('description'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                        </div>
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('ticket_per_type', trans("tickets user type"), array('class'=>' control-label')) !!}
                                    <select class="form-control" id="ticket_per_type" name="ticket_per_type">
										<option value="Couples" selected>Couples</option>
										<option value="Single Male">Single Male</option>
										<option value="Single Female">Single Female</option>
									</select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                
                            </div>
                        </div>
						<div class="form-group">
                            {!! Form::label('note', trans("ticket note"), array('class'=>'control-label')) !!}
                            {!!  Form::text('note', Input::old('note'),
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('start_sale_date', trans("ManageEvent.start_sale_on"), array('class'=>' control-label')) !!}
                                    {!!  Form::text('start_sale_date', Input::old('start_sale_date'),
                                                    [
                                                'class'=>'form-control start hasDatepicker ',
                                                'data-field'=>'datetime',
                                                'data-startend'=>'start',
                                                'data-startendelem'=>'.end',
                                                'readonly'=>''

                                            ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    {!!  Form::label('end_sale_date', trans("ManageEvent.end_sale_on"),
                                                [
                                            'class'=>' control-label '
                                        ])  !!}
                                    {!!  Form::text('end_sale_date', Input::old('end_sale_date'),
                                            [
                                        'class'=>'form-control end hasDatepicker ',
                                        'data-field'=>'datetime',
                                        'data-startend'=>'end',
                                        'data-startendelem'=>'.start',
                                        'readonly'=>''
                                    ])  !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('min_per_person', trans("ManageEvent.minimum_tickets_per_order"), array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('min_per_person', 1, 100, 1, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('max_per_person', trans("ManageEvent.maximum_tickets_per_order"), array('class'=>' control-label')) !!}
                                    {!! Form::selectRange('max_per_person', 1, 100, 30, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-checkbox">
                                        {!! Form::checkbox('is_hidden', 1, false, ['id' => 'is_hidden']) !!}
                                        {!! Form::label('is_hidden', trans("ManageEvent.hide_this_ticket"), array('class'=>' control-label')) !!}
                                    </div>

                                </div>
                            </div>
				<div class="col-md-12 hidden" id="access_code_div">
								<h4>{{ __('AccessCodes.select_access_code') }}</h4>
								@if($event->access_codes->count())
									<?php
									$isSelected = false;
									$selectedAccessCodes="";										
									?>
									@foreach($event->access_codes as $access_code)
										<div class="row">
											<div class="col-md-12">
												<div class="custom-checkbox mb5">
													{!! Form::checkbox('ticket_access_codes[]', $access_code->id, false, ['id' => 'ticket_access_code_' . $access_code->id, 'data-toggle' => 'toggle']) !!}
													{!! Form::label('ticket_access_code_' . $access_code->id, $access_code->code) !!}
												</div>
											</div>
										</div>
									@endforeach
								@else
									<div class="alert alert-info">
										@lang("AccessCodes.no_access_codes_yet")
									</div>
								@endif
							</div>
							<div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-radio">
                                        {!! Form::radio('is_normal', 2, true, ['id' => 'is_normal']) !!}
                                        {!! Form::label('is_normal', trans("basic.normal"), array('class'=>' control-label')) !!}
                                    </div>

                                </div>
                            </div>
							<div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-radio">
                                        {!! Form::radio('is_normal', 3, false, ['id' => 'is_free_n']) !!}
                                        {!! Form::label('is_free_n', trans("basic.free"), array('class'=>' control-label')) !!}
                                    </div>

                                </div>
                            </div>
							<div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-radio">
                                        {!! Form::radio('is_normal', 4, false, ['id' => 'is_suggested_donation']) !!}
                                        {!! Form::label('is_suggested_donation', trans("basic.suggested_donation"), array('class'=>' control-label')) !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>                    

                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit(trans("ManageEvent.create_ticket"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>