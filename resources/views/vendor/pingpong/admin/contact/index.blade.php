@extends($layout)


@section('customjavascript')
<script>
var messageUrl = "{!! route('messageDel') !!}";

$(document).ready(function() {
	$(document ).on( "click", "#hiddenUser" , function() {
		var r=confirm("Do you want to perform action!");
		if(r == true){
			var id= $(this).data('userid');
			var RowId = $(this).closest("tr");

			$.ajax({
				type : "get",
				url : messageUrl,
				data : 'id='+id+'&_token='+CSRF_TOKEN,
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
	var oTable=$('#messageList').DataTable({
			"language": {
			"paginate": {
			"previous": "<<",
			"next":">>"
		}},

		"processing": true,
		"responsive":true,
		"searching":true,
		"serverSide": true,
		"ajax": {
			"url": "{!! route('messagelistajax') !!}",
		}
		
	});

});

</script>


@stop

@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Contact Messages' !!} 
	</h1>
<div style="clear:both"></div>

@stop
@section('content')
<div style="clear:both;margin-bottom:20px;"></div>
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif
	<div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="table-responsive">
	<table class="table" id="messageList" style="width:100%">
		<thead>
			<th>S.No</th>
			<th>User Id</th>
			<th>Full Name</th>
			<th>Email</th>
			<th>Source</th>			
			<th>Status</th>
			<th class="text-center">Action</th>
		</thead>
	</table>
				</div>
          </div>
        </div>
      </div>
@stop

@stop


