<?php
session_start();
if (empty($_SESSION['agmsuid']) || ($_SESSION['agmsrole'] ?? '') !== 'artist') {
    header('location: ../login.php');
    exit();
}
include('../includes/dbconnection.php');
$artist_id = $_SESSION['agmsuid'];
$artist_name = $_SESSION['agmsfullname'] ?? 'Artist';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Artist Panel | Art Gallery Management System</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/fontawesome-all.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <style>
    .sidebar { min-height: 100vh; background: #2c3e50; padding-top: 20px; }
    .sidebar a { color: #ecf0f1; display: block; padding: 12px 20px; text-decoration: none; }
    .sidebar a:hover, .sidebar a.active { background: #34495e; color: #fff; }
    .main-content { padding: 20px; }
    .stat-box { background: #fff; border-radius: 5px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar">
        <h4 class="text-center text-white mb-4">Artist Panel</h4>
        <a href="dashboard.php"><i class="fa fa-tachometer"></i> Dashboard</a>
        <a href="add-art.php"><i class="fa fa-upload"></i> Add Art</a>
        <a href="manage-art.php"><i class="fa fa-list"></i> My Art</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
      </div>
      <div class="col-md-10 main-content">
        <nav class="navbar navbar-default">
          <div class="navbar-header">
            <span class="navbar-brand">Welcome, <?php echo htmlspecialchars($artist_name); ?></span>
          </div>
        </nav>