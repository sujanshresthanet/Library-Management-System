<?php

// Define the Books class to manage books and their related operations
class Books
{
    // Public properties
    public $bookid;
    public $name;
    public $isbn;
    public $no_of_copy;
    public $author;
    public $publisher;
    public $category;
    public $rack;
    public $status;
    public $picture;

    // Private table names and database connection
    private $bookTable = 'book';
    private $issuedBookTable = 'issued_book';
    private $categoryTable = 'category';
    private $authorTable = 'author';
    private $publisherTable = 'publisher';
    private $rackTable = 'rack';
    private $userTable = 'user';
    private $conn;

    // Constructor to initialize the database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Method to list all books with filtering, sorting, and pagination support
    public function listBook()
    {
        // Base SQL query to fetch book details with related author, category, publisher, and rack information
        $sqlQuery = 'SELECT book.bookid, book.picture, book.name, book.status, book.isbn, book.no_of_copy, book.updated_on, author.name as author_name, category.name AS category_name, rack.name As rack_name, publisher.name AS publisher_name 
            FROM ' . $this->bookTable . " book\t\t    
            LEFT JOIN " . $this->authorTable . ' author ON author.authorid = book.authorid
            LEFT JOIN ' . $this->categoryTable . ' category ON category.categoryid = book.categoryid
            LEFT JOIN ' . $this->rackTable . ' rack ON rack.rackid = book.rackid
            LEFT JOIN ' . $this->publisherTable . ' publisher ON publisher.publisherid = book.publisherid ';

        // Apply filtering if search term is provided
        if (isset($_POST['search']['value'])) {
            $sqlQuery .= ' WHERE (book.bookid LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR book.name LIKE "%' . $_POST['search']['value'] . '%" ';
            $sqlQuery .= ' OR book.status LIKE "%' . $_POST['search']['value'] . '%") ';
        }

        // Apply sorting if order parameters are provided
        if (isset($_POST['order']['0']['column'])) {
            $column = $_POST['order']['0']['column'];

            // Map columns for sorting
            switch ($column) {
                case '0': $column = 'name'; break;
                case '1': $column = 'isbn'; break;
                case '2': $column = 'author_name'; break;
                case '3': $column = 'publisher_name'; break;
                case '4': $column = 'category_name'; break;
                case '5': $column = 'rack_name'; break;
                case '6': $column = 'no_of_copy'; break;
                case '7': $column = 'status'; break;
                case '8': $column = 'updated_on'; break;
            }

            $sqlQuery .= 'ORDER BY ' . $column . ' ' . $_POST['order']['0']['dir'] . ' ';
        } else {
            $sqlQuery .= 'ORDER BY book.bookid DESC '; // Default order by bookid in descending order
        }

        // Create a copy of the query for total record count
        $sqlQueryAll = $sqlQuery;

        // Apply pagination if length is specified
        if ($_POST['length'] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['length'] . ' OFFSET ' . $_POST['start'];
        }

        // Prepare and execute the query to fetch books with applied filters, sorting, and pagination
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
        while ($book = $result->fetch_assoc()) {
            $rows = array();
            // Set a default picture if none exists
            if (!$book['picture']) {
                $book['picture'] = 'default.jpg';
            }
            // Format the book details for display
            $rows[] = isset($book['name']) ? ucfirst($book['name']) : '';
            $rows[] = isset($book['isbn']) ? ucfirst($book['isbn']) : '';
            $rows[] = isset($book['author_name']) ? ucfirst($book['author_name']) : '';
            $rows[] = isset($book['publisher_name']) ? ucfirst($book['publisher_name']) : '';
            $rows[] = isset($book['category_name']) ? ucfirst($book['category_name']) : '';
            $rows[] = isset($book['rack_name']) ? ucfirst($book['rack_name']) : '';
            $rows[] = isset($book['no_of_copy']) ? ucfirst($book['no_of_copy']) : '';
            $rows[] = $book['status'];
            $rows[] = $book['updated_on'];
            // Add Edit and Delete buttons with appropriate IDs
            $rows[] = '<div class="d-flex gap-2"><button type="button" name="update" id="' . $book['bookid'] . '" class="btn app-btn-primary update"><span class="glyphicon glyphicon-edit" title="Edit">Edit</span></button>
                <button type="button" name="delete" id="' . $book['bookid'] . '" class="btn app-btn-secondary delete" ><span class="glyphicon glyphicon-remove" title="Delete">Delete</span></button></div>';
            $records[] = $rows;
            $count++;
        }

        // Return the formatted data as a JSON object
        $output = array(
            'draw' => intval($_POST['draw']),
            'iTotalRecords' => $displayRecords,
            'iTotalDisplayRecords' => $allRecords,
            'data' => $records,
            'sqlQuery' => $sqlQuery, // Debugging SQL query
        );

        echo json_encode($output);
    }

