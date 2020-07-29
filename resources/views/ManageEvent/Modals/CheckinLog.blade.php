<div role="dialog"  class="modal fade" style="display: none;">
    <style>
        .well.nopad {
            padding: 0px;
        }
    </style>

    <div class="modal-dialog">
        <div class="modal-content">
           
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 class="modal-title">@lang('basic.checkin_history')</h3>
                <div class="well nopad bgcolor-white p0">
                    <div class="table-responsive">
                        <table class="table table-hover" >
                            <thead>
                            <th>
                                @lang("Attendee.check_clear_time")
                            </th>
							<th>
                                @lang("basic.checkin_time")(s)
                            </th>
                            <th>
                                @lang("Attendee.check_in_time")
                            </th>
                            <th>
                                @lang("Attendee.check_out_time")
                            </th>                           
                            </thead>
                            <tbody>
                                @foreach($checkinLog as $check_item)
                                <tr>
                                    <td>
                                       {{$check_item->clear_checkin_at}}
                                    </td>
									<td>
                                       {{gmdate("H:i:s", $check_item->period_check)}} 									   
                                    </td>
									<td>
                                        {{$check_item->checkin_at}}
                                    </td>
                                    <td>
										{{$check_item->checkout_at}}
                                    </td>
                                </tr>
                                @endforeach                                
                            </tbody>
                        </table>
                    </div>
					 
                </div> 
						
            </div> <!-- /end modal body-->

            <div class="modal-footer">
                {!! Form::button(trans("ManageEvent.close"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
</div>
