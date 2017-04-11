@extends('frontend.common')

  @section('customjavascript')

  <script type="text/javascript">
    var deleteCar = "{!! route('deleteCar') !!}";
    var updateCar = "{!! route('updateCar') !!}";
  </script>

  @stop

@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">YOUR CARS</h3>
</div>

<!-- <a class='deleteCar' carAction='delete' carID='123' href="#">DELETE</a> -->

<!--  SECTION-1 -->
<section>
  <?php 
    $createjoinind_date = new DateTime($myData['created_at']);
    $newjoinind_date = $createjoinind_date->format('m/d/Y');
  ?>
  <div class="container mtop-30" id="driverprofileedit">

    @include('frontend.passengersidebar')

    <div class="col-md-8 addCar_sec">
        <h3 class="addCar-h3">Add Car </h3>
      <div class="panel panel-default">
            @if(Session::has('message'))
              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
          {!! Form::open(array('url' => 'passenger/yourcars','class' => 'form-horizontal form-bank-information','files' => true,'id'=>'addCarDetail')) !!}
            <div class="form-group">
              <label class="col-sm-3 col-xs-12 control-label">Make*</label>
              <div class="col-sm-8 col-xs-12">
                <input type="text" name="make" placeholder="Make" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 col-xs-12 control-label">Model*</label>
              <div class="col-sm-8 col-xs-12">
                <input type="text" name="model" placeholder="Model" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 col-xs-12 control-label">  Year*</label>
              <div class="col-sm-8 col-xs-12">
               <select class="form-control" id="sel1" name="year" required>
                  <?php
                     for($i = 1900 ; $i <= date('Y'); $i++){
                        echo '<option value="'.$i.'">'.$i.'</option>';
                     }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 col-xs-12 control-label">License Plate #</label>
              <div class="col-sm-8 col-xs-12">
                <input type="text" name="number" placeholder="Optional" class="form-control" >
              </div>
            </div>
           <div class="form-group">
              <label class="col-sm-3 col-xs-12 control-label">Transmission*</label>
                <div class="col-sm-8 col-xs-12">
                  <label class="radio-inline">
                    <input checked='checked' type="radio" name="transmission" id="inlineRadio1" value="automatic"> Automatic
                  </label>
                  <label class="radio-inline">
                    <input type="radio" name="transmission" id="inlineRadio2" value="manual"> Manual
                  </label>  
          
                </div>
            </div>
          <div class="form-group">
          <label class="col-sm-5 col-xs-12 control-label"></label>
          <div class="col-sm-5 col-xs-12 carDetailAdd">
              <button class="btn btn-primary green-btn-s" type="submit">Save</button>
            </div>
          </div>
      {!! Form::close() !!}
    </div><!--//panel panel-default-->
    
    <h3 class="add-h3">Your Cars</h3>

