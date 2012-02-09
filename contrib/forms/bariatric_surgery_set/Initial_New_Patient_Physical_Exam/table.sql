CREATE TABLE IF NOT EXISTS `form_Initial_New_Patient_Physical_Exam` (
id bigint(20) NOT NULL auto_increment,
date datetime default NULL,
pid bigint(20) default NULL,
user varchar(255) default NULL,
groupname varchar(255) default NULL,
authorized tinyint(4) default NULL,
activity tinyint(4) default NULL,
sweeter TEXT,
bloater TEXT,
grazer TEXT,
general TEXT,
head TEXT,
eyes TEXT,
ears TEXT,
nose TEXT,
throat TEXT,
oral_cavity TEXT,
dentition TEXT,
neck TEXT,
heart TEXT,
lung TEXT,
chest TEXT,
breast TEXT,
male TEXT,
female TEXT,
note TEXT,
abdomen TEXT,
scar TEXT,
umbilius TEXT,
groins TEXT,
extremities TEXT,
peripheral_pulses TEXT,
right_peripheral_pulses TEXT,
left_peripheral_pulses TEXT,
neurological TEXT,
right_neurological TEXT,
left_neurological TEXT,
rectum TEXT,
pelvic TEXT,
assessment TEXT,
note2 TEXT,
recommendations TEXT,
note3 TEXT,

PRIMARY KEY (id)
) TYPE=MyISAM;
