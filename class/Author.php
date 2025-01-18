<?php

// Define the Author class to handle author-related actions
class Author
{
    // Public properties
    public $name;
    public $status;
    public $authorid;
    
    // Private property for table name and database connection
    private $authorTable = 'author';
    private $conn;

    // Constructor to initialize the class with the database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to list authors with support for filtering, ordering, and pagination
    public function listAuthor()
    {
        // Start the base SQL query to select author details
        $sqlQuery = 'SELECT authorid, name, status
            FROM ' . $this->authorTable . ' ';

        // If search term is provided, add a WHERE clause to filter results
        if (!empty($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (authorid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // If order parameters are provided, apply ordering to the query
        if (!empty($_POST['order'])) {
            $sqlQuery .= 'ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
        } else {
            // Default ordering by authorid in descending order
            $sqlQuery .= 'ORDER BY authorid DESC ';
        }

        // Create a copy of the query for getting total count of authors
        $sqlQueryAll = $sqlQuery;

        // Apply pagination if length is specified
        if ($_POST['length'] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Prepare and execute the query to get paginated author data
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Execute query to get the total number of authors (without pagination)
        $stmtTotal = $this->conn->prepare($sqlQueryAll);
        $stmtTotal->execute();
        $allResult = $stmtTotal->get_result();
        $allRecords = $allResult->num_rows;

        // Get the number of records retrieved in the current page
        $displayRecords = $result->num_rows;
        $records = array();
        $count = 1;

        // Loop through the authors and format the data for display
        while ($author = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count; // Add a serial number
            $rows[] = ucfirst($author['name']); // Capitalize the first letter of the name
            $rows[] = $author['status']; // Add status
            // Add Edit and Delete buttons with appropriate IDs
            $rows[] = '<div class="d-flex gap-2"><button type="button" name="update" id="' . $author['authorid'] . '" class="btn app-btn-primary update"><span class="glyphicon glyphicon-edit" title="Edit">Edit</span></button><button type="button" name="delete" id="' . $author['authorid'] . '" class="btn app-btn-secondary delete" ><span class="glyphicon glyphicon-remove" title="Delete">Delete</span></button></div>';
            $records[] = $rows;
            $count++;
        }

        // Return the formatted data as a JSON object
        $output = array(
            'draw' => intval($_POST['draw']),
            'iTotalRecords' => $displayRecords,
            'iTotalDisplayRecords' => $allRecords,
            'data' => $records
        );

        echo json_encode($output);
    }

    // Method to insert a new author into the database
    public function insert()
    {
        // Check if name and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare the SQL query for insertion
            $stmt = $this->conn->prepare('
                INSERT INTO ' . $this->authorTable . '(`name`, `status`)
                VALUES(?, ?)');

            // Sanitize input data to prevent XSS or SQL injection
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));

            // Bind parameters and execute the query
            $stmt->bind_param('ss', $this->name, $this->status);

            if ($stmt->execute()) {
                return true; // Return true if insertion is successful
            }
        }
    }

    // Method to update an existing author's details
    public function update()
    {
        // Check if name and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare the SQL query for updating author data
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->authorTable . ' 
                SET name = ?, status = ?
                WHERE authorid = ?');

            // Sanitize input data to prevent XSS or SQL injection
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->authorid = htmlspecialchars(strip_tags($this->authorid));

            // Bind parameters and execute the query
            $stmt->bind_param('ssi', $this->name, $this->status, $this->authorid);

            if ($stmt->execute()) {
                return true; // Return true if update is successful
            }
        }
    }

    // Method to delete an author from the database
    public function delete()
    {
        // Check if authorid and user session are valid
        if ($this->authorid && $_SESSION['userid']) {
            // Prepare the SQL query for deleting an author
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->authorTable . ' 
                WHERE authorid = ?');

            // Sanitize authorid to prevent SQL injection
            $this->authorid = htmlspecialchars(strip_tags($this->authorid));

            // Bind parameters and execute the query
            $stmt->bind_param('i', $this->authorid);

            if ($stmt->execute()) {
                return true; // Return true if deletion is successful
            }
        }
    }

    // Method to get details of a specific author
    public function getAuthorDetails()
    {
        // Check if authorid and user session are valid
        if ($this->authorid && $_SESSION['userid']) {
            // Prepare the SQL query to fetch a specific author's details
            $sqlQuery = '
                SELECT authorid, name, status
                FROM ' . $this->authorTable . '
                WHERE authorid = ?';

            // Prepare and bind the query parameters
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->authorid);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = array();

            // Loop through the results and format the data
            while ($author = $result->fetch_assoc()) {
                $rows = array();
                $rows['authorid'] = $author['authorid'];
                $rows['name'] = $author['name'];
                $rows['status'] = $author['status'];
                $records[] = $rows;
            }

            // Return the formatted data as a JSON object
            $output = array(
                'data' => $records
            );
            echo json_encode($output);
        }
    }
}
?>