<?php if($myCars['cardetails']){ ?>
  <div class="add-car-data-table">
    <?php foreach ($myCars['cardetails'] as $cardetail) {
      ?>  
      <div class="add-car-cls"> 
        <div class="col-xs-12 col-sm-6 left-cls-div">
         <div class="add-car-img">
          <?php if($cardetail->transmission == 'automatic'){ ?>
            <img src="{!! asset('public/images/icon_auto.png') !!}" alt="test" class="img-responsive">
          <?php }else{ ?>
            <img src="{!! asset('public/images/icon_manual.png') !!}" alt="test" class="img-responsive">
          <?php } ?> 
         </div>
          <p><?php echo strtoupper($cardetail->make); ?>    <?php echo strtoupper($cardetail->model); ?></p>
          <p><?php echo strtoupper($cardetail->number); ?></p>
          <p><?php echo $cardetail->year; ?></p>
        </div>
        <div class="col-xs-12 col-sm-6 right-cls-div ">
       @if($cardetail->is_default == '1')
          <div class="col-xs-4 col-sm-4 text-center add-car-detail"><i class="glyphicon glyphicon-ok"></i> </div>
          @else
         <div class="col-xs-4 col-sm-4 text-center add-car-detail"><i class="glyphicon glyphicon-remove"></i> </div>
     @endif
          <div class="col-xs-4 col-sm-4 text-center add-car-detail"><a class='deleteCar' carAction='deleteCar' carID='<?php echo $cardetail->id; ?>' href=""><i class="fa fa-trash-o"></i></a></div>
          <div class="col-xs-4 col-sm-4 text-center add-car-detail"><a class="UpdateDeleteCar" data-toggle="modal" data-target="#myModal_<?php echo $cardetail->id; ?>"><i class="fa  fa-edit"></i></a></div>
        </div>
        <div class="clearfix"></div>
      </div>
      <!--model foe update or delete car start-->
        <!-- Trigger the modal with a button -->
        
        <!-- Modal -->
        <div id="myModal_<?php echo $cardetail->id; ?>" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <h2 class="mt-0"> Edit Car Details </h2>
                {!! Form::open(array('url' => 'updateCar','class' => 'form-horizontal form-bank-information updateCarDetail','files' => true,'id'=>'')) !!}
                  <input type="hidden" value="<?php echo $cardetail->id; ?>" class="updatedcarId">
                  <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">Make*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input type="text" name="make" placeholder="Make" value="<?php echo $cardetail->make; ?>" class="form-control make" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">Model*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input type="text" name="model" placeholder="Model" value="<?php echo $cardetail->model; ?>" class="form-control model" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">  Year*</label>
                    <div class="col-sm-8 col-xs-12">
                     <select class="form-control year" id="sel1" name="year" >
                        <?php
                           $selectedyear = $cardetail->year; 
            // echo $selectedyear ;
                           for($i = 1900 ; $i <= date('Y'); $i++){
                              echo '<option value="'.$i.'"'.($i == $selectedyear ? ' selected="selected"' : '').'>'.$i.'</option>';
                           }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">License Plate #</label>
                    <div class="col-sm-8 col-xs-12">
                      <input type="text" name="number" value="<?php echo $cardetail->number; ?>" placeholder="Optional" class="form-control number">
                    </div>
                  </div>
                 <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">Transmission*</label>
                      <div class="col-sm-8 col-xs-12">
                        <label class="radio-inline">
                          <input type="radio" class="transmission" name="transmission" <?php if($cardetail->transmission =='automatic'){ echo 'checked'; } ?> id="inlineRadio1" value="automatic"> Automatic
                        </label>
                        <label class="radio-inline">
                          <input type="radio" class="transmission" name="transmission" <?php if($cardetail->transmission =='manual'){ echo 'checked'; } ?> id="inlineRadio2" value="manual"> Manual
                        </label>
                      </div>
                  </div>
                <?php if($total_user_cars == 1){
                  //show nothing
                } else{ ?>
                <div class="form-group">
                    <label class="col-sm-3 col-xs-12 control-label">Default Car*</label>
                      <div class="col-sm-8 col-xs-12">
                         <label class="radio-inline">
                              <input type="radio" name="default_car" id="inlineRadio1" value="1" <?php if($cardetail->is_default =='1'){ echo 'checked'; } ?> > Yes
                          </label>  
                          <label class="radio-inline">
                              <input type="radio" name="default_car" id="inlineRadio2" value="0" <?php if($cardetail->is_default =='0'){ echo 'checked'; } ?>> No
                          </label>
                      </div>
                  </div>
                  <?php } ?>
                <div class="form-group">
                <div class="col-sm-12 col-xs-12">
                    <button class="btn btn-primary green-btn-s carDetailUpdate" type="submit">Update</button>
                    <a class="btn btn-primary green-btn-s carDetailDelete deleteCar" carAction='deleteCar' carID='<?php echo $cardetail->id; ?>' href="">Delete</a>
                  </div>
                </div>
                <input type="hidden" name="total_user_cars" id="total_user_cars" value="<?php echo $total_user_cars; ?>">
            {!! Form::close() !!}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>
      <!--//model foe update or delete car end-->



    <?php } ?>          
  </div>  
<?php  } ?>          
      
      
      
      
    </div>   
  </div>
</section>
@endsection