@include('header')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					My Profile 
				</div>

				<div class="panel-body">
				
					<div class="col-md-10 col-md-offset-1">
						<form role="form" action="{{ url('/store') }}" method="post">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="id" value="{{ Auth::user()->id }}">
						  <div class="form-group">
							<label for="text">First Name:</label>
							<input type="text" class="form-control" id="name" name="fname" value="{{ Auth::user()->fname }}">
						  </div>
						    <div class="form-group">
							<label for="text">Last Name:</label>
							<input type="text" class="form-control" id="name" name="lname" value="{{ Auth::user()->lname }}">
						  </div>
						  <div class="form-group">
							<label for="email">Email address:</label>
							<input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}">
						  </div>

						  <button type="submit" class="btn btn-default">Submit</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@include('footer')