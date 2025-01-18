<?php

class Rack
{
    // Declare properties for the rack including name, status, and rackid
    public $name;
    public $status;
    public $rackid;
    private $rackTable = 'rack'; // Table name for racks
    private $conn; // Database connection object

    // Constructor method accepts a database connection and assigns it to the class
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // List all racks with pagination, searching, and ordering features
    public function listRack()
    {
        // Initial SQL query to select rack details
        $sqlQuery = 'SELECT rackid, name, status FROM ' . $this->rackTable;

        // Check if a search term is provided and modify the query accordingly
        if (!empty($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (rackid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Order the results based on the column and direction from the request
        if (!empty($_POST['order'])) {
            $column = $_POST['order']['0']['column']; // Get the column index for sorting

            switch ($column) {
                case '1':
                    $column = 'name'; // If column index is 1, use 'name' as the sort column
                    break;
                case '2':
                    $column = 'status'; // If column index is 2, use 'status' as the sort column
                    break;
            }
            // Apply ordering based on the column and direction
            $sqlQuery .= ' ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'];
        } else {
            // Default ordering by rackid in descending order
            $sqlQuery .= ' ORDER BY rackid DESC';
        }

        // Save the query for getting total records
        $sqlQueryAll = $sqlQuery;

        // Apply pagination: limit and offset based on the request
        if ($_POST['length'] != -1) {
            $sqlQuery .= ' LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Execute the query with pagination
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Execute the query again to get the total record count without pagination
        $stmtTotal = $this->conn->prepare($sqlQueryAll);
        $stmtTotal->execute();
        $allResult = $stmtTotal->get_result();
        $allRecords = $allResult->num_rows;

        // Get the number of records returned in the current query
        $displayRecords = $result->num_rows;
        $records = array();
        $count = 1;

        // Process the result and format it for DataTables
        while ($rack = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count; // Row number
            $rows[] = ucfirst($rack['name']); // Capitalize the first letter of the name
            $rows[] = $rack['status']; // Rack status
            // Create action buttons for editing and deleting
            $rows[] = '<div class="d-flex gap-2">
                        <button type="button" name="update" id="' . $rack['rackid'] . '" class="btn app-btn-primary update">
                            <span class="glyphicon glyphicon-edit" title="Edit">Edit</span>
                        </button>
                        <button type="button" name="delete" id="' . $rack['rackid'] . '" class="btn app-btn-secondary delete">
                            <span class="glyphicon glyphicon-remove" title="Delete">Delete</span>
                        </button>
                    </div>';
            $records[] = $rows; // Add formatted row to records array
            $count++;
        }

        // Return the data in JSON format as expected by DataTables
        $output = array(
            'draw' => intval($_POST['draw']),
            'iTotalRecords' => $displayRecords,
            'iTotalDisplayRecords' => $allRecords,
            'data' => $records
        );

        echo json_encode($output); // Output the data as JSON
    }

    // Insert a new rack into the database
    public function insert()
    {
        // Ensure name is provided and user session exists
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query to insert a new rack
            $stmt = $this->conn->prepare('INSERT INTO ' . $this->rackTable . '(`name`, `status`) VALUES(?, ?)');

            // Sanitize inputs to avoid security issues
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

    // Update an existing rack's details
    public function update()
    {
        // Ensure name, rackid, and user session exist
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query to update rack information
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->rackTable . ' 
                SET name = ?, status = ?
                WHERE rackid = ?');

            // Sanitize the inputs
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->rackid = htmlspecialchars(strip_tags($this->rackid));

            // Bind parameters and execute the statement
            $stmt->bind_param('ssi', $this->name, $this->status, $this->rackid);

            // Return true if the update is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the update failed
    }

    // Delete a rack from the database
    public function delete()
    {
        // Ensure rackid exists and user session is valid
        if ($this->rackid && $_SESSION['userid']) {
            // Prepare SQL query to delete a rack
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->rackTable . ' 
                WHERE rackid = ?');

            // Sanitize the rackid
            $this->rackid = htmlspecialchars(strip_tags($this->rackid));

            // Bind parameters and execute the statement
            $stmt->bind_param('i', $this->rackid);

            // Return true if the deletion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the deletion failed
    }

    // Retrieve details of a specific rack
    public function getRackDetails()
    {
        // Ensure rackid and user session exist
        if ($this->rackid && $_SESSION['userid']) {
            // Prepare SQL query to get details of a specific rack by rackid
            $sqlQuery = 'SELECT rackid, name, status 
                         FROM ' . $this->rackTable . ' 
                         WHERE rackid = ?';

            // Prepare and bind parameters
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->rackid);
            $stmt->execute();
            $result = $stmt->get_result();

            // Process the result and return it in JSON format
            $records = array();
            while ($rack = $result->fetch_assoc()) {
                $rows = array();
                $rows['rackid'] = $rack['rackid'];
                $rows['name'] = $rack['name'];
                $rows['status'] = $rack['status'];
                $records[] = $rows;
            }

            $output = array(
                'data' => $records
            );
            echo json_encode($output); // Output rack details in JSON format
        }
    }
}
?>
