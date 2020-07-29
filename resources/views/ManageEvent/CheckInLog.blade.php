@extends('Shared.Layouts.Master')

@section('title')
@parent
@lang("Attendee.event_attendees")
@stop


@section('page_title')
<i class="ico-users"></i>
{{$event->title}}
@lang("ManageEvent.check-in-log")
@lang("instruction.instruction13")
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop


@section('head')

@stop

@section('page_header')

<div class="col-md-10 col-sm-10">
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group-responsive">
			<a class="btn btn-success" href="{{route('showPrintCheckinLog', ['event_id'=>$event->id])}}" target="_blank"><i class="ico-print"></i> @lang("basic.print_checkinlog_list")</a>
		</div>	
		<div class="btn-group btn-group-responsive">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="ico-users"></i> @lang("ManageEvent.export") <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{route('showExportCheckinLog', ['event_id'=>$event->id,'export_as'=>'xls'])}}">@lang("File_format.Excel_xlsx")</a></li>
                <li><a href="{{route('showExportCheckinLog', ['event_id'=>$event->id,'export_as'=>'xls'])}}">@lang("File_format.Excel_xls")</a></li>
                <li><a href="{{route('showExportCheckinLog', ['event_id'=>$event->id,'export_as'=>'csv'])}}">@lang("File_format.csv")</a></li>
                <li><a href="{{route('showExportCheckinLog', ['event_id'=>$event->id,'export_as'=>'html'])}}">@lang("File_format.html")</a></li>
            </ul>
        </div>
		
	   <div class="btn-group btn-group-responsive">
            <button data-modal-id="MessageAttendees" href="javascript:void(0);" data-href="{{route('showMessageAttendees', ['event_id'=>$event->id])}}" class="loadModal btn btn-success" type="button"><i class="ico-envelope"></i> @lang("ManageEvent.message_attendees")</button>
        </div>
		<div class="btn-group btn-group-responsive">
			
			
			<div class="btn-group">
			<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang("basic.certificate") <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu">                                        
				@foreach($sign_certs as $sign_cert)
				<li>
					<a href="#" onclick="setCertificate('{{$event->id}}', '{{$sign_cert->id}}', '{{$sign_cert->title}}')">{{$sign_cert->title}}</a>
				</li>
				@endforeach
			</ul>
			</div>
		</div>
		<div class="btn-group btn-group-responsive hidden">
			<input type="button" class="btn btn-success" value="Send All Certificates" onclick="sendCeu()" />
		</div>
		<div class="btn-group btn-group-responsive">
			<a href="{{route('exportAllCertificates', array('event_id' => $event->id))}}" class="btn btn-success" target="_blank">
				Export All Certificates
			</a>
		</div>
	</div>	
