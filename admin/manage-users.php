<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['agmsaid']==0)) {
  header('location:logout.php');
} else {

if(isset($_GET['delid']))
{
$rid=intval($_GET['delid']);
$sql=mysqli_query($con,"delete from tblusers where ID='$rid'");
 echo "<script>alert('User deleted successfully');</script>"; 
  echo "<script>window.location.href = 'manage-users.php'</script>";     
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Manage Users | Art Gallery Management System</title>
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
            <h3 class="page-header"><i class="fa fa-users"></i> Manage Users</h3>
            <ol class="breadcrumb">
              <li><i class="fa fa-home"></i><a href="dashboard.php">Home</a></li>
              <li><i class="fa fa-users"></i>Users</li>
              <li><i class="fa fa-th-list"></i>Manage Users</li>
            </ol>
          </div>
        </div>
        <!-- page start-->
        <div class="row">
          <div class="col-sm-12">
            <section class="panel">
              <header class="panel-heading">
                Manage Users
              </header>
              <table class="table">
                <thead>
                  <tr>
                    <th>S.NO</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Registration Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $ret=mysqli_query($con,"select * from tblusers");
                $cnt=1;
                while ($row=mysqli_fetch_array($ret)) {
                ?>
                  <tr>
                    <td><?php echo $cnt;?></td>
                    <td><?php echo $row['FullName'];?></td>
                    <td><?php echo $row['UserName'];?></td>
                    <td><?php echo $row['Email'];?></td>
                    <td><?php echo $row['MobileNumber'];?></td>
                    <td><?php echo $row['Address'];?></td>
                    <td><?php echo $row['RegDate'];?></td>
                    <td>
                      <a href="edit-user-detail.php?editid=<?php echo $row['ID'];?>" class="btn btn-success">Edit</a> || 
                      <a href="manage-users.php?delid=<?php echo $row['ID'];?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                  </tr>
                <?php 
                $cnt=$cnt+1;
                }?>
                </tbody>
              </table>
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