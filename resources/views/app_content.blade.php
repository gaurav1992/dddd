@if (Auth::guest())   
   
@else
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">
					My Profile 
				</div>

				<div class="panel-body">
					<div class="col-md-10 col-md-offset-1">
						<button type="button" class="btn btn-default" style="float:right"><a href="{{ url('/create') }}">Edit</a></button>
					</div>
					<div class="col-md-10 col-md-offset-1">
							Name:{{ Auth::user()->fname }} {{ Auth::user()->lname }}<br>
							Email:{{ Auth::user()->email }}
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif