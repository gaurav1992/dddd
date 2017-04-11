@extends('frontend.common')
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">FORGOT PASSWORD</h3>
</div>
<!--  SECTION-1 -->
<section>
    <div class="container sing-up" id="forgot-pass">        
        <div class="row">
            <div class="center col-sm-5 top-space">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                            <div class="panel-body">
								<p>Enter your new password</p>
                                {!! Form::open(array('url' => 'resetpassword','class' => 'form-horizontal', 'role' => 'form', 'id' => 'resetpasswordform', 'method' => 'post')) !!}
                                    <?php $value = Session::get('password_token'); ?>
                                    {!! Form::hidden('password_tokn',$value,array('class'=>'form-control',)) !!}
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            {!! Form::password('password',array('class'=>'form-control required', 'id' => 'password', 'placeholder' => 'Password')) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            {!! Form::password('password_confirmation',array('class'=>'form-control required', 'id' => 'password_confirmation', 'placeholder' => 'Confirm Password')) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            {!! Form::submit('Reset Password', array('class'=>'btn btn-lg btn-primary btn-block text-green')) !!}
                                        </div>
                                    </div>
                                {!! Form::close() !!}
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
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