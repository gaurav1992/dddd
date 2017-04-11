@extends('layouts.default')
@section('content')

 <div class="form-body regMid">  
	 <div class="container">        
		 <div class="row">    
		 <h2 class="text-center">Sign Up</h2> 
			 <div class="col-lg-offset-3 col-lg-6 ">   
			 <p class="heading">
			 <a href="{!!URL::to('/login/facebook')!!}"><span><i class="fa fa-facebook"></i></span>Sign up with Facebook</a></p> 
			 <p class="or">or</p>     
			 @if (count($errors) > 0)	
				 <div class="alert alert-danger">		
			 
			 <ul>							
			 @foreach ($errors->all() as $error)		
			 <li>{{ $error }}</li>							
			 @endforeach					
			 </ul>				
			 </div>			
			 @endif			
			 <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/register') }}" id="usersignup">		
			 <input type="hidden" name="_token" value="{{ csrf_token() }}">	
			 <div class="form-group">		
			 <input type="text" class="form-control bckGrey required" id="fname" placeholder="First Name" name="fname" value="{{ old('fname') }}">			
			 </div>		
			 <div class="form-group">	
			 <input type="text" class="form-control bckGrey required" id="lname" placeholder="Last Name"   name="lname" value="{{ old('lname') }}">	
			 </div>		
			 <div class="form-group">			
			 <input type="email" class="form-control required email" id="email" placeholder="Email" name="email" value="{{ old('email') }}">	
			 </div>				
			 <div class="form-group">			
			 <input type="password" class="form-control required" id="pwd" placeholder="Password" name="password">			
			 </div>			
			 <div class="form-group">				
			 <input type="password" class="form-control required" id="cpwd" placeholder="Confirm Password" name="password_confirmation">	
			 </div>	
			 <div class="form-group">		
			 <p class="terms-cond">By signing up, I agree to Flying Chalks' <a href="{{ url('/pages/privacy-policy') }}" target="_blank">Terms of Service</a> and <a href="{{ url('/pages/terms-of-use') }}" target="_blank">Privacy Policy</a>.</p>	
			 </div>						<button type="submit" class="btn btn-default regBtn">Register</button>		
			 </form>    
			 </div>           
		</div>      
	 </div>          
 <img class="img-back" src="{{ asset('public/img/innerpageimg.png') }}">    


 </div>
@stop
	 