@extends('frontend.common')

@section('creditcss')
  {!! HTML::style('public/css/libs/creditly.css') !!}
@endsection

@section('creditjs')
  {!! HTML::script('public/js/framework/creditly.js'); !!}
  {!! HTML::script('public/js/framework/addCard.js'); !!}
@endsection

@section('customjavascript')

  <script type="text/javascript">
    var savecarddetail = "{!! route('savecarddetail') !!}";

    var deltecard = "{!! route('deltecard') !!}";    
  </script>

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
    
    @include('frontend.passengersidebar')
   
    <div class="col-md-8">
        <h3 class="addCar-h3">Add New Card</h3>
      <div class="panel panel-default">
        @if(Session::has('status'))
          <p class="alert alert-info"> {{Session::get('status')}}</p>
        @endif

        <p class="alert alert-info" style="display:none;" id="errorcard"></p>
          {!! Form::open(array('url' => 'passenger/savecarddetail','class' => 'creditly-card-form form-horizontal form-bank-information','id'=>'checkout')) !!}
            <input type="hidden" value="card" name="account_type">
            <section class="creditly-wrapper">
              <div class="credit-card-wrapper">
                  <div class="form-group controls">
                    <label class="col-sm-3 col-xs-12 control-label">Card Number*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input required name="card_number" class="number credit-card-number form-control" type="text" data-braintree-name="number"
                      inputmode="numeric" autocomplete="cc-number" autocompletetype="cc-number" x-autocompletetype="cc-number"
                      placeholder="&#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149; &#149;&#149;&#149;&#149;" >
                    </div>
                  </div>
                  <div class="form-group controls">
                    <label class="col-sm-3 col-xs-12 control-label">Expiration*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input required name="expiration" class="expiration-month-and-year form-control" type="text" data-braintree-name="expiration_date" placeholder="MM/YY">
                    </div>
                  </div>
                   <div class="form-group controls">
                    <label class="col-sm-3 col-xs-12 control-label">CVV*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input required class="form-control" type="text" name="cvv" data-braintree-name="cvv" placeholder="CVV">
                    </div>
                  </div>
                  <div class="form-group controls">
                    <label class="col-sm-3 col-xs-12 control-label">Name on Card*</label>
                    <div class="col-sm-8 col-xs-12">
                      <input name="cardHolder" required data-braintree-name="cardholder_name" class="form-control" type="text" placeholder="Name on Card">
                    </div>
                  </div>
                  <div class="form-group controls">
                    <label class="col-sm-3 col-xs-12 control-label">Zip Code*</label>
                    <div class="col-sm-8 col-xs-12">
                       <input name="postalcode" required data-braintree-name="postal_code" class="form-control" type="text" placeholder="Zip Code">
        
                    </div>
                  </div>


               
              </div>
            </section>

            <div class="form-group">
              <div class="col-sm-12 col-xs-12  text-center">
                <button class="btn btn-primary green-btn-s savePaymentCard">Save</button>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-12 text-center">Or</label>
            </div>
            <div class="form-group">
              <label class="col-sm-12 text-center">
              <div class="" id="paypal-container"> </div>
             

          {!! Form::close() !!}


       <?php  $cToken =  Braintree_ClientToken::generate(); ?>
         <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
   
        <SCRIPT>
        braintree.setup('<?php echo $cToken; ?>', 'custom', {id: 'checkout'});

        </SCRIPT> 
        <script type="text/javascript">
          braintree.setup("<?php echo $cToken; ?>", "custom", {
            paypal: {
              container: "paypal-container",
            },
            onPaymentMethodReceived: function (obj) {
              sendonserver(obj.nonce);
            }
          });
          
          function sendonserver(nonce){
            var tokent = '<?php echo Session::token(); ?>';
            console.log(tokent);
                  $.ajax({
                    type:'post',
                    dataType : "json",
                    url:savecarddetail,
                    data : {payment_method_nonce : nonce,_token : tokent,account_type:'paypal'  },
                    success:function(response)
                    {
                        console.log(response.message);
                      if(response.message=='Payment method saved successfully'){
                          location.reload();
                      }else{
                        $("#errorcard").html(response.message);
                        if(response.message != 'null'){$("#errorcard").show();}
                        alert("OOPS !!! Something went wrong.Please try again.")
                      }

                    }
                  });
          }
          
        </script>


       </label>
      </div>




        <!-- <form class="form-horizontal form-bank-information">
          <div class="form-group">
            <label class="col-sm-3 col-xs-12 control-label">Make Model*</label>
            <div class="col-sm-8 col-xs-12">
              <input type="email" placeholder="Male" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 col-xs-12 control-label">Expiration date*</label>
            <div class="col-sm-8 col-xs-12">
              <input type="text" placeholder="Model" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 col-xs-12 control-label">Security Code *</label>
            <div class="col-sm-8 col-xs-12">
              <input type="text" placeholder="Optional" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-12 text-center">Or</label>
          </div>
          <div class="form-group">
            <label class="col-sm-12 text-center">
            <div class="paypal-cls"> Paypal </div>
            </label>
          </div>
          <div class="form-group">
            <div class="col-sm-12 col-xs-12  text-center">
              <button class="btn btn-primary green-btn-s" type="submit">Save</button>
            </div>
          </div>
        </form> -->
      </div>



      <h3 class="add-h3">Credit Cards</h3>
 
        @foreach ($card as $account)
       
             <div class="add-car-data-table card-detail" style="height: 100px;">
                <div class="add-car-cls add-card-deatail">
                  <div class="col-sm-6 left-cls-div">
                    <div class="col-sm-4 "> <img src="{!! asset('public/images/visa.png') !!}" width="50" > </div>
                    <div class="col-sm-8 personal-add">
                      <p>{{$account->masked_number}}</p>
                    </div>
                  </div>
                  <!--<div class="col-sm-6 right-cls-div ">-->
                    <div class="col-sm-3 text-center add-car-detail"><?php if ($account->is_default ==1) { ?><i class="glyphicon glyphicon-ok"></i><?php } ?> </div>
                    <div class="col-sm-3 pull-right text-center add-car-detail"> <a href="javascript:void(0)" onclick="deletecard({{$account->id}},{{$account->user_id}})"><i class="fa fa-trash-o"></i></a> </div>
                    <!--<div class="col-sm-4 text-center add-car-detail"> <i class="fa  fa-edit"></i> </div>-->
                  <!--</div>-->
                  <div class="clearfix"></div>
                </div>
            </div>
           
        @endforeach 
      
      
      <h3 class="add-h3">Paypal</h3>

           
           @foreach ($paypalAccount as $account)
                  <div class="add-car-cls add-card-deatail">
                    <div class="col-sm-6 left-cls-div">
                     
                        <p>{{ $account->account_email }}</p>
                        
                    </div>
                    <div class="col-sm-3 pull-right right-cls-div ">
                
                      <div class="col-sm-12 text-center paypal-icon"><a href="javascript:void(0)" onclick="deletecard({{$account->id}},{{$account->user_id}})"> <i class="fa fa-trash-o" ></i> </a></div>
                      <!--<div class="col-sm-6 text-center paypal-icon"> <i class="fa  fa-edit"></i> </div>-->
                    </div>
                    <div class="clearfix"></div>
                  </div>
            @endforeach 

        
        <hr class="hr-creadit"/>
      
      <table width="100%" border="0" class="text-center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      
      <td width="50%">
        <style type="text/css">
          .ps-apply-promo:hover{
            text-transform: none;
            color: #fff;
            text-decoration: none;
            background: #72b301;
          }
          .ps-apply-promo{
            float: left;
            display: block;
            height: 40px;
            width: 173px;
            background: #9DDA1F;
            color: #000;
            margin: 0px;
            padding: 0px;
            box-shadow: none;
            border: 0px;
          }
          .ps_promo_code{
            height: 40px; 
            width: 173px;
            float: left;
          }
        </style>

      {!! Form::open(array('url' => 'passenger/savecarddetail','class' => 'form-ps-promo','id'=>'form-ps-promo')) !!}


        <?php if (Session::has('ps_promo_code')) {
          $ps_promo_code_value = Session::get('ps_promo_code');
        } else {
          $ps_promo_code_value = '';
        } ?>

        <input class="ps_promo_code form-control input-sm" id="ps_promo_code" type="text" name='ps_promo_code' value='<?php echo $ps_promo_code_value; ?>'required>
        <input type='submit' class="ps-apply-promo" value='Apply Promo Code'>

      {!! Form::close() !!}

      </td>
      
      <td width="50%"><p style="border:#dcdcdc 1px solid; line-height:40px; margin:0px;padding-left:5px;">Available Dezi Credit : @if($user_credit[0]->creditBalance != '')${{  $user_credit[0]->creditBalance }}@else $00.00 @endif</p></td> 
    </tr>
    
  </tbody>
</table>
    </div>    
  </div>
<?php }else{ ?>

<?php } ?>  
<script type="text/javascript">
  function deletecard(id,user_id){
    var tokent = '<?php echo Session::token(); ?>';
    console.log("id="+id+"user_id="+user_id);
     $.ajax({
        type:'post',
        dataType : "json",
        url:deltecard,
        data : {id : id,_token : tokent,user_id:user_id  },
        success:function(response)
        {
          if(response.message=='Payment method deleted successfully'){
                          location.reload();
          }else{
            alert("OOPS !!! Something went wrong.Please try again.")
          }
        }
      });
  }
</script>
</section>
@endsection
