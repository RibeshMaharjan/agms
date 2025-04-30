<?php
    echo  "Failed"; //This is a simple
    
    // Store success message in session
    $_SESSION['success'] = "Payment failed. Please try again.";
    header('Location: ./../index.php');
?>