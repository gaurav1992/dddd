@extends('frontend.common')

@section('resendotp')
  {!! HTML::script('public/js/framework/resendotp.js'); !!}
@endsection

@section('phonecodecss')
  {!! HTML::style('public/css/libs/prism.css') !!}
  {!! HTML::style('public/css/libs/intlTelInput.css') !!}
  {!! HTML::style('public/css/libs/demo.css') !!}
  {!! HTML::style('public/css/libs/isValidNumber.css') !!}
@endsection

@section('phonecodejs')
  {!! HTML::script('public/js/framework/prism.js'); !!}
  {!! HTML::script('public/js/framework/intlTelInput.js'); !!}
  {!! HTML::script('public/js/framework/isValidNumber.js'); !!}
@endsection

@section('content')

<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
  <h3 class="page-heading">PHONE VERIFICATION</h3>
</div>
<!--  SECTION-1 -->
<!--  SECTION-1 -->
<section>
  <div class="container phone-verification" id="forgot-pass">   
        <div class="row">
            <div class="center col-sm-5 top-space">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                          @if(Session::has('registerConfirm'))
                              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('registerConfirm') }}</p>
                          @endif
                          <p>Enter your phone number for verification</p>
                            <div class="panel-body">                              
                              {!! Form::open(array('url' => 'phoneverification','class' => 'form','id'=>'PhoneVerification')) !!}
                                <fieldset>
                                  <?php
                                    $phone_code = Session::get('phone_code');
                                    $phone_num = Session::get('phone_num');
                                  ?>
                                  <div class="form-group">
                                    <div id="result">
                                        <input id="phone" value="<?php echo $phone_num ?>" type="text" name="phone_number" placeholder="Mobile Number" class="form-control span6">
                                    </div>
                                  </div>
                                  <div class="form-group">
                                    <input id="phonecode" name="phonecode" type="hidden" value="+1">
                                    <!--data hidden for resend verification code-->
                                      <input type="hidden" value="<?php echo $phone_code; ?>" id="phone_code_resend">
                                      <input type="hidden" value="<?php echo $phone_num; ?>" id="phone_num_resend">
                                    <!--//data hidden for resend verification code-->

                                    {!! Form::hidden('phoneverification','1',array('id'=>'','class'=>'form-control')) !!}
                                    <?php if($phone_num !=''){ ?>
                                      {!! Form::submit('Re-Send verification code', array('class'=>'btn btn-lg btn-primary btn-block GRN phoneverified')) !!}
                                    <?php }else{ ?>
                                      {!! Form::submit('Send verification code', array('class'=>'btn btn-lg btn-primary btn-block GRN phoneverified')) !!}
                                    <?php } ?>
                                  </div>
                                </fieldset>
                              {!! Form::close() !!} 
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                @endif            
                              <hr/>               
                              {!! Form::open(array('url' => 'phoneverification','class' => 'form code','id'=>'PhoneCodeConfirm')) !!}
                                <fieldset>
                                  <div class="form-group">
                                    <div class="verification_code">
                                      <p>Enter verification code</p>
                                      {!! Form::text('opt','',array('id'=>'','class'=>'form-control')) !!}
                                    </div>
                                  </div>
                                  <div class="form-group">
                                    {!! Form::submit('Confirm', array('class'=>'btn btn-lg btn-primary btn-block')) !!}
                                  </div>
                                </fieldset>
                             {!! Form::close() !!}
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    



    
  </div>
</section>
@endsection