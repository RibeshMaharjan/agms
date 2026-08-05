<?php
include('includes/header.php');

// Get artist's profile ID
$profile_query = mysqli_query($con, "SELECT ArtistProfileID FROM tblusers WHERE ID='$artist_id'");
$profile = mysqli_fetch_array($profile_query);
$artist_profile_id = $profile['ArtistProfileID'] ?? 0;

if (!$artist_profile_id || !isset($_GET['id'])) {
    header('location: manage-art.php');
    exit();
}

$art_id = intval($_GET['id']);

// Verify ownership
$art_query = mysqli_query($con, "SELECT * FROM tblartproduct WHERE ID='$art_id' AND Artist='$artist_profile_id'");
$art = mysqli_fetch_array($art_query);

if (!$art) {
    header('location: manage-art.php');
    exit();
}

$error = '';
$success = '';

if(isset($_POST['submit']))
{
    $title=mysqli_real_escape_string($con, $_POST['title']);
    $dimension=mysqli_real_escape_string($con, $_POST['dimension']);
    $orientation=mysqli_real_escape_string($con, $_POST['orientation']);
    $size=mysqli_real_escape_string($con, $_POST['size']);
    $arttype=mysqli_real_escape_string($con, $_POST['arttype']);
    $artmed=mysqli_real_escape_string($con, $_POST['artmed']);
    $sprice=mysqli_real_escape_string($con, $_POST['sprice']);
    $description=mysqli_real_escape_string($con, $_POST['description']);
    $tagList=mysqli_real_escape_string($con, $_POST['tagList']);

    if($sprice < 1) {
        $error = "Art price cannot be less than 1.";
    } else {
        $update_sql = "UPDATE tblartproduct SET Title='$title',Dimension='$dimension',Orientation='$orientation',Size='$size',ArtType='$arttype',ArtMedium='$artmed',SellingPricing='$sprice',Description='$description',tags='$tagList'";

        // Handle image update if new file uploaded
        if ($_FILES["images"]["name"]) {
            include_once('../includes/cnn_helper.php');
            $pic=$_FILES["images"]["name"];
            $extension = substr($pic,strlen($pic)-4,strlen($pic));
            $allowed_extensions = array(".jpg","jpeg",".png",".gif");

            if(!in_array($extension,$allowed_extensions)) {
                $error = "Image format invalid.";
            } else {
                $proimg=md5($pic).time().$extension;
                move_uploaded_file($_FILES["images"]["tmp_name"],"../images/".$proimg);

                $cnnResult = detectAIGeneratedImage("../images/" . $proimg);
                $isAIFlagged = 0;
                if (isset($cnnResult['error'])) {
                    error_log("AI detection error: " . $cnnResult['error']);
                } else {
                    $isAIFlagged = $cnnResult['is_ai_generated'] ? 1 : 0;
                }
                $update_sql .= ",Image='$proimg',IsAIGenerated='$isAIFlagged'";
            }
        }

        if (empty($error)) {
            $update_sql .= " WHERE ID='$art_id' AND Artist='$artist_profile_id'";
            $query=mysqli_query($con, $update_sql);
            if ($query) {
                $success = "Art product updated successfully.";
                // Refresh art data
                $art_query = mysqli_query($con, "SELECT * FROM tblartproduct WHERE ID='$art_id' AND Artist='$artist_profile_id'");
                $art = mysqli_fetch_array($art_query);
            } else {
                $error = "Something went wrong.";
            }
        }
    }
}
?>

<h3><i class="fa fa-edit"></i> Edit Art</h3>
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
      <p>Current Image</p>
      <img src="../images/<?php echo htmlspecialchars($art['Image']); ?>" class="img-responsive" style="max-height:300px; margin:0 auto;">
      <?php if($art['IsAIGenerated'] == 1): ?>
        <p><span class="label label-danger">Flagged as AI-generated</span></p>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-md-8">
    <form method="post" action="" enctype="multipart/form-data">
      <div class="stat-box">
        <h4>Edit Art Details</h4>
        <div class="form-group">
          <label>Title</label>
          <input class="form-control" name="title" type="text" value="<?php echo htmlspecialchars($art['Title']); ?>" required>
        </div>
        <div class="form-group">
          <label>New Image (leave blank to keep current)</label>
          <input type="file" class="form-control" name="images" accept="image/*">
        </div>
        <div class="form-group">
          <label>Dimension</label>
          <input class="form-control" name="dimension" type="text" value="<?php echo htmlspecialchars($art['Dimension']); ?>" required>
        </div>
        <div class="form-group">
          <label>Orientation</label>
          <select class="form-control" name="orientation" required>
            <option value="Potrait" <?php if($art['Orientation']=='Potrait') echo 'selected'; ?>>Potrait</option>
            <option value="Landscape" <?php if($art['Orientation']=='Landscape') echo 'selected'; ?>>Landscape</option>
          </select>
        </div>
        <div class="form-group">
          <label>Size</label>
          <select class="form-control" name="size" required>
            <option value="Small" <?php if($art['Size']=='Small') echo 'selected'; ?>>Small</option>
            <option value="Medium" <?php if($art['Size']=='Medium') echo 'selected'; ?>>Medium</option>
            <option value="Large" <?php if($art['Size']=='Large') echo 'selected'; ?>>Large</option>
          </select>
        </div>
        <div class="form-group">
          <label>Art Type</label>
          <select class="form-control" name="arttype" required>
            <?php
            $query=mysqli_query($con,"select * from tblarttype");
            while($row=mysqli_fetch_array($query)) {
                $selected = ($row['ID'] == $art['ArtType']) ? 'selected' : '';
                echo "<option value='{$row['ID']}' $selected>{$row['ArtType']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Art Medium</label>
          <select class="form-control" name="artmed" required>
            <?php
            $query=mysqli_query($con,"select * from tblartmedium");
            while($row=mysqli_fetch_array($query)) {
                $selected = ($row['ID'] == $art['ArtMedium']) ? 'selected' : '';
                echo "<option value='{$row['ID']}' $selected>{$row['ArtMedium']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Price (Rs.)</label>
          <input class="form-control" name="sprice" type="number" min="1" value="<?php echo $art['SellingPricing']; ?>" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" name="description" rows="3" required><?php echo htmlspecialchars($art['Description']); ?></textarea>
        </div>
        <div class="form-group">
          <label>Tags</label>
          <input class="form-control" name="tagList" type="text" value="<?php echo htmlspecialchars($art['tags']); ?>">
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-block">Update Art</button>
        <a href="manage-art.php" class="btn btn-default btn-block" style="margin-top:5px;">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php include('includes/footer.php'); ?>