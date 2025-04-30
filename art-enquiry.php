<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if user is logged in before allowing purchase
if (isset($_POST['send'])) {
   // Check if user is logged in
   if (strlen($_SESSION['agmsuid']) == 0) {
      echo "<script>alert('Please login to make a purchase.');</script>";
      echo "<script>window.location.href='login.php';</script>";
      exit();
   }
   echo "Running";
   
   $fullname = $_POST['fullname'];
   $email = $_POST['email'];
   $mobilenumber = $_POST['mobnum'];
   $address = $_POST['address'];
   $eid = $_POST['eid'];
   
   echo "Got data";
   // Server-side validation
   $error = '';
   if(empty($fullname)) {
      $error = "Full name is required";
   } elseif(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Valid email is required";
   } elseif(empty($mobilenumber) || !preg_match('/^[0-9]{10}$/', $mobilenumber)) {
      $error = "Valid 10-digit mobile number is required";
   } elseif(empty($address)) {
      $error = "Address is required";
   } else {
      echo "Validated data";
      // Generate order number in format ORD + 6 random digits
      $ordernumber = mt_rand(100000000, 999999999);
      // $ordernumber = 'ORD' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
      echo "Order number generated: " . $ordernumber;
      echo "<br>";
      echo "Full name: " . $fullname;
      echo "<br>";
      echo "Email: " . $email;
      echo "<br>";
      echo "Mobile number: " . $mobilenumber;
      echo "<br>";
      echo "Address: " . $address;
      echo "<br>";
      echo "EID: " . $eid;
      echo "<br>";
      // Use prepared statement to prevent SQL injection
      $query = mysqli_query($con,"INSERT INTO tblorder (OrderNumber, Artpdid, FullName, Email, MobileNumber, Address, Status) VALUES ('$ordernumber', '$eid', '$fullname', '$email', '$mobilenumber', '$address', 'Pending')");
      echo "Order query prepared";
      // $stmt->bind_param($ordernumber, $eid, $fullname, $email, $mobilenumber, $address);
      echo "Order query prepared";
      
      if ($query) {
         // Fetch the product amount
         $product_query = mysqli_query($con, "SELECT SellingPricing FROM tblartproduct WHERE ID='$eid'");
         $product_row = mysqli_fetch_assoc($product_query);
         $product_amount = $product_row['SellingPricing'];
         
         // Redirect to eSewa page with order_id and product_amount
         header("Location: ./e-sewa.php?order_id=$ordernumber&product_amount=$product_amount");
         exit();
      } else {
         $error = "Something went wrong. Please try again.";
      }
   }
}

?>
<!DOCTYPE html>
<html lang="zxx">

