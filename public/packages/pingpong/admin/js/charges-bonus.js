/** Function for convert 24 hour date format to 12 hour**/
function tConvert (timeString) {
  // Check correct time format and split into components
	var H = +timeString.substr(0, 2);
	var h = (H % 12) || 12;
	var ampm = H < 12 ? " AM" : " PM";
	timeString = h + timeString.substr(2, 3) + ampm;
	return timeString; // return adjusted time or original string
}



$(document).ready(function() {

	/** Call ajax on radio select in driver bonus section **/
	
	$('.readOnly').click(function(){
		return false;
	});

	$('.day').change(function(){

		
		$.ajax({
			type: 'GET',
			url: driverBonusAjax,
			data:'dayId='+$(this).val()+'&_token='+CSRF_TOKEN,
			beforeSend: function( xhr ) {
					/// alert(loadingImage);
					 //$('.actions li:first-child a').hide();
					$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
					//return false;
				},
			success: function(responce){
				$('#loadering').remove();
				responce = JSON.parse(responce);

    			//alert(responce['commission_silver']);
    			$.each(responce, function(i, val) {
				  //$("#" + this).text("My id is " + this + ".");
				  //return (this != "four"); // will stop running to skip "five"
				  //alert(i+'----'+val);
				  if($("#" + i).length){
					 if(i=='to_time' || i=='from_time'){
						val =  tConvert( val );
					 } 
					 $("#" + i).val(val);
				  	
				  }
				  
				});
  			},
		});

	});	
	
	/** Compare 2 time**/
	$.validator.addMethod("comparetotime", function(value, element) {
		var from_time = $('#from_time').val();
		var to_time = value;
		console.log( getHour24(from_time) );
		console.log( getHour24(to_time)  );
		console.log( getHour24(to_time)-getHour24(from_time)  );
		
		//return this.optional(element) || (parseFloat(value) > 0);
	}, "* Amount must be greater than zero");

/**
 * Validation for driver bonus
 */
$("#driverbonus").validate({
			rules: {				
				commission_silver:{
					number:true,					
				},
				commission_gold:{
					number:true,					
				},
				commission_platinum:{
					number:true,					
				},
				commission_diamond:{
					number:true,					
				},
				total_hrs_silver:{
					number:true,					
				},
				total_hrs_gold:{
					number:true,					
				},
				total_hrs_platinum:{
					number:true,					
				},
				total_hrs_diamond:{
					number:true,					
				}
				,
				total_hrs_schedule_silver:{
					number:true,					
				},
				total_hrs_schedule_gold:{
					number:true,					
				},
				total_hrs_schedule_platinum:{
					number:true,					
				},
				total_hrs_schedule_diamond:{
					number:true,					
				}
				,
				acceptance_silver:{
					number:true,					
				},
				acceptance_gold:{
					number:true,					
				},
				acceptance_platinum:{
					number:true,					
				},
				acceptance_diamond:{
					number:true,					
				}
				,
				cancellation_silver:{
					number:true,					
				},
				cancellation_gold:{
					number:true,					
				},
				cancellation_platinum:{
					number:true,					
				},
				cancellation_diamond:{
					number:true,					
				}				
			},
			errorPlacement: function(error, element) {
				//return false;
			}			
			
		});

	
	//scheduled time for driver tier levels
	$('input[name^="from_time"]').each(function() {

	    console.log($(this).val());
	
	});

	

});
