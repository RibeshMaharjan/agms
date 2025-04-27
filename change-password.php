<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['agmsuid']==0)) {
  header('location:logout.php');
}
else{
  if(isset($_POST['submit']))
  {
    $userid=$_SESSION['agmsuid'];
    $cpassword=md5($_POST['currentpassword']);
    $newpassword=md5($_POST['newpassword']);
    
    $query=mysqli_query($con,"select ID from tblusers where ID='$userid' and Password='$cpassword'");
    $row=mysqli_fetch_array($query);
    if($row>0){
      $ret=mysqli_query($con,"update tblusers set Password='$newpassword' where ID='$userid'");
      echo '<script>alert("Your password has been changed successfully.")</script>';
    } else {
      echo '<script>alert("Your current password is wrong.")</script>';
    }
  }
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
   <title>Art Gallery Management System | Change Password</title>
   <script type="application/x-javascript">
      addEventListener("load", function() { 
          setTimeout(hideURLbar, 0); 
      }, false);
      function hideURLbar(){ 
          window.scrollTo(0,1); 
      }
   </script>
   <!-- Custom Theme files -->
   <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
   <!-- //Bootstrap end -->
   <!-- font-awesome icons -->
   <link href="css/fontawesome-all.min.css" rel="stylesheet" type="text/css" media="all">
   <!-- //font-awesome icons -->
   <!--Shoping cart-->
   <link rel="stylesheet" href="css/shop.css" type="text/css" />
   <!--//Shoping cart-->
   <link rel="stylesheet" type="text/css" href="css/jquery-ui1.css">
   <!--stylesheets-->
   <link href="css/style.css" rel='stylesheet' type='text/css' media="all">
   <!--//stylesheets-->
   <link href="//fonts.googleapis.com/css?family=Sunflower:500,700" rel="stylesheet">
   <link href="//fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
   <script type="text/javascript">
      function checkpass() {
          if(document.changepassword.newpassword.value != document.changepassword.confirmpassword.value) {
              alert('New Password and Confirm Password field does not match');
              document.changepassword.confirmpassword.focus();
              return false;
          }
          return true;
      } 
   </script>
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
            <li>Change Password</li>
         </ul>
      </div>
   </div>
   <!-- //short-->
   <!--contact -->
   <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
      <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
         <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Change Password</h3>
         <div class="contact-list-grid">
            <form action="#" method="post" name="changepassword" onsubmit="return checkpass();">
               <div class=" agile-wls-contact-mid">
                  <div class="form-group contact-forms">
                     <label>Current Password</label>
                     <input type="password" class="form-control" placeholder="Current Password" name="currentpassword" required="true">
                  </div>
                  <div class="form-group contact-forms">
                     <label>New Password</label>
                     <input type="password" class="form-control" placeholder="New Password" name="newpassword" required="true">
                  </div>
                  <div class="form-group contact-forms">
                     <label>Confirm Password</label>
                     <input type="password" class="form-control" placeholder="Confirm Password" name="confirmpassword" required="true">
                  </div>
                  <button type="submit" class="btn btn-block sent-butnn" name="submit">Change</button>
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
   <!-- price range (top products) -->
   <script src="js/jquery-ui.js"></script>
   <!-- //price range (top products) -->
   <!--bootstrap working-->
   <script src="js/bootstrap.min.js"></script>
   <!-- //bootstrap working-->
</body>
</html>
<?php } ?> 