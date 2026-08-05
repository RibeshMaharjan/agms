<?php
include('includes/header.php');

// Get artist profile info
$profile_query = mysqli_query($con, "SELECT * FROM tblusers WHERE ID='$artist_id'");
$profile = mysqli_fetch_array($profile_query);
$artist_profile_id = $profile['ArtistProfileID'] ?? 0;

// Get art count
$art_count = 0;
$recent_art = [];
if ($artist_profile_id) {
    $count_result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM tblartproduct WHERE Artist='$artist_profile_id'");
    $art_count = mysqli_fetch_array($count_result)['cnt'] ?? 0;
    
    $recent_query = mysqli_query($con, "SELECT * FROM tblartproduct WHERE Artist='$artist_profile_id' ORDER BY CreationDate DESC LIMIT 5");
    while ($row = mysqli_fetch_assoc($recent_query)) {
        $recent_art[] = $row;
    }
}
?>

<h3><i class="fa fa-laptop"></i> Dashboard</h3>
<hr>

<div class="row">
  <div class="col-md-4">
    <div class="stat-box text-center">
      <h2><?php echo $art_count; ?></h2>
      <p>Total Art Uploaded</p>
      <a href="add-art.php" class="btn btn-primary btn-sm">Upload New Art</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-box text-center">
      <h2><i class="fa fa-user-circle"></i></h2>
      <p>My Profile</p>
      <a href="profile.php" class="btn btn-default btn-sm">View Profile</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-box text-center">
      <h2><i class="fa fa-list"></i></h2>
      <p>Manage Art</p>
      <a href="manage-art.php" class="btn btn-default btn-sm">View All</a>
    </div>
  </div>
</div>

<div class="row" style="margin-top: 20px;">
  <div class="col-md-12">
    <div class="stat-box">
      <h4>Recent Uploads</h4>
      <?php if (empty($recent_art)): ?>
        <p>No art uploaded yet. <a href="add-art.php">Upload your first art piece</a>.</p>
      <?php else: ?>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Title</th>
              <th>Price</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_art as $art): ?>
            <tr>
              <td><?php echo htmlspecialchars($art['Title']); ?></td>
              <td>Rs. <?php echo number_format($art['SellingPricing']); ?></td>
              <td><?php echo date('M d, Y', strtotime($art['CreationDate'])); ?></td>
              <td><a href="edit-art.php?id=<?php echo $art['ID']; ?>" class="btn btn-xs btn-default">Edit</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>