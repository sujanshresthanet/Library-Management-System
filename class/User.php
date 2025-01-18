<?php

class User
{
    // Declare properties related to the user information
    public $user_id;
    public $role;
    public $email;
    public $first_name;
    public $last_name;
    public $password;
    private $userTable = 'user'; // Name of the table where user data is stored
    private $conn; // Database connection object

    // Constructor accepts a database connection and assigns it to the class
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Log in a user by verifying the email and password
    public function login()
    {
        // Check if email and password are provided
        if ($this->email && $this->password) {
            // Prepare SQL query to check the user credentials
            $sqlQuery = 'SELECT * FROM ' . $this->userTable . ' WHERE email = ? AND password = ?';
            $stmt = $this->conn->prepare($sqlQuery);
            $password = md5($this->password); // Hash the password for comparison
            $stmt->bind_param('ss', $this->email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if user exists and set session variables
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['userid'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['email'];
                return 1; // Successful login
            } else {
                return 0; // Invalid credentials
            }
        } else {
            return 0; // Missing credentials
        }
    }

    // Check if the user is logged in by checking the session
    public function loggedIn()
    {
        if (!empty($_SESSION['userid'])) {
            return 1; // User is logged in
        } else {
            return 0; // User is not logged in
        }
    }

    // Check if the logged-in user is an admin
    public function isAdmin()
    {
        if (!empty($_SESSION['userid']) && $_SESSION['role'] == 'admin') {
            return 1; // User is an admin
        } else {
            return 0; // User is not an admin
        }
    }

    // List all users with search, pagination, and ordering features
    public function listUsers()
    {
        // Initial SQL query to select user details
        $sqlQuery = 'SELECT id, first_name, last_name, email, password, role FROM ' . $this->userTable;

        // Check if a search term is provided and modify the query
        if (!empty($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (id LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR first_name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR email LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR password LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR role LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Order the results based on the column and direction from the request
        if (!empty($_POST['order'])) {
            $column = $_POST['order']['0']['column']; // Get the column index for sorting

            switch ($column) {
                case '1':
                    $column = 'first_name';
                    break;
                case '2':
                    $column = 'email';
                    break;
                case '3':
                    $column = 'role';
                    break;
            }
            // Apply ordering based on the column and direction
            $sqlQuery .= ' ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'];
        } else {
            // Default ordering by user ID in descending order
            $sqlQuery .= ' ORDER BY id DESC';
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
        while ($user = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count; // Row number
            $rows[] = ($user['first_name'] ? ucfirst($user['first_name']) : '') . ' ' . ($user['last_name'] ? ucfirst($user['last_name']) : ''); // Full name

            $rows[] = $user['email']; // User's email
            $rows[] = $user['role']; // User's role
            // Create action buttons for editing and deleting
            $rows[] = '<div class="d-flex gap-2">
                        <button type="button" name="update" id="' . $user['id'] . '" class="btn app-btn-primary update">
                            <span class="glyphicon glyphicon-edit" title="Edit">Edit</span>
                        </button>
                        <button type="button" name="delete" id="' . $user['id'] . '" class="btn app-btn-secondary delete">
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

    // Insert a new user into the database
    public function insert()
    {
        // Ensure role, email, password, and user session exist
        if ($this->role && $this->email && $this->password && $_SESSION['userid']) {
            // Prepare SQL query to insert a new user
            $stmt = $this->conn->prepare('
                INSERT INTO ' . $this->userTable . '(`first_name`, `last_name`, `email`, `password`, `role`)
                VALUES(?, ?, ?, ?, ?)');

            // Sanitize the inputs to avoid security issues
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));
            $this->password = md5($this->password); // Hash the password before storing it

            // Bind parameters and execute the statement
            $stmt->bind_param('sssss', $this->first_name, $this->last_name, $this->email, $this->password, $this->role);

            // Return true if the insertion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if insertion failed
    }

    // Update an existing user's details
    public function update()
    {
        // Ensure role, email, and user session exist
        if ($this->role && $this->email && $_SESSION['userid']) {
            $updatePass = '';
            // If password is provided, hash it and include in the update query
            if ($this->password) {
                $this->password = md5($this->password);
                $updatePass = ", password = '" . $this->password . "'";
            }

            // Prepare SQL query to update user details
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->userTable . " 
                SET first_name = ?, last_name = ?, email = ?, role = ? $updatePass
                WHERE id = ?");

            // Sanitize the inputs
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));

            // Bind parameters and execute the statement
            $stmt->bind_param('ssssi', $this->first_name, $this->last_name, $this->email, $this->role, $this->id);

            // Return true if the update is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the update failed
    }

    // Delete a user from the database
    public function delete()
    {
        // Ensure user id and session exist
        if ($this->id && $_SESSION['userid']) {
            // Prepare SQL query to delete a user
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->userTable . ' 
                WHERE id = ?');

            // Sanitize the user id
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind parameters and execute the statement
            $stmt->bind_param('i', $this->id);

            // Return true if the deletion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
        return false; // Return false if the deletion failed
    }

    // Retrieve details of a specific user by their user_id
    public function getUserDetails()
    {
        // Ensure user_id and session exist
        if ($this->user_id && $_SESSION['userid']) {
            // Prepare SQL query to get details of a specific user
            $sqlQuery = 'SELECT id, first_name, last_name, email, password, role 
                         FROM ' . $this->userTable . ' 
                         WHERE id = ?';

            // Prepare and bind parameters
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Process the result and return it in JSON format
            $records = array();
            while ($user = $result->fetch_assoc()) {
                $rows = array();
                $rows['id'] = $user['id'];
                $rows['first_name'] = $user['first_name'];
                $rows['last_name'] = $user['last_name'];
                $rows['email'] = $user['email'];
                $rows['role'] = $user['role'];
                $records[] = $rows;
            }

            $output = array(
                'data' => $records
            );
            echo json_encode($output); // Output user details in JSON format
        }
    }

    // Get the list of users with the role 'user'
    function getUsersList()
    {
        $stmt = $this->conn->prepare('
        SELECT id, first_name, last_name 
        FROM ' . $this->userTable . ' 
        WHERE role = "user"');
        $stmt->execute();
        $result = $stmt->get_result();
        return $result; // Return the result set of users with role 'user'
    }
}
?>
