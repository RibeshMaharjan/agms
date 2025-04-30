<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if user is logged in
if (strlen($_SESSION['agmsuid']) == 0) {
    header('location:login.php');
    exit();
}

$userid = $_SESSION['agmsuid'];
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>My Orders - Kathmandu Canvas</title>
    <script>
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <!--booststrap-->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
    <!--//booststrap end-->
    <!-- font-awesome icons -->
    <link href="css/fontawesome-all.min.css" rel="stylesheet" type="text/css" media="all">
    <!-- //font-awesome icons -->
    <!--Shoping cart-->
    <link rel="stylesheet" href="css/shop.css" type="text/css" />
    <!--//Shoping cart-->
    <link rel="stylesheet" type="text/css" href="css/jquery-ui1.css">
    <link href="css/easy-responsive-tabs.css" rel='stylesheet' type='text/css' />
    <link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />
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
    <!-- short -->
    <div class="using-border py-3">
        <div class="inner_breadcrumb  ml-4">
            <ul class="short_ls">
                <li>
                    <a href="index.php">Home</a>
                    <span>/</span>
                </li>
                <li>My Orders</li>
            </ul>
        </div>
    </div>
    <!-- //short-->

    <!-- Orders Section -->
    <section class="contact py-lg-4 py-md-3 py-sm-3 py-3">
        <div class="container py-lg-5 py-md-4 py-sm-4 py-3">
            <h3 class="title text-center mb-lg-5 mb-md-4 mb-sm-4 mb-3">My Orders</h3>
            
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Order #</th>
                                    <th>Artwork</th>
                                    <th>Amount</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Admin Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Join query to get order details along with artwork information
                                $query = mysqli_query($con, "SELECT o.*, a.Title, a.SellingPricing, a.Image 
                                                           FROM tblorder o 
                                                           LEFT JOIN tblartproduct a ON o.Artpdid = a.ID 
                                                           WHERE o.Email = (SELECT Email FROM tblusers WHERE ID = $userid) 
                                                           ORDER BY o.OrderDate DESC");
                                
                                if (mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_array($query)) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['OrderNumber']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="admin/images/<?php echo $row['Image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $row['Title']; ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rs. <?php echo number_format($row['SellingPricing'], 2); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($row['OrderDate'])); ?></td>
                                        <td>
                                            <?php 
                                            $status = $row['Status'];
                                            $statusClass = '';
                                            switch($status) {
                                                case 'Pending':
                                                    $statusClass = 'badge badge-warning';
                                                    break;
                                                case 'Accepted':
                                                    $statusClass = 'badge badge-success';
                                                    break;
                                                case 'Rejected':
                                                    $statusClass = 'badge badge-danger';
                                                    break;
                                                default:
                                                    $statusClass = 'badge badge-secondary';
                                            }
                                            ?>
                                            <span class="<?php echo $statusClass; ?>"><?php echo $status; ?></span>
                                        </td>
                                        <td><?php echo $row['AdminRemark'] ? $row['AdminRemark'] : 'Not Updated'; ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No orders found</td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <!-- //bootstrap working-->
</body>

</html> 