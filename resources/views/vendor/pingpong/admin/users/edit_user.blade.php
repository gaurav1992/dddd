@extends($layout)
@section('title', 'Edit user')
@section('content-header')
	<h1 style="text-align:center;">
		
    @if( $user->role_id == 1 )
      {!! $title or 'Edit Account' !!} 
    @endif
    @if( $user->role_id == 2 )
      {!! $title or 'Edit Account' !!} 
    @endif

	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
  <script>



    $(function() {
   // $( "#DOB,#Anniversary" ).datepicker();
    var start = new Date();
    // set end date to max one year period:
    var end = new Date(new Date().setYear(start.getFullYear()+1));

    $('#DOB').datepicker({
        // update "toDate" defaults whenever "fromDate" changes
    }).on('changeDate', function(){
      // set the "toDate" start to not be later than "fromDate" ends:
      $('#Anniversary').datepicker('setStartDate', new Date($(this).val()));
    }); 

    $('#Anniversary').datepicker({
      startDate : start,
      endDate   : end
    // update "fromDate" defaults whenever "toDate" changes
    }).on('changeDate', function(){
      // set the "fromDate" end to not be later than "toDate" starts:
      $('#DOB').datepicker('setEndDate', new Date($(this).val()));
    });
  });
   var getcityurl= "{!! route('getcity') !!}";
  var CSRF_TOKEN= "{!! csrf_token() !!}";
  
  function cityCode(stateCode){
	  
	    
      console.log(stateCode);
      $.ajax({
         type:'post',
         url:getcityurl,
         data:'stateCode='+stateCode+'&_token='+CSRF_TOKEN,
         beforeSend: function( xhr ) {
          $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
        },
         success:function(returnData)
         {
          $('#divLoading').remove();
          // console.log(returnData);
          // var parsedJson=$.parseJSON(returnData);
          $( "#cityIds" ).html(returnData);

         }
      });
  }
	$( document ).on( "change", "#stateIds", function(){
     var stateCode= $(this).val();
	 cityCode(stateCode);
    
    });
	
if('{{$user->state}}'!=''){
		var stateCode= '{{$user->state}}';
		$("#stateIds option[value='"+stateCode+"']").attr('selected', 'selected');
		cityCode(stateCode);
		var cityId='{{$user->city}}';
		console.log(cityId);
		//$.each(console.log($('#cityIds option').val()));
		setTimeout(function(){
			//$('#cityIds').val(cityId);
			$("#cityIds option[value='"+cityId+"']").attr('selected', 'selected');
		},1000);
		
}
 /*
  *ENABLING THE CHECKBOX
  *
  */
  $('#updateuser').on('click',function(){
		var isEdit=false;
			var isView=false;
			
			if ($('#cstCareRadio').is(':checked')==true)
			{
				 
				 
					$('.pr_edit').each(function() {
						if($(this).is(":checked")==true){
							isEdit=true;
						}
					
					});
				   
					//alert(isEdit);
					$('.pr_view').each(function() {
					  if($(this).is(":checked")==true){
							isView=true;
					  }
					
					});
				   if(isEdit==true || isView==true)
				   {
					   
				   }
				   else
				   {
					alert('please select permissions'); 
				    return false;
				   }	
			}
			
			
		var orgbtnVal = $("#updateuser").text();
		
		$("#updateuser").text("Processing...");
		
		$('.pr_view').prop('disabled',false);				
		setTimeout(function(){
			//$("#addUserForm").submit();
			$("#updateuser").text(orgbtnVal);
		},500);
		 
	   
  });
 
  </script>
