$(document).ready(function(){	
	var userRecords = $('#authorListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "author_action.php",
	        type: "POST",
	        data: { action: 'listAuthor' },
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
	
	$('#addAuthor').click(function(){
		$('#add-author-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-author-modal").on("shown.bs.modal", function () {
			$('#authorForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add author");					
			$('#action').val('addAuthor');
			$('#save').val('Save');
		});
	});		
	
	$("#authorListing").on('click', '.update', function(){
		var authorid = $(this).attr("id");
		var action = 'getAuthorDetails';
		$.ajax({
			url:'author_action.php',
			method:"POST",
			data:{authorid:authorid, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-author-modal").on("shown.bs.modal", function () { 
					$('#authorForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#authorid').val(item['authorid']);						
						$('#name').val(item['name']);	
						$('#status').val(item['status']);						
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit author");
					$('#action').val('updateAuthor');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');
			}
		});
	});
	
	$("#add-author-modal").on('submit','#authorForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"author_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#authorForm')[0].reset();
				$('#add-author-modal').modal('hide');				
				$('#save').attr('disabled', false);
				userRecords.ajax.reload();
			}
		})
	});		

	$("#authorListing").on('click', '.delete', function(){
		var authorid = $(this).attr("id");		
		var action = "deleteAuthor";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"author_action.php",
				method:"POST",
				data:{authorid:authorid, action:action},
				success:function(data) {					
					userRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});