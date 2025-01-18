<?php 
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if($user->loggedIn()) {	
	header("Location: dashboard.php");	
}

$loginMessage = '';
if(!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {	
	$user->email = $_POST["email"];
	$user->password = $_POST["password"];	
	if($user->login()) {
		header("Location: dashboard.php");	
	} else {
		$loginMessage = 'Invalid login! Please try again.';
	}
} else {
	$loginMessage = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>LibraryHub</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Portal - Bootstrap 5 Admin Dashboard Template For Developers">
    <meta name="author" content="Xiaoying Riley at 3rd Wave Media">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- FontAwesome JS-->
    <script defer src="assets/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->
    <link id="theme-style" rel="stylesheet" href="assets/css/portal.css">

</head>

<body class="app app-login p-0">
    <div class="row g-0 app-auth-wrapper">
        <div class="col-12 auth-main-col text-center p-5">
            <div class="d-flex flex-column align-content-end">
                <div class="app-auth-body mx-auto">
                    <div class="app-auth-branding mb-4"><a class="app-logo" href="index.php"><img class="logo-icon me-2"
                                src="assets/images/app-logo.svg" alt="logo"></a></div>
                    <h2 class="auth-heading text-center mb-5">Log in to LibraryHub</h2>
                    <div class="auth-form-container text-start">



                        <?php if ($loginMessage != '') { ?>
                        <div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $loginMessage; ?></div>
                        <?php } ?>
                        <form id="loginform" class="form-horizontal auth-form login-form" role="form" method="POST"
                            action="">
                            <div class="email mb-3">
                                <label class="sr-only" for="signin-email">Email</label>
                                <input id="signin-email" name="email" type="email"
                                    value="<?php if(!empty($_POST["email"])) { echo $_POST["email"]; } ?>"
                                    class="form-control signin-email" placeholder="Email address" required="required">
                            </div>
                            <!--//form-group-->
                            <div class="password mb-3">
                                <label class="sr-only" for="signin-password">Password</label>
                                <input id="signin-password" name="password" type="password"
                                    value="<?php if(!empty($_POST["password"])) { echo $_POST["password"]; } ?>"
                                    class="form-control signin-password" placeholder="Password" required="required">
                                <div class="extra mt-3 row justify-content-between">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="RememberPassword">
                                            <label class="form-check-label" for="RememberPassword">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                    <!--//col-6-->
                                    <div class="col-6">
                                        <div class="forgot-password text-end">
                                            <a href="">Forgot password?</a>
                                        </div>
                                    </div>
                                    <!--//col-6-->
                                </div>
                                <!--//extra-->
                            </div>
                            <!--//form-group-->
                            <div class="text-center mb-3">
                                <input type="submit" name="login" class="btn app-btn-primary w-100 theme-btn mx-auto"
                                    value="Log In">
                            </div>
                            <div class="text-center">
                                <p>
                                    <strong>Admin User Login:</strong><br>
                                    <strong>Email: </strong>john.doe@libraryhub.com<br>
                                    <strong>Password:</strong> john<br><br>
                                </p>
                            </div>
                        </form>

                        <div class="auth-option text-center pt-5">No Account? Sign up <a class="text-link"
                                href="">here</a>.</div>
                    </div>
                    <!--//auth-form-container-->

                </div>
                <!--//auth-body-->

            </div>
            <!--//flex-column-->
        </div>
        <!--//auth-main-col-->

    </div>
    <!--//row-->

</body>

</html>