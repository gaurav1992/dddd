@extends('frontend.common')   
@section('customjavascript')
<?php  $states = DB::table('dn_states')->get(); ?>
<script type="text/javascript">
var getcityurl= "{!! route('getcity') !!}";
var CSRF_TOKEN= "{!! csrf_token() !!}";
$(document).ready(function(){
  
  
$('.editTime,.cancel').hide();
$('.editDriverProfile').on('click',function(){
    $("#EditDriverProfile").html("EDIT DRIVER PROFILE");
      $('.showtime').hide();
    $('.editTime,.cancel').show();
    $('.uploadFile').removeAttr('disabled');
    $('#emailDriver').removeAttr('readonly');
		//$('#licence_exp').val('');
		//$('#insurance_exp').val('');

    
});
  
  
  
});

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
    <h3 class="page-heading" id="EditDriverProfile">DRIVER PROFILE</h3>
</div>
<!--  SECTION-1 -->
<?php 
    $id = Auth::id();
    if(Auth::check()) {
        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
       
        //print_r($user_detail);
        $joinind_date = $user_detail->created_at;
        $email = $user_detail->email;
        $contact_number = $user_detail->contact_number;
        $dob = $user_detail->dob;
        $anniversary = $user_detail->anniversary;
        $first_name = $user_detail->first_name;
        $last_name = $user_detail->last_name;
        $profile_pic = $user_detail->profile_pic;
        $is_driver_approved = $user_detail->is_driver_approved;
        $become_driver_request = $user_detail->become_driver_request;
        $contact_number = $user_detail->contact_number;

        $address_1=$user_detail->address_1;
        $address_2=$user_detail->address_2;
        $city=$user_detail->city;
        $state=$user_detail->state;
        $zip_code=$user_detail->zip_code;

        $gender = $user_detail->gender;
        

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
        
        $createjoinind_date = new DateTime($joinind_date);
        $newjoinind_date = $createjoinind_date->format('m/d/Y');

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

        $dn_users_data = DB::table('dn_users_data')->select('*')->where('user_id', $id)->first();

        
        if(!empty($dn_users_data)) {

          //print_r($dn_users_data);die;
            $license_number = $dn_users_data->license_number;
            
            if(!empty($dn_users_data->ssn)){
               //echo $dn_users_data->ssn;exit;
                
                 //echo $dispnum;exit;
                //$ssn = '#####'.substr($dn_users_data->ssn, -4);//substr($dispnum, 0, 2) . str_repeat("*", strlen($dispnum)-2);
                $ssn = $dn_users_data->ssn;
            }else{
               $ssn='';
            }
              
            $driver_profile_pic = $dn_users_data->driver_profile_pic;
        } else {
            $license_number = '';
            $driver_profile_pic = '';
            $ssn = ''; }
?>
<!--  SECTION-1 -->

<section>
  <div class="container mtop-30" id="registration-form">

  @include('frontend.driversidebar')
   
  <div class="col-md-8">
    @if(@$myData['is_suspended'] == 2)
      <p class="alert alert-info">Your documents are about to expire, kindly update them.</p>
    @elseif(@$myData['is_suspended'] == 1)
      <p class="alert alert-info">You have been suspended. Kindly review your documents.</p>
    @elseif(@$myData['is_suspended'] == 3)
      <p class="alert alert-info">Your documents are under review.</p>
    @endif 

    @if(Session::has('message'))
      <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif

    {!! Form::open(array('url' => 'editdriver','class' => 'form editdriver','files' => true,'id'=>'editdriver')) !!}
    <div class="col-sm-12 smallScreen_padzero">
      <div class="edit-user uploadProfilePic">
        <?php if($driver_profile_pic !=''){ ?>
            <img src="{!! $driver_profile_pic !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
        <?php }else{ ?>
            <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
        <?php } ?>   
		 <label for="profile_pic" class="btn img-upload"><i class="fa fa-camera" aria-hidden="true"></i></label>
        <input type="file" id="profile_pic" name="profile_pic" disabled class="uploadFile" data-max-size="1000000" accept="image/jpg,image/png,image/jpeg,image/gif">
        <p class="user-profile">Change profile picture</p> 
      </div>
    </div>
	
      <div class="col-sm-12 smallScreen_padzero">
          <h3 class="page-heading">Profile-information</h3>       
        <div class="col-md-6 smallScreen_padzero">
        <label for="first_name">First Name:</label>
          <fieldset class="form-group">
            {!! Form::text('first_name',$first_name,array('id'=>'','class'=>'form-control','placeholder' => 'First Name','disabled')) !!}
          </fieldset>
        </div>
        <div class="col-md-6 smallScreen_padzero">
        <label for="last_name">Last Name:</label>
          <fieldset class="form-group">
            {!! Form::text('last_name',$last_name,array('id'=>'','class'=>'form-control','placeholder' => 'Last Name','disabled')) !!}
          </fieldset>
        </div>
        
        <div class="col-md-12 smallScreen_padzero">
        <label for="email">Email:</label>
          <fieldset class="form-group">
            {!! Form::email('email',$email,array('id'=>'emailDriver','class'=>'form-control','placeholder' => 'Email','disabled')) !!}
          </fieldset>
        </div>

		<div class="col-md-12 smallScreen_padzero">
		<label for="license_number">Driver's License #:</label>
			<fieldset class="form-group">
		  {!! Form::text('license_number',$license_number,array('id'=>'','class'=>'form-control','placeholder' => 'Driver License #','disabled')) !!}
			</fieldset>
		</div>
          <div class="col-md-12 smallScreen_padzero">
          <label for="license_number">SSN/TIN:</label>
            <fieldset class="form-group">
              {!! Form::text('ssn',$ssn,array('id'=>'ssn-tin','class'=>'form-control','placeholder' => 'SSN/TIN','disabled')) !!}
            </fieldset>
          </div>
          <div class="col-md-12 smallScreen_padzero">
          <label for="contact_number">Contact Number:</label>
            <fieldset class="form-group">
              {!! Form::text('contact_number',$contact_number,array('id'=>'','class'=>'form-control','placeholder' => 'Contact Number','disabled')) !!}
            </fieldset>
          </div>
              <div class="col-md-6 smallScreen_padzero">
              <label for="licence_exp">License Expiration:</label>
               <fieldset class="form-group">
                  <div class='input-group date boder date-picker-one datePicker' id='datetimepicker5'>
                      {!! Form::text('licence_exp',$licence_expiration,array('id'=>'licence_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required','disabled')) !!}
                      <a class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </a>
                  </div>
                </fieldset>
            </div>

        <div class="col-md-6 smallScreen_padzero">
        <label for="insurance_exp">Insurance Expiration:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two datePicker' id='datetimepicker6'>
                  {!! Form::text('insurance_exp',$insurance_expiration,array('id'=>'insurance_exp','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required','disabled')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
        </div>
        <div class="col-md-6 smallScreen_padzero">
          <label for="dob">Date of birth:</label>
          <?php if($newDob ==''){ ?>
             <fieldset class="form-group">
                <div class='input-group date boder date-picker-three datePicker' id='datetimepicker1'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <!--a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a-->
                </div>
              </fieldset>
          <?php } else{ ?>
              <fieldset class="form-group">
                <div class='input-group date boder date-picker-three'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'DD/MM/YYYY','readonly' => 'readonly', 'required' => 'true')) !!}
                    <!--a class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </a-->
                </div>
              </fieldset>
          <?php } ?>    
          </div>

        <div class="col-md-6 smallScreen_padzero">
        <label for="anniversary">Date of Anniversary:</label>
           <fieldset class="form-group">
              <div class='input-group date boder date-picker-two' id='datetimepicker4'>
                  {!! Form::text('anniversary',$newanniversary,array('id'=>'anniversary','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required','disabled')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
        </div>
      <div class="col-md-6 smallScreen_padzero">
      <label for="address_1">Address Line 1:</label>
            <fieldset class="form-group">
              {!! Form::text('address_1',$address_1,array('id'=>'','class'=>'form-control','placeholder' => 'Addess-1','disabled')) !!}
            </fieldset>
          </div>
          <div class="col-md-6 smallScreen_padzero">
          <label for="address_2">Address Line 2:</label>
            <fieldset class="form-group">
              {!! Form::text('address_2',$address_2,array('id'=>'','class'=>'form-control','placeholder' => 'Addess-2','disabled')) !!}
            </fieldset>
          </div>
        
          <div class="col-md-6 showtime smallScreen_padzero">
          <label for="state">State:</label>
            <fieldset class="form-group">
        <?php $statename = DB::table('dn_states')->select('state')->where('state_code',@$state)->first(); $statecode=$state; ?>
              {!! Form::text('state',@$statename->state,array('id'=>'','class'=>'form-control','placeholder' => 'State','disabled')) !!}
            </fieldset>
          </div>
      <div class="col-md-6 showtime smallScreen_padzero">
        <label for="city">City:</label>
            <fieldset class="form-group">
      <?php $cityName = DB::table('dn_cities')->select('city')->where('id',@$city)->first();  ?>
              {!! Form::text('city',@$cityName->city,array('id'=>'','class'=>'form-control','placeholder' => 'City','disabled')) !!}
            </fieldset>
          </div>
       <div class="col-md-6 editTime smallScreen_padzero">
      <label for="state">State:</label>
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
        <label for="city">City:</label>
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
        <label for="zip_code">Zip Code:</label>
            <fieldset class="form-group">
              {!! Form::text('zip_code',$zip_code,array('id'=>'','class'=>'form-control','placeholder' => 'Zip','disabled')) !!}
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
              <label for="cartransmission">Car Transmission Ability:</label>
                <?php if ($car_transmission=="automatic") { ?>

                 <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic" disabled>Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" disabled>Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" disabled>Both
                 </label>

                <?php } elseif($car_transmission=="manual"){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic" disabled>Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="manual" disabled>Manual
                 </label> 

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" disabled>Both
                 </label>

                <?php }elseif($car_transmission=="both"){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="automatic" disabled>Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" disabled>Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="both" disabled>Both
                 </label>

                <?php }elseif($car_transmission==""){ ?>

                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" checked value="automatic" disabled>Automatic
                  </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="manual" disabled>Manual
                 </label>
                  <label class="radio-inline">
                      <input type="radio" name="car_transmission" value="both" disabled>Both
                 </label>

                <?php }  ?>
            </div>
          </div>
            
          <div class="col-md-12 uploadDocuments smallScreen_padzero">
            <h2 class='margin-left-0'>Verification</h2>
            <br/>
            <br/>
            <div class="row">
              <div class="col-md-6">
             <label for="license_verification">Driver License:</label>
               <a href="{!! asset($license_verification) !!}" download>Download</a>
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="licVer" readonly placeholder="Driver License" disabled class="form-control input-lg DriverLicense">
                    <label for="license_verification" class="btn uploadBtn">Upload</label>
                    <input type="file" disabled style="visibility:hidden; height: 1px; margin: 0;padding: 0;" name="license_verification" id="license_verification" class="license_verification uploadFile" data-max-size="4000000" disabled>
                  </div>
                </div>
                <!--//-->
                <p id="myBar"></p>
                <!--//-->
                
              </div>

              <div class="col-md-6">
                
              <label for="proof_of_insurance">Proof Of Insurance:</label>
              <a href="{!! asset($proof_of_insurance) !!}" download>Download</a>
                <div class="form-group browsefile">
                  <div class="input-group col-xs-12">
                    <input type="text" name="proofins" value="{!!  pathinfo($proof_of_insurance, PATHINFO_FILENAME) .'.'. pathinfo($proof_of_insurance, PATHINFO_EXTENSION)  !!}" readonly class="form-control input-lg ProofOfInsurance" placeholder="Proof Of Insurance">
                    <label for="proof_of_insurance" class="btn uploadBtn">Upload</label>
                    <input type="file" style="visibility:hidden; height: 1px; margin: 0;padding: 0;" id="proof_of_insurance" name="proof_of_insurance" class="proof_of_insurance uploadFile" data-max-size="4000000" disabled>
                  </div>
                </div>
                
              </div>
            </div>
          </div>
          <br/>
          <br/>     
      <div class="submit-btn">
              <div class="lodingDiv" style="display:none;margin-top: 8%;margin-left: 15%;">
                <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
              </div>
            <input type="submit" value="Edit" class="btn btn-primary green-btn-s editDriverProfile editDriverBtn">
            <input type="submit" value="Update" class="btn btn-primary green-btn-s UpdateDriverProfile editDriverBtn" style="display:none;">
        <a href="{!! asset('/editdriver') !!}" class="btn btn-primary green-btn-s cancel">Cancel</a>
      </div>
  </div>
  {!! Form::close() !!}
    </div>    
  </div>
   <div class="col-md-2">
   </div>
</div>
<?php }else{ ?>
  <div class="container mtop-30" id="driverprofileedit">
    <div class="col-md-6 col-sm-offset-3"> 
      <p class="alert {{ Session::get('alert-class', 'alert-info') }}">Login First To Edit Your Profile !!</p><br/>
    </div>
</div>
<?php } ?>
</section>
@endsection
