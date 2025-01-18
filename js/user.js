$(document).ready(function(){	
	var userRecords = $('#userListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "user_action.php",
	        type: "POST",
	        data: { action: 'listUsers' },
	        dataType: "json"
	    },
	    "columnDefs": [
	        {
	            "targets": [0,4],    // Make these columns sortable
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
		
	$('#addUser').click(function(){
		// Reset the form and populate data
		$('#userForm')[0].reset();				
		$('.modal-title').html("<i class='fa fa-plus'></i> Add user");					
		$('#action').val('addUser');
		$('#save').val('Save');

		$('#add-user-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-user-modal").on("shown.bs.modal", function () {
			$('#userForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add user");					
			$('#action').val('addUser');
			$('#save').val('Save');
		});
	});	
	$("#userListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getUserDetails';
		$.ajax({
			url:'user_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-user-modal").on("shown.bs.modal", function () { 
					$('#userForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#id').val(item['id']);						
						$('#role').val(item['role']);	
						$('#first_name').val(item['first_name']);
						$('#last_name').val(item['last_name']);	
						$('#email').val(item['email']);	
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit user");
					$('#action').val('updateUser');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');
			}
		});
	});
	
	$("#add-user-modal").on('submit','#userForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"user_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#userForm')[0].reset();
				$('#add-user-modal').modal('hide');				
				$('#save').attr('disabled', false);
				userRecords.ajax.reload();
			}
		})
	});		

	$("#userListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteUser";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"user_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					userRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});