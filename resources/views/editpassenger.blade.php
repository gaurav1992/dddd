@extends('frontend.common')
@section('customjavascript')

  <script type="text/javascript">
    var editPhone = "{!! route('editPhone') !!}";
    var confirmOTP = "{!! route('confirmOTP') !!}";
    var getcityurl= "{!! route('getcity') !!}";
    var CSRF_TOKEN= "{!! csrf_token() !!}";
  $(document).ready(function(){
    
    $(".cancel,.user-profile,.edicontactNumber").hide();
    $(".EDITBtnPassenger1").click(function(){
       $("#profileEdit").html("EDIT PROFILE");
      $(".cancel,.user-profile,.edicontactNumber").show();
       $('input[type=email]').removeAttr('readonly');
       $('input[name=first_name],input[name=last_name]').removeAttr('readonly');
       $('#payoutState').removeAttr('disabled');
       $('#payoutCity').removeAttr('disabled');
       $('#datepickericon').show();
       $('#anniversary').removeAttr('disabled');
    });
  });


  $( document ).on( "change", "#payoutState", function(){
      
      var stateCode= $(this).val();
      console.log(stateCode);
      $.ajax({
         type:'post',
         url:getcityurl,
         data:'stateCode='+stateCode+'&_token='+CSRF_TOKEN,
         beforeSend: function( xhr ){
          $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
        },
         success:function(returnData)
         {
          $('#divLoading').remove();
           console.log(returnData);
          // var parsedJson=$.parseJSON(returnData);
          $( "#payoutCity" ).html(returnData);

         }
      });
    });

  </script>

@stop

@section('resendotp')
  {!! HTML::script('public/js/framework/editPhone.js'); !!}
@endsection

@section('phonecodecss')
  {!! HTML::style('public/css/libs/prism.css') !!}
  {!! HTML::style('public/css/libs/intlTelInput.css') !!}
  {!! HTML::style('public/css/libs/demo.css') !!}
  {!! HTML::style('public/css/libs/isValidNumber.css') !!}
@endsection

@section('phonecodejs')
  {!! HTML::script('public/js/framework/prism.js'); !!}
 
@endsection

