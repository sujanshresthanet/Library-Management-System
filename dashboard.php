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

$pageTitle = "Dashboard"; 
?>

<?php include ('inc/header.php'); ?>
<div class="container-xl">
    <h1 class="app-page-title">Dashboard</h1>
    <div class="app-card alert alert-dismissible shadow-sm mb-4 border-left-decoration" role="alert">
        <div class="inner">
            <div class="app-card-body p-3 p-lg-4">
                <h3 class="mb-3">Welcome to LibraryHub!</h3>
                <div class="row gx-5 gy-3">
                    <div class="col-12 col-lg-9">
                        <div>Portal is a free Bootstrap 5 admin dashboard template. The design is simple, clean and
                            modular so it's a great base for building any modern web app.</div>
                    </div>
                </div>
                <!--//row-->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!--//app-card-body-->
        </div>
        <!--//inner-->
    </div>
    <!--//app-card-->
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Total Books</h4>
                    <div class="stats-figure"> <a href="books.php"><?php echo $book->getTotalBooks(); ?></a></div>
                </div>
                <!--//app-card-body-->
            </div>
            <!--//app-card-->
        </div>
        <!--//col-->
        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Available Books</h4>
                    <div class="stats-figure"> <a
                            href="books.php"><?php echo ($book->getTotalBooks() - $book->getTotalIssuedBooks()); ?></a>
                    </div>
                </div>
                <!--//app-card-body-->
            </div>
            <!--//app-card-->
        </div>
        <!--//col-->
        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Returned Books</h4>
                    <div class="stats-figure"> <a href="issue_books.php"><?php echo $book->getTotalReturnedBooks(); ?></a>
                    </div>
                </div>
                <!--//app-card-body-->
            </div>
            <!--//app-card-->
        </div>
        <!--//col-->
        <div class="col-6 col-lg-3">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Issued Books</h4>
                    <div class="stats-figure"> <a href="issue_books.php"><?php echo $book->getTotalIssuedBooks(); ?></a></div>
                </div>
                <!--//app-card-body-->
            </div>
            <!--//app-card-->
        </div>
        <!--//col-->
    </div>
    <!--//row-->
</div>
<!--//container-fluid-->
<?php include ('inc/footer.php'); ?>