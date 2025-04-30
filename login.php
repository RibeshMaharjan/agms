<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$error = '';

if(isset($_POST['login']))
{
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  
  // Server-side validation
  if(empty($username) || empty($password)) {
    $error = "Username and password are required.";
  } else {
    // Sanitize inputs to prevent SQL injection
    $username = mysqli_real_escape_string($con, $username);
    
    $query = mysqli_query($con, "select * from tblusers where UserName='$username'");
    
    if(mysqli_num_rows($query) > 0) {
      $ret = mysqli_fetch_array($query);
      if(password_verify($password, $ret['Password'])){
        $_SESSION['agmsuid'] = $ret['ID'];
        $_SESSION['agmsuname'] = $ret['UserName'];
        $_SESSION['agmsfullname'] = $ret['FullName'];
        echo "<script type='text/javascript'> document.location ='index.php'; </script>";
      } else {
        $error = "Invalid password. Please try again.";
      }
    } else {
      $error = "Username not found. Please check your username or register.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="zxx">
   <head>
      <title>Art Gallery Management System | Login Page</title>
      
      <script>
         addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
         }, false);
         
         function hideURLbar() {
            window.scrollTo(0, 1);
         }
         
         // Form validation function
         function validateLoginForm() {
            var username = document.forms["loginForm"]["username"].value;
            var password = document.forms["loginForm"]["password"].value;
            var errorDiv = document.getElementById("clientError");
            var isValid = true;
            
            // Clear previous errors
            errorDiv.innerHTML = '';
            errorDiv.style.display = 'none';
            
            if (username.trim() == "") {
               showError("Username is required");
               isValid = false;
            }
            
            if (password.trim() == "") {
               showError("Password is required");
               isValid = false;
            }
            
            function showError(message) {
               errorDiv.style.display = 'block';
               errorDiv.innerHTML += '<div class="alert alert-danger">' + message + '</div>';
            }
            
            return isValid;
         }
      </script>
      <!--//meta tags ends here-->
      <!--booststrap-->
      <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
      <!--//booststrap end-->
      <!-- font-awesome icons -->
      <link href="css/fontawesome-all.min.css" rel="stylesheet" type="text/css" media="all">
      <!-- //font-awesome icons -->
      <!--Shoping cart-->
      <link rel="stylesheet" href="css/shop.css" type="text/css" />
      <!--//Shoping cart-->
      <!--stylesheets-->
      <link href="css/style.css" rel='stylesheet' type='text/css' media="all">
      <!--//stylesheets-->
      <link href="//fonts.googleapis.com/css?family=Sunflower:500,700" rel="stylesheet">
      <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
   </head>
   <body>
      <!--headder-->
      <?php include_once('includes/header.php');?>
      <!-- banner -->
      <div class="inner_page-banner one-img">
      </div>
      <!--//banner -->
      <!-- short -->
      <div class="using-border py-3">
         <div class="inner_breadcrumb  ml-4">
            <ul class="short_ls">
               <li>
                  <a href="index.php">Home</a>
                  <span>/</span>
               </li>
               <li>Login</li>
            </ul>
         </div>
      </div>
      <!-- //short-->
      <!--contact -->
      <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
         <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
            <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Login</h3>
            <div id="clientError" style="display: none;"></div>
            <?php if(!empty($error)) { ?>
            <div class="alert alert-danger text-center">
               <?php echo $error; ?>
            </div>
            <?php } ?>
            <div class="contact-list-grid">
               <form action="#" method="post" name="loginForm" onsubmit="return validateLoginForm()">
                  <div class=" agile-wls-contact-mid">
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Username" name="username" required="true" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="password" class="form-control" placeholder="Password" name="password" required="true">
                     </div>
                     <button type="submit" class="btn btn-block sent-butnn" name="login">Login</button>
                     <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="register.php" class="fw-bold text-danger">Register here</a></p>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </section>
      <!-- //contact -->
      <?php include_once('includes/footer.php');?>
      <!--js working-->
      <script src='js/jquery-2.2.3.min.js'></script>
      <!--//js working-->
      <!-- cart-js -->
      <script src="js/minicart.js"></script>
      <!-- //cart-js -->
      <!--bootstrap working-->
      <script src="js/bootstrap.min.js"></script>
      <!-- //bootstrap working-->
   </body>
</html> 