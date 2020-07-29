@extends('Shared.Layouts.Master')

@section('title')
@parent
@lang("Attendee.event_attendees")
@stop


@section('page_title')
<i class="ico-users"></i>
{{$event->title}}
@lang("basic.sign")
@lang("instruction.instruction13")
@stop

@section('top_nav')

@stop

@section('menu')

@stop


@section('head')

@stop

@section('page_header')

@stop


@section('content')

<!--Start Attendees table-->
{!! Form::open(array('url' => route('postCheckInAttendeeSign', ['event_id'=>$event->id]), 'method' => 'post')) !!}
<input type="hidden" name="attendee_id" value="{{ $attendee_id }}">
<input type="hidden" name="checking" value="{{ $checking }}">
<div class="row" id="pdf_content">
        <table width="100%">
            <tr><td style="padding:20px;text-align: left; font-size: 20px;">
                    <style>
                        #signatureparent,#signatureparent1{
                            color: darkblue;
                            background-color: #FFF;
                            padding: 20px;
                        }
                        #signature, #signature1 {
                            border: 2px dotted black;
                            background-color:lightgrey;
                        }
						#signature input, #signature1 input{
							left: 35px !important;
							top:0px !important;
							font-size: 10px !important;							
						}
                        html.touch #content {
                            float:left;
                            width:92%;
                        }
                        html.touch #scrollgrabber {
                            float:right;
                            width:4%;
                            margin-right:2%;
                            background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAFCAAAAACh79lDAAAAAXNSR0IArs4c6QAAABJJREFUCB1jmMmQxjCT4T/DfwAPLgOXlrt3IwAAAABJRU5ErkJggg==)
                        }
                        html.borderradius #scrollgrabber {
                            border-radius: 1em;
                        }
                    </style>
                <!--<script src="{{asset('assets/javascript/libs/jquery.js')}}"></script>-->
                    <script>
                        /*  @preserve
                        jQuery pub/sub plugin by Peter Higgins (dante@dojotoolkit.org)
                        Loosely based on Dojo publish/subscribe API, limited in scope. Rewritten blindly.
                        Original is (c) Dojo Foundation 2004-2010. Released under either AFL or new BSD, see:
                        http://dojofoundation.org/license for more information.
                        */
                        function printPdf(){
							var printContents = document.getElementById('pdf_content').innerHTML;
							//var originalContents = document.body.innerHTML;
							//document.body.innerHTML = printContents;
							window.print();
							//document.body.innerHTML = originalContents;
							//setSignature();
						}
						function submit_sign(){
							var data = $("#signature").jSignature('getData', "image");
							var signature_val1 = 'data:' + data[0] + ',' + data[1];
							var data1 = $("#signature1").jSignature('getData', "image");
							var signature_val2 = 'data:' + data1[0] + ',' + data1[1];
							var formData = new FormData();
							formData.append("sign_id","{{$event->pdf_sign}}");
							formData.append("event_id","{{$event->id}}");
							formData.append("attendee_id","{{$attendee_id}}");
							formData.append("checking","{{$checking}}");
							formData.append("signature_val1",signature_val1);
							formData.append("signature_val2",signature_val2);
							var signature = false;
							var that = this;
							$("input").each(function() {
								if($(this).attr("value")=="Undo last stroke") {
									var sign = $(this).css("display");
									if(sign=="block"){
										signature = true;
									}
								}
							});
							if(signature){								
								$.ajax({
									headers: {
										'X-CSRF-TOKEN': '{{ csrf_token() }}'
									},
									url: "{{route('postCheckInAttendeeSign', array('event_id' => $event->id))}}",
									dataType    : 'text',           // what to expect back from the PHP script, if anything
									cache       : false,
									contentType : false,
									processData : false,
									data        : formData,                         
									type        : 'post',
									success: function(response)
									{
										document.location.href="{{route('showCheckIn', array('event_id' => $event->id))}}";
									},
									error: function(){
										
									}
								});
							}else{
								alert("Please sign!");
							}
						}
						function decline(){
							var formData = new FormData();
							formData.append("event_id","{{$event->id}}");
							formData.append("attendee_id","{{$attendee_id}}");
							formData.append("checking","{{$checking}}");
							$.ajax({
								headers: {
									'X-CSRF-TOKEN': '{{ csrf_token() }}'
								},
								url: "{{route('postCheckInAttendeeSign', array('event_id' => $event->id))}}",
								dataType    : 'text',           // what to expect back from the PHP script, if anything
								cache       : false,
								contentType : false,
								processData : false,
								data        : formData,                         
								type        : 'post',
								success: function(response)
								{
									document.location.href="{{route('showCheckIn', array('event_id' => $event->id))}}";
								},
								error: function(){
									
								}
							});
						}
						(function($) {
                            var topics = {};
                            $.publish = function(topic, args) {
                                if (topics[topic]) {
                                    var currentTopic = topics[topic],
                                        args = args || {};

                                    for (var i = 0, j = currentTopic.length; i < j; i++) {
                                        currentTopic[i].call($, args);
                                    }
                                }
                            };
                            $.subscribe = function(topic, callback) {
                                if (!topics[topic]) {
                                    topics[topic] = [];
                                }
                                topics[topic].push(callback);
                                return {
                                    "topic": topic,
                                    "callback": callback
                                };
                            };
                            $.unsubscribe = function(handle) {
                                var topic = handle.topic;
                                if (topics[topic]) {
                                    var currentTopic = topics[topic];

                                    for (var i = 0, j = currentTopic.length; i < j; i++) {
                                        if (currentTopic[i] === handle.callback) {
                                            currentTopic.splice(i, 1);
                                        }
                                    }
                                }
                            };
                        })(jQuery);

                    </script>
                    <script src="{{asset('assets/javascript/src/jSignature.js')}}"></script>
                    <script src="{{asset('assets/javascript/src/plugins/jSignature.CompressorBase30.js')}}"></script>
                    <script src="{{asset('assets/javascript/src/plugins/jSignature.CompressorSVG.js')}}"></script>
                    <script src="{{asset('assets/javascript/src/plugins/jSignature.UndoButton.js')}}"></script>
                    <script src="{{asset('assets/javascript/src/plugins/signhere/jSignature.SignHere.js')}}"></script>
                    <script>
                        function setPdfSigner() {
                            $("#signature canvas").trigger("resize");
                        }
                        function setSignature(){
                            var $extraarea = $('#displayarea');
                            $extraarea.html("");
                            var i = new Image()
                            var data = $("#signature").jSignature('getData', "image");
                            i.src = 'data:' + data[0] + ',' + data[1]
                            $('<span></span>').appendTo($extraarea)
                            $(i).appendTo($extraarea)
                        }
                        $(document).ready(function() {
                            // This is the part where jSignature is initialized.
                            var $sigdiv = $("#signature").jSignature({'UndoButton':true})
                            var $sigdiv1 = $("#signature1").jSignature({'UndoButton':true})
                        })

                    </script>
                    <div class="container">
                        <div id="content">
                            <div class="text-center"><h1>{{$sign_html->title}}</h1></div>
                            <div class="col-md-12" style="padding-left:30px;padding-right:30px;">{{$sign_html->description}}</div>
                            @if($event->activate_sign==1)
							<div class="col-md-12 col-sm-12">
                                <div class="col-md-6 col-sm-6">
                                    <div id="signatureparent">
                                        <div id="signature"></div>
                                    </div>
                                    <div class="text-center"><p>(Releasor's Signature)</p></div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div id="signatureparent1">
                                        <div id="signature1"></div>
                                    </div>
                                    <div class="text-center"><p>(Parent's Signature, if Signatory is minor)</p></div>
                                </div>
                                <div id="tools"></div>
                            </div>
							@endif
                            <div class="col-md-12 col-sm-12">
                                <div class="col-md-6  col-sm-6">
                                    <div class="col-md-12">
                                        <input type="text" id="sign_user_name" class="form-control">
                                        <div class="text-center"><p>(Print Name)</p></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="col-md-12">
                                        <input type="text" id="sign_parent_name" class="form-control">
                                        <div class="text-center"><p>(Print Name)</p></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <div class="col-md-6 col-sm-6">
                                    <div class="col-md-12">
                                        <input type="text" id="sign_date" class="form-control">
                                        <div class="text-center"><p>(Date)</p></div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                </div>
                            </div>
                            
                        </div>
                        <div id="scrollgrabber"></div>
                    </div>
                </td></tr>
        </table>
</div>   
<div class="col-md-12 col-sm-12">
	@if($event->status==2)
	<div class="col-md-2 col-sm-2"></div> 
	<div class="col-md-4 col-sm-4 text-center"><input type="button" class="btn btn-success" value="done signature" onclick="submit_sign()"><div id="displayarea"></div></div>
	<div class="col-md-4 col-sm-4 text-center"><input type="button" class="btn btn-success" value="print as pdf" onclick="printPdf()"></div>
	<div class="col-md-2 col-sm-2"></div>
	@elseif($event->status==1)
	<div class="col-md-4 col-sm-4 text-center"><input type="button" class="btn btn-success" value="done signature" onclick="submit_sign()"><div id="displayarea"></div></div>
	<div class="col-md-4 col-sm-4 text-center"><input type="button" class="btn btn-success" value="decline" onclick="decline()"></div>
	<div class="col-md-4 col-sm-4 text-center"><input type="button" class="btn btn-success" value="print as pdf" onclick="printPdf()"></div>
	@endif
</div>
 <!--/End attendees table-->
{!! Form::close() !!}
@stop


