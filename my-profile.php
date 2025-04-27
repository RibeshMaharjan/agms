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
    $uid=$_SESSION['agmsuid'];
    $fullname=$_POST['fullname'];
    $mobno=$_POST['mobilenumber'];
    $email=$_POST['email'];
    $address=$_POST['address'];
    
    $query=mysqli_query($con, "update tblusers set FullName='$fullname', MobileNumber='$mobno', Email='$email', Address='$address' where ID='$uid'");
    if ($query) {
      echo '<script>alert("Profile has been updated")</script>';
      echo "<script>window.location.href='my-profile.php'</script>";
    }
    else
    {
      echo '<script>alert("Something went wrong. Please try again.")</script>';
    }
  }
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
   <title>Art Gallery Management System | My Profile</title>
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
            <li>My Profile</li>
         </ul>
      </div>
   </div>
   <!-- //short-->
   <!--contact -->
   <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
      <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
         <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">My Profile</h3>
         <div class="contact-list-grid">
            <form method="post">
               <?php
               $uid=$_SESSION['agmsuid'];
               $ret=mysqli_query($con,"select * from tblusers where ID='$uid'");
               $cnt=1;
               while ($row=mysqli_fetch_array($ret)) {
               ?>
               <div class="agile-wls-contact-mid">
                  <div class="form-group contact-forms">
                     <label>Full Name</label>
                     <input type="text" class="form-control" name="fullname" value="<?php echo $row['FullName'];?>" required="true">
                  </div>
                  <div class="form-group contact-forms">
                     <label>Username</label>
                     <input type="text" class="form-control" value="<?php echo $row['UserName'];?>" readonly>
                  </div>
                  <div class="form-group contact-forms">
                     <label>Mobile Number</label>
                     <input type="text" class="form-control" name="mobilenumber" value="<?php echo $row['MobileNumber'];?>" required="true" maxlength="10" pattern="[0-9]+">
                  </div>
                  <div class="form-group contact-forms">
                     <label>Email</label>
                     <input type="email" class="form-control" name="email" value="<?php echo $row['Email'];?>" required="true">
                  </div>
                  <div class="form-group contact-forms">
                     <label>Address</label>
                     <textarea class="form-control" name="address" required="true"><?php echo $row['Address'];?></textarea>
                  </div>
                  <div class="form-group contact-forms">
                     <label>Registration Date</label>
                     <input type="text" class="form-control" value="<?php echo $row['RegDate'];?>" readonly>
                  </div>
                  <?php } ?>
                  <button type="submit" class="btn btn-block sent-butnn" name="submit">Update Profile</button>
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
<?php } ?> 