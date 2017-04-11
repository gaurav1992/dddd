$(document).ready(function() {

	//Edit Credit Callout
	$( document ).on( "click", ".credit-edit", function( e ){
		
		e.preventDefault();
		$('.credit-edit-callout').show(400);
	});

	$( document ).on( "click", ".close-me", function( e ){
		
		e.preventDefault();
		$(this).parent().hide(400);
	});
	

	//ORIGINAL SCRIPT - REFUND AMONT AJAX AND CALCULATIONS
	$('.expCalTotal').click(function() {
		$(".my-loader").show(400);
		var expRideStatus = $('#expRideStatus').attr("expRideStatus");

		if (expRideStatus != 1) {
			$(".my-loader").hide(400);
        	$("#azNotificationModal #azTitle").html('ERROR : No Refund');
        	$("#azNotificationModal #azMessage").html('Error: No refund will be processed for payments with In Progress/Not Completed Status.');
        	$("#azNotificationModal").modal('show');
        	return false;
		};

		var expRideID = $('#expRideID').attr("expRideID");
		var rideamount = $('#rideamount').attr("rideamount");
		var expChargeID = $('#expChargeID').attr("expChargeID");
		var expDriverLevel = $('#expDriverLevel').attr("expDriverLevel");

		var expAmount = $('.expAmount').attr("expAmount");
		var expRefundAmount = $('.expRefundAmount').val();
		var expDeziCredit = $('.expDeziCredit').attr('expDeziCredit');
		var expTip = $('.expTip').attr('expTip');
		var expRefundTip = $('.expRefundTip').val();
		
		if (rideamount < expRefundTip) { 
			$("#azNotificationModal #azTitle").html('ERROR : No Refund.');
		 	$("#azNotificationModal #azMessage").html('Error: Refund can not be more then Ride amount.');
		 	$("#azNotificationModal").modal('show');
		 	$(".my-loader").hide(400);
		 	return false;
		 }
		if (expRefundAmount == '') { expRefundAmount = 0; };
		if (expRefundTip == '') { expRefundTip = 0; };

		total = expAmount-expRefundAmount-expRefundTip;

		jQuery.ajax({
	        type : "post",
	        dataType : "json",
	        url : userrefundURL,

		    statusCode: {
		        500: function() {
		          	$(".my-loader").hide(400);
	            	$("#azNotificationModal #azTitle").html('Internal Server Error Occurred');
	            	$("#azNotificationModal #azMessage").html('Internal Server Error Occurred : Error 500');
	            	$("#azNotificationModal").modal('show');
		        }
		    },

	        data : { _token : CSRF_TOKEN,
	        	ride_id : expRideID,
	        	driver_level : expDriverLevel,
	        	total_amount : total,
	        	expChargeID : expChargeID,
	        	expAmount : expAmount, 
	        	expRefundAmount : expRefundAmount, 
	        	expDeziCredit : expDeziCredit, 
	        	expTip : expTip,
	        	expRefundTip : expRefundTip },
	         
	        //if using the form then use this
	        //data : serializedData,

	        success: function(response) {
	            
	            if (response.azStatus == 'success') {
	            	$(".my-loader").hide(400);
	            	$("#azNotificationModal #azTitle").html(response.azTitle);
	            	$("#azNotificationModal #azMessage").html(response.azMessage);
	            	$("#azNotificationModal").modal('show');
	            	
	            	$(".expShowTotal").html(total);
	            	//location.reload();
	            }

	            else { 

	            	$(".my-loader").hide(400);
	            	$("#azNotificationModal #azTitle").html(response.azTitle);
	            	$("#azNotificationModal #azMessage").html(response.azMessage);
	            	$("#azNotificationModal").modal('show');
	            }
	        },
	        failure: function(errMsg) {
		        alert(errMsg);
		    }
	    });

	});
	// /ORIGINAL SCRIPT - REFUND AMONT AJAX AND CALCULATIONS


/**
 * Active Driver List Table 
 **/
//alert(activeDriverList);

if(typeof testAZ09 !== 'undefined')
{

		/* testAZ09Table */
		var testAZ09Table=$('#testAZ09').DataTable({
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
		"url": testAZ09,
		"data": function ( d ) {
					
				d.useriddriver = userid;
				d.startDate = $('#AZ09startDate').val();
				d.endDate = $('#AZ09endDate').val();
			},
		"complete": function (json) {                       
                        $("#ridesTaken").html(json.responseJSON.recordsTotal);
						
                    },
		 },
		 "fnDrawCallback":function(){
	             if(jQuery('table#testAZ09 td').hasClass('dataTables_empty')){
	                jQuery('.b1,#testAZ09_paginate').hide();
					
	             } else {
	               jQuery('.b1,#testAZ09_paginate').show();
	             }
	             
	            
	        }
	  	
	});
	
	if($('#testAZ09').length){
		
	var tableTools = new $.fn.dataTable.Buttons( testAZ09Table, {
    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue b1 genRepoBtn',exportOptions:{columns: [1, 2,3,4,5,6,7]}}]
	});
	
	
	
	$( tableTools.container()).insertAfter('#end');	
		
	}

}

if(typeof pasangerPaymentHistoryURL !== 'undefined')
{
	var opasangerPaymentHistoryTable=$('#pasangerPaymentHistoryTable').DataTable({
		"language": {
			"paginate": {
			"previous": "<<",
			"next":">>"

			}
		},
		"processing": true,
		"columnDefs": [ {
			"targets": 'no-sort',
			"orderable": false,
		} ],
		"serverSide": true,
		"ajax": {
			"url": pasangerPaymentHistoryURL,
			"data": function ( d ) {
				
				d.useriddriver = userid;
				d.startDate = $('#payHisStartDate').val();
				d.endDate = $('#payHisEndDate').val();
			}
		},
		"fnDrawCallback":function(){
	             if(jQuery('table#pasangerPaymentHistoryTable td').hasClass('dataTables_empty')){
	                jQuery('.pht,#pasangerPaymentHistoryTable_paginate').hide();
	             } else {
	               jQuery('.pht,#pasangerPaymentHistoryTable_paginate').show();
	             }
	            
				}

	});
	
	if($('#pasangerPaymentHistoryTable').length){
	var tableTools = new $.fn.dataTable.Buttons( opasangerPaymentHistoryTable, {
    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue pht genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
	});
	
	//console.log(tableTools);
	$( tableTools.container() ).insertAfter('#end3');	
}

}
if(typeof pasangerIssueHistory !== 'undefined')
{
	var oPasangerIssueHistory=$('#pasangerIssueTable').DataTable({
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
		"url": pasangerIssueHistory,
		"data": function ( d ) {
					
				d.useriddriver = userid;
				d.startDate = $('#issueStartDate').val();
				d.endDate = $('#issueEndDate').val();
			},
		"complete": function (json) {                       
                        $("#IssuesRaised").html(json.responseJSON.recordsTotal);
						
                    }
		},
		"fnDrawCallback":function(){
	             if(jQuery('table#pasangerIssueTable td').hasClass('dataTables_empty')){
	                jQuery('.PIBTN,#pasangerIssueTable_paginate').hide();
	             } else {
	               jQuery('.PIBTN,#pasangerIssueTable_paginate').show();
	             }

	           
				}
	});
	
	if($('#pasangerIssueTable').length){
	var tableTools = new $.fn.dataTable.Buttons(oPasangerIssueHistory, {
    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue PIBTN genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
	});
	
	//console.log(tableTools);
	$( tableTools.container() ).insertAfter('#end2');	
	}
}

