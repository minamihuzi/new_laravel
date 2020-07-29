<!doctype html>
<html>
<head>
    <title>
       Test
    </title>

    {!! HTML::script('vendor/vue/dist/vue.min.js') !!}
    {!! HTML::script('vendor/vue-resource/dist/vue-resource.min.js') !!}

    {!! HTML::style('assets/stylesheet/application.css') !!}
    {!! HTML::style('assets/stylesheet/check_in.css') !!}
    {!! HTML::script('vendor/jquery/dist/jquery.min.js') !!}

    @include('Shared/Layouts/ViewJavascript')
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            background: url({{asset('assets/images/background.png')}}) repeat;
            background-color: #2E3254;
            background-attachment: fixed;
        }
		
		.at .ci {
			background: url({{asset('assets/images/inv.png')}});
			background-size:cover;
		}
		.type-span{
			line-height: 34px;
			margin-left: 15px;
		}
		.checkin-header{
			background-color: transparent;
			padding: 0px;
		}
		.checkin-header .container{
			background-color:#FFF;
		}
		header .attendee_list{
			padding:0 14px;
		}
		header .at{cursor:pointer;}
		header .list-group{
			margin-bottom: 0px;
		}
		section.attendeeList{
			margin-top:282px;
		}
		@media (min-width: 100px) and (max-width: 767px) {
			header .attendee_list{
				padding:0px;
			}
		}
		@media (min-width: 100px) and (max-width: 991px) {
			section.attendeeList{
				margin-top:300px;
			}
		}
		@media (min-width: 100px) and (max-width: 647px) {
			.phone_hidden{
				display:none;
			}
			.phone_show{
				width:70%;
			}
		}
		@media (min-width: 648px){
			.phone_show{
				width:30%;
			}
		}
    </style>
	<script>  		
		function readURL(input, id) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();				
				
				reader.onload = function (e) {
					$('#photo-'+id).css('background-image', 'url(' + e.target.result + ')');
					$('#small-photo-'+id).css('background-image', 'url(' + e.target.result + ')');
					$('#full-photo-'+id).css('background-image', 'url(' + e.target.result + ')');
					var form_data = new FormData();
					form_data.append('file', input.files[0]);
					form_data.append('attendee_id', id);
					$.ajax({
							headers: {
								'X-CSRF-TOKEN': '{{ csrf_token() }}'
							},
							url         : '{{ url('/upload_photo') }}',
							dataType    : 'text',           // what to expect back from the PHP script, if anything
							cache       : false,
							contentType : false,
							processData : false,
							data        : form_data,                         
							type        : 'post',
							success     : function(output){
								//alert(output);              // display response from the PHP script, if any
							}
					 });	
					
				}
				reader.readAsDataURL(input.files[0]);
				
			}
		}
		function deletePhoto(id) {			
			$('#photo-'+id).css('background-image', '');
			$('#small-photo-'+id).css('background-image', '');
			$('#full-photo-'+id).css('background-image', '');
			var form_data = new FormData();			
			form_data.append('attendee_id', id);
			$.ajax({
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					url         : '{{ url('/delete_photo') }}',
					dataType    : 'text',           // what to expect back from the PHP script, if anything
					cache       : false,
					contentType : false,
					processData : false,
					data        : form_data,                         
					type        : 'post',
					success     : function(output){
						
					}
			 });									
		}	
		function show() {
			var el = document.documentElement
				, rfs =
					   el.requestFullScreen
					|| el.webkitRequestFullScreen
					|| el.mozRequestFullScreen
			;
			rfs.call(el);
		};	
	$(document).ready(function() {		
		$("#screen_img").click(function(){
			if($(this).hasClass("full_screen")){
				$(this).attr('src',"{{asset('assets/images/min_screen_black.png')}}");
				$(this).removeClass("full_screen");
				$(this).addClass("min_screen");
				openFullscreen();
			}else{
				$(this).attr('src',"{{asset('assets/images/full_screen_black.png')}}");
				$(this).removeClass("min_screen");
				$(this).addClass("full_screen");
				closeFullscreen()
			}
		});
		var elem = document.documentElement;

		/* View in fullscreen */
		function openFullscreen() {
		  if (elem.requestFullscreen) {
			elem.requestFullscreen();
		  } else if (elem.mozRequestFullScreen) { /* Firefox */
			elem.mozRequestFullScreen();
		  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
			elem.webkitRequestFullscreen();
		  } else if (elem.msRequestFullscreen) { /* IE/Edge */
			elem.msRequestFullscreen();
		  }
		}

		/* Close fullscreen */
		function closeFullscreen() {
		  if (document.exitFullscreen) {
			document.exitFullscreen();
		  } else if (document.mozCancelFullScreen) { /* Firefox */
			document.mozCancelFullScreen();
		  } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
			document.webkitExitFullscreen();
		  } else if (document.msExitFullscreen) { /* IE/Edge */
			document.msExitFullscreen();
		  }
		}	
	});		
    </script>