    // Method to insert a new book into the database
    public function insert()
    {
        // Check if name and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query for insertion
            $stmt = $this->conn->prepare('
                INSERT INTO ' . $this->bookTable . '(`name`, `status`, `isbn`, `no_of_copy`, `categoryid`, `authorid`, `rackid`, `publisherid`)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?)');

            // Sanitize input data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->isbn = htmlspecialchars(strip_tags($this->isbn));
            $this->no_of_copy = htmlspecialchars(strip_tags($this->no_of_copy));
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->publisher = htmlspecialchars(strip_tags($this->publisher));
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->rack = htmlspecialchars(strip_tags($this->rack));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->picture = ''; // Default empty picture

            // Bind parameters and execute query
            $stmt->bind_param('sssiiiii', $this->name, $this->status, $this->isbn, $this->no_of_copy, $this->category, $this->author, $this->rack, $this->publisher);

            if ($stmt->execute()) {
                return true; // Return true if insertion is successful
            }
        }
    }

    // Method to update an existing book
    public function update()
    {
        // Check if name and user session are valid
        if ($this->name && $_SESSION['userid']) {
            // Prepare SQL query for updating book details
            $stmt = $this->conn->prepare('
                UPDATE ' . $this->bookTable . ' 
                SET name = ?, status = ?, isbn = ?, no_of_copy = ?, categoryid = ?, authorid = ?, rackid = ?, publisherid = ?
                WHERE bookid = ?');

            // Sanitize input data
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->isbn = htmlspecialchars(strip_tags($this->isbn));
            $this->no_of_copy = htmlspecialchars(strip_tags($this->no_of_copy));
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->publisher = htmlspecialchars(strip_tags($this->publisher));
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->rack = htmlspecialchars(strip_tags($this->rack));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->bookid = htmlspecialchars(strip_tags($this->bookid));

            // Bind parameters and execute query
            $stmt->bind_param('sssiiiiii', $this->name, $this->status, $this->isbn, $this->no_of_copy, $this->category, $this->author, $this->rack, $this->publisher, $this->bookid);

            if ($stmt->execute()) {
                return true; // Return true if update is successful
            }
        }
    }

    // Method to delete a book
    public function delete()
    {
        // Check if bookid and user session are valid
        if ($this->bookid && $_SESSION['userid']) {
            // Prepare SQL query for deletion
            $stmt = $this->conn->prepare('
                DELETE FROM ' . $this->bookTable . ' 
                WHERE bookid = ?');

            // Sanitize bookid
            $this->bookid = htmlspecialchars(strip_tags($this->bookid));

            // Bind parameters and execute query
            $stmt->bind_param('i', $this->bookid);

            if ($stmt->execute()) {
                return true; // Return true if deletion is successful
            }
        }
    }

    // Method to get details of a specific book
    public function getBookDetails()
    {
        // Check if bookid and user session are valid
        if ($this->bookid && $_SESSION['userid']) {
            // Prepare SQL query to fetch book details by bookid
            $sqlQuery = 'SELECT book.bookid, book.picture, book.name, book.status, book.isbn, book.no_of_copy, book.updated_on, author.authorid, category.categoryid, rack.rackid, publisher.publisherid 
            FROM ' . $this->bookTable . " book\t\t    
            LEFT JOIN " . $this->authorTable . ' author ON author.authorid = book.authorid
            LEFT JOIN ' . $this->categoryTable . ' category ON category.categoryid = book.categoryid
            LEFT JOIN ' . $this->rackTable . ' rack ON rack.rackid = book.rackid
            LEFT JOIN ' . $this->publisherTable . ' publisher ON publisher.publisherid = book.publisherid 
            WHERE bookid = ? ';

            // Prepare and execute the query
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param('i', $this->bookid);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = array();
            
            // Loop through and return the book details
            while ($book = $result->fetch_assoc()) {
                $rows = array();
                $rows['bookid'] = $book['bookid'];
                $rows['name'] = $book['name'];
                $rows['status'] = $book['status'];
                $rows['isbn'] = $book['isbn'];
                $rows['no_of_copy'] = $book['no_of_copy'];
                $rows['categoryid'] = $book['categoryid'];
                $rows['rackid'] = $book['rackid'];
                $rows['publisherid'] = $book['publisherid'];
                $rows['authorid'] = $book['authorid'];
                $records[] = $rows;
            }

            // Return the details as JSON
            $output = array(
                'data' => $records
            );
            echo json_encode($output);
        }
    }

    // Method to get the list of authors
    function getAuthorList()
    {
        $stmt = $this->conn->prepare('
        SELECT authorid, name 
        FROM ' . $this->authorTable);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get the list of categories
    function getCategoryList()
    {
        $stmt = $this->conn->prepare('
        SELECT categoryid, name 
        FROM ' . $this->categoryTable);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get the list of publishers
    function getPublisherList()
    {
        $stmt = $this->conn->prepare('
        SELECT publisherid, name 
        FROM ' . $this->publisherTable);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get the list of racks
    function getRackList()
    {
        $stmt = $this->conn->prepare('
        SELECT rackid, name 
        FROM ' . $this->rackTable);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get the list of books
    function getBookList()
    {
        $stmt = $this->conn->prepare('
        SELECT book.bookid, book.name, issue_book.status
        FROM ' . $this->bookTable . ' book
        LEFT JOIN ' . $this->issuedBookTable . ' issue_book ON issue_book.bookid = book.bookid');
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Method to get the total number of books
    function getTotalBooks()
    {
        $stmt = $this->conn->prepare('
        SELECT *
        FROM ' . $this->bookTable);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }

    // Method to get the total number of issued books
    function getTotalIssuedBooks()
    {
        $stmt = $this->conn->prepare('
        SELECT * 
        FROM ' . $this->issuedBookTable . " 
        WHERE status = 'Issued'");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }

    // Method to get the total number of returned books
    function getTotalReturnedBooks()
    {
        $stmt = $this->conn->prepare('
        SELECT * 
        FROM ' . $this->issuedBookTable . " 
        WHERE status = 'Returned'");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows;
    }
}
?>