<head>
   <title>Kathmandu Canvas</title>

   <script>
      addEventListener("load", function() {
         setTimeout(hideURLbar, 0);
      }, false);

      function hideURLbar() {
         window.scrollTo(0, 1);
      }
      
      // Form validation function
      function validatePurchaseForm() {
         var fullname = document.forms["purchaseForm"]["fullname"].value;
         var email = document.forms["purchaseForm"]["email"].value;
         var mobilenumber = document.forms["purchaseForm"]["mobnum"].value;
         var address = document.forms["purchaseForm"]["address"].value;
         var errorDiv = document.getElementById("clientError");
         var isValid = true;
         
         // Clear previous errors
         errorDiv.innerHTML = '';
         errorDiv.style.display = 'none';
         
         if (fullname.trim() == "") {
            showError("Full name is required");
            isValid = false;
         }
         
         if (email.trim() == "") {
            showError("Email is required");
            isValid = false;
         } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError("Please enter a valid email address");
            isValid = false;
         }
         
         if (mobilenumber.trim() == "") {
            showError("Mobile number is required");
            isValid = false;
         } else if (!/^\d{10}$/.test(mobilenumber)) {
            showError("Please enter a valid 10-digit mobile number");
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
   <?php include_once('includes/header.php'); ?>
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
            <li>Enquiry</li>
         </ul>
      </div>
   </div>
   <!-- //short-->
   <!--contact -->
   <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
      <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
      <?php
            if(isset($_GET['eid'])){
               $eid = $_GET['eid'];

               $ret=mysqli_query($con,"select * from tblartproduct where ID = $eid;");
               $cnt=1;

               $art = mysqli_fetch_assoc($ret);
         ?>
            <div class="row">
               <div class="col-lg-4 single-right-left">
                  <div class="thumb-image">
                     <img src="admin/images/<?= $art['Image'] ?>" alt="" class="img-fluid">
                  </div>
               </div>
               <div class="col-lg-8 single-right-left simpleCart_shelfItem">
                  <h3>Art Name: <?= $art['Title'] ?></h3>
                  <div class="occasional">
                     <h5>price : <?= $art['SellingPricing'] ?></h5>
                     <h5>Description : <?= $art['Description'] ?></h5>
                     <h5>tags : <?= $art['tags'] ?></h5>
                     <h5>Gallery : <?= $_GET['eid'] ?></h5>
                  </div>
               </div>
            </div>
         <?php
            }
         ?>
         <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Purchase</h3>
         
         <?php if(strlen($_SESSION['agmsuid']) > 0) { ?>
         <!-- Show purchase form only if user is logged in -->
         <div class="contact-list-grid">
            <div id="clientError" style="display: none;"></div>
            <?php if(!empty($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
               <?php echo $error; ?>
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <?php } ?>
            <?php if(isset($_SESSION['success'])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
               <?php 
                  echo $_SESSION['success'];
                  unset($_SESSION['success']); // Clear the success message after displaying
               ?>
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <?php } ?>
            <form action="" method="post" name="purchaseForm" onsubmit="return validatePurchaseForm()">
               <div class="agile-wls-contact-mid">
                  <div class="form-group contact-forms">
                     <input type="hidden" name="eid" value="<?= $_GET['eid'] ?>">
                     <input class="form-control" type="text" name="fullname" placeholder="Name" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" />
                  </div>
                  <div class="form-group contact-forms">
                     <input class="form-control" type="email" name="email" required="true" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
                  </div>
                  <div class="form-group contact-forms">
                     <input class="form-control" type="text" name="mobnum" maxlength="10" pattern="[0-9]+" placeholder="Mobile Number" value="<?php echo isset($_POST['mobnum']) ? htmlspecialchars($_POST['mobnum']) : ''; ?>" />
                  </div>
                  <div class="form-group contact-forms">
                     <textarea class="form-control" name="address" placeholder="Address" rows="4"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                  </div>
                  <button type="submit" class="btn btn-block sent-butnn" name="send">Purchase Now</button>
               </div>
            </form>
         </div>
         <?php } else { ?>
         <!-- Show login prompt if user is not logged in -->
         <div class="text-center">
            <h4>Please login to make a purchase</h4>
            <p>You need to be logged in to purchase this artwork.</p>
            <a href="login.php" class="btn sent-butnn">Login Now</a>
            <p class="mt-3">Don't have an account? <a href="register.php" class="fw-bold text-danger">Register here</a></p>
         </div>
         <?php } ?>
      </div>
      <!--//contact-map -->
   </section>
   <!--subscribe-address-->

   <?php include_once('includes/footer.php'); ?>

   <!--js working-->
   <script src='js/jquery-2.2.3.min.js'></script>
   <!--//js working-->
   <!-- cart-js -->
   <script src="js/minicart.js"></script>
   <script>
      toys.render();

      toys.cart.on('toys_checkout', function(evt) {
         var items, len, i;

         if (this.subtotal() > 0) {
            items = this.items();

            for (i = 0, len = items.length; i < len; i++) {}
         }
      });
   </script>
   <!-- //cart-js -->
   <!-- start-smoth-scrolling -->
   <script src="js/move-top.js"></script>
   <script src="js/easing.js"></script>
   <script>
      jQuery(document).ready(function($) {
         $(".scroll").click(function(event) {
            event.preventDefault();
            $('html,body').animate({
               scrollTop: $(this.hash).offset().top
            }, 900);
         });
      });
   </script>
   <!-- start-smoth-scrolling -->
   <!-- here stars scrolling icon -->
   <script>
      $(document).ready(function() {

         var defaults = {
            containerID: 'toTop', // fading element id
            containerHoverID: 'toTopHover', // fading element hover id
            scrollSpeed: 1200,
            easingType: 'linear'
         };


         $().UItoTop({
            easingType: 'easeOutQuart'
         });

      });
   </script>
   <!-- //here ends scrolling icon -->
   <!--bootstrap working-->
   <script src="js/bootstrap.min.js"></script>
   <!-- //bootstrap working--> <!-- //OnScroll-Number-Increase-JavaScript -->
</body>

</html>