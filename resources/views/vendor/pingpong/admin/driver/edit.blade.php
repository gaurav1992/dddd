<?php //echo  $user->id;exit;?>
@extends($layout)
@section('title', 'Driver profile')
@section('customjavascript')
<script>
var deleteCarUrl = "{!! route('deleteCar') !!}";
var getcityurl= "{!! route('getcity') !!}";
var CSRF_TOKEN= "{!! csrf_token() !!}";
function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                jQuery('.passengerprofile.profilePic').attr('src', e.target.result);
				jQuery(".passengerprofile.profilePic").css({ 'height': '100px', 'width': '100px' });
				//jQuery(".passengerprofile.profilePic").attr('width','100');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
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

         }
      });
    });
	
	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
          return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Please enter only letters");


jQuery("#editdriver").validate({
  errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
			first_name: { required: true },
			profile_pic: { required: true },
			last_name: { required: true},
			contact_number: { required: true},
			//address_2: { required: true},
			email: { required: true},
			licence_exp: { required: true},
			anniversary: { required: false},
			address_1: { required: true},
			city: { required: true},
			state: { required: true},
			ssn:{ required: true,digits: true,minlength: 9,maxlength: 9},
			zip_code: { required: true,minlength: 4,maxlength: 6,number: true},
			license_number: { required: true},
			termsConditions: { required: true},
			proofins:{required: true},
			licVer:{required: true}
    },
    submitHandler:function(form){
      jQuery(".lodingDiv").show();
      jQuery(".UpdateDriverProfile").attr('disabled','disabled');
            form.submit();
    }
  });
	jQuery("#PhoneVerification").validate({
    errorClass: "my-error-class",
    validClass: "my-valid-class",
    rules: {
        phone_number: { required: true, minlength: 8,maxlength: 15,number: true}
    },
});
nowDob = new Date("1900/01/01");
      defaultDob = new Date(nowDob.getFullYear(), nowDob.getMonth(), nowDob.getDate(), 0, 0, 0, 0);
      FromEndDate = new Date();
// Date Pickers
      jQuery('#datetimepicker1 #dob').datepicker({
        format: 'mm/dd/yyyy',
        startDate: defaultDob,
        endDate : FromEndDate,
        autoclose: true
      });
	  
	   jQuery('#datetimepicker4 #anniversary').datepicker({
        format: 'mm/dd/yyyy',
        startDate: defaultDob,
        autoclose: true
      });
var dp_now = new Date();
   
    var dp_yesterday = new Date(dp_now.getFullYear(), dp_now.getMonth(), dp_now.getDate()-1);
    var dp_tomorrow = new Date(dp_now.getFullYear(), dp_now.getMonth(), dp_now.getDate()+1);
     jQuery('#datetimepicker5 #licence_exp').datepicker({
        format: 'mm/dd/yyyy',
        startDate: dp_tomorrow,
        autoclose: true
      });
     jQuery('#datetimepicker6 #insurance_exp').datepicker({
        format: 'mm/dd/yyyy',
        startDate: dp_tomorrow,
        autoclose: true
      });
/*--jquery start for check file extension--*/
      jQuery(".uploadProfilePic input").change(function(){
		  
        var filename = jQuery(this).val();
        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            return false();
          } else if (extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif'){
            readURL(this);
          } else{
            jQuery("#profile_pic").val('');
            alert("Use Only jpg, PNG, JPEG, gif formet");
            return false();
          }
      });
	  
