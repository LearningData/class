<?php
/**								create_transportbook.php	
 */

/**
 * Will have a corresponding community with name=bus_id,
 * type=transport, year=0000, capacity is set and detail will be the
 * bus_name.
 */
mysql_query("
CREATE TABLE transport_bus (
	id				smallint unsigned auto_increment,
	name			varchar(30) not null default '',
	detail			text not null default '',
	route_id		smallint unsigned not null default 0,
	direction		enum('I','O') not null,
	day				enum('1','2','3','4','5','6','7','X') not null default 'X',
	departuretime	time,
	teacher_id		varchar(14) not null default '',
	primary key		(id)
);");

mysql_query("
CREATE TABLE transport_route (
	id				smallint unsigned auto_increment,
	name			varchar(30) not null default '',
	detail			text not null default '',
	primary key  	(id)
);");

mysql_query("
CREATE TABLE transport_rtidstid (
	route_id		smallint unsigned not null default '0',
	stop_id			smallint unsigned not null default '0',
	sequence		smallint unsigned not null default '0',
	traveltime		time,
	primary key		(route_id, stop_id)
);");

mysql_query("
CREATE TABLE transport_stop (
	id				smallint unsigned auto_increment, 
	lat				decimal(10,6) not null,
	lng				decimal(10,6) not null,
	name			varchar(30) not null default '', 
	detail			text not null default '',
	primary key		(id)
);");

mysql_query("
CREATE TABLE transport_attendance (
	student_id		int unsigned not null default '0',
	journey_id		int unsigned not null default '0',
	status			enum('a','p') not null default 'p',
	startdate		date not null default '0000-00-00',
	enddate			date not null default '0000-00-00',
	comment			text,
	primary key		(student_id,journey_id)
);");

/**
 * Made a journey to/from this stop using this transport.
 */
mysql_query("
CREATE TABLE transport_journey (
	id				int unsigned not null auto_increment,
	bus_id			smallint unsigned not null default '0',
	stop_id			smallint unsigned not null default '0',
	cost			decimal(10,2) unsigned not null default '0',
	unique			indexjourney (bus_id,stop_id),
	primary key		(id)
);");

?>