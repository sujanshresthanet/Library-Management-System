<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Books.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if (!$user->loggedIn()) {
    header('Location: index.php');
}

$book = new Books($db);
$pageTitle = 'Manage Books';

?>

<?php include ('inc/header.php'); ?>

<div class="container-xl">
    <div class="row">
        <div class="col">
            <h2>Manage Books</h2>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="panel-title"></h3>
                    </div>
                    <div class="col-md-2 text-end">
                        <!-- Button to open modal -->
                        <button id="addBook" type="button" class="btn app-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-book-modal" title="Add book">
                            <i class="fas fa-plus-circle"></i> Add Book
                        </button>
                    </div>
                </div>
            </div>
            <table id="bookListing" class="table table-striped table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <!-- <td></td> -->
                        <th scope="col">Book</th>
                        <th scope="col">ISBN</th>
                        <th scope="col">Author</th>
                        <th scope="col">Publisher</th>
                        <th scope="col">Category</th>
                        <th scope="col">Rack</th>
                        <th scope="col">No of Copies</th>
                        <th scope="col">Status</th>
                        <th scope="col">Updated On</th>
                        <td class="text-center"></td>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table body content will go here -->
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="add-book-modal" tabindex="-1" aria-labelledby="add-book-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-book-modalLabel"><i class="fa fa-plus"></i> Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="bookForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Book Name</label>
                            <input type="text" name="name" id="name" autocomplete="off" class="form-control"
                                placeholder="Enter book name" required />
                        </div>
                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN No</label>
                            <input type="text" name="isbn" id="isbn" autocomplete="off" class="form-control"
                                placeholder="Enter ISBN" required />
                        </div>
                        <div class="mb-3">
                            <label for="no_of_copy" class="form-label">No of Copies</label>
                            <input type="number" name="no_of_copy" id="no_of_copy" autocomplete="off"
                                class="form-control" placeholder="Enter number of copies" required />
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <select name="author" id="author" class="form-select" required>
                                <option value="">Select Author</option>
                                <?php
                                $authorResult = $book->getAuthorList();
                                while ($author = $authorResult->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $author['authorid']; ?>"><?php echo $author['name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <select name="publisher" id="publisher" class="form-select" required>
                                <option value="">Select Publisher</option>
                                <?php
                                $publisherResult = $book->getPublisherList();
                                while ($publisher = $publisherResult->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $publisher['publisherid']; ?>">
                                    <?php echo $publisher['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php
                                $categoryResult = $book->getCategoryList();
                                while ($category = $categoryResult->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $category['categoryid']; ?>"><?php echo $category['name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rack" class="form-label">Rack</label>
                            <select name="rack" id="rack" class="form-select" required>
                                <option value="">Select Rack</option>
                                <?php
                                $rackResult = $book->getRackList();
                                while ($rack = $rackResult->fetch_assoc()) {
                                    ?>
                                <option value="<?php echo $rack['rackid']; ?>"><?php echo $rack['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Enable">Enable</option>
                                <option value="Disable">Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="bookid" id="bookid" />
                        <input type="hidden" name="action" id="action" value="" />
                        <button type="submit" name="save" id="save" class="btn app-btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$jsFile = 'books.js';
include ('inc/footer.php');
?>