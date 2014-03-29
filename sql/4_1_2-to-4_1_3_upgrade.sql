--
--  Comment Meta Language Constructs:
--
--  #IfNotTable
--    argument: table_name
--    behavior: if the table_name does not exist,  the block will be executed

--  #IfTable
--    argument: table_name
--    behavior: if the table_name does exist, the block will be executed

--  #IfMissingColumn
--    arguments: table_name colname
--    behavior:  if the table exists but the column does not,  the block will be executed

--  #IfNotColumnType
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a column colname with a data type equal to value, then the block will be executed

--  #IfNotRow
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a row where colname = value, the block will be executed.

--  #IfNotRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfNotRow3D
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfNotRow4D
--    arguments: table_name colname value colname2 value2 colname3 value3 colname4 value4
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3 AND colname4 = value4, the block will be executed.

--  #IfNotRow2Dx2
--    desc:      This is a very specialized function to allow adding items to the list_options table to avoid both redundant option_id and title in each element.
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  The block will be executed if both statements below are true:
--               1) The table table_name does not have a row where colname = value AND colname2 = value2.
--               2) The table table_name does not have a row where colname = value AND colname3 = value3.

--  #IfRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfIndex
--    desc:      This function is most often used for dropping of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the table and index exist the relevant statements are executed, otherwise not.

--  #IfNotIndex
--    desc:      This function will allow adding of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the index does not exist, it will be created

--  #EndIf
--    all blocks are terminated with a #EndIf statement.

#IfNotRow4D supported_external_dataloads load_type ICD9 load_source CMS load_release_date 2013-10-01 load_filename cmsv31-master-descriptions.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD9', 'CMS', '2013-10-01', 'cmsv31-master-descriptions.zip', 'fe0d7f9a5338f5ff187683b4737ad2b7');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename 2013_PCS_long_and_abbreviated_titles.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', '2013_PCS_long_and_abbreviated_titles.zip', '04458ed0631c2c122624ee0a4ca1c475');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename 2013-DiagnosisGEMs.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', '2013-DiagnosisGEMs.zip', '773aac2a675d6aefd1d7dd149883be51');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename ICD10CMOrderFiles_2013.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', 'ICD10CMOrderFiles_2013.zip', '1c175a858f833485ef8f9d3e66b4d8bd');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename ProcedureGEMs_2013.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', 'ProcedureGEMs_2013.zip', '92aa7640e5ce29b9629728f7d4fc81db');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename 2013-ReimbursementMapping_dx.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', '2013-ReimbursementMapping_dx.zip', '0d5d36e3f4519bbba08a9508576787fb');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2012-10-01 load_filename ReimbursementMapping_pr_2013.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2012-10-01', 'ReimbursementMapping_pr_2013.zip', '4c3920fedbcd9f6af54a1dc9069a11ca');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename 2014-PCS-long-and-abbreviated-titles.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', '2014-PCS-long-and-abbreviated-titles.zip', '2d03514a0c66d92cf022a0bc28c83d38');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename DiagnosisGEMs-2014.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', 'DiagnosisGEMs-2014.zip', '3ed7b7c5a11c766102b12d97d777a11b');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename 2014-ICD10-Code-Descriptions.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', '2014-ICD10-Code-Descriptions.zip', '5458b95f6f37228b5cdfa03aefc6c8bb');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename ProcedureGEMs-2014.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', 'ProcedureGEMs-2014.zip', 'be46de29f4f40f97315d04821273acf9');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename 2014-Reimbursement-Mappings-DX.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', '2014-Reimbursement-Mappings-DX.zip', '614b3957304208e3ef7d3ba8b3618888');
#EndIf

#IfNotRow4D supported_external_dataloads load_type ICD10 load_source CMS load_release_date 2013-10-01 load_filename 2014-Reimbursement-Mappings-PR.zip
INSERT INTO `supported_external_dataloads` (`load_type`, `load_source`, `load_release_date`, `load_filename`, `load_checksum`) VALUES ('ICD10', 'CMS', '2013-10-01', '2014-Reimbursement-Mappings-PR.zip', 'f306a0e8c9edb34d28fd6ce8af82b646');
#EndIf

#IfMissingColumn patient_data email_direct
ALTER TABLE `patient_data` ADD COLUMN `email_direct` varchar(255) NOT NULL default '';
INSERT INTO `layout_options` (`form_id`, `field_id`, `group_name`, `title`, `seq`, `data_type`, `uor`, `fld_length`, `max_length`, `list_id`, `titlecols`, `datacols`, `default_value`, `edit_options`, `description`, `fld_rows`) VALUES('DEM', 'email_direct', '2Contact', 'Trusted Email', 14, 2, 1, 30, 95, '', 1, 1, '', '', 'Trusted (Direct) Email Address', 0);
#EndIf

#IfMissingColumn users email_direct
ALTER TABLE `users` ADD COLUMN `email_direct` varchar(255) NOT NULL default '';
#EndIf

#IfNotTable erx_ttl_touch
CREATE TABLE `erx_ttl_touch` (
  `patient_id` BIGINT(20) UNSIGNED NOT NULL COMMENT 'Patient record Id', 
  `process` ENUM('allergies','medications') NOT NULL COMMENT 'NewCrop eRx SOAP process',
  `updated` DATETIME NOT NULL COMMENT 'Date and time of last process update for patient', 
  PRIMARY KEY (`patient_id`, `process`) ) 
ENGINE = InnoDB COMMENT = 'Store records last update per patient data process';
#EndIf

#IfMissingColumn form_misc_billing_options box_14_date_qual
ALTER TABLE `form_misc_billing_options` 
ADD COLUMN `box_14_date_qual` CHAR(3) NULL DEFAULT NULL;
#EndIf

#IfMissingColumn form_misc_billing_options box_15_date_qual
ALTER TABLE `form_misc_billing_options` 
ADD COLUMN `box_15_date_qual` CHAR(3) NULL DEFAULT NULL;
#EndIf

#IfNotTable esign_signatures
CREATE TABLE `esign_signatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL COMMENT 'Table row ID for signature',
  `table` varchar(255) NOT NULL COMMENT 'table name for the signature',
  `uid` int(11) NOT NULL COMMENT 'user id for the signing user',
  `datetime` datetime NOT NULL COMMENT 'datetime of the signature action',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'sig, lock or amendment',
  `amendment` text COMMENT 'amendment text, if any',
  `hash` varchar(255) NOT NULL COMMENT 'hash of signed data',
  `signature_hash` varchar(255) NOT NULL COMMENT 'hash of signature itself',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `table` (`table`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;
#EndIf


#IfNotTable modules
CREATE TABLE `modules` (
  `mod_id` INT(11) NOT NULL AUTO_INCREMENT,
  `mod_name` VARCHAR(64) NOT NULL DEFAULT '0',
  `mod_directory` VARCHAR(64) NOT NULL DEFAULT '',
  `mod_parent` VARCHAR(64) NOT NULL DEFAULT '',
  `mod_type` VARCHAR(64) NOT NULL DEFAULT '',
  `mod_active` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `mod_ui_name` VARCHAR(20) NOT NULL DEFAULT '''',
  `mod_relative_link` VARCHAR(64) NOT NULL DEFAULT '',
  `mod_ui_order` TINYINT(3) NOT NULL DEFAULT '0',
  `mod_ui_active` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `mod_description` VARCHAR(255) NOT NULL DEFAULT '',
  `mod_nick_name` VARCHAR(25) NOT NULL DEFAULT '',
  `mod_enc_menu` VARCHAR(10) NOT NULL DEFAULT 'no',
  `permissions_item_table` CHAR(100) DEFAULT NULL,
  `directory` VARCHAR(255) NOT NULL,
  `date` DATETIME NOT NULL,
  `sql_run` TINYINT(4) DEFAULT '0',
  `type` TINYINT(4) DEFAULT '0',
  PRIMARY KEY (`mod_id`,`mod_directory`)
) ENGINE=InnoDB;
#EndIf

#IfNotTable module_acl_group_settings
CREATE TABLE `module_acl_group_settings` (
  `module_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `allowed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`module_id`,`group_id`,`section_id`)
) ENGINE=InnoDB;
#EndIf

#IfNotTable module_acl_sections
CREATE TABLE `module_acl_sections` (
  `section_id` int(11) DEFAULT NULL,
  `section_name` varchar(255) DEFAULT NULL,
  `parent_section` int(11) DEFAULT NULL,
  `section_identifier` varchar(50) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL
) ENGINE=InnoDB;
#EndIf

#IfNotTable module_acl_user_settings
CREATE TABLE `module_acl_user_settings` (
  `module_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `allowed` int(1) DEFAULT NULL,
  PRIMARY KEY (`module_id`,`user_id`,`section_id`)
) ENGINE=InnoDB;
#EndIf

