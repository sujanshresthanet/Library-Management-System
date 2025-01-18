$(document).ready(function(){	
	var bookRecords = $('#bookListing').DataTable({
	    "lengthChange": true,            // Allow changing the number of records per page
	    "processing": true,              // Show processing indicator when data is being fetched
	    "serverSide": true,              // Enable server-side processing
	    "bFilter": true,                 // Enable the global search filter (filter all columns)
	    'serverMethod': 'post',          // Use POST for server requests
	    "order": [],                     // Disable initial sorting
	    "ajax": {
	        url: "books_action.php",
	        type: "POST",
	        data: { action: 'listBook' },
	        dataType: "json"
	    },
	    "columnDefs": [
	        {
	            "targets": [9],    // Make these columns sortable
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
	
	$('#addBook').click(function(){
		// Reset the form and populate data
		$('#bookForm')[0].reset();				
		$('.modal-title').html("<i class='fa fa-plus'></i> Add book");					
		$('#action').val('addBook');
		$('#save').val('Save');

		$('#add-book-modal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#add-book-modal").on("shown.bs.modal", function () {
			$('#bookForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add book");					
			$('#action').val('addBook');
			$('#save').val('Save');
		});
	});		
	
	$("#bookListing").on('click', '.update', function(){
		var bookid = $(this).attr("id");
		var action = 'getBookDetails';
		$.ajax({
			url:'books_action.php',
			method:"POST",
			data:{bookid:bookid, action:action},
			dataType:"json",
			success:function(respData){		
				// Reset the form and populate data
            	$('#bookForm')[0].reset();
				$("#add-book-modal").on("shown.bs.modal", function () { 
					respData.data.forEach(function(item){						
					$('#bookid').val(item['bookid']);						
					$('#name').val($('<textarea/>').html(item['name']).text());
					$('#isbn').val(item['isbn']);
					$('#no_of_copy').val(item['no_of_copy']);
					$('#category').val(item['categoryid']);
					$('#rack').val(item['rackid']);
					$('#publisher').val(item['publisherid']);
					$('#author').val(item['authorid']);							
					$('#status').val(item['status']);	
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit book");
					$('#action').val('updateBook');
					$('#save').val('Save');						
				});	
				}).modal({
					backdrop: 'static',
					keyboard: false
				}).modal('show'); // Explicitly show the modal;			
			}
		});
	});
	
	$("#add-book-modal").on('submit','#bookForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"books_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#bookForm')[0].reset();
				$('#add-book-modal').modal('hide');				
				$('#save').attr('disabled', false);
				bookRecords.ajax.reload();
			}
		})
	});		

	$("#bookListing").on('click', '.delete', function(){
		var bookid = $(this).attr("id");		
		var action = "deleteBook";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"books_action.php",
				method:"POST",
				data:{bookid:bookid, action:action},
				success:function(data) {					
					bookRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});
	
});