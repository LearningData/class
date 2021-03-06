CREATE TABLE student (
	id				int unsigned not null auto_increment, 
	surname 		varchar(120) not null default '', 
	forename		varchar(120) not null default '',
	middlenames		varchar(120) not null default '',
	surnamefirst	enum('Y','N') not null default 'N',
	middlenamelast	enum('Y','N') not null default 'N',
	preferredforename varchar(30) not null default '',
	formersurname	varchar(120) not null default '',
	gender 			enum('','M','F') not null default '', 
	dob 			date not null default '0000-00-00', 
	form_id 		varchar(10) not null default '',
	yeargroup_id 	smallint, 
	index 			index_name (surname(5),forename(5)),
	index 			index_forename (forename(5)),
	primary key (id)
) ENGINE=MYISAM;

CREATE TABLE yeargroup (
		id				smallint not null default '0',
		name			varchar(20) not null default '',
		sequence	   	smallint unsigned not null default '0',
		section_id		smallint unsigned not null default '0',
		primary key (id)
) ENGINE=MYISAM;

CREATE TABLE subject (
		id				varchar(10) not null default '',
		name			varchar(120) not null default '',
		primary key (id)
) ENGINE=MYISAM;

CREATE TABLE course (
	id				varchar(10) not null default '', 
	name 			varchar(40) not null default '',
	sequence	   	smallint unsigned not null default '0',
    generate		enum('', 'forms','sets','none') not null default '',
	naming			varchar(40) not null default '',
	many			smallint unsigned not null default '4',
	nextcourse_id	varchar(10) not null default '',
	endmonth		enum('','1','2','3','4','5','6','7','8','9','10','11','12') not null default '',
	primary key (id)
) ENGINE=MYISAM;

CREATE TABLE classes (
		course_id		varchar(10) not null default '',
	    subject_id		varchar(10) not null default '',
		stage			char(3) not null default '',
        generate		enum('', 'forms','sets','none') not null default '',
		naming			varchar(40) not null default '',
		many			smallint unsigned not null default 0,
		sp				smallint unsigned not null default 0,
		dp				smallint unsigned not null default 0,
		block			char(3) not null default '',
		description		text not null default '',
		formgroup		enum('N','Y') not null,
		primary 		key (course_id, subject_id, stage)
) ENGINE=MYISAM;

CREATE TABLE class (
	   	id				int unsigned not null auto_increment, 
       	name	    	varchar(20) not null default '',
       	detail	    	varchar(240) not null default '',
		subject_id		varchar(10) not null default '',
		course_id		varchar(10) not null default '',
		stage			char(3) not null default '',
		cohort_id 		int unsigned not null,
		index			index_bid (subject_id),
		index			index_crid (course_id),
		primary key  	(id)
) ENGINE=MYISAM;

CREATE TABLE cidsid (
		 class_id		int unsigned not null default 0,
		 student_id		int unsigned not null default 0,
		 primary key 	(class_id, student_id)
) ENGINE=MYISAM;

CREATE TABLE tidcid (
		 teacher_id		varchar(14) not null default '',
		 class_id		int unsigned not null default 0,
		 component_id  	varchar(10) not null default '',
		 primary key 	(teacher_id, class_id)
) ENGINE=MYISAM;

CREATE TABLE component (
		 id				varchar(10) not null default '',
		 course_id		varchar(10) not null default '',
		 subject_id		varchar(10) not null default '',
		 status			enum('N','V','U') not null default 'N',
		 sequence	   	smallint unsigned not null default '0',
		 weight			smallint unsigned not null default '1',
		 year			year not null default '0000',
		 primary key 	(id, course_id, subject_id, year)
) ENGINE=MYISAM;

CREATE TABLE  users (
  uid			int(10) unsigned auto_increment,
  username		varchar(14) not null default '', 
  passwd		char(32) binary not null default '',
  cookie		char(32) binary not null default '',
  session		char(32) binary not null default '',
  ip			varchar(15) binary not null default '', 
  forename		varchar(50) not null default '',
  surname		varchar(50) not null default '',
  title			varchar(20) not null default '',
  email			varchar(200) not null default '',
  emailuser		varchar(60) not null default '',
  emailpasswd	char(32) binary not null default '',
  epfusername	varchar(128) not null default '',
  language		varchar(10) not null default '',
  firstbookpref varchar(20),
  role			varchar(20),
  senrole		enum('0','1') not null,
  medrole		enum('0','1') not null,
  worklevel	   	enum('-1','0', '1', '2') not null default '0',
  nologin		tinyint(1) not null default '0',
  jobtitle		varchar(240) not null default '', 
  personalemail	varchar(200) not null default '',
  homephone		varchar(22) not null default '',
  mobilephone	varchar(22) not null default '',
  personalcode	varchar(120) not null default '',
  address_id	int unsigned not null default '0',
  dob			date not null default '0000-00-00',
  contractdate  date not null default '0000-00-00',
  education		varchar(240) not null default '', 
  education2	varchar(240) not null default '', 
  logcount		int(10) unsigned not null default '0',
  logtime		timestamp,
  index			index_name(username),
  primary key	(uid)
) ENGINE=MYISAM;

