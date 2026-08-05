<?php
include('includes/header.php');

// Get artist profile data
$profile_query = mysqli_query($con, "SELECT u.*, a.Name as ArtistName, a.MobileNumber as ArtistMobile, a.Email as ArtistEmail, a.Education, a.Award, a.Profilepic FROM tblusers u LEFT JOIN tblartist a ON u.ArtistProfileID = a.ID WHERE u.ID='$artist_id'");
$profile = mysqli_fetch_array($profile_query);

$error = '';
$success = '';

if(isset($_POST['submit']))
{
    $education=mysqli_real_escape_string($con, $_POST['education']);
    $award=mysqli_real_escape_string($con, $_POST['award']);

    $artist_profile_id = $profile['ArtistProfileID'] ?? 0;
    if ($artist_profile_id) {
        $query=mysqli_query($con, "UPDATE tblartist SET Education='$education', Award='$award' WHERE ID='$artist_profile_id'");
        if ($query) {
            $success = "Profile updated successfully.";
            // Refresh
            $profile_query = mysqli_query($con, "SELECT u.*, a.Name as ArtistName, a.MobileNumber as ArtistMobile, a.Email as ArtistEmail, a.Education, a.Award, a.Profilepic FROM tblusers u LEFT JOIN tblartist a ON u.ArtistProfileID = a.ID WHERE u.ID='$artist_id'");
            $profile = mysqli_fetch_array($profile_query);
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>

<h3><i class="fa fa-user"></i> My Profile</h3>
<hr>

<?php if(!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if(!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="stat-box text-center">
      <?php if(!empty($profile['Profilepic'])): ?>
        <img src="../images/<?php echo htmlspecialchars($profile['Profilepic']); ?>" class="img-circle" width="150" height="150" style="object-fit:cover;">
      <?php else: ?>
        <i class="fa fa-user-circle" style="font-size:150px;color:#ccc;"></i>
      <?php endif; ?>
      <h4><?php echo htmlspecialchars($profile['ArtistName'] ?? $profile['FullName']); ?></h4>
      <p><?php echo htmlspecialchars($profile['ArtistEmail'] ?? $profile['Email']); ?></p>
      <p><?php echo htmlspecialchars($profile['ArtistMobile'] ?? $profile['MobileNumber']); ?></p>
      <hr>
      <p><strong>Username:</strong> <?php echo htmlspecialchars($profile['UserName']); ?></p>
      <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($profile['Role'])); ?></p>
    </div>
  </div>
  <div class="col-md-8">
    <form method="post" action="">
      <div class="stat-box">
        <h4>Edit Artist Profile</h4>
        <div class="form-group">
          <label>Education</label>
          <textarea class="form-control" name="education" rows="3"><?php echo htmlspecialchars($profile['Education'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
          <label>Awards / Accolades</label>
          <textarea class="form-control" name="award" rows="3"><?php echo htmlspecialchars($profile['Award'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Update Profile</button>
      </div>
    </form>
  </div>
</div>

<?php include('includes/footer.php'); ?>