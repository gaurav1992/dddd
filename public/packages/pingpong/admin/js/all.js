function convertToSlug(Text) {
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
}

$('[name=title], [name=name]').on('keyup', function() {
    var title = $(this).val();
    var slug = convertToSlug(title);

    $('[name=slug]').val(slug);

    $('.slug-preview').text(slug);

});



/**
 *VALIDATING DATE RANGE
 *
 */

function validateDateRange(startDateId, endDateId) {

    var start = new Date();
    // set end date to max one year period:
    var end = new Date(new Date().setYear(start.getFullYear() + 1));

    $('#' + startDateId).datepicker({
        // update "toDate" defaults whenever "fromDate" changes
    }).on('changeDate', function() {
        // set the "toDate" start to not be later than "fromDate" ends:
        $('#' + endDateId).datepicker('setStartDate', new Date($(this).val()));
    });

    $('#' + endDateId).datepicker({
        startDate: start,
        endDate: end
            // update "fromDate" defaults whenever "toDate" changes
    }).on('changeDate', function() {
        // set the "fromDate" end to not be later than "toDate" starts:
        $('#' + startDateId).datepicker('setEndDate', new Date($(this).val()));
    });


}
function emptyinput()
{
	
	$('input[name="cost_per_mile"]').val("0");
	$('input[name="per_min_charge"]').val("0");
	$('input[name="less_mile_travel_cost"]').val("0");
	$('input[name="greater_mile_travel_cost"]').val("0");
	$('input[name="service_charge"]').val("0");
	$('input[name="min_charge"]').val("0");
	$('input[name="cancelation_charge"]').val("0");
	$('input[name="day_start_time"]').val("00:00");
	$('input[name="day_end_time"]').val("00:00");
}
function AjaxCallForDaysCharges(city_id,dayid,CSRF_TOKEN)
{
	
	jQuery.ajax({
                type: "post",
                dataType: "json",
                url: wkdayChargesUrl,
                data: {
                    _token: CSRF_TOKEN,
                    CityId: city_id,
                    dayid: dayid
                },
                //if using the form then use this
                //data : serializedData,
                success: function(response) {
					//$('#divLoading').remove();
                    if (response != '') {
                        console.log("response" + response);
                        $('#jsonData').val(JSON.stringify(localStorage));
                    }
                    //var parsedData=jQuery.parseJSON(response);
                     console.log(response);
                    // alert(response.city_id);
					
						$('input[name="day_number"]').val(dayid);
                        $('input[name="city_id"]').val(city_id);
                    if (response.city_id != '') {
						
                        $('input[name="cost_per_mile"]').val(response.cost_per_mile);
                        $('input[name="id"]').val(response.id);
                        $('input[name="per_min_charge"]').val(response.per_min_charge);
                        $('input[name="less_mile_travel_cost"]').val(response.less_mile_travel_cost);
                        $('input[name="greater_mile_travel_cost"]').val(response.greater_mile_travel_cost);
                        $('input[name="service_charge"]').val(response.service_charge);
                        $('input[name="min_charge"]').val(response.min_charge);
                        $('input[name="cancelation_charge"]').val(response.cancelation_charge);
                        $('input[name="day_start_time"]').val(response.from_time);
                        $('input[name="day_end_time"]').val(response.to_time);
                        //child.prop('checked',true);
                        //label.removeClass('active');
                    }
                    if (response == '') {
						//$('input[name="day_number"]').val(dayid);
                        emptyinput();
                    }
                },
                failure: function(errMsg) {
                    console.log(errMsg);
                }
            });


}

function updateDaysCharges(){
	request={};
	request['id']=$('input[name="id"]').val();
	request['day_number']=$('input[name="day_number"]').val();
	request['city_id']=$('input[name="city_id"]').val();
	request['cost_per_mile']=$('input[name="cost_per_mile"]').val();
    request['per_min_charge']=$('input[name="per_min_charge"]').val();
    request['less_mile_travel_cost']=$('input[name="less_mile_travel_cost"]').val();
    request['greater_mile_travel_cost']=$('input[name="greater_mile_travel_cost"]').val();
    request['service_charge']= $('input[name="service_charge"]').val();
    request['min_charge']= $('input[name="min_charge"]').val();
    request['cancelation_charge']= $('input[name="cancelation_charge"]').val();
    request['from_time']= $.trim($('input[name="day_start_time"]').val());
    request['to_time']=  $.trim($('input[name="day_end_time"]').val());
    request['is_active']=  '1';
	console.log(JSON.stringify(request));
	
	
	jQuery.ajax({
                type : "post",
                dataType : "json",
                url : driverChargeUpdateURL, 
                data : { request : JSON.stringify(request),_token: CSRF_TOKEN },
                //if using the form then use this
                //data : serializedData,
                success: function(response){
                    console.log(response);
                    }
        			
        			});
}

