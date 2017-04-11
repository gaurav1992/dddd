$(document).ready(function() {
if(typeof driverBonusHistory !== 'undefined'){
  	var driverRideListTable=$('#bonusHistory').DataTable({
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
        "searching" : false,
		"ajax": {
		"url": driverBonusHistory,
		"data": function ( d ) {
					d.row_id = $('#bonusHistory').attr('data-row-id');
				}
	  }
	
	});
  }
});