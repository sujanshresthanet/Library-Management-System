$(document).ready(function(){	
	var userRecords = $('#categoryListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "category_action.php",
	        type: "POST",
	        data: { action: 'listCategory' },
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

	$('#addCategory').click(function(){
		// Reset the form and populate data
		$('#categoryForm')[0].reset();				
		$('.modal-title').html("<i class='fa fa-plus'></i> Add category");					
		$('#action').val('addCategory');
		$('#save').val('Save');

		$('#add-category-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-category-modal").on("shown.bs.modal", function () {
			$('#categoryForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add category");					
			$('#action').val('addCategory');
			$('#save').val('Save');
		});
	});	
	$("#categoryListing").on('click', '.update', function(){
		var categoryid = $(this).attr("id");
		var action = 'getCategoryDetails';
		$.ajax({
			url:'category_action.php',
			method:"POST",
			data:{categoryid:categoryid, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-category-modal").on("shown.bs.modal", function () { 
					$('#categoryForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#categoryid').val(item['categoryid']);						
						$('#name').val(item['name']);	
						$('#status').val(item['status']);						
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit category");
					$('#action').val('updateCategory');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');
			}
		});
	});
	
	$("#add-category-modal").on('submit','#categoryForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"category_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#categoryForm')[0].reset();
				$('#add-category-modal').modal('hide');				
				$('#save').attr('disabled', false);
				userRecords.ajax.reload();
			}
		})
	});		

	$("#categoryListing").on('click', '.delete', function(){
		var categoryid = $(this).attr("id");		
		var action = "deleteCategory";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"category_action.php",
				method:"POST",
				data:{categoryid:categoryid, action:action},
				success:function(data) {					
					userRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});