$(document).ready(function() {

    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-z\s]+$/i.test(value);
    }, "Only alphabetical characters");

    /**
     * Driver Charges: driverCharges 
     **/
    var flag = true;
    $(document).on("click", "#price_schdule", function() {
		 $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
        $('.price_schdule').show(700);
        $('.updateCharges').prop('disabled', true);
        $('.updateCharges').hide();
        
        
        $('input[name="is_schedule_charges"]').val('1');
		
		if($('.dayTab').parent().hasClass('active'))
		{   
			
			var refr=$('.dayTab').attr('href');
			//alert(refr);
			$(refr).html(html);
			
			setTimeout(function(){
				$("#from_time").wickedpicker({twentyFour: false});
				$("#to_time").wickedpicker({twentyFour: false});
			},1000);
			
			
		}
		$('input[name="city_id[]"]:checked').each(function() {

                city_id = this.value;
            });
		//var dayid=$(this).data('value');
		AjaxCallForDaysCharges(city_id,'1',CSRF_TOKEN);
       setTimeout(function(){$('#divLoading').remove();	 },1000);
		

    });
    $(document).on("click", ".close-me", function() {
        $('.price_schdule').hide(700);
        $('input[name="is_schedule_charges"]').val('0');
		$('.updateCharges').prop('disabled', false);
		$('.updateCharges').show();
    });

    //Passenger Dezicredit Form
	$('#price_schdule').hide();
    $(document).on('click', '.view-charges', function(e) { 
	// alert(5);//use on if jQuery 1.7+
      $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
	  e.preventDefault(); //prevent form from submitting
        countCity = 0;
		
        $('input[name="city_id[]"]:checked').each(function() {
            countCity++;
            city_id = this.value;
        });
        if (countCity == 1) {

            var CSRF_TOKEN = $(".driver-charges input[name='_token']").val();
            var getCityCharges = city_id;

            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: viewCityChargesURL,

                data: {
                    _token: CSRF_TOKEN,
                    getCityCharges: getCityCharges
                },

                //if using the form then use this
                //data : serializedData,

                success: function(response) {

                    if (response.azStatus == 'success') {
						
                        $('#default_cost_per_mile').val(response.view_city_charges.cost_per_mile);
                        $('#default_per_min_charge').val(response.view_city_charges.per_min_charge);
                        $('#default_less_mile_travel_cost').val(response.view_city_charges.less_mile_travel_cost);
                        $('#default_greater_mile_travel_cost').val(response.view_city_charges.greater_mile_travel_cost);
                        $('#default_service_charges').val(response.view_city_charges.service_charge);
                        $('#default_min_charge').val(response.view_city_charges.min_charge);
                        $('#default_cancelation_charge').val(response.view_city_charges.cancelation_charge);
						
						$('.view-charges').hide();
						$('#price_schdule').show();
                    } else {
                        alert(response.azMessage);
                        $('.driver-charges').find("input[type=text], textarea").val("");
                    }
                },
                failure: function(errMsg) {
                    alert(errMsg);
                }
            });

        } else {
            alert('please select only one city to view the charges or download the log file to view all the city charges.');
        }

		setTimeout(function(){$('#divLoading').remove();	 },1000);
        return false;
    });

    // Particular day rate
	//$(".Priceform,.selectday").hide();
	
	var html='<div class="col-sm-12 Priceform">\
      <div class="col-sm-6">\
        <div class="col-sm-10">\
		<input type="hidden"  name="day_number"  id="day_number">\
		<input type="hidden"  name="city_id">\
		<input type="hidden"  name="id">\
          <input name = "day_start_time" value="" class="form-control" id="from_time" placeholder="Start Time" type="text" readonly>\
        </div>\
        <div class="col-sm-2 clock-pad">\
          <i class="fa fa-clock-o" aria-hidden="true"></i>\
        </div>\
      </div> \
      <div class="col-sm-6">\
        <div class="col-sm-10">\
          <input name="day_end_time" value="" class="form-control" id="to_time" placeholder="End time" type="text" readonly>\
        </div>\
        <div class="col-sm-2 clock-pad">\
          <i class="fa fa-clock-o" aria-hidden="true"></i>\
        </div>\
      </div> \
    </div>\
    <div class="col-sm-12 Cost-per-mile Priceform">\
      <div class="form-group">\
        <label for="inputEmail3" class="col-sm-2 control-label">Cost per mile</label>\
        <div class="col-sm-10">\
          <input name="cost_per_mile" value="" class="form-control" id="cost_per_mile" placeholder="Cost Per Mile" type="text">\
        </div>\
      </div>\
\
	  <div class="form-group">\
        <label for="inputEmail3" class="col-sm-2 control-label">Service Charge</label>\
        <div class="col-sm-10">\
          <input name="service_charge" value="" class="form-control" id="service_charge" placeholder="Service Charge" type="text">\
        </div>\
      </div>\
			\
      <div class="form-group">\
        <label for="inputEmail3" class="col-sm-2 control-label">Cancellation Charge</label>\
        <div class="col-sm-10">\
<input name="cancelation_charge" value="" class="form-control" id="cancelation_charge" placeholder="Cancelation Charge" type="text">\
        </div>\
      </div>\
\
			 <div class="form-group">\
          <label for="inputEmail3" class="col-sm-2 control-label">Per Min charges</label>\
          <div class="col-sm-10">\
            <input name="per_min_charge" value="" class="form-control" id="per_min_charge" placeholder="Per Min charges" type="text">\
          </div>\
        </div>\
\
				<div class="form-group">\
          <label for="inputEmail3" class="col-sm-2 control-label">Cost For &lt 2mi Driver travel</label>\
          <div class="col-sm-10">\
            <input name="less_mile_travel_cost" value="" class="form-control" id="less_mile_travel_cost" placeholder="Cost For &lt 2mi Driver travel" type="text">\
          </div>\
        </div>\
\
        <div class="form-group">\
          <label for="inputEmail3" class="col-sm-2 control-label">Cost For &gt 2mi Driver travel</label>\
          <div class="col-sm-10">\
            <input name="greater_mile_travel_cost" value="" class="form-control" id="greater_mile_travel_cost" placeholder="Cost For &gt 2mi Driver travel" type="text">\
          </div>\
        </div>\
\
				<div class="form-group">\
          <label for="inputEmail3" class="col-sm-2 control-label">Minimum Charge</label>\
          <div class="col-sm-10">\
            <input name="min_charge" value="" class="form-control" id="min_charge" placeholder="Min Charge" type="text">\
          </div>\
	<div class="col-sm-12 trigger-main">\
  	<a  class="btn btn-default trigger-but1 upDaysCharges" href="javascript:void(0)">Update</a>\
	</div>\
        </div></div> ';
	
	
			// Run code
	var lastclicked=1;
	$('.dayTab').on('click',function(){
		
		 var currentTab=$(this);
		
		 //alert(lastclicked);
		 var r = confirm("Are you sure, you want to Switch the Day?");
		 console.log(r);
		 $('input[name="city_id[]"]:checked').each(function() {

			city_id = this.value;
				});
		 if(r==true){

				$('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
				updateDaysCharges();
				var href=$(this).attr('href');
				 //alert(html);
				$('.tab-pane').html('');
				$(href).html(html);
				setTimeout(function(){
				$("#from_time").wickedpicker({twentyFour: false});
				$("#to_time").wickedpicker({twentyFour: false});
				},1000);
				var dayid=$(this).data('value');
				AjaxCallForDaysCharges(city_id,dayid,CSRF_TOKEN);
				setTimeout(function(){$('#divLoading').remove();},1000);
				//generate('success','You have successfully updated the charges for this day!!');
				//alert('You have successfully updated the charges for this day!!');
		 }else{
		
			return false;
			 
		 }
		lastclicked=$(this).data('value');
		 
		
	});
	
	
	$('body').on('click','.upDaysCharges',function(){
		var r = confirm("Are you sure, you want to save the charges?");
		if(r==true){
			$('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
			$('input[name="city_id[]"]:checked').each(function() {
			    var	city_id = this.value;
					});

			if($('.listTab').hasClass('active')){
				var day_id=$('li.active').find('a.dayTab').attr('data-value');	
				var href=$('li.active').find('a.dayTab').attr('href');
			}
        updateDaysCharges();
		
				 //alert(html);
				$('.tab-pane').html('');
				$(href).html(html);
				
				
				AjaxCallForDaysCharges(city_id,day_id,CSRF_TOKEN);
				setTimeout(function(){$('#divLoading').remove();},1000);
	
		}else{
			return false;
		}
		//console.log($('li.active').children().attr('data-value'));	
	});
	
	var localstore=[];
    $(document).on('click', '.selectday', function(e) { //use on if jQuery 1.7+
        //console.log(155454);
        //$(this).children('.selectday').trigger( "click" );

        console.log($(this).is(":checked"));
        if ($(this).is(":checked") == false) {
            $(this).parent().removeClass('active');
			$(".Priceform").hide();
        } else if ($(this).is(":checked") == true) {
            $(this).parent().addClass('active');
			$(".Priceform").show();
            $('input[name="city_id[]"]:checked').each(function() {

                city_id = this.value;
            });
            var dayid = $(this).val();
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: wkdayChargesUrl,
				 beforeSend: function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
				},
                data: {
                    _token: CSRF_TOKEN,
                    CityId: city_id,
                    dayid: dayid
                },
                //if using the form then use this
                //data : serializedData,
                success: function(response) {
					$('#divLoading').remove();
                    if (response != '') {
                        console.log("response" + response);
                        $('#jsonData').val(JSON.stringify(localStorage));
                    }
                    //var parsedData=jQuery.parseJSON(response);
                    // console.log(response);
                    // alert(response.city_id);
                    //json_decode(resopnse);
                    if (response.city_id != '') {
						localstore.push(response);
                        $('input[name="cost_per_mile"]').val(response.cost_per_mile);
                        $('input[name="per_min_charge"]').val(response.per_min_charge);
                        $('input[name="less_mile_travel_cost"]').val(response.less_mile_travel_cost);
                        $('input[name="greater_mile_travel_cost"]').val(response.greater_mile_travel_cost);
                        $('input[name="service_charge"]').val(response.service_charge);
                        $('input[name="min_charge"]').val(response.min_charge);
                        $('input[name="cancelation_charge"]').val(response.cancelation_charge);
                        $('input[name="day_start_time"]').val(response.from_time);
                        $('input[name="day_end_time"]').val(response.to_time);
                        //child.prop('checked',true);
                        //label.removeClass('active');
                    }
                    if (response == '') {
                        $('input[name="cost_per_mile"]').val("0");
                        $('input[name="per_min_charge"]').val("0");
                        $('input[name="less_mile_travel_cost"]').val("0");
                        $('input[name="greater_mile_travel_cost"]').val("0");
                        $('input[name="service_charge"]').val("0");
                        $('input[name="min_charge"]').val("0");
                        $('input[name="cancelation_charge"]').val("0");
                        $('input[name="day_start_time"]').val("00:00");
                        $('input[name="day_end_time"]').val("00:00");
                    }
                },
                failure: function(errMsg) {
                    console.log(errMsg);
                }
            });

        }

    });
		
	
		$('body').on('click','#to_time, #from_time',function(){
			$(this).wickedpicker({twentyFour: false});
		});
   

    /**
     * Validation for passenger promos section in admin side 
     */
    $("#riderpromos").validate({
        rules: {
            referal_credit_for_5_10: {
                number: true,
                maxlength: 5
            },
            referal_credit_for_20: {
                number: true,
                maxlength: 5
            }
        },
        //errorContainer: "#errorContainer", 
        //errorElement: "li",
        //errorPlacement: function(error, element) {
        //alert(error);
        //error.appendTo("#errorContainer");
        //$('#errorContainer').addClass('alert alert-danger');

        //},
        messages: {
            referal_credit_for_5_10: {
                number: 'Please enter valid credit.'
            },
            referal_credit_for_20: {
                number: 'Please enter valid credit.'
            },

        }
    });

    /**
     * Validation for passenger promos section in admin side 
     */
    /*
	$("#passengerpromos").validate({
			rules: {
				referal_credit:{
					number:true,
					maxlength: 5
					},
				anniversary_promo_credit:{
					number:true,
					maxlength: 5
					},
				birthday_promo_credit:{
					number:true,
					maxlength: 5
					},
				new_ride_promo_credit:{
					number:true,
					maxlength: 5
					},
				promo_credit:{
					number:true,
					maxlength: 5
				},							
			},
			errorContainer: "#errorContainer", 
       		errorElement: "li",
			errorPlacement: function(error, element) {
			//alert(error);
				error.appendTo("#errorContainer");
				$('#errorContainer').addClass('alert alert-danger');
				//error.appendTo("#errorContainer")
               // if (element.attr("name") == "DOB" )
               // {
                  // error.appendTo("#error");
               // }else{
					//error.insertAfter(element)
			},
			messages: {
				referal_credit:{
					number:'Please enter valid referal credit.'
				},
				anniversary_promo_credit:{
					number:'Please enter valid anniversary credit.'
				},
				birthday_promo_credit:{
					number:'Please enter valid birthday credit.'
				},
				new_ride_promo_credit:{
					number:'Please enter valid ride credit.'
				},
				promo_credit:{
					number:'Please enter valid promo credit.'
				},
			}
			
		});*/

    /*****************************/
    /**
     * Passengers Table 
     **/
    if (typeof indexUrl2 !== 'undefined') {
        var Ntable = $('#Passengertable').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "responsive": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": indexUrl2,
                "data": function(d) {
                    d.state = $('#state').val();
                    d.city = $('#city').val();
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                    d.billingval1 = $('#billingRange1').val();
                    d.billingval2 = $('#billingRange2').val();
                    d.revokedBy = $('#revokedBy').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#Passengertable td').hasClass('dataTables_empty')) {
                    jQuery('.b2,#Passengertable_paginate').hide();
                } else {
                    jQuery('.b2,#Passengertable_paginate').show();
                }
            }

        });
    }
    /**
     * Suspended Passengers Table 
     **/
    if (typeof SPList !== 'undefined') {
        var Sptable = $('#SpList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": SPList,
                "data": function(d) {
                    d.state = $('#Spstate').val();
                    d.city = $('.Spcity').val();
                    d.startDate = $('#SpstartDate').val();
                    d.endDate = $('#SpendDate').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#SpList td').hasClass('dataTables_empty')) {
                    jQuery('.b2').hide();
                } else {
                    jQuery('.b2').show();
                }
            }

        });
    }
    /**
     *CHANGED POSITION OF GENERATE REPORT BUTTON 
     **/
    if ($('#Passengertable').length) {
        var tableTools = new $.fn.dataTable.Buttons(Ntable, {
            buttons: [{
                extend: 'pdf',
                text: 'Generate Report',
                className: 'btn btn-default color-blue b2 genRepoBtn tp_mrgn',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }]

        });
        console.log(tableTools.container());
        //$("<p>hfgfhgfgffjf</p>").insertAfter('#billing');	
        $(tableTools.container()).insertAfter('#billing');

    } 

    if ($('#SpList').length) {
        var tableTools = new $.fn.dataTable.Buttons(Sptable, {
            buttons: [{
                extend: 'pdf',
                text: 'Generate Report',
                className: 'btn btn-default color-blue b2 genRepoBtn',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }]

        });
        //console.log(tableTools);
        $(tableTools.container()).insertAfter('#driverlist');
    }


    /**
     * SEARCHING ON STATE 
     **/
    $('#state').on('change', function() {
        $('#city').val('');
        Ntable.ajax.reload();
    });

    $('#revokedBy').on('change', function() {

        Ntable.ajax.reload();
    });

    $('#billingRange1,#billingRange2').on('change', function() {
        Ntable.ajax.reload();
    });
    //$('#state').on('click',function(){Ntable.ajax.reload();});
    $('#Spstate').on('change', function() {
        Sptable.ajax.reload();
    });


    /**
     * SEARCHING ON CITY 
     **/
    $('#city').on('change', function() {
        Ntable.ajax.reload();
    });
    $('#Spcity').on('change', function() {
        Ntable.ajax.reload();

    });

    $('.Spcity').on('change', function() {

        Sptable.ajax.reload();
    });
	
	
    /*****************************/
    /**
     * driverApplicantList 
     **/
    if (typeof driverUrl !== 'undefined') {
        var oTable = $('#driverApplicantList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": driverUrl,
                "data": function(d) {
                    d.state = $('#state').val();
                    d.city = $('#city').val();
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                }

            }

        });
    }
    /**
     *CHANGED POSITION OF GENERATE REPORT BUTTON 
     **/
    if ($('#driverApplicantList').length) {
        var tableTools = new $.fn.dataTable.Buttons(oTable, {
            buttons: [{
                extend: 'pdf',
                text: 'Generate Report',
                className: 'btn btn-default color-blue genRepoBtn',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }]

        });
        //console.log(tableTools);
        $(tableTools.container()).insertAfter('#billing');
    }
    /*****************************/
    /**
     * rejectedDriverUrl 
     **/
    if (typeof rejectedDriverUrl !== 'undefined') {

        var RdoTable = $('#rejectedDriverList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "responsive": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": rejectedDriverUrl,
                "data": function(d) {
                    d.state = $('#state').val();
                    d.city = $('#city').val();
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#rejectedDriverList td').hasClass('dataTables_empty')) {
                    jQuery('.b2').hide();
                } else {
                    jQuery('.b2').show();
                }
            }

        });
    }

    /**
     *CHANGED POSITION OF GENERATE REPORT BUTTON 
     **/
    if ($('#rejectedDriverList').length) {
        var tableTools = new $.fn.dataTable.Buttons(RdoTable, {
            buttons: [{
                extend: 'pdf',
                text: 'Generate Report',
                className: 'btn btn-default color-blue b2 genRepoBtn',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }]

        });
        //console.log(tableTools);
        $(tableTools.container()).insertAfter('#billing');
    }
    /*****************************/
    /**
     * New Driver URL 
     **/
    if (typeof newDriverUrl !== 'undefined') {
        var NdoTable = $('#newDriverList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "responsive": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": newDriverUrl,
                "data": function(d) {
                    d.state = $('#state').val();
                    d.city = $('#city').val();
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#newDriverList td').hasClass('dataTables_empty')) {
                    jQuery('.b2').hide();
                } else {
                    jQuery('.b2').show();
                }
            }


        });
    }
    
     /**
     *SUSPENDED DRIVER LISTING START
     **/
	if (typeof newSuspendedListAjax !== 'undefined') {
        var newSuspendedListTable = $('#newSuspendedList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "responsive": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": newSuspendedListAjax,
                "data": function(d) {
                    d.state = $('#dl_state').val();
                    d.city = $('#dl_city').val();
                    d.startDate = $('#dl_startDate').val();
                    d.endDate = $('#dl_endDate').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#newSuspendedList td').hasClass('dataTables_empty')) {
                    jQuery('.b2').hide();
                } else {
                    jQuery('.b2').show();
                }
            }


        });
    }
    
	$('#dl_state').on('change', function() {
		$('#dl_city').val('');
		newSuspendedListTable.ajax.reload();
	}); 
	$('#dl_city').on('change', function() {
		newSuspendedListTable.ajax.reload();
	});
	 $(document).on('change', '#dl_startDate, #dl_endDate', function() {
		 
            newSuspendedListTable.ajax.reload();
        }); 
    /**
     *SUSPENDED DRIVER LISTING END
     **/
     
     
      /**
     *DOCUMENT REVIEW OF SUSPENDED DRIVER LISTING START
     **/
	if (typeof newdocumentReviewListAjax !== 'undefined') {
        var newdocumentReviewList = $('#newdocumentReviewList').DataTable({
            "language": {
                "paginate": {
                    "previous": "<<",
                    "next": ">>"

                }
            },
            "processing": true,
            "responsive": true,
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }],
            "serverSide": true,
            "ajax": {
                "url": newdocumentReviewListAjax,
                "data": function(d) {
                    d.state = $('#drsl_state').val();
                    d.city = $('#drsl_city').val();
                    d.startDate = $('#drsl_startDate').val();
                    d.endDate = $('#drsl_endDate').val();
                }

            },
            "fnDrawCallback": function() {
                if (jQuery('table#newdocumentReviewList td').hasClass('dataTables_empty')) {
                    jQuery('.b2').hide();
                } else {
                    jQuery('.b2').show();
                }
            }


        });
    }
    
	$('#drsl_state').on('change', function() {
		$('#drsl_city').val('');
		newdocumentReviewList.ajax.reload();
	}); 
	$('#drsl_city').on('change', function() {
		newdocumentReviewList.ajax.reload();
	});
	 $(document).on('change', '#drsl_startDate, #drsl_endDate', function() {
		 
            newdocumentReviewList.ajax.reload();
        });
    /**
    *DOCUMENT REVIEW OF SUSPENDED DRIVER LISTING END
     **/
     
     
    /**
     *CHANGED POSITION OF GENERATE REPORT BUTTON 
     **/
    if ($('#newDriverList').length) {
        var tableTools = new $.fn.dataTable.Buttons(NdoTable, {
            buttons: [{
                extend: 'pdf',
                text: 'Generate Report',
                className: 'btn btn-default color-blue b2 genRepoBtn',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7]
                }
            }]
        });

        //console.log(tableTools);
        $(tableTools.container()).insertAfter('#billing');
    }

    /*****************************/
    /**
     * Cars Table 
     **/
    var carTable = $('#carTable').DataTable({
        "bPaginate": false,
        "columns": [{
            "data": "S.NO."
        }, {
            "data": "Make"
        }, {
            "data": "Model"
        }, {
            "data": "License No."
        }, {
            "data": "Transmission"
        }, {
            "data": "Action"
        }]

    });

    if ($('#carTable').length) {
        var tableTools = new $.fn.dataTable.Buttons(carTable, {
            buttons: [{
                extend: 'pdf',
                text: 'Download',
                className: 'color-blue genRepoBtn',
                exportOptions: {
                    columns: [1, 2, 3, 4]
                }
            }]

        });
        //console.log(tableTools);
        $(tableTools.container()).appendTo('.carsReport');
    }

    /*****************************/
    /**
     * DATEPICKER HIDE CODE ON CHANGE DATE 
     **/
    var minDate = new Date();
    $(function() {
        $("#startDate,#endDate,#DOB,#startDatepdf,#SpstartDate,#SpendDate,#endDatepdf,#dl_startDate,#dl_endDate").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");

            });

        });

        $(document).on('change', '#startDate, #endDate', function() {
            Ntable.ajax.reload();
        });
        
        /*
		     $("#promo_till_date").each(function () {
		        $(this).datepicker().on('changeDate', function (ev) {
		            $(this).datepicker("hide");
		        });
		    });
			
			*/
			$(document).on('change', '#SpstartDate, #SpendDate', function () {
					
		       Sptable.ajax.reload();
		       
		    }); 

    });

    $(function() {
        // $( "#DOB,#Anniversary" ).datepicker();
        var start = new Date();
        // set end date to max one year period:
        var end = new Date(new Date().setYear(start.getFullYear() + 1));

        $('#startDate,#startDatepdf,#SpstartDate').datepicker({
            // update "toDate" defaults whenever "fromDate" changes
        }).on('changeDate', function() {
            // set the "toDate" start to not be later than "fromDate" ends:
            $('#endDate,#SpendDate,#endDatepdf').datepicker('setStartDate', new Date($(this).val()));
        });

        $('#endDate,#SpendDate,#endDatepdf').datepicker({
            startDate: start,
            endDate: end
                // update "fromDate" defaults whenever "toDate" changes
        }).on('changeDate', function() {
            // set the "fromDate" end to not be later than "toDate" starts:
            $('#startDate,#startDatepdf,#SpstartDate').datepicker('setEndDate', new Date($(this).val()));
        });
    });

    /**
     * Ajax call for suspend and active passengers
     **/
    $(document).on("click", ".driver_suspend,.passenger_Active", function() {

        if ($(this).hasClass('driver_suspend')) {
            var r = confirm("Are you sure, you want to suspend this account?");
        } else if ($(this).hasClass('passenger_Active')) {
            var r = confirm("Are you sure, you want to activate this account?");
        }

        if (r == true) {
            var id = $(this).data('userid');
            var cell = $(this);
            var RowId = $(this).closest("tr").find('td:eq(8)');
            //console.log(id);
            //alert( RowId );
            var dataAction = $(this).data('action');
            // alert(dataAction);
            $.ajax({
                type: 'post',
                url: suspendUrl,
                data: 'action=' + dataAction + '&id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr){
                    /// alert(loadingImage);
                    //$('.actions li:first-child a').hide();
                    $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                    //return false;
                },
                success: function(returnData) {
                    $('#loadering').remove();
                    console.log(returnData);
                    if (returnData == 'suspendSuccess') {

                        RowId.html('Suspended');
                        $(".statusClass").html('<label> Status : </label> Suspended');
                        cell.html('Activate');
                        cell.removeClass("driver_suspend").addClass("passenger_Active");
                        cell.removeClass("btn-primary").addClass("btn-success");
                        //cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
                        cell.data('action', "passenger_Active");
                        //console.log(RowId);
                    }
                    if (returnData == 'activeSuccess') {
                        if (typeof SPList !== 'undefined') {
                            cell.parent().parent().parent().hide();
                            return false;
                        }
                        RowId.html('Active');

                        $(".statusClass").html('<label> Status : </label> Active ');
                        cell.html('Suspend');
                        cell.removeClass("passenger_Active").addClass("driver_suspend");
                        cell.removeClass("btn-success").addClass("btn btn-primary");
                        //cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
                        cell.data('action', "driver_suspend");
                        //console.log(RowId);
                    }
                }
            });
        }
    });
    /**
     * Ajax call for suspend and active driver
     **/
    $(document).on("click", ".driver_approve, .driver_disapprove, .btnDriver_suspend, .btnDriver_approve, .btnDriver_disapprove, .btnDriver_active", function() {
        if ($(this).hasClass('driver_approve') || $(this).hasClass('btnDriver_approve')) {
            var r = confirm("Are you sure, you want to approve this account ?");
        } else if ($(this).hasClass('driver_disapprove') || $(this).hasClass('btnDriver_disapprove')) {
            var r = confirm("Are you sure, you want to reject this account ?");
        } else if ($(this).hasClass('btnDriver_suspend')) {
            var r = confirm("Are you sure, you want to suspend this account ?");
        } else if ($(this).hasClass('btnDriver_active')) {
            var r = confirm("Are you sure, you want to activate this account ?");
        }
        if (r == true) {

            var id = $(this).data('userid');
            var cell = $(this);
            var RowId = $(this).closest("tr").find('td:eq(7)');
            //console.log(id);
            //alert( RowId );
            var dataAction = $(this).data('action');
            // alert(dataAction);
            $.ajax({
                type: 'post',
                url: driverActionUrl,
                data: 'action=' + dataAction + '&id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr) {
                    /// alert(loadingImage);
                    //$('.actions li:first-child a').hide();
                    $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                    //return false;
                },
                success: function(returnData) {
                    //alert(returnData);

                    $('#loadering').remove();
                    console.log(returnData);

                    if (returnData == 'suspendSuccess') {
                        alert('Suspended Successfully');
                        location.reload();

                        /*$('.btnDriver_suspend').html('User Suspended');
                        $('.btnDriver_suspend').css('background', '#dd4b39');
                        $('.btnDriver_suspend').attr('data-action', 'driver_approve');
								
                        $('.btnDriver_suspend').removeClass('btnDriver_suspend');
                        $('.btnDriver_suspend').addClass('btnDriver_active');*/

                    }
                    if (returnData == 'suspendSuccess') {
                        alert('Suspended Successfully');
                        location.reload();

                        /*$('.btnDriver_suspend').html('User Suspended');
                        $('.btnDriver_suspend').css('background', '#dd4b39');
                        $('.btnDriver_suspend').attr('data-action', 'driver_approve');
								
                        $('.btnDriver_suspend').removeClass('btnDriver_suspend');
                        $('.btnDriver_suspend').addClass('btnDriver_active');*/

                    }

                    if (returnData == 'approveSuccess') {
                        alert('Approved Successfully');
                        location.reload();

                        /*RowId.html('Active');
                        cell.html('Approved');
                        //cell.removeClass( "passenger_Active" ).addClass( "driver_suspend" );
                        cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
                        cell.removeAttr("data-action" ).attr( 'data-action',"driver_approve" );
                        cell.data( 'action',"driver_disapprove" );
                        //console.log(RowId);*/
                    }

                    if (returnData == 'btnapproveSuccess') {
                        alert('Approved Successfully');
                        // location.reload();
                        location.replace(document.referrer);

                        /*$('.btnDriver_approve').html('User Approved	');
                        $('.btnDriver_approve').css('background', '#00a65a');
                        $('.driver_approve').html('User Approved	');
                        $('.driver_approve').css('background', '#00a65a');*/


                    }
                    if (returnData == 'btndisapproveSuccess') {

                        alert('Disapproved Successfully');
                        location.replace(document.referrer);

                        /*$('.btnDriver_disapprove').html('User Disapproved	');
                        $('.btnDriver_disapprove').css('background', '#dd4b39');
                        $('.driver_disapprove').html('User Disapproved	');
                        $('.driver_disapprove').css('background', '#dd4b39');*/

                    }

                    if (returnData == 'btnactiveSuccess') {
                        alert('user Activated Successfully');
                        location.reload();
                        /*
						$('.btnDriver_active').html('User Activated	');
						$('.btnDriver_active').css('background', '#00a65a');*/

                    }

                    if (returnData == 'disapproveSuccess') {
                        alert('User Disapproved Successfully');
                        location.reload();
                        //NdoTable.ajax.reload();
                        /*RowId.html('Active');
                        cell.html('Disapproved');
                        //cell.removeClass( "passenger_Active" ).addClass( "driver_suspend" );
                        cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
                        cell.removeAttr("data-action" ).attr( 'data-action',"driver_disapprove" );
                        cell.data( 'action',"driver_approve" );*/
                        //console.log(RowId);
                    }

                    if (returnData == 'error') {
                        alert('error');
                        console.log('error');
                    }
                }
            });
        }
    });

    //delete driver

    $(document).on("click", ".driver_dlt,.passenger_dlt", function() {
        if ($(this).hasClass('driver_dlt')) {
            var r = confirm("Are you sure, you want to delete this driver ?");
        } else if ($(this).hasClass('passenger_dlt')) {
            var r = confirm("Are you sure, you want to delete this passenger ? ");
        }
        if (r == true) {

            var id = $(this).data('userid');
            var cell = $(this);
            var RowId = $(this).closest("tr").find('td:eq(7)');
            //console.log(id);
            //alert( RowId );
            var dataAction = 'dltSuccess';
            // alert(dataAction);
            $.ajax({
                type: 'post',
                url: dltDrive,
                data: 'action=' + dataAction + '&id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr) {
                    /// alert(loadingImage);
                    //$('.actions li:first-child a').hide();
                    $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                    //return false;
                },
                success: function(returnData) {
                    //alert(returnData);

                    $('#loadering').remove();
                    console.log(returnData);

                    if (returnData == 'dltSuccess') {
                        alert('Deleted Successfully');
                        location.reload();

                        /*$('.btnDriver_suspend').html('User Suspended');
                        $('.btnDriver_suspend').css('background', '#dd4b39');
                        $('.btnDriver_suspend').attr('data-action', 'driver_approve');
								
                        $('.btnDriver_suspend').removeClass('btnDriver_suspend');
                        $('.btnDriver_suspend').addClass('btnDriver_active');*/

                    }

                    if (returnData == 'error') {
                        alert('Something went wrong.Kindly referesh and try again.');

                    }
                }
            });
        }
    });
    //Delete driver  
    /**
     * Ajax call for suspend and active passengers
     **/
    $(document).on("click", ".subadmin_suspend, .subadmin_Active", function() {
        if ($(this).hasClass('subadmin_suspend')) {
            var r = confirm("Are you sure, you want to suspend this account ?");
        } else if ($(this).hasClass('subadmin_Active')) {
            var r = confirm("Are you sure, you want to activate this account ?");
        }
        if (r == true) {
            var id = $(this).data('userid');
            var cell = $(this);
            var RowId = $(this).closest("tr").find('td:eq(7)');
            //console.log(id);
            //alert( RowId );
            var dataAction = $(this).data('action');
            // alert(dataAction);
            $.ajax({
                type: 'post',
                url: suspendAdminUrl,
                data: 'action=' + dataAction + '&id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr) {
                    /// alert(loadingImage);
                    //$('.actions li:first-child a').hide();
                    $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                    //return false;
                },
                success: function(returnData) {
                    $('#loadering').remove();
                    //console.log(returnData);
                    if (returnData == 'suspendSuccess') {
                        RowId.html('Suspended');
                        cell.html('Activate');
                        cell.removeClass("subadmin_suspend").addClass("subadmin_Active");
                        cell.removeClass("btn-primary").addClass("btn-success");
                        //cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
                        cell.data('action', "subadmin_Active");
                        //console.log(RowId);
                    }
                    if (returnData == 'activeSuccess') {
                        RowId.html('Active');
                        cell.html('Suspend');
                        cell.removeClass("subadmin_Active").addClass("subadmin_suspend");
                        cell.removeClass("btn-success").addClass("btn btn-primary");
                        //cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
                        cell.data('action', "subadmin_suspend");
                        //console.log(RowId);
                    }
                }
            });
        }
    });


    /**
     ** GET CITY WITH AJAX CALL
     **/
    $(document).on("change", "#state,#dl_state,#drsl_state, #Spstate,#stateDriver", function() {

        var stateCode = $(this).val();
        //console.log(stateCode);
        $.ajax({
            type: 'post',
            url: homeUrl,
            data: 'stateCode=' + stateCode + '&_token=' + CSRF_TOKEN,
            beforeSend: function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
            },
            success: function(returnData) {
                $('#divLoading').remove();
                console.log(returnData);
                // var parsedJson=$.parseJSON(returnData);
                $("#city").html(returnData);
                $("#cityDriver").html(returnData);
                $(".Spcity").html(returnData);
                $("#drsl_city").html(returnData);
                $("#dl_city").html(returnData);

            }
        });
    });

    /**
     * FORM VALIDATION
     **/
	
    // validate signup form on keyup and submit
   var validateForm = $("#addUserForm,#editUser").validate({
		//onkeyup: function(element) {alert("hfdhgdhgfhg");$(element).valid();},  
        rules: {
            fname: {
                required: true,
                lettersonly: true,
            },
            lname: {
                required: true,
                lettersonly: true,
            },
            Email: {
                required: true,
                email: true
            },
            Phone: {
                required: true,
                minlength: 10
            },
            pwd: {
                required: true,
                minlength: 4,
                maxlength: 12,

            },
            DOB: "required",
            userType: "required"
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "DOB") {
                error.appendTo("#error");
            } else {
                error.insertAfter(element)
            }
        },
        messages: {
            fname: {
                required: "Please enter your firstname",
                lettersonly: "Only alphabets is allowed",
            },
            lname: {
                required: "Please enter your firstname",
                lettersonly: "Only alphabets is allowed",
            },
            Email: "Please enter a valid email address",
            Phone: "Please enter a valid phone number minimum of 10 digit",
            pwd: {
                required: "Please enter Password",
                minlength: "Password should be greater than 4 character",
                maxlength: "Password should be less than 12 character",

            },
            DOB: "Please enter your DOB",
            userType: "Please Select User Type"
        }
    });

