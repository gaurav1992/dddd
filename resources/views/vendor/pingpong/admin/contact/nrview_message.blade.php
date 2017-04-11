
@extends($layout)

@section('customjavascript')
<script>
// var deleteCarUrl = "{!! route('deleteCar') !!}";
// var indexUrl= "{!! route('passengerAjax') !!}";
// var homeUrl= "{!! route('index') !!}";
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
              
                <div class="col-sm-12 col-md-7 col-lg-6 col-xs-12 ">
                <div class="box-body profile-detail center1">
                    
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
										<p class="tpbutton btn-toolbar text-center">
											{!! link_to_route('messageAction', 'Delete Message', array('action' => 'nrdelete', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-danger' )) !!}
											
											@if($user_message_data->status=='Archive')
												{!! link_to_route('messageAction', 'archive Message', array('action' => 'nrarchive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary disabled' )) !!}
											@else
												{!! link_to_route('messageAction', 'archive Message', array('action' => 'nrarchive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary' )) !!}
											@endif            
										</p>
									<?php 
									}else if(!empty($loggedInUserPermission)){
									?>		
									<?php	
									
										foreach($loggedInUserPermission as $userPermission){
													
											if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
											?>
												<p class="tpbutton btn-toolbar text-center">
													{!! link_to_route('messageAction', 'Delete Message', array('action' => 'nrdelete', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-danger' )) !!}
													
													@if($user_message_data->status=='Archive')
														{!! link_to_route('messageAction', 'archive Message', array('action' => 'nrarchive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary disabled' )) !!}
													@else
														{!! link_to_route('messageAction', 'archive Message', array('action' => 'nrarchive', 'id' =>  base64_encode(convert_uuencode($user_message_data->message_id))), $attributes = array('class'=>'btn navbar-btn btn-primary' )) !!}
													@endif            
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
								
								<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
									<label class="control-label" >Subject</label>
								</div>
								
								<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
									<p>
										@if($user_message_data->subject=='')
											N/A
										@else
											{!! $user_message_data->subject !!}		
										@endif            
										
									</p>
								</div>
							</div>
							
							<div class="form-group">
								<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
									<label class="control-label" >Message</label>
								</div>
								
								<div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
								
									<p>
										@if($user_message_data->message=='')
											N/A
										@else
											{!! $user_message_data->message !!}		
										@endif            
										
									</p>
									
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
