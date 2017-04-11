@extends('frontend.common')
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">FAQs</h3>
</div>
<!--  SECTION-1 -->
<section>
<?php
if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">
    
    @include('frontend.driversidebar')
   
    <div class="col-md-8">
  <h2 class="mt-0"> FAQs</h2>
      
      <div class="clearfix"></div>

      <div class="faq-cls">
       
        @foreach($getAllfaq as $key => $faq)
        <div id="panel-<?php  echo $key ?>" class="panel-group">
          <div class="panel panel-default">
            <div class="panel-heading"> <a  href="#panel-element-<?php  echo $key ?>" data-parent="#panel-<?php  echo $key ?>" data-toggle="collapse" class="panel-title">  {{ $key+1 }} . {{ $faq->question}} </a> </div>
            <div class="panel-collapse collapse out" id="panel-element-<?php  echo $key ?>">
              <div  class="panel-body"> {{ $faq-> answer}} </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
   
    </div>    
  </div>
<?php }else{ ?>

<?php } ?>  
</section>
@endsection