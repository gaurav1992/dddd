<?php 
$user=$data['user'];
$message_Archive=$data['message_Archive'];
$lastLoginData=$data['lastLoginData'];
$lastLogin=$data['lastLogin'];

?>
@extends($layout)

@section('customjavascript')
<script>
var Loghistory= "{!! route('loghistory') !!}";
var user_id = $("#user_id").val();
if(typeof Loghistory !== 'undefined'){ 
	  	var Loghistory=$('#logHisotry').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"searching": false,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": Loghistory,
			"data": function ( d ) {

                d.startDate = $('#startDates').val();
				d.endDate = $('#endDates').val();
                d.user_id = $("#user_id").val();
				d.adminId="{!! $user->id !!}";
               // alert(d.user_id);
			}
			
		  }
		
		});
	}

	if($('#logHisotry').length){
		var tableTools = new $.fn.dataTable.Buttons( Loghistory, {
			buttons: [{ extend: 'pdf', title: 'Log List',text: 'Generate Report', className: 'btn btn-default btn_pad color-blue drlists genRepoBtn',exportOptions:{columns: [0,1, 2,3,4]}}]
		});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#end');	 
				
	}
	
 		$("#startDates,#endDates").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
               
                $(this).datepicker("hide");
                Loghistory.ajax.reload();
              

            });

        });
 validateDateRange('startDates', 'endDates');

</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>
@stop
@section('style')
<style>
.emailWidth{
	width: 240px;
	margin-left:15px;
}
</style>
@stop
@section('content')

                <!-- Main content -->
        <section class="content">	
		
            <div class="row">
            	<div class="box">
               	 	<div class="box-body">
                    	<div class="row">
                        	<div class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                            	<div class="browse-image text-center">
							 <?php if(empty($user->profile_pic)){ $profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";
								} else{
									 $profile_pic = asset('').'/'.$user->profile_pic;
									} ?>
							 {!! HTML::image($profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                
                            	</div>
								<ul class="profile_dl text-center">
									<li>
									<p class="rad-heading txt_lft"> <label> Full Name : </label> {{$user->first_name .' '. $user->last_name }} </p>
									</li>
								
									<li>
									<p class="rad-heading txt_lft"> <label> Email : </label> {{$user->email}} </p>
									</li>
									
									<li>
									<p class="rad-heading txt_lft"> <label> Phone : </label> {{$user->contact_number}}</p>
									</li>												
									
									<li>
									<p class="rad-heading txt_lft"> <label> DOB : </label> {!!  date('m-d-Y', strtotime($user->dob)) !!} </p>
									</li>
									
									
									<li>
									<p class="rad-heading txt_lft"> <label> Anniversary: </label> @if($user->anniversary=="0000-00-00") {!! $user->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($user->anniversary)) !!} @endif</p>
									</li>

									<li>
									<p class="rad-heading txt_lft"> <label> Gender: </label> {{$user->gender}}</p>
									</li>	
								
								</ul>
	                        </div>

                        	<div class="col-lg-9 m-15 col-md-3 col-sm-3 col-xs-12">
	
								<div class="row"> 
		                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
		                                <div class="form-group">
											<p class="rad-heading txt_lft"> <label> User Type : </label>{{$user->type}}  </p>
										</div>
										
										<div class="form-group">
											<p class="rad-heading txt_lft"> <label> User ID : </label> {{$user->unique_code}} </p>
										</div>
										
										<div class="form-group">
											<p class="rad-heading txt_lft"> <label> Status : </label> @if($user->active == 1) Active @else Blocked @endif </p>
										</div>
		                            </div>
		                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
		                              
										
										<div class="form-group">
											<p class="rad-heading txt_lft"> <label> Join Date : </label>  {!!  date('m-d-Y', strtotime($user->created_at)) !!}  </p>
										</div>
										<?php // echo"<pre>"; print_r($lastLogin);die;?>

										<div class="form-group">
											<p class="rad-heading txt_lft"> <label> Last Login : </label> {!! @$lastLogin !!}</p>
										</div>
		                            </div>
                                    
                                    
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-center">
		                              
										
									
                                        
                                         <div class="form-group">
											<p class="rad-heading txt_lft"> <label> Refunds Given  :</label> </p> 
										</div>	
                                        
                                        
                                        <div class="form-group">
												<p class="rad-heading text-center  txt_lft"> <label >  Message Archived :
												</label> {{$message_Archive}} </p>
											</div>
                                            
                                            <div class="form-group">
											<p class="rad-heading text-center txt_lft"> <label> Hours Logged : </label>  </p>
											</div>
                                    	
		                        </div>
							
						
								
                    		</div>
                		</div>
                	</div>
				</div>
        	</div>

	  
	  <!-- User History -->
	  		
		<div class="row">
        	<div class="col-md-12">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>User Log</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input class="form-control321" type="text" Placeholder="Start Date" id="startDates"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end">
								<label>End Date</label> <br>
								<input class="form-control321" type="text" Placeholder="End Date" id="endDates"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							<input id="user_id" type="hidden" value="<?=$user->id;?>">
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="logHisotry" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Activity</th>
				                    <th>User Name</th>
				                    <th>User Id</th>
				                    <th>Time stamp</th>
			                  	</tr>
			                </thead>
		              	</table>
					</div> 
          		</div>
        	</div>
      	</div>
	

      	  <!-- User History END -->
</section>

@stop
@stop
