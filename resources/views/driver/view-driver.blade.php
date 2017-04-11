@extends('frontend.common')
@section('drivercustom')
{!! HTML::script('public/js/framework/drivercustom.js'); !!}
@stop
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">PAYMENTS</h3>
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
  <?php if($user_documents ==''){ ?>
    <h1>No Document Found</h1>
 <?php }else{ ?>

<h2 class="mt-0"> View Driver Application</h2>
<h4>Upload Documents</h4>
    <div class="row upload-document">
    <div class="col-sm-6 cls-upload-docu"> <div class="file-input-cls"><label class="btn btn-file"><a href="{{ $user_documents->license_verification }}" download=""><i class="fa  fa-download "></i>  </a>  </label>  <span>
  <a href="http://www.mobilytedev.com/deziNow/uploads/drivers-documents/6b35cad8ec513d9298491a092498e382.jpg" target="_blank">Driver License</a>
       <span>
      </div> </div>
   <div class="col-sm-6 cls-upload-docu"> <div class="file-input-cls"> <label class="btn btn-file"><a href="{{ $user_documents->proof_of_insurance }} " download="">
   <i class="fa  fa-download "></i>   
   </a>  </label>  <span>
   
    <a href="http://www.mobilytedev.com/deziNow/uploads/drivers-documents/6b35cad8ec513d9298491a092498e382.jpg" 
target="_blank">Driver Insurance  </a>
   </span> </div>   </div>
 
    </div>
    
      <div class="clearfix"></div>
   
      <table class="table table-referral view-driver-applocation">
     <thead>
          <tr> 
            <th>Answers to the following questions </th>
          </tr>
        </thead>
      
        <tbody>
    
  @foreach($user_documents->driver_records as $ss)
          <tr>
            <td>{{ $ss['question'] }}</td>
            <td> <select class="form-control"> <option>@if($ss['answer'] == "1") 
        Yes 
      @else 
         No
     @endif
  </option>
  </select>  </td>
          </tr>
    @endforeach
        </tbody>
      </table>
      <h4>How did you hear about us?</h4>
      <textarea class="form-control" rows="5" value="{{ $user_documents->driver_records[6]['answer'] }}" >{{ $user_documents->driver_records[6]['answer'] }}</textarea>
      
      
      <div class="clearfix"></div>
    
    </div>


<?php } ?>
</div>
</div>
 <?php }else{ ?>

<?php } ?>  
</section>

@endsection















