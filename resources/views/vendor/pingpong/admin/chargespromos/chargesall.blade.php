@extends($layout)
@section('title', 'All Charges')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Charges' !!} 

	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop


@section('content')
<div class="blade-3">
<div class="content-part container-part-1">
<div class="col-sm-12 new-city-box">
<div class="search-box pull-left">
<input type="search">
</div>
<div class="button-left pull-right">
	<button type="submit" class="btn btn-default">Select All</button>
</div>

              <table class="table table-hover">
                <tbody><tr>
                  <th>A</th>
                  <th>B</th>
                  <th>C</th>
                  <th>D</th>
                  <th>E</th>
				  <th>F</th>
                  <th>G</th>
                  <th>H</th>
                  <th>I</th>
                </tr>
                <tr>
                  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
                </tr>
				  <tr>
                  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
                </tr>
				  <tr>
                  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
                </tr>
				  <tr>
                  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
				  <td><div class="checkbox"><label><input type="checkbox">Boston</label></div></td>
                </tr>
              </tbody>
			  </table>
<div class="col-sm-12 new-city-butt">
<button type="submit" class="btn btn-default">Done</button>
</div>
</div>
<div class="col-sm-12 current-price pad">
<div class="col-sm-5">
<div class="col-sm-8 pad">
<p>Current Price Set Time 12-10-16;1400hrs</p>
<p>Current Price Set Time 12-10-16;1400hrs</p>
</div>
<div class="col-sm-4 schdule pad">
<button type="submit" class="btn btn-default">Price Schdule</button>
</div>
</div>
<div class="col-sm-7 date-label">
<div class="form-group fl w100">
<label>From date</label>
    <input type="text" id="startDate" readonly="true" placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
	<div class="form-group fl w100">
    <label for="pwd" class="l_blck">TO</label>
    <input type="text" id="endDate" readonly="true" placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
<div class="dt-buttons"><a class="dt-button buttons-pdf buttons-html5 btn btn-default color-blue" tabindex="0" aria-controls="Passengertable"><span>Download Change Log</span></a></div>


</div>
<div class="col-sm-8 driver-charge-main">
<p>Driver Charges</p>
<div class="form-group col-sm-6 driver-charge">
                  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
				  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
                </div>
				<div class="form-group col-sm-6">
                  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
				  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
                </div>
</div>
<div class="col-sm-4">
<p>Driver Charges</p>
<div class="form-group driver-charge-main">
                  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
				  <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
				   <input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
                </div>
</div>

</div>
</div>
<div class="content-part charge-blad rajat-form-group">
      <div class="col-sm-12 blade-all-main">
	    <div class="form-group week-form">
            <div class="check-butt ">
			<div class="btn-group" data-toggle="buttons">
  <label class="btn btn-primary active circle">
    <input type="radio" name="options" id="option1" autocomplete="off" checked><span>S</span>
  </label>
  <label class="btn btn-primary circle">
    <input type="radio" name="options" id="option2" autocomplete="off"><span> M</span>
  </label>
  <label class="btn btn-primary circle">
    <input type="radio" name="options" id="option3" autocomplete="off"><span> T</span>
  </label>
    <label class="btn btn-primary active circle">
    <input type="radio" name="options" id="option1" autocomplete="off" checked> <span>W</span>
  </label>
  <label class="btn btn-primary circle">
    <input type="radio" name="options" id="option2" autocomplete="off"><span> T</span>
  </label>
  <label class="btn btn-primary circle">
    <input type="radio" name="options" id="option3" autocomplete="off"><span> F</span>
  </label>
    <label class="btn btn-primary circle">
    <input type="radio" name="options" id="option3" autocomplete="off"><span> S</span>
  </label>
</div>
			
                  
            </div>

</div>
<div class="col-sm-12">
<div class="col-sm-6">
<div class="col-sm-10">
<input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
</div>
<div class="col-sm-2 clock-pad">
<i class="fa fa-clock-o" aria-hidden="true"></i>
</div>

</div> 
<div class="col-sm-6">
<div class="col-sm-10">
<input class="form-control" id="exampleInputEmail1" placeholder="Cost Per Mile" type="text">
</div>
<div class="col-sm-2 clock-pad">
<i class="fa fa-clock-o" aria-hidden="true"></i>
</div>

</div> 
</div>
<div class="col-sm-12 Cost-per-mile">
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="email">
                  </div>
                </div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="text">
                  </div>
                </div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="text">
                  </div>
                </div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="text">
                  </div>
                </div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="text">
                  </div>
                </div>
				<div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>

                  <div class="col-sm-10">
                    <input class="form-control" id="inputEmail3" placeholder="Email" type="text">
                  </div>
                </div>
</div>        
</div>
</div>
	<div class="col-sm-12 trigger-main">
		<button type="submit" class="btn btn-default trigger-but1">Update</button>
		<button type="Reset" class="btn btn-primary undo">Undo Changes</button>
		</div>
		</div>
@stop

@section('customjavascript')
<script>
$('.undo').click(function(){
	alert(7);
$( ":input" ).val()='';
});
</script>
@stop

