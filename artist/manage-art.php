<?php
include('includes/header.php');

// Get artist's profile ID
$profile_query = mysqli_query($con, "SELECT ArtistProfileID FROM tblusers WHERE ID='$artist_id'");
$profile = mysqli_fetch_array($profile_query);
$artist_profile_id = $profile['ArtistProfileID'] ?? 0;

$art_products = [];
if ($artist_profile_id) {
    $query = mysqli_query($con, "SELECT * FROM tblartproduct WHERE Artist='$artist_profile_id' ORDER BY CreationDate DESC");
    while ($row = mysqli_fetch_assoc($query)) {
        $art_products[] = $row;
    }
}

// Handle delete
if (isset($_GET['delid'])) {
    $delid = intval($_GET['delid']);
    mysqli_query($con, "DELETE FROM tblartproduct WHERE ID='$delid' AND Artist='$artist_profile_id'");
    echo "<script>window.location.href='manage-art.php';</script>";
}
?>

<h3><i class="fa fa-list"></i> My Art</h3>
<hr>

<?php if (empty($art_products)): ?>
    <div class="stat-box text-center">
        <p>No art uploaded yet.</p>
        <a href="add-art.php" class="btn btn-primary">Upload Your First Art</a>
    </div>
<?php else: ?>
<div class="stat-box">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Title</th>
                <th>Price</th>
                <th>Date</th>
                <th>AI Flag</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($art_products as $i => $art): ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><img src="../images/<?php echo htmlspecialchars($art['Image']); ?>" width="60" height="60" style="object-fit:cover;"></td>
                <td><?php echo htmlspecialchars($art['Title']); ?></td>
                <td>Rs. <?php echo number_format($art['SellingPricing']); ?></td>
                <td><?php echo date('M d, Y', strtotime($art['CreationDate'])); ?></td>
                <td>
                    <?php if($art['IsAIGenerated'] == 1): ?>
                        <span class="label label-danger">AI</span>
                    <?php else: ?>
                        <span class="label label-success">Human</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit-art.php?id=<?php echo $art['ID']; ?>" class="btn btn-xs btn-default"><i class="fa fa-edit"></i> Edit</a>
                    <a href="manage-art.php?delid=<?php echo $art['ID']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this art?');"><i class="fa fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include('includes/footer.php'); ?>