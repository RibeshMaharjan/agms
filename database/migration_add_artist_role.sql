-- Migration: Add artist role support to tblusers
-- Links tblusers to tblartist via ArtistProfileID foreign key

ALTER TABLE tblusers
  ADD COLUMN `Role` ENUM('user','artist','admin') DEFAULT 'user' AFTER `Address`,
  ADD COLUMN `ArtistProfileID` int DEFAULT NULL AFTER `Role`;

-- Optional: Add foreign key constraint (uncomment if desired)
-- ALTER TABLE tblusers
--   ADD FOREIGN KEY (`ArtistProfileID`) REFERENCES `tblartist`(`ID`);