jQuery(".license_verification").change(function(){
        jQuery(".DriverLicense").attr("value",'');
        var filename = jQuery(this).val();
        var filenameonly = filename.replace(/^.*[\\\/]/, '');

        var lengthfile = $.trim(filenameonly).length;
        if(lengthfile > '15' ){
          var fileUploadName = filenameonly.slice(-15);
        }else{
          var fileUploadName = filenameonly;
        }

        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            jQuery(".DriverLicense").attr("value",'');
            return false;
          }else{
            jQuery(".DriverLicense").attr("value",fileUploadName);
          }
        /*--//size limit for file upload code end--*/  
        if(extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'pdf' || extension == 'doc' || extension == 'rtf' || extension == 'docx'){

        }else{
          jQuery(".DriverLicense").attr("value",'');
          alert("Use Only PDF, doc, rtf, jpg, PNG, JPEG, gif, docx");
          return false;
        }
      });
      jQuery(".proof_of_insurance").change(function(){
        jQuery(".ProofOfInsurance").attr("value",'');
        var filename = jQuery(this).val();
        var filenameonly = filename.replace(/^.*[\\\/]/, '');
        
        var lengthfile = $.trim(filenameonly).length;
          if(lengthfile > '15' ){
            var fileUploadName = filenameonly.slice(-15);
          }else{
            var fileUploadName = filenameonly;
          }

        var extension = filename.replace(/^.*\./, '');
        /*--size limit for file upload code start--*/
          var fileSize = jQuery(this).get(0).files[0].size; // in bytes
          var sizeInMB = (fileSize / (1024*1024)).toFixed(2);
          if(fileSize > '15000000'){
            alert("file size is more then 15 MB (Please Select File Size Less Than 15 MB)");
            jQuery(".ProofOfInsurance").attr("value",'');
            return false;
          }else{
            //readURL(this);
            jQuery(".ProofOfInsurance").attr("value",fileUploadName);
          }
        /*--//size limit for file upload code end--*/  
        if(extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif' || extension == 'pdf' || extension == 'doc' || extension == 'rtf' || extension == 'docx'){

        }else{
          jQuery(".ProofOfInsurance").attr("value",'');
          alert("Use Only PDF, doc, rtf, jpg, PNG, JPEG, gif, docx");
          return false;
        }
      });
	 validateDateRange('dob','anniversary');
	  $( document ).ready(function() {
	/**
	 *VALIDATING DATE RANGE
	 *
	 */
	  
	 $('#dob,#anniversary').on("change", function(){
		 	var inputDate=$(this).val();
			var dateA = new Date(inputDate);
			var dateB = new Date();
			var id=$(this).attr("id");
			var dateAniversary = new Date($('#anniversary').val());
			if (dateA >= dateB){
				alert("Date should be less than today's Date!");
				$(this).val("");
				return false;
			}

			if(id=="dob"){
				if (dateA >= dateAniversary){
				alert("DOB should be less than Anniversary's Date!");
				$(this).val("");
				return false;
				}
				
				var today=new Date();
				if((dateA.getFullYear() + 18) > today.getFullYear())
				{
					alert("Driver's age should not be less than 18 years.");
					$(this).val("");
					return false;
				}
			}else if(id=="anniversary"){
				var dob=$('#dob').val();
				if (dob >= dateAniversary){
				alert("Anniversary should be greater than DOB's Date!");
				$(this).val("");
				return false;
				}
			}
			
		 
	 });
	$('#anniversary').on("change", function(){
			var dob=$("#dob").val();
			var anniversary=$("#anniversary").val();
			var dateA = new Date(dob);
			var dateB = new Date(anniversary);
			if (dateA >= dateB){
				alert("DOB should be less than Anniversary Date!");
				$("#anniversary").val("");
				return false;
			}
			// set end date to max one year period:
			});
		});
/*--//jquery end for check file extension--*/

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
	
if('{{$usersData->state}}'!=''){
		var stateCode= '{{$usersData->state}}';
		$("#stateIds option[value='"+stateCode+"']").attr('selected', 'selected');
		cityCode(stateCode);
		var cityId='{{$usersData->city}}';
		console.log(cityId);
		//$.each(console.log($('#cityIds option').val()));
		setTimeout(function(){
			//$('#cityIds').val(cityId);
			$("#cityIds option[value='"+cityId+"']").attr('selected', 'selected');
		},1000);
		
}
</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>
@stop

