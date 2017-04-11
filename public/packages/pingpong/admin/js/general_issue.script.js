$(function() 
{
	validateDateRange('issueStartDate','issueEndDate');
    $("#issueStartDate,#issueEndDate").each(function () {
        $(this).datepicker().on('changeDate', function (ev) {
            $(this).datepicker("hide");
           
        });
        
    });
		
});	  