</head>
<body id="app" onload="show();">
<header class="checkin-header attendee_list">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="attendee_list">
					<h4 class="text-center">                        
						
					</h4>
					<h4 class="text-center">                        						
						
						<span style="float:right;">
							<img style="width: 35px;margin-top:5px;" id="screen_img" class="full_screen" alt="Screen" src="{{asset('assets/images/full_screen_black.png')}}"/>
						</span>							
                    </h4>
					<h4 class="text-center"> 
						
					</h4>
					<div class="progress">
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="@{{rateArrived}}"
					  aria-valuemin="0" aria-valuemax="100" style="width:@{{rateArrived}}%">
					  10% Checked In
					  </div>
					</div>
                    
					<div class="row" style="margin:10px;">
						<div class="col-sm-4">
						<div class="col-sm-12 text-center" @click="showWalkinModal" style="background: #bb6cbd; color:#FFFFFF;padding:10px; border-radius:5px; cursor:pointer;">
							<i class="glyphicon glyphicon-user"></i>
							<span>Walkin</span>						  
						</div>
						
						</div>
						<div class="col-sm-4">
						<div class="col-sm-12 text-center" @click="showQrModal" style="background: #bf7979; color:#FFFFFF;padding:10px; border-radius:5px; cursor:pointer;">
							<i class="glyphicon glyphicon-qrcode"></i>
							<span>Scan</span>						  
						</div>
						</div>
						
					</div>
					<a @click="showTypeModal()"  href="javascript:void(0);">
					<div class="input-group">
						<div class="input-group-btn">
						  <button class="btn btn-default" type="submit" @click="showTypeModal()">
							<i class="glyphicon glyphicon-filter"></i>
						  </button>
						</div>
						<span class="type-span" id="valueTType">Registration Type Filter</span>					
					</div>
					</a>
					<div class="input-group">
						<div class="input-group-btn">
						  <button class="btn btn-default" type="submit" @click="searchCheckin()">
							<i class="glyphicon glyphicon-search"></i>
						  </button>
						</div>
						<input type="text" id="search_checkin" class="form-control" placeholder="Search" v-model="searchKey" @change="searchCheckin()" @keyup="onKeyPress">						
					</div>
					<div style="margin: 10px;" v-if="searchResultsCount == 0 && searchTerm" class="alert alert-info"
                         v-cloak>
                        @lang("ManageEvent.no_attendees_matching") <b></b>
                    </div>
					
				</div>
            </div>
        </div>
    </div>
</header>

<footer class="hide">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            </div>
        </div>
    </div>
</footer>

