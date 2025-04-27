<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit']))
{
  $fullname=$_POST['fullname'];
  $username=$_POST['username'];
  $mobile=$_POST['mobile'];
  $email=$_POST['email'];
  $password=md5($_POST['password']);
  $address=$_POST['address'];

  // Check if username already exists
  $query_check=mysqli_query($con,"select ID from tblusers where UserName='$username'");
  $ret_check=mysqli_fetch_array($query_check);
  if($ret_check>0){
    echo "<script>alert('Username already taken. Please choose another username');</script>";
  }
  else{
    $query=mysqli_query($con,"insert into tblusers(FullName,UserName,MobileNumber,Email,Password,Address) value('$fullname','$username','$mobile','$email','$password','$address')");
    if($query){
      echo "<script>alert('Registration successful. You can now login.');</script>";
      echo "<script>window.location.href='login.php'</script>";
    }
    else{
      echo "<script>alert('Something went wrong. Please try again.');</script>";
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
            <div class="contact-list-grid">
               <form action="#" method="post">
                  <div class=" agile-wls-contact-mid">
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Full Name" name="fullname" required="true">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Username" name="username" required="true">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" required="true" maxlength="10" pattern="[0-9]+">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="email" class="form-control" placeholder="Email" name="email" required="true">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="password" class="form-control" placeholder="Password" name="password" required="true">
                     </div>
                     <div class="form-group contact-forms">
                        <textarea class="form-control" placeholder="Address" name="address" required="true"></textarea>
                     </div>
                     <button type="submit" class="btn btn-block sent-butnn" name="submit">Register</button>
                     <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
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