if(typeof driverRideList !== 'undefined')
{	/* /testAZ09Table */
	var driverRideListTable=$('#driverRideHisotry').DataTable({
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
			"url": driverRideList,
			"data": function ( d ) {
				d.useriddriver = userid;
				d.startDate = $('#startDate').val();
				d.endDate = $('#endDate').val();
			}
		},
		"fnDrawCallback":function(){
	            
	           
	           
				}
	});
		
}

if(typeof isuueHistoryList !== 'undefined')
{
	var IssuesHisotryTable=$('#IssuesHisotry').DataTable({
		 
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
			"url": isuueHistoryList,
			"data": function ( d ) {
					
					d.useriddriver = userid;
					d.startDate = $('#issueStartDate').val();
					d.endDate = $('#issueEndDate').val();
				}
	  		}

		});
	}


if(typeof DzCreditUrl !== 'undefined')
{
	var DzCreditTable=$('#DzCreditTable').DataTable({
		 
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
			"url": DzCreditUrl,
			"data": function ( d ) {
					d.trnTyp = $('#TrnTYp').val();
					d.Pid = userid;
					d.startDate = $('#DzStartDate').val();
					d.endDate = $('#DzEndDate').val();
				}
	  		},
			"fnDrawCallback":function(){
	             if(jQuery('table#DzCreditTable td').hasClass('dataTables_empty')){
	                jQuery('.dzcrd,#DzCreditTable_paginate').hide();
	             } else {
	               jQuery('.dzcrd,#DzCreditTable_paginate').show();
	             }
				}

		});
	}
	$('#TrnTYp').on('change',function(){
				var selectedValue = $(this).val();
				DzCreditTable.ajax.reload();
			});
	
	if($('#DzCreditTable').length){
			var tableTools = new $.fn.dataTable.Buttons( DzCreditTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue dzcrd genRepoBtn',exportOptions:{columns: [0,1, 2,3,4, 5,6 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#daterange');	
		}

