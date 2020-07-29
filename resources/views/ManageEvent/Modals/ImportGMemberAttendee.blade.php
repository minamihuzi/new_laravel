{!! HTML::script(config('attendize.cdn_url_static_assets').'/dist/js/bootstrap-multiselect.js') !!}
{!! HTML::style(config('attendize.cdn_url_static_assets').'/dist/css/bootstrap-multiselect.css') !!}
<style>

	.dropdown-menu {
		border-radius: 0;
	}
	.multiselect-native-select {
		position: relative;
		select {
			border: 0 !important;
			clip: rect(0 0 0 0) !important;
			height: 1px !important;
			margin: -1px -1px -1px -3px !important;
			overflow: hidden !important;
			padding: 0 !important;
			position: absolute !important;
			width: 1px !important;
			left: 50%;
			top: 30px;
		}
	}
	.multiselect-container {
		position: absolute;
		list-style-type: none;
		margin: 0;
		padding: 0;
		.input-group {
			margin: 5px;
		}
		li {
			padding: 0;
			.multiselect-all {
				label {
					font-weight: 700;
				}
			}
			a {
				padding: 0;
				label {
					margin: 0;
					height: 100%;
					cursor: pointer;
					font-weight: 400;
					padding: 3px 20px 3px 40px;
					input[type=checkbox] {
						margin-bottom: 5px;
					}
				}
				label.radio {
					margin: 0;
				}
				label.checkbox {
					margin: 0;
				}
			}
		}
		li.multiselect-group {
			label {
				margin: 0;
				padding: 3px 20px 3px 20px;
				height: 100%;
				font-weight: 700;
			}
		}
		li.multiselect-group-clickable {
			label {
				cursor: pointer;
			}
		}
	}
	.btn-group {
		.btn-group {
				.multiselect.btn {
					border-top-left-radius: 4px;
					border-bottom-left-radius: 4px;
				}
		}
	}
	.form-inline {
		.multiselect-container {
			label.checkbox {
				padding: 3px 20px 3px 40px;
			}
			label.radio {
				padding: 3px 20px 3px 40px;
			}
			li {
				a {
					label.checkbox {
						input[type=checkbox] {
							margin-left: -20px;
							margin-right: 0;
						}
					}
					label.radio {
						input[type=radio] {
							margin-left: -20px;
							margin-right: 0;
						}
					}
				}
			}
		}
	}
	


</style>
<script>
var globalUsers = {};
$(document).ready(function() {
	var formData = new FormData();
		formData.append("user_email",'{{$user_email}}');
		formData.append("user_name", '');
	$.ajax({
		url: "http://localhost/chameleon_last/get_group_members.php?user_email={{$user_email}}",
		type: "GET",
		data:  formData,
		contentType: "json",
		cache: false,
		processData:false,
		success: function(response)
		{
			var u_json = JSON.parse(response);
			var option_u = "";			
			for(i=0;i<u_json.total;i++){
				var em = u_json.users[i].email;
				var name = u_json.users[i].name;
				globalUsers[em] = name;
				option_u = option_u+"<option value='"+u_json.users[i].email+"'>"+u_json.users[i].name+"</option>";
			}
			$("#multiselect").html(option_u);
			$('#multiselect').multiselect({
				buttonWidth : '160px',
				includeSelectAllOption : true,
					nonSelectedText: 'Select members'
			});
		},
		error: function(){
			console.log("error");			
		}
	});

});

function getSelectedValues() {
  var selectedVal = $("#multiselect").val();
	if(selectedVal ==null || selectedVal.length==0){
		$("#group_state").val(0);
		alert("Please select group users.");
	}else{
		var accounting = [];
		for(var i=0; i<selectedVal.length; i++){			
			var em = selectedVal[i]
			accounting.push({ 
				"first_name" : globalUsers[em],
				"last_name"  : '',
				"email"       : selectedVal[i] 
			});			
		}
		
		var formData = new FormData();
		var ticket_id = $("#ticket_id").val();
		
		formData.append("ticket_id",ticket_id);
		formData.append("user_email",'{{$user_email}}');
		formData.append("email_ticket",'1');
		formData.append("group_state", '1');
		formData.append("accounting",JSON.stringify(accounting));
		formData.append('csrfmiddlewaretoken', $("meta[name='_token']").attr('content'));
		
		var ticket_name = $( "#ticket_id option:selected" ).text();
		var reData = {
			ticket_id: ticket_id,
			accounting: JSON.stringify(accounting),
			ticket_name: ticket_name,
			user_email: '{{$user_email}}'
		};
		$.ajax({
				url         : "http://localhost/chameleon_last/send_invites_mails.php",
				dataType    : 'json',           
				data        : reData,                         
				type        : 'post',
				success     : function(output){
					var url = "http://localhost/Whisprrz_Wevents_new/public/event/{{$event->id}}/attendees";
					window.open(url,'_self');
				}
		});	
		/*
		$.ajax({
			url: "http://localhost/chameleon_last/send_invites_mails.php?ticket_id="+ticket_id+"&accounting="+JSON.stringify(accounting)+"&user_email={{$user_email}}",
			type: "GET",
			data:  reData,
			contentType: "json",
			cache: false,
			processData:false,
			success: function(response)
			{
				console.log(response);
				var u_json = JSON.parse(response);
				console.log(u_json);
			},
			error: function(){
				console.log("error");			
			}
		});
		
		$.ajax({
			url: "http://localhost/Whisprrz_Wevents/public/event/{{$event->id}}/attendees/import?group_state="+1,
			type: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function (request) {
				return request.setRequestHeader('X-CSRF-Token', $("meta[name='_token']").attr('content'));
			},
			success: function(response)
			{
				var url = response.redirectUrl;
				window.open(url,'_self');
			},
			error: function(){
				
			}
		});
		*/
	}
}

</script>
<div role="dialog"  class="modal fade " style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-user-plus"></i>
                    @lang("ManageEvent.import_group_members")</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                   {!! Form::label('ticket_id', trans("ManageEvent.ticket"), array('class'=>'control-label required')) !!}
                                   {!! Form::select('ticket_id', $tickets, null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
						<!-- Import -->
						<div class="row">
							<div class="col-md-8">
								<select id="multiselect" multiple="multiple">
									<!--
									@foreach($group_members as $mem)
										<option value="{{ $mem }}">{{ $mem }}</option>
									@endforeach									
									-->
								</select>								
								<input type="hidden" name="group_state" id="group_state" value="0">
							</div>
						</div>
						<br>                        
                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               <input type="button" onclick="getSelectedValues()" class="btn btn-primary" value="@lang('basic.invite_attendees')">
            </div>
        </div><!-- /end modal content-->
       
    </div>
</div>
