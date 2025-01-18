<?php

// Define the Category class for managing categories in the system
class Category
{
    // Public properties
    public $name;
    public $status;
    public $categoryid;
    
    // Private table name and database connection
    private $categoryTable = 'category';
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to list all categories with filtering, sorting, and pagination support
    public function listCategory()
    {
        // Base SQL query to fetch category details
        $sqlQuery = 'SELECT categoryid, name, status
            FROM ' . $this->categoryTable . ' ';

        // Apply filtering if search term is provided
        if (isset($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (categoryid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Apply sorting if order parameters are provided
        if (isset($_POST['order'])) {
            $column = $_POST['order']['0']['column'];

            // Map columns for sorting
            switch ($column) {
                case '1':
                    $column = 'name';
                    break;
                case '2':
                    $column = 'status';
                    break;
            }

            // Add sorting order to the query
            $sqlQuery .= 'ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'] . ' ';
        } else {
            $sqlQuery .= 'ORDER BY categoryid DESC '; // Default order by categoryid descending
        }

        // Create a copy of the query for total record count
        $sqlQueryAll = $sqlQuery;

        // Apply pagination if length is specified
        if ($_POST['length'] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Prepare and execute the query to fetch categories with filters, sorting, and pagination
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Execute query to fetch total records without pagination
        $stmtTotal = $this->conn->prepare($sqlQueryAll);
        $stmtTotal->execute();
        $allResult = $stmtTotal->get_result();
        $allRecords = $allResult->num_rows;

        // Get the number of records in the current page
        $displayRecords = $result->num_rows;
        $records = array();
        $count = 1;

        // Loop through the results and format the data for display
        while ($category = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count;
            $rows[] = ucfirst($category['name']);
            $rows[] = $category['status'];
            // Add Edit and Delete buttons with appropriate category ID
            $rows[] = '<div class="d-flex gap-2"><button type="button" name="update" id="' . $category['categoryid'] . '" class="btn app-btn-primary update"><span class="glyphicon glyphicon-edit" title="Edit">Edit</span></button><button type="button" name="delete" id="' . $category['categoryid'] . '" class="btn app-btn-secondary delete" ><span class="glyphicon glyphicon-remove" title="Delete">Delete</span></button></div>';
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

    // Method to insert a new category into the database
    public function insert()
    {
        // Check if name and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query for insertion
            $stmt = $this->conn->prepare('
                INSERT INTO ' . $this->categoryTable . '(`name`, `status`)
                VALUES(?, ?)');

            // Sanitize input data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));

            // Bind parameters and execute query
            $stmt->bind_param('ss', $this->name, $this->status);

            // Return true if insertion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
    }

    // Method to update an existing category
    public function update()
    {
        // Check if name, categoryid, and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query for updating category details
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->categoryTable . ' 
                SET name = ?, status = ?
                WHERE categoryid = ?');

            // Sanitize input data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->categoryid = htmlspecialchars(strip_tags($this->categoryid));

            // Bind parameters and execute query
            $stmt->bind_param('ssi', $this->name, $this->status, $this->categoryid);

            // Return true if update is successful
            if ($stmt->execute()) {
                return true;
            }
        }
    }

    // Method to delete a category
    public function delete()
    {
        // Check if categoryid and user session are valid
        if ($this->categoryid && $_SESSION['userid']) {
            // Prepare SQL query for deletion
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->categoryTable . ' 
                WHERE categoryid = ?');

            // Sanitize categoryid
            $this->categoryid = htmlspecialchars(strip_tags($this->categoryid));

            // Bind parameters and execute query
            $stmt->bind_param('i', $this->categoryid);

            // Return true if deletion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
    }

    // Method to get details of a specific category by categoryid
    public function getCategoryDetails()
    {
        // Check if categoryid and user session are valid
        if ($this->categoryid && $_SESSION['userid']) {
            // Prepare SQL query to fetch category details by categoryid
            $sqlQuery = '
                SELECT categoryid, name, status
                FROM ' . $this->categoryTable . "\t\t\t
                WHERE categoryid = ? ";

            // Prepare and execute the query
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->categoryid);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = array();

            // Loop through and return the category details
            while ($category = $result->fetch_assoc()) {
                $rows = array();
                $rows['categoryid'] = $category['categoryid'];
                $rows['name'] = $category['name'];
                $rows['status'] = $category['status'];
                $records[] = $rows;
            }

            // Return the details as JSON
            $output = array(
                'data' => $records
            );
            echo json_encode($output);
        }
    }
}
?>
