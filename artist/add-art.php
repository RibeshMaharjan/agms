<?php
include('includes/header.php');

// Get artist's profile ID
$profile_query = mysqli_query($con, "SELECT ArtistProfileID FROM tblusers WHERE ID='$artist_id'");
$profile = mysqli_fetch_array($profile_query);
$artist_profile_id = $profile['ArtistProfileID'] ?? 0;

if (!$artist_profile_id) {
    echo "<div class='alert alert-danger'>Artist profile not found. Please contact admin.</div>";
    include('includes/footer.php');
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
    $refno=mt_rand(100000000, 999999999);

    if($sprice < 1) {
        $error = "Art price cannot be less than 1.";
    } else {
        $pic=$_FILES["images"]["name"];
        $extension = substr($pic,strlen($pic)-4,strlen($pic));
        $allowed_extensions = array(".jpg","jpeg",".png",".gif");

        if(!in_array($extension,$allowed_extensions)) {
            $error = "Featured image has Invalid format. Only jpg / jpeg/ png /gif format allowed.";
        } else {
            include_once('../includes/cnn_helper.php');
            $proimg=md5($pic).time().$extension;
            move_uploaded_file($_FILES["images"]["tmp_name"],"../images/".$proimg);

            $cnnResult = detectAIGeneratedImage("../images/" . $proimg);
            $isAIFlagged = 0;
            if (isset($cnnResult['error'])) {
                error_log("AI detection error for image $proimg: " . $cnnResult['error']);
            } else {
                $isAIFlagged = $cnnResult['is_ai_generated'] ? 1 : 0;
            }

            $query=mysqli_query($con, "INSERT INTO tblartproduct(Title,Dimension,Orientation,Size,Artist,ArtType,ArtMedium,SellingPricing,Description,Image,RefNum,tags,IsAIGenerated) VALUES('$title','$dimension','$orientation','$size','$artist_profile_id','$arttype','$artmed','$sprice','$description','$proimg','$refno','$tagList','$isAIFlagged')");

            if ($query) {
                if ($isAIFlagged) {
                    $success = "Art uploaded but flagged as potentially AI-generated (" . round(($cnnResult['confidence'] ?? 0) * 100, 1) . "% confidence).";
                } else {
                    $success = "Art product has been submitted successfully.";
                }
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<h3><i class="fa fa-upload"></i> Add New Art</h3>
<hr>

<?php if(!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if(!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data" class="form-horizontal">
  <div class="row">
    <div class="col-md-6">
      <div class="stat-box">
        <h4>Art Details</h4>
        <div class="form-group">
          <label>Title</label>
          <input class="form-control" name="title" type="text" required>
        </div>
        <div class="form-group">
          <label>Featured Image</label>
          <input type="file" class="form-control" name="images" accept="image/*" required>
        </div>
        <div class="form-group">
          <label>Dimension</label>
          <input class="form-control" name="dimension" type="text" required>
        </div>
        <div class="form-group">
          <label>Orientation</label>
          <select class="form-control" name="orientation" required>
            <option value="">Choose orientation</option>
            <option value="Potrait">Potrait</option>
            <option value="Landscape">Landscape</option>
          </select>
        </div>
        <div class="form-group">
          <label>Size</label>
          <select class="form-control" name="size" required>
            <option value="">Choose Size</option>
            <option value="Small">Small</option>
            <option value="Medium">Medium</option>
            <option value="Large">Large</option>
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stat-box">
        <h4>More Details</h4>
        <div class="form-group">
          <label>Art Type</label>
          <select class="form-control" name="arttype" required>
            <option value="">Choose Art Type</option>
            <?php
            $query=mysqli_query($con,"select * from tblarttype");
            while($row=mysqli_fetch_array($query)) {
                echo "<option value='{$row['ID']}'>{$row['ArtType']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Art Medium</label>
          <select class="form-control" name="artmed" required>
            <option value="">Choose Art Medium</option>
            <?php
            $query=mysqli_query($con,"select * from tblartmedium");
            while($row=mysqli_fetch_array($query)) {
                echo "<option value='{$row['ID']}'>{$row['ArtMedium']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label>Price (Rs.)</label>
          <input class="form-control" name="sprice" type="number" min="1" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea class="form-control" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
          <label>Tags (comma separated)</label>
          <input class="form-control" name="tagList" type="text" placeholder='["tag1","tag2"]'>
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-block">Upload Art</button>
      </div>
    </div>
  </div>
</form>

<?php include('includes/footer.php'); ?>