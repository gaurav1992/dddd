function convertToSlug(Text)
{
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

$('[name=title], [name=name]').on('keyup', function ()
{
    var title = $(this).val();
    var slug = convertToSlug(title);

    $('[name=slug]').val(slug);

    $('.slug-preview').text(slug);

});

$(document).ready(function() {

	/**
	 * Driver Charges: driverCharges 
	**/

	$( document ).on( "click", "#price_schdule", function() { 
		$('.price_schdule').show(700);
		$('input[name="is_schedule_charges"]').val('1');
	});
	$( document ).on( "click", ".close-me", function() { 
		$('.price_schdule').hide(700); 
		$('input[name="is_schedule_charges"]').val('0');
	})

	//Passenger Dezicredit Form
	$( document ).on('click', '.view-charges', function(e) { //use on if jQuery 1.7+
        e.preventDefault();  //prevent form from submitting
       	countCity = 0;
       	$('input[name="city_id[]"]:checked').each(function() {
       	   countCity++;
		   city_id = this.value;
		});
		if (countCity == 1) {

			var CSRF_TOKEN = $(".driver-charges input[name='_token']").val();
			var getCityCharges = city_id;

			jQuery.ajax({
		        type : "post",
		        dataType : "json",
		        url : viewCityChargesURL,
		        
		        data : { _token: CSRF_TOKEN, getCityCharges : getCityCharges },
		         
		        //if using the form then use this
		        //data : serializedData,

		        success: function(response) {
		            
		            if (response.azStatus == 'success') {

		            	$('input[name="default_cost_per_mile"]').val(response.view_city_charges.cost_per_mile);
		            	$('input[name="default_per_min_charge"]').val(response.view_city_charges.per_min_charge);
		            	$('input[name="default_less_mile_travel_cost"]').val(response.view_city_charges.less_mile_travel_cost);
		            	$('input[name="default_greater_mile_travel_cost"]').val(response.view_city_charges.greater_mile_travel_cost);
		            	$('input[name="default_service_charges"]').val(response.view_city_charges.service_charge);
		            	$('input[name="default_min_charge"]').val(response.view_city_charges.min_charge);
		            	$('input[name="default_cancelation_charge"]').val(response.view_city_charges.cancelation_charge);
		            }

		            else { 
		            	alert(response.azMessage); 
		            	$('.driver-charges').find("input[type=text], textarea").val("");
		            }
		        },
		        failure: function(errMsg) {
			        alert(errMsg);
			    }
		    });

		} else {
			alert('Please Select Only One City To View The Charges Or Download The Log File To View All The City Charges.');	
		}
       	

		return false;
    });

	$('#to_time, #from_time').wickedpicker({twentyFour: false});

	/**
	 * Validation for passenger promos section in admin side 
	 */
	$("#riderpromos").validate({
				rules: {
				referal_credit_for_5_10:{
					number:true,
					maxlength: 5
					},
				referal_credit_for_20:{
					number:true,
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
				referal_credit_for_5_10:{
					number:'Please enter valid credit.'
				},
				referal_credit_for_20:{
					number:'Please enter valid credit.'
				},
				
			}
		});

	/**
	 * Validation for passenger promos section in admin side 
	 */
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
		});


		/**
		* Passengers Table 
		**/
		 if(typeof indexUrl !== 'undefined'){
		  var oTable=$('#Passengertable').DataTable({
				  "language": {
					  "paginate": {
					  "previous": "<<",
					  "next":">>"
					
				  }},
				"processing": true,
				 "columnDefs": [ {
		          "targets": 'no-sort',
		          "orderable": false,
		    	} ],
		        "serverSide": true,
				"ajax": {
				"url": indexUrl,
				"data": function ( d ) {
					d.state = $('#state').val();
					d.city = $('#city').val();
					d.startDate = $('#startDate').val();
					d.endDate = $('#endDate').val();
				}
				
			  }
			
		 } );}

		/**
		  * driverApplicantList 
		  **/
		 if(typeof driverUrl !== 'undefined'){
		  var oTable=$('#driverApplicantList').DataTable({
				  "language": {
					  "paginate": {
					  "previous": "<<",
					  "next":">>"
					
				  }},
				"processing": true,
				 "columnDefs": [ {
		          "targets": 'no-sort',
		          "orderable": false,
		    	} ],
		        "serverSide": true,
				"ajax": {
				"url": driverUrl,
				"data": function ( d ) {
					d.state = $('#state').val();
					d.city = $('#city').val();
					d.startDate = $('#startDate').val();
					d.endDate = $('#endDate').val();
				}
				
			  }
			
		 } );}

		/**
		 * rejectedDriverUrl 
		**/
		if(typeof rejectedDriverUrl !== 'undefined'){
		  	
		  	var oTable=$('#rejectedDriverList').DataTable({
				  "language": {
					  "paginate": {
					  "previous": "<<",
					  "next":">>"
					
				  }},
				"processing": true,
				 "columnDefs": [ {
		          "targets": 'no-sort',
		          "orderable": false,
		    	} ],
		        "serverSide": true,
				"ajax": {
				"url": rejectedDriverUrl,
				"data": function ( d ) {
					d.state = $('#state').val();
					d.city = $('#city').val();
					d.startDate = $('#startDate').val();
					d.endDate = $('#endDate').val();
				}
				
			  }
			
		 	});
		}

		/**
		  * New Driver URL 
		  **/
		 if(typeof newDriverUrl !== 'undefined'){
		  var oTable=$('#newDriverList').DataTable({
				  "language": {
					  "paginate": {
					  "previous": "<<",
					  "next":">>"
					
				  }},
				"processing": true,
				 "columnDefs": [ {
		          "targets": 'no-sort',
		          "orderable": false,
		    	} ],
		        "serverSide": true,
				"ajax": {
				"url": newDriverUrl,
				"data": function ( d ) {
					d.state = $('#state').val();
					d.city = $('#city').val();
					d.startDate = $('#startDate').val();
					d.endDate = $('#endDate').val();
				}
				
			  }
			
		 } );}

	
		/**
		  * Cars Table 
		  **/
		 var carTable=$('#carTable').DataTable({
					"bPaginate": false,
					  "columns": [
								{ "data": "S.NO." },
								{ "data": "Make" },
								{ "data": "Model" },
								{ "data": "License No." },
								{ "data": "Transmission" },
								{ "data": "Action" }
								]

			} );
		/**
		  * SEARCHING ON STATE 
		  **/
		$('#state').on('change',function(){
				var selectedValue = $(this).val();
				oTable.ajax.reload();
			});
			
		/**
		  * SEARCHING ON CITY 
		  **/
		$('#city').on('change',function(){
				oTable.ajax.reload();
		    });

		/**
		  *CHANGED POSITION OF GENERATE REPORT BUTTON 
		  **/
		if($('#Passengertable').length){
			var tableTools = new $.fn.dataTable.Buttons( oTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#billing');	
		}

		/**
		  *CHANGED POSITION OF GENERATE REPORT BUTTON 
		  **/
		if($('#driverApplicantList').length){
			var tableTools = new $.fn.dataTable.Buttons( oTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#billing');	
		}

		/**
		  *CHANGED POSITION OF GENERATE REPORT BUTTON 
		  **/
		if($('#rejectedDriverList').length){
			var tableTools = new $.fn.dataTable.Buttons( oTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#billing');	
		}

		/**
		  *CHANGED POSITION OF GENERATE REPORT BUTTON 
		  **/
		if($('#newDriverList').length){
			var tableTools = new $.fn.dataTable.Buttons( oTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			});
			
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#billing');	
		}


		if($('#carTable').length){
		var tableTools = new $.fn.dataTable.Buttons( carTable, {
		    buttons: [{ extend: 'pdf', text: 'Download', className: 'color-blue',exportOptions:{columns: [1, 2,3,4 ]}}]
			
		} );
		//console.log(tableTools);
		$( tableTools.container() ).appendTo('.carsReport');	
		}

		/**
		  * DATEPICKER HIDE CODE ON CHANGE DATE 
		  **/
		$(function () {
		    $("#startDate,#endDate,#DOB,#startDatepdf,#endDatepdf").each(function () {
		        $(this).datepicker().on('changeDate', function (ev) {
		            $(this).datepicker("hide");
		        });
		    });
		    $(document).on('change', '#startDate, #endDate', function () {
		       oTable.ajax.reload();
		       
		    });
		    
		     $("#promo_till_date").each(function () {
		        $(this).datepicker().on('changeDate', function (ev) {
		            $(this).datepicker("hide");
		        });
		    });

		});

		/**
		 * Ajax call for suspend and active passengers
		 **/
		$( document ).on( "click", ".driver_suspend,.passenger_Active", function() {
			var r=confirm("Do you want to perform action!");
			if(r==true){
				var id= $(this).data('userid');
				var cell=$(this);
				var RowId = $(this).closest("tr").find('td:eq(7)');
				//console.log(id);
				//alert( RowId );
				var dataAction =  $(this).data('action');
				// alert(dataAction);
				$.ajax({
					type:'post',
					url:suspendUrl,
					data:'action='+dataAction+'&id='+id+'&_token='+CSRF_TOKEN,
					beforeSend: function( xhr ) {
						/// alert(loadingImage);
						 //$('.actions li:first-child a').hide();
						$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
						//return false;
					},
					success:function(returnData)
					{
						$('#loadering').remove();
						//console.log(returnData);
						if(returnData=='suspendSuccess')
						{
							RowId.html('Suspended');
							cell.html('Active');
							cell.removeClass( "driver_suspend" ).addClass( "passenger_Active" );
							cell.removeClass( "btn-primary" ).addClass( "btn-success" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
							cell.data( 'action',"passenger_Active" );
							//console.log(RowId);
						}
						if(returnData=='activeSuccess')
						{ 
							
							RowId.html('Active');
							cell.html('Suspend');
							cell.removeClass( "passenger_Active" ).addClass( "driver_suspend" );
							cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
							cell.data( 'action',"driver_suspend" );
							//console.log(RowId);
						}
					}
				});
			}
		});	
		/**
		 * Ajax call for Revoke driver
		 **/
		$( document ).on( "click", ".driver_revoke,.driver_unrevoke", function() {
			var r=confirm("Do you want to perform action!");
			if(r==true){
				var id= $(this).data('userid');
				var cell=$(this);
				var RowId = $(this).closest("tr").find('td:eq(7)');
				//console.log(id);
				//alert( RowId );
				var dataAction =  $(this).data('action');
				// alert(dataAction);
				$.ajax({
					type:'post',
					url:revokeURL,
					data:'action='+dataAction+'&id='+id+'&_token='+CSRF_TOKEN,
					beforeSend: function( xhr ) {
						/// alert(loadingImage);
						 //$('.actions li:first-child a').hide();
						$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
						//return false;
					},
					success:function(returnData)
					{
						$('#loadering').remove();
						//console.log(returnData);
						if(returnData=='RevokedSuccess')
						{
							RowId.html('Revoke');
							cell.html('Revoked');
							cell.removeClass( "driver_revoke" ).addClass( "driver_unrevoke" );
							cell.removeClass( "btn-primary" ).addClass( "btn-success" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
							cell.data( 'action',"driver_unrevoke" );
							//console.log(RowId);
						}
						if(returnData=='unrevokedsuccess')
						{ 

							RowId.html('Revoked');
							cell.html('Revoke');
							cell.removeClass( "driver_unrevoke" ).addClass( "driver_revoke" );
							cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
							cell.data( 'action',"driver_revoke" );
							//console.log(RowId);
						}
					}
				});
			}
		});	

		/**
		  * Ajax call for suspend and active driver
		  **/
			$( document ).on( "click", ".driver_approve, .driver_disapprove, .btnDriver_suspend, .btnDriver_approve, .btnDriver_disapprove, .btnDriver_active", function() {
				var r=confirm("Do you want to perform action!");

				if(r==true){

					var id= $(this).data('userid');
					var cell=$(this);
					var RowId = $(this).closest("tr").find('td:eq(7)');
					//console.log(id);
					//alert( RowId );
					var dataAction =  $(this).data('action');
					// alert(dataAction);
					$.ajax({
						type:'post',
						url:driverActionUrl,
						data:'action='+dataAction+'&id='+id+'&_token='+CSRF_TOKEN,
						beforeSend: function( xhr ) {
							/// alert(loadingImage);
							 //$('.actions li:first-child a').hide();
							$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
							//return false;
						},
						success:function(returnData)
						{
							//alert(returnData);

							$('#loadering').remove();
							console.log(returnData);

							if(returnData=='suspendSuccess')
							{
								$('.btnDriver_suspend').html('User Suspended');
								$('.btnDriver_suspend').css('background', '#dd4b39');
								$('.btnDriver_suspend').attr('data-action', 'driver_approve');
								
								$('.btnDriver_suspend').removeClass('btnDriver_suspend');
								$('.btnDriver_suspend').addClass('btnDriver_active');

							}
							if(returnData=='suspendSuccess')
							{
								$('.btnDriver_suspend').html('User Suspended');
								$('.btnDriver_suspend').css('background', '#dd4b39');
								$('.btnDriver_suspend').attr('data-action', 'driver_approve');
								
								$('.btnDriver_suspend').removeClass('btnDriver_suspend');
								$('.btnDriver_suspend').addClass('btnDriver_active');

							}

							if(returnData=='approveSuccess')
							{ 
								RowId.html('Active');
								cell.html('Approved');
								//cell.removeClass( "passenger_Active" ).addClass( "driver_suspend" );
								cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
								cell.removeAttr("data-action" ).attr( 'data-action',"driver_approve" );
								cell.data( 'action',"driver_disapprove" );
								//console.log(RowId);
							}

							if(returnData=='btnapproveSuccess')
							{
								$('.btnDriver_approve').html('User Approved	');
								$('.btnDriver_approve').css('background', '#00a65a');
								$('.driver_approve').html('User Approved	');
								$('.driver_approve').css('background', '#00a65a');
								

							}
							if(returnData=='btndisapproveSuccess')
							{
								$('.btnDriver_disapprove').html('User Disapproved	');
								$('.btnDriver_disapprove').css('background', '#dd4b39');
								$('.driver_disapprove').html('User Disapproved	');
								$('.driver_disapprove').css('background', '#dd4b39');

							}

							if(returnData=='btnactiveSuccess')
							{
								$('.btnDriver_active').html('User Activated	');
								$('.btnDriver_active').css('background', '#00a65a');

							}

							if(returnData=='disapproveSuccess')
							{ 
								
								RowId.html('Active');
								cell.html('Disapproved');
								//cell.removeClass( "passenger_Active" ).addClass( "driver_suspend" );
								cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
								cell.removeAttr("data-action" ).attr( 'data-action',"driver_disapprove" );
								cell.data( 'action',"driver_approve" );
								//console.log(RowId);
							}

							if(returnData=='error')
							{ 
								alert('error');
								console.log('error');
							}
						}
					});
				}
			});	


		/**
	  	 * Ajax call for suspend and active passengers
	  	**/
		$( document ).on( "click", ".subadmin_suspend, .subadmin_Active", function() {
				var r=confirm("Do you want to perform action!");
				if(r==true){
					var id= $(this).data('userid');
					var cell=$(this);
					var RowId = $(this).closest("tr").find('td:eq(7)');
					//console.log(id);
					//alert( RowId );
					var dataAction =  $(this).data('action');
					// alert(dataAction);
					$.ajax({
						type:'post',
						url:suspendAdminUrl,
						data:'action='+dataAction+'&id='+id+'&_token='+CSRF_TOKEN,
						beforeSend: function( xhr ) {
							/// alert(loadingImage);
							 //$('.actions li:first-child a').hide();
							$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
							//return false;
						},
						success:function(returnData)
						{
							$('#loadering').remove();
							//console.log(returnData);
							if(returnData=='suspendSuccess')
							{
								RowId.html('Suspended');
								cell.html('Active');
								cell.removeClass( "subadmin_suspend" ).addClass( "subadmin_Active" );
								cell.removeClass( "btn-primary" ).addClass( "btn-success" );
								//cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
								cell.data( 'action',"subadmin_Active" );
								//console.log(RowId);
							}
							if(returnData=='activeSuccess')
							{ 
								RowId.html('Active');
								cell.html('Suspend');
								cell.removeClass( "subadmin_Active" ).addClass( "subadmin_suspend" );
								cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
								//cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
								cell.data( 'action',"subadmin_suspend" );
								//console.log(RowId);
							}
						}
					});
				}
			});	


		/**
		  * Ajax call for delete cars
		  **/
			$( document ).on( "click",".deleteCar", function() {
				 var r=confirm("Do you want to Delete!");
				 if(r==true){
				var id= $(this).data('carid');
				var RowId = $(this).closest("tr");
				console.log(id);
				 $.ajax({
				 type:'get',
				 url:deleteCarUrl,
				 data:'id='+id+'&_token='+CSRF_TOKEN,
				 beforeSend: function( xhr ) {
					$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
				 success:function(returnData)
				 {
					 $('#loadering').remove();
					if(returnData='deleted')
					{
						RowId.remove();
					}
				 }
				 });}
			});

		/**
		  ** GET CITY WITH AJAX CALL
		  **/
		$( document ).on( "change", "#state", function(){
			var stateCode= $(this).val();
			console.log(stateCode);
			$.ajax({
				 type:'post',
				 url:homeUrl,
				 data:'stateCode='+stateCode+'&_token='+CSRF_TOKEN,
				 beforeSend: function( xhr ) {
					$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
				 success:function(returnData)
				 {
					$('#loadering').remove();
					 console.log(returnData);
					// var parsedJson=$.parseJSON(returnData);
					$( "#city" ).html(returnData);
					
				 }
			});
		});

		/**
		  * FORM VALIDATION
		  **/
			// validate signup form on keyup and submit
			$("#addUserForm,#editUser").validate({
				rules: {
					fname: "required",
					lname: "required",
					Email: {
						required: true,
						email: true
					},
					Phone:{
						required:true,
						minlength: 10
						},
					pwd:"required",
					DOB:"required",
					userType:"required"
				},
				errorPlacement: function(error, element) {
		            if (element.attr("name") == "DOB" )
		            {
		               error.appendTo("#error");
		            }else{
						error.insertAfter(element)
				}},
				messages: {
					fname: "Please enter your firstname",
					lname: "Please enter your lastname",
					Email: "Please enter a valid email address",
					Phone: "Please enter a valid phone number minimum of 10 digit",
					pwd: "Please enter  valid Password",
					DOB:"Please enter your DOB",
					userType:"Please Select User Type"
				}
			});
				
		/**
		 * Code to hide permission panel 
		**/

		$('input:radio[name="userType"]').change(
		    function(){
		        if ($(this).is(':checked') && $(this).val() == 'superAdmin') {
		            // append goes here
					$('input:checkbox').removeAttr('checked');
					$(".Auth").hide();
		        }
				else{$(".Auth").show();}
		    });

		/**
		  * image preview
		  **/
		  function readURL(input) {
		        if (input.files && input.files[0]) {
		            var reader = new FileReader();
		            reader.onload = function (e) {
		                $('#profile_pic').attr('src', e.target.result);
		            }
		            reader.readAsDataURL(input.files[0]);
		        }
		    }
		    $("input[name=image]").change(function () {
		        readURL(this);
		    });
		/**
		  * Custom browse
		  **/	
		$("#browseImageBtn").click(function () {
		    $("#imgbrowse").click();
		});

		// ***** SCRIPT HARISH ***** //

		// ***** SCRIPT CITY SELECT ALL ***** //

		$('.select-all-city').click( function( event ){
			event.preventDefault();
			var check_uncheck = $(this).attr('check-uncheck');
			if( check_uncheck === 'check' )
			{
				$('.city-check').prop('checked', true);
				$(this).attr('check-uncheck' , 'uncheck');
				$(this).html('Unselect All');
			}
			else
			{
				$('.city-check').prop('checked', false);
				$(this).attr('check-uncheck' , 'check');
				$(this).html('Select All');
			}
		});


		/**
		 * GET CITY WITH AJAX CALL
		**/
		$( document ).on( "keyup", "#search_city", function(){

			var cityNameLike = $(this).val();
			//console.log(cityNameLike);
			$.ajax({
				 type:'post',
				 url:cityAjax,
				 data:'cityNameLike='+cityNameLike+'&_token='+CSRF_TOKEN,
				 beforeSend: function( xhr ) {
					$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
				 success:function(returnData)
				 {
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