#IfNotTable module_configuration
CREATE TABLE `module_configuration` (
  `module_config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(10) unsigned NOT NULL,
  `field_name` varchar(45) NOT NULL,
  `field_value` varchar(255) NOT NULL,
  PRIMARY KEY (`module_config_id`)
) ENGINE=InnoDB;
#EndIf

#IfNotTable modules_hooks_settings
CREATE TABLE `modules_hooks_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_id` int(11) DEFAULT NULL,
  `enabled_hooks` varchar(255) DEFAULT NULL,
  `attached_to` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
#EndIf

#IfNotTable modules_settings
CREATE TABLE `modules_settings` (
  `mod_id` INT(11) DEFAULT NULL,
  `fld_type` SMALLINT(6) DEFAULT NULL COMMENT '1=>ACL,2=>preferences,3=>hooks',
  `obj_name` VARCHAR(255) DEFAULT NULL,
  `menu_name` VARCHAR(255) DEFAULT NULL,
  `path` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;
#EndIf

#IfNotTable ccda_components
CREATE TABLE ccda_components (
  ccda_components_id int(11) NOT NULL AUTO_INCREMENT,
  ccda_components_field varchar(100) DEFAULT NULL,
  ccda_components_name varchar(100) DEFAULT NULL,
  PRIMARY KEY (ccda_components_id)
);
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('1','progress_note','Progress Notes');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('2','consultation_note','Consultation Note');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('3','continuity_care_document','Continuity Care Document');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('4','diagnostic_image_reporting','Diagnostic Image Reporting');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('5','discharge_summary','Discharge Summary');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('6','history_physical_note','History and Physical Note');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('7','operative_note','Operative Note');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('8','procedure_note','Procedure Note');
insert into ccda_components (ccda_components_id, ccda_components_field, ccda_components_name) values('9','unstructured_document','Unstructured Document');
#EndIf

#IfNotTable ccda_sections
CREATE TABLE ccda_sections (
  ccda_sections_id int(11) NOT NULL AUTO_INCREMENT,
  ccda_components_id int(11) DEFAULT NULL,
  ccda_sections_field varchar(100) DEFAULT NULL,
  ccda_sections_name varchar(100) DEFAULT NULL,
  ccda_sections_req_mapping tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (ccda_sections_id)
);
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('1','1','assessment_plan','Assessment and Plan','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('2','2','assessment_plan','Assessment and Plan','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('3','2','history_of_present_illness','History of Present Illness','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('4','2','physical_exam','Physical Exam','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('5','2','reason_of_visit','Reason for Referral/Reason for Visit','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('6','3','allergies','Allergies','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('7','3','medications','Medications','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('8','3','problem_list','Problem List','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('9','3','procedures','Procedures','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('10','3','results','Results','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('11','4','report','Report','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('12','5','allergies','Allergies','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('13','5','hospital_course','Hospital Course','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('14','5','hospital_discharge_diagnosis','Hospital Discharge Diagnosis','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('15','5','hospital_discharge_medications','Hospital Discharge Medications','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('16','5','plan_of_care','Plan of Care','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('17','6','allergies','Allergies','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('19','6','chief_complaint','Chief Complaint / Reason for Visit','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('21','6','family_history','Family History','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('22','6','general_status','General Status','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('23','6','hpi_past_med','History of Past Illness (Past Medical History)','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('24','6','hpi','History of Present Illness','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('25','6','medications','Medications','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('26','6','physical_exam','Physical Exam','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('28','6','results','Results','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('29','6','review_of_systems','Review of Systems','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('30','6','social_history','Social History','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('31','6','vital_signs','Vital Signs','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('32','7','anesthesia','Anesthesia','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('33','7','complications','Complications','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('34','7','post_operative_diagnosis','Post Operative Diagnosis','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('35','7','pre_operative_diagnosis','Pre Operative Diagnosis','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('36','7','procedure_estimated_blood_loss','Procedure Estimated Blood Loss','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('37','7','procedure_findings','Procedure Findings','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('38','7','procedure_specimens_taken','Procedure Specimens Taken','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('39','7','procedure_description','Procedure Description','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('40','8','assessment_plan','Assessment and Plan','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('41','8','complications','Complications','1');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('42','8','postprocedure_diagnosis','Postprocedure Diagnosis','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('43','8','procedure_description','Procedure Description','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('44','8','procedure_indications','Procedure Indications','0');
insert into ccda_sections (ccda_sections_id, ccda_components_id, ccda_sections_field, ccda_sections_name, ccda_sections_req_mapping) values('45','9','unstructured_doc','Document','0');
#EndIf

#IfNotTable ccda_table_mapping
CREATE TABLE ccda_table_mapping (
  id int(11) NOT NULL AUTO_INCREMENT,
  ccda_component varchar(100) DEFAULT NULL,
  ccda_component_section varchar(100) DEFAULT NULL,
  form_dir varchar(100) DEFAULT NULL,
  form_type smallint(6) DEFAULT NULL,
  form_table varchar(100) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  deleted tinyint(4) NOT NULL DEFAULT '0',
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
#EndIf

#IfNotTable ccda_field_mapping
CREATE TABLE ccda_field_mapping (
  id int(11) NOT NULL AUTO_INCREMENT,
  table_id int(11) DEFAULT NULL,
  ccda_field varchar(100) DEFAULT NULL,
  PRIMARY KEY (id)
);
#EndIf

#IfNotTable ccda
CREATE TABLE ccda (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pid BIGINT(20) DEFAULT NULL,
  encounter BIGINT(20) DEFAULT NULL,
  ccda_data MEDIUMTEXT,
  time VARCHAR(50) DEFAULT NULL,
  status SMALLINT(6) DEFAULT NULL,
  updated_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_key (pid,encounter,time)
);
ALTER TABLE ccda ADD COLUMN user_id INT NULL;
#EndIf

#IfNotRow module_menu controller_name carecoordination
SET @id=0;
SELECT @id:=mod_id FROM modules WHERE mod_directory="Carecoordination";
INSERT INTO module_menu(menu_id,module_id,menu_name,parent_id,controller_name,ACTION,icon,STATUS,group_id,order_id,url) VALUES ( NULL,@id,'My Health','0','carecoordination','index','icon-myhealth','1','1','1','https://sso.myhealthaccessnetwork.healthcare.covisint.com/login.do?host=https://myhealthaccessnetwork.healthcare.covisint.com&CT_ORIG_URL=%2F&ct_orig_uri=%2F'); 
INSERT INTO module_menu(menu_id,module_id,menu_name,parent_id,controller_name,ACTION,icon,STATUS,group_id,order_id,url) VALUES ( NULL,@id,'Encounter Manager','0','carecoordination','index','icon-myhealth','1','1','2','https://testers3demo.zhopenemr.com/emr/interface/modules/zend_modules/public/encountermanager');
#EndIf

#IfMissingColumn module_configuration field_name
ALTER TABLE module_configuration ADD COLUMN field_name VARCHAR(100) NULL;
#EndIf

#IfMissingColumn module_configuration field_value
ALTER TABLE module_configuration ADD COLUMN field_value VARCHAR(100) NULL;
#EndIf

#IfMissingColumn module_acl_sections module_id
ALTER TABLE module_acl_sections ADD COLUMN module_id INT NULL;
#EndIf

#IfMissingColumn ccda couch_docid
ALTER TABLE ccda ADD COLUMN couch_docid VARCHAR(100) NULL;
#EndIf

#IfMissingColumn ccda couch_revid
ALTER TABLE ccda ADD COLUMN couch_revid VARCHAR(100) NULL;
#EndIf

#IfMissingColumn immunizations route
ALTER TABLE immunizations ADD COLUMN route VARCHAR(10) NULL;
#EndIf

#IfNotColumnType ccda user_id VARCHAR(50)
alter table ccda change user_id user_id varchar(50) null;
#EndIf

#IfNotRow list_options list_id religious_affiliation
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1001','Adventist');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1002','African Religions');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1003','Afro-Caribbean Religions');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1004','Agnosticism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1005','Anglican');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1006','Animism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1061','Assembly of God');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1007','Atheism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1008',"Babi & Baha\'I faiths");
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1009','Baptist');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1010','Bon');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1062','Brethren');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1011','Cao Dai');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1012','Celticism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1013','Christian (non-Catholic, non-specific)');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1063','Christian Scientist');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1064','Church of Christ');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1065','Church of God');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1014','Confucianism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1066','Congregational');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1015','Cyberculture Religions');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1067','Disciples of Christ');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1016','Divination');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1068','Eastern Orthodox');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1069','Episcopalian');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1070','Evangelical Covenant');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1017','Fourth Way');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1018','Free Daism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1071','Friends');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1072','Full Gospel');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1019','Gnosis');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1020','Hinduism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1021','Humanism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1022','Independent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1023','Islam');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1024','Jainism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1025',"Jehovah's Witnesses");
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1026','Judaism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1027','Latter Day Saints');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1028','Lutheran');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1029','Mahayana');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1030','Meditation');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1031','Messianic Judaism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1073','Methodist');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1032','Mitraism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1074','Native American');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1075','Nazarene');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1033','New Age');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1034','non-Roman Catholic');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1035','Occult');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1036','Orthodox');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1037','Paganism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1038','Pentecostal');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1076','Presbyterian');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1039','Process, The');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1077','Protestant');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1078','Protestant, No Denomination');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1079','Reformed');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1040','Reformed/Presbyterian');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1041','Roman Catholic Church');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1080','Salvation Army');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1042','Satanism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1043','Scientology');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1044','Shamanism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1045','Shiite (Islam)');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1046','Shinto');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1047','Sikism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1048','Spiritualism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1049','Sunni (Islam)');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1050','Taoism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1051','Theravada');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1081','Unitarian Universalist');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1052','Unitarian-Universalism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1082','United Church of Christ');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1053','Universal Life Church');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1054','Vajrayana (Tibetan)');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1055','Veda');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1056','Voodoo');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1057','Wicca');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1058','Yaohushua');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1059','Zen Buddhism');
INSERT INTO list_options (list_id, option_id, title) VALUES ('religious_affiliation','1060','Zoroastrianism');
#EndIf

#IfNotRow list_options list_id race_ccda
insert into list_options (list_id,option_id,title) values ('race_ccda','1006-6','Abenaki');
insert into list_options (list_id,option_id,title) values ('race_ccda','1579-2','Absentee Shawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1490-2','Acoma');
insert into list_options (list_id,option_id,title) values ('race_ccda','2126-1','Afghanistani');
insert into list_options (list_id,option_id,title) values ('race_ccda','2060-2','African');
insert into list_options (list_id,option_id,title) values ('race_ccda','2058-6','African American');
insert into list_options (list_id,option_id,title) values ('race_ccda','1994-3','Agdaagux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1212-0','Agua Caliente');
insert into list_options (list_id,option_id,title) values ('race_ccda','1045-4','Agua Caliente Cahuilla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1740-0','Ahtna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1654-3','Ak-Chin');
insert into list_options (list_id,option_id,title) values ('race_ccda','1993-5','Akhiok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1897-8','Akiachak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1898-6','Akiak');
insert into list_options (list_id,option_id,title) values ('race_ccda','2007-3','Akutan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1187-4','Alabama Coushatta');
insert into list_options (list_id,option_id,title) values ('race_ccda','1194-0','Alabama Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1195-7','Alabama Quassarte');
insert into list_options (list_id,option_id,title) values ('race_ccda','1899-4','Alakanuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1383-9','Alamo Navajo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1744-2','Alanvik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1737-6','Alaska Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1735-0','Alaska Native');
insert into list_options (list_id,option_id,title) values ('race_ccda','1739-2','Alaskan Athabascan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1741-8','Alatna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1900-0','Aleknagik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1966-1','Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','2008-1','Aleut Corporation');
insert into list_options (list_id,option_id,title) values ('race_ccda','2009-9','Aleutian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2010-7','Aleutian Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','1742-6','Alexander');
insert into list_options (list_id,option_id,title) values ('race_ccda','1008-2','Algonquian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1743-4','Allakaket');
insert into list_options (list_id,option_id,title) values ('race_ccda','1671-7','Allen Canyon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1688-1','Alpine');
insert into list_options (list_id,option_id,title) values ('race_ccda','1392-0','Alsea');
insert into list_options (list_id,option_id,title) values ('race_ccda','1968-7','Alutiiq Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','1845-7','Ambler');
insert into list_options (list_id,option_id,title) values ('race_ccda','1004-1','American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1002-5','American Indian or Alaska Native');
insert into list_options (list_id,option_id,title) values ('race_ccda','1846-5','Anaktuvuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1847-3','Anaktuvuk Pass');
insert into list_options (list_id,option_id,title) values ('race_ccda','1901-8','Andreafsky');
insert into list_options (list_id,option_id,title) values ('race_ccda','1814-3','Angoon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1902-6','Aniak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1745-9','Anvik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1010-8','Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','2129-5','Arab');
insert into list_options (list_id,option_id,title) values ('race_ccda','1021-5','Arapaho');
insert into list_options (list_id,option_id,title) values ('race_ccda','1746-7','Arctic');
insert into list_options (list_id,option_id,title) values ('race_ccda','1849-9','Arctic Slope Corporation');
insert into list_options (list_id,option_id,title) values ('race_ccda','1848-1','Arctic Slope Inupiat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1026-4','Arikara');
insert into list_options (list_id,option_id,title) values ('race_ccda','1491-0','Arizona Tewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','2109-7','Armenian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1366-4','Aroostook');
insert into list_options (list_id,option_id,title) values ('race_ccda','2028-9','Asian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2029-7','Asian Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1028-0','Assiniboine');
insert into list_options (list_id,option_id,title) values ('race_ccda','1030-6','Assiniboine Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','2119-6','Assyrian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2011-5','Atka');
insert into list_options (list_id,option_id,title) values ('race_ccda','1903-4','Atmautluak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1850-7','Atqasuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1265-8','Atsina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1234-4','Attacapa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1046-2','Augustine');
insert into list_options (list_id,option_id,title) values ('race_ccda','1124-7','Bad River');
insert into list_options (list_id,option_id,title) values ('race_ccda','2067-7','Bahamian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2030-5','Bangladeshi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1033-0','Bannock');
insert into list_options (list_id,option_id,title) values ('race_ccda','2068-5','Barbadian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1712-9','Barrio Libre');
insert into list_options (list_id,option_id,title) values ('race_ccda','1851-5','Barrow');
insert into list_options (list_id,option_id,title) values ('race_ccda','1587-5','Battle Mountain');
insert into list_options (list_id,option_id,title) values ('race_ccda','1125-4','Bay Mills Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1747-5','Beaver');
insert into list_options (list_id,option_id,title) values ('race_ccda','2012-3','Belkofski');
insert into list_options (list_id,option_id,title) values ('race_ccda','1852-3','Bering Straits Inupiat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1904-2','Bethel');
insert into list_options (list_id,option_id,title) values ('race_ccda','2031-3','Bhutanese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1567-7','Big Cypress');
insert into list_options (list_id,option_id,title) values ('race_ccda','1905-9',"Bill Moore's Slough");
insert into list_options (list_id,option_id,title) values ('race_ccda','1235-1','Biloxi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1748-3','Birch Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1417-5','Bishop');
insert into list_options (list_id,option_id,title) values ('race_ccda','2056-0','Black');
insert into list_options (list_id,option_id,title) values ('race_ccda','2054-5','Black or African American');
insert into list_options (list_id,option_id,title) values ('race_ccda','1035-5','Blackfeet');
insert into list_options (list_id,option_id,title) values ('race_ccda','1610-5','Blackfoot Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1126-2','Bois Forte');
insert into list_options (list_id,option_id,title) values ('race_ccda','2061-0','Botswanan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1853-1','Brevig Mission');
insert into list_options (list_id,option_id,title) values ('race_ccda','1418-3','Bridgeport');
insert into list_options (list_id,option_id,title) values ('race_ccda','1568-5','Brighton');
insert into list_options (list_id,option_id,title) values ('race_ccda','1972-9','Bristol Bay Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','1906-7','Bristol Bay Yupik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1037-1','Brotherton');
insert into list_options (list_id,option_id,title) values ('race_ccda','1611-3','Brule Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1854-9','Buckland');
insert into list_options (list_id,option_id,title) values ('race_ccda','2032-1','Burmese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1419-1','Burns Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1039-7','Burt Lake Band');
insert into list_options (list_id,option_id,title) values ('race_ccda','1127-0','Burt Lake Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1412-6','Burt Lake Ottawa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1047-0','Cabazon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1041-3','Caddo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1054-6','Cahto');
insert into list_options (list_id,option_id,title) values ('race_ccda','1044-7','Cahuilla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1053-8','California Tribes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1907-5','Calista Yupik');
insert into list_options (list_id,option_id,title) values ('race_ccda','2033-9','Cambodian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1223-7','Campo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1068-6','Canadian and Latin American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1069-4','Canadian Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1384-7','Canoncito Navajo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1749-1','Cantwell');
insert into list_options (list_id,option_id,title) values ('race_ccda','1224-5','Capitan Grande');
insert into list_options (list_id,option_id,title) values ('race_ccda','2092-5','Carolinian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1689-9','Carson');
insert into list_options (list_id,option_id,title) values ('race_ccda','1076-9','Catawba');
insert into list_options (list_id,option_id,title) values ('race_ccda','1286-4','Cayuga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1078-5','Cayuse');
insert into list_options (list_id,option_id,title) values ('race_ccda','1420-9','Cedarville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1393-8','Celilo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1070-2','Central American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1815-0','Central Council of Tlingit and Haida Tribes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1465-4','Central Pomo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1750-9','Chalkyitsik');
insert into list_options (list_id,option_id,title) values ('race_ccda','2088-3','Chamorro');
insert into list_options (list_id,option_id,title) values ('race_ccda','1908-3','Chefornak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1080-1','Chehalis');
insert into list_options (list_id,option_id,title) values ('race_ccda','1082-7','Chemakuan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1086-8','Chemehuevi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1985-1','Chenega');
insert into list_options (list_id,option_id,title) values ('race_ccda','1088-4','Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1089-2','Cherokee Alabama');
insert into list_options (list_id,option_id,title) values ('race_ccda','1100-7','Cherokee Shawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1090-0','Cherokees of Northeast Alabama');
insert into list_options (list_id,option_id,title) values ('race_ccda','1091-8','Cherokees of Southeast Alabama');
insert into list_options (list_id,option_id,title) values ('race_ccda','1909-1','Chevak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1102-3','Cheyenne');
insert into list_options (list_id,option_id,title) values ('race_ccda','1612-1','Cheyenne River Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1106-4','Cheyenne-Arapaho');
insert into list_options (list_id,option_id,title) values ('race_ccda','1108-0','Chickahominy');
insert into list_options (list_id,option_id,title) values ('race_ccda','1751-7','Chickaloon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1112-2','Chickasaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1973-7','Chignik');
insert into list_options (list_id,option_id,title) values ('race_ccda','2013-1','Chignik Lagoon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1974-5','Chignik Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1816-8','Chilkat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1817-6','Chilkoot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1055-3','Chimariko');
insert into list_options (list_id,option_id,title) values ('race_ccda','2034-7','Chinese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1855-6','Chinik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1114-8','Chinook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1123-9','Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1150-2','Chippewa Cree');
insert into list_options (list_id,option_id,title) values ('race_ccda','1011-6','Chiricahua');
insert into list_options (list_id,option_id,title) values ('race_ccda','1752-5','Chistochina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1153-6','Chitimacha');
insert into list_options (list_id,option_id,title) values ('race_ccda','1753-3','Chitina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1155-1','Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1910-9','Chuathbaluk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1984-4','Chugach Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','1986-9','Chugach Corporation');
insert into list_options (list_id,option_id,title) values ('race_ccda','1718-6','Chukchansi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1162-7','Chumash');
insert into list_options (list_id,option_id,title) values ('race_ccda','2097-4','Chuukese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1754-1','Circle');
insert into list_options (list_id,option_id,title) values ('race_ccda','1479-5','Citizen Band Potawatomi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1911-7',"Clark's Point");
insert into list_options (list_id,option_id,title) values ('race_ccda','1115-5','Clatsop');
insert into list_options (list_id,option_id,title) values ('race_ccda','1165-0','Clear Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1156-9','Clifton Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1056-1','Coast Miwok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1733-5','Coast Yurok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1492-8','Cochiti');
insert into list_options (list_id,option_id,title) values ('race_ccda','1725-1','Cocopah');
insert into list_options (list_id,option_id,title) values ('race_ccda','1167-6',"Coeur D'Alene");
insert into list_options (list_id,option_id,title) values ('race_ccda','1169-2','Coharie');
insert into list_options (list_id,option_id,title) values ('race_ccda','1171-8','Colorado River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1394-6','Columbia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1116-3','Columbia River Chinook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1173-4','Colville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1175-9','Comanche');
insert into list_options (list_id,option_id,title) values ('race_ccda','1755-8','Cook Inlet');
insert into list_options (list_id,option_id,title) values ('race_ccda','1180-9','Coos');
insert into list_options (list_id,option_id,title) values ('race_ccda','1178-3','Coos, Lower Umpqua, Siuslaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1756-6','Copper Center');
insert into list_options (list_id,option_id,title) values ('race_ccda','1757-4','Copper River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1182-5','Coquilles');
insert into list_options (list_id,option_id,title) values ('race_ccda','1184-1','Costanoan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1856-4','Council');
insert into list_options (list_id,option_id,title) values ('race_ccda','1186-6','Coushatta');
insert into list_options (list_id,option_id,title) values ('race_ccda','1668-3','Cow Creek Umpqua');
insert into list_options (list_id,option_id,title) values ('race_ccda','1189-0','Cowlitz');
insert into list_options (list_id,option_id,title) values ('race_ccda','1818-4','Craig');
insert into list_options (list_id,option_id,title) values ('race_ccda','1191-6','Cree');
insert into list_options (list_id,option_id,title) values ('race_ccda','1193-2','Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1207-0','Croatan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1912-5','Crooked Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1209-6','Crow');
insert into list_options (list_id,option_id,title) values ('race_ccda','1613-9','Crow Creek Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1211-2','Cupeno');
insert into list_options (list_id,option_id,title) values ('race_ccda','1225-2','Cuyapaipe');
insert into list_options (list_id,option_id,title) values ('race_ccda','1614-7','Dakota Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1857-2','Deering');
insert into list_options (list_id,option_id,title) values ('race_ccda','1214-6','Delaware');
insert into list_options (list_id,option_id,title) values ('race_ccda','1222-9','Diegueno');
insert into list_options (list_id,option_id,title) values ('race_ccda','1057-9','Digger');
insert into list_options (list_id,option_id,title) values ('race_ccda','1913-3','Dillingham');
insert into list_options (list_id,option_id,title) values ('race_ccda','2070-1','Dominica Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','2069-3','Dominican');
insert into list_options (list_id,option_id,title) values ('race_ccda','1758-2','Dot Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1819-2','Douglas');
insert into list_options (list_id,option_id,title) values ('race_ccda','1759-0','Doyon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1690-7','Dresslerville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1466-2','Dry Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1603-0','Duck Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1588-3','Duckwater');
insert into list_options (list_id,option_id,title) values ('race_ccda','1519-8','Duwamish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1760-8','Eagle');
insert into list_options (list_id,option_id,title) values ('race_ccda','1092-6','Eastern Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1109-8','Eastern Chickahominy');
insert into list_options (list_id,option_id,title) values ('race_ccda','1196-5','Eastern Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1215-3','Eastern Delaware');
insert into list_options (list_id,option_id,title) values ('race_ccda','1197-3','Eastern Muscogee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1467-0','Eastern Pomo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1580-0','Eastern Shawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1233-6','Eastern Tribes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1093-4','Echota Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1914-1','Eek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1975-2','Egegik');
insert into list_options (list_id,option_id,title) values ('race_ccda','2120-4','Egyptian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1761-6','Eklutna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1915-8','Ekuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1916-6','Ekwok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1858-0','Elim');
insert into list_options (list_id,option_id,title) values ('race_ccda','1589-1','Elko');
insert into list_options (list_id,option_id,title) values ('race_ccda','1590-9','Ely');
insert into list_options (list_id,option_id,title) values ('race_ccda','1917-4','Emmonak');
insert into list_options (list_id,option_id,title) values ('race_ccda','2110-5','English');
insert into list_options (list_id,option_id,title) values ('race_ccda','1987-7','English Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1840-8','Eskimo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1250-0','Esselen');
insert into list_options (list_id,option_id,title) values ('race_ccda','2062-8','Ethiopian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1094-2','Etowah Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','2108-9','European');
insert into list_options (list_id,option_id,title) values ('race_ccda','1762-4','Evansville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1990-1','Eyak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1604-8','Fallon');
insert into list_options (list_id,option_id,title) values ('race_ccda','2015-6','False Pass');
insert into list_options (list_id,option_id,title) values ('race_ccda','2101-4','Fijian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2036-2','Filipino');
insert into list_options (list_id,option_id,title) values ('race_ccda','1615-4','Flandreau Santee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1569-3','Florida Seminole');
insert into list_options (list_id,option_id,title) values ('race_ccda','1128-8','Fond du Lac');
insert into list_options (list_id,option_id,title) values ('race_ccda','1480-3','Forest County');
insert into list_options (list_id,option_id,title) values ('race_ccda','1252-6','Fort Belknap');
insert into list_options (list_id,option_id,title) values ('race_ccda','1254-2','Fort Berthold');
insert into list_options (list_id,option_id,title) values ('race_ccda','1421-7','Fort Bidwell');
insert into list_options (list_id,option_id,title) values ('race_ccda','1258-3','Fort Hall');
insert into list_options (list_id,option_id,title) values ('race_ccda','1422-5','Fort Independence');
insert into list_options (list_id,option_id,title) values ('race_ccda','1605-5','Fort McDermitt');
insert into list_options (list_id,option_id,title) values ('race_ccda','1256-7','Fort Mcdowell');
insert into list_options (list_id,option_id,title) values ('race_ccda','1616-2','Fort Peck');
insert into list_options (list_id,option_id,title) values ('race_ccda','1031-4','Fort Peck Assiniboine Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1012-4','Fort Sill Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1763-2','Fort Yukon');
insert into list_options (list_id,option_id,title) values ('race_ccda','2111-3','French');
insert into list_options (list_id,option_id,title) values ('race_ccda','1071-0','French American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1260-9','Gabrieleno');
insert into list_options (list_id,option_id,title) values ('race_ccda','1764-0','Gakona');
insert into list_options (list_id,option_id,title) values ('race_ccda','1765-7','Galena');
insert into list_options (list_id,option_id,title) values ('race_ccda','1892-9','Gambell');
insert into list_options (list_id,option_id,title) values ('race_ccda','1680-8','Gay Head Wampanoag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1236-9','Georgetown (Eastern Tribes)');
insert into list_options (list_id,option_id,title) values ('race_ccda','1962-0','Georgetown (Yupik-Eskimo)');
insert into list_options (list_id,option_id,title) values ('race_ccda','2112-1','German');
insert into list_options (list_id,option_id,title) values ('race_ccda','1655-0','Gila Bend');
insert into list_options (list_id,option_id,title) values ('race_ccda','1457-1','Gila River Pima-Maricopa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1859-8','Golovin');
insert into list_options (list_id,option_id,title) values ('race_ccda','1918-2','Goodnews Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1591-7','Goshute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1129-6','Grand Portage');
insert into list_options (list_id,option_id,title) values ('race_ccda','1262-5','Grand Ronde');
insert into list_options (list_id,option_id,title) values ('race_ccda','1130-4','Grand Traverse Band of Ottawa/Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1766-5','Grayling');
insert into list_options (list_id,option_id,title) values ('race_ccda','1842-4','Greenland Eskimo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1264-1','Gros Ventres');
insert into list_options (list_id,option_id,title) values ('race_ccda','2087-5','Guamanian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2086-7','Guamanian or Chamorro');
insert into list_options (list_id,option_id,title) values ('race_ccda','1767-3','Gulkana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1820-0','Haida');
insert into list_options (list_id,option_id,title) values ('race_ccda','2071-9','Haitian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1267-4','Haliwa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1481-1','Hannahville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1726-9','Havasupai');
insert into list_options (list_id,option_id,title) values ('race_ccda','1768-1','Healy Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1269-0','Hidatsa');
insert into list_options (list_id,option_id,title) values ('race_ccda','2037-0','Hmong');
insert into list_options (list_id,option_id,title) values ('race_ccda','1697-2','Ho-chunk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1083-5','Hoh');
insert into list_options (list_id,option_id,title) values ('race_ccda','1570-1','Hollywood Seminole');
insert into list_options (list_id,option_id,title) values ('race_ccda','1769-9','Holy Cross');
insert into list_options (list_id,option_id,title) values ('race_ccda','1821-8','Hoonah');
insert into list_options (list_id,option_id,title) values ('race_ccda','1271-6','Hoopa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1275-7','Hoopa Extension');
insert into list_options (list_id,option_id,title) values ('race_ccda','1919-0','Hooper Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1493-6','Hopi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1277-3','Houma');
insert into list_options (list_id,option_id,title) values ('race_ccda','1727-7','Hualapai');
insert into list_options (list_id,option_id,title) values ('race_ccda','1770-7','Hughes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1482-9','Huron Potawatomi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1771-5','Huslia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1822-6','Hydaburg');
insert into list_options (list_id,option_id,title) values ('race_ccda','1976-0','Igiugig');
insert into list_options (list_id,option_id,title) values ('race_ccda','1772-3','Iliamna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1359-9','Illinois Miami');
insert into list_options (list_id,option_id,title) values ('race_ccda','1279-9','Inaja-Cosmit');
insert into list_options (list_id,option_id,title) values ('race_ccda','1860-6','Inalik Diomede');
insert into list_options (list_id,option_id,title) values ('race_ccda','1442-3','Indian Township');
insert into list_options (list_id,option_id,title) values ('race_ccda','1360-7','Indiana Miami');
insert into list_options (list_id,option_id,title) values ('race_ccda','2038-8','Indonesian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1861-4','Inupiaq');
insert into list_options (list_id,option_id,title) values ('race_ccda','1844-0','Inupiat Eskimo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1281-5','Iowa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1282-3','Iowa of Kansas-Nebraska');
insert into list_options (list_id,option_id,title) values ('race_ccda','1283-1','Iowa of Oklahoma');
insert into list_options (list_id,option_id,title) values ('race_ccda','1552-9','Iowa Sac and Fox');
insert into list_options (list_id,option_id,title) values ('race_ccda','1920-8','Iqurmuit (Russian Mission)');
insert into list_options (list_id,option_id,title) values ('race_ccda','2121-2','Iranian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2122-0','Iraqi');
insert into list_options (list_id,option_id,title) values ('race_ccda','2113-9','Irish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1285-6','Iroquois');
insert into list_options (list_id,option_id,title) values ('race_ccda','1494-4','Isleta');
insert into list_options (list_id,option_id,title) values ('race_ccda','2127-9','Israeili');
insert into list_options (list_id,option_id,title) values ('race_ccda','2114-7','Italian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1977-8','Ivanof Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','2048-7','Iwo Jiman');
insert into list_options (list_id,option_id,title) values ('race_ccda','2072-7','Jamaican');
insert into list_options (list_id,option_id,title) values ('race_ccda','1313-6','Jamestown');
insert into list_options (list_id,option_id,title) values ('race_ccda','2039-6','Japanese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1495-1','Jemez');
insert into list_options (list_id,option_id,title) values ('race_ccda','1157-7','Jena Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1013-2','Jicarilla Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1297-1','Juaneno');
insert into list_options (list_id,option_id,title) values ('race_ccda','1423-3','Kaibab');
insert into list_options (list_id,option_id,title) values ('race_ccda','1823-4','Kake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1862-2','Kaktovik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1395-3','Kalapuya');
insert into list_options (list_id,option_id,title) values ('race_ccda','1299-7','Kalispel');
insert into list_options (list_id,option_id,title) values ('race_ccda','1921-6','Kalskag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1773-1','Kaltag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1995-0','Karluk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1301-1','Karuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1824-2','Kasaan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1468-8','Kashia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1922-4','Kasigluk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1117-1','Kathlamet');
insert into list_options (list_id,option_id,title) values ('race_ccda','1303-7','Kaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1058-7','Kawaiisu');
insert into list_options (list_id,option_id,title) values ('race_ccda','1863-0','Kawerak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1825-9','Kenaitze');
insert into list_options (list_id,option_id,title) values ('race_ccda','1496-9','Keres');
insert into list_options (list_id,option_id,title) values ('race_ccda','1059-5','Kern River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1826-7','Ketchikan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1131-2','Keweenaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1198-1','Kialegee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1864-8','Kiana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1305-2','Kickapoo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1520-6','Kikiallus');
insert into list_options (list_id,option_id,title) values ('race_ccda','2014-9','King Cove');
insert into list_options (list_id,option_id,title) values ('race_ccda','1978-6','King Salmon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1309-4','Kiowa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1923-2','Kipnuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','2096-6','Kiribati');
insert into list_options (list_id,option_id,title) values ('race_ccda','1865-5','Kivalina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1312-8','Klallam');
insert into list_options (list_id,option_id,title) values ('race_ccda','1317-7','Klamath');
insert into list_options (list_id,option_id,title) values ('race_ccda','1827-5','Klawock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1774-9','Kluti Kaah');
insert into list_options (list_id,option_id,title) values ('race_ccda','1775-6','Knik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1866-3','Kobuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1996-8','Kodiak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1979-4','Kokhanok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1924-0','Koliganek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1925-7','Kongiganak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1992-7','Koniag Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','1319-3','Konkow');
insert into list_options (list_id,option_id,title) values ('race_ccda','1321-9','Kootenai');
insert into list_options (list_id,option_id,title) values ('race_ccda','2040-4','Korean');
insert into list_options (list_id,option_id,title) values ('race_ccda','2093-3','Kosraean');
insert into list_options (list_id,option_id,title) values ('race_ccda','1926-5','Kotlik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1867-1','Kotzebue');
insert into list_options (list_id,option_id,title) values ('race_ccda','1868-9','Koyuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1776-4','Koyukuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1927-3','Kwethluk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1928-1','Kwigillingok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1869-7','Kwiguk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1332-6','La Jolla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1226-0','La Posta');
insert into list_options (list_id,option_id,title) values ('race_ccda','1132-0','Lac Courte Oreilles');
insert into list_options (list_id,option_id,title) values ('race_ccda','1133-8','Lac du Flambeau');
insert into list_options (list_id,option_id,title) values ('race_ccda','1134-6','Lac Vieux Desert Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1497-7','Laguna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1777-2','Lake Minchumina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1135-3','Lake Superior');
insert into list_options (list_id,option_id,title) values ('race_ccda','1617-0','Lake Traverse Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','2041-2','Laotian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1997-6','Larsen Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1424-1','Las Vegas');
insert into list_options (list_id,option_id,title) values ('race_ccda','1323-5','Lassik');
insert into list_options (list_id,option_id,title) values ('race_ccda','2123-8','Lebanese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1136-1','Leech Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1216-1','Lenni-Lenape');
insert into list_options (list_id,option_id,title) values ('race_ccda','1929-9','Levelock');
insert into list_options (list_id,option_id,title) values ('race_ccda','2063-6','Liberian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1778-0','Lime');
insert into list_options (list_id,option_id,title) values ('race_ccda','1014-0','Lipan Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1137-9','Little Shell Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1425-8','Lone Pine');
insert into list_options (list_id,option_id,title) values ('race_ccda','1325-0','Long Island');
insert into list_options (list_id,option_id,title) values ('race_ccda','1048-8','Los Coyotes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1426-6','Lovelock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1618-8','Lower Brule Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1314-4','Lower Elwha');
insert into list_options (list_id,option_id,title) values ('race_ccda','1930-7','Lower Kalskag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1199-9','Lower Muscogee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1619-6','Lower Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1521-4','Lower Skagit');
insert into list_options (list_id,option_id,title) values ('race_ccda','1331-8','Luiseno');
insert into list_options (list_id,option_id,title) values ('race_ccda','1340-9','Lumbee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1342-5','Lummi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1200-5','Machis Lower Creek Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2052-9','Madagascar');
insert into list_options (list_id,option_id,title) values ('race_ccda','1344-1','Maidu');
insert into list_options (list_id,option_id,title) values ('race_ccda','1348-2','Makah');
insert into list_options (list_id,option_id,title) values ('race_ccda','2042-0','Malaysian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2049-5','Maldivian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1427-4','Malheur Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1350-8','Maliseet');
insert into list_options (list_id,option_id,title) values ('race_ccda','1352-4','Mandan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1780-6','Manley Hot Springs');
insert into list_options (list_id,option_id,title) values ('race_ccda','1931-5','Manokotak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1227-8','Manzanita');
insert into list_options (list_id,option_id,title) values ('race_ccda','2089-1','Mariana Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','1728-5','Maricopa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1932-3','Marshall');
insert into list_options (list_id,option_id,title) values ('race_ccda','2090-9','Marshallese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1454-8','Marshantucket Pequot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1889-5',"Mary\s Igloo");
insert into list_options (list_id,option_id,title) values ('race_ccda','1681-6','Mashpee Wampanoag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1326-8','Matinecock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1354-0','Mattaponi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1060-3','Mattole');
insert into list_options (list_id,option_id,title) values ('race_ccda','1870-5','Mauneluk Inupiat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1779-8','Mcgrath');
insert into list_options (list_id,option_id,title) values ('race_ccda','1620-4','Mdewakanton Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1933-1','Mekoryuk');
insert into list_options (list_id,option_id,title) values ('race_ccda','2100-6','Melanesian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1356-5','Menominee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1781-4','Mentasta Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','1228-6','Mesa Grande');
insert into list_options (list_id,option_id,title) values ('race_ccda','1015-7','Mescalero Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1838-2','Metlakatla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1072-8','Mexican American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1358-1','Miami');
insert into list_options (list_id,option_id,title) values ('race_ccda','1363-1','Miccosukee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1413-4','Michigan Ottawa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1365-6','Micmac');
insert into list_options (list_id,option_id,title) values ('race_ccda','2085-9','Micronesian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2118-8','Middle Eastern or North African');
insert into list_options (list_id,option_id,title) values ('race_ccda','1138-7','Mille Lacs');
insert into list_options (list_id,option_id,title) values ('race_ccda','1621-2','Miniconjou');
insert into list_options (list_id,option_id,title) values ('race_ccda','1139-5','Minnesota Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1782-2','Minto');
insert into list_options (list_id,option_id,title) values ('race_ccda','1368-0','Mission Indians');
insert into list_options (list_id,option_id,title) values ('race_ccda','1158-5','Mississippi Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1553-7','Missouri Sac and Fox');
insert into list_options (list_id,option_id,title) values ('race_ccda','1370-6','Miwok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1428-2','Moapa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1372-2','Modoc');
insert into list_options (list_id,option_id,title) values ('race_ccda','1729-3','Mohave');
insert into list_options (list_id,option_id,title) values ('race_ccda','1287-2','Mohawk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1374-8','Mohegan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1396-1','Molala');
insert into list_options (list_id,option_id,title) values ('race_ccda','1376-3','Mono');
insert into list_options (list_id,option_id,title) values ('race_ccda','1327-6','Montauk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1237-7','Moor');
insert into list_options (list_id,option_id,title) values ('race_ccda','1049-6','Morongo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1345-8','Mountain Maidu');
insert into list_options (list_id,option_id,title) values ('race_ccda','1934-9','Mountain Village');
insert into list_options (list_id,option_id,title) values ('race_ccda','1159-3','Mowa Band of Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1522-2','Muckleshoot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1217-9','Munsee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1935-6','Naknek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1498-5','Nambe');
insert into list_options (list_id,option_id,title) values ('race_ccda','2064-4','Namibian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1871-3','Nana Inupiat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1238-5','Nansemond');
insert into list_options (list_id,option_id,title) values ('race_ccda','1378-9','Nanticoke');
insert into list_options (list_id,option_id,title) values ('race_ccda','1937-2','Napakiak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1938-0','Napaskiak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1936-4','Napaumute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1380-5','Narragansett');
insert into list_options (list_id,option_id,title) values ('race_ccda','1239-3','Natchez');
insert into list_options (list_id,option_id,title) values ('race_ccda','2079-2','Native Hawaiian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2076-8','Native Hawaiian or Other Pacific Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','1240-1','Nausu Waiwash');
insert into list_options (list_id,option_id,title) values ('race_ccda','1382-1','Navajo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1475-3','Nebraska Ponca');
insert into list_options (list_id,option_id,title) values ('race_ccda','1698-0','Nebraska Winnebago');
insert into list_options (list_id,option_id,title) values ('race_ccda','2016-4','Nelson Lagoon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1783-0','Nenana');
insert into list_options (list_id,option_id,title) values ('race_ccda','2050-3','Nepalese');
insert into list_options (list_id,option_id,title) values ('race_ccda','2104-8','New Hebrides');
insert into list_options (list_id,option_id,title) values ('race_ccda','1940-6','New Stuyahok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1939-8','Newhalen');
insert into list_options (list_id,option_id,title) values ('race_ccda','1941-4','Newtok');
insert into list_options (list_id,option_id,title) values ('race_ccda','1387-0','Nez Perce');
insert into list_options (list_id,option_id,title) values ('race_ccda','2065-1','Nigerian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1942-2','Nightmute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1784-8','Nikolai');
insert into list_options (list_id,option_id,title) values ('race_ccda','2017-2','Nikolski');
insert into list_options (list_id,option_id,title) values ('race_ccda','1785-5','Ninilchik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1241-9','Nipmuc');
insert into list_options (list_id,option_id,title) values ('race_ccda','1346-6','Nishinam');
insert into list_options (list_id,option_id,title) values ('race_ccda','1523-0','Nisqually');
insert into list_options (list_id,option_id,title) values ('race_ccda','1872-1','Noatak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1389-6','Nomalaki');
insert into list_options (list_id,option_id,title) values ('race_ccda','1873-9','Nome');
insert into list_options (list_id,option_id,title) values ('race_ccda','1786-3','Nondalton');
insert into list_options (list_id,option_id,title) values ('race_ccda','1524-8','Nooksack');
insert into list_options (list_id,option_id,title) values ('race_ccda','1874-7','Noorvik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1022-3','Northern Arapaho');
insert into list_options (list_id,option_id,title) values ('race_ccda','1095-9','Northern Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1103-1','Northern Cheyenne');
insert into list_options (list_id,option_id,title) values ('race_ccda','1429-0','Northern Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1469-6','Northern Pomo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1787-1','Northway');
insert into list_options (list_id,option_id,title) values ('race_ccda','1391-2','Northwest Tribes');
insert into list_options (list_id,option_id,title) values ('race_ccda','1875-4','Nuiqsut');
insert into list_options (list_id,option_id,title) values ('race_ccda','1788-9','Nulato');
insert into list_options (list_id,option_id,title) values ('race_ccda','1943-0','Nunapitchukv');
insert into list_options (list_id,option_id,title) values ('race_ccda','1622-0','Oglala Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','2043-8','Okinawan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1016-5','Oklahoma Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1042-1','Oklahoma Cado');
insert into list_options (list_id,option_id,title) values ('race_ccda','1160-1','Oklahoma Choctaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1176-7','Oklahoma Comanche');
insert into list_options (list_id,option_id,title) values ('race_ccda','1218-7','Oklahoma Delaware');
insert into list_options (list_id,option_id,title) values ('race_ccda','1306-0','Oklahoma Kickapoo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1310-2','Oklahoma Kiowa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1361-5','Oklahoma Miami');
insert into list_options (list_id,option_id,title) values ('race_ccda','1414-2','Oklahoma Ottawa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1446-4','Oklahoma Pawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1451-4','Oklahoma Peoria');
insert into list_options (list_id,option_id,title) values ('race_ccda','1476-1','Oklahoma Ponca');
insert into list_options (list_id,option_id,title) values ('race_ccda','1554-5','Oklahoma Sac and Fox');
insert into list_options (list_id,option_id,title) values ('race_ccda','1571-9','Oklahoma Seminole');
insert into list_options (list_id,option_id,title) values ('race_ccda','1998-4','Old Harbor');
insert into list_options (list_id,option_id,title) values ('race_ccda','1403-5','Omaha');
insert into list_options (list_id,option_id,title) values ('race_ccda','1288-0','Oneida');
insert into list_options (list_id,option_id,title) values ('race_ccda','1289-8','Onondaga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1140-3','Ontonagon');
insert into list_options (list_id,option_id,title) values ('race_ccda','1405-0','Oregon Athabaskan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1407-6','Osage');
insert into list_options (list_id,option_id,title) values ('race_ccda','1944-8','Oscarville');
insert into list_options (list_id,option_id,title) values ('race_ccda','2500-7','Other Pacific Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','2131-1','Other Race');
insert into list_options (list_id,option_id,title) values ('race_ccda','1409-2','Otoe-Missouria');
insert into list_options (list_id,option_id,title) values ('race_ccda','1411-8','Ottawa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1999-2','Ouzinkie');
insert into list_options (list_id,option_id,title) values ('race_ccda','1430-8','Owens Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1416-7','Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','2044-6','Pakistani');
insert into list_options (list_id,option_id,title) values ('race_ccda','1333-4','Pala');
insert into list_options (list_id,option_id,title) values ('race_ccda','2091-7','Palauan');
insert into list_options (list_id,option_id,title) values ('race_ccda','2124-6','Palestinian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1439-9','Pamunkey');
insert into list_options (list_id,option_id,title) values ('race_ccda','1592-5','Panamint');
insert into list_options (list_id,option_id,title) values ('race_ccda','2102-2','Papua New Guinean');
insert into list_options (list_id,option_id,title) values ('race_ccda','1713-7','Pascua Yaqui');
insert into list_options (list_id,option_id,title) values ('race_ccda','1441-5','Passamaquoddy');
insert into list_options (list_id,option_id,title) values ('race_ccda','1242-7','Paugussett');
insert into list_options (list_id,option_id,title) values ('race_ccda','2018-0','Pauloff Harbor');
insert into list_options (list_id,option_id,title) values ('race_ccda','1334-2','Pauma');
insert into list_options (list_id,option_id,title) values ('race_ccda','1445-6','Pawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1017-3','Payson Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1335-9','Pechanga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1789-7','Pedro Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1828-3','Pelican');
insert into list_options (list_id,option_id,title) values ('race_ccda','1448-0','Penobscot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1450-6','Peoria');
insert into list_options (list_id,option_id,title) values ('race_ccda','1453-0','Pequot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1980-2','Perryville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1829-1','Petersburg');
insert into list_options (list_id,option_id,title) values ('race_ccda','1499-3','Picuris');
insert into list_options (list_id,option_id,title) values ('race_ccda','1981-0','Pilot Point');
insert into list_options (list_id,option_id,title) values ('race_ccda','1945-5','Pilot Station');
insert into list_options (list_id,option_id,title) values ('race_ccda','1456-3','Pima');
insert into list_options (list_id,option_id,title) values ('race_ccda','1623-8','Pine Ridge Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1624-6','Pipestone Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1500-8','Piro');
insert into list_options (list_id,option_id,title) values ('race_ccda','1460-5','Piscataway');
insert into list_options (list_id,option_id,title) values ('race_ccda','1462-1','Pit River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1946-3','Pitkas Point');
insert into list_options (list_id,option_id,title) values ('race_ccda','1947-1','Platinum');
insert into list_options (list_id,option_id,title) values ('race_ccda','1443-1','Pleasant Point Passamaquoddy');
insert into list_options (list_id,option_id,title) values ('race_ccda','1201-3','Poarch Band');
insert into list_options (list_id,option_id,title) values ('race_ccda','1243-5','Pocomoke Acohonock');
insert into list_options (list_id,option_id,title) values ('race_ccda','2094-1','Pohnpeian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1876-2','Point Hope');
insert into list_options (list_id,option_id,title) values ('race_ccda','1877-0','Point Lay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1501-6','Pojoaque');
insert into list_options (list_id,option_id,title) values ('race_ccda','1483-7','Pokagon Potawatomi');
insert into list_options (list_id,option_id,title) values ('race_ccda','2115-4','Polish');
insert into list_options (list_id,option_id,title) values ('race_ccda','2078-4','Polynesian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1464-7','Pomo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1474-6','Ponca');
insert into list_options (list_id,option_id,title) values ('race_ccda','1328-4','Poospatuck');
insert into list_options (list_id,option_id,title) values ('race_ccda','1315-1','Port Gamble Klallam');
insert into list_options (list_id,option_id,title) values ('race_ccda','1988-5','Port Graham');
insert into list_options (list_id,option_id,title) values ('race_ccda','1982-8','Port Heiden');
insert into list_options (list_id,option_id,title) values ('race_ccda','2000-8','Port Lions');
insert into list_options (list_id,option_id,title) values ('race_ccda','1525-5','Port Madison');
insert into list_options (list_id,option_id,title) values ('race_ccda','1948-9','Portage Creek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1478-7','Potawatomi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1487-8','Powhatan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1484-5','Prairie Band');
insert into list_options (list_id,option_id,title) values ('race_ccda','1625-3','Prairie Island Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1202-1','Principal Creek Indian Nation');
insert into list_options (list_id,option_id,title) values ('race_ccda','1626-1','Prior Lake Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1489-4','Pueblo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1518-0','Puget Sound Salish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1526-3','Puyallup');
insert into list_options (list_id,option_id,title) values ('race_ccda','1431-6','Pyramid Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','2019-8','Qagan Toyagungin');
insert into list_options (list_id,option_id,title) values ('race_ccda','2020-6','Qawalangin');
insert into list_options (list_id,option_id,title) values ('race_ccda','1541-2','Quapaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1730-1','Quechan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1084-3','Quileute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1543-8','Quinault');
insert into list_options (list_id,option_id,title) values ('race_ccda','1949-7','Quinhagak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1385-4','Ramah Navajo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1790-5','Rampart');
insert into list_options (list_id,option_id,title) values ('race_ccda','1219-5','Rampough Mountain');
insert into list_options (list_id,option_id,title) values ('race_ccda','1545-3','Rappahannock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1141-1','Red Cliff Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1950-5','Red Devil');
insert into list_options (list_id,option_id,title) values ('race_ccda','1142-9','Red Lake Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1061-1','Red Wood');
insert into list_options (list_id,option_id,title) values ('race_ccda','1547-9','Reno-Sparks');
insert into list_options (list_id,option_id,title) values ('race_ccda','1151-0',"Rocky Boy's Chippewa Cree");
insert into list_options (list_id,option_id,title) values ('race_ccda','1627-9','Rosebud Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1549-5','Round Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1791-3','Ruby');
insert into list_options (list_id,option_id,title) values ('race_ccda','1593-3','Ruby Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1551-1','Sac and Fox');
insert into list_options (list_id,option_id,title) values ('race_ccda','1143-7','Saginaw Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','2095-8','Saipanese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1792-1','Salamatof');
insert into list_options (list_id,option_id,title) values ('race_ccda','1556-0','Salinan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1558-6','Salish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1560-2','Salish and Kootenai');
insert into list_options (list_id,option_id,title) values ('race_ccda','1458-9','Salt River Pima-Maricopa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1527-1','Samish');
insert into list_options (list_id,option_id,title) values ('race_ccda','2080-0','Samoan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1018-1','San Carlos Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1502-4','San Felipe');
insert into list_options (list_id,option_id,title) values ('race_ccda','1503-2','San Ildefonso');
insert into list_options (list_id,option_id,title) values ('race_ccda','1506-5','San Juan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1505-7','San Juan De');
insert into list_options (list_id,option_id,title) values ('race_ccda','1504-0','San Juan Pueblo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1432-4','San Juan Southern Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1574-3','San Manual');
insert into list_options (list_id,option_id,title) values ('race_ccda','1229-4','San Pasqual');
insert into list_options (list_id,option_id,title) values ('race_ccda','1656-8','San Xavier');
insert into list_options (list_id,option_id,title) values ('race_ccda','1220-3','Sand Hill');
insert into list_options (list_id,option_id,title) values ('race_ccda','2023-0','Sand Point');
insert into list_options (list_id,option_id,title) values ('race_ccda','1507-3','Sandia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1628-7','Sans Arc Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1508-1','Santa Ana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1509-9','Santa Clara');
insert into list_options (list_id,option_id,title) values ('race_ccda','1062-9','Santa Rosa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1050-4','Santa Rosa Cahuilla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1163-5','Santa Ynez');
insert into list_options (list_id,option_id,title) values ('race_ccda','1230-2','Santa Ysabel');
insert into list_options (list_id,option_id,title) values ('race_ccda','1629-5','Santee Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1510-7','Santo Domingo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1528-9','Sauk-Suiattle');
insert into list_options (list_id,option_id,title) values ('race_ccda','1145-2','Sault Ste. Marie Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1893-7','Savoonga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1830-9','Saxman');
insert into list_options (list_id,option_id,title) values ('race_ccda','1952-1','Scammon Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1562-8','Schaghticoke');
insert into list_options (list_id,option_id,title) values ('race_ccda','1564-4','Scott Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','2116-2','Scottish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1470-4','Scotts Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1878-8','Selawik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1793-9','Seldovia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1657-6','Sells');
insert into list_options (list_id,option_id,title) values ('race_ccda','1566-9','Seminole');
insert into list_options (list_id,option_id,title) values ('race_ccda','1290-6','Seneca');
insert into list_options (list_id,option_id,title) values ('race_ccda','1291-4','Seneca Nation');
insert into list_options (list_id,option_id,title) values ('race_ccda','1292-2','Seneca-Cayuga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1573-5','Serrano');
insert into list_options (list_id,option_id,title) values ('race_ccda','1329-2','Setauket');
insert into list_options (list_id,option_id,title) values ('race_ccda','1795-4','Shageluk');
insert into list_options (list_id,option_id,title) values ('race_ccda','1879-6','Shaktoolik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1576-8','Shasta');
insert into list_options (list_id,option_id,title) values ('race_ccda','1578-4','Shawnee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1953-9',"Sheldon's Point");
insert into list_options (list_id,option_id,title) values ('race_ccda','1582-6','Shinnecock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1880-4','Shishmaref');
insert into list_options (list_id,option_id,title) values ('race_ccda','1584-2','Shoalwater Bay');
insert into list_options (list_id,option_id,title) values ('race_ccda','1586-7','Shoshone');
insert into list_options (list_id,option_id,title) values ('race_ccda','1602-2','Shoshone Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1881-2','Shungnak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1891-1','Siberian Eskimo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1894-5','Siberian Yupik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1607-1','Siletz');
insert into list_options (list_id,option_id,title) values ('race_ccda','2051-1','Singaporean');
insert into list_options (list_id,option_id,title) values ('race_ccda','1609-7','Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1631-1','Sisseton Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1630-3','Sisseton-Wahpeton');
insert into list_options (list_id,option_id,title) values ('race_ccda','1831-7','Sitka');
insert into list_options (list_id,option_id,title) values ('race_ccda','1643-6','Siuslaw');
insert into list_options (list_id,option_id,title) values ('race_ccda','1529-7','Skokomish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1594-1','Skull Valley');
insert into list_options (list_id,option_id,title) values ('race_ccda','1530-5','Skykomish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1794-7','Slana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1954-7','Sleetmute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1531-3','Snohomish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1532-1','Snoqualmie');
insert into list_options (list_id,option_id,title) values ('race_ccda','1336-7','Soboba');
insert into list_options (list_id,option_id,title) values ('race_ccda','1146-0','Sokoagon Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1882-0','Solomon');
insert into list_options (list_id,option_id,title) values ('race_ccda','2103-0','Solomon Islander');
insert into list_options (list_id,option_id,title) values ('race_ccda','1073-6','South American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1595-8','South Fork Shoshone');
insert into list_options (list_id,option_id,title) values ('race_ccda','2024-8','South Naknek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1811-9','Southeast Alaska');
insert into list_options (list_id,option_id,title) values ('race_ccda','1244-3','Southeastern Indians');
insert into list_options (list_id,option_id,title) values ('race_ccda','1023-1','Southern Arapaho');
insert into list_options (list_id,option_id,title) values ('race_ccda','1104-9','Southern Cheyenne');
insert into list_options (list_id,option_id,title) values ('race_ccda','1433-2','Southern Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1074-4','Spanish American Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1632-9','Spirit Lake Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1645-1','Spokane');
insert into list_options (list_id,option_id,title) values ('race_ccda','1533-9','Squaxin Island');
insert into list_options (list_id,option_id,title) values ('race_ccda','2045-3','Sri Lankan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1144-5','St. Croix Chippewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','2021-4','St. George');
insert into list_options (list_id,option_id,title) values ('race_ccda','1963-8',"St. Mary's");
insert into list_options (list_id,option_id,title) values ('race_ccda','1951-3','St. Michael');
insert into list_options (list_id,option_id,title) values ('race_ccda','2022-2','St. Paul');
insert into list_options (list_id,option_id,title) values ('race_ccda','1633-7','Standing Rock Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1203-9','Star Clan of Muscogee Creeks');
insert into list_options (list_id,option_id,title) values ('race_ccda','1955-4','Stebbins');
insert into list_options (list_id,option_id,title) values ('race_ccda','1534-7','Steilacoom');
insert into list_options (list_id,option_id,title) values ('race_ccda','1796-2','Stevens');
insert into list_options (list_id,option_id,title) values ('race_ccda','1647-7','Stewart');
insert into list_options (list_id,option_id,title) values ('race_ccda','1535-4','Stillaguamish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1649-3','Stockbridge');
insert into list_options (list_id,option_id,title) values ('race_ccda','1797-0','Stony River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1471-2','Stonyford');
insert into list_options (list_id,option_id,title) values ('race_ccda','2002-4','Sugpiaq');
insert into list_options (list_id,option_id,title) values ('race_ccda','1472-0','Sulphur Bank');
insert into list_options (list_id,option_id,title) values ('race_ccda','1434-0','Summit Lake');
insert into list_options (list_id,option_id,title) values ('race_ccda','2004-0','Suqpigaq');
insert into list_options (list_id,option_id,title) values ('race_ccda','1536-2','Suquamish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1651-9','Susanville');
insert into list_options (list_id,option_id,title) values ('race_ccda','1245-0','Susquehanock');
insert into list_options (list_id,option_id,title) values ('race_ccda','1537-0','Swinomish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1231-0','Sycuan');
insert into list_options (list_id,option_id,title) values ('race_ccda','2125-3','Syrian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1705-3','Table Bluff');
insert into list_options (list_id,option_id,title) values ('race_ccda','1719-4','Tachi');
insert into list_options (list_id,option_id,title) values ('race_ccda','2081-8','Tahitian');
insert into list_options (list_id,option_id,title) values ('race_ccda','2035-4','Taiwanese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1063-7','Takelma');
insert into list_options (list_id,option_id,title) values ('race_ccda','1798-8','Takotna');
insert into list_options (list_id,option_id,title) values ('race_ccda','1397-9','Talakamish');
insert into list_options (list_id,option_id,title) values ('race_ccda','1799-6','Tanacross');
insert into list_options (list_id,option_id,title) values ('race_ccda','1800-2','Tanaina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1801-0','Tanana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1802-8','Tanana Chiefs');
insert into list_options (list_id,option_id,title) values ('race_ccda','1511-5','Taos');
insert into list_options (list_id,option_id,title) values ('race_ccda','1969-5','Tatitlek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1803-6','Tazlina');
insert into list_options (list_id,option_id,title) values ('race_ccda','1804-4','Telida');
insert into list_options (list_id,option_id,title) values ('race_ccda','1883-8','Teller');
insert into list_options (list_id,option_id,title) values ('race_ccda','1338-3','Temecula');
insert into list_options (list_id,option_id,title) values ('race_ccda','1596-6','Te-Moak Western Shoshone');
insert into list_options (list_id,option_id,title) values ('race_ccda','1832-5','Tenakee Springs');
insert into list_options (list_id,option_id,title) values ('race_ccda','1398-7','Tenino');
insert into list_options (list_id,option_id,title) values ('race_ccda','1512-3','Tesuque');
insert into list_options (list_id,option_id,title) values ('race_ccda','1805-1','Tetlin');
insert into list_options (list_id,option_id,title) values ('race_ccda','1634-5','Teton Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1513-1','Tewa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1307-8','Texas Kickapoo');
insert into list_options (list_id,option_id,title) values ('race_ccda','2046-1','Thai');
insert into list_options (list_id,option_id,title) values ('race_ccda','1204-7','Thlopthlocco');
insert into list_options (list_id,option_id,title) values ('race_ccda','1514-9','Tigua');
insert into list_options (list_id,option_id,title) values ('race_ccda','1399-5','Tillamook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1597-4','Timbi-Sha Shoshone');
insert into list_options (list_id,option_id,title) values ('race_ccda','1833-3','Tlingit');
insert into list_options (list_id,option_id,title) values ('race_ccda','1813-5','Tlingit-Haida');
insert into list_options (list_id,option_id,title) values ('race_ccda','2073-5','Tobagoan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1956-2','Togiak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1653-5',"Tohono O'Odham");
insert into list_options (list_id,option_id,title) values ('race_ccda','1806-9','Tok');
insert into list_options (list_id,option_id,title) values ('race_ccda','2083-4','Tokelauan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1957-0','Toksook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1659-2','Tolowa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1293-0','Tonawanda Seneca');
insert into list_options (list_id,option_id,title) values ('race_ccda','2082-6','Tongan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1661-8','Tonkawa');
insert into list_options (list_id,option_id,title) values ('race_ccda','1051-2','Torres-Martinez');
insert into list_options (list_id,option_id,title) values ('race_ccda','2074-3','Trinidadian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1272-4','Trinity');
insert into list_options (list_id,option_id,title) values ('race_ccda','1837-4','Tsimshian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1205-4','Tuckabachee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1538-8','Tulalip');
insert into list_options (list_id,option_id,title) values ('race_ccda','1720-2','Tule River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1958-8','Tulukskak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1246-8','Tunica Biloxi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1959-6','Tuntutuliak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1960-4','Tununak');
insert into list_options (list_id,option_id,title) values ('race_ccda','1147-8','Turtle Mountain');
insert into list_options (list_id,option_id,title) values ('race_ccda','1294-8','Tuscarora');
insert into list_options (list_id,option_id,title) values ('race_ccda','1096-7','Tuscola');
insert into list_options (list_id,option_id,title) values ('race_ccda','1337-5','Twenty-Nine Palms');
insert into list_options (list_id,option_id,title) values ('race_ccda','1961-2','Twin Hills');
insert into list_options (list_id,option_id,title) values ('race_ccda','1635-2','Two Kettle Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1663-4','Tygh');
insert into list_options (list_id,option_id,title) values ('race_ccda','1807-7','Tyonek');
insert into list_options (list_id,option_id,title) values ('race_ccda','1970-3','Ugashik');
insert into list_options (list_id,option_id,title) values ('race_ccda','1672-5','Uintah Ute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1665-9','Umatilla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1964-6','Umkumiate');
insert into list_options (list_id,option_id,title) values ('race_ccda','1667-5','Umpqua');
insert into list_options (list_id,option_id,title) values ('race_ccda','1884-6','Unalakleet');
insert into list_options (list_id,option_id,title) values ('race_ccda','2025-5','Unalaska');
insert into list_options (list_id,option_id,title) values ('race_ccda','2006-5','Unangan Aleut');
insert into list_options (list_id,option_id,title) values ('race_ccda','2026-3','Unga');
insert into list_options (list_id,option_id,title) values ('race_ccda','1097-5','United Keetowah Band of Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1118-9','Upper Chinook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1636-0','Upper Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1539-6','Upper Skagit');
insert into list_options (list_id,option_id,title) values ('race_ccda','1670-9','Ute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1673-3','Ute Mountain Ute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1435-7','Utu Utu Gwaitu Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1808-5','Venetie');
insert into list_options (list_id,option_id,title) values ('race_ccda','2047-9','Vietnamese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1247-6','Waccamaw-Siousan');
insert into list_options (list_id,option_id,title) values ('race_ccda','1637-8','Wahpekute Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1638-6','Wahpeton Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1675-8','Wailaki');
insert into list_options (list_id,option_id,title) values ('race_ccda','1885-3','Wainwright');
insert into list_options (list_id,option_id,title) values ('race_ccda','1119-7','Wakiakum Chinook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1886-1','Wales');
insert into list_options (list_id,option_id,title) values ('race_ccda','1436-5','Walker River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1677-4','Walla-Walla');
insert into list_options (list_id,option_id,title) values ('race_ccda','1679-0','Wampanoag');
insert into list_options (list_id,option_id,title) values ('race_ccda','1064-5','Wappo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1683-2','Warm Springs');
insert into list_options (list_id,option_id,title) values ('race_ccda','1685-7','Wascopum');
insert into list_options (list_id,option_id,title) values ('race_ccda','1598-2','Washakie');
insert into list_options (list_id,option_id,title) values ('race_ccda','1687-3','Washoe');
insert into list_options (list_id,option_id,title) values ('race_ccda','1639-4','Wazhaza Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1400-1','Wenatchee');
insert into list_options (list_id,option_id,title) values ('race_ccda','2075-0','West Indian');
insert into list_options (list_id,option_id,title) values ('race_ccda','1098-3','Western Cherokee');
insert into list_options (list_id,option_id,title) values ('race_ccda','1110-6','Western Chickahominy');
insert into list_options (list_id,option_id,title) values ('race_ccda','1273-2','Whilkut');
insert into list_options (list_id,option_id,title) values ('race_ccda','2106-3','White');
insert into list_options (list_id,option_id,title) values ('race_ccda','1148-6','White Earth');
insert into list_options (list_id,option_id,title) values ('race_ccda','1887-9','White Mountain');
insert into list_options (list_id,option_id,title) values ('race_ccda','1019-9','White Mountain Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1888-7','White Mountain Inupiat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1692-3','Wichita');
insert into list_options (list_id,option_id,title) values ('race_ccda','1248-4','Wicomico');
insert into list_options (list_id,option_id,title) values ('race_ccda','1120-5','Willapa Chinook');
insert into list_options (list_id,option_id,title) values ('race_ccda','1694-9','Wind River');
insert into list_options (list_id,option_id,title) values ('race_ccda','1024-9','Wind River Arapaho');
insert into list_options (list_id,option_id,title) values ('race_ccda','1599-0','Wind River Shoshone');
insert into list_options (list_id,option_id,title) values ('race_ccda','1696-4','Winnebago');
insert into list_options (list_id,option_id,title) values ('race_ccda','1700-4','Winnemucca');
insert into list_options (list_id,option_id,title) values ('race_ccda','1702-0','Wintun');
insert into list_options (list_id,option_id,title) values ('race_ccda','1485-2','Wisconsin Potawatomi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1809-3','Wiseman');
insert into list_options (list_id,option_id,title) values ('race_ccda','1121-3','Wishram');
insert into list_options (list_id,option_id,title) values ('race_ccda','1704-6','Wiyot');
insert into list_options (list_id,option_id,title) values ('race_ccda','1834-1','Wrangell');
insert into list_options (list_id,option_id,title) values ('race_ccda','1295-5','Wyandotte');
insert into list_options (list_id,option_id,title) values ('race_ccda','1401-9','Yahooskin');
insert into list_options (list_id,option_id,title) values ('race_ccda','1707-9','Yakama');
insert into list_options (list_id,option_id,title) values ('race_ccda','1709-5','Yakama Cowlitz');
insert into list_options (list_id,option_id,title) values ('race_ccda','1835-8','Yakutat');
insert into list_options (list_id,option_id,title) values ('race_ccda','1065-2','Yana');
insert into list_options (list_id,option_id,title) values ('race_ccda','1640-2','Yankton Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','1641-0','Yanktonai Sioux');
insert into list_options (list_id,option_id,title) values ('race_ccda','2098-2','Yapese');
insert into list_options (list_id,option_id,title) values ('race_ccda','1711-1','Yaqui');
insert into list_options (list_id,option_id,title) values ('race_ccda','1731-9','Yavapai');
insert into list_options (list_id,option_id,title) values ('race_ccda','1715-2','Yavapai Apache');
insert into list_options (list_id,option_id,title) values ('race_ccda','1437-3','Yerington Paiute');
insert into list_options (list_id,option_id,title) values ('race_ccda','1717-8','Yokuts');
insert into list_options (list_id,option_id,title) values ('race_ccda','1600-6','Yomba');
insert into list_options (list_id,option_id,title) values ('race_ccda','1722-8','Yuchi');
insert into list_options (list_id,option_id,title) values ('race_ccda','1066-0','Yuki');
insert into list_options (list_id,option_id,title) values ('race_ccda','1724-4','Yuman');
insert into list_options (list_id,option_id,title) values ('race_ccda','1896-0','Yupik Eskimo');
insert into list_options (list_id,option_id,title) values ('race_ccda','1732-7','Yurok');
insert into list_options (list_id,option_id,title) values ('race_ccda','2066-9','Zairean');
insert into list_options (list_id,option_id,title) values ('race_ccda','1515-6','Zia');
insert into list_options (list_id,option_id,title) values ('race_ccda','1516-4','Zuni');

update patient_data set race='2135-2' where race='hisp_or_latin';
update patient_data set race='2186-5' where race='not_hisp_or_latin';
update patient_data set race='' where race='Afro American';
update patient_data set race='' where race='American Indian';
update patient_data set race='' where race='Asian';
#EndIf

#IfNotRow list_options list_id ethnicity_ccda
insert into list_options (list_id,option_id,title) values ('ethnicity_ccda','2135-2','Hispanic or Latino');
insert into list_options (list_id,option_id,title) values ('ethnicity_ccda','2186-5','NOT Hispanic OR Latino');

update patient_data set ethnicity='2135-2' where ethnicity='hisp_or_latin';
update patient_data set ethnicity='2186-5' where ethnicity='not_hisp_or_latin';
#EndIf

#IfNotRow list_options list_id personal_relationship
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','ADOPT','adopted child');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','AUNT','aunt');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','CHILD','Child');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','CHLDINLAW','child in-law');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','COUSN','cousin');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','DOMPART','domestic partner');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','FAMMEMB','Family Member');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','CHLDFOST','foster child');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','GRNDCHILD','grandchild');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','GPARNT','grandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','GRPRN','Grandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','GGRPRN','great grandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','HSIB','half-sibling');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','MAUNT','MaternalAunt');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','MCOUSN','MaternalCousin');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','MGRPRN','MaternalGrandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','MGGRPRN','MaternalGreatgrandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','MUNCLE','MaternalUncle');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','NCHILD','natural child');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','NPRN','natural parent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','NSIB','natural sibling');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','NBOR','neighbor');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','NIENEPH','niece/nephew');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PRN','Parent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PRNINLAW','parent in-law');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PAUNT','PaternalAunt');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PCOUSN','PaternalCousin');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PGRPRN','PaternalGrandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PGGRPRN','PaternalGreatgrandparent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','PUNCLE','PaternalUncle');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','ROOM','Roommate');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','SIB','Sibling');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','SIBINLAW','sibling in-law');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','SIGOTHR','significant other');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','SPS','spouse');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','STEP','step child');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','STPPRN','step parent');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','STPSIB','step sibling');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','UNCLE','uncle');
INSERT INTO list_options (list_id, option_id, title) VALUES ('personal_relationship','FRND','unrelated friend');
#EndIf

#IfMissingColumn lists severity_al
ALTER TABLE lists ADD COLUMN severity_al VARCHAR(50) NULL;
#EndIf

#IfNotRow list_options list_id severity_ccda
insert into list_options (list_id, option_id, title) values ('severity_ccda','399166001','Fatal');
insert into list_options (list_id, option_id, title) values ('severity_ccda','442452003','Life threatening severity');
insert into list_options (list_id, option_id, title) values ('severity_ccda','255604002','Mild');
insert into list_options (list_id, option_id, title) values ('severity_ccda','371923003','Mild to moderate');
insert into list_options (list_id, option_id, title) values ('severity_ccda','6736007','Moderate');
insert into list_options (list_id, option_id, title) values ('severity_ccda','371924009','Moderate to severe');
insert into list_options (list_id, option_id, title) values ('severity_ccda','24484000','Severe');

update lists set severity_al='399166001' where severity_al='Fatal';
update lists set severity_al='255604002' where severity_al='Mild';
update lists set severity_al='371923003' where severity_al='Mild to Moderate';
update lists set severity_al='6736007' where severity_al='Moderate';
update lists set severity_al='371924009' where severity_al='Moderate to Severe';
update lists set severity_al='24484000' where severity_al='Severe';
update lists set severity_al='255604002' where severity_al='1';
update lists set severity_al='6736007' where severity_al='2';
update lists set severity_al='24484000' where severity_al='3';
update lists set severity_al='371923003' where severity_al='4';
update lists set severity_al='371924009' where severity_al='5';
#EndIf

#IfNotRow list_options list_id immunization_route
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','ID','Intradermal');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','IM','Intramuscular');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','NS','Nasal');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','IV','Intravenous');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','PO','Oral');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','OTH','Other/Miscellaneous');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','SC','Subcutaneous');
INSERT INTO list_options (list_id, option_id, title) VALUES ('immunization_route','TD','Transdermal');
#EndIf

#IfNotRow list_options list_id physician_type
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','405279007','Attending physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','310172001','Audiological physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309345004','Chest physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','23278007','Community health physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','158967008','Consultant physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','59058001','General physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309358003','Genitourinary medicine physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','158973009','Occupational physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309359006','Palliative care physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309343006','Physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','56466003','Public health physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309360001','Rehabilitation physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','405277009','Resident physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','69280009','Specialized physician');
INSERT INTO list_options (list_id,option_id,title) VALUES ('physician_type','309346003','Thoracic physician');
#EndIf

#IfNotRow list_options list_id marital_status_ccda
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','A','Annulled');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','D','Divorced');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','T','Domestic partner');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','I','Interlocutory');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','L','Legally Separated');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','M','Married');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','S','Never Married');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','P','Polygamous');
INSERT INTO list_options (list_id,option_id,title) VALUES ('marital_status_ccda','W','Widowed');

update patient_data set status='D' where status='divorced';
update patient_data set status='T' where status='domestic partner';
update patient_data set status='M' where status='married';
update patient_data set status='S' where status='single';
update patient_data set status='W' where status='widowed';
#EndIf

#IfMissingColumn users physician_type
ALTER TABLE users ADD COLUMN physician_type VARCHAR(15);
#EndIf

#IfMissingColumn facility facility_code
ALTER TABLE facility ADD COLUMN facility_code VARCHAR(20);
#EndIf

#IfMissingColumn documents imported
ALTER TABLE documents ADD COLUMN imported TINYINT DEFAULT 0 NOT NULL;
#EndIf

#IfMissingColumn documents audit_master_id
ALTER TABLE documents ADD COLUMN audit_master_id INT DEFAULT 0 NOT NULL;
#EndIf

#IfMissingColumn documents audit_master_approval_status
ALTER TABLE documents ADD COLUMN audit_master_approval_status TINYINT DEFAULT 1 NOT NULL COMMENT 'approval_status from audit_master table';
#EndIf

#IfMissingColumn lists severity_al
ALTER TABLE `lists` ADD `severity_al` VARCHAR( 50 ) NULL;
#EndIf

#IfRow2D layout_options field_id race form_id DEM
UPDATE layout_options SET list_id='race_ccda' WHERE field_id='race' AND form_id='DEM';
#EndIf

#IfRow2D layout_options field_id ethnicity form_id DEM
UPDATE layout_options SET list_id='ethnicity_ccda' WHERE field_id='ethnicity' AND form_id='DEM';
#EndIf

#IfRow2D layout_options field_id religion form_id DEM
UPDATE layout_options SET list_id='religious_affiliation' WHERE field_id='religion' AND form_id='DEM';
#EndIf

#IfRow2D layout_options field_id status form_id DEM
UPDATE layout_options SET list_id='marital_status_ccda' WHERE field_id='status' AND form_id='DEM';
#EndIf

#IfNotRow2D list_options list_id abook_type option_id ccda
INSERT INTO list_options (list_id, option_id, title) VALUES ('abook_type', 'ccda', 'Care Coordination');
#EndIf

#IfMissingColumn patient_data religion
SET @group_name = (SELECT group_name FROM layout_options WHERE field_id='ethnicity' AND form_id='DEM');
SET @seq = (SELECT MAX(seq) FROM layout_options WHERE group_name=@group_name AND form_id='DEM');
INSERT INTO `layout_options` (`form_id`, `field_id`, `group_name`, `title`, `seq`, `data_type`, `uor`, `fld_length`, `max_length`, `list_id`, `titlecols`, `datacols`, `default_value`, `edit_options`, `description`) VALUES ('DEM', 'religion', @group_name, 'Religion', @seq+1, 1, 1, 30, 255, 'religious_affiliation', 1, 3, '', '', 'Patient Religion' ) ;
ALTER TABLE patient_data ADD COLUMN religion TEXT NOT NULL;
#EndIf

#IfMissingColumn ccda user_id
ALTER TABLE ccda ADD COLUMN user_id INT NULL;
#EndIf

#IfMissingColumn ccda view
ALTER TABLE ccda ADD COLUMN `view` tinyint(4) NOT NULL DEFAULT '0';
#EndIf

#IfMissingColumn ccda transfer
ALTER TABLE ccda ADD COLUMN `transfer` tinyint(4) NOT NULL DEFAULT '0';
#EndIf

#IfNotTable list_codes
CREATE TABLE `list_codes` (
  `list_code_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `list_code` varchar(255) DEFAULT NULL,
  `icd9_code` varchar(255) DEFAULT NULL,
  `icd10_code` varchar(255) DEFAULT NULL,
  `snomed_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`list_code_id`)
);
#EndIf

#IfMissingColumn prescriptions erx_diagnosis
ALTER TABLE `prescriptions` ADD COLUMN `erx_diagnosis` VARCHAR(10) NULL;
#EndIf

#IfMissingColumn prescriptions erx_diagnosis_name
ALTER TABLE `prescriptions` ADD COLUMN `erx_diagnosis_name` TEXT NULL;
#EndIf

#IfNotTable procedure_subtest_result
CREATE TABLE `procedure_subtest_result` (
  `procedure_subtest_result_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `procedure_report_id` bigint(20) NOT NULL COMMENT 'references procedure_report.procedure_report_id',
  `subtest_code` varchar(30) NOT NULL DEFAULT '',
  `subtest_desc` varchar(255) NOT NULL DEFAULT '',
  `result_value` varchar(255) NOT NULL DEFAULT '',
  `units` varchar(30) NOT NULL DEFAULT '',
  `range` varchar(255) NOT NULL DEFAULT '',
  `abnormal_flag` varchar(31) NOT NULL DEFAULT '' COMMENT 'no,yes,high,low',
  `result_status` varchar(31) NOT NULL DEFAULT '' COMMENT 'preliminary, cannot be done, final, corrected, incompete...etc.',
  `result_time` datetime DEFAULT NULL,
  `providers_id` bigint(20) NOT NULL DEFAULT '0',
  `comments` text NOT NULL COMMENT 'comments of subtest',
  `order_title` varchar(255) DEFAULT NULL,
  `code_suffix` varchar(255) DEFAULT NULL,
  `profile_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`procedure_subtest_result_id`),
  KEY `procedure_report_id` (`procedure_report_id`)
);
#EndIf

#IfMissingColumn procedure_result order_title
ALTER TABLE procedure_result
  ADD COLUMN `order_title`    varchar(255) DEFAULT NULL,
  ADD COLUMN `code_suffix`     varchar(255) DEFAULT NULL,
  ADD COLUMN `profile_title`  varchar(255) DEFAULT NULL;
#EndIf

update patient_data set language='arm' where language='armenian';
update patient_data set language='chi' where language='chinese';
update patient_data set language='dan' where language='danish';
update patient_data set language='eng' where language='English';
update patient_data set language='fre' where language='french';
update patient_data set language='ger' where language='german';
update patient_data set language='grc' where language='greek';
update patient_data set language='gre' where language='greek';
update patient_data set language='hmn' where language='hmong';
update patient_data set language='ita' where language='italian';
update patient_data set language='jpn' where language='japanese';
update patient_data set language='kor' where language='korean';
update patient_data set language='lao' where language='laotian';
update patient_data set language='nno' where language='norwegian';
update patient_data set language='nob' where language='norwegian';
update patient_data set language='por' where language='portuguese';
update patient_data set language='rus' where language='russian';
update patient_data set language='spa' where language='Spanish';
update patient_data set language='tgl' where language='tagalog';
update patient_data set language='tur' where language='turkish';
update patient_data set language='vie' where language='vietnamese';
update patient_data set language='yid' where language='yiddish';
update patient_data set language='zul' where language='zulu';