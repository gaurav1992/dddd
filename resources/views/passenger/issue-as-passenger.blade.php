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
    
  @include('frontend.passengersidebar')
   
  <div class="col-md-8">

    <div class="right-sec">
      <a href="/passenger/triphistory"><button class="btn-grn edit pull-left back"><i aria-hidden="true" class="fa fa-angle-left"></i></button></a><span>Report an Issue</span>
    </div>   
        
        <div class="clearfix"></div>
        
        @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        @endif

         <div class="panel panel-default report-panel-cls">
   
       <div class="panel-body">
     {!! Form::open(array('route' => 'passengerReportAnIssue')) !!}
          
          <input type='hidden' value='1' name='submit'>

         <div class="form-group">
        <label>Issue</label><br><br>
        <input type="hidden" value="{{ $ride_id }} " name="rideId" id="ride_id"> 
        <select  class="form-control" id="myselectcat" name="category" onchange="subCat()">
    <?php foreach($catg as $cat) { ?>
          <option value="<?php echo $cat->id; ?>" ><?php echo $cat->category; ?></option>
    <?php } ?>
        </select>
      </div>
         
         
           <div class="form-group">
        <label>Sub Issue</label><br><br>
        <select  class="form-control" id="myselectsubcat" name="subcategory">
       
          <option value="" ></option>
    
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
<script type="text/javascript">
  var passengerSubCat= "{!! route('passengerSubCat') !!}";
  var CSRF_TOKEN='{{csrf_token()}}';
  function subCat(){
    var catId=$("#myselectcat").val();
    if(catId != ''){
      $.ajax({
          type: 'post',
          url: passengerSubCat,
          data: 'id=' + catId + '&_token=' + CSRF_TOKEN,
          timeout: 4000,
          beforeSend: function(xhr) {
            $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
          },
          success: function(returnData) {
            var option='';           
            $.each(returnData, function (key, val) {
                option = option +'<option value="">Please select sub category</option><option value='+val.id+' >'+val.subcategory+'</option>';
               
            });
            $("#myselectsubcat").html(option);
            $("#divLoading").hide();
          }
      });
    }
  }
  
</script>