if(typeof carUrl !== 'undefined')
{
	var carTable=$('#pasangerCarTable').DataTable({
		 
		"language": {
			  "paginate": {
			  "previous": "<<",
			  "next":">>"
			
		}},
		"processing": true,
		"responsive": true,
		"searching":false,
		 "columnDefs": [ {
	      "targets": 'no-sort',
	      "orderable": false,
		} ],
	    "serverSide": true,
		"ajax": {  
			"url": carUrl,
			"data": function ( d ) {
					
					d.Pid = userid;
					
				}
	  		},
			"fnDrawCallback":function(){
	             if(jQuery('table#pasangerCarTable td').hasClass('dataTables_empty')){
	                jQuery('.pcrd,#pasangerCarTable_paginate').hide();
	             } else {
	               jQuery('.pcrd,#pasangerCarTable_paginate').show();
	             }
				}

		});

if($('#pasangerCarTable').length){
			var tableTools = new $.fn.dataTable.Buttons( carTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report',title: 'Car Details', className: 'btn btn-default color-blue pcrd genRepoBtn',exportOptions:{columns: [0,1, 2,3,4]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('.carTable');	
		}

	}
	
if(typeof FavPlaceUrl !== 'undefined')
{
	var FavPlaceTable=$('#PfavPlace').DataTable({
		 
		"language": {
			  "paginate": {
			  "previous": "<<",
			  "next":">>"
			
		}},
		"processing": true,
		"responsive": true,
		"searching":false,
		 "columnDefs": [ {
	      "targets": 'no-sort',
	      "orderable": false,
		} ],
	    "serverSide": true,
		"ajax": {  
			"url": FavPlaceUrl,
			"data": function ( d ) {
					
					d.Pid = userid;
					
				}
	  		},
		"fnDrawCallback":function(){
	             if(jQuery('table#PfavPlace td').hasClass('dataTables_empty')){
	                jQuery('.pfvp,#PfavPlace_paginate').hide();
	             } else {
	               jQuery('.pfvp,#PfavPlace_paginate').show();
	             }
				}

		});

if($('#PfavPlace').length){
			var tableTools = new $.fn.dataTable.Buttons( FavPlaceTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue pfvp genRepoBtn',exportOptions:{columns: [0,1, 2,3]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('.favplaceTable');	
		}




	}
//payment account detail
if(typeof Accdetail !== 'undefined')
{
	var Accdetailall=$('#AccDtl').DataTable({
		 
		"language": {
			  "paginate": {
			  "previous": "<<",
			  "next":">>"
			
		}},
		"processing": true,
		"responsive": true,
		"searching":false,
		 "columnDefs": [ {
	      "targets": 'no-sort',
	      "orderable": false,
		} ],
	    "serverSide": true,
		"ajax": {  
			"url": Accdetail,
			"data": function ( d ) {
					
					d.Pid = userid;
					
				}
	  		},
		"fnDrawCallback":function(){
	             if(jQuery('table#AccDtl td').hasClass('dataTables_empty')){
	                jQuery('.pfvp,#AccDtl_paginate').hide();
	             } else {
	               jQuery('.pfvp,#AccDtl_paginate').show();
	             }
				}

		});

	if($('#AccDtl').length){
			var tableTools = new $.fn.dataTable.Buttons( AccDtlTable, {
		    	buttons: [{ extend: 'pdf',title: 'Passenger Account detail', text: 'Generate Report', className: 'btn btn-default color-blue pfvp genRepoBtn',exportOptions:{columns: [0,1, 2,3,4,5]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('.AccDtlTable');	
		}

	}

	
$(function() 
{
    $("#startDate,#endDate").each(function () {
        $(this).datepicker().on('changeDate', function (ev) {
            $(this).datepicker("hide");
        });
    });
	
	validateDateRange('startDate','endDate');
    
	
	$("#DzStartDate,#DzEndDate").each(function () {
        $(this).datepicker().on('changeDate', function (ev) {
            $(this).datepicker("hide");
        });
    });
	 validateDateRange('DzStartDate','DzEndDate');
	 $(document).on('change', '#DzStartDate, #DzEndDate', function () {
       DzCreditTable.ajax.reload();
    });
    /* testAZ09 */
	
    $(document).on('change', '#AZ09startDate, #AZ09endDate', function () {
       $(this).datepicker().on('changeDate', function (ev) {
            alert("test");
            $(this).datepicker("hide");
        });
    });
     
	
    $(document).on('change', '#issueStartDate, #issueEndDate', function () {
       $(this).datepicker().on('changeDate', function (ev) {
            $(this).datepicker("hide");
        });
    });
	validateDateRange('issueStartDate','issueEndDate');
    $(document).on('change', '#issueStartDate, #issueEndDate', function () {
       oPasangerIssueHistory.ajax.reload();
       $(this).datepicker("hide");
    });
    
    /* /testAZ09 */
	/*payment history   */
	
	$("#payHisStartDate,#payHisEndDate").each(function () {
        $(this).datepicker().on('changeDate', function (ev) {
            $(this).datepicker("hide");
        });
    });
	
	validateDateRange('payHisStartDate','payHisEndDate');
	$(document).on('change', '#payHisStartDate, #payHisEndDate', function () {
       opasangerPaymentHistoryTable.ajax.reload();
    });
	
});
		
/**
  *CHANGED POSITION OF GENERATE REPORT BUTTON 
  **/



 
$('.state').on('change',function(){
	var selectedValue = $(this).val();
	activeDriverListTable.ajax.reload();
	
});


$( "#driver_detail_rideid" ).keyup(function( event ) {	
	
	console.log('ad');
	driverRideListTable.ajax.reload();
});

	
/**
  * SEARCHING ON CITY 
  **/
$('.city').on('change',function(){
		activeDriverListTable.ajax.reload();
    });

/**
  ** GET CITY WITH AJAX CALL
  **/
$( document ).on( "change", ".state", function(){
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
			$( ".city" ).html(returnData);
			
		 }
	});
});	

	if($('#activeDriverList').length){
			var tableTools = new $.fn.dataTable.Buttons( activeDriverListTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#driverlist');
			//$('#driverlist').html(tableTools.container());	
	}

	//For date picker

	$(function () {
		    $("#startDatedriverlist,#endDatedriverlist").each(function () {
		        $(this).datepicker().on('changeDate', function (ev) {
		            $(this).datepicker("hide");
		        });
		    });
			validateDateRange('startDatedriverlist','endDatedriverlist');
		    $(document).on('change', '#startDatedriverlist, #endDatedriverlist', function () {
		       activeDriverListTable.ajax.reload();
		       
		    });
		   
		    

		});
	

	//Passenger Actions
	$( document ).on( "click", ".passangerAzAction", function(){
	//$(".passangerAzAction").click(function(event){
		event.preventDefault();
		
		id = $(this).attr('azId');
		azAction = $(this).attr('azAction');

	    //if using the form the serialize the form data
	    //var serializedData = jQuery("#form").serialize();

	    jQuery.ajax({
	        type : "post",
	        dataType : "json",
	        url : pasangerActionURL,
	        
	        data : { _token: CSRF_TOKEN, azId : id, azAction : azAction },
	         
	        //if using the form then use this
	        //data : serializedData,

	        success: function(response) {
	            
	            if (response.azStatus == 'success') { 
	            	alert(response.azStatus);
	            }
	            else { alert(response.azStatus); }
	        },
	        failure: function(errMsg) {
		        alert(errMsg);
		    }
	    });
	});




    //Passenger Actions
	$( document ).on( "click", ".deletePassenger", function(){
	
		event.preventDefault();
		
		alert('Delete function is in process');

		return false;

	});
	/* $('.statusC').on('change',function(){
	var selectedValue = $(this).val();
	activeDriverListTable.ajax.reload();
	}); */
		
	
	
	
	setTimeout(function(){
		$(".editcarDtl").on("click",function(){
			var id=$(this).attr('id');
			  $.ajax({
				type:"POST",
				url:carsEditURL,
				data:{carid:id,_token: CSRF_TOKEN},
				
				success: function(data){
					retData= jQuery.parseJSON(data);
					
					$('#id').val(retData.id);
					$('#make').val(retData.make);
					$('#Model').val(retData.model);
					$('#Transmission').val(retData.transmission);
					
				},
				error: function(data){
				}
			}); 
			
		});
		
		$("#carEdit").on("submit",function(e){
		$('#myModal').hide();
		e.preventDefault();
		
		//console.log($(this).serialize());
			 $.ajax({
				type:"POST",
				url:carsEditURL,
				data:$(this).serialize(),
				dataType: 'json',
				success: function(data){	
						
					carTable.ajax.reload();
				},
				error: function(data){
					location.reload();
					carTable.ajax.reload();
				}
			});
		});
		
		

/**
  * Ajax call for delete cars
  **/
$( document ).on( "click",".deleteCar", function() {
				 var r=confirm("Do you want to Delete!");
				 if(r==true){
				var id = $(this).attr('id');
				//alert(id);
				 $.ajax({
				 type:'POST',
				 url:deleteCarUrl,
				 data:'id='+id+'&_token='+CSRF_TOKEN,
				
				 success:function(returnData)
				 {
					 //$('#loadering').remove();
					carTable.ajax.reload();
				 }
				 });}
			});
	},1000);
	
});

$(document).on('click','.statusC',function(){
		
		if(confirm("Do you want to Change the Status?")){
		var stat= $(this).data('status');
		var id= $(this).data('id');
		var thisel = $(this);
	
	$.ajax({
		type:'post',
		url:issueStatusUrl,
		data:{'status':stat,id:id},
		headers: {
                    'X-CSRF-TOKEN':CSRF_TOKEN 
                },
		
		success:function(response){
			
		if(response==0){
				
			//console.log("in----0");
						thisel.prev('label.stLabel').html('Pending');
						thisel.data('status',0);
		                thisel.prev('label.stLabel').removeClass('btn-success').addClass('btn-danger');
						location.reload();
						//thisel.prev('label.stLabel').addClass('btn-danger');
						}
		if(response==1){
			
			//console.log("in----1");
			thisel.prev('label.stLabel').html('Addressed');
			thisel.data('status',1);
			thisel.prev('label.stLabel').removeClass('btn-danger').addClass('btn-success');
			location.reload();
			//thisel.prev('label.stLabel').addClass('btn-success');
			}
		
		}
	});}
	}); 

