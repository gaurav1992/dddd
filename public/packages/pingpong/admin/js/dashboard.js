
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

$(document).ready(function() {
    var start = '';
    var end = '';

    /**
     * FUNCTION FOR PAYOUT
     **/
    $(function() {
        validateDateRange('startDatedpayout', 'endDatepayout');
        $("#startDatedpayout,#endDatepayout").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");

                if ($(this).is("#startDatedpayout")) {
                    end = '';
                    var startDate = new Date($("#startDatedpayout").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#endDatepayout")) {
                    var endDate = new Date($('#endDatepayout').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint').html(start + " - " + end);
            });
        });

    });



    $(document).on('change', '#startDatedpayout, #endDatepayout,#payoutState,#payoutCity', function() {
        var startDate = $("#startDatedpayout").val();
        var endDate = $('#endDatepayout').val();
        var payoutState = $('#payoutState').val();
        var payoutCity = $('#payoutCity').val();
		if((startDate!='' && endDate!='') || (payoutState!='' || payoutCity!='')){
        $.ajax({
            "method": "POST",
            "url": dynamicPayout,
            "beforeSend": function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
            },
            "data": {
                startDate: startDate,
                endDate: endDate,
                payoutCity: payoutCity,
                payoutState: payoutState,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                $('#divLoading').remove();
                $('#payoutsum').html('$ ' + response);
            }

        });
		}

    });

    /**
     * FUNCTION FOR NEW PASSENGER
     **/

    $(function() {
        validateDateRange('PstartDate', 'PendDate');
        $("#PstartDate,#PendDate").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");


                if ($(this).is("#PstartDate")) {
                    end = '';
                    var startDate = new Date($("#PstartDate").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#PendDate")) {
                    var endDate = new Date($('#PendDate').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint2').html(start + " - " + end);
            });
        });
    });

    $(document).on('change', '#PstartDate, #PendDate,#passengerCity,#passengerState', function() {
        var startDate = $("#PstartDate").val();
        var endDate = $('#PendDate').val();
		
        var passengerCity = $('#passengerCity').val();
        var passengerState = $('#passengerState').val();
		
        $.ajax({
            "method": "POST",
            "url": DnewPass,
            "beforeSend": function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
            },
            "data": {
                startDate: startDate,
                endDate: endDate,
                passengerCity: passengerCity,
                passengerState: passengerState,
                _token: CSRF_TOKEN
            },
            success: function(response) {
				//console.log(response);
                $('#divLoading').remove();
                $('#passnew').html(response);
            }

        });


    });
    /**
     ** GET CITY WITH AJAX CALL
     **/
    $(document).on("change", "#stateIds,.stateIds", function() {
        //alert(56);
        var stateCode = $(this).val();
		var thisElement= $(this);
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
                //console.log(returnData);
                // var parsedJson=$.parseJSON(returnData);
				thisElement.parent().next().find('select.cityTag').html(returnData);
                //$("#cityId").html(returnData);

            }
        });
    });
    /**
     * FUNCTION FOR NEW DRIVER
     **/
    $(function() {
        validateDateRange('DrstartDate', 'DrendDate');
        $("#DrstartDate,#DrendDate").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");

                if ($(this).is("#DrstartDate")) {
                    end = '';
                    var startDate = new Date($("#DrstartDate").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#DrendDate")) {
                    var endDate = new Date($('#DrendDate').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint3').html(start + " - " + end);
            });
        });
    });

    $(document).on('change','#DrstartDate,#DrendDate,#driverState,#driverCity',function() {
        var startDate = $("#DrstartDate").val();
        var endDate = $('#DrendDate').val();
        var driverState = $('#driverState').val();
        var driverCity = $('#driverCity').val();
        $.ajax({
            "method": "POST",
            "url": DnewDriver,
            "beforeSend": function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
            },
            "data": {
                startDate: startDate,
                endDate: endDate,
                driverState: driverState,
                driverCity: driverCity,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                $('#divLoading').remove();
                $('#drivernew').html(response);
            }

        });


    });


    /**
     * FUNCTION FOR NEW Ride Request
     **/

    $(function() {
        validateDateRange('rdstartDate', 'rdendDate');
        $("#rdstartDate,#rdendDate").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");


                if ($(this).is("#rdstartDate")) {
                    end = '';
                    var startDate = new Date($("#rdstartDate").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#rdendDate")) {
                    var endDate = new Date($('#rdendDate').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint4').html(start + " - " + end);
                $('.dateprint5').html(start + " - " + end);
            });
        });
    });

    $(document).on('change', '#rdstartDate, #rdendDate', function() {
        var startDate = $("#rdstartDate").val();
        var endDate = $('#rdendDate').val();

        if (startDate != '' && endDate != '') {
            $.ajax({
                "method": "POST",
                "url": DnewRide,
                "beforeSend": function(xhr) {
                    $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
                },
                "data": {
                    startDate: startDate,
                    endDate: endDate,
                    _token: CSRF_TOKEN
                },
                success: function(response) {
                    $('#divLoading').remove();
                    var parseData = jQuery.parseJSON(response);
                    console.log(parseData);

                    if ((typeof parseData !== 'undefined')) {
                        var maxCityName = parseData[parseData.maxRideCityIndex].city_name;
                        var maxCityCount = parseData[parseData.maxRideCityIndex].counting;
                        var minCityName = parseData[parseData.leastRideCityIndex].city_name;
                        var minCityCount = parseData[parseData.leastRideCityIndex].counting;

                        $('#ridesnew1').html('Most Ride City :' + maxCityName + '(' + maxCityCount + ')');
                        $('#ridesnew2').html('Least Ride City :' + minCityName + '(' + minCityCount + ')');

                    } else {

                        $('#ridesnew1').html(0 + ' ' + '');
                        $('#ridesnew2').html(0 + ' ' + '');
                    }

                }

            });
        }



    });


    /**
     * FUNCTION FOR NEW Refunds
     **/
    $(function() {
        validateDateRange('startDaterefund', 'endDatepayoutrefund');
        $("#startDaterefund,#endDatepayoutrefund").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");

                if ($(this).is("#startDaterefund")) {
                    end = '';
                    var startDate = new Date($("#startDaterefund").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#endDatepayoutrefund")) {
                    var endDate = new Date($('#endDatepayoutrefund').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint6').html(start + " - " + end);
                //$('.dateprint5').html(start + " - " +end);
            });
        });
    });

    $(document).on('change', '#startDaterefund, #endDatepayoutrefund,#refundState,#refundCity', function() {
        var startDate = $("#startDaterefund").val();
        var endDate = $('#endDatepayoutrefund').val();
        var refundCity = $('#refundCity').val();
        var refundState = $('#refundState').val();
        $.ajax({
            "method": "POST",
            "url": DrefundUrl,
            "beforeSend": function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
            },
            "data": {
                startDate: startDate,
                endDate: endDate,
                refundCity: refundCity,
                refundState: refundState,
                _token: CSRF_TOKEN
            },
            success: function(response) {
                $('#divLoading').remove();
                $('#refundSum').html(response);
            }

        });

    });


    /**
     * FUNCTION FOR Locations
     **/
    $(function() {
        validateDateRange('startDateloc', 'endDatepayoutloc');
        $("#startDateloc,#endDatepayoutloc").each(function() {
            $(this).datepicker().on('changeDate', function(ev) {
                $(this).datepicker("hide");

                if ($(this).is("#startDateloc")) {
                    //end='';
                    var startDate = new Date($("#startDateloc").val());
                    var d = startDate.getDate();
                    var m = startDate.getMonth();
                    m += 1; // JavaScript months are 0-11
                    var y = startDate.getFullYear();
                    start = d + "." + m + "." + y;
                }
                if ($(this).is("#endDatepayoutloc")) {
                    $(this).datepicker({
                        startDate: '-3d'
                    });
                    var endDate = new Date($('#endDatepayoutloc').val());
                    var d2 = endDate.getDate();
                    var m2 = endDate.getMonth();
                    m2 += 1; // JavaScript months are 0-11
                    var y2 = endDate.getFullYear();
                    end = d2 + "." + m2 + "." + y2;

                }
                $('.dateprint7').html(start + " - " + end);
                //$('.dateprint5').html(start + " - " +end);
            });
        });
    });
	$(document).on('change', '#stateIds',function(){
		
		if($(this).val()==''){
			location.reload();
			return false;
		}
		
	});
    $(document).on('change', '#startDateloc, #endDatepayoutloc, #cityId', function() {
        var startDate = $("#startDateloc").val();
        var endDate = $('#endDatepayoutloc').val();
        var city = $('#cityId').val();
        var state = $('#stateIds').val();
        var cityName = $('#cityId :selected').text();
        console.log(cityName);
		if((startDate!='' && endDate !='') || (city!='')){
			   $.ajax({
					"method": "POST",
					"url": DlocationUrl,
					"beforeSend": function(xhr) {
					$('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
					},
					"data": {
						startDate: startDate,
						endDate: endDate,
						city: city,
						state: state,
						cityName: cityName,
						_token: CSRF_TOKEN
					},
					success: function(response) {
					$('#divLoading').remove();
					var parseData = jQuery.parseJSON(response);
					var profit = parseFloat(parseData.tRevenue) - parseFloat(parseData.payouts);
					
					$('#locationF').html('Passengers <span style="color:blue;">' + parseData.passengers + '</span>  &nbsp;&nbsp; Drivers <span style="color:blue;"> ' + parseData.drivers  + '</span>');
					
					$('#locationF2').html('Total Revenue <span style="color:blue;">' + parseData.tRevenue + '</span>  &nbsp;&nbsp;Payouts <span style="color:blue;">' + parseData.payouts + '</span> &nbsp;&nbsp; Profit <span style="color:blue;">' + profit + '</span>');
					}
            });
		}
         });


});




