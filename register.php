<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$error = '';
$success = '';

if(isset($_POST['submit']))
{
  $fullname = trim($_POST['fullname']);
  $username = trim($_POST['username']);
  $mobile = trim($_POST['mobile']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  $confirm_password = trim($_POST['confirm_password']);
  $address = trim($_POST['address']);

  // Server-side validation
  if(empty($fullname) || empty($username) || empty($mobile) || empty($email) || empty($password) || empty($confirm_password) || empty($address)) {
    $error = "All fields are required.";
  } elseif(strlen($username) < 4) {
    $error = "Username must be at least 4 characters long.";
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email address.";
  } elseif(strlen($mobile) != 10 || !is_numeric($mobile)) {
    $error = "Please enter a valid 10-digit mobile number.";
  } elseif(strlen($password) < 6) {
    $error = "Password must be at least 6 characters long.";
  } elseif($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } else {
    // Sanitize inputs to prevent SQL injection
    $fullname = mysqli_real_escape_string($con, $fullname);
    $username = mysqli_real_escape_string($con, $username);
    $mobile = mysqli_real_escape_string($con, $mobile);
    $email = mysqli_real_escape_string($con, $email);
    $address = mysqli_real_escape_string($con, $address);
    
    // Check if username already exists
    $query_check = mysqli_query($con, "select ID from tblusers where UserName='$username'");
    if(mysqli_num_rows($query_check) > 0) {
      $error = "Username already taken. Please choose another username.";
    } else {
      // Check if email already exists
      $email_check = mysqli_query($con, "select ID from tblusers where Email='$email'");
      if(mysqli_num_rows($email_check) > 0) {
        $error = "Email already registered. Please use another email or login.";
      } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = mysqli_query($con, "insert into tblusers(FullName,UserName,MobileNumber,Email,Password,Address) value('$fullname','$username','$mobile','$email','$hashed_password','$address')");
        if($query) {
          $success = "Registration successful. You can now login.";
          // Redirect after 2 seconds
          echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
        } else {
          $error = "Something went wrong. Please try again.";
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="zxx">
   <head>
      <title>Art Gallery Management System | Registration Page</title>
      
      <script>
         addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
         }, false);
         
         function hideURLbar() {
            window.scrollTo(0, 1);
         }
         
         // Form validation function
         function validateRegistrationForm() {
            var fullname = document.forms["registrationForm"]["fullname"].value;
            var username = document.forms["registrationForm"]["username"].value;
            var mobile = document.forms["registrationForm"]["mobile"].value;
            var email = document.forms["registrationForm"]["email"].value;
            var password = document.forms["registrationForm"]["password"].value;
            var confirm_password = document.forms["registrationForm"]["confirm_password"].value;
            var address = document.forms["registrationForm"]["address"].value;
            var errorDiv = document.getElementById("clientError");
            var isValid = true;
            
            // Clear previous errors
            errorDiv.innerHTML = '';
            errorDiv.style.display = 'none';
            
            if (fullname.trim() == "") {
               showError("Full Name is required");
               isValid = false;
            }
            
            if (username.trim() == "") {
               showError("Username is required");
               isValid = false;
            }
            
            if (username.length < 4) {
               showError("Username must be at least 4 characters long");
               isValid = false;
            }
            
            if (mobile.trim() == "") {
               showError("Mobile Number is required");
               isValid = false;
            }
            
            if (mobile.length != 10 || isNaN(mobile)) {
               showError("Please enter a valid 10-digit mobile number");
               isValid = false;
            }
            
            if (email.trim() == "") {
               showError("Email is required");
               isValid = false;
            }
            
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
               showError("Please enter a valid email address");
               isValid = false;
            }
            
            if (password.trim() == "") {
               showError("Password is required");
               isValid = false;
            }
            
            if (password.length < 6) {
               showError("Password must be at least 6 characters long");
               isValid = false;
            }
            
            if (confirm_password.trim() == "") {
               showError("Confirm Password is required");
               isValid = false;
            }
            
            if (password !== confirm_password) {
               showError("Passwords do not match");
               isValid = false;
            }
            
            if (address.trim() == "") {
               showError("Address is required");
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
               <li>Register</li>
            </ul>
         </div>
      </div>
      <!-- //short-->
      <!--contact -->
      <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
         <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
            <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Register</h3>
            <div id="clientError" style="display: none;"></div>
            <?php if(!empty($error)) { ?>
            <div class="alert alert-danger text-center">
               <?php echo $error; ?>
            </div>
            <?php } ?>
            <?php if(!empty($success)) { ?>
            <div class="alert alert-success text-center">
               <?php echo $success; ?>
            </div>
            <?php } ?>
            <div class="contact-list-grid">
               <form action="#" method="post" name="registrationForm" onsubmit="return validateRegistrationForm()">
                  <div class=" agile-wls-contact-mid">
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Full Name" name="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" maxlength="10" pattern="[0-9]+" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="password" class="form-control" placeholder="Password" name="password">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password">
                     </div>
                     <div class="form-group contact-forms">
                        <textarea class="form-control" placeholder="Address" name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                     </div>
                     <button type="submit" class="btn btn-block sent-butnn" name="submit">Register</button>
                     <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php" class="fw-bold text-danger">Login here</a></p>
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