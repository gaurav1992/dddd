
@extends($layout)
@section('title', 'Admin-Subadmin Profile')
@section('customjavascript')
<script>
// var deleteCarUrl = "{!! route('deleteCar') !!}";
// var indexUrl= "{!! route('passengerAjax') !!}";
// var homeUrl= "{!! route('index') !!}";
</script>
@stop
@section('content')

      <!-- Main content -->
      <section class="content">
	  <!-- favourite place -->
	  	@if(Session::has('message'))
		<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
		@endif	
		<div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
		  
		
            <div class="table-responsive">
			
			
			<h4 class="title-12"><b>Car</b></h4>
			  <div class="box_search"> 
					 
					
					<div class="form-group form_fl">
						<label> Search </label> 
						 
						<br>
						 <input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
					</div> 
					
					<div class="form-group form_fl">
					
						<label> Time Stamp </label> <br>
						 
						 <input type="text" Placeholder="Ride ID/Driver Name/Driver ID"/>
						 <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
					</div> 
					
					<div class="form-group form_fl">
						<label> Time Stamp </label> <br>
						 
						 <input type="text" Placeholder="Ride ID/Driver Name/Driver ID"/>
						 <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
					</div> 
					
					<div class="form-group mt15 pull-right mr30">
					<button class="btn btn-default color-blue"> Download </button>
					</div> 	
				</div>
              <table class="table">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Ride ID</th>
                    <th>Timesstamp</th>
                    <th>Drive Name</th>
                    <th>Driver ID</th>
                    <th>Reported Issues</th>
                    <th>Status</th>
                    <th>Billing Amount</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>123456</td>
                    <td>Harry</td>
                    <td>03-05-15</td>
                    <td>03-05-15</td>
                    <td>harry@gmail.com</td>
                    <td>1234567890</td>
                    <td>Active</td>
                    <td><a class="label-success label" href="#"> View</a> </td>
                  </tr>
                </tbody>
              </table>
            <div class="container text-center">
              <ul class="pagination">
                <li class="previous"><a href="#"><i class="fa fa-angle-double-left" aria-hidden="true"></i> </a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li class="next"><a href="#"><i class="fa fa-angle-double-right" aria-hidden="true"></i> </a></li>
              </ul>
            </div>
			</div> 
          </div>
        </div>
      </div>
      </section>

@stop
@stop
