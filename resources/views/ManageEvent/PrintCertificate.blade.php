<html>
    <head>
        <title>
            @lang('basic.sign')
        </title>

        <!--Style-->
       {!!HTML::style('assets/stylesheet/application.css')!!}
	   {!! HTML::script(config('attendize.cdn_url_static_assets').'/vendor/jquery/dist/jquery.min.js') !!}
        <!--/Style-->

        <style type="text/css">
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                padding: 3px;
            }
            table {
                font-size: 13px;
            }
			#org_sign{
				height:100px;
			}
			.template-page.edit-mode .form-group{
				border:none !important;
			}
			.tools{
				display:none;
			}
        </style>
		<script>
			$(document).ready(function() {
				$("#org_name").html("{{$attendee->full_name}}");
				var ceu = {{$event->ceu_total}};
				var ceu_hr = 1*{{$event->ceu_hr}};
				var checkintime = {{gmdate("H", $attendee->period_in)}};
				var checkintime_m = {{gmdate("i", $attendee->period_in)}};
				var ceu_val = 1.0*(1.0*ceu_hr/ceu)*checkintime+(1.0*ceu_hr/ceu)*checkintime_m/60; 
				$("#org_hrs").html(ceu_val.toFixed(2)+" {{$event->ceu_unit}}");
				$("#org_name").html("{{$attendee->full_name}}");
			});		
		</script>
    </head>
    <body style="background-color: #FFFFFF;" onload="window.print();">     
		<div class="row" id="pdf_content">
			<table width="100%">
				<tr><td style="padding:20px;text-align: left; font-size: 20px;">
						<style>
							.form-control{
								border:none;
							}
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
						
						<link rel="stylesheet" href="{{asset('assets/stylesheet/template.css')}}" />
						<div class="container">
							@if(isset($template))
								<h1>Certificate</h1>
							@else
								<h1>Empty Certificate</h1>
							@endif

							<div class="row">
								<div class="col-md-12">			
									<div class="col-md-12 col-sm-12 template-col">
										<img src = '<?=(isset($template))? $template->img_background: ""?>' style="position:absolute;width:95%;height:100%">
										<form class="template-page edit-mode" template-id="<?=(isset($template))? $template->id:'0' ?>" style='<?=(isset($template) && isset($template->padding))? "padding:".$template->padding: ""?>'>
											<div class="header" element-type="header" style="position:relative;">
												<div><span class="title" value=""><?=(isset($template))? $template->title: "[TEMPLATE TITLE]" ?></span></div>
												@if(isset($template->logo) && $template->logo!="")
													<img src="<?=$template->logo; ?>" class="logo-preview <?=$template->logo_position; ?>" value="" alt="LOGO"/>						    
												@else
													<img src="{{asset('assets/images/apple-touch-icon.png')}}" class="logo-preview" value="" alt="LOGO"/>	
												@endif						
											</div>
											<div class="text-muted empty-form text-center" id="point_value" style="font-size: 24px;"></div>
											<div class="row form-body template-contents">
											@if(isset($template))
												<?=$template->contents?>
											@else
												<div class="col-md-12 droppable sortable">
												</div>
												<div class="col-md-6 droppable sortable" style="display: none;">
												</div>
												<div class="col-md-6 droppable sortable" style="display: none;">
												</div>
											@endif
											</div>
										</form>
									</div>
								</div>
							</div>
							<div id="scrollgrabber"></div>
						</div>
					</td></tr>
			</table>
		</div>  
    </body>
</html>
