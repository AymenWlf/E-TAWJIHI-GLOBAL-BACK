-- Fix corrupted data in user_profiles table
UPDATE user_profiles SET annual_budget = NULL WHERE annual_budget = '' OR annual_budget = '0';

-- Fix corrupted data in qualifications table  
UPDATE qualifications SET score = NULL WHERE score = '' OR score = '0';

-- Add detailed_scores column to qualifications table if it doesn't exist
ALTER TABLE qualifications ADD COLUMN detailed_scores JSON DEFAULT NULL;
