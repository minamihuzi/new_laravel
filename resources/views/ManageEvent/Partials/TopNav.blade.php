@section('pre_header')
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
@stop
<ul class="nav navbar-nav navbar-left">
    <!-- Show Side Menu -->
    <li class="navbar-main">
        <a href="javascript:void(0);" class="toggleSidebar" title="Show sidebar">
            <span class="toggleMenuIcon">
                <span class="icon ico-menu"></span>
            </span>
        </a>
    </li>
    <!--/ Show Side Menu -->
    <li class="nav-button">
        <a target="blank" href="{{$event->event_url}}">
            <span>
                <i class="ico-eye2"></i>&nbsp;@lang("ManageEvent.event_page")
            </span>
        </a>
    </li>
</ul>
<ul class="exit-nav navbar-exit">
        <h5>{{isset($organiser->name) ? $organiser->name : $event->organiser->name}} {{isset($user->first_name) ? $user->first_name:""}} {{isset($user->last_name) ? $user->last_name:""}}&nbsp;&nbsp;&nbsp;
        <a href="{{route('logout')}}"><span class="icon ico-exit">&nbsp;</span>@lang("Top.sign_out")
       </a>
     </h5>
   </ul>