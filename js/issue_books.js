$(document).ready(function(){	
	var issuedBookRecords = $('#issuedBookListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "issue_books_action.php",
	        type: "POST",
	        data: { action: 'listIssuedBook' },
	        dataType: "json"
	    },
	    "columnDefs": [
	        {
	            "targets": [0,8],    // Make these columns sortable
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

	$('#issueBook').click(function(){
		// Reset the form and populate data
		$('#issuedBookForm')[0].reset();				
		$('.modal-title').html("<i class='fa fa-plus'></i> Add book");					
		$('#action').val('issueBook');
		$('#save').val('Save');

		$('#add-issued-book-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-issued-book-modal").on("shown.bs.modal", function () {
			$('#issuedBookForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add book");					
			$('#action').val('issueBook');
			$('#save').val('Save');
		});
	});	
	$("#issuedBookListing").on('click', '.update', function(){
		var issuebookid = $(this).attr("id");
		var action = 'getIssueBookDetails';
		$.ajax({
			url:'issue_books_action.php',
			method:"POST",
			data:{issuebookid:issuebookid, action:action},
			dataType:"json",
			success:function(respData){				
				$("#add-issued-book-modal").on("shown.bs.modal", function () { 
					$('#issuedBookForm')[0].reset();
					respData.data.forEach(function(item){
						$('#issuebookid').val(item['issuebookid']);							
						$('#book').val(item['bookid']);						
						$('#users').val(item['userid']);
						$('#expected_return_date').val(item['expected_return_date']);
						$('#return_date').val(item['return_date_time']);						
						$('#status').val(item['status']);						
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit issued book");
					$('#action').val('updateIssueBook');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show');	
			}
		});
	});
	
	$("#add-issued-book-modal").on('submit','#issuedBookForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"issue_books_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#issuedBookForm')[0].reset();
				$('#add-issued-book-modal').modal('hide');				
				$('#save').attr('disabled', false);
				issuedBookRecords.ajax.reload();
			}
		})
	});		

	$("#issuedBookListing").on('click', '.delete', function(){
		var issuebookid = $(this).attr("id");		
		var action = "deleteIssueBook";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"issue_books_action.php",
				method:"POST",
				data:{issuebookid:issuebookid, action:action},
				success:function(data) {					
					issuedBookRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});