CREATE TABLE  history (
  uid			int(10) unsigned,
  page			varchar(60) not null default '', 
  time			timestamp
) ENGINE=MYISAM;

CREATE TABLE groups (
	gid 			int(10) unsigned auto_increment,
	subject_id		varchar(10) not null default '',
	course_id		varchar(10) not null default '',
	yeargroup_id	smallint,
	community_id	int(10) unsigned not null default '0',
    type			enum('a','p','b','s','u','c') not null default 'a',
	index			index_crid(course_id),
	index			index_bid(subject_id),
	index			index_yid(yeargroup_id),
  	primary key		(gid)
) ENGINE=MYISAM;

CREATE TABLE perms (
  uid 			int(10) not null default '0',
  gid 			int(10) not null default '0',
  r				set('0','1') not null default '0',
  w				set('0','1') not null default '0',
  x				set('0','1') not null default '0',
  e				set('0','1') not null default '0',
  primary key  	(uid, gid)
) ENGINE=MYISAM;

CREATE TABLE section (
	id				smallint unsigned not null auto_increment, 
	name			varchar(240) not null default '', 
	sequence	   	smallint unsigned not null default '0',
	address_id		int unsigned not null default '0',
	gid 			int(10) NOT NULL default '0',
	primary key		(id)
) ENGINE=MYISAM;

CREATE TABLE community (
	id			int unsigned not null auto_increment, 
	name		varchar(30) not null default '', 
    type		enum('','academic','family','form','year','tutor','alumni','enquired','applied','accepted','trip','reg','transport','extra','house','accomodation') not null default '',
	year		year not null default '0000',
	season		enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') not null default '',
	capacity	smallint unsigned not null default 0,
	count		smallint unsigned not null default 0,
    detail		varchar(240) not null default '',
	charge		decimal(10,2) unsigned not null default '0',
	chargetype	enum('0','1','2'),
	sessions	varchar(240) not null default '',
	startdate	date not null,
	enddate		date not null,
	index		indexcom (type,name),
	primary		key (id)
) ENGINE=MYISAM;

CREATE TABLE comidsid (
	community_id	int unsigned not null default '0',
	student_id		int unsigned not null default '0',
	joiningdate		date null,
	leavingdate 	date null,
	special			char(2) not null default '',
	primary key 	(community_id, student_id)
) ENGINE=MYISAM;

CREATE TABLE cohort (
	id				int unsigned not null auto_increment, 
	course_id	   	varchar(10) not null default '',
	stage			char(3) not null default '',
	year			year not null default '0000',
	season			enum('','S','W','M','1','2','3','4','5','6','7','8','9','a','b','c') not null default 'S',
	unique			indexcohort (course_id,stage,year,season),
	primary key (id)
) ENGINE=MYISAM;

CREATE TABLE cohidcomid (
	cohort_id		int unsigned not null default '0',
	community_id	int unsigned not null default '0',
	primary key 	(cohort_id, community_id)
) ENGINE=MYISAM;

CREATE TABLE categorydef (
	id				int unsigned not null auto_increment, 
	name			varchar(240) not null default '',
	type			char(3) not null default '',
	subtype			varchar(20) not null default '',
	rating			smallint not null default 0,
	rating_name		varchar(30) not null default '',
	comment			text not null default '',
	subject_id		varchar(10) not null default '',
	course_id		varchar(10) not null default '',
	stage			char(3) not null default '',
	section_id		smallint not null default 0,
	othertype		varchar(20) not null default '',
   	primary key		(id)
) ENGINE=MYISAM;

CREATE TABLE rating (
	name			varchar(30) not null default '',
	descriptor		varchar(30) not null default '',
	longdescriptor	varchar(250) not null default '',
	value			smallint not null default 0,
   	primary key		(name, value)
) ENGINE=MYISAM;


CREATE TABLE file (
 id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 owner enum('s','g','u','c') NOT NULL,
 owner_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 folder_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 title varchar(255) NOT NULL DEFAULT '',
 originalname varchar(255) NOT NULL DEFAULT '',
 description varchar(255) NOT NULL DEFAULT '',
 location varchar(255) NOT NULL DEFAULT '',
 access varchar(20) NOT NULL DEFAULT '',
 size int(11) NOT NULL DEFAULT '0',
 other_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY (id),
 KEY fileowner (owner,owner_id,folder_id)
 ) ENGINE=MYISAM;

CREATE TABLE file_folder (
 id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 owner enum('s','g','u') NOT NULL,
 owner_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 parent_folder_id int(10) UNSIGNED NOT NULL DEFAULT '0',
 name varchar(128) NOT NULL DEFAULT '',
 access varchar(20) NOT NULL DEFAULT '',
 PRIMARY KEY (id),
 KEY folderowner (owner,owner_id),
 KEY name (name)
) ENGINE=MYISAM;