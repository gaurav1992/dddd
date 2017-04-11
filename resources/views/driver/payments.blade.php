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
    <div class="right-sec">
         @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        @endif
        <h2 class="mt-0">Bank information</h2> </div>
      <div class="clearfix"></div>
      @if($bank_detail)
      <div class="form-horizontal form-bank-information">
  <div class="form-group">
 <label  class="col-sm-3">Bank Name*</label>
    <div class="col-sm-9">
     <p>{{ $bank_detail->bank_name}}</p>
    </div>
  </div>
 <div class="form-group">
    <label  class="col-sm-3">Account Number*</label>
    <div class="col-sm-9">
          <p>{{ $bank_detail->acc_number}}</p>
    </div>
  </div>

<div class="form-group">
    <label  class="col-sm-3">Routing Number*</label>
    <div class="col-sm-9">

<p>{{ $bank_detail->routing_number}}</p>

    </div>
  </div>

<div class="form-group">
    <label  class="col-sm-3">Branch*</label>
    <div class="col-sm-9">

<p>{{ $bank_detail->branch}}</p>

    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-5 text-center col-xs-12">
<!-- Trigger the modal with a button -->
     <a class="btn btn-primary green-btn-s" data-toggle="modal" data-target="#myModal">Edit</a>
    </div>
  </div>
</div>
@else
<div class="form-horizontal form-bank-information">
  <div class="form-group">
 <label  class="col-sm-3">Bank Name*</label>
    <div class="col-sm-9">
     <p>Name</p>
    </div>
  </div>
 <div class="form-group">
    <label  class="col-sm-3">Account Number*</label>
    <div class="col-sm-9">
          <p>XXXXXXXXXXXXX</p>
    </div>
  </div>

<div class="form-group">
    <label  class="col-sm-3">Routing Number*</label>
    <div class="col-sm-9">

       <p>XXXXXXXXXXXXX</p>

    </div>
  </div>
<div class="form-group">
    <label  class="col-sm-3">Branch*</label>
    <div class="col-sm-9">

      <p> Branch*</p>

    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-5 text-center col-xs-12">
<!-- Trigger the modal with a button -->
     <a class="btn btn-primary green-btn-s" data-toggle="modal" data-target="#myModal">Edit</a>
    </div>
  </div>
</div>
@endif



<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <!--h4 class="modal-title">Bank information </h4-->
      </div>
      <div class="modal-body">
<!-- <div class="panel panel-default"> -->
  <!--   <div class="panel-heading"></div>
 -->
		<h2 class="mt-0"> Bank Information </h2>
<?php echo Form::open(array('url' => 'payments', 'method' => 'put','class'=>'form-horizontal form-bank-information','id' => 'bank_info')) ?>
    <?php 
        if($bank_detail){
          $bankNmae = $bank_detail->bank_name;
          $bankAcount = $bank_detail->acc_number;
          $bankrout = $bank_detail->routing_number;
          $bankBranch =$bank_detail->branch;
        }else{
          $bankNmae = '';
          $bankAcount = '';
          $bankrout = '';
          $bankBranch = '';
        }
  ?>
   <div class="form-group">
    <label  class="col-sm-3 col-xs-12 control-label">Bank Name*</label>
    <div class="col-sm-8 col-xs-12">
      <input type="text" class="form-control" name="bank_name" id="bank_name" required placeholder="Name" value='<?php echo $bankNmae; ?>'>
    </div>
  </div>
  <div class="form-group">
    <label  class="col-sm-3 col-xs-12 control-label">Account Number*</label>
    <div class="col-sm-8 col-xs-12">
      <input type="text" class="form-control" name="acc_number" id="acc_number" required placeholder="XXXXXXXXXX" value='<?php echo $bankAcount; ?>'>
    </div>
  </div>

    <div class="form-group">
    <label  class="col-sm-3 col-xs-12 control-label">Routing Number*</label>
    <div class="col-sm-8 col-xs-12">
      <input type="text" class="form-control"  name="routing_number" required id="routing_number" placeholder="XXXXXXXXXX" value='<?php echo $bankrout; ?>'>
    </div>
  </div>

     <div class="form-group">
    <label  class="col-sm-3 col-xs-12 control-label">Branch*</label>
    <div class="col-sm-8 col-xs-12">
      <input type="text" class="form-control" name="branch" required id="branch" placeholder="Branch" value='<?php echo $bankBranch; ?>'>
    </div>
  </div>

  <div class="form-group">
  <div class="col-sm-12">
      <button  type="button" class="btn btn-primary green-btn-s" id="bankdetail">Save</button>
    </div>
  </div>

<!-- {{ Form::close() }} -->

<!-- </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



</div>
</div>
 <?php }else{ ?>

<?php } ?>
</section>

@endsection