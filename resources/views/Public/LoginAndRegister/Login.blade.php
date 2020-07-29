@extends('Shared.Layouts.MasterWithoutMenus')
payment_check('Wevents');
@section('title', trans("User.login"))
<script>
function login_chk(){
		var formData = new FormData();
			var email = $('#email').val();
			var password = $('#password').val();
			formData.append("email",email);
			formData.append("password", password);
			formData.append("option","Wevents");
			formData.append("user_type", "");
		if(email==""){
			$('#email').focus();
			return;
		}
		if(password==""){
			$('#password').focus();
			return;
		}
		$.ajax({
			//url: "https://whisprrz.com/getPaymentState.php?user_type=&option='Wevents'&days=&user_name="+email,
			url: "http://localhost/chameleon_last/getPaymentState.php?user_type=&option='Wevents'&days=&user_name="+email,
			type: "GET",
			data:  formData,
			contentType: "json",
			cache: false,
			processData:false,
			success: function(response)
			{
				var u_json = JSON.parse(response);
				if(u_json.paystate==1){
					 $("#loginForm").submit(); 
				}else{					
					alert("You have to pay for login.");
					//window.location.replace("https://whisprrz.com/upgrade.php");
					return;
				}
			},
			error: function(){
				
			}
		});
	}
</script>
@section('content')
    
	{!! Form::open(array('url' => route("login"),'id' => 'loginForm')) !!}
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel">
                <div class="panel-body">
                    <div class="logo">
                        {!!HTML::image('assets/images/logo-dark.png')!!}
                    </div>

                    @if(Session::has('failed'))
                        <h4 class="text-danger mt0">@lang("basic.whoops")! </h4>
                        <ul class="list-group">
                            <li class="list-group-item">@lang("User.login_fail_msg")</li>
                        </ul>
                    @endif

                    <div class="form-group">
                        {!! Form::label('email', trans("basic.name"), ['class' => 'control-label']) !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'autofocus' => true]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', trans("User.password"), ['class' => 'control-label']) !!}
                        {!! Form::password('password',  ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <button type="button" id="login" onclick="login_chk()" class="btn btn-block btn-success">@lang("User.login")</button>
                    </div>

                    @if(Utils::isAttendize())
                    <div class="signup">
                        <span>@lang("User.dont_have_account_button", ["url"=> route('showSignup')])</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop
