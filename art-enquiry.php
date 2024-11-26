<?php
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['send'])) {
   $fullname = $_POST['fullname'];
   $email=$_POST['email'];
   $mobilenumber = $_POST['mobnum'];
   $address = $_POST['address'];
   $ordernumber = mt_rand(100000000, 999999999);
   $eid = $_GET['eid'];

   $query1=mysqli_query($con,"insert into tblorder(Artpdid,FullName,Email,MobileNumber,Address,OrderNumber) value('$eid','$fullname','$email','$mobilenumber','$address','$ordernumber')");

   if ($query1) {
      $order_id = $ordernumber;

      // Fetch the product amount
      $product_query = mysqli_query($con, "SELECT SellingPricing FROM tblartproduct WHERE ID='$eid'");
      $product_row = mysqli_fetch_assoc($product_query);
      $product_amount = $product_row['SellingPricing'];

      // Redirect to eSewa page with order_id and product_amount
      header("Location: ./e-sewa.php?order_id=$order_id&product_amount=$product_amount");
      exit();
   } else {
      echo "<script>alert('Something went wrong. Please try again.');</script>";
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
                  </div>
               </div>
            </div>
         <?php
            }
         ?>
         <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">Purchase</h3>
         <?php
         // if ($_GET['eid']) {
         //    $eid = $_GET['eid'];

         //    $ret = mysqli_query($con, "select tblarttype.ID as atid,tblarttype.ArtType as typename,tblartmedium.ID as amid,tblartmedium.ArtMedium as amname,tblartproduct.ID as apid,tblartist.Name,tblartproduct.Title,tblartproduct.Dimension,tblartproduct.Orientation,tblartproduct.Size,tblartproduct.Artist,tblartproduct.ArtType,tblartproduct.ArtMedium,tblartproduct.SellingPricing,tblartproduct.Description,tblartproduct.Image,tblartproduct.Image1,tblartproduct.Image2,tblartproduct.Image3,tblartproduct.Image4,tblartproduct.RefNum,tblartproduct.ArtType from tblartproduct join tblarttype on tblarttype.ID=tblartproduct.ArtType join tblartmedium on tblartmedium.ID=tblartproduct.ArtMedium join tblartist on tblartist.ID=tblartproduct.Artist where tblartproduct.ID='$eid'");
         //    $cnt = 1;

         //    $row = mysqli_fetch_array($ret);
         // }
         ?>
         
         <div class="contact-list-grid">
            <form action="" method="post">
               <div class="agile-wls-contact-mid">
                  <div class="form-group contact-forms">
                     <input class="form-control" type="text" name="fullname" placeholder="Name" />
                  </div>
                  <div class="form-group contact-forms">
                     <input class="form-control" type="email" name="email" required="true" placeholder="Email"/>
                  </div>
                  <div class="form-group contact-forms">
                     <input class="form-control" type="text" name="mobnum" maxlength="10" pattern="[0-9]+" placeholder="Mobile Number" />
                  </div>
                  <div class="form-group contact-forms">
                     <textarea class="form-control" name="address" placeholder="Address" rows="4"></textarea>
                  </div>
                  <button type="submit" class="btn btn-block sent-butnn" name="send">Send</button>
               </div>
            </form>
         </div>
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