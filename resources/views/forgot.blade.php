@extends('frontend.common')
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">FORGOT PASSWORD</h3>
</div>
<!--  SECTION-1 -->
<section>
  <div class="container" id="forgot-pass">  
      <div class="row">
          <div class="center col-sm-5 top-space">
              <div class="panel panel-default">
                  <div class="panel-body">
                      <div class="text-center">
                        <p>Enter your email for verification</p>
                          @if(Session::has('verify'))
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('verify') }}</p>
                          @endif
                          @if(Session::has('message'))
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                          @endif
                          <div class="panel-body">
                            {!! Form::open(array('url' => 'forgot','class' => 'form','id'=>'EmailVerification')) !!}
                              <fieldset>
                                 <div class="form-group">
                                      <div class="icon-addon addon-lg">
                                          {!! Form::email('email','',array('id'=>'email','class'=>'form-control','placeholder' => 'Email')) !!}
                                          <label for="email" class="glyphicon glyphicon-envelope" rel="tooltip" title="email"></label>
                                      </div>
                                  </div>
                                <div class="form-group">
                                  {!! Form::hidden('forgotpassword','0',array('id'=>'','class'=>'form-control blank')) !!}
                                  {!! Form::submit('Send verification code', array('class'=>'btn btn-lg btn-primary btn-block text-green')) !!}
                                </div>
                              </fieldset>
                            {!! Form::close() !!}           
                            <hr/>
                              {!! Form::open(array('url' => 'forgot','class' => 'form code','id'=>'EmailVerificationCode')) !!}
                                <fieldset>
                                  <div class="form-group">
                                    <div class="verification_code">
                                      <p>Enter verification code</p>
                                      {!! Form::text('password_token','',array('id'=>'','class'=>'form-control')) !!}
                                    </div>
                                    <div id="errorscode"></div>
                                  </div>
                                  <div class="form-group">
                                    {!! Form::hidden('forgotpassword','1',array('id'=>'','class'=>'form-control blank')) !!}
                                    {!! Form::submit('Confirm', array('class'=>'btn btn-lg btn-primary btn-block text-green')) !!}
                                  </div>
                                </fieldset>
                              {!! Form::close() !!}
                              @if(Session::has('error'))
                                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('error') }}</p>
                              @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    



    
  </div>
</section>
@endsection