//SSN update validation 




	/*
	 *VIEW BOX IS CHECKED WHEN CLICK ON EDIT CHECKBOX
	 */
	
	$('.pr_edit').on('click',function(){ 
	
		if($(this).is(":checked")==true){
			$(this).parent().prev().children().prop('checked', true);
			 $(this).parent().prev().children().prop('disabled', true);
			
		}
		
		if($(this).is(":checked")==false){
			$(this).parent().prev().children().prop('checked', false);
			$(this).parent().prev().children().removeAttr('disabled');
			
		}
	
	});
 
	 
	$('.pr_edit').each(function() {
						if($(this).is(":checked")==true){
						 $(this).parent().prev().children().prop('disabled', true);	
						}
					
					});
	
	
    /**
     * Code to hide permission panel 
     **/

    $('input:radio[name="userType"]').change(
        function() {
            if ($(this).is(':checked') && $(this).val() == 'superAdmin') {
                // append goes here
                $('input:checkbox').removeAttr('checked');
                $(".Auth").hide();
            } else {
                $(".Auth").show();
            }
        });
		
		$("#createUser").on('click',function(){ 
			var isEdit=false;
			var isView=false;
			
			if ($('#cstCareRadio').is(':checked')==true)
			{
				 
				 
					$('.pr_edit').each(function() {
						if($(this).is(":checked")==true){
							isEdit=true;
						}
					
					});
				   
					//alert(isEdit);
					$('.pr_view').each(function() {
					  if($(this).is(":checked")==true){
							isView=true;
					  }
					
					});
				   if(isEdit==true || isView==true)
				   {
					   
				   }
				   else
				   {
					alert('please select permissions'); 
				    return false;
				   }
				 
				
				
			}
			
			
		});
	
    /**
     * image preview
     **/
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profile_pic').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("input[name=image]").change(function() {
        readURL(this);
    });
    /**
     * Custom browse
     **/
    $("#browseImageBtn").click(function() {
        $("#imgbrowse").click();
    });

    // ***** SCRIPT HARISH ***** //

    // ***** SCRIPT CITY SELECT ALL ***** //

    $('.select-all-city').click(function(event) {
        event.preventDefault();
        var check_uncheck = $(this).attr('check-uncheck');
        if (check_uncheck === 'check') {
            $('.city-check').prop('checked', true);
            $(this).attr('check-uncheck', 'uncheck');
            $(this).html('Unselect All');
        } else {
            $('.city-check').prop('checked', false);
            $(this).attr('check-uncheck', 'check');
            $(this).html('Select All');
        }
    });


