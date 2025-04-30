<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['agmsaid']==0)) {
  header('location:logout.php');
} else {
  if(isset($_POST['submit'])) {
    $fullname=$_POST['fullname'];
    $username=$_POST['username'];
    $email=$_POST['email'];
    $mobilenumber=$_POST['mobilenumber'];
    $password=$_POST['password'];
    $address=$_POST['address'];
    
    // Server-side validation
    $errors = array();
    
    // Validate Full Name
    if(empty($fullname)) {
      $errors[] = "Full Name is required";
    } elseif(strlen($fullname) < 3) {
      $errors[] = "Full Name must be at least 3 characters long";
    }
    
    // Validate Username
    if(empty($username)) {
      $errors[] = "Username is required";
    } elseif(strlen($username) < 3) {
      $errors[] = "Username must be at least 3 characters long";
    } else {
      // Check if username already exists
      $check_username = mysqli_query($con, "SELECT UserName FROM tblusers WHERE UserName='$username'");
      if(mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username already exists";
      }
    }
    
    // Validate Email
    if(empty($email)) {
      $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format";
    } else {
      // Check if email already exists
      $check_email = mysqli_query($con, "SELECT Email FROM tblusers WHERE Email='$email'");
      if(mysqli_num_rows($check_email) > 0) {
        $errors[] = "Email already exists";
      }
    }
    
    // Validate Mobile Number
    if(empty($mobilenumber)) {
      $errors[] = "Mobile Number is required";
    } elseif(!preg_match("/^[0-9]{10}$/", $mobilenumber)) {
      $errors[] = "Mobile Number must be 10 digits";
    }
    
    // Validate Password
    if(empty($password)) {
      $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
      $errors[] = "Password must be at least 6 characters long";
    } elseif(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
      $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, and one number";
    }
    
    // Validate Address
    if(empty($address)) {
      $errors[] = "Address is required";
    }
    
    if(empty($errors)) {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $query = mysqli_query($con, "insert into tblusers(FullName,UserName,Email,MobileNumber,Password,Address) value('$fullname','$username','$email','$mobilenumber','$hashed_password','$address')");
      if ($query) {
        echo "<script>alert('User has been added successfully.');</script>";
        echo "<script>window.location.href ='manage-users.php'</script>";
      } else {
        echo "<script>alert('Something Went Wrong. Please try again.');</script>";
      }
    } else {
      $error_message = implode("<br>", $errors);
      echo "<script>alert('$error_message');</script>";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Add User | Art Gallery Management System</title>
  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- bootstrap theme -->
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <!--external css-->
  <!-- font icon -->
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles -->
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <style>
    .error {
      color: red;
      font-size: 12px;
      margin-top: 5px;
    }
    .form-control:focus {
      border-color: #66afe9;
      box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(102, 175, 233, 0.6);
    }
  </style>
</head>

<body>
  <!-- container section start -->
  <section id="container" class="">
    <!--header start-->
    <?php include_once('includes/header.php');?>
    <!--header end-->

    <!--sidebar start-->
    <?php include_once('includes/sidebar.php');?>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <div class="row">
          <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-user"></i> Add User</h3>
            <ol class="breadcrumb">
              <li><i class="fa fa-home"></i><a href="dashboard.php">Home</a></li>
              <li><i class="fa fa-users"></i>Users</li>
              <li><i class="fa fa-user-plus"></i>Add User</li>
            </ol>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <section class="panel">
              <header class="panel-heading">
                Add User Details
              </header>
              <div class="panel-body">
                <form class="form-horizontal" method="post" action="" id="userForm">
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Full Name</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="fullname" name="fullname" type="text" required minlength="3">
                      <div id="fullnameError" class="error"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="username" name="username" type="text" required minlength="3">
                      <div id="usernameError" class="error"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="email" name="email" type="email" required>
                      <div id="emailError" class="error"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Mobile Number</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="mobilenumber" name="mobilenumber" type="text" required pattern="[0-9]{10}" maxlength="10">
                      <div id="mobilenumberError" class="error"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                      <input class="form-control" id="password" name="password" type="password" required minlength="6">
                      <div id="passwordError" class="error"></div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label">Address</label>
                    <div class="col-sm-10">
                      <textarea class="form-control" id="address" name="address" required></textarea>
                      <div id="addressError" class="error"></div>
                    </div>
                  </div>
                  <p style="text-align: center;">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                  </p>
                </form>
              </div>
            </section>
          </div>
        </div>
      </section>
    </section>
    <?php include_once('includes/footer.php');?>
  </section>
  <!-- container section end -->
  <!-- javascripts -->
  <script src="js/jquery.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <!-- nicescroll -->
  <script src="js/jquery.scrollTo.min.js"></script>
  <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
  <!--custome script for all page-->
  <script src="js/scripts.js"></script>
  
  <script>
    $(document).ready(function() {
      // Client-side validation
      $('#userForm').on('submit', function(e) {
        let isValid = true;
        const errors = {};
        
        // Full Name validation
        const fullname = $('#fullname').val().trim();
        if (fullname.length < 3) {
          errors.fullname = 'Full Name must be at least 3 characters long';
          isValid = false;
        }
        
        // Username validation
        const username = $('#username').val().trim();
        if (username.length < 3) {
          errors.username = 'Username must be at least 3 characters long';
          isValid = false;
        }
        
        // Email validation
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          errors.email = 'Please enter a valid email address';
          isValid = false;
        }
        
        // Mobile Number validation
        const mobilenumber = $('#mobilenumber').val().trim();
        const mobileRegex = /^[0-9]{10}$/;
        if (!mobileRegex.test(mobilenumber)) {
          errors.mobilenumber = 'Mobile Number must be 10 digits';
          isValid = false;
        }
        
        // Password validation
        const password = $('#password').val();
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
        if (password.length < 6) {
          errors.password = 'Password must be at least 6 characters long';
          isValid = false;
        } else if (!passwordRegex.test(password)) {
          errors.password = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
          isValid = false;
        }
        
        // Address validation
        const address = $('#address').val().trim();
        if (address.length === 0) {
          errors.address = 'Address is required';
          isValid = false;
        }
        
        // Display errors
        Object.keys(errors).forEach(function(key) {
          $(`#${key}Error`).text(errors[key]);
        });
        
        // Clear previous errors
        $('.error').each(function() {
          if (!errors[$(this).attr('id').replace('Error', '')]) {
            $(this).text('');
          }
        });
        
        if (!isValid) {
          e.preventDefault();
        }
      });
      
      // Clear error messages on input
      $('input, textarea').on('input', function() {
        const fieldId = $(this).attr('id');
        $(`#${fieldId}Error`).text('');
      });
    });
  </script>
</body>
</html>
<?php } ?> 