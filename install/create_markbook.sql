-- Three predefined marktypes: level, sum and average which are
-- secondary calculated values based on the values of marks pointed to by
-- the mids in mid_list.  Or a compound marktype which associates several
-- marks in one column All other marktypes (indicated by marktype=score)
-- are user-defined and are given their definition in the table:markdef
-- and their values in the table:score.
CREATE TABLE mark ( 
	id				int unsigned not null auto_increment, 
	entrydate		date not null default '0000-00-00', 
	marktype		enum('score', 'sum', 'average', 'level', 
							'dif', 'compound', 'report', 'hw') not null, 
	topic			varchar(60) not null default '', 
	comment			text not null default '', 
	def_name		varchar(20) not null default '', 
	midlist			text, 
	levelling_name	varchar(20) not null default '', 
	total			smallint unsigned not null default '0', 
	assessment		enum('no','yes','other') not null, 
	author			varchar(14) not null default '', 
	component_id	varchar(10) not null default '', 
	primary key		(id) 
) ENGINE=MYISAM;


-- 	marktype is the definition of a mark. The values in table:score
-- 	can be any one of scoretype=(comment, value, grade, percentage). A
-- 	scoretype=percentage indicates a raw numerical score.value and an
-- 	score.outoftotal are to be used to generate a rounded percentage
-- 	in score.grade.
CREATE TABLE markdef (
       name				varchar(20) not null default '',
       scoretype		enum('value','grade','percentage','comment') not null default 'value',
	   outoftotal		smallint unsigned not null default '0',
	   grading_name	    varchar(20) not null default '',
	   comment			text,
	   course_id		varchar(10) not null default '',
	   subject_id		varchar(10) not null default '',
	   author			varchar(14) not null default '',
	   index			index_bid (subject_id),
       primary key	(course_id, name)
) ENGINE=MYISAM;


CREATE TABLE homework (
	   id				int unsigned not null auto_increment, 
       title			varchar(120) not null default '',
	   description		text not null default '',
	   refs				text not null default '',
	   def_name			varchar(20) not null default '', 
	   course_id		varchar(10) not null default '',
	   subject_id		varchar(10) not null default '',
	   component_id		varchar(10) not null default '',
	   stage			char(3) not null default '',
	   author			varchar(14) not null default '',
	   index			index_crid (course_id),
       primary key	(id)
) ENGINE=MYISAM;


CREATE TABLE score (
       mark_id		int unsigned not null default '0',
       student_id	int unsigned not null default '0',	 		
       grade		smallint default null,
       value		float default null,
       comment		text,
       outoftotal	smallint unsigned not null default '0',
	   extra	   	enum('0','1','2','3','4') not null default '0',
       primary key	(mark_id, student_id)
) ENGINE=MYISAM;




-- 	Provides user-defined level boundaries for the raw values stored in table:score
CREATE TABLE levelling (
       name				varchar(20) not null default '',
       levels			varchar(200) not null default '',
       grading_name		varchar(20) not null default '',
       comment			text,
	   course_id		varchar(10) not null default '',
	   subject_id		varchar(10) not null default '',
	   author			varchar(14) not null default '',
       primary key		(course_id, name)
) ENGINE=MYISAM;



-- 	Provides user-defined grade names for the raw grades stored in table:score
CREATE TABLE grading (
       name				varchar(20) not null default '',
       grades			text not null default '',
       comment			text not null default'',
       author			varchar(14) not null default '',
       primary key		(name)
) ENGINE=MYISAM;



-- 	Lookup index providing marks for classes
CREATE TABLE midcid (
		 class_id		int unsigned not null default 0,
		 mark_id		int unsigned not null default 0,
		 primary key 	(class_id, mark_id)
) ENGINE=MYISAM;