$('body').on('click','.city-check',function(){
			
	$('#price_schdule').hide();$('.view-charges').show();
	$('.price_schdule').hide(700);
        $('input[name="is_schedule_charges"]').val('0');
		$('.updateCharges').prop('disabled', false);
		$('.updateCharges').show();
		});
    /**
     * GET CITY WITH AJAX CALL
     **/
    $(document).on("keyup", "#search_city", function() {

        var cityNameLike = $(this).val();
        //console.log(cityNameLike);
        $.ajax({
            type: 'post',
            url: cityAjax,
            data: 'cityNameLike=' + cityNameLike + '&_token=' + CSRF_TOKEN,
            beforeSend: function(xhr) {
                $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                //return false;
            },
            success: function(returnData) {
                $('#loadering').remove();
                $('.all-city-section').html(returnData);
                //console.log(returnData);

                //alert(returnData);

                // var parsedJson=$.parseJSON(returnData);
                //$( "#city" ).html(returnData);

            }
        });
    });

    /**
     * GET CITY WITH AJAX CALL
     **/
    /*$( document ).on( "click", ".day_id", function(){

			var dayID = $(this).attr('dayID');

			//console.log(cityNameLike);
			$.ajax({
				 type:'post',
				 dataType : "json",
				 url:dayChargesAjax,
				 //data:'dayID='+dayID+'&_token='+CSRF_TOKEN,
				 
				 data : {dayID: dayID, _token : CSRF_TOKEN },

				 beforeSend: function( xhr ) {
					$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
				 success:function(response)
				 {
					$('#loadering').remove();
					//$('.all-city-section').html(returnData);


					charges = [];

					for(var i = 0; i < response.day_charges.length; ++i)
					{
					   $.each(response.day_charges[i], function(key, value) { 
					      //alert(key + ': ' + value); 
					      charges[key] = value;
 					      
					    });
					}
					
					$('#cancelation_charge').val(charges['cancelation_charge']);
					$('#from_time').val(charges['from_time']);
					$('#to_time').val(charges['to_time']);
					$('#cost_per_mile').val(charges['cost_per_mile']);
					$('#service_charge').val(charges['service_charge']);
					$('#less_mile_travel_cost').val(charges['less_mile_travel_cost']);
					$('#greater_mile_travel_cost').val(charges['greater_mile_travel_cost']);
					$('#min_charge').val(charges['min_charge']);
					$('#per_min_charge').val(charges['per_min_charge']);
				 }
			});
		});*/

});
