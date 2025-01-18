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
$pageTitle = 'Manage Publisher';

?>
<?php include ('inc/header.php'); ?>
<div class="container-xl">
    <div class="row">
        <div class="col">
            <h2>Publisher List</h2>
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-10">
                        <h3 class="panel-title"></h3>
                    </div>
                    <div class="col-md-2" align="right">
                        <button id="addPublisher" type="button" class="btn app-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-publisher-modal" title="Add publisher">
                            <i class="fas fa-plus-circle"></i> Add Publisher
                        </button>
                    </div>
                </div>
            </div>
            <table id="publisherListing" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Sn.</th>
                        <th>Name</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="add-publisher-modal" tabindex="-1" aria-labelledby="add-publisher-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-publisher-modalLabel"><i class="fa fa-plus"></i> Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="publisherForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Publisher</label>
                            <input type="text" name="name" id="name" autocomplete="off" class="form-control"
                                placeholder="Publisher name" />
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select</option>
                                <option value="Enable">Enable</option>
                                <option value="Disable">Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="publisherid" id="publisherid" />
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
$jsFile = 'publisher.js';
include ('inc/footer.php');
?>