{{--QR Modal--}}
<div role="dialog" id="QrModal" class="scannerModal" v-show="showScannerModal" v-cloak>
    <div class="scannerModalContent">

        <a @click="closeScanner"  class="closeScanner" href="javascript:void(0);">
        <i class="ico-close"></i>
        </a>
        <video id="scannerVideo" playsinline autoplay></video>

        <div class="scannerButtons">
                    <a @click="initScanner" v-show="!isScanning" href="javascript:void(0);">
                    @lang("Attendee.scan_another_ticket")
                    </a>
        </div>
        <div v-if="isScanning" class="scannerAimer">
        </div>

        <div v-if="scanResult" class="scannerResult @{{ scanResultObject.status }}">
            <i v-if="scanResultObject.status == 'success'" class="ico-checkmark"></i>
            <i v-if="scanResultObject.status == 'error'" class="ico-close"></i>
        </div>

        <div class="ScanResultMessage">
                    <span class="message" v-if="scanResultObject.status == 'error'">
                        @{{ scanResultObject.message }}
                    </span>
                    <span class="message" v-if="scanResultObject.status == 'success'">
                        <span class="uppercase">@lang("Attendee.name")</span>: @{{ scanResultObject.name }}<br>
                        <span class="uppercase">@lang("Attendee.reference")</span>: @{{scanResultObject.reference }}<br>
                        <span class="uppercase">@lang("Attendee.ticket")</span>: @{{scanResultObject.ticket }}
                    </span>
                    <span v-if="isScanning">
                        <div id="scanning-ellipsis">@lang("Attendee.scanning")<span>.</span><span>.</span><span>.</span></div>
                    </span>
        </div>
        <canvas id="QrCanvas" width="800" height="600"></canvas>
    </div>
</div>
{{-- /END QR Modal--}}


{{--Item Modal--}}
<div role="dialog" id="CheckInItem" class="scannerModal" v-show="showCheckInItem" v-cloak>
    <div class="scannerModalContent">

        <a @click="closeCheckInItem"  class="closeScanner" href="javascript:void(0);">
        <i class="ico-close"></i>
        </a>
        <video id="scannerVideo" playsinline autoplay></video>
		<div class="row" style="width:90%;text-align:center;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Contact Information</p>
			<input type="hidden" id="attendee_id" value="">
			<table width="100%">
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">P-Type</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
					<select class="form-control" id="ticket_per_type" name="ticket_per_type" @change="setCoupleName(1)" v-model="ticketTypeKey">
						<option value="Couples">Couples</option>
						<option value="Single Male">Single Male</option>
						<option value="Single Female">Single Female</option>
					</select>
				</td></tr>
			<tr><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">first name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="first_name" value=""></td></tr>
			<tr><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">last name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="last_name" value=""></td></tr>
			<tr v-show="showTicketType" v-cloak><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">couple first name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="couple_first_checkin" value=""></td></tr>
			<tr v-show="showTicketType" v-cloak><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">couple last name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="couple_last_checkin" value=""></td></tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">User Name</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				<input style="width:100%; border:none;" type="text" id="business_name" value="">
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Email</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				<input style="width:100%; border:none;" type="text" id="email" value="" placeholder="">
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Reference</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				<input style="width:100%; border:none;" type="text" id="reference" value="">
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Amount</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				 <div class="input-group"> 					
					<input type="number" id="amount" value="" placeholder="0.00" value="10.00" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" id="c2" />				
					<span class="input-group-addon" style="color: #ffffff;background-color: #43630d;">USD</span>
				</div> 				
				</td>
			</tr>
			<tr>
			<td style="text-align:center;" colspan="2">
				<div class="row">
					<div class="col-sm-3">
						<div @click="showConfirmClearModal(1)" class="col-sm-12" style="background:#ffa067;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-edit" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Clear Check In</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="setCheckin(1)" class="col-sm-12" style="background:#8fbf40;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<img style="width: 40px;" class="logo" alt="Attendize" src="{{ asset('/assets/images/public/EventPage/checkin.png') }}"/>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Check In</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="closeCheckInItem()" class="col-sm-12" style="background:#916741;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-transfer" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Back</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="showConfirmPayModal(1)" class="col-sm-12" style="background:#43630d;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-shopping-cart" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Payment Received</p>
						</div>
					</div>
				</div>
			</td>
			</tr>
			</table>			
		</div>
    </div>
