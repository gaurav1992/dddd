@extends('frontend.common')
@section('drivercustom')
{!! HTML::script('public/js/framework/drivercustom.js'); !!}
@stop
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">REPORT AN ISSUE</h3>
</div>
<section>
<?php
if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">
    
  @include('frontend.driversidebar')
   
	<div class="col-md-8">

		<div class="right-sec">
		<h2 class="mt-0">Report an issue </h2>
		</div>   
        
        <div class="clearfix"></div>
        
         <div class="panel panel-default report-panel-cls">
   
       <div class="panel-body">
	         @if(Session::has('message'))
              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
	  {!! Form::open(array('route' => 'driverReportAnIssue')) !!}
          
          <input type='hidden' value='1' name='submit'>

         <div class="form-group">
        <label>Issue</label><br><br>
        <input type="hidden" value="{{ $ride_id }} " name="rideId" id="ride_id"> 
        <select  class="form-control" id="myselectcat" name="category">
    <?php foreach($catg as $cat) { ?>
          <option value="<?php echo $cat->id; ?>" ><?php echo $cat->category; ?></option>
    <?php } ?>
        </select>
      </div>
         
         
           <div class="form-group">
        <label>Sub Issue</label><br><br>
        <select  class="form-control" id="myselectsubcat" name="subcategory">
         <?php foreach($subcatg as $subcat) { ?>
          <option value="<?php echo $subcat->id; ?>" ><?php echo $subcat->subcategory; ?></option>
    <?php } ?>
        </select>
      </div>
      
        <div class="form-group">
        <label>Massage</label><br><br>
      <textarea class="form-control" placeholder="Massage" name="message" id="message"></textarea>
      </div>
         <button class="btn btn-primary green-btn-s" id="rep" >Submit</button>
      {!! Form::close() !!}
       </div>
	   
  		</div>
        
    </div>

  </div>
  <?php }else{ ?>

<?php } ?>  
</section>

@endsection
