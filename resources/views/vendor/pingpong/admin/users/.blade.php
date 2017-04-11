@extends($layout)

@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Manage Passengers' !!} 

	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var suspendUrl = "{!! route('suspend') !!}";
</script>
@stop
<?php $allActive=0; ?>
@section('content')
<style type="text/css">
.fl {
	float: left;
	margin-right: 20px;
}
.l_blck {
	display: block;
	font-size: 11px;
}
.top_mg {
	line-height: 10px;
}
.cost_input {
	width: 31%;
}
.upload_btn {
	display: inline-block;
	text-align: center;
	width: 100%;
}
.promo_code_btn {
  display: table-caption;
  left: 0;
  margin: 0 auto;
  right: 0;
  text-align: center;
  width: 34%;
}
.cost_input > input {
  float: right;
}

.second_form{
	margin:20px 0px;
	
}

.thi_form{
	margin:20px 0px;	
}

.mg_le {
  margin-left: 20px;
  margin-right: 15px;
}

.lbl_center{ width:100%; text-align:center;}

.psng_dri {
	float: left;
	margin-right: 20px;
	width:100%;
}

.psng_dri input[type='text']
{
	width:40%;
  
}

</style>
<div class="container">
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <form role="form">
        <div class="form-group fl">
          <label for="email">Change City</label>
          <select>
            <option value="volvo">New York City</option>
            <option value="saab">New York City</option>
            <option value="mercedes">New York City</option>
            <option value="audi">New York City</option>
          </select>
        </div>
        <div class="form-group fl">
          <label for="pwd" >Form Date</label>
          <input type="text" placeholder="14/02/2006">
          <i class="fa fa-calendar" aria-hidden="true"></i> </div>
        <div class="form-group fl">
          <input type="text" placeholder="14/02/2006">
          <i class="fa fa-calendar" aria-hidden="true"></i> </div>
        <div class="form-group fl">
          <select >
            <option value="volvo">$From</option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
          <select >
            <option value="volvo">$To</option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
        </div>
        <button type="submit" class="btn btn-default top_mg">Download Chnage Log</button>
      </form>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <form role="form" class="second_form">
        <div class="form-group fl cost_input">
          <label for="email">Cost per mile</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">Wait charge/min</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">Min.charge</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">Service Charge</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">Cost for < 2mi Driver travel</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">%per mile earned by driver</label>
          <input type="text" value="$" />
        </div>
        <div class="form-group fl cost_input">
          <label for="email">Cancellation Charge</label>
          <input type="text" value="$" />
        </div>
        <div class="upload_btn">
          <button> upload </button>
          <button> undo changes </button>
        </div>
      </form>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="row thi_form">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <form role="form" >
            <label for="email" class="fl lbl_center">Passenger User Referral</label>
            <div class="form-group psng_dri">
              <input type="checkbox" name="vehicle" value="Bike">
              Enable
              <label for="email" class="mg_le">Amount received for referal</label>
              <input type="text" placeholder="DeziCredit"  />
            </div>
            <label for="email" class="fl">Create Promo Codes</label>
            <div class="form-group fl">
              <input type="checkbox" name="vehicle" value="Bike">
              Enable
               <input type="text" placeholder="Promocode" />
              <label for="email" class="mg_le">Credit</label>
              <input type="text" placeholder="DeziCredit" />
            </div>
            <div class="form-group fl">
              <label for="pwd" >till</label>
              <input type="text" placeholder="14/02/2006">
              <i class="fa fa-calendar" aria-hidden="true"></i> </div>
            <button class="promo_code_btn"> Add New Promo</button>
          </form>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <form role="form">
            <label for="email" class="fl lbl_center">Driver User Referral</label>
            <div class="form-group psng_dri">
              <input type="checkbox" name="vehicle" value="Bike">
              Enable
              <label for="email" class="mg_le">Amount received for referal</label>
              <input type="text" placeholder="DeziCredit" />
            </div>
            <label for="email" style="float:right; margin-right:25px; font-weight:normal; font-size:11px;">for 5/10 rides</label>
            <div class="form-group fl psng_dri">
              <input type="checkbox" name="vehicle" value="Bike">
              Enable
              <label for="email" class="mg_le">Amount received for referal</label>
              <input type="text" placeholder="DeziCredit" />
                 <label for="email" style="float:right; margin-right:25px; font-weight:normal; font-size:11px;">for 20 rides</label>
            </div>
            
          
          </form>
        </div>
        <div class="upload_btn">
          <button> Save </button>
          <button> Cancel </button>
        </div>
      </div>
    </div>
  </div>
</div>




@stop


