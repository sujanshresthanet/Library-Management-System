<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Books.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$book = new Books($db);

if (!$user->loggedIn()) {
header('Location: index.php');
}
$pageTitle = "Issue Books"; 

?>

<?php include('inc/header.php'); ?>
<div class="container-xl">
    <div class="row">
        <div class="col">
            <h2>Manage Issue Books</h2>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="panel-title"></h3>
                    </div>
                    <div class="col-md-2" align="right">
                        <button id="issueBook" type="button" class="btn app-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-issued-book-modal" title="Add issued book">
                            <i class="fas fa-plus-circle"></i> Issue Book
                        </button>
                    </div>
                </div>
            </div>
            <table id="issuedBookListing" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Book</th>
                        <th>ISBN</th>
                        <th>User</th>
                        <th>Issue Date</th>
                        <th>Expected Return</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="add-issued-book-modal" tabindex="-1" aria-labelledby="add-issued-book-modal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-issued-book-modal"><i class="fa fa-plus"></i> Edit issued book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="issuedBookForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="book" class="form-label">Available Books</label>
                            <select name="book" id="book" class="form-select" required>
                                <option value="">Select</option>
                                <?php 
$bookResult = $book->getBookList();
while ($book = $bookResult->fetch_assoc()) {                    
?>
                                <option value="<?php echo $book['bookid']; ?>"><?php echo $book['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="users" class="form-label">User</label>
                            <select name="users" id="users" class="form-select" required>
                                <option value="">Select</option>
                                <?php 
$usersResult = $user->getUsersList();
while ($user = $usersResult->fetch_assoc()) {   
?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo ucfirst($user['first_name'])." ".ucfirst($user['last_name']); ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="expected_return_date" class="form-label">Expected Return Date</label>
                            <input type="datetime-local" step="1" name="expected_return_date" id="expected_return_date"
                                autocomplete="off" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="return_date" class="form-label">Return Date</label>
                            <input type="datetime-local" step="1" name="return_date" id="return_date" autocomplete="off"
                                class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select</option>
                                <option value="Issued">Issued</option>
                                <option value="Returned">Returned</option>
                                <option value="Not Return">Not Return</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="issuebookid" id="issuebookid" />
                        <input type="hidden" name="action" id="action" value="" />
                        <input type="submit" name="save" id="save" class="btn app-btn-primary" value="Save" />
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php 
$jsFile = "issue_books.js"; 
include('inc/footer.php'); 
?>