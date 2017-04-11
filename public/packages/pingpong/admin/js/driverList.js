$(document).ready(function() {
	 /**
	  * Active Driver List Table 
	  **/
	  //alert(activeDriverList);
	 if(typeof activeDriverList !== 'undefined'){
	  	var activeDriverListTable=$('#activeDriverList').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": activeDriverList,
			"data": function ( d ) {
				d.state = $('#stateDriver').val();
				d.city = $('#cityDriver').val();
				d.startDate = $('#startDatedriverlist').val();
				d.endDate = $('#endDatedriverlist').val();
			},
			"complete": function (json) {  
				console.log(json.responseJSON.recordsTotal);
				$(".totalDR").html(json.responseJSON.recordsTotal);
				
			},
			
		  },
		  "fnDrawCallback":function(){
	             if(jQuery('table#activeDriverList td').hasClass('dataTables_empty')){
	                jQuery('.drlists').hide();
	             } else {
	               jQuery('.drlists').show();
	             }
	        }
		
	 	});
	}	
	if($('#activeDriverList').length){
		var tableTools = new $.fn.dataTable.Buttons( activeDriverListTable, {
			buttons: [{ extend: 'excel', 
						sRowSelect:'multi',
						//title: 'All Drivers List',
						text: 'Generate Report', 
						 /* action: function ( e, dt, node, conf ) {
							console.log( 'Button 2 clicked on' );
						}, */
						/* customize: function ( xlsx ){
							var sheet = xlsx.xl.worksheets['sheet1.xml'];
			 
							// jQuery selector to add a border
							$('row c[r*="9"]', sheet).attr( 's', '25' );
						}, */
						className: 'btn btn-default color-blue drlists genRepoBtn',
						exportOptions:{columns: [0,1, 2,3,4, 5,6,7,8,9,10,11,12,13,14,15,16,17,19,20,22]}}]
		});
				
				//console.log(tableTools);
				//$(tableTools.container() ).insertAfter('#drList');
				
	}
	 $(document).on("click", ".delImage", function() {
 		var r = confirm("Are you sure, you want to Delete the profile picture?");
		var delbtn=$(this);
 		if (r == true){
 			 var id = $(this).data('driverid');
 			 console.log(id);
 				$.ajax({
                type: 'post',
                url: delImageUrl,
                data: 'id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr){
                    /// alert(loadingImage);
                    //$('.actions li:first-child a').hide();
                    $('body').append('<div id="loadering"><img src="' + loadingImage + '"></div>');
                    //return false;
                },
                success: function(returnData) {
                    $('#loadering').remove(); 
                    console.log(returnData);
					if(returnData=="deleted"){
						$("#profile_pic").attr("src","http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g");
						delbtn.hide();
					}
                }
            });

 		}
	 });
	
	  /**
     * Ajax call for suspend and active passengers
     **/
    $(document).on("click", ".suspendDriver,.activateDriver", function() {
        if ($(this).hasClass('suspendDriver')) {
            var r = confirm("Are you sure, you want to suspend this account?");
        } else if ($(this).hasClass('activateDriver')) {
            var r = confirm("Are you sure, you want to activate this account?");
        }

        if (r == true){
            var id = $(this).data('userid');
            var cell = $(this);
            var RowId = $(this).closest("tr").find('td:eq(17)');
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
					activeDriverListTable.ajax.reload();
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
	
	
	 if(typeof dzbnsEarningAjax !== 'undefined'){
		
	  	var refDrDetailsTable =$('#refDrDetails').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			"searching": false,
			 "columnDefs": [ {
	          "targets": 7,
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": dzbnsEarningAjax,
			"data": function ( d ) {
				d.driverId = driverId;	
			}	
		  }
	 	});
	}

	   /**
	  * Revoke Driver List Table 
	  **/
	  //alert(activeDriverList);
	if(typeof revokeDriverList !== 'undefined'){
	  	var revokedriver=$('#RevokeDriverList').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": revokeDriverList,
			"data": function ( d ) {
				d.state = $('#stateDriver').val();
				d.city = $('#cityDriver').val();
				d.startDate = $('#startDate').val();
				d.endDate = $('#endDate').val();
			}
			
		  },
		  "fnDrawCallback":function(){
	             if(jQuery('table#RevokeDriverList td').hasClass('dataTables_empty')){
	                jQuery('.b2,#RevokeDriverList_paginate').hide();
	             } else {
	               jQuery('.b2,#RevokeDriverList_paginate').show();
	             }
				}
		
		});
	}
	
		
	/**
	  * SEARCHING ON CITY 
	  **/
	$('#cityDriver').on('change',function(){
				revokedriver.ajax.reload();
		    });
	
	/**
	  * SEARCHING ON STATE 
	  **/
	$('.Restate').on('change',function(){
				var selectedValue = $(this).val();
				revokedriver.ajax.reload();
			});
	 
	if($('#RevokeDriverList').length){
				var tableTools = new $.fn.dataTable.Buttons( revokedriver, {
			    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue b2 genRepoBtn',exportOptions:{columns: [0,1, 2,3,4, 5,6]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#driverlist');	
	}

	//All Log hostory for admin 
	

	/*Avinash thakur 29th july initialize bonus history table and add data to it*/
	if(typeof driverBonusHistory !== 'undefined'){ 
	  	var driverBonusTable=$('#BonusHistory').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": driverBonusHistory,
			"data": function ( d ) {
						
						d.useriddriver = userid;
						d.startDate = $('#bonusStartDate').val();
						d.endDate = $('#bonusEndDate').val();
					}
		  }
		
		});
	}
	if($('#BonusHistory').length){
				var tableTools = new $.fn.dataTable.Buttons( driverBonusTable, {
			    	buttons: [{ extend: 'pdf',title: drID+'Dezi Bonus', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [0,1, 2,3,4, 5]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#dezibns');	
	}
	/*bonus history table end*/
	  /**
	  * Active Driver List Table 
	  **/
	  //alert(activeDriverList);
	if(typeof driverRideList !== 'undefined'){

	  	var driverRideListTable=$('#driverRideHisotry').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [{
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": driverRideList,
			"data": function ( d ) {
						d.useriddriver = userid;
						d.startDate = $('#rideStartDate').val();
						d.endDate = $('#rideEndDate').val();
						d.rideStatus = $('#rideStatus').val();
					},
			"complete": function (json) {                       
				$(".ridesTaken").html(json.responseJSON.recordsTotal);
				
			},
		  },
		  "fnDrawCallback":function(){
	             if(jQuery('table#driverRideHisotry td').hasClass('dataTables_empty')){
	                jQuery('.drh').hide();
	             } else {
	               jQuery('.drh').show();
	             }
				}
		
		});
		
		var IssuesHisotryTable=$('#IssuesHisotry').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {  
			"url": isuueHistoryList,
			"data": function ( d ){
						d.useriddriver = userid;
						d.startDate = $('#issueStartDate').val();
						d.endDate = $('#issueEndDate').val();
					},
			"complete": function (json) {                       
                        $(".issuesRaised").html(json.responseJSON.recordsTotal);
						
                    },
		  },
		  "fnDrawCallback":function(){
	             if(jQuery('table#rejectedDriverList td').hasClass('dataTables_empty')){
	                jQuery('.b2').hide();
	             } else {
	               jQuery('.b2').show();
	             }
				}
		
		});
		var EarningHisotryTable=$('#EarningHisotry').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {  
			"url": EarningHisotryList,
			"data": function ( d ) {
						d.useriddriver = userid;
						d.startDate = $('#earningStartDate').val();
						d.endDate = $('#earningEndDate').val();
					}
		  }
		
		});
		
		if($('#EarningHisotry').length){
				var tableTools = new $.fn.dataTable.Buttons( EarningHisotryTable, {
			    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [0,1, 2,3,4, 5,6,7]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#end3');	
		}
		

		var HourLogTable=$('#HourLogTable').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax":{  
			"url": hourLog,
			"data": function ( d ) {
						d.useriddriver = userid;
						d.startDate = $('#hourlogStartDate').val();
						d.endDate = $('#hourlogEndDate').val();
					}
		  }
		
		});
		
		if($('#HourLogTable').length){
				var tableTools = new $.fn.dataTable.Buttons( HourLogTable, {
			    	buttons: [{ extend: 'pdf',title: drivername+'| HOUR LOG', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [0,1, 2,3,4]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#end5');	
		}
		
var bankDetailsTable=$('#bankdetTable').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [{
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax": {
			"url": bankdetailsUrl,
			"data": function ( d ) {
						d.driverId = userid;
						
					},
			},
		  "fnDrawCallback":function(){
	             if(jQuery('table#bankdetTable td').hasClass('dataTables_empty')){
	                jQuery('.bnkbtn1').hide();
	             } else {
	               jQuery('.bnkbtn1').show();
	             }
				}
		
		});		
			if($('#bankdetTable').length){
				var tableTools = new $.fn.dataTable.Buttons( bankDetailsTable, { 
			    	buttons: [{ extend: 'pdf',title:'Bank Details |'+drID, text: 'Generate Report', className: 'btn btn-default color-blue bnkbtn1 genRepoBtn',exportOptions:{columns: [0,1,2,3,4]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#bankbtn');	
		}
			
		var documentTable=$('#documentTable').DataTable({
			  "language": {
				  "paginate": {
				  "previous": "<<",
				  "next":">>"
				
			  }},
			"processing": true,
			"responsive": true,
			 "columnDefs": [ {
	          "targets": 'no-sort',
	          "orderable": false,
	    	} ],
	        "serverSide": true,
			"ajax":{  
			"url": documentAjax,
			"data": function ( d ) {
						d.useriddriver = userid;
						d.startDate = $('#documentStartDate').val();
						d.endDate = $('#documentEndDate').val();
					}
		  }
		
		});
		
		if($('#documentTable').length){
				var tableTools = new $.fn.dataTable.Buttons( documentTable, {
			    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [0,1, 2,3]}}]
				});
				
				//console.log(tableTools);
				$(tableTools.container() ).insertAfter('#end6');	
		}
	}
	
	
	$(function () {
			    $("#rideStartDate,#rideEndDate,#issueStartDate,#bonusStartDate,#bonusEndDate,#issueEndDate,#earningStartDate,#earningEndDate,#hourlogStartDate,#hourlogEndDate,#documentStartDate,#documentEndDate").each(function () {
			        $(this).datepicker().on('changeDate', function (ev) {
			            $(this).datepicker("hide");
			        });
			    });
				
				validateDateRange('rideStartDate','rideEndDate');
				validateDateRange('issueStartDate','issueEndDate');
				validateDateRange('bonusStartDate','bonusEndDate');
				validateDateRange('earningStartDate','earningEndDate');
				validateDateRange('hourlogStartDate','hourlogEndDate');
				validateDateRange('documentStartDate','documentEndDate');

			    $(document).on('change', '#rideStartDate, #rideEndDate,#rideStatus', function () {
			       driverRideListTable.ajax.reload();
			       
			    });
				$(document).on('change', '#bonusStartDate, #bonusEndDate', function () {
			       driverBonusTable.ajax.reload();
			       
			    });
				
			    $(document).on('change', '#issueStartDate, #issueEndDate', function () {
			       IssuesHisotryTable.ajax.reload();
			       
			    });
				$(document).on('change', '#earningStartDate, #earningEndDate', function () {
			       EarningHisotryTable.ajax.reload();
			       
			    });
				$(document).on('change', '#hourlogStartDate, #hourlogEndDate', function () {
			       HourLogTable.ajax.reload();
			       
			    });
				$(document).on('change', '#documentStartDate, #documentEndDate', function () {
			       documentTable.ajax.reload();
			       
			    });
			    

	});
			
	/**
	  *CHANGED POSITION OF GENERATE REPORT BUTTON 
	  **/
	if($('#driverRideHisotry').length){
		var tableTools = new $.fn.dataTable.Buttons( driverRideListTable, {
	    	buttons: [{ extend: 'pdf',title:'Ride History', text: 'Generate Report', className: 'btn btn-default color-blue drh genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
		});
		
		//console.log(tableTools);
		$( tableTools.container() ).insertAfter('#end');	
	}
	
	if($('#IssuesHisotry').length){
		var tableTools = new $.fn.dataTable.Buttons( IssuesHisotryTable, {
	    	buttons: [{ extend: 'pdf',title:'Issue History', text: 'Generate Report', className: 'btn btn-default color-blue isht genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
		});
		
		//console.log(tableTools);
		$( tableTools.container() ).insertAfter('#end2');	
	}
	 
	$('.state').on('change',function(){
		$('.city').val('');
		activeDriverListTable.ajax.reload();
		
		
	});



	$( "#driver_detail_rideid" ).keyup(function( event ) {	
		
		console.log('ad');
		driverRideListTable.ajax.reload();
	});

	
	/**
  * Ajax call for Revoke driver
  **/
		$( document ).on( "click", ".driver_revoke,.driver_unrevoke", function() {
			//console.log(89);
			if($(this).hasClass('driver_revoke'))
				{
					var r=confirm("Do you want to revoke the driver ?");
				}
			else if($(this).hasClass('driver_unrevoke'))
				{
					var r=confirm("Do you want to re-allow ?");
				}
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
							//RowId.html('Revoke');
							cell.html('Revoked');
							cell.removeClass( "driver_revoke" ).addClass( "driver_unrevoke" );
							cell.removeClass( "btn-primary" ).addClass( "btn-success" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"passenger_Active" );
							cell.data( 'action',"driver_unrevoke" );
							//console.log(RowId);
							if(activeDriverListTable)
							{
								activeDriverListTable.ajax.reload();
							}
							
						}
						if(returnData=='unrevokedsuccess')
						{ 
							cell.parent().parent().parent().hide();
							//RowId.html('Revoked');
							cell.html('Revoke');
							cell.removeClass( "driver_unrevoke" ).addClass( "driver_revoke" );
							cell.removeClass( "btn-success" ).addClass( "btn btn-primary" );
							//cell.removeAttr("data-action" ).attr( 'data-action',"driver_suspend" );
							cell.data( 'action',"driver_revoke" );
							//console.log(RowId);
							
						}
						if(window.location.href != alldriver)
						{
							
							location.reload(); 
						}
					}
				});
			}
		});	


		
	/**
	  * SEARCHING ON CITY 
	  **/
	$('.city').on('change',function(){
			activeDriverListTable.ajax.reload();
	});
	
	$('#startDatedriverlist,#endDatedriverlist').on('change',function(){
			activeDriverListTable.ajax.reload();
	});
		
		

	if($('#activeDriverList').length){
			var tableTools = new $.fn.dataTable.Buttons( activeDriverListTable, {
		    	buttons: [{ extend: 'pdf', text: 'Generate Report', className: 'btn btn-default color-blue genRepoBtn',exportOptions:{columns: [1, 2,3,4, 5,6,7 ]}}]
			
			});
			//console.log(tableTools);
			$( tableTools.container() ).insertAfter('#driverlist');	
	}

		//For date picker

	$(function () {
		   
		     $("#promo_till_date,#startDatedriverlist,#endDatedriverlist").each(function () {
		        $(this).datepicker().on('changeDate', function (ev) {
		            $(this).datepicker("hide");
		        });
		    });
			validateDateRange('startDatedriverlist','endDatedriverlist');

	});
			
			
			

	
});

/**
	  ** GET CITY WITH AJAX CALL
	  **/
	$( document ).on( "change", ".Restate", function(){
		var stateCode= $(this).val();
		//console.log(stateCode);
		$.ajax({
			 type:'get',
			 url:revokedriverUrl,
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
				$( ".Recity" ).html(returnData);
				
			 }
		});
	});	
	
	$( document ).on( "change", "#stateCh", function(){
		var stateCode= $(this).val();
		//console.log(stateCode);
		$.ajax({
			 type:'get',
			 url:driverChargesurl,
			 data:'stateCode='+stateCode+'&_token='+CSRF_TOKEN,
			 beforeSend: function( xhr ) {
				$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
				//return false;
			},
			 success:function(returnData)
			 {
				$('#loadering').remove();
				 //console.log(returnData);
				// var parsedJson=$.parseJSON(returnData);
				$( ".cityblock" ).html(returnData);
				
			 }
		});
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
			//thisel.closest('.issueDate').html();
			//thisel.prev('label.stLabel').addClass('btn-success');
			}
		
		}
	});}
	}); 



	


	
