#
# Table structure for table 'tx_ligestmembrelabo_MembreDuLabo'
#
CREATE TABLE tx_ligestmembrelabo_MembreDuLabo (
	afficher_theses_hdr int(11) DEFAULT '0' NOT NULL
	afficher_theses_dirigees_hdr int(11) DEFAULT '0' NOT NULL
);



#
# Table structure for table 'tx_ligesttheses_TheseHDR'
#
CREATE TABLE tx_ligesttheses_TheseHDR (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	Titre varchar(255) DEFAULT '' NOT NULL,
	Description text NOT NULL,
	DateDebut date DEFAULT '0000-00-00' NOT NULL,
	DateFin date DEFAULT '0000-00-00' NOT NULL,
	Jury varchar(255) DEFAULT '' NOT NULL,
	Resultat char(1) DEFAULT '' NOT NULL,
	Type char(1) DEFAULT '' NOT NULL,
	idMembreLabo int(11) DEFAULT '0' NOT NULL,
	Afficher_Dirige int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_ligesttheses_Dirige'
#
CREATE TABLE tx_ligesttheses_Dirige (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	idMembreLabo int(11) DEFAULT '0' NOT NULL,
	idTheseHDR int(11) DEFAULT '0' NOT NULL,
	CoefficientDEncadrement varchar(255) DEFAULT '' NOT NULL,
	EstDirecteur char(1) DEFAULT '' NOT NULL,
	DateDebut date DEFAULT '0000-00-00' NOT NULL,
	DateFin date DEFAULT '0000-00-00' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);