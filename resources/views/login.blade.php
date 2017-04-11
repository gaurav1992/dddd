@extends('frontend.common')
@section('content')
<style type="text/css">
.errors {
    text-align: left;
    color: red;
    padding-left: 0px;
    margin-left: 0px;
}
</style>
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">LOG IN</h3>
</div>
<!--  SECTION-1 -->
<?php @$becomedriver = Session::get('becomedriver'); ?>
@if ($errors->has('token_error'))
    {{ $errors->first('token_error') }}
@endif
<section>
    <div class="container sing-up" id="forgot-pass">        
        <div class="row">
            <div class="center col-sm-5 top-space">
                <div class="panel panel-default">
                    <div class="panel-body">
                      <?php if(Auth::check()) { ?>
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">You are already login !!</p>
                      <?php } else{ ?>    
                        <div class="text-center">
                           @if(Session::has('resetpassword'))
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('resetpassword') }}</p>
                          @endif
                          @if(Session::has('conOpt'))
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('conOpt') }}</p>
                          @endif
                          @if(Session::has('error'))
                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('error') }}</p>
                          @endif
                          @if(Session::has('message'))
                              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                          @endif
                          @if(Session::has('verify'))
                              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('verify') }}</p>
                          @endif
                          <br/>
                          <a href="{!! asset('/signup') !!}" class="fb-link">Don't have an account? Sign up here</a>
                            <div class="panel-body">
                                {!! Form::open(array('url' => 'login','class' => 'form','id'=>'LoginForm')) !!}
                                <input type="hidden" placeholder="becomedriver" class="form-control"  name="becomeD" value="<?php echo @$becomedriver; ?>">
                                <fieldset>
                                  <div class="form-group">
                                    <div class="input-group boder">
                                      <span class="input-group-addon fb-sing">
                                      <span class="fb-icon"><i class="fa fa-facebook-official" aria-hidden="true"></i></span></span>
                                      <a class="btn btn-lg btn-primary btn-block fb" href="login/facebook">Continue with Facebook</a>
                                    </div>
                                  </div>
                                  <h5>Or</h5>
                                  <div class="form-group">
                                      <div class="icon-addon addon-lg">
                                          {!! Form::email('email','',array('id'=>'email','class'=>'form-control','placeholder' => 'Email')) !!}
                                          <label for="email" class="glyphicon glyphicon-envelope" rel="tooltip" title="email"></label>
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <div class="icon-addon addon-lg">
                                          <input type="password" placeholder="Password" class="form-control" id="password" name="password">
                                          <label for="password" class="glyphicon glyphicon-lock" rel="tooltip" title="password"></label>
                                      </div>
                                  </div>
                                <a href="{!! asset('/forgot') !!}" class="fb-link">Forgot password?</a>
                                <br/>
                                <br/>
                                <div class="form-group">
                                    {!! form::hidden('check','',array('id'=>'checkuser')) !!}
                                    {!! Form::submit('Login', array('class'=>'btn btn-lg btn-primary btn-block GRN LoginHere')) !!}
                                </div>
                                </fieldset>
                              {!! Form::close() !!}
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection