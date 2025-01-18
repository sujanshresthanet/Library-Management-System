<?php

class IssueBooks
{
    // Public properties to store book, user, and issue information
    public $bookid;
    public $book;
    public $users;
    public $expected_return_date;
    public $return_date;
    public $status;
    public $issuebookid;
    
    // Private variables to store table names and database connection
    private $issuedBookTable = 'issued_book';
    private $bookTable = 'book';
    private $userTable = 'user';
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to list all issued books with search, sorting, and pagination functionality
    public function listIssuedBook()
    {
        // SQL query to fetch issued book details with joins to book and user tables
        $sqlQuery = 'SELECT issue_book.issuebookid, issue_book.issue_date_time, issue_book.expected_return_date, issue_book.return_date_time, issue_book.status, book.name As book_name, book.isbn, user.first_name, user.last_name 
            FROM ' . $this->issuedBookTable . " issue_book
            LEFT JOIN " . $this->bookTable . ' book ON book.bookid = issue_book.bookid
            LEFT JOIN ' . $this->userTable . ' user ON user.id = issue_book.userid ';

        // Add search filter if search term is provided
        if (!empty($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (issue_book.issuebookid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR book.name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR book.isbn LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR user.first_name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR user.last_name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR issue_book.issue_date_time LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR issue_book.status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Apply sorting if order parameters are provided
        if (!empty($_POST['order'])) {
            $column = $_POST['order']['0']['column'];

            // Mapping columns for sorting
            switch ($column) {
                case '0':
                    $column = 'name';
                    break;
                case '1':
                    $column = 'isbn';
                    break;
                case '2':
                    $column = 'first_name';
                    break;
                case '3':
                    $column = 'issue_date_time';
                    break;
                case '4':
                    $column = 'expected_return_date';
                    break;
                case '5':
                    $column = 'return_date_time';
                    break;
                case '6':
                    $column = 'status';
                    break;
            }
            // Add sorting order to the query
            $sqlQuery .= 'ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'] . ' ';
        } else {
            // Default order by issuebookid in descending order
            $sqlQuery .= 'ORDER BY issue_book.issuebookid DESC ';
        }

        // Store original query for total record count (without pagination)
        $sqlQueryAll = $sqlQuery;

        // Apply pagination if length is provided
        if ($_POST['length'] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Execute the query to fetch the records with pagination and sorting
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Execute query for total records without pagination
        $stmtTotal = $this->conn->prepare($sqlQueryAll);
        $stmtTotal->execute();
        $allResult = $stmtTotal->get_result();
        $allRecords = $allResult->num_rows;

        // Get the number of records in the current page
        $displayRecords = $result->num_rows;
        $records = array();
        $count = 1;

        // Loop through the results and format them for display
        while ($issueBook = $result->fetch_assoc()) {
            $rows = array();
            $rows[] = $count;
            $rows[] = isset($issueBook['book_name']) ? ucfirst($issueBook['book_name']) : '';
            $rows[] = isset($issueBook['isbn']) ? ucfirst($issueBook['isbn']) : '';
            $rows[] = (isset($issueBook['first_name']) ? ucfirst($issueBook['first_name']) : '') . ' ' . (isset($issueBook['last_name']) ? ucfirst($issueBook['last_name']) : '');
            $rows[] = isset($issueBook['issue_date_time']) ? ucfirst($issueBook['issue_date_time']) : '';
            $rows[] = isset($issueBook['expected_return_date']) ? ucfirst($issueBook['expected_return_date']) : '';
            $rows[] = isset($issueBook['return_date_time']) ? ucfirst($issueBook['return_date_time']) : '';
            $rows[] = isset($issueBook['status']) ? $issueBook['status'] : '';

            // Add action buttons (Edit, Delete) for each record
            $rows[] = '<div class="d-flex gap-2"><button type="button" name="update" id="' . $issueBook['issuebookid'] . '" class="btn app-btn-primary update"><span class="glyphicon glyphicon-edit" title="Edit">Edit</span></button><button type="button" name="delete" id="' . $issueBook['issuebookid'] . '" class="btn app-btn-secondary delete" ><span class="glyphicon glyphicon-remove" title="Delete">Delete</span></button></div>';
            $records[] = $rows;
            $count++;
        }

        // Return the data as a JSON object
        $output = array(
            'draw' => intval($_POST['draw']),
            'iTotalRecords' => $displayRecords,
            'iTotalDisplayRecords' => $allRecords,
            'data' => $records
        );

        echo json_encode($output);
    }

    // Method to insert a new issued book record into the database
    public function insert()
    {
        if ($this->book && $_SESSION['userid']) {
            $stmt = $this->conn->prepare('
                INSERT INTO ' . $this->issuedBookTable . '(`bookid`, `userid`, `expected_return_date`, `return_date_time`, `status`)
                VALUES(?, ?, ?, ?, ?)');

            // Sanitize and prepare input data
            $this->book = htmlspecialchars(strip_tags($this->book));
            $this->users = htmlspecialchars(strip_tags($this->users));
            $this->expected_return_date = htmlspecialchars(strip_tags($this->expected_return_date));
            $this->return_date = htmlspecialchars(strip_tags($this->return_date));
            $this->status = htmlspecialchars(strip_tags($this->status));

            // Set NULL if the dates are empty
            if (!$this->expected_return_date) {
                $this->expected_return_date = NULL;
            }
            if (!$this->return_date) {
                $this->return_date = NULL;
            }

            // Bind parameters and execute the query
            $stmt->bind_param('iisss', $this->book, $this->users, $this->expected_return_date, $this->return_date, $this->status);

            // Return true if the insertion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
    }

    // Method to update an existing issued book record
    public function update()
    {
        if ($this->issuebookid && $this->book && $_SESSION['userid']) {
            // Prepare the SQL query to update the issued book
            $stmt = $this->conn->prepare("
                UPDATE " . $this->issuedBookTable . " 
                SET bookid = ?, userid = ?, expected_return_date = ?, return_date_time = ?, status = ?
                WHERE issuebookid = ?
            ");

            // Sanitize and prepare input data
            $this->book = htmlspecialchars(strip_tags($this->book));
            $this->users = htmlspecialchars(strip_tags($this->users));

            // Handle dates (set NULL if not provided)
            $this->expected_return_date = $this->expected_return_date ? htmlspecialchars(strip_tags($this->expected_return_date)) : NULL;
            $this->return_date = $this->return_date ? htmlspecialchars(strip_tags($this->return_date)) : NULL;
            $this->status = htmlspecialchars(strip_tags($this->status));

            // Bind parameters and execute the query
            $stmt->bind_param('iisssi', $this->book, $this->users, $this->expected_return_date, $this->return_date, $this->status, $this->issuebookid);

            // Return true if the update is successful
            if ($stmt->execute()) {
                return true;
            } else {
                // Output error if execution fails
                echo 'Error executing statement: ' . $stmt->error;
                return false;
            }
        }
        return false;  // Return false if the update didn't succeed
    }

    // Method to delete an issued book record
    public function delete()
    {
        if ($this->issuebookid && $_SESSION['userid']) {
            // Prepare the SQL query to delete the issued book
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->issuedBookTable . ' 
                WHERE issuebookid = ?');

            // Sanitize and bind the issuebookid
            $this->issuebookid = htmlspecialchars(strip_tags($this->issuebookid));

            // Bind parameters and execute the query
            $stmt->bind_param('i', $this->issuebookid);

            // Return true if the deletion is successful
            if ($stmt->execute()) {
                return true;
            }
        }
    }

    // Method to get the details of a specific issued book
    public function getIssueBookDetails()
    {
        if ($this->issuebookid && $_SESSION['userid']) {
            // SQL query to fetch the details of a specific issued book
            $sqlQuery = 'SELECT issue_book.issuebookid, issue_book.issue_date_time, issue_book.expected_return_date, issue_book.return_date_time, issue_book.status, issue_book.bookid, issue_book.userid, book.name AS book_name
            FROM ' . $this->issuedBookTable . " issue_book
            LEFT JOIN " . $this->bookTable . ' book ON book.bookid = issue_book.bookid
            LEFT JOIN ' . $this->userTable . ' user ON user.id = issue_book.userid
            WHERE issue_book.issuebookid = ?';

            // Prepare and execute the query
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->issuebookid);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = array();

            // Fetch and format the record
            while ($issueBook = $result->fetch_assoc()) {
                $rows = array();
                $rows['issuebookid'] = $issueBook['issuebookid'];
                $rows['bookid'] = $issueBook['bookid'];
                $rows['book_name'] = $issueBook['book_name'];
                $rows['status'] = $issueBook['status'];
                $rows['userid'] = $issueBook['userid'];
                $rows['expected_return_date'] = $issueBook['expected_return_date'] ? date('Y-m-d\TH:i:s', strtotime($issueBook['expected_return_date'])) : '';
                $rows['return_date_time'] = $issueBook['return_date_time'] ? date('Y-m-d\TH:i:s', strtotime($issueBook['return_date_time'])) : '';
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
