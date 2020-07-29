<html>
    <head>
        <title>
            @lang('basic.print_checkinlog_title')
        </title>

        <!--Style-->
       {!!HTML::style('assets/stylesheet/application.css')!!}
        <!--/Style-->

        <style type="text/css">
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                padding: 3px;
            }
            table {
                font-size: 13px;
            }
        </style>
    </head>
    <body style="background-color: #FFFFFF;" onload="window.print();">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>@lang("Attendee.ticket")</th>
					<th>@lang("Attendee.name")</th>
					<th>@lang("Attendee.business_name")</th>
					<th>@lang("Attendee.ticket_type")</th>
					<th>@lang("Attendee.profile_type")</th>
                    <th>@lang("Attendee.email")</th>                    
                    <th>@lang("Attendee.check_in_time")</th>					
                </tr>
            </thead>
            <tbody>
                @foreach($attendees as $attendee)
                <tr class="attendee_{{$attendee->id}} {{$attendee->is_cancelled ? 'danger' : ''}}">
					<td>
						{{{$attendee->ticket->title}}}
					</td>
					<td>
						{{{$attendee->full_name}}}
					</td>
					<td>
						{{{$attendee->business_name}}}
					</td>
					<td>
						@if($attendee->ticket->is_hidden == '1')
							<span >@lang("basic.hide_short")</span>
						@elseif($attendee->ticket->is_normal == '2')
							<span >@lang("basic.normal_short")</span>
						@elseif($attendee->ticket->is_normal == '3')
							<span >@lang("basic.free_short")</span>
						@elseif($attendee->ticket->is_normal == '4')
							<span >@lang("basic.suggested_donation_short")</span>	
						@elseif($attendee->ticket->is_normal == '5')
							<span >Walkin</span>	
						@endif
					</td>
					<td>
						{{{$attendee->ticket->type}}}
					</td>	
					<td>
						<a data-modal-id="MessageAttendee" href="javascript:void(0);" class="loadModal"
							data-href="{{route('showMessageAttendee', ['attendee_id'=>$attendee->id])}}"
							> {{$attendee->email}}</a>
					</td>  
					 <td style="width:150px;text-align:center;">
						{{gmdate("H:i:s", $attendee->period_in)}} 
					</td>
				</tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
