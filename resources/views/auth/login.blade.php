@extends('layouts.default')
@section('content')
 <div class="form-body loginMid">
          <div class="container">
              <div class="row">
              <h2 class="text-center">Welcome to Flying Chalks</h2>

                  <div class="col-lg-offset-3 col-lg-6 ">
                  <p class="heading"><a href="{!!URL::to('/login/facebook')!!}"><span><i class="fa fa-facebook"></i>
</span>Log in with Facebook</a></p>
                  <p class="or">or</p>
				  @if (session('status'))
						<div class="alert alert-success">
							{{ session('status') }}
						</div>
					@endif
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
                      	<form  role="form" method="POST" action="{{ url('/auth/login') }}" id="userlogin">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
              
              <input type="email" class="form-control required email" id="email" placeholder="Email" name="email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
            
              <input type="password" class="form-control required" id="pwd" placeholder="Password" name="password">
            </div>
            <div class="checkbox pull-right">
              <label><input type="checkbox" name="remember"> Remember me</label>
            </div>
            <button type="submit" class="btn btn-default">Log in</button>
			 <p class="f-cl"><a href="{{ url('/password/email') }}">Forgot password? </a>
           <a href="{{ url('/auth/register') }}">Don't have an account? Sign up now!</a></p>
          </form>
                  </div>
              </div>
          </div>
          <img class="img-back" src="{{ asset('public/img/innerpageimg.png') }}">
          
        </div>
        
@stop
