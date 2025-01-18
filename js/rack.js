$(document).ready(function(){	

	var rackRecords = $('#rackListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "rack_action.php",
	        type: "POST",
	        data: { action: 'listRack' },
	        dataType: "json"
	    },
	    "columnDefs": [
	        {
	            "targets": [0,3],    // Make these columns sortable
	            // "targets": 'all',        // Apply to all columns to make them sortable
	            "orderable": false
	        }
	    ],
	    "pageLength": 5,                 // Default number of rows per page
	    "lengthMenu": [                  // Define page length options
	        [2, 5, 10, 25, 50, -1],         // Available page length options
	        [2, 5, 10, 25, 50, "All"]       // Labels for the options
	    ],
	    "language": {
	        "search": "Filter all:",     // Custom filter label
	        "lengthMenu": "Show _MENU_ entries"  // Custom text for the page length dropdown
	    },
	    "initComplete": function(settings, json) {
	        // Add sorting icons after initialization (if needed)
	        $('.dataTables_wrapper .dataTables_filter input').addClass('form-control');  // Custom input field style
	    }
	});
	
	$('#addRack').click(function(){
		// Reset the form and populate data
		$('#rackForm')[0].reset();				
		$('.modal-title').html("<i class='fa fa-plus'></i> Add rack");					
		$('#action').val('addRack');
		$('#save').val('Save');

		$('#add-rack-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-rack-modal").on("shown.bs.modal", function () {
			$('#rackForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add rack");					
			$('#action').val('addRack');
			$('#save').val('Save');
		});
	});	
	
	$("#rackListing").on('click', '.update', function(){
		var rackid = $(this).attr("id");
		var action = 'getRackDetails';
		$.ajax({
			url:'rack_action.php',
			method:"POST",
			data:{rackid:rackid, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-rack-modal").on("shown.bs.modal", function () { 
					$('#rackForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#rackid').val(item['rackid']);						
						$('#name').val(item['name']);	
						$('#status').val(item['status']);						
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit rack");
					$('#action').val('updateRack');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');		
			}
		});
	});
	
	$("#add-rack-modal").on('submit','#rackForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"rack_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#rackForm')[0].reset();
				$('#add-rack-modal').modal('hide');				
				$('#save').attr('disabled', false);
				rackRecords.ajax.reload();
			}
		})
	});		

	$("#rackListing").on('click', '.delete', function(){
		var rackid = $(this).attr("id");		
		var action = "deleteRack";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"rack_action.php",
				method:"POST",
				data:{rackid:rackid, action:action},
				success:function(data) {					
					rackRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});