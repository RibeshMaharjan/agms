-- Migration: Add AI-generated image detection column to tblartproduct
-- Run this against the agms database:
--   mysql -u root agms < database/migration_add_ai_column.sql

ALTER TABLE tblartproduct 
ADD COLUMN IsAIGenerated TINYINT(1) DEFAULT NULL 
COMMENT '0=Human, 1=AI-generated, NULL=Not checked';
