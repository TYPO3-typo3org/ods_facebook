#
# Table structure for table 'tx_odsfacebook_auth'
#
CREATE TABLE tx_odsfacebook_auth (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	api_key varchar(255) DEFAULT '' NOT NULL,
	client_id varchar(255) DEFAULT '' NOT NULL,
	client_secret varchar(255) DEFAULT '' NOT NULL,
	access_token varchar(255) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);