</div>
{{-- /END Item Modal--}}
{{--Item Modal--}}
<div role="dialog" id="CheckInType" class="scannerModal" v-show="showCheckInType" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:60%;text-align:center;height:400px;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Registration Type Filter
			<a @click="closeCheckInType" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr class="hidden"><td style="padding:20px;text-align: left; font-size: 20px;">
				<div class="custom-checkbox" @click="setTType(1)">
					<input id="is_hidden" name="is_hidden" type="checkbox" value="1"  v-el:is_hidden>
					<label for="is_hidden" class=" control-label">@lang("ManageEvent.hide_this_ticket")</label>
				</div>
			</td></tr>
			<tr>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(2)">
						<input id="is_normal" name="is_normal" type="checkbox" value="2">
						<label for="is_normal" class=" control-label">@lang("ManageEvent.normal")</label>
					</div>
				</td>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(20)">
						<input id="is_couple" name="is_couple" type="checkbox" value="Couples">
						<label for="is_couple" class=" control-label">Couples</label>
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(3)">
						<input id="is_free" name="is_free" type="checkbox" value="3">
						<label for="is_free" class=" control-label">@lang("ManageEvent.free")</label>
					</div>
				</td>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(30)">
						<input id="is_male" name="is_male" type="checkbox" value="Single Male">
						<label for="is_male" class=" control-label">Single Male</label>
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(4)">
						<input id="is_suggested_donation" name="is_suggested_donation" type="checkbox" value="4">
						<label for="is_suggested_donation" class=" control-label">@lang("ManageEvent.suggested_donation")</label>
					</div>
				</td>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div class="custom-checkbox" @click="setTType(40)">
						<input id="is_female" name="is_female" type="checkbox" value="Single Female">
						<label for="is_female" class=" control-label">Single Female</label>
					</div>
				</td>
			</tr>
			<tr>
			<td style="text-align:right;" colspan="2">
				<a href="javascript:void(0);" @click="clearTType()"><span style="padding:20px;line-height:30px;"><b>CLEAR</b></span></a>
				<a href="javascript:void(0);" @click="searchTType()"><span style="padding:20px;line-height:30px;"><b>DONE</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
