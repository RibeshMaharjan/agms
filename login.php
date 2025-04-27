<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['login']))
{
  $username=$_POST['username'];
  $password=md5($_POST['password']);

  $query=mysqli_query($con,"select ID from tblusers where UserName='$username' && Password='$password'");
  $ret=mysqli_fetch_array($query);
  if($ret>0){
    $_SESSION['agmsuid']=$ret['ID'];
    echo "<script type='text/javascript'> document.location ='index.php'; </script>";
  }
  else{
    echo "<script>alert('Invalid Details');</script>";
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
            <div class="contact-list-grid">
               <form action="#" method="post">
                  <div class=" agile-wls-contact-mid">
                     <div class="form-group contact-forms">
                        <input type="text" class="form-control" placeholder="Username" name="username" required="true">
                     </div>
                     <div class="form-group contact-forms">
                        <input type="password" class="form-control" placeholder="Password" name="password" required="true">
                     </div>
                     <button type="submit" class="btn btn-block sent-butnn" name="login">Login</button>
                     <div class="mt-3 text-center">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
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