@section('content')

<!-- Main content -->
	<div class="row">
		<div class="col-md-12">
			<div class="main_heading">
                   <h3>Edit Driver Details</h3>
			</div>
		</div>
	</div>
                
    <section class="content">	
		<div class="row">
			<div class="col-md-12">
				 <div class="col-md-12">
    

    {!! Form::open(array('url' => route('editDriver',$usersData->id),'class' => 'form editdriver','files' => true,'id'=>'editdriver')) !!}
    <?php 
			$license_number = '';
            $driver_profile_pic = '';
            $ssn = '';
			$driver_profile_pic=$dn_users_data->driver_profile_pic;
			$first_name=$usersData->first_name;
			$last_name=$usersData->last_name;
			$email=$usersData->email;
			$license_number=$dn_users_data->license_number;
			$ssn=$dn_users_data->ssn;
			
			$contact_number = $usersData->contact_number;
			$dob=$usersData->dob;
			$anniversary=$usersData->anniversary;
			$address_1=$usersData->address_1;
			$address_2=$usersData->address_2;
			$city=$usersData->city;
			$state=$usersData->state;
			$zip_code=$usersData->zip_code;
			$gender = $usersData->gender;
			
			 if($dob ==''){
            $newDob = '';
          }else{
            $origionalDob = $dob;
            $arrBob = explode('-', $origionalDob);
            $newDob = $arrBob[1].'/'.$arrBob[2].'/'.$arrBob[0];
          }

          if($anniversary ==''){
            $newanniversary = '';
          }else{
            $origionalanniversary = $anniversary;
            $arranniversary = explode('-', $origionalanniversary);
            $newanniversary = $arranniversary[1].'/'.$arranniversary[2].'/'.$arranniversary[0];
          }
		  $driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $id)->first();
        if($driver_detail){
            $car_transmission = $driver_detail->car_transmission;
            $license_verification = $driver_detail->license_verification;
            $proof_of_insurance = $driver_detail->proof_of_insurance;

            $licence_expirationDb = $driver_detail->licence_expiration;
            $liceexp = new DateTime($licence_expirationDb);
            $licence_expiration = $liceexp->format('m/d/Y');
            
            $insurance_expirationdb = $driver_detail->insurance_expiration;
            $insuexp = new DateTime($insurance_expirationdb);
            $insurance_expiration = $insuexp->format('m/d/Y');

        }else{
            $car_transmission = '';
            $licence_expiration = '';
            $insurance_expiration = '';
            $license_verification = '';
            $proof_of_insurance = '';
        }
		
			if(empty($driver_profile_pic)){ 
				 $imgEmpty=true;
				 $driver_profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
					 $imgEmpty=false;
						$driver_profile_pic = $driver_profile_pic;} ?>	
	
	 <div class="col-sm-12 smallScreen_padzero">
	 <center>
      <div class="edit-user uploadProfilePic">

	  
        <?php if($driver_profile_pic !=''){ ?>
            <img src="{!! asset($driver_profile_pic) !!}"  class="img-responsive center  passengerprofile profilePic" style="height:100px;" alt="#"/>
        <?php }else{ ?>
            <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive   passengerprofile profilePic" style="height:100px;" alt="#"/>
        <?php } ?>   
		 <label for="profile_pic" class="btn img-upload"><i class="fa fa-camera cameraSymbol cam_mrg" aria-hidden="true"></i></label>
        <input type="file" id="profile_pic" name="profile_pic"  class="uploadFile" data-max-size="1000000" accept="image/jpg,image/png,image/jpeg,image/gif">
        <p class="">Change profile picture</p> 
      </div>
    </center>
	</div>
	
      <div class="col-sm-12 smallScreen_padzero">
          <h3 class="page-heading">Profile-information</h3>       
        <div class="col-md-6 smallScreen_padzero">
        <label for="first_name">First Name:<supscript class="text-red"> * </supscript></label>
          <fieldset class="form-group">
            {!! Form::text('first_name',$first_name,array('id'=>'first_name','class'=>'form-control required-field','placeholder' => 'First Name')) !!}
			 {!! Form::hidden('id',$usersData->id,array('id'=>'id','class'=>'form-control')) !!}
          </fieldset>
        </div>
        <div class="col-md-6 smallScreen_padzero">
        <label for="last_name">Last Name: <supscript class="text-red"> * </supscript></label>
          <fieldset class="form-group">
            {!! Form::text('last_name',$last_name,array('id'=>'','class'=>'form-control','placeholder' => 'Last Name')) !!}
          </fieldset>
        </div>
        
        <div class="col-md-6 smallScreen_padzero">
        <label for="email">Email: <supscript class="text-red"> * </supscript></label>
          <fieldset class="form-group">
            {!! Form::email('email',$email,array('id'=>'emailDriver','class'=>'form-control','placeholder' => 'Email')) !!}
          </fieldset>
        </div>

          <div class="col-md-6 smallScreen_padzero">
          <label for="license_number">Driver's License #: <supscript class="text-red"> * </supscript></label>
            <fieldset class="form-group">
              {!! Form::text('license_number',$license_number,array('id'=>'','class'=>'form-control','placeholder' => 'Driver License #')) !!}
            </fieldset>
          </div>
          <div class="col-md-6 smallScreen_padzero">
          <label for="license_number">SSN/TIN:<supscript class="text-red"> * </supscript></label>
            <fieldset class="form-group">
              {!! Form::text('ssn',$ssn,array('id'=>'ssn-tin','class'=>'form-control','placeholder' => 'SSN/TIN')) !!}
            </fieldset>
          </div>
          <div class="col-md-6 smallScreen_padzero">
          <label for="contact_number">Contact Number:<supscript class="text-red"> * </supscript></label>
            <fieldset class="form-group">
              {!! Form::text('contact_number',$contact_number,array('id'=>'','class'=>'form-control','placeholder' => 'Contact Number')) !!}
            </fieldset>
          </div>
              <div class="col-md-6 smallScreen_padzero">
              <label for="licence_exp">License Expiration:<supscript class="text-red"> * </supscript></label>
               <fieldset class="form-group">
                  <div class='input-group date boder date-picker-one datePicker' id='datetimepicker5'>
                      {!! Form::text('licence_exp',$licence_expiration,array('id'=>'licence_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required','readonly'=>'readonly')) !!}
                      <a class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </a>
                  </div>
                </fieldset>
            </div>

        <div class="col-md-6 smallScreen_padzero">
        <label for="insurance_exp">Insurance Expiration:<supscript class="text-red"> * </supscript></label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two datePicker' id='datetimepicker6'>
                  {!! Form::text('insurance_exp',$insurance_expiration,array('id'=>'insurance_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required','readonly'=>'readonly')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
        </div>
        <div class="col-md-6 smallScreen_padzero">
          <label for="dob">Date of birth: <supscript class="text-red"> * </supscript></label>
          <?php if($newDob ==''){ ?>
             <fieldset class="form-group">
                <div class='input-group date boder date-picker-three datePicker' id='datetimepicker1'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a>
                </div>
             </fieldset>
          <?php } else{ ?>
              <fieldset class="form-group">
                <div class='input-group date boder date-picker-three'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a>
                </div>
              </fieldset>
          <?php } ?>    
        </div>

        <div class="col-md-6 smallScreen_padzero">
        <label for="anniversary">Date of Anniversary:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two' id='datetimepicker4'>
                  {!! Form::text('anniversary',$newanniversary,array('id'=>'anniversary','readonly' => 'readonly','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
        </div>
		<div class="col-md-6 smallScreen_padzero">
			<label for="address_1">Address Line 1: <supscript class="text-red"> * </supscript></label>
            <fieldset class="form-group">
              {!! Form::text('address_1',$address_1,array('id'=>'','class'=>'form-control','placeholder' => 'Addess-1')) !!}
            </fieldset>
        </div>
        <div class="col-md-6 smallScreen_padzero">
			<label for="address_2">Address Line 2:</label>
            <fieldset class="form-group">
              {!! Form::text('address_2',$address_2,array('id'=>'','class'=>'form-control','placeholder' => 'Addess-2')) !!}
            </fieldset>
        </div>
        
        
		<div class="col-md-6 editTime smallScreen_padzero">
			<label for="state">State:<supscript class="text-red"> * </supscript></label>
			<fieldset class="form-group">
			<select id="stateIds" name="state" class="form-control" > 
				  <option value="">---State---</option>
				  <?php   ?>
				  @foreach($states as $state)
            @if(@$statename->state == @$state->state)
  				  <option  value="{{$state->state_code}}" selected> {{@$state->state}}</option>
            @else
             <option  value="{{$state->state_code}}"> {{@$state->state}}</option>
            @endif
				  @endforeach
			</select>
			  
			</fieldset>
		</div>
			<div class="col-md-6 editTime smallScreen_padzero">
				<label for="city">City:<supscript class="text-red"> * </supscript></label>
          <?php 
		          $citynameb = DB::table('dn_cities')->select('city','id')->where('state_code',@$statecode)->get();

            ?>
					
          <fieldset class="form-group">
			  <select id="cityIds" name="city" class="form-control"> 
				  <option value="">---City---</option>
		@if(!empty(@$citynameb))
           @foreach($citynameb as $citsy)
            @if($cityName->city == $citsy->city)
            <option  value="{{$citsy->id}}" selected> {{$citsy->city}}</option>
            @else
             <option  value="{{$citsy->id}}"> {{$citsy->city}}</option>
            @endif
          @endforeach
		  @endif
			  </select> 
					</fieldset>
			</div>
		  

        <div class="col-md-6 smallScreen_padzero">
        <label for="zip_code">Zip Code:<supscript class="text-red"> * </supscript></label>
            <fieldset class="form-group">
              {!! Form::text('zip_code',$zip_code,array('id'=>'','class'=>'form-control','placeholder' => 'Zip')) !!}
            </fieldset>
          </div>
           <div class="col-md-12 smallScreen_padzero">
            <div class="radio">
              <label for="gender">Gender:</label>
                  <label class="radio-inline">
                    <?php echo $gender; ?>
                </label>
            </div>
          </div>
          
          <div class="col-md-12 smallScreen_padzero">
            <div class="radio">
              <label for="cartransmission">Car Transmission Ability:<supscript class="text-red"> * </supscript></label>
                <?php if ($car_transmission=="automatic") { ?>

                 <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic" >Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" >Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" >Both
                 </label>

                <?php } elseif($car_transmission=="manual"){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic" >Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="manual" >Manual
                 </label> 

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" >Both
                 </label>

                <?php }elseif($car_transmission=="both"){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic" >Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" >Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="both" >Both
                 </label>

                <?php }elseif($car_transmission==""){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic" >Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" >Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" >Both
                 </label>

                <?php }  ?>
            </div>
          </div>
            
          <div class="col-md-12 uploadDocuments smallScreen_padzero">
            <h2 class='margin-left-0'>Verification</h2>
            <br/>
            
            <div class="row">
              <div class="col-md-6">
             <label for="license_verification">Driver License:<supscript class="text-red"> * </supscript></label>
               <a href="{!! asset($license_verification) !!}" download>Download</a>
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="licVer" readonly placeholder="Driver License" value="{!!  pathinfo($license_verification, PATHINFO_FILENAME) .'.'. pathinfo($license_verification, PATHINFO_EXTENSION)  !!}"  class="form-control input-lg inpt_upld DriverLicense">
                    <label for="license_verification" class="btn uploadBtn up_btn_tp">Upload</label>
                    <input type="file"  style=" height: 1px; margin: 0;padding: 0;" name="license_verification" id="license_verification" class="license_verification uploadFile" data-max-size="4000000" >
                  </div>
                </div>
                <!--//-->
                <p id="myBar"></p>
                <!--//-->
                
              </div>

              <div class="col-md-6">
                
              <label for="proof_of_insurance">Proof Of Insurance:<supscript class="text-red"> * </supscript></label>
			 
              <a href="{!! asset($proof_of_insurance) !!}" download>Download</a>
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="proofins" value="{!!  pathinfo($proof_of_insurance, PATHINFO_FILENAME) .'.'. pathinfo($proof_of_insurance, PATHINFO_EXTENSION)  !!}" readonly  class="form-control input-lg inpt_upld ProofOfInsurance" placeholder="Proof Of Insurance">
                    <label for="proof_of_insurance" class="btn uploadBtn up_btn_tp ">Upload</label>
                    <input type="file" style=" height: 1px; margin: 0;padding: 0;" id="proof_of_insurance" name="proof_of_insurance" class="proof_of_insurance uploadFile" data-max-size="4000000" >
                  </div>
                </div>
                
              </div>
            </div> 
          </div>
        
      <div class="submit-btn col-md-6">
            <input type="submit" value="Update" class="btn btn-primary UpdateDriverProfile editDriverBtn">
        <a href="{!! route('alldriver') !!}" class="btn btn-primary  cancel">Cancel</a>
      </div>
  </div>
  {!! Form::close() !!}
    </div>    
  </div>
			</div><!-- row CLOSE -->
     <!-- User History END -->
		<!--ALL TABLES END--->
	</section>

@stop
@stop
<style type="text/css">
.profile_dl label{text-align:left; width:110px;}
.border{border:1px solid #ff0000;}
.main_heading{width:100%; margin:0px; padding:0px;}
.main_heading h3{background:#238BCC; text-align:center; font-weight:normal; font-size:22px; color:#ffffff; padding:10px 0;}
hr {	
  border-top: 2px solid #3c8dbc !important;
}
.my-error-class{color:red;}
.profilePic {border-radius: 50%; width:140px !important; height:140px !important}
.cameraSymbol{margin-left:20px;font-size: 25px !important;}
*::after, *::before {
  box-sizing: border-box;
}

.user-profile {
    margin-left: -14px;
}

input.form-control.input-lg {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    box-shadow: none;
    font-size: 14px;
}
.form-control {
    background: #f2f1f1 none repeat scroll 0 0;
    border: 1px solid #ccc;
    border-radius: 0;
    font-weight: normal;
    height: 50px;
    padding: 1px 15px;
}
body div, a, button, input, img {
    outline: medium none;
}
.uploadBtn {
   
    border: 2px solid #95d60a !important;
    border-radius: 35px !important;
    color: #000;
    float: right;
   
    font-size: 15px;
    line-height: 2;
    margin-right: 4%; 
    padding: 0 25px;
    text-transform: capitalize;
    z-index: 9999;
}
.uploadBtn {
    background-color: #f2f1f1;
    margin-right: 10px !important;
    position: absolute;
    right: 0;
    top: 8px;
    z-index: 999 !important;
}

label {
    display: inline-block;
    font-weight: 700;
    margin-bottom: 5px;
    max-width: 100%;
}
* {
    box-sizing: border-box;
}
.input-group {
    border-collapse: separate;
}
.green-btn-s {
    background: #98db09 none repeat scroll 0 0 !important;
    border: 2px solid #fff !important;
    border-radius: 25px !important;
    color: #000 !important;
    font-family: andadabold_italic !important;
    font-size: 20px !important;
    margin-top: 25px !important;
    padding: 7px 30px !important;
}
.cam_mrg{ margin-left:0px !important}
.inpt_upld{ background-color:#fff !important; border:1px solid #ccc !important}
.up_btn_tp{ top:5px !important}
</style>
