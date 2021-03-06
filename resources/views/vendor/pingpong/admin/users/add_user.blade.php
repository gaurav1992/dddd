@extends($layout)
@section('title', 'Add User')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Add Admin Account' !!} 

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
  
  /*
  *ENABLING THE CHECKBOX
  *
  */
  $('#createUser').on('click',function(){
	 
		var orgbtnVal = $("#createUser").text();
		$("#createUser").text("Processing...");
		$('.pr_view').prop('disabled',false);				
		setTimeout(function(){
			//$("#addUserForm").submit();
			$("#createUser").text(orgbtnVal);
		},500);   
  });
  <?php $states=DB::table('dn_states')->get(); ?>
  var getcityurl= "{!! route('getcity') !!}";
  var CSRF_TOKEN= "{!! csrf_token() !!}";
	$( document ).on( "change", "#stateIds", function(){
      
      var stateCode= $(this).val();
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
           console.log(returnData);
          // var parsedJson=$.parseJSON(returnData);
          $( "#cityIds" ).html(returnData);
		  return false;

         }
      });
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
	{!! Form::open(array('id' => 'addUserForm','name'=>'addUserForm','files' => true)) !!}
    <!--form role="form" id="addUserForm" name="addUserForm" method="post" novalidate-->
      <div class="row">
        <div class="box">
      
        <div class="box-body">
          <div class="row">
           <div class="col-lg-2  col-md-2 col-sm-2 col-xs-12  ">
             <div class="browse-image text-center">
              <img src="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g" id="profile_pic" class="img-responsive" alt="dummy"> 
               <input type="file" accept="image/*" name="image" id="imgbrowse">
			   <button type="button" id="browseImageBtn" class="btn btn-primary">Browse Image</button>

             </div>
           </div>
           <div class="col-lg-3 m-15 col-md-3 col-sm-3 col-xs-12  ">   
  <div class="form-group">
    <input type="text" class="form-control" name="fname" id="fname" placeholder="First Name" required>
  </div>
  <div class="form-group">
	<input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name" required>
  </div>
    <div class="form-group m-10">
    <label class="rad-heading">Gender</label>
	<div class="pull-right"><label class="radio-inline"><input type="radio" name="gender" value="male" checked>Male</label>
	<label class="radio-inline"><input type="radio" name="gender" value="female" required>Female</label></div>
	</div>
           </div>
		<div class="col-lg-3 m-15 col-md-3 col-sm-3 col-xs-12  ">
			<div class="form-group">
				<input type="email" name="Email" class="form-control" id="Email" placeholder="Email" required>
			</div>
			<div class="form-group">
				<input type="text"  name="Phone" class="form-control" id="" placeholder="Phone" style="display:none;" required>
				<input type="text"  name="Phone" class="form-control" id="Phone" placeholder="Phone" autocomplete="off" required>
			</div>
			<div class="form-group">
				<input type="password"  name="pwd" class="form-control" id="" value="{!! rand(1000000,9999999) !!}" style="display:none;" placeholder="Password" required>
				<input type="password"  name="pwd" class="form-control" id="pwd" value="" autocomplete="off" placeholder="Password" required>
			</div>    
			</div> 

			
		
           <div class="col-lg-4 m-15 col-md-4 col-sm-4 col-xs-12">
             <div class="form-group dis-block" >
                <label class="l_blck" for="DOB">DOB</label>
                <input type="text" id="DOB" name="DOB" placeholder="" readonly="true" required>
                <i aria-hidden="true" id="dobCal" class="fa fa-calendar custom_cal"></i>
				<span id="error" ></span></div>
                <div class="form-group dis-block">
                <label class="l_blck" for="Anniversary">Anniversary </label>
                <input type="text" name="Anniversary" id="Anniversary" placeholder=""  readonly="true" >
                <i aria-hidden="true" class="fa fa-calendar custom_cal"></i> 
			 </div>
				
				
           </div>
		   
          </div>
		
		  <div class="form-group col-md-3" style="margin-left:17%;">
				
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
  <div class="pull-right"><label class="radio-inline"><input type="radio" name="userType" id="cstCareRadio" value="customerCare" checked>Customer Care</label>
<label class="radio-inline"><input type="radio" name="userType" value="superAdmin" id="supAdminradio" required>Super Admin</label></div>
</div>
</div>
        </div>
        <div class="row color-diff" id="authoritySection">
		
		@foreach($modules as $k=>$module)
       <?php $k++; ?>
	   @if($module->module_slug!='dashboard')
		<div class="form-group col-lg-3 text-center col-md-3 col-sm-3 col-xs-12 Auth" >
          <div class="border-p">
            <p><label>{!! $module->module_name !!}</label></p>
            <label class="checkbox-inline "><input type="checkbox" class="pr_view"   value="permission[{!! $module->id !!}]" name="View[{!! $module->id !!}]">View</label>
			
			@if($module->module_slug != 'reports')
            <label class="checkbox-inline"><input type="checkbox" class="pr_edit"  value="permission[{!! $module->id !!}]" name="Edit[{!! $module->id !!}]">Edit</label>
		    @endif
			
          </div>
        </div>
		@endif
		@if($k%4==0)
			
		@endif
		
		@endforeach

        <p class="text-center create-but"><button class="btn btn-primary" id="createUser">Create User</button><button class="btn btn-default" onclick="window.location='{{ URL::previous() }}'" type="button">Cancel</button></p>

        </div>                    
      </div>
      </div>

  </div> 
  
{!! Form::close() !!}
	  
 </section>
   

@stop

<style>
#DOB {
  color: #000 !important;
}
.form-control.error {
  color: #000 !important;
}
</style>