$(function() {

    $(document).on("click", ".GenerateReportPayouts_po", function() {

        var startDatedpayout = $("#startDatedpayout").val();

        var endDatepayout = $("#endDatepayout").val();

        if (startDatedpayout == '' || endDatepayout == '') {

            $(".payout_err").text('Date-range fields can not empty')
            return false;

        } else {

            $(".payout_err").text('')
            return true;
        }

    });


    $(document).on("click", ".GenerateReportPayouts_npsg", function() {

        var PstartDate = $("#PstartDate").val();

        var PendDate = $("#PendDate").val();

        if (PstartDate == '' || PendDate == '') {

            $(".passenger_err").text('Date-range fields can not empty')
            return false;

        } else {

            $(".passenger_err").text('')
            return true;
        }

    });

    $(document).on("click", ".GenerateReportPayouts_ndrvr", function() {

        var DrstartDate = $("#DrstartDate").val();

        var DrendDate = $("#DrendDate").val();

        if (DrstartDate == '' || DrendDate == '') {

            $(".newdrvr_err").text('Date-range fields can not empty')
            return false;

        } else {

            $(".newdrvr_err").text('')
            return true;
        }

    });

    $(document).on("click", ".GenerateReportPayouts_rdrqst", function() {

        var rdstartDate = $("#rdstartDate").val();

        var rdendDate = $("#rdendDate").val();

        if (rdstartDate == '' || rdendDate == '') {

            $(".ride_err").text('Date-range fields can not empty')
            return false;

        } else {

            $(".ride_err").text('')
            return true;
        }

    });


    $(document).on("click", ".GenerateReportrefunds_rfnd", function() {

        var startDaterefund = $("#startDaterefund").val();

        var endDatepayoutrefund = $("#endDatepayoutrefund").val();

        if (startDaterefund == '' || endDatepayoutrefund == '') {

            $(".rfnd_err").text('Date-range fields can not empty')
            return false;

        } else {

            $(".rfnd_err").text('')
            return true;
        }

    });

});