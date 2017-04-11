
@extends($layout)
@section('title','Message details')
@section('customjavascript')
<script>
// var deleteCarUrl = "{!! route('deleteCar') !!}";
// var indexUrl= "{!! route('passengerAjax') !!}";
// var homeUrl= "{!! route('index') !!}";
$(function(){
	//$(".right-side").css("min-height",'650px');
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
								if(empty($user_message_data->profile_pic)){ 
									$user_message_data->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";
								}else{
									$user_message_data->profile_pic = "/public/img/memberImages/".$user_message_data->profile_pic;
								} 
								?>
								
								{!! HTML::image( $user_message_data->profile_pic, 'a picture', array('class' => 'img-responsive img-circle profile-user-img','id'=>'profile_pic')) !!}
                 
								<h4 class=" text-center">{!! $user_message_data->first_name ."  ".  $user_message_data->last_name !!}</h4>
								<p class="text-muted text-center">{!! $user_message_data->email !!}</p>
							
							</div>
						
						</div>
                
						<div class="col-sm-12 col-md-7 col-lg-6 col-xs-12 ">
						
							<div class="box-body profile-detail center1">
								
								<div class="form-group">
									  <label class="col-sm-6 col-md-3 control-label" >Full Name</label>
									  <div class="col-sm-6 col-md-9">
									   <p>{!! $user_message_data->first_name ."  ".  $user_message_data->last_name  !!}</p>
									  </div>
								</div>
								
								<div class="form-group">
									  <label class="col-sm-6 col-md-3 control-label" >User ID</label>
									  <div class="col-sm-6 col-md-9">
									 <p> {!! $user_message_data->user_id !!}</p>
									  </div>
								</div>
								
								<div class="form-group">
									  <label class="col-sm-6 col-md-3 control-label" >Message Date</label>
									  <div class="col-sm-6 col-md-9">
										<p>@if($user_message_data->message_date=="0000-00-00 00:00:00") N/A @else {!!  date('m-d-Y', strtotime($user_message_data->message_date)) !!} @endif</p>
									  </div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-6 col-md-3 control-label" >Status</label>
									<div class="col-sm-6 col-md-9">
										<p> {!! $user_message_data->status !!}</p>
									</div>
								</div>
								
							  </div>
							</div>  
						   
							<div class="col-sm-12 col-md-5 col-lg-3 col-xs-12">
								 <!--Subadmin Permission Code Start-->
								<?php 
									$loggedInUserPermission = Session::get('userPermissions'); 
									if(empty($loggedInUserPermission)){
									?>
										<p class="tpbutton btn-toolbar" style="text-align:center">
											{!! link_to_route('messageAction', 'Delete Message', array('action' => 'delete', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-danger' )) !!}
											{!! link_to_route('messageAction', 'archive Message', array('action' => 'archive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary' )) !!}
										</p>
									<?php 
									}else if(!empty($loggedInUserPermission)){
									?>		
									<?php	
									
										foreach($loggedInUserPermission as $userPermission){
													
											if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
											?>
												<p class="tpbutton btn-toolbar" style="text-align:center">
													{!! link_to_route('messageAction', 'Delete Message', array('action' => 'delete', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-danger' )) !!}
													{!! link_to_route('messageAction', 'archive Message', array('action' => 'archive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary' )) !!}
												</p>
											<?php
											}
										}
										?>
										
									<?php 
									} 
								?>
								<!--Subadmin Permission Code Start-->
								
							</div>
						
						
							<div class="col-xs-12">  <hr/></div>
						</div>
                
						<div class="row">
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="box-body sub-text">
						   
									<div class="form-group">
										<label class="col-sm-2 col-lg-1 control-label" >Message</label>
										<div class="col-sm-10 col-lg-11">
											<p>{!! $user_message_data->message !!}</p>
										</div>
									</div> 
								</div>
							</div>
						</div>	
                
					</div>
				</div>
			</div>
   
   </section>

@stop
@stop

