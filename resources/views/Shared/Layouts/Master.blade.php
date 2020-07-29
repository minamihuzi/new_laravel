<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
<head>
    <!--
                ____      ____      _____ ____      _ __   ___ _____ 
     \    /\    |	\    /|    |\  |  |      /     | '_ \ / _ \  |
      \  /  \  /|--	 \  / |--  | \ |  |     /      | | | |  __/  |
       \/    \/	|___  \/  |___ |  \|  |    /___ (_)|_| | |\____  |
    -->
    <title>
        @section('title')
            Weventz -
        @show
    </title>
    @include('Shared.Layouts.ViewJavascript')

    <!--Meta-->
    @include('Shared.Partials.GlobalMeta')
   <!--/Meta-->

    <!--JS-->
    {!! HTML::script(config('attendize.cdn_url_static_assets').'/vendor/jquery/dist/jquery.min.js') !!}
    <!--/JS-->

    <!--Style-->
    {!! HTML::style(config('attendize.cdn_url_static_assets').'/assets/stylesheet/application.css') !!}
    <!--/Style-->

    @yield('head')
<script>
$(document).ready(function() {
	//window.open(window.location.href, '_self');
	$("#screen_img").click(function(){
		if($(this).hasClass("full_screen")){
			$(this).attr('src',"{{asset('assets/images/min_screen.png')}}");
			$(this).removeClass("full_screen");
			$(this).addClass("min_screen");
			openFullscreen();
		}else{
			$(this).attr('src',"{{asset('assets/images/full_screen.png')}}");
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
<body class="attendize">
@yield('pre_header')
<header id="header" class="navbar">

    <div class="navbar-header">
        <a class="navbar-brand" href="javascript:void(0);">
            <img style="width: 150px;" class="logo" alt="Attendize" src="{{asset('assets/images/logo-light.png')}}"/>
			<span>
				<img style="width: 20px;margin-top:5px;" id="screen_img" class="full_screen" alt="Screen" src="{{asset('assets/images/full_screen.png')}}"/>
			</span>
        </a>
    </div>
    <div class="navbar-toolbar clearfix">
        @yield('top_nav')
		<ul class="nav navbar-nav">
			<li>
			<a href="#">
				<span>{{isset($organiser->instruction) ? $organiser->instruction : $event->organiser->instruction}}</span>
		   </a>
		 </li>
		</ul>
	</div>
   </header>

@yield('menu')

<!--Main Content-->
<section id="main" role="main">
    <div class="container-fluid">
        <div class="page-title">
            <h1 class="title">@yield('page_title')</h1>
        </div>
        @if(array_key_exists('page_header', View::getSections()))
        <!--  header -->
        <div class="page-header page-header-block row">
            <div class="row">
                @yield('page_header')
            </div>
        </div>
        <!--/  header -->
        @endif

        <!--Content-->
        @yield('content')
        <!--/Content-->
    </div>

    <!--To The Top-->
    <a href="#" style="display:none;" class="totop"><i class="ico-angle-up"></i></a>
    <!--/To The Top-->

</section>
<!--/Main Content-->

<!--JS-->
@include("Shared.Partials.LangScript")
{!! HTML::script('assets/javascript/backend.js') !!}
<script>
    $(function () {
        $('.winstruction').click(function(){
			if($(this).parent().find(".icontent").hasClass("show")){
				$(this).html("Show Instruction");
				$(this).parent().find(".icontent").removeClass("show");
				$(this).parent().find(".icontent").addClass("hide");
			}else{
				$(this).html("[Hide]");
				$(this).parent().find(".icontent").removeClass("hide");
				$(this).parent().find(".icontent").addClass("show");
			}
		});
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });

    @if(!Auth::user()->first_name)
      setTimeout(function () {
        $('.editUserModal').click();
    }, 1000);
    @endif

</script>
<!--/JS-->
@yield('foot')

@include('Shared.Partials.GlobalFooterJS')

</body>
</html>
