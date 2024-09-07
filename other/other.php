<ul class="row list-unstyled">
                  <?php
                  $result = mysqli_query($con, "SELECT tags FROM tblartproduct WHERE id = $pid");
                  $row = mysqli_fetch_assoc($result);
                  $tags = json_decode($row['tags'], true);
                  
                  // Build the tag conditions for the SQL query
                  $tagConditions = [];
                  foreach ($tags as $tag) {
                      $escapedTag = mysqli_real_escape_string($con, $tag);
                      $tagConditions[] = "tags LIKE '%$escapedTag%'";
                  }
                  $sql = "SELECT tblarttype.ID AS atid, tblarttype.ArtType AS typename, tblartproduct.ID AS apid, tblartproduct.Title, tblartproduct.Image, tblartproduct.ArtType
                     FROM tblartproduct
                     JOIN tblarttype ON tblarttype.ID = tblartproduct.ArtType and tblartproduct.id != $pid
                     WHERE " . implode(" OR ", $tagConditions) . " ORDER BY tblartproduct.CreationDate  DESC LIMIT 4 ";
                  $ret = mysqli_query($con, $sql);
                  $cnt=1;
                  while ($row=mysqli_fetch_array($ret)) {
                     ?>
                  <li class="col-3">
                     
                   
                     <div>
                        <img src="admin/images/<?php echo $row['Image'];?>" style="width: 100%; height: 300px; object-fit: cover;" alt=""/>
                        <div class="banner-right-icon">
                           <h4 class="pt-3"><?php echo $row['typename'];?></h4>
                        </div>
                        <div class="outs_more-buttn">
                           <a href="art-enquiry.php?eid=<?php echo $row['apid'];?>">Enquiry</a>
                           <a href="single-product.php?pid=<?php echo $row['apid'];?>">View</a>
                        </div>
                     </div>
                  </li><?php }?>
                
               </ul>