<div role="dialog" id="Walkin" class="scannerModal" v-show="showWalkin" v-cloak>
    <div class="scannerModalContent">

        <a @click="closeWalkin"  class="closeScanner" href="javascript:void(0);">
        <i class="ico-close"></i>
        </a>
        <video id="scannerVideo" playsinline autoplay></video>
		<div class="row" style="width:90%;text-align:center;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Walkin Information</p>
			<span id="walkError" style="color: #F00;font-size:18px;" v-show="showConfirmError"  v-cloak></span>
			<input type="hidden" id="attendee_id_walk" value="">
			<input type="hidden" id="event_id_walk" value="1">
			<table width="100%">
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Profile Type</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
					<select class="form-control" id="profile_per_type_walk" name="profile_per_type_walk" @change="setCoupleName(1)" v-model="ticketTypeKey">
						<option value="Couples">Couples</option>
						<option value="Single Male">Single Male</option>
						<option value="Single Female">Single Female</option>
					</select>
				</td></tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Ticket Type</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
					<select class="form-control" id="ticket_per_type_walk" name="ticket_per_type_walk">
						<option value="2" selected>Normal</option>
						<option value="3">Free</option>
						<option value="4">Suggested Donation</option>
					</select>
				</td>
			</tr>
			<tr><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">first name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="first_name_walk" value=""></td></tr>
			<tr><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">last name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="last_name_walk" value=""></td></tr>
			<tr v-show="showTicketType" v-cloak><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">couple first name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="couple_first_walk" value=""></td></tr>
			<tr v-show="showTicketType" v-cloak><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">couple last name</td><td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;"><input style="width:100%; border:none;" type="text" id="couple_last_walk" value=""></td></tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">User Name</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				<input style="width:100%; border:none;" type="text" id="business_name_walk" value="">
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Email</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
				<input style="width:100%; border:none;" type="text" id="email_walk" value=""  placeholder="">
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Paid Type</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
					<select class="form-control" id="pay_walk" name="pay_walk">
						<option value="credit card">credit card</option>
						<option value="cash">cash</option>
						<option value="check">check</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;">Paid Amount</td>
				<td style="padding-left:20px;line-height: 50px; text-align: left; font-size: 20px; border-bottom: 1px solid;font-weight:bolder;">
					<div class="input-group"> 					
						<input type="number" id="pay_amount" placeholder="0.00" value="10.00" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" id="c2" />				
						<span class="input-group-addon" style="color: #ffffff;background-color: #43630d;">USD</span>
					</div> 	
				</td>
			</tr>
			<tr>
			<td style="text-align:center;" colspan="2">
				<div class="row">
					<div class="col-sm-3">
						<div @click="setWalkin()" class="col-sm-12" style="background:#8fbf40;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<img style="width: 40px;" class="logo" alt="Attendize" src="{{ asset('/assets/images/public/EventPage/checkin.png') }}"/>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Check In</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="emailWalkin()" class="col-sm-12" style="background:#9d9d9c;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-envelope" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Email Ticket</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="printWalkin()" class="col-sm-12" style="background:#40adbf;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-print" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Print / Email Ticket</p>
						</div>
					</div>
					<div class="col-sm-3">
						<div @click="showConfirmPayModal(2)" class="col-sm-12" style="background:#43630d;text-align: center; vertical-align: top; padding-top: 10px;cursor:pointer;">
							<i class="glyphicon glyphicon-shopping-cart" style="font-size:45px; color:#FFFFFF;"></i>
							<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Payment Received</p>
						</div>
					</div>
				</div>						
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
{{-- /END Item Modal--}}
{{--Item Modal--}}
<div role="dialog" id="showConfirmBackModal" class="scannerModal" v-show="showConfirmBack" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:50%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Please input valid code for back dashboard!
			<a @click="closeConfirmBackModal" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr><td style="padding:20px;text-align: left; font-size: 20px;">
				<div>
					<label>4-digit pass code:</label><input type="text" id="pass_code" name="pass_code">
				</div>
				<span style="margin-left: 200px; color: #F00;font-size:18px;" v-show="showConfirmPassError"  v-cloak>Please input valid pass code.</span>
			</td></tr>
			
			<tr>
			<td style="text-align:right;" colspan="2">
				<a href="javascript:void(0);" @click="closeConfirmBackModal"><span style="padding:20px;line-height:30px;"><b>CANCEL</b></span></a>
				<a href="javascript:void(0);" @click="doConfirmBack()"><span style="padding:20px;line-height:30px;"><b>DONE</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
<div role="dialog" id="showConfirmPayModal" class="scannerModal" v-show="showConfirmPay" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:50%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Please input valid code for confirm payment!
			<a @click="closeConfirmPayModal" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr><td style="padding:20px;text-align: left; font-size: 20px;">
				<div>
					<label>4-digit pass code:</label><input type="text" id="pass_code_pay" name="pass_code_pay">
				</div>
				<span style="margin-left: 200px; color: #F00;font-size:18px;" v-show="showConfirmPassError"  v-cloak>Please input valid pass code.</span>
			</td></tr>
			
			<tr>
			<td style="text-align:right;" colspan="2">
				<a href="javascript:void(0);" @click="closeConfirmPayModal"><span style="padding:20px;line-height:30px;"><b>CANCEL</b></span></a>
				<a href="javascript:void(0);" @click="doConfirmPay()"><span style="padding:20px;line-height:30px;"><b>DONE</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
<div role="dialog" id="showConfirmPayCheckinModal" class="scannerModal" v-show="showConfirmPayCheckin" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:50%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Please input valid code for confirm payment!
			<a @click="closeConfirmPayCheckinModal" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr><td style="padding:20px;text-align: left; font-size: 20px;">
				<div>
					<label>4-digit pass code:</label><input type="text" id="pass_code_pay_checkin" name="pass_code_pay_checkin">
				</div>
				<span style="margin-left: 200px; color: #F00;font-size:18px;" v-show="showConfirmPassError"  v-cloak>Please input valid pass code.</span>
			</td></tr>
			
			<tr>
			<td style="text-align:right;" colspan="2">
				<a href="javascript:void(0);" @click="closeConfirmPayCheckinModal"><span style="padding:20px;line-height:30px;"><b>CANCEL</b></span></a>
				<a href="javascript:void(0);" @click="doConfirmPayCheckin()"><span style="padding:20px;line-height:30px;"><b>DONE</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
<div role="dialog" id="showConfirmClearCheckinModal" class="scannerModal" v-show="showConfirmClearCheckin" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:50%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Please input valid code for clear checkin!
			<a @click="closeConfirmClearCheckinModal" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr><td style="padding:20px;text-align: left; font-size: 20px;">
				<div>
					<label>4-digit pass code:</label><input type="text" id="pass_code_clear_checkin" name="pass_code_clear_checkin">
				</div>
				<span style="margin-left: 200px; color: #F00;font-size:18px;" v-show="showConfirmClearPassError"  v-cloak>Please input valid pass code.</span>
			</td></tr>
			
			<tr>
			<td style="text-align:right;" colspan="2">
				<a href="javascript:void(0);" @click="closeConfirmClearCheckinModal"><span style="padding:20px;line-height:30px;"><b>CANCEL</b></span></a>
				<a href="javascript:void(0);" @click="doConfirmClearCheckin(1)"><span style="padding:20px;line-height:30px;"><b>DONE</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>

<div role="dialog" id="showTakePhotoModal" class="scannerModal" v-show="showTakePhoto" v-cloak>
    <div class="scannerModalContent">        
        <div class="row" style="border:1px solid #525996;; width:50%;text-align:center;height:100%;background:#FFFFFF;margin:40px auto;padding:20px;">
			<p style="line-height: 50px; font-size: 20px; border-bottom: 1px solid;">Please select a photo!
			<a @click="closeShowTakePhotoModal" href="javascript:void(0);" style="float:right;">
			<i class="ico-close"></i>
			</a>
			</p>
			<table width="100%">
			<tr>
				<td style="padding:20px;text-align: left; font-size: 20px; vertical-align:top; width:50px">
					<div style="background:url(@{{ attendee_photo_path }});background-size:cover;width:50px; height:50px;" id="small-photo-@{{ attendee_id }}">
							   
					</div>
					<div style="text-align:left; margin-top:30px;">
						<a href="javascript:void(0);" onclick="$('#file-input-@{{ attendee_id }}').click()"><span style="line-height:30px;"><b>ReTake</b></span></a>
					</div>
					<div style="text-align:left; margin-top:30px;">
						<a href="javascript:void(0);" onclick="deletePhoto(@{{ attendee_id }})"><span style="line-height:30px;"><b>Delete</b></span></a>
					</div>
				</td>
				<td style="padding:20px;text-align: left; font-size: 20px;">
					<div style="background:url(@{{ attendee_photo_path }});background-size:cover;width:100%; height:500px;" id="full-photo-@{{ attendee_id }}">
							   
					</div>
				</td>
			</tr>			
			<tr>
			<td style="text-align:center;" colspan="2" class="hidden">
				<a href="javascript:void(0);" onclick="$('#file-input-@{{ attendee_id }}').click()"><span style="padding:20px;line-height:30px;"><b>ReTake</b></span></a>
				<a href="javascript:void(0);" onclick="deletePhoto(@{{ attendee_id }})"><span style="padding:20px;line-height:30px;"><b>Delete</b></span></a>
			</td>
			</tr>
			</table>
		</div>
    </div>
</div>
<script>
Vue.http.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
</script>

@include("Shared.Partials.LangScript")
{!! HTML::script('vendor/qrcode-scan/llqrcode.js') !!}
{!! HTML::script('assets/javascript/check_in.js') !!}
</body>
</html>
