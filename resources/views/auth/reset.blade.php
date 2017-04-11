@extends('layouts.default')
@section('content')

<div class="form-body resetMid">
          <div class="container">
              <div class="row">
              <h2 class="text-center">Reset Password</h2>

                  <div class="col-lg-offset-3 col-lg-6 ">

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
                      	<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}" id="resetform">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="token" value="{{ $token }}">

						<div class="form-group">
							
							<div class="col-md-12">
								<input type="email" class="form-control required email" name="email" value="{{ old('email') }}" id="email" placeholder="Email address">
							</div>
						</div>

						<div class="form-group">
							
							<div class="col-md-12">
								<input type="password" class="form-control required" name="password" id="password" placeholder="Password">
							</div>
						</div>

						<div class="form-group">
						
							<div class="col-md-12">
								<input type="password" class="form-control required" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Reset Password
								</button>
							</div>
						</div>
					</form>
                  </div>
              </div>
          </div>
          <img class="img-back" src="{{ asset('public/img/innerpageimg.png') }}">
          
        </div>

@stop