</div>
<div class="col-md-2 col-sm-2">
   {!! Form::open(array('url' => route('showCheckInLog', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
    <div class="input-group">
        <input name="q" value="{{$q or ''}}" placeholder="@lang("Attendee.search_attendees")" type="text" class="form-control" />
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
   {!! Form::close() !!}
</div>
<div class="col-md-12 col-sm-12" style="margin-top:10px;">
	<div class="btn-toolbar" role="toolbar">
		<div class="btn-group btn-group-responsive">
			<input class="form-control" type="number" id="ceu_total" style="min-width:100px;" value="{{$event->ceu_total}}" size="4">
		</div>
		<div class="btn-group btn-group-responsive" style="line-height:33px;font-size:15px;">
			HRS
		</div>
		<div class="btn-group btn-group-responsive" style="line-height:33px;font-size:25px;">
			=
		</div>
		<div class="btn-group btn-group-responsive">
			<input class="form-control" type="number" id="ceu_hr" style="min-width:100px;" value="{{$event->ceu_hr}}" size="4">
		</div>
		<div class="btn-group btn-group-responsive" style="margin-top:5px;">
			<div style="position:relative;min-width:100px;height:25px;border:0;padding:0;margin:0;">
			  <select style="position:absolute;top:0px;left:0px;width:100px; height:25px;line-height:20px;margin:0;padding:0;"
					  onchange="document.getElementById('ceu_unit').value=this.options[this.selectedIndex].text; document.getElementById('idValue').value=this.options[this.selectedIndex].value;">
				<option></option>
				<option value="CEU">CEU</option>
				<option value="Point">Point</option>
				<option value="HRS">HRS</option>
				<option value="Credits">Credits</option>
			  </select>
			  <input type="text" id="ceu_unit" value="{{$event->ceu_unit}}"
					 placeholder="add/select a value" onfocus="this.select()"
					 style="position:absolute;top:0px;left:0px;width:83px;width:80px\9;#width:80px;height:25px; height:26px\9;#height:25px;border:1px solid #556;"  >
			  <input name="idValue" id="idValue" type="hidden">
			</div>
		</div>
		<div class="btn-group btn-group-responsive">
			<input type="button" class="btn btn-success" value="save" onclick="saveCeu()" />
		</div>
		<div class="btn-group btn-group-responsive" style="line-height:33px;">
			<span id="cert_title">{{$event->certificate_title}}</span>
		</div>
    </div>
</div>
@stop


@section('content')

<!--Start Attendees table-->
<div class="row">
    <div class="col-md-12">
        @if($attendees->count())
        <div class="panel">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10%">
                               {!!Html::sortable_link(trans("Attendee.ticket"), $sort_by, 'ticket_id', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>
							<th width="15%">
                               {!!Html::sortable_link(trans("Attendee.name"), $sort_by, 'first_name', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>    
							<th width="15%">
                               {!!Html::sortable_link(trans("Attendee.business_name"), $sort_by, 'business_name', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>  							
							<th width="10%">
                               {!!Html::sortable_link(trans("Attendee.ticket_type"), $sort_by, 'is_normal', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>
							<th width="10%">
                               {!!Html::sortable_link(trans("Attendee.profile_type"), $sort_by, 'type', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>
                            <th width="15%">
                               {!!Html::sortable_link(trans("Attendee.email"), $sort_by, 'email', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th> 
							<th width="10%">
                               {!!Html::sortable_link(trans("Attendee.check_in_time"), $sort_by, 'email', $sort_order, ['q' => $q , 'page' => $attendees->currentPage()])!!}
                            </th>								
							<th></th>	
							<th></th>	
							<th></th>								
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
							<td style="width:50px;text-align:center;">
                            
								<a data-modal-id="CheckinLog" href="javascript:void(0);" class="loadModal"
                                    data-href="{{route('postCheckinLog', ['attendee_id'=>$attendee->id])}}"
                                    > {{gmdate("H:i:s", $attendee->period_in)}} </a>
							
                            </td>								
												
							<td>
								<a href="{{route('showCreateCertificate', array('event_id' => $event->id,'attendee_id' => $attendee->id))}}" class="btn btn-success">
									view
								</a>
							</td>
							<td>							
								<a href="#" onclick="sendCertificate('{{$event->id}}', '{{$sign_cert->id}}', '{{$attendee->id}}')" class="btn btn-success">send</a>								
							</td>	
							<td>
								<a href="{{route('showPrintCertificate', array('event_id' => $event->id,'attendee_id' => $attendee->id))}}" class="btn btn-success" target="_blank">
									print
								</a>
							</td>							
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else

        @if(!empty($q))
        @include('Shared.Partials.NoSearchResults')
        @else
        @include('ManageEvent.Partials.AttendeesBlankSlate')
        @endif

        @endif
    </div>
	<script>
		function sendCertificate(event_id, cert_id, attendee_id){
			var formData = new FormData();
			formData.append("event_id","{{$event->id}}");
			formData.append("certificate_id",cert_id);
			formData.append("attendee_id",attendee_id);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				url: "{{route('postSendCertificate', array('event_id' => $event->id))}}",
				dataType    : 'text',           // what to expect back from the PHP script, if anything
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post',
				success: function(response)
				{
					alert("Message Successfully Sent!");
				},
				error: function(){
					
				}
			});
		}
		function setCertificate(event_id, cert_id, cert_title){
			$("#cert_title").html(cert_title);
			var formData = new FormData();
			formData.append("event_id","{{$event->id}}");
			formData.append("certificate_id",cert_id);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				url: "{{route('postCheckInAttendeeCertificate', array('event_id' => $event->id))}}",
				dataType    : 'text',           // what to expect back from the PHP script, if anything
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post',
				success: function(response)
				{
					//document.location.href="{{route('showCheckIn', array('event_id' => $event->id))}}";
				},
				error: function(){
					
				}
			});
		}
		function saveCeu(){
			var formData = new FormData();
			formData.append("event_id","{{$event->id}}");
			var ceu_hr = $("#ceu_hr").val();
			var ceu_total = $("#ceu_total").val();
			var ceu_unit = $("#ceu_unit").val();			
			formData.append("ceu_total",ceu_total);
			formData.append("ceu_hr",ceu_hr);
			formData.append("ceu_unit",ceu_unit);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				url: "{{route('postCheckInAttendeeCertificateCeu', array('event_id' => $event->id))}}",
				dataType    : 'text',           // what to expect back from the PHP script, if anything
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post',
				success: function(response)
				{
					alert("saved");
					//document.location.href="{{route('showCheckIn', array('event_id' => $event->id))}}";
				},
				error: function(){
					
				}
			});
		}
		function viewCertificate(attendee_id){
			var formData = new FormData();
			formData.append("event_id","{{$event->id}}");
			formData.append("attendee_id",attendee_id);			
			
		}
	</script>
    <div class="col-md-12">
        {!!$attendees->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render()!!}
    </div>
</div>    <!--/End attendees table-->

@stop


