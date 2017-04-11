@extends('frontend.common')
@section('content')
<!--  SECTION-1 -->
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
	<div class="carousel-caption"> </div>
	<h3 class="page-heading">EARN WITH DEZINOW</h3>
</div>
<section>
  <div class="container" id="about-us">
  <div class="page-header text-center info-text">
    <h2>DeziNow</h2>
    <p class="mtop-30">You can sign up to be a driver on our DeziNow website. We are accepting applications now.
    
Being a driver on our site gives you a great opportunity to meet new people and earn money doing so. 
 </p>
</div>
<div class="tittle info-text "> 


<h3 class="title_width_h3">How it works</h3>
<ul>
<li>Apply to become a driver and those that qualify will be added to a pool of drivers available on our website and application.
</li>
<li> Customers will be able to request for a driver to come and drive their cars and them to the customer’s desired location.
</li>
<li> You will receive the request via your smartphone on the DeziNow App and will be able to accept or reject requests coming in.
</li>
<li> <b>You set your own hours. </b> You control how much or little you want to work. 
</li>
<li><b>Payments.</b>  Payments are all cashless. We collect all the fares via credit cards and paypal and pass that payment to you once a week minus our commission.
</li>
<li><b>Vehicle.</b> You don’t have to have a vehicle to sign up. This is a driver service where you will be driving the customer’s car. You don’t have to worry about cleaning the car. You don’t have to pay for gas or add milage to your own car.  
</li>
</ul>
</div>


<p><?php

 session_start();
  if(!empty(@Session::get('userid'))){ ?>
<a href="{{ url('becomedriver') }}" class="btn btn-primary green-btn-s  pull-left">Apply Today!</a> <?php } else{?>
<a href="{{ url('/login') }}" class="btn btn-primary green-btn-s pull-left">Apply Today!</a> 
<?php }?></p>
		<a style='clear:both' class="pull-left clearboth" href="{!! asset('public/driver-terms-service-agreement.pdf') !!}" target="_blank" title="TERMS OF SERVICE AGREEMENT">See terms and conditions for full details.</a>
  </div>
</section>
@endsection