@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading" id="profileEdit">PROFILE</h3>
</div>
<?php $becomedriver = Session::get('becomedriver');  ?>
<!--  SECTION-1 -->
<?php 
    $id = Auth::id();
    if(Auth::check()) {
        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
       // print_r($user_detail);exit;
        $joinind_date = $user_detail->created_at;
        $email = $user_detail->email;
        $country_phone_code = $user_detail->country_phone_code;
        $contact_number = $user_detail->contact_number;
        $dob = $user_detail->dob;
        $anniversary = $user_detail->anniversary;
        $first_name = $user_detail->first_name;
        $last_name = $user_detail->last_name;
        $profile_pic = $user_detail->profile_pic;
        

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


?>

<section>
  <div class="container mtop-30" id="driverprofileedit">
    @include('frontend.passengersidebar')
   
    <div class="col-md-8">
      @if(Session::has('updateProfile'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('updateProfile') }}</p>
      @endif
      
      @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
      @endif
      @if(Session::has('confirmUpdateContact'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('confirmUpdateContact') }}</p>
    @endif 
      <!-- <div class="right-sec">
        <a class="btn-grn edit pull-left back" href="{!! asset('passenger/profile') !!}"><i class="fa fa-angle-left" aria-hidden="true"></i></a><span>EDIT PROFILE</span>
      </div> -->

      {!! Form::open(array('url' => 'editpassenger','class' => 'form editpassenger','files' => true,'id'=>'EditPassanger')) !!}
        <input type="hidden" placeholder="becomedriver" class="form-control"  name="becomeD" value="<?php echo @$becomedriver; ?>">
        <div class="col-sm-12 padzero">
          <div class="edit-user uploadProfilePic">
              <?php if($profile_pic !=''){ ?>
                  <img src="{!! asset($profile_pic) !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
              <?php }else{ ?>
                  <img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail passengerprofile profilePic" alt="#"/>
              <?php } ?>
            <label for="profile_pic" class="btn img-upload profilePicIcon" style="display:none;"><i class="fa fa-camera" aria-hidden="true"></i></label>
            <input type="file" id="profile_pic" name="profile_pic" class="uploadFile" data-max-size="1000000" accept="image/jpg,image/png,image/jpeg,image/gif">
            <p class="user-profile">Change profile picture</p>      
          </div>
        </div>
        <div class="col-sm-12 padzero">
          <div class="col-md-6 smallScreen_padzero">
            <label for="first_name">First Name:</label>
            <fieldset class="form-group">
            <?php if ($first_name == ''){ ?>
              {!! Form::text('first_name',$first_name,array('id'=>'','class'=>'form-control','placeholder' => 'First Name','disabled')) !!}
            <?php }else{ ?>
              {!! Form::text('first_name',$first_name,array('id'=>'','class'=>'form-control','placeholder' => 'First Name','disabled')) !!}
            <?php } ?>
            </fieldset>
          </div>
          <div class="col-md-6 smallScreen_padzero">
            <label for="last_name">Last Name:</label>
            <fieldset class="form-group">
            <?php if ($first_name == ''){ ?>
              {!! Form::text('last_name',$last_name,array('id'=>'','class'=>'form-control','placeholder' => 'Last Name','disabled')) !!}
            <?php }else{ ?>
               {!! Form::text('last_name',$last_name,array('id'=>'','class'=>'form-control','placeholder' => 'Last Name','disabled')) !!}
            <?php } ?> 
            </fieldset>
          </div>
          <div class="col-md-12 smallScreen_padzero">
            <label for="email">Email:</label>
            <fieldset class="form-group">
              {!! Form::email('email',$email,array('id'=>'','class'=>'form-control','placeholder' => 'Email','disabled')) !!}
            </fieldset>
          </div>
          <div class="col-md-6 smallScreen_padzero">
          <label for="dob">Date of birth:</label>
          <?php if($newDob ==''){ ?>
             <fieldset class="form-group">
                <div class='input-group date boder date-picker-three datePicker' id='datetimepicker1'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required' => 'true','disabled', 'readonly')) !!}
                    
                </div>
              </fieldset>
          <?php } else{ ?>
              <fieldset class="form-group">
                <div class='input-group date boder date-picker-three'>
                    {!! Form::text('dob',$newDob,array('id'=>'dob','class'=>'form-control','placeholder' => 'MM/DD/YYYY','required' => 'true','disabled','readonly')) !!}
                    
                </div>
              </fieldset>
          <?php } ?>    
          </div>
          <div class="col-md-6 smallScreen_padzero">
          <label for="anniversary">Date of Anniversary:</label>
            <fieldset class="form-group">
              <div class='input-group date boder date-picker-four' id='datetimepicker2'> 
                  {!! Form::text('anniversary',$newanniversary,array('id'=>'anniversary','class'=>'form-control','placeholder' => 'MM/DD/YYYY','disabled')) !!}
                  <a class="input-group-addon" id="datepickericon" style="display:none;">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
              </div>
            </fieldset>
          </div>
            <div class="col-md-6 smallScreen_padzero">
            <label for="pwd" class="l_blck">State:</label>
             <fieldset class="form-group">
            <select class="stateIds form-control" id="payoutState" name="payoutState" disabled required> 
              <option value="">---State---</option>
               
              <?php $statename = DB::table('dn_states')->select('state','state_code')->where('state_code',@$user_detail->state)->first();  ?>
            <?php //print_r($statename);exit;?>
              @foreach(@$myData['states'] as $states)
                @if(@$statename->state == $states->state)
                <option  value="{{@$states->state_code}}" selected> {{@$states->state}}</option>
               @else
                <option  value="{{@$states->state_code}}"> {{@$states->state}}</option>
               @endif
              @endforeach
            </select>
            </fieldset>
        </div>
          <div class="col-md-6 smallScreen_padzero">
            <label for="pwd" class="l_blck">City</label>
             <fieldset class="form-group">
              
                <?php @$cityName = DB::table('dn_cities')->select('city')->where('id',@$user_detail->city)->first();  ?>
            
            <select  name="payoutCity" id="payoutCity" class="cityClass cityTag form-control" disabled required> 
              <option value="{{@$user_detail->city}}">{{@$cityName->city}}</option>
            </select>
            </fieldset>
          </div>
           <div class="col-md-6 smallScreen_padzero">
              <label for="anniversary">Phone Number:</label>
            </div>
            <div class="add-car-cls col-sm-12 EditPhoneNumberDiv smallScreen_padzero">
                <div class="left-cls-div">
                  <div class="col-xs-10 col-sm-6 text-center add-contact-detail ph_wght1" style="text-align:left; height:50px;padding-top:10px;">
                      <?php echo trim($country_phone_code.$contact_number); ?>
                  </div>   
                  <div class="col-xs-2 col-sm-6 padleftzero text-center add-contact-detail ph_wght1">
                      <a class="editPhone edicontactNumber flt_rgt" phoneAction="editPhone" id="editPhone" href="" data-toggle="modal" data-target="#EditPhoneNumber" ><i class="fa fa-edit" style="margin-top:10px;"></i></a>
                  </div>
                </div>
            </div>
          <div class="submit-btn">
              <div class="lodingDiv" style="display:none;margin-top: 8%;margin-left: 15%;">
                <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
              </div>
              <input type="submit" value="Edit" class="btn btn-primary green-btn-s EDITBtnPassenger1 updatepassengerprofilebtn">
              <input type="submit" value="Update" class="btn btn-primary green-btn-s EDITBtnPassenger2 updatepassengerprofilebtn" style="display:none;">
            <a href="{!! asset('/editpassenger') !!}" class="btn btn-primary cancel green-btn-s">Cancel</a>
          </div>
        </div>
        {!! Form::close() !!}



      </div>    
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


<!--phone number detail code start-->
<input type="hidden" id="country_phone_code" value="<?php echo $country_phone_code; ?>">
<input type="hidden" id="contact_number" value="<?php echo $contact_number; ?>">
<!--//phone number detail code end-->

<!-- Modal -->
<div id="EditPhoneNumber" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
    
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body EditPhoneNumber">
    <h2 class="mt-0"> Edit Phone Details </h2>
      <p class="alert alert-class" id="verificationMessage"></p>
        {!! Form::open(array('url' => 'editPhone','class' => 'form','id'=>'editPhoneNumber')) !!}
     <fieldset>
              <div class="panel-body" style="background:transparent; padding:0 0 0 0;" >
                
                    <div class="form-group">
                      <div id="result">
                          <input id="phone" type="text" name="phone_number" placeholder="Mobile Number" class="form-control span6 UpdateContact" value="<?php echo $contact_number; ?>" required>
                      </div>
                    </div>
                    <div class="form-group">
                      <input id="phonecode" name="phonecode" type="hidden" value="<?php echo $country_phone_code; ?>">
                      <input id="UerID" name="user_id" type="hidden" value="<?php echo $id; ?>">
                    </div>
                 
              </div>
            <div class="lodingDiv" style="display:none;">
              <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
            </div>
          <button class="btn btn-primary green-btn-s updatePhone" style="margin-top:0px !important;" type="submit">Send verification code</button> 
        </fieldset>     
        {!! Form::close() !!} 
          @if(Session::has('message'))
              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
          @endif            
          <hr/>
          <p class="alert alert-class" id="otpConfirmMessage"></p>   
              
          {!! Form::open(array('url' => 'confirmOTP','class' => 'form code','id'=>'confirmOTP')) !!}
            <fieldset>
              <div class="form-group">
                <div class="col-xs-12 padzero editPhone">
                  <p>Enter verification code</p>
                  {!! Form::text('opt','',array('id'=>'','class'=>'form-control')) !!}
                </div>
              </div>
              <div class="lodingDivconfirm" style="display:none;text-align:center;">
                <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg"  style="display:inline-block;">
              </div>
              <div class="form-group">
         <div class="input-group">
                {!! Form::submit('Confirm', array('class'=>'btn btn-primary green-btn-s btn-block confirmOTP')) !!}
               </div>
              </div>
            </fieldset>
          {!! Form::close() !!} 

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection