<!doctype html>
<html>
<head>
    <title>
        @lang("Attendee.check_in", ["event"=>$event->title])
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
    </style>
</head>
<body id="app">
<header>
    <div class="menuToggle hide">
        <i class="ico-menu"></i>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="attendee_input_wrap">
                    <div class="input-group">
                                  <span class="input-group-btn">
                                 <button @click="showQrModal" title="Scan QR Code" class="btn btn-default qr_search" type="button"><i
                                              class="ico-qrcode"></i></button>
                                </span>
                        {!!  Form::text('attendees_q', null, [
                    'class' => 'form-control attendee_search',
                            'id' => 'search',
                            'v-model' => 'searchTerm',
                            '@keyup' => 'fetchAttendees | debounce 500',
                            '@keyup.esc' => 'clearSearch',
                            'placeholder' => trans("ManageEvent.checkin_search_placeholder")
                ])  !!}


                    </div>

                    <span v-if='searchTerm' @click='clearSearch' class="clearSearch ico-cancel"></span>
                </div>
            </div>
        </div>
    </div>
</header>


<section class="attendeeList">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="attendee_list">
                    <h4 class="attendees_title">
                        <span v-if="!searchTerm">
                            @lang("ManageEvent.all_attendees")							
                        </span>
                        <span v-else v-cloak>
                            @{{searchResultsCount}} @lang("ManageEvent.result_for") <b>@{{ searchTerm }}</b>
                        </span>
						@lang("instruction.instruction09")
                    </h4>

                    <div style="margin: 10px;" v-if="searchResultsCount == 0 && searchTerm" class="alert alert-info"
                         v-cloak>
                        @lang("ManageEvent.no_attendees_matching") <b>@{{ searchTerm }}</b>
                    </div>

                    <ul v-if="searchResultsCount > 0" class="list-group" id="attendee_list" v-cloak>
                        <li
                        @click="toggleCheckin(attendee)"
                        v-for="attendee in attendees"
						data-modal-id='ticket-@{{ attendee.id }}' 						
                        class="at list-group-item"
                        :class = "{arrived : attendee.has_arrived || attendee.has_arrived == '1'}"
                        >
						<table width="100%"><tr><td width="40%">
						@lang("Attendee.name"): <b>@{{ attendee.first_name }} @{{ attendee.last_name }} </b> &nbsp; <span v-if="!attendee.is_payment_received" class="label label-danger">@lang("Order.awaiting_payment")</span>
                        <br>
                            @lang("Order.reference"): <b>@{{ attendee.order_reference + '-' + attendee.reference_index }}</b>
                        <br>
                            @lang("Order.ticket"): <b>@{{ attendee.ticket }}</b>
                        <a href="" class="ci btn btn-successfulQrRead">
                            <i class="ico-checkmark"></i>
                        </a>
						<br>
							@lang("ManageEvent.type"): <b>@{{ attendee.type}}</b>
						</td>
						<td width="20%">@{{ attendee.first_name }} @{{ attendee.last_name }}</td>
						<td width="20%">@{{ attendee.email}}</td>
						<td width=""></td>
						<td style="text-align:right;">
							<div style="background:#8fbf40;text-align: center; vertical-align: top; padding-top: 10px;width:110px;float:right;">
								<img style="width: 40px;" class="logo" alt="Attendize" src="{{ asset('/assets/images/public/EventPage/checkin.png') }}"/>
								<p style="color:#FFFFFF;font-weight:bolder;margin-top:5px;">Check In</p>
							</div>
						</td>
						</tr></table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

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

<script>
Vue.http.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
</script>

@include("Shared.Partials.LangScript")
{!! HTML::script('vendor/qrcode-scan/llqrcode.js') !!}
{!! HTML::script('assets/javascript/check_in.js') !!}
</body>
</html>
