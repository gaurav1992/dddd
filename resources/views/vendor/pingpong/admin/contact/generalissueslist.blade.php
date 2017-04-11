@extends($layout)

@section('customjavascript')
<script>

var generalIssueStatusChangeUrl = "{!! route('generalIssueStatusChange') !!}";
var GEN_CSRF_TOKEN = '<?php echo csrf_token(); ?>';


$("#issueStartDate, #issueEndDate").datepicker();

$(document).ready(function() {
	$(document ).on( "click", "#hiddenUser" , function() {
		var r=confirm("Do you want to perform action!");
		if(r == true){
			var id= $(this).data('userid');
			var RowId = $(this).closest("tr");

			$.ajax({
				type : "get",
				url : messageUrl,
				data : 'id='+id+'&_token='+GEN_CSRF_TOKEN,
				success:function(returnData)
				{
					if(returnData=='deleteSuccess')
					{
						RowId.remove();

					}
					
				}
				
			});
		}
		
	});
	
	var generalIssueTable=$('#generalIssueList').DataTable({
			"language": {
				"paginate": {
				"previous": "<<",
				"next":">>"
				}
			},
		"processing": true,
		"responsive":true,
		"serverSide": true,
		"ajax": {
			"url": "{!! route('generalIssuesAjax') !!}",
			"data": function ( d ) {
					
				d.startDate = $('#issueStartDate').val();
				d.endDate = $('#issueEndDate').val();
			}
		}
		
	});
	
	$('#issueStartDate,#issueEndDate').on('change',function(){
			generalIssueTable.ajax.reload();
	});
	
	$(document).on('change','.generalIssueStatusChange',function(){
		
		if(confirm("Do you want to Change the Status?")){
			var stat= $(this).val();
			
			var id= $(this).data('id');
			var thisel = $(this);
			
	
			$.ajax({
				type:'post',
				url:generalIssueStatusChangeUrl,
				data:{'status':stat,id:id},
				headers: {
							'X-CSRF-TOKEN':GEN_CSRF_TOKEN 
						},
				
				success:function(response){
					
					generalIssueTable.ajax.reload();
				}
			});
		}
	}); 
	 /**
     *CHANGED POSITION OF GENERATE REPORT BUTTON 
     **/
	 if ($('#generalIssueList').length) {
        var tableTools = new $.fn.dataTable.Buttons(generalIssueTable, {
            buttons: [{
                extend: 'pdf',
                text: 'Pdf-Report',
				title:'general_issue',
                className: 'btn-info color-blue genRepoBtn',
                exportOptions: {
                    columns: [0,1, 2, 3, 4, 5]
                }
            },{
                extend: 'excel',
                text: 'Excel-Report',
				title:'general_issue',
                className: 'btn-info color-blue genRepoBtn',
                exportOptions: {
                    columns: [0,1, 2, 3, 4, 5]
                }
            }]

        });
        //console.log(tableTools);
        $(tableTools.container()).insertAfter('#end2');
    }
});


</script>
<script src="{!! admin_asset('js/general_issue.script.js') !!}" type="text/javascript"></script> 

@stop

@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'General Issues' !!} 
	</h1>
<div style="clear:both"></div>

@stop
@section('content')
<div style="clear:both;margin-bottom:20px;"></div>
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif
	
	<!-- Passanger Test Ride History -->
      <div class="row">
		  
        <div class="col-md-12 pad0">
			
          <div class="box">
			  
            <div class="table-responsive">
			                             
                <div class="box_search"> 
                  
                  <div class="form-group form_fl">
                    <label> Start Date </label> <br>
                    <input type="text" Placeholder="Start Date" readonly id="issueStartDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                
                  <div class="form-group form_fl" id="end2">
                    <label>End Date</label> <br>
                    <input type="text" Placeholder="End Date" readonly id="issueEndDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
               
               </div>
              
               <table class="table" id="generalIssueList" style="width:100%"> 
                 <thead>
                      <tr>
                          <th>S.No</th>
                          <th>Passenger ID</th>
                          <th>Passenger Name</th>
                          <!--<th>Message</th>-->
                          <th style="width:175px">Issues Type</th>
                          <th>Status</th>
                          <th>Dated on</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                </table>
              </div> 
            </div>
          </div>
        </div>
        <!--  -->


@stop

@stop


