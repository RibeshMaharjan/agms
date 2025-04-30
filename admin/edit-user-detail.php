<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['agmsaid']==0)) {
  header('location:logout.php');
} else {
  if(isset($_POST['submit'])) {
    $uid=$_GET['editid'];
    $fullname=$_POST['fullname'];
    $username=$_POST['username'];
    $email=$_POST['email'];
    $mobilenumber=$_POST['mobilenumber'];
    $address=$_POST['address'];
    
    $sql=mysqli_query($con,"update tblusers set FullName='$fullname',UserName='$username',Email='$email',MobileNumber='$mobilenumber',Address='$address' where ID='$uid'");
    if($sql) {
      echo "<script>alert('User details updated successfully');</script>";
      echo "<script>window.location.href = 'manage-users.php'</script>";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Edit User Details | Art Gallery Management System</title>
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
</head>

<body>
  <!-- container section start -->
  <section id="container" class="">
    <!--header start-->
    <?php include_once('includes/header.php');?>
    <!--header end-->

    <!--sidebar start-->
    <?php include_once('includes/sidebar.php');?>

    <!--main content start-->
    <section id="main-content">
      <section class="wrapper">
        <div class="row">
          <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-user"></i> Edit User Details</h3>
            <ol class="breadcrumb">
              <li><i class="fa fa-home"></i><a href="dashboard.php">Home</a></li>
              <li><i class="fa fa-users"></i>Users</li>
              <li><i class="fa fa-edit"></i>Edit User Details</li>
            </ol>
          </div>
        </div>
        <!-- page start-->
        <div class="row">
          <div class="col-sm-12">
            <section class="panel">
              <header class="panel-heading">
                Edit User Details
              </header>
              <div class="panel-body">
                <form role="form" method="post">
                  <?php
                  $uid=$_GET['editid'];
                  $ret=mysqli_query($con,"select * from tblusers where ID='$uid'");
                  while ($row=mysqli_fetch_array($ret)) {
                  ?>
                  <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $row['FullName'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['UserName'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['Email'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="mobilenumber">Mobile Number</label>
                    <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" value="<?php echo $row['MobileNumber'];?>" required>
                  </div>
                  <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control" id="address" name="address" required><?php echo $row['Address'];?></textarea>
                  </div>
                  <?php } ?>
                  <button type="submit" class="btn btn-primary" name="submit">Update</button>
                  <a href="manage-users.php" class="btn btn-default">Cancel</a>
                </form>
              </div>
            </section>
          </div>
        </div>
        <!-- page end-->
      </section>
    </section>
    <!--main content end-->
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
</body>
</html>
<?php }  ?> 