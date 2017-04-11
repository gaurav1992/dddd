@extends('frontend.apppages')
@section('customjavascript')
<?php  $states = DB::table('dn_states')->get(); ?>
<script type="text/javascript">
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

         }
      });
    });

</script>
@stop
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">PROFILE</h3>
</div>
<!--  SECTION-1 -->
<?php 
  if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
  $profile_pic = $myData['profile_pic'];

  if($myData['licence_expirationDb'] !=''){
    $liceexp = new DateTime($myData['licence_expirationDb']);
    $licence_expiration = $liceexp->format('m/d/Y');
  }else{
    $licence_expiration ='';
  }
  
  if($myData['insurance_expirationdb'] !=''){
    $insuexp = new DateTime($myData['insurance_expirationdb']);
    $insurance_expiration = $insuexp->format('m/d/Y');
  }else{
    $insurance_expiration = '';
  }
?>

<section>
  <div class="container mtop-30" id="registration-form">
    <div class="col-md-2">
    </div>
   
  <div class="col-md-8">
    <p class="driverRequest" style="display:none;"><?php echo $myData['become_driver_request']; ?></p>
    <?php if($myData['become_driver_request'] == '1' && $myData['is_driver_approved'] =='0'){ ?>
          <div class="confmsg">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">
              Your form is pending for approval
            </p>
          </div>  
    <?php }elseif ($myData['become_driver_request'] == '1' && $myData['is_driver_approved'] =='1') { ?>
          <div class="confmsg">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">
              Your application has been approved
            </p>
          </div> 
    <?php }elseif ($myData['become_driver_request'] == '1' && $myData['is_driver_approved'] =='2'){ ?>
          <div class="confmsg">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">
              Your application has been rejected
            </p>
          </div>
    <?php } ?> 

    @if(Session::has('message'))
      <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
 <?php if($myData['is_driver_approved'] !='1' && $myData['become_driver_request'] !='1'){ ?>    
    {!! Form::open(array('url' => 'driver','class' => 'form becomedriver','files' => true,'id'=>'BecomeDriver')) !!}
        <div class="col-sm-12">
          <div class="edit-user uploadProfilePic">
            <?php if( $myData['driver_profile_pic'] !='' ){ ?>
                <img src="{!! asset($myData['driver_profile_pic']) !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
            <?php }else{ ?>
                <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
            <?php } ?>
            <label for="profile_pic" class="btn img-upload"><i class="fa fa-camera" aria-hidden="true"></i></label>
            <input type="file" style="visibility:hidden;" id="profile_pic" name="profile_pic" class="uploadFile" data-max-size="4000000" accept="image/jpg,image/png,image/jpeg,image/gif">
            <p class="user-profile">Change profile picture</p>        
          </div>
        </div>
        <div class="col-sm-12">
          <h3 class="page-heading">Profile-information</h3>       
          <div class="col-md-6">
          <label for="first_name">First Name:</label>
            <fieldset class="form-group">
              {!! Form::text('first_name',$myData['first_name'],array('id'=>'','class'=>'form-control','placeholder' => 'First Name','readonly' => 'readonly')) !!}
            </fieldset>
          </div>
          <div class="col-md-6">
          <label for="last_name">Last Name:</label>
            <fieldset class="form-group">
              {!! Form::text('last_name',$myData['last_name'],array('id'=>'','class'=>'form-control','placeholder' => 'Last Name','readonly' => 'readonly')) !!}
            </fieldset>
          </div>
        
          <div class="col-md-12">
          <label for="email">Email:</label>
            <fieldset class="form-group">
              {!! Form::email('email',$myData['email'],array('id'=>'','class'=>'form-control','placeholder' => 'Email','readonly' => 'readonly')) !!}
            </fieldset>
          </div>
        <div class="col-md-12">
        <label for="license_number">Driver's License #</label>
          <fieldset class="form-group">
            {!! Form::text('license_number',$myData['license_number'],array('id'=>'','class'=>'form-control','placeholder' => 'License Number')) !!}
          </fieldset>
        </div>
        <div class="col-md-12">
        <label for="contact_number">Contact Number:</label>
          <fieldset class="form-group">
            {!! Form::text('contact_number',$myData['contact_number'],array('id'=>'','class'=>'form-control','placeholder' => 'Contact Number','readonly' => 'readonly')) !!}
          </fieldset>
        </div>
        <div class="col-md-6">
        <label for="licence_exp">License Expiration:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-one datePicker' id='datetimepicker5'>
                  {!! Form::text('licence_exp',$licence_expiration,array('id'=>'licence_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar" ></span>
                  </a>
              </div>
            </fieldset>
        </div>
        <div class="col-md-6">
        <label for="insurance_exp">Insurance Expiration:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two datePicker' id='datetimepicker6'>
                  {!! Form::text('insurance_exp',$insurance_expiration,array('id'=>'insurance_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar" ></span>
                  </a>
              </div>
            </fieldset>
        </div>
        <div class="col-md-6">
          <label for="dob">Date of birth:</label>
          <?php if($myData['dob']==''){ ?>
             <fieldset class="form-group">
                <div class='input-group date boder date-picker-three datePicker' id='datetimepicker3'>
                    {!! Form::text('dob',$myData['dob'],array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a>
                </div>
              </fieldset>
          <?php } else{ ?>
              <fieldset class="form-group">
                <div class='input-group date boder date-picker-three'>
                    {!! Form::text('dob',$myData['dob'],array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a>
                </div>
              </fieldset>
          <?php } ?>    
          </div>

        <div class="col-md-6">
        <label for="anniversary">Date of Anniversary:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two' id='datetimepicker4'>
                  {!! Form::text('anniversary',$myData['anniversary'],array('id'=>'anniversary','class'=>'form-control','placeholder' => 'DD/MM/YYYY')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
        </div>
        <div class="col-md-6">
         <label for="address_1">Address Line 1:</label>
          <fieldset class="form-group">
            {!! Form::text('address_1',$myData['address_1'],array('id'=>'','class'=>'form-control','placeholder' => 'Addess-1')) !!}
          </fieldset>
        </div>
        <div class="col-md-6">
        <label for="address_2">Address Line 2:</label>
          <fieldset class="form-group">
            {!! Form::text('address_2',$myData['address_2'],array('id'=>'','class'=>'form-control','placeholder' => 'Addess-2')) !!}
          </fieldset>
        </div>
    <div class="col-md-6">
    <label for="state">State:</label>
    <fieldset class="form-group">
    <select id="stateIds" name="state" class="form-control"> 
          <option value="">---State---</option>
          @foreach($states as $state)
          <option  value="{{$state->state_code}}"> {{$state->state}}</option>
          @endforeach
    </select>
      
    </fieldset>
    </div>
     <div class="col-md-6">
        <label for="city">City:</label>
            <fieldset class="form-group">
     <select id="cityIds" name="city" class="form-control"> 
          <option value="">---City---</option>
     </select> 
            </fieldset>
     </div>
        <div class="col-md-6">
        <label for="zip_code">Zip Code:</label>
          <fieldset class="form-group">
            {!! Form::text('zip_code',$myData['zip_code'],array('id'=>'','class'=>'form-control','placeholder' => 'Zip')) !!}
          </fieldset>
        </div>
          <div class="col-md-12">
            <div class="radio">
              <label for="gender">Gender:</label>
                <?php if ($myData['gender']=="male") { ?>
                  <label class="radio-inline">
                    <input type="radio" name="gender" checked value="male">Male
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="gender" value="female">Female
                  </label> 
                <?php } elseif($myData['gender']=="female"){ ?>
                  <label class="radio-inline">
                    <input type="radio" name="gender" value="male">Male
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="gender" checked value="female">Female
                  </label>  
                <?php }elseif($myData['gender']==""){ ?>
                  <label class="radio-inline">
                    <input type="radio" name="gender" checked value="male">Male
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="gender" value="female">Female
                  </label>
                <?php } ?>
            </div>
          </div>
          <div class="col-md-12">
            <div class="radio">
              <label for="cartransmission">Car Transmission Ability:</label>
                <?php if ($myData['car_transmission']=="automatic") { ?>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic">Automatic
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual">Manual
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both">Both
                    </label>
                <?php } elseif($myData['car_transmission']=="manual"){ ?>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic">Automatic
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="manual">Manual
                    </label> 
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both">Both
                    </label>
                <?php }elseif($myData['car_transmission']=="both"){ ?>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic">Automatic
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual">Manual
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="both">Both
                    </label>
                <?php }elseif($myData['car_transmission']==""){ ?>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic">Automatic
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual">Manual
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both">Both
                    </label>
                <?php }  ?>
            </div>
          </div>
          <div class="col-md-12">
            <h2 class='margin-left-0'>Verification</h2><br/><br/>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="licVer" readonly="readonly"  class="form-control input-lg DriverLicense hiddenInput" value="" placeholder="Driver License">
                    <label for="license_verification" class="btn uploadBtn">Upload</label>
                    <input type="file" style="visibility:hidden;" name="license_verification" id="license_verification" class="license_verification uploadFile" data-max-size="4000000">
                  </div>
                </div>
                <!--//-->
                <p id="myBar"></p>
                <!--//-->
              </div>
              <div class="col-md-6">
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="proofins" readonly="readonly" class="form-control input-lg ProofOfInsurance hiddenInput" placeholder="Proof Of Insurance">
                    <label for="proof_of_insurance" class="btn uploadBtn">Upload</label>
                    <input type="file" style="visibility:hidden;" id="proof_of_insurance" name="proof_of_insurance" class="proof_of_insurance uploadFile" data-max-size="4000000">
                  </div>
                </div>
              </div>
            </div>
          </div><br/><br/>
          
          
        <h2> Your Records</h2>
        <?php
          $count = '0';
          $quest = '1';
          foreach ($myData['driver_records'] as $value) {
          $question = $value->question;
          $answer = $value->answer;
          if (is_numeric($answer)){
        ?>
              <div class="col-md-12 driver_question_answer">
                <h4 class="txt-grn"> Q<?php echo $quest; ?>. <span class="radioquestion"><?php echo $question; ?></span></h4>
                <div class="radio">
                  <label class="radio-inline">
                    <input type="radio" name="driver_records_<?php echo $count; ?>" <?php if ($answer=="1") echo "checked";?> value="1">Yes
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="driver_records_<?php echo $count; ?>" <?php if ($answer=="0") echo "checked";?> value="0">No
                  </label>
                </div>
              </div>
      <?php }else{ ?>
              <div class="col-md-12 clearfix driver_question_answertext">
                <h4 class="txt-grn">Q10. <span class="textquestion"><?php echo $question; ?></span></h4>
                <div class="input-group">
                    {!! Form::textarea('driver_records_9',$answer,array('id'=>'','rows' => '10', 'cols' => '100', 'class'=>'form-control','placeholder' => 'Describe Here')) !!}
                </div>
        <?php } $count++;$quest++; }  ?>
        <div class="col-md-12">
          <div class="checkbox" id="termCondition">
              <!-- <input type="checkbox" value="1" name="terms"> -->
              <input type="checkbox" id="termsConditions" name="termsConditions"/>
              <a target="_blank" href="{!! asset('public/driver-terms-service-agreement.pdf') !!}">Agree to terms and conditions</a>
          </div>
        </div>     
      <div class="submit-btn">
        <div class="lodingDiv" style="display:none;margin-top: 8%;margin-left: 15%;">
          <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
        </div>
        {!! Form::submit('UPDATE', array('class'=>'btn btn-primary green-btn-s becomedriveredit')) !!}
        <a href="" class="btn btn-primary green-btn-s">CANCEL</a>
      </div>
  </div>
  {!! Form::hidden('userId',$myData['id']) !!}
     {!! Form::close() !!}
 <?php } ?> 
      </div>
    </div>
</div>
</section>
<?php }else{ ?>
<div class="container mtop-30" id="driverprofileedit">
    <div class="col-md-6 col-sm-offset-3"> 
      <p class="alert {{ Session::get('alert-class', 'alert-info') }}">Login First To Edit Your Profile !!</p><br/>
    </div>
</div>
<?php } ?>
<script>

</script>
@endsection