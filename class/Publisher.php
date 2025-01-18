<?php

class Publisher
{
    // Declare properties for the publisher, including the database connection and table name
    public $name;
    public $status;
    public $publisherid;
    private $publisherTable = 'publisher'; // Table name for publisher
    private $conn; // Database connection object

    // Constructor accepts a database connection and assigns it to the connection property
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // List all publishers with pagination, searching, and ordering features
    public function listPublisher()
    {
        // Initial SQL query to fetch publisher details
        $sqlQuery = 'SELECT publisherid, name, status FROM ' . $this->publisherTable;

        // Search functionality: filter results based on a search term if present
        if (!empty($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (publisherid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Order the results based on the column specified in the request
        if (!empty($_POST['order'])) {
            $column = $_POST['order']['0']['column']; // Get the column index
            switch ($column) {
                case '1':
                    $column = 'name'; // If the column index is 1, use 'name' as the sort column
                    break;
                case '2':
                    $column = 'status'; // If the column index is 2, use 'status' as the sort column
                    break;
            }
            // Apply ordering
            $sqlQuery .= ' ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'];
        } else {
            // Default ordering by publisher ID in descending order
            $sqlQuery .= ' ORDER BY publisherid DESC';
        }

        // To get the total number of records, save the original query
        $sqlQueryAll = $sqlQuery;

        // Apply pagination: limit and offset based on the request
        if ($_POST['length'] != -1) {
            $sqlQuery .= ' LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Execute the query with the limit and offset for pagination
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Execute the query again to get the total record count
        $stmtTotal = $this->conn->prepare($sqlQueryAll);
        $stmtTotal->execute();
        $allResult = $stmtTotal->get_result();
        $allRecords = $allResult->num_rows;

        // Get the number of records returned in the current query
        $displayRecords = $result->num_rows;
        $records = array();
        $count = 1;

        // Process the result and format it for DataTables
        while ($publisher = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count; // Row number
            $rows[] = ucfirst($publisher['name']); // Capitalize the first letter of the name
            $rows[] = $publisher['status']; // Publisher status
            // Create action buttons for editing and deleting
            $rows[] = '<div class="d-flex gap-2">
                        <button type="button" name="update" id="' . $publisher['publisherid'] . '" class="btn app-btn-primary update">
                            <span class="glyphicon glyphicon-edit" title="Edit">Edit</span>
                        </button>
                        <button type="button" name="delete" id="' . $publisher['publisherid'] . '" class="btn app-btn-secondary delete">
                            <span class="glyphicon glyphicon-remove" title="Delete">Delete</span>
                        </button>
                    </div>';
            $records[] = $rows; // Add formatted row to records array
            $count++;
        }

        // Return data in JSON format as expected by DataTables
        $output = array(
            'draw' => intval($_POST['draw']),
            'iTotalRecords' => $displayRecords,
            'iTotalDisplayRecords' => $allRecords,
            'data' => $records
        );

        echo json_encode($output); // Output the data as JSON
    }

    // Insert a new publisher into the database
    public function insert()
    {
        // Ensure the name is provided and the user is logged in (checked via session)
        if ($this->name && $_SESSION['userid']) {
            // Prepare the SQL query to insert a new publisher
            $stmt = $this->conn->prepare('INSERT INTO ' . $this->publisherTable . '(`name`, `status`) VALUES(?, ?)');

            // Sanitize inputs to avoid any malicious code
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));

            // Bind parameters and execute the statement
            $stmt->bind_param('ss', $this->name, $this->status);

            // Return true if the insertion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if insertion failed
    }

    // Update an existing publisher's details
    public function update()
    {
        // Ensure the name, publisherid, and user session exist
        if ($this->name && $_SESSION['userid']) {
            // Prepare the SQL query to update publisher information
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->publisherTable . ' 
                SET name = ?, status = ?
                WHERE publisherid = ?');

            // Sanitize the inputs
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->publisherid = htmlspecialchars(strip_tags($this->publisherid));

            // Bind parameters and execute the statement
            $stmt->bind_param('ssi', $this->name, $this->status, $this->publisherid);

            // Return true if the update is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the update failed
    }

    // Delete a publisher from the database
    public function delete()
    {
        // Ensure the publisherid exists and the user is logged in
        if ($this->publisherid && $_SESSION['userid']) {
            // Prepare the SQL query to delete a publisher
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->publisherTable . ' 
                WHERE publisherid = ?');

            // Sanitize the publisherid
            $this->publisherid = htmlspecialchars(strip_tags($this->publisherid));

            // Bind parameters and execute the statement
            $stmt->bind_param('i', $this->publisherid);

            // Return true if the deletion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the deletion failed
    }

    // Retrieve details of a specific publisher
    public function getPublisherDetails()
    {
        // Ensure publisherid and user session exist
        if ($this->publisherid && $_SESSION['userid']) {
            // Prepare the SQL query to get publisher details by publisherid
            $sqlQuery = 'SELECT publisherid, name, status 
                         FROM ' . $this->publisherTable . ' 
                         WHERE publisherid = ?';

            // Prepare and bind parameters
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->publisherid);
            $stmt->execute();
            $result = $stmt->get_result();

            // Process the result and return it as JSON
            $records = array();
            while ($publisher = $result->fetch_assoc()) {
                $rows = array();
                $rows['publisherid'] = $publisher['publisherid'];
                $rows['name'] = $publisher['name'];
                $rows['status'] = $publisher['status'];
                $records[] = $rows;
            }

            $output = array(
                'data' => $records
            );
            echo json_encode($output); // Output publisher details in JSON format
        }
    }
}
?>
