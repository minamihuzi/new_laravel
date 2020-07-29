<script>

	$(document).ready(function() {
		if(window.location.href.indexOf("stop")>0){
		}else{
			//window.open(window.location.href+"?stop", '_self');
		}
	});
	function gotoCheckin() {
		window.open("{{route('showCheckIn', array('event_id' => $event->id))}}");
		window.close();	
		document.write('You will leave this page.\n Please close this window.');
		return "";
	}	
</script>
<aside class="sidebar sidebar-left sidebar-menu">
   <ul>
        <section class="content">
        <h5 class="heading">@lang("basic.main_menu")</h5></ul>
        <section class="content">
        <ul id="nav" class="topmenu">
            <li>
                <a href="{{route('showOrganiserDashboard', ['organiser_id' => $event->organiser->id])}}">
                    <span class="figure"><i class="ico-arrow-left"></i></span>
                    <span class="text">@lang("basic.back_to_page", ["page"=>$event->organiser->name])</span>
                </a>
            </li>
        </ul>
        <h5 class="heading">@lang('basic.event_menu')</h5>
        <ul id="nav_event" class="topmenu">
            <li class="{{ Request::is('*customize*') ? 'active' : '' }}">
                <a href="{{route('showEventCustomize', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cog"></i></span>
                    <span class="text">@lang("basic.customize")</span>
                </a>
            </li>
            <li class="{{ Request::is('*dashboard*') ? 'active' : '' }}">
                <a href="{{route('showEventDashboard', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-home2"></i></span>
                    <span class="text">@lang("basic.dashboard")</span>
                </a>
                 </li>
            <li class="{{ Request::is('*tickets*') ? 'active' : '' }}">
                <a href="{{route('showEventTickets', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-ticket"></i></span>
                    <span class="text">@lang("basic.tickets")</span>
                </a>
            </li>
            <li class="{{ Request::is('*orders*') ? 'active' : '' }}">
                <a href="{{route('showEventOrders', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-cart"></i></span>
                    <span class="text">@lang("basic.orders")</span>
                </a>
            </li>
            <li class="{{ Request::is('*attendees*') ? 'active' : '' }}">
                <a href="{{route('showEventAttendees', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-user"></i></span>
                    <span class="text">@lang("basic.attendees")</span>
                </a>
            </li>
            <li class="{{ Request::is('*promote*') ? 'active' : '' }} hide">
                <a href="{{route('showEventPromote', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-bullhorn"></i></span>
                    <span class="text">@lang("basic.promote")</span>
                </a>
            </li>
        </ul>
        <h5 class="heading">@lang("ManageEvent.event_tools")</h5>
        <ul id="nav_event" class="topmenu">
            <li class="{{ Request::is('*check_in') ? 'active' : '' }}">
                <a href="javascript:gotoCheckin();">
                    <span class="figure"><i class="ico-checkbox-checked"></i></span>
                    <span class="text">@lang("ManageEvent.check-in")</span>
                </a>
            </li>
			<li class="{{ Request::is('*check_in_log*') ? 'active' : '' }}">
                <a href="{{route('showCheckInLog', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-history"></i></span>
                    <span class="text">@lang("ManageEvent.check-in-log")</span>
                </a>
            </li>
            <li class="{{ Request::is('*surveys*') ? 'active' : '' }}">
                <a href="{{route('showEventSurveys', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-question"></i></span>
                    <span class="text">@lang("ManageEvent.surveys")</span>
                </a>
            </li>
            <li class="{{ Request::is('*widgets*') ? 'active' : '' }}">
                <a href="{{route('showEventWidgets', array('event_id' => $event->id))}}">
                    <span class="figure"><i class="ico-code"></i></span>
                    <span class="text">@lang("ManageEvent.widgets")</span>
                </a>
            </li>
            <li class="{{ Request::is('*access_codes*') ? 'active' : '' }}">
                <a href="{{ route('showEventAccessCodes', [ 'event_id' => $event->id ]) }}">
                    <span class="figure"><i class="ico-money"></i></span>
                    <span class="text">@lang("AccessCodes.title")</span>
                </a>
            </li>
			<li>
                <a href="../../../../wowslider.php" target="_blank">
                    <span class="figure"><i class="ico-calendar"></i></span>
                    <span class="text">@lang("ManageEvent.whisp_slider")</span>
                </a>
            </li>
        </ul>
        @if(!$event->is_live)
        <style>
            .sidebar {
                top: 43px;
            }
        </style>
        <div class="alert alert-warning top_of_page_alert">
            {{ @trans("ManageEvent.event_not_live") }}
            <a href="{{ route('MakeEventLive', ['event_id' => $event->id]) }}">{{ @trans("ManageEvent.publish_it") }}</a>
        </div>
    @endif
    </section>
</aside>