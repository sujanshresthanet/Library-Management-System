$(document).ready(function(){	

	var publisherRecords = $('#publisherListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "publisher_action.php",
	        type: "POST",
	        data: { action: 'listPublisher' },
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

	$('#addPublisher').click(function(){
		$('#add-publisher-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-publisher-modal").on("shown.bs.modal", function () {
			$('#publisherForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add publisher");					
			$('#action').val('addPublisher');
			$('#save').val('Save');
		});
	});		
	
	$("#publisherListing").on('click', '.update', function(){
		var publisherid = $(this).attr("id");
		var action = 'getPublisherDetails';
		$.ajax({
			url:'publisher_action.php',
			method:"POST",
			data:{publisherid:publisherid, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-publisher-modal").on("shown.bs.modal", function () { 
					$('#publisherForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#publisherid').val(item['publisherid']);						
						$('#name').val(item['name']);	
						$('#status').val(item['status']);						
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit publisher");
					$('#action').val('updatePublisher');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');
			}
		});
	});
	
	$("#add-publisher-modal").on('submit','#publisherForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"publisher_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#publisherForm')[0].reset();
				$('#add-publisher-modal').modal('hide');				
				$('#save').attr('disabled', false);
				publisherRecords.ajax.reload();
			}
		})
	});		

	$("#publisherListing").on('click', '.delete', function(){
		var publisherid = $(this).attr("id");		
		var action = "deletePublisher";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"publisher_action.php",
				method:"POST",
				data:{publisherid:publisherid, action:action},
				success:function(data) {					
					publisherRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});