@stop
<?php $allActive=0; ?>
@section('content')

    <!-- Main content -->
    <section class="content"> 
	@if(Session::has('Errmessage'))
	<p class="alert {{ Session::get('alert-class', 'alert-danger') }}"><strong>Danger!</strong> {{ Session::get('Errmessage') }}</p>
	@endif
	@if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-success') }} "><strong>Success!</strong>{{ Session::get('message') }}</p>
	@endif
	{!! Form::open(array('id' => 'addUserForm','name'=>'editUserForm','files' => true)) !!}
    <!--form role="form" id="addUserForm" name="addUserForm" method="post" novalidate-->
      <div class="row">
        <div class="box">
        <div class="box-body">
          <div class="row">
           <div class="col-lg-2  col-md-2 col-sm-2 col-xs-12  ">
             <div class="browse-image text-center">
			 <?php if(empty($user->profile_pic)){ 
					$profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g"; 
				 } else {
					$profile_pic = $user->profile_pic;
				 } ?>
			    {!! HTML::image( $profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
              <input type="file" accept="image/*" name="image" id="imgbrowse">
			   <button type="button" id="browseImageBtn" class="btn btn-primary">Browse Image</button>
             </div>
           </div>
           <div class="col-lg-3 m-15 col-md-3 col-sm-3 col-xs-12  ">   
  <div class="form-group">
 
  <input type="hidden" name="user_id" value="{{ $user->user_id}}">
    <input type="text" class="form-control" name="fname" id="fname" placeholder="First Name" value="{{ $user->first_name }}" required>
  </div>
  <div class="form-group">
	<input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name"  value="{{ $user->last_name }}" required>
  </div>
    <div class="form-group m-10">
    <label class="rad-heading">Gender</label>
	<div class="pull-right"><label class="radio-inline"><input type="radio" value="male" name="gender" @if($user->gender=='male') checked @endif >Male</label>
	<label class="radio-inline"><input type="radio" value="female" name="gender" @if($user->gender=='female') checked @endif  required>Female</label></div>
	</div>
       </div>
   <div class="col-lg-3 m-15 col-md-3 col-sm-3 col-xs-12  ">
   <div class="form-group">
    <input type="email" name="Email" class="form-control" id="Email" placeholder="Email" value="{!! $user->email !!}" required>
   </div>
   <div class="form-group">
    <input type="text"  name="Phone" class="form-control" id="Phone" placeholder="Phone" value="{!! $user->contact_number !!}" required>
   </div>
   <div class="form-group">
    <input type="password"  name="pwd" class="form-control" id="pwd" value="" placeholder="Password" required>
  </div>       
  </div>
	   <div class="col-lg-4 m-15 col-md-4 col-sm-4 col-xs-12">
		 <div class="form-group dis-block">
			<label class="l_blck" for="DOB">DOB</label>
			<input type="text" id="DOB" name="DOB" placeholder="MM/DD/YYYY" value="{!!  date('m/d/Y', strtotime($user->dob)) !!}" readonly="true" required>
			<i aria-hidden="true" class="fa fa-calendar custom_cal"></i> </div>
			<div class="form-group dis-block">
			<label class="l_blck" for="Anniversary">Anniversary </label>
			<input type="text" name="Anniversary" id="Anniversary" placeholder="MM/DD/YYYY" value="@if($user->anniversary=='')@else {!! date('m/d/Y',strtotime($user->anniversary))!!} @endif" readonly="true">
			<i aria-hidden="true" class="fa fa-calendar custom_cal"></i> </div>
	   </div>
          </div>
		  
		    <div class="form-group col-md-3" style="margin-left:17%;">
				<?php $states=DB::table('dn_states')->get(); ?>
				<select id="stateIds" name="state" class="form-control" required> 
					<option value="">---State---</option>
					@foreach($states as $state)
					<option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select>
		  </div>
		<div class="form-group col-md-3">
				  
				<select id="cityIds" name="city" class="form-control" required> 
					<option value="">---City---</option>
				</select> 
		</div> 
        <hr/>
        <div class="row">
    <div class="col-lg-12">
        <div class="centt">
		<label class="rad-heading">User Type</label>
		
		@if($user->role_id=='2')
		<label class="radio-inline customerCare">&nbsp;&nbsp;<input type="radio" class="customerCare" id="cstCareRadio" name="userType" value="customerCare"  checked >Customer Care</label>@endif
		@if($user->role_id=='1')
		<label class="radio-inline superAdmin">&nbsp;&nbsp;<input type="radio" class="superAdmin" name="userType" value="superAdmin"  checked  required>Super Admin</label>@endif
		</div>
	</div>
        </div>
        <div class="row color-diff" id="authoritySection">
		
		@foreach($permissions as $permission)
		 
        <div class="form-group col-lg-3 text-center col-md-3 col-sm-3 col-xs-12 Auth" >
          <div class="border-p">
            <p><label>{!! $permission->module_name !!}</label></p>
            <label class="checkbox-inline"><input type="checkbox"  class="pr_view"  value="permission[{!! $permission->module_id !!}]" name="View[{!! $permission->module_id !!}]" @if($permission->view_permission=='1') checked @endif >View</label>
			@if($permission->module_slug != 'reports' && $permission->module_slug!='dashboard')
            <label class="checkbox-inline"><input type="checkbox" class="pr_edit"  value="permission[{!! $permission->module_id !!}]" name="Edit[{!! $permission->module_id !!}]" @if($permission->edit_permission=='1') checked @endif >Edit</label>
		    @endif
          </div>
        </div> 
			  
			@endforeach
        <p class="text-center create-but"><button class="btn btn-primary" onclick="javascript:void(0);" id="updateuser">Update User</button>
		
		<button class="btn btn-default" onclick="window.location='{{ URL::previous() }}'" type="button">Cancel</button></p>
        </div>                    
      </div>
      </div>
  </div> 
{!! Form::close() !!}  
 </section>
@stop


