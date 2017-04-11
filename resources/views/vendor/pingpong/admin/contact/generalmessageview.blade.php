
@extends($layout)
@section('title','General Issue Details')
@section('customjavascript')
<script>
// var deleteCarUrl = "{!! route('deleteCar') !!}";
// var indexUrl= "{!! route('passengerAjax') !!}";
// var homeUrl= "{!! route('index') !!}";
$(function(){

	$(".right-side").css("min-height",'650px');

	$("#genIssueRep").validate({
				rules: {
					body_message:{
						required:true,
					}
				},
				messages: {
					body_message:{
						required:'This field is required.'
					},
				} 
	});
});

</script>
@stop
@section('content')

 
    <!-- Main content -->
    <section class="content"> 
	@if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
	@endif
      <!-- Info boxes -->
		<div class="row">
			
			<div class="col-md-12">
				
				<div class="box">
					
						<div class="row">
							
							<div class="col-sm-12 col-md-12 col-lg-3 col-xs-12">
								
								<div class="box-body profile-picture">
									
									<?php 
									if(empty($general_message_data->profile_pic)){ 
										$profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";
									}else{
										$profile_pic = $general_message_data->profile_pic;
									} 
									?>
									
									{!! HTML::image( $profile_pic, 'a picture', array('class' => 'img-responsive img-circle profile-user-img','id'=>'profile_pic')) !!}
					 
									<h4 class=" text-center">{!! @$general_message_data->full_name !!}</h4>
									<p class="text-muted text-center">{!! @$general_message_data->email !!}</p>
								
								</div>
							
							</div>
					
							<div class="col-sm-12 col-md-7 col-lg-6 col-xs-12 ">
							
								<div class="box-body profile-detail center1">
									
									<div class="form-group">
										  <label class="col-sm-6 col-md-3 control-label" >Full Name</label>
										  <div class="col-sm-6 col-md-9">
										   <p>{!! @$general_message_data->first_name  !!} {!! @$general_message_data->last_name  !!}</p>
										  </div>
									</div>
									
									<div class="form-group">
										  <label class="col-sm-6 col-md-3 control-label" >User ID</label>
										  <div class="col-sm-6 col-md-9">
										 <p> {!! @$general_message_data->user_id !!}</p>
										  </div>
									</div>
									
									<div class="form-group">
										  <label class="col-sm-6 col-md-3 control-label" >Message Date</label>
										  <div class="col-sm-6 col-md-9">
											<p>@if(@$general_message_data->created_at=="0000-00-00 00:00:00") N/A @else {!!  date('m-d-Y', strtotime(@$general_message_data->created_at)) !!} @endif</p>
										  </div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-6 col-md-3 control-label" >Status</label>
										<div class="col-sm-6 col-md-9">
											<p> 
												<?php 
													echo ucwords(str_replace("_"," ",$general_message_data->status));
												?>
											</p>
										</div>
									</div>
									
								  </div>
								</div>  
							 
							<div class="col-xs-12">  <hr/></div>
						</div>
					
						<div class="row">
							
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								
								<div class="box-body sub-text">
						   
									<div class="form-group">
										<label class="col-sm-2 col-lg-1 control-label" >Message</label>
										<div class="col-sm-10 col-lg-11">
											<p>
												@if(@$general_message_data->message=="") 
													N/A 
												@else 
													{!!  @$general_message_data->message !!} 
												@endif
											</p>
										</div>
									</div> 
									
								</div>
								
							</div>
						
						</div>	
						 <!--Subadmin Permission Code Start-->
							<?php 
								$loggedInUserPermission = Session::get('userPermissions'); 
								if(empty($loggedInUserPermission)){
								?>
									<div class="row padLR20">
							
										<div class="col-md-12">
											{!! Form::open(array('url' => array('/admin/view_general_message',$orgid), 'id' => 'genIssueRep','method'=>'Post','files' => true)) !!} 
											<!--<form  id="genIssueRep" method="post" action="/admin/view_general_message/{{ $orgid }}">-->
												<div class="form-group">
													<label for="message">Message for reply</label>
													<textarea name="body_message" class="form-control" placeholder="Start typing for reply on passenger issue..."></textarea>
												</div>
												<input name="submit" value="Send Reply" type="submit" class="btn btn-success">
											<!--</form>-->
											{!! Form::close() !!}
			 
										
										</div>		
										
									</div>
									
								<?php 
								}else if(!empty($loggedInUserPermission)){
								?>		
								<?php	
								
									foreach($loggedInUserPermission as $userPermission){
												
										if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
										?>
											<div class="row padLR20">
							
												<div class="col-md-12">
													{!! Form::open(array('url' => array('/admin/view_general_message',$orgid), 'id' => 'genIssueRep','method'=>'Post','files' => true)) !!} 
													<!--<form  id="genIssueRep" method="post" action="/admin/view_general_message/{{ $orgid }}">-->
														<div class="form-group">
															<label for="message">Message for reply</label>
															<textarea name="body_message" class="form-control" placeholder="Start typing for reply on passenger issue..."></textarea>
														</div>
														<input name="submit" value="Send Reply" type="submit" class="btn btn-success">
													<!--</form>-->
													{!! Form::close() !!}
					 
												
												</div>		
												
											</div>
											
										<?php
										}
									}
									?>
									
								<?php 
								} 
							?>
							<!--Subadmin Permission Code Start-->
						
				</div>
					
					
			</div>
				
		</div>
		
   </section>

@stop
@stop

<style>
.padLR20 {
  padding: 0 25px 40px
}
</style>
