@extends('frontend.common')

@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
	<div class="carousel-caption"> </div>
	<h3 class="page-heading">CONTACT US</h3>
</div>

<!--  SECTION-1 -->
<section>

	<div class="container" id="contact-us">  
		<div class="col-sm-6 center-aline padzero">
			@if(Session::has('message'))
	          <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
	      	@endif	      	
			{!! Form::open(array('url' => 'contact','class' => 'form','id'=>'ContactForm')) !!}
			<input type="hidden" name="_token" value='{!! csrf_token() !!}'>
				<div class="col-sm-12 padleftzero">
                	<div class="tittle2">
                		<h3>Get in touch with us</h3>
                    </div>
					
					<?php 
						if(Auth::check()){ 
						$email = Auth::user()->email;
					   $first_name = Auth::user()->first_name;
					  $last_name = Auth::user()->last_name;
					?>
					    {!! Form::text('first_name',$first_name,array('id'=>'','class'=>'','placeholder' => 'First Name')) !!}
					    {!! Form::text('last_name',$last_name,array('id'=>'','class'=>'','placeholder' => 'Last Name')) !!}
						{!! Form::email('email',$email,array('id'=>'','class'=>'','placeholder' => 'Email')) !!}
					<?php }else{ ?>
					   {!! Form::text('first_name','',array('id'=>'','class'=>'','placeholder' => 'First Name')) !!}
						{!! Form::text('last_name','',array('id'=>'','class'=>'','placeholder' => 'Last Name')) !!}
						{!! Form::email('email','',array('id'=>'','class'=>'','placeholder' => 'Email')) !!}
					<?php } ?>
					{!! Form::textarea('message','',array('rows' => '1','placeholder' => 'Message')) !!}
				</div>
				
				<div class="col-sm-12 padleftzero">
					<!--google re-captcha detail-->
					<!--
					site key : 6LfnDCUTAAAAAFJQkO8CtMClK_P1GFysru7XtIMd
					secret key : 6LfnDCUTAAAAAPVQw225Xe3bFulIeB1eb5Q05WAG
					-->
					<script src='https://www.google.com/recaptcha/api.js'></script>
					<div class="g-recaptcha" data-sitekey="6Lf30hMUAAAAAFEjhiEm89lmwF8zt5wo5_-37LDs"></div>
					<!-- <img class="img-responsive" src="{!! asset('public/images/captcha.jpg') !!}" alt="#"/> -->
					<button class="btn btn-primary green-btn-s submitContactForm" type="submit">Submit</button>
				</div>
			{!! Form::close() !!}

		</div>

		<div class="col-sm-6 padrightzero smallScreen_padzero">
			<div class="col-sm-12 address2 padrightzero">
				<h4 class="bottom-border">
					<i class="fa fa-envelope-square" aria-hidden="true"></i> Customer Service
				</h4>
				
				<div class="cont-info">					
					<p class="ml25"><a target="blank_self" href="mailto:info@dezinow.com">info@dezinow.com</a></p>
				</div>
				
				<h4 class="bottom-border">
					<i class="fa fa-phone-square" aria-hidden="true"></i> Contact Number
				</h4>
				<div class="cont-info">					
					<p class="ml25">415-735-7008</p>
				</div>
				
				<h4 class="bottom-border2">
					<i class="fa fa-clock-o" aria-hidden="true"></i> Service availability and requests
				</h4>
				<div class="cont-info">					
					<p class="ml25">Service is available 24/7. DeziNow app will be available for download soon in the app and google play store. </p>					
				</div>
			</div>
			
		</div>
	</div>
</section>

@endsection
<style>
.ml25{margin-left:25px}
</style>
