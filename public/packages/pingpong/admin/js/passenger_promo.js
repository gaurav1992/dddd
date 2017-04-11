$(document).ready(function(){
	   
		var start = new Date();
		// set end date to max one year period:
		var end = new Date(new Date().setYear(start.getFullYear()+1));

		$('#promo_till_date').datepicker({
			startDate : start,
			
		}).on('changeDate', function(){
			
			 $(this).datepicker("hide");
		}); 

		
	  	var activePassengerPromoListTable=$('#passengerPromoCode').DataTable({
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
				"url": ajaxpassengerPromos,
				"complete": function (json) {                       
					$(".totalDR").html(json.responseJSON.recordsTotal);
				},
			
			},
			"fnDrawCallback":function(){
	             if(jQuery('table#passengerPromoCode td').hasClass('dataTables_empty')){
	                jQuery('.drlists').hide();
	             } else {
	               jQuery('.drlists').show();
	             }
	        }
		
	 	});
	 	
	 	if($('#passengerPromoCode').length){
			var tableTools = new $.fn.dataTable.Buttons( activePassengerPromoListTable, {
				buttons: [{ extend: 'pdf', title: 'All Passengers Promo',text: 'Generate Report', className: 'btn btn-default color-blue drlists genRepoBtn',exportOptions:{columns: [0,1,2,3,4]}}]
			});
					
					//console.log(tableTools);
					$(tableTools.container() ).insertAfter('#drList');	
					
		}
	
	
		$( document ).on( "click", ".promo_dlt", function() {
			
			if($(this).hasClass('promo_dlt'))
			{
				var r=confirm("Are you sure, you want to delete this promo ?");
			}
			
			if(r==true){

				var id= $(this).attr('data-promoid');
				var dataAction= $(this).attr('data-action');
				var cell=$(this);
				var RowId = $(this).closest("tr").find('td:eq(7)');
							
				$.ajax({
					type:'post',
					url:deletepassengerpromo,
					data:'action='+dataAction+'&id='+id+'&_token='+PROMO_CSRF_TOKEN,
					beforeSend: function( xhr ) {
						
						
						$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
						//return false;
					},
					success:function(returnData)
					{
						//alert(returnData);

						$('#loadering').remove();
						console.log(returnData);

						if(returnData=='btnpromo_dlt')
						{
							activePassengerPromoListTable.ajax.reload();
							
						}

						if(returnData=='error')
						{ 
							alert('Something went wrong.Kindly referesh and try again.');
							
						}
					}
				});
			}
		});	

		$( document ).on( "keyup", "#promo_code_1", function(){

			var promo_code_name = $(this).val();
			
			if(promo_code_name==''){
				
				$(".duplicate_promo").text("This field is required");
			
			}else{
				
				$(".duplicate_promo").text("");
				if(promo_code_name !=''){
					$.ajax({
				 
					 type:'post',
					 
					 url:promo_code_check,
					 
					 data:'action=check_promo_code&promo_code_name='+promo_code_name+'&_token='+PROMO_CSRF_TOKEN,
					 
					 success:function(returnData)
					 {
						if($.trim(returnData)==1){
							$(".duplicate_promo").text("Promo code already exist");
						}
						
					 }
					});
				}
				
			}
			
		});
		
		$( document ).on( "keyup", "#promo_credit_1", function(){

			var promo_credit_1 = $(this).val();
			
			$(".invalid_pc").text("");
			
			if(promo_credit_1==''){
									$(".invalid_pc").text("This field is required.");	
			}else{
				if($.isNumeric(promo_credit_1)){
					return true;
				}else{
					$(".invalid_pc").text("Please use valid credit value.");	
				}
			}
		});

});


