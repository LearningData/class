ALTER TABLE comidsid DROP PRIMARY KEY;
ALTER TABLE comidsid ADD memberid int(10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY id(memberid);
ALTER TABLE comidsid ADD index indexmember (community_id, student_id);

ALTER TABLE mark ADD weblog_post_id INT;
