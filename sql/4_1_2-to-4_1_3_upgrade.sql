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

#IfMissingColumn layout_options list_backup_id
ALTER TABLE `layout_options` ADD COLUMN `list_backup_id` VARCHAR(31) NOT NULL DEFAULT '';
UPDATE `layout_options` SET `list_backup_id` = 'ethrace' WHERE `layout_options`.`form_id` = 'DEM' AND `layout_options`.`field_id` = 'ethnicity';
UPDATE `layout_options` SET `list_backup_id` = 'ethrace' WHERE `layout_options`.`form_id` = 'DEM' AND `layout_options`.`field_id` = 'race';
#EndIf

UPDATE `layout_options` SET `data_type` = '36' WHERE `layout_options`.`form_id` = 'DEM' AND `layout_options`.`field_id` = 'race';
UPDATE `layout_options` SET `data_type` = '1', `datacols` = '3' WHERE `layout_options`.`form_id` = 'DEM' AND `layout_options`.`field_id` = 'language';


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

#IfMissingColumn list_options activity
ALTER TABLE `list_options` ADD COLUMN `activity` TINYINT DEFAULT 1 NOT NULL;
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
  user_id VARCHAR(50) null,
  couch_docid VARCHAR(100) NULL,
  couch_revid VARCHAR(100) NULL,
  `view` tinyint(4) NOT NULL DEFAULT '0',
  `transfer` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY unique_key (pid,encounter,time)
);
#EndIf

#IfNotRow list_options list_id religious_affiliation
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','adventist','1001','Adventist');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','african_religions','1002','African Religions');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','afro-caribbean_religions','1003','Afro-Caribbean Religions');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','agnosticism','1004','Agnosticism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','anglican','1005','Anglican');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','animism','1006','Animism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','assembly_of_god','1061','Assembly of God');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','atheism','1007','Atheism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','babi_bahai_faiths','1008',"Babi & Baha'I faiths");
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','baptist','1009','Baptist');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','bon','1010','Bon');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','brethren','1062','Brethren');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','cao_dai','1011','Cao Dai');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','celticism','1012','Celticism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','christian_non-catholic_non-specific','1013','Christian (non-Catholic, non-specific)');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','christian_scientist','1063','Christian Scientist');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','church_of_christ','1064','Church of Christ');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','church_of_god','1065','Church of God');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','confucianism','1014','Confucianism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','congregational','1066','Congregational');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','cyberculture_religions','1015','Cyberculture Religions');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','disciples_of_christ','1067','Disciples of Christ');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','divination','1016','Divination');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','eastern_orthodox','1068','Eastern Orthodox');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','episcopalian','1069','Episcopalian');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','evangelical_covenant','1070','Evangelical Covenant');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','fourth_way','1017','Fourth Way');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','free_daism','1018','Free Daism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','friends','1071','Friends');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','full_gospel','1072','Full Gospel');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','gnosis','1019','Gnosis');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','hinduism','1020','Hinduism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','humanism','1021','Humanism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','independent','1022','Independent');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','islam','1023','Islam');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','jainism','1024','Jainism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','jehovahs_witnesses','1025',"Jehovah's Witnesses");
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','judaism','1026','Judaism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','latter_day_saints','1027','Latter Day Saints');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','lutheran','1028','Lutheran');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','mahayana','1029','Mahayana');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','meditation','1030','Meditation');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','messianic_judaism','1031','Messianic Judaism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','methodist','1073','Methodist');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','mitraism','1032','Mitraism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','native_american','1074','Native American');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','nazarene','1075','Nazarene');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','new_age','1033','New Age');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','non-roman_catholic','1034','non-Roman Catholic');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','occult','1035','Occult');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','orthodox','1036','Orthodox');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','paganism','1037','Paganism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','pentecostal','1038','Pentecostal');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','presbyterian','1076','Presbyterian');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','process_the','1039','Process, The');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','protestant','1077','Protestant');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','protestant_no_denomination','1078','Protestant, No Denomination');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','reformed','1079','Reformed');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','reformed_presbyterian','1040','Reformed/Presbyterian');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','roman_catholic_church','1041','Roman Catholic Church');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','salvation_army','1080','Salvation Army');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','satanism','1042','Satanism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','scientology','1043','Scientology');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','shamanism','1044','Shamanism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','shiite_islam','1045','Shiite (Islam)');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','shinto','1046','Shinto');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','sikism','1047','Sikism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','spiritualism','1048','Spiritualism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','sunni_islam','1049','Sunni (Islam)');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','taoism','1050','Taoism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','theravada','1051','Theravada');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','unitarian_universalist','1081','Unitarian Universalist');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','unitarian-universalism','1052','Unitarian-Universalism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','united_church_of_christ','1082','United Church of Christ');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','universal_life_church','1053','Universal Life Church');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','vajrayana_tibetan','1054','Vajrayana (Tibetan)');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','veda','1055','Veda');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','voodoo','1056','Voodoo');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','wicca','1057','Wicca');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','yaohushua','1058','Yaohushua');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','zen_buddhism','1059','Zen Buddhism');
INSERT INTO list_options (list_id, option_id, notes,title) VALUES ('religious_affiliation','zoroastrianism','1060','Zoroastrianism');
#EndIf

#IfNotRow2D list_options list_id race option_id abenaki
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','abenaki','1006-6','Abenaki', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','absentee_shawnee','1579-2','Absentee Shawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','acoma','1490-2','Acoma', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','afghanistani','2126-1','Afghanistani', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','african','2060-2','African', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','african_american','2058-6','African American', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','agdaagux','1994-3','Agdaagux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','agua_caliente','1212-0','Agua Caliente', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','agua_caliente_cahuilla','1045-4','Agua Caliente Cahuilla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ahtna','1740-0','Ahtna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ak-chin','1654-3','Ak-Chin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','akhiok','1993-5','Akhiok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','akiachak','1897-8','Akiachak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','akiak','1898-6','Akiak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','akutan','2007-3','Akutan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alabama_coushatta','1187-4','Alabama Coushatta', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alabama_creek','1194-0','Alabama Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alabama_quassarte','1195-7','Alabama Quassarte', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alakanuk','1899-4','Alakanuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alamo_navajo','1383-9','Alamo Navajo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alanvik','1744-2','Alanvik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alaska_indian','1737-6','Alaska Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alaska_native','1735-0','Alaska Native', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alaskan_athabascan','1739-2','Alaskan Athabascan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alatna','1741-8','Alatna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aleknagik','1900-0','Aleknagik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aleut','1966-1','Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aleut_corporation','2008-1','Aleut Corporation', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aleutian','2009-9','Aleutian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aleutian_islander','2010-7','Aleutian Islander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alexander','1742-6','Alexander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','algonquian','1008-2','Algonquian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','allakaket','1743-4','Allakaket', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','allen_canyon','1671-7','Allen Canyon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alpine','1688-1','Alpine', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alsea','1392-0','Alsea', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','alutiiq_aleut','1968-7','Alutiiq Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ambler','1845-7','Ambler', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','american_indian','1004-1','American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','anaktuvuk','1846-5','Anaktuvuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','anaktuvuk_pass','1847-3','Anaktuvuk Pass', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','andreafsky','1901-8','Andreafsky', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','angoon','1814-3','Angoon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aniak','1902-6','Aniak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','anvik','1745-9','Anvik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','apache','1010-8','Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arab','2129-5','Arab', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arapaho','1021-5','Arapaho', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arctic','1746-7','Arctic', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arctic_slope_corporation','1849-9','Arctic Slope Corporation', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arctic_slope_inupiat','1848-1','Arctic Slope Inupiat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arikara','1026-4','Arikara', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','arizona_tewa','1491-0','Arizona Tewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','armenian','2109-7','Armenian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','aroostook','1366-4','Aroostook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','asian_indian','2029-7','Asian Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','assiniboine','1028-0','Assiniboine', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','assiniboine_sioux','1030-6','Assiniboine Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','assyrian','2119-6','Assyrian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','atka','2011-5','Atka', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','atmautluak','1903-4','Atmautluak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','atqasuk','1850-7','Atqasuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','atsina','1265-8','Atsina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','attacapa','1234-4','Attacapa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','augustine','1046-2','Augustine', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bad_river','1124-7','Bad River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bahamian','2067-7','Bahamian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bangladeshi','2030-5','Bangladeshi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bannock','1033-0','Bannock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','barbadian','2068-5','Barbadian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','barrio_libre','1712-9','Barrio Libre', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','barrow','1851-5','Barrow', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','battle_mountain','1587-5','Battle Mountain', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bay_mills_chippewa','1125-4','Bay Mills Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','beaver','1747-5','Beaver', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','belkofski','2012-3','Belkofski', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bering_straits_inupiat','1852-3','Bering Straits Inupiat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bethel','1904-2','Bethel', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bhutanese','2031-3','Bhutanese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','big_cypress','1567-7','Big Cypress', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bill_moores_slough','1905-9',"Bill Moore's Slough", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','biloxi','1235-1','Biloxi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','birch_creek','1748-3','Birch Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bishop','1417-5','Bishop', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','black','2056-0','Black', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','blackfeet','1035-5','Blackfeet', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','blackfoot_sioux','1610-5','Blackfoot Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bois_forte','1126-2','Bois Forte', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','botswanan','2061-0','Botswanan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','brevig_mission','1853-1','Brevig Mission', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bridgeport','1418-3','Bridgeport', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','brighton','1568-5','Brighton', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bristol_bay_aleut','1972-9','Bristol Bay Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','bristol_bay_yupik','1906-7','Bristol Bay Yupik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','brotherton','1037-1','Brotherton', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','brule_sioux','1611-3','Brule Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','buckland','1854-9','Buckland', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','burmese','2032-1','Burmese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','burns_paiute','1419-1','Burns Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','burt_lake_band','1039-7','Burt Lake Band', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','burt_lake_chippewa','1127-0','Burt Lake Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','burt_lake_ottawa','1412-6','Burt Lake Ottawa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cabazon','1047-0','Cabazon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','caddo','1041-3','Caddo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cahto','1054-6','Cahto', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cahuilla','1044-7','Cahuilla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','california_tribes','1053-8','California Tribes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','calista_yupik','1907-5','Calista Yupik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cambodian','2033-9','Cambodian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','campo','1223-7','Campo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','canadian_and_latin_american_indian','1068-6','Canadian and Latin American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','canadian_indian','1069-4','Canadian Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','canoncito_navajo','1384-7','Canoncito Navajo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cantwell','1749-1','Cantwell', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','capitan_grande','1224-5','Capitan Grande', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','carolinian','2092-5','Carolinian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','carson','1689-9','Carson', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','catawba','1076-9','Catawba', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cayuga','1286-4','Cayuga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cayuse','1078-5','Cayuse', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cedarville','1420-9','Cedarville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','celilo','1393-8','Celilo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','central_american_indian','1070-2','Central American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','central_council_of_tlingit_and_haida_tribes','1815-0','Central Council of Tlingit and Haida Tribes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','central_pomo','1465-4','Central Pomo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chalkyitsik','1750-9','Chalkyitsik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chamorro','2088-3','Chamorro', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chefornak','1908-3','Chefornak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chehalis','1080-1','Chehalis', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chemakuan','1082-7','Chemakuan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chemehuevi','1086-8','Chemehuevi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chenega','1985-1','Chenega', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cherokee','1088-4','Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cherokee_alabama','1089-2','Cherokee Alabama', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cherokee_shawnee','1100-7','Cherokee Shawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cherokees_of_northeast_alabama','1090-0','Cherokees of Northeast Alabama', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cherokees_of_southeast_alabama','1091-8','Cherokees of Southeast Alabama', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chevak','1909-1','Chevak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cheyenne','1102-3','Cheyenne', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cheyenne_river_sioux','1612-1','Cheyenne River Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cheyenne-arapaho','1106-4','Cheyenne-Arapaho', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chickahominy','1108-0','Chickahominy', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chickaloon','1751-7','Chickaloon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chickasaw','1112-2','Chickasaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chignik','1973-7','Chignik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chignik_lagoon','2013-1','Chignik Lagoon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chignik_lake','1974-5','Chignik Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chilkat','1816-8','Chilkat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chilkoot','1817-6','Chilkoot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chimariko','1055-3','Chimariko', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chinese','2034-7','Chinese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chinik','1855-6','Chinik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chinook','1114-8','Chinook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chippewa','1123-9','Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chippewa_cree','1150-2','Chippewa Cree', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chiricahua','1011-6','Chiricahua', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chistochina','1752-5','Chistochina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chitimacha','1153-6','Chitimacha', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chitina','1753-3','Chitina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','choctaw','1155-1','Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chuathbaluk','1910-9','Chuathbaluk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chugach_aleut','1984-4','Chugach Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chugach_corporation','1986-9','Chugach Corporation', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chukchansi','1718-6','Chukchansi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chumash','1162-7','Chumash', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','chuukese','2097-4','Chuukese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','circle','1754-1','Circle', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','citizen_band_potawatomi','1479-5','Citizen Band Potawatomi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','clarks_point','1911-7',"Clark's Point", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','clatsop','1115-5','Clatsop', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','clear_lake','1165-0','Clear Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','clifton_choctaw','1156-9','Clifton Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coast_miwok','1056-1','Coast Miwok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coast_yurok','1733-5','Coast Yurok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cochiti','1492-8','Cochiti', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cocopah','1725-1','Cocopah', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coeur_dalene','1167-6',"Coeur D'Alene", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coharie','1169-2','Coharie', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','colorado_river','1171-8','Colorado River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','columbia','1394-6','Columbia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','columbia_river_chinook','1116-3','Columbia River Chinook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','colville','1173-4','Colville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','comanche','1175-9','Comanche', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cook_inlet','1755-8','Cook Inlet', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coos','1180-9','Coos', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coos_lower_umpqua_siuslaw','1178-3','Coos, Lower Umpqua, Siuslaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','copper_center','1756-6','Copper Center', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','copper_river','1757-4','Copper River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coquilles','1182-5','Coquilles', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','costanoan','1184-1','Costanoan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','council','1856-4','Council', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','coushatta','1186-6','Coushatta', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cow_creek_umpqua','1668-3','Cow Creek Umpqua', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cowlitz','1189-0','Cowlitz', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','craig','1818-4','Craig', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cree','1191-6','Cree', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','creek','1193-2','Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','croatan','1207-0','Croatan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','crooked_creek','1912-5','Crooked Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','crow','1209-6','Crow', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','crow_creek_sioux','1613-9','Crow Creek Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cupeno','1211-2','Cupeno', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','cuyapaipe','1225-2','Cuyapaipe', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dakota_sioux','1614-7','Dakota Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','deering','1857-2','Deering', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','delaware','1214-6','Delaware', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','diegueno','1222-9','Diegueno', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','digger','1057-9','Digger', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dillingham','1913-3','Dillingham', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dominica_islander','2070-1','Dominica Islander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dominican','2069-3','Dominican', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dot_lake','1758-2','Dot Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','douglas','1819-2','Douglas', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','doyon','1759-0','Doyon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dresslerville','1690-7','Dresslerville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','dry_creek','1466-2','Dry Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','duck_valley','1603-0','Duck Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','duckwater','1588-3','Duckwater', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','duwamish','1519-8','Duwamish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eagle','1760-8','Eagle', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_cherokee','1092-6','Eastern Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_chickahominy','1109-8','Eastern Chickahominy', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_creek','1196-5','Eastern Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_delaware','1215-3','Eastern Delaware', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_muscogee','1197-3','Eastern Muscogee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_pomo','1467-0','Eastern Pomo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_shawnee','1580-0','Eastern Shawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eastern_tribes','1233-6','Eastern Tribes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','echota_cherokee','1093-4','Echota Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eek','1914-1','Eek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','egegik','1975-2','Egegik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','egyptian','2120-4','Egyptian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eklutna','1761-6','Eklutna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ekuk','1915-8','Ekuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ekwok','1916-6','Ekwok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','elim','1858-0','Elim', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','elko','1589-1','Elko', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ely','1590-9','Ely', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','emmonak','1917-4','Emmonak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','english','2110-5','English', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','english_bay','1987-7','English Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eskimo','1840-8','Eskimo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','esselen','1250-0','Esselen', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ethiopian','2062-8','Ethiopian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','etowah_cherokee','1094-2','Etowah Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','european','2108-9','European', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','evansville','1762-4','Evansville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','eyak','1990-1','Eyak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fallon','1604-8','Fallon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','false_pass','2015-6','False Pass', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fijian','2101-4','Fijian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','filipino','2036-2','Filipino', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','flandreau_santee','1615-4','Flandreau Santee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','florida_seminole','1569-3','Florida Seminole', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fond_du_lac','1128-8','Fond du Lac', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','forest_county','1480-3','Forest County', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_belknap','1252-6','Fort Belknap', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_berthold','1254-2','Fort Berthold', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_bidwell','1421-7','Fort Bidwell', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_hall','1258-3','Fort Hall', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_independence','1422-5','Fort Independence', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_mcdermitt','1605-5','Fort McDermitt', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_mcdowell','1256-7','Fort Mcdowell', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_peck','1616-2','Fort Peck', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_peck_assiniboine_sioux','1031-4','Fort Peck Assiniboine Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_sill_apache','1012-4','Fort Sill Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','fort_yukon','1763-2','Fort Yukon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','french','2111-3','French', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','french_american_indian','1071-0','French American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gabrieleno','1260-9','Gabrieleno', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gakona','1764-0','Gakona', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','galena','1765-7','Galena', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gambell','1892-9','Gambell', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gay_head_wampanoag','1680-8','Gay Head Wampanoag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','georgetown_eastern_tribes','1236-9','Georgetown (Eastern Tribes)', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','georgetown_yupik-eskimo','1962-0','Georgetown (Yupik-Eskimo)', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','german','2112-1','German', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gila_bend','1655-0','Gila Bend', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gila_river_pima-maricopa','1457-1','Gila River Pima-Maricopa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','golovin','1859-8','Golovin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','goodnews_bay','1918-2','Goodnews Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','goshute','1591-7','Goshute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','grand_portage','1129-6','Grand Portage', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','grand_ronde','1262-5','Grand Ronde', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','grand_traverse_band_of_ottawa/chippewa','1130-4','Grand Traverse Band of Ottawa/Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','grayling','1766-5','Grayling', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','greenland_eskimo','1842-4','Greenland Eskimo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gros_ventres','1264-1','Gros Ventres', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','guamanian','2087-5','Guamanian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','guamanian_or_chamorro','2086-7','Guamanian or Chamorro', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','gulkana','1767-3','Gulkana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','haida','1820-0','Haida', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','haitian','2071-9','Haitian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','haliwa','1267-4','Haliwa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hannahville','1481-1','Hannahville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','havasupai','1726-9','Havasupai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','healy_lake','1768-1','Healy Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hidatsa','1269-0','Hidatsa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hmong','2037-0','Hmong', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ho-chunk','1697-2','Ho-chunk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hoh','1083-5','Hoh', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hollywood_seminole','1570-1','Hollywood Seminole', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','holy_cross','1769-9','Holy Cross', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hoonah','1821-8','Hoonah', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hoopa','1271-6','Hoopa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hoopa_extension','1275-7','Hoopa Extension', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hooper_bay','1919-0','Hooper Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hopi','1493-6','Hopi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','houma','1277-3','Houma', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hualapai','1727-7','Hualapai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hughes','1770-7','Hughes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','huron_potawatomi','1482-9','Huron Potawatomi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','huslia','1771-5','Huslia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','hydaburg','1822-6','Hydaburg', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','igiugig','1976-0','Igiugig', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iliamna','1772-3','Iliamna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','illinois_miami','1359-9','Illinois Miami', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','inaja-cosmit','1279-9','Inaja-Cosmit', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','inalik_diomede','1860-6','Inalik Diomede', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','indian_township','1442-3','Indian Township', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','indiana_miami','1360-7','Indiana Miami', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','indonesian','2038-8','Indonesian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','inupiaq','1861-4','Inupiaq', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','inupiat_eskimo','1844-0','Inupiat Eskimo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iowa','1281-5','Iowa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iowa_of_kansas-nebraska','1282-3','Iowa of Kansas-Nebraska', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iowa_of_oklahoma','1283-1','Iowa of Oklahoma', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iowa_sac_and_fox','1552-9','Iowa Sac and Fox', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iqurmuit_russian_mission','1920-8','Iqurmuit (Russian Mission)', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iranian','2121-2','Iranian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iraqi','2122-0','Iraqi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','irish','2113-9','Irish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iroquois','1285-6','Iroquois', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','isleta','1494-4','Isleta', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','israeili','2127-9','Israeili', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','italian','2114-7','Italian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ivanof_bay','1977-8','Ivanof Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','iwo_jiman','2048-7','Iwo Jiman', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','jamaican','2072-7','Jamaican', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','jamestown','1313-6','Jamestown', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','japanese','2039-6','Japanese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','jemez','1495-1','Jemez', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','jena_choctaw','1157-7','Jena Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','jicarilla_apache','1013-2','Jicarilla Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','juaneno','1297-1','Juaneno', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kaibab','1423-3','Kaibab', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kake','1823-4','Kake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kaktovik','1862-2','Kaktovik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kalapuya','1395-3','Kalapuya', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kalispel','1299-7','Kalispel', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kalskag','1921-6','Kalskag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kaltag','1773-1','Kaltag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','karluk','1995-0','Karluk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','karuk','1301-1','Karuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kasaan','1824-2','Kasaan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kashia','1468-8','Kashia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kasigluk','1922-4','Kasigluk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kathlamet','1117-1','Kathlamet', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kaw','1303-7','Kaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kawaiisu','1058-7','Kawaiisu', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kawerak','1863-0','Kawerak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kenaitze','1825-9','Kenaitze', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','keres','1496-9','Keres', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kern_river','1059-5','Kern River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ketchikan','1826-7','Ketchikan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','keweenaw','1131-2','Keweenaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kialegee','1198-1','Kialegee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kiana','1864-8','Kiana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kickapoo','1305-2','Kickapoo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kikiallus','1520-6','Kikiallus', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','king_cove','2014-9','King Cove', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','king_salmon','1978-6','King Salmon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kiowa','1309-4','Kiowa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kipnuk','1923-2','Kipnuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kiribati','2096-6','Kiribati', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kivalina','1865-5','Kivalina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','klallam','1312-8','Klallam', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','klamath','1317-7','Klamath', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','klawock','1827-5','Klawock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kluti_kaah','1774-9','Kluti Kaah', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','knik','1775-6','Knik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kobuk','1866-3','Kobuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kodiak','1996-8','Kodiak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kokhanok','1979-4','Kokhanok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','koliganek','1924-0','Koliganek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kongiganak','1925-7','Kongiganak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','koniag_aleut','1992-7','Koniag Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','konkow','1319-3','Konkow', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kootenai','1321-9','Kootenai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','korean','2040-4','Korean', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kosraean','2093-3','Kosraean', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kotlik','1926-5','Kotlik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kotzebue','1867-1','Kotzebue', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','koyuk','1868-9','Koyuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','koyukuk','1776-4','Koyukuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kwethluk','1927-3','Kwethluk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kwigillingok','1928-1','Kwigillingok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','kwiguk','1869-7','Kwiguk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','la_jolla','1332-6','La Jolla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','la_posta','1226-0','La Posta', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lac_courte_oreilles','1132-0','Lac Courte Oreilles', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lac_du_flambeau','1133-8','Lac du Flambeau', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lac_vieux_desert_chippewa','1134-6','Lac Vieux Desert Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','laguna','1497-7','Laguna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lake_minchumina','1777-2','Lake Minchumina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lake_superior','1135-3','Lake Superior', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lake_traverse_sioux','1617-0','Lake Traverse Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','laotian','2041-2','Laotian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','larsen_bay','1997-6','Larsen Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','las_vegas','1424-1','Las Vegas', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lassik','1323-5','Lassik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lebanese','2123-8','Lebanese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','leech_lake','1136-1','Leech Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lenni-lenape','1216-1','Lenni-Lenape', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','levelock','1929-9','Levelock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','liberian','2063-6','Liberian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lime','1778-0','Lime', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lipan_apache','1014-0','Lipan Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','little_shell_chippewa','1137-9','Little Shell Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lone_pine','1425-8','Lone Pine', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','long_island','1325-0','Long Island', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','los_coyotes','1048-8','Los Coyotes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lovelock','1426-6','Lovelock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_brule_sioux','1618-8','Lower Brule Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_elwha','1314-4','Lower Elwha', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_kalskag','1930-7','Lower Kalskag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_muscogee','1199-9','Lower Muscogee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_sioux','1619-6','Lower Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lower_skagit','1521-4','Lower Skagit', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','luiseno','1331-8','Luiseno', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lumbee','1340-9','Lumbee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','lummi','1342-5','Lummi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','machis_lower_creek_indian','1200-5','Machis Lower Creek Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','madagascar','2052-9','Madagascar', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','maidu','1344-1','Maidu', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','makah','1348-2','Makah', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','malaysian','2042-0','Malaysian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','maldivian','2049-5','Maldivian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','malheur_paiute','1427-4','Malheur Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','maliseet','1350-8','Maliseet', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mandan','1352-4','Mandan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','manley_hot_springs','1780-6','Manley Hot Springs', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','manokotak','1931-5','Manokotak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','manzanita','1227-8','Manzanita', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mariana_islander','2089-1','Mariana Islander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','maricopa','1728-5','Maricopa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','marshall','1932-3','Marshall', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','marshallese','2090-9','Marshallese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','marshantucket_pequot','1454-8','Marshantucket Pequot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','marys_igloo','1889-5',"Mary's Igloo", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mashpee_wampanoag','1681-6','Mashpee Wampanoag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','matinecock','1326-8','Matinecock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mattaponi','1354-0','Mattaponi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mattole','1060-3','Mattole', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mauneluk_inupiat','1870-5','Mauneluk Inupiat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mcgrath','1779-8','Mcgrath', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mdewakanton_sioux','1620-4','Mdewakanton Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mekoryuk','1933-1','Mekoryuk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','melanesian','2100-6','Melanesian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','menominee','1356-5','Menominee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mentasta_lake','1781-4','Mentasta Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mesa_grande','1228-6','Mesa Grande', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mescalero_apache','1015-7','Mescalero Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','metlakatla','1838-2','Metlakatla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mexican_american_indian','1072-8','Mexican American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','miami','1358-1','Miami', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','miccosukee','1363-1','Miccosukee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','michigan_ottawa','1413-4','Michigan Ottawa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','micmac','1365-6','Micmac', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','micronesian','2085-9','Micronesian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','middle_eastern_or_north_african','2118-8','Middle Eastern or North African', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mille_lacs','1138-7','Mille Lacs', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','miniconjou','1621-2','Miniconjou', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','minnesota_chippewa','1139-5','Minnesota Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','minto','1782-2','Minto', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mission_indians','1368-0','Mission Indians', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mississippi_choctaw','1158-5','Mississippi Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','missouri_sac_and_fox','1553-7','Missouri Sac and Fox', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','miwok','1370-6','Miwok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','moapa','1428-2','Moapa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','modoc','1372-2','Modoc', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mohave','1729-3','Mohave', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mohawk','1287-2','Mohawk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mohegan','1374-8','Mohegan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','molala','1396-1','Molala', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mono','1376-3','Mono', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','montauk','1327-6','Montauk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','moor','1237-7','Moor', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','morongo','1049-6','Morongo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mountain_maidu','1345-8','Mountain Maidu', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mountain_village','1934-9','Mountain Village', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','mowa_band_of_choctaw','1159-3','Mowa Band of Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','muckleshoot','1522-2','Muckleshoot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','munsee','1217-9','Munsee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','naknek','1935-6','Naknek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nambe','1498-5','Nambe', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','namibian','2064-4','Namibian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nana_inupiat','1871-3','Nana Inupiat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nansemond','1238-5','Nansemond', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nanticoke','1378-9','Nanticoke', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','napakiak','1937-2','Napakiak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','napaskiak','1938-0','Napaskiak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','napaumute','1936-4','Napaumute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','narragansett','1380-5','Narragansett', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','natchez','1239-3','Natchez', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','native_hawaiian','2079-2','Native Hawaiian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nausu_waiwash','1240-1','Nausu Waiwash', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','navajo','1382-1','Navajo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nebraska_ponca','1475-3','Nebraska Ponca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nebraska_winnebago','1698-0','Nebraska Winnebago', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nelson_lagoon','2016-4','Nelson Lagoon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nenana','1783-0','Nenana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nepalese','2050-3','Nepalese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','new_hebrides','2104-8','New Hebrides', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','new_stuyahok','1940-6','New Stuyahok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','newhalen','1939-8','Newhalen', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','newtok','1941-4','Newtok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nez_perce','1387-0','Nez Perce', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nigerian','2065-1','Nigerian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nightmute','1942-2','Nightmute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nikolai','1784-8','Nikolai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nikolski','2017-2','Nikolski', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ninilchik','1785-5','Ninilchik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nipmuc','1241-9','Nipmuc', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nishinam','1346-6','Nishinam', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nisqually','1523-0','Nisqually', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','noatak','1872-1','Noatak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nomalaki','1389-6','Nomalaki', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nome','1873-9','Nome', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nondalton','1786-3','Nondalton', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nooksack','1524-8','Nooksack', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','noorvik','1874-7','Noorvik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northern_arapaho','1022-3','Northern Arapaho', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northern_cherokee','1095-9','Northern Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northern_cheyenne','1103-1','Northern Cheyenne', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northern_paiute','1429-0','Northern Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northern_pomo','1469-6','Northern Pomo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northway','1787-1','Northway', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','northwest_tribes','1391-2','Northwest Tribes', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nuiqsut','1875-4','Nuiqsut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nulato','1788-9','Nulato', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','nunapitchukv','1943-0','Nunapitchukv', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oglala_sioux','1622-0','Oglala Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','okinawan','2043-8','Okinawan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_apache','1016-5','Oklahoma Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_cado','1042-1','Oklahoma Cado', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_choctaw','1160-1','Oklahoma Choctaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_comanche','1176-7','Oklahoma Comanche', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_delaware','1218-7','Oklahoma Delaware', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_kickapoo','1306-0','Oklahoma Kickapoo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_kiowa','1310-2','Oklahoma Kiowa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_miami','1361-5','Oklahoma Miami', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_ottawa','1414-2','Oklahoma Ottawa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_pawnee','1446-4','Oklahoma Pawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_peoria','1451-4','Oklahoma Peoria', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_ponca','1476-1','Oklahoma Ponca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_sac_and_fox','1554-5','Oklahoma Sac and Fox', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oklahoma_seminole','1571-9','Oklahoma Seminole', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','old_harbor','1998-4','Old Harbor', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','omaha','1403-5','Omaha', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oneida','1288-0','Oneida', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','onondaga','1289-8','Onondaga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ontonagon','1140-3','Ontonagon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oregon_athabaskan','1405-0','Oregon Athabaskan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','osage','1407-6','Osage', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','oscarville','1944-8','Oscarville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','other_pacific_islander','2500-7','Other Pacific Islander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','other_race','2131-1','Other Race', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','otoe-missouria','1409-2','Otoe-Missouria', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ottawa','1411-8','Ottawa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ouzinkie','1999-2','Ouzinkie', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','owens_valley','1430-8','Owens Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','paiute','1416-7','Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pakistani','2044-6','Pakistani', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pala','1333-4','Pala', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','palauan','2091-7','Palauan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','palestinian','2124-6','Palestinian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pamunkey','1439-9','Pamunkey', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','panamint','1592-5','Panamint', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','papua_new_guinean','2102-2','Papua New Guinean', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pascua_yaqui','1713-7','Pascua Yaqui', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','passamaquoddy','1441-5','Passamaquoddy', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','paugussett','1242-7','Paugussett', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pauloff_harbor','2018-0','Pauloff Harbor', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pauma','1334-2','Pauma', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pawnee','1445-6','Pawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','payson_apache','1017-3','Payson Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pechanga','1335-9','Pechanga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pedro_bay','1789-7','Pedro Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pelican','1828-3','Pelican', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','penobscot','1448-0','Penobscot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','peoria','1450-6','Peoria', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pequot','1453-0','Pequot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','perryville','1980-2','Perryville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','petersburg','1829-1','Petersburg', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','picuris','1499-3','Picuris', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pilot_point','1981-0','Pilot Point', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pilot_station','1945-5','Pilot Station', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pima','1456-3','Pima', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pine_ridge_sioux','1623-8','Pine Ridge Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pipestone_sioux','1624-6','Pipestone Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','piro','1500-8','Piro', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','piscataway','1460-5','Piscataway', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pit_river','1462-1','Pit River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pitkas_point','1946-3','Pitkas Point', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','platinum','1947-1','Platinum', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pleasant_point_passamaquoddy','1443-1','Pleasant Point Passamaquoddy', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','poarch_band','1201-3','Poarch Band', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pocomoke_acohonock','1243-5','Pocomoke Acohonock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pohnpeian','2094-1','Pohnpeian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','point_hope','1876-2','Point Hope', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','point_lay','1877-0','Point Lay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pojoaque','1501-6','Pojoaque', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pokagon_potawatomi','1483-7','Pokagon Potawatomi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','polish','2115-4','Polish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','polynesian','2078-4','Polynesian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pomo','1464-7','Pomo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ponca','1474-6','Ponca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','poospatuck','1328-4','Poospatuck', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','port_gamble_klallam','1315-1','Port Gamble Klallam', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','port_graham','1988-5','Port Graham', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','port_heiden','1982-8','Port Heiden', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','port_lions','2000-8','Port Lions', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','port_madison','1525-5','Port Madison', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','portage_creek','1948-9','Portage Creek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','potawatomi','1478-7','Potawatomi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','powhatan','1487-8','Powhatan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','prairie_band','1484-5','Prairie Band', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','prairie_island_sioux','1625-3','Prairie Island Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','principal_creek_indian_nation','1202-1','Principal Creek Indian Nation', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','prior_lake_sioux','1626-1','Prior Lake Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pueblo','1489-4','Pueblo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','puget_sound_salish','1518-0','Puget Sound Salish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','puyallup','1526-3','Puyallup', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','pyramid_lake','1431-6','Pyramid Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','qagan_toyagungin','2019-8','Qagan Toyagungin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','qawalangin','2020-6','Qawalangin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','quapaw','1541-2','Quapaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','quechan','1730-1','Quechan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','quileute','1084-3','Quileute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','quinault','1543-8','Quinault', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','quinhagak','1949-7','Quinhagak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ramah_navajo','1385-4','Ramah Navajo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','rampart','1790-5','Rampart', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','rampough_mountain','1219-5','Rampough Mountain', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','rappahannock','1545-3','Rappahannock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','red_cliff_chippewa','1141-1','Red Cliff Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','red_devil','1950-5','Red Devil', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','red_lake_chippewa','1142-9','Red Lake Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','red_wood','1061-1','Red Wood', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','reno-sparks','1547-9','Reno-Sparks', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','rocky_boys_chippewa_cree','1151-0',"Rocky Boy's Chippewa Cree", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','rosebud_sioux','1627-9','Rosebud Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','round_valley','1549-5','Round Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ruby','1791-3','Ruby', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ruby_valley','1593-3','Ruby Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sac_and_fox','1551-1','Sac and Fox', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','saginaw_chippewa','1143-7','Saginaw Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','saipanese','2095-8','Saipanese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','salamatof','1792-1','Salamatof', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','salinan','1556-0','Salinan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','salish','1558-6','Salish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','salish_and_kootenai','1560-2','Salish and Kootenai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','salt_river_pima-maricopa','1458-9','Salt River Pima-Maricopa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','samish','1527-1','Samish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','samoan','2080-0','Samoan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_carlos_apache','1018-1','San Carlos Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_felipe','1502-4','San Felipe', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_ildefonso','1503-2','San Ildefonso', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_juan','1506-5','San Juan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_juan_de','1505-7','San Juan De', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_juan_pueblo','1504-0','San Juan Pueblo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_juan_southern_paiute','1432-4','San Juan Southern Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_manual','1574-3','San Manual', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_pasqual','1229-4','San Pasqual', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','san_xavier','1656-8','San Xavier', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sand_hill','1220-3','Sand Hill', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sand_point','2023-0','Sand Point', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sandia','1507-3','Sandia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sans_arc_sioux','1628-7','Sans Arc Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_ana','1508-1','Santa Ana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_clara','1509-9','Santa Clara', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_rosa','1062-9','Santa Rosa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_rosa_cahuilla','1050-4','Santa Rosa Cahuilla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_ynez','1163-5','Santa Ynez', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santa_ysabel','1230-2','Santa Ysabel', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santee_sioux','1629-5','Santee Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','santo_domingo','1510-7','Santo Domingo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sauk-suiattle','1528-9','Sauk-Suiattle', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sault_ste_marie_chippewa','1145-2','Sault Ste. Marie Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','savoonga','1893-7','Savoonga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','saxman','1830-9','Saxman', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','scammon_bay','1952-1','Scammon Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','schaghticoke','1562-8','Schaghticoke', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','scott_valley','1564-4','Scott Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','scottish','2116-2','Scottish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','scotts_valley','1470-4','Scotts Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','selawik','1878-8','Selawik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','seldovia','1793-9','Seldovia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sells','1657-6','Sells', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','seminole','1566-9','Seminole', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','seneca','1290-6','Seneca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','seneca_nation','1291-4','Seneca Nation', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','seneca-cayuga','1292-2','Seneca-Cayuga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','serrano','1573-5','Serrano', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','setauket','1329-2','Setauket', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shageluk','1795-4','Shageluk', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shaktoolik','1879-6','Shaktoolik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shasta','1576-8','Shasta', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shawnee','1578-4','Shawnee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sheldons_point','1953-9',"Sheldon's Point", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shinnecock','1582-6','Shinnecock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shishmaref','1880-4','Shishmaref', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shoalwater_bay','1584-2','Shoalwater Bay', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shoshone','1586-7','Shoshone', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shoshone_paiute','1602-2','Shoshone Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','shungnak','1881-2','Shungnak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','siberian_eskimo','1891-1','Siberian Eskimo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','siberian_yupik','1894-5','Siberian Yupik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','siletz','1607-1','Siletz', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','singaporean','2051-1','Singaporean', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sioux','1609-7','Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sisseton_sioux','1631-1','Sisseton Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sisseton-wahpeton','1630-3','Sisseton-Wahpeton', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sitka','1831-7','Sitka', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','siuslaw','1643-6','Siuslaw', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','skokomish','1529-7','Skokomish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','skull_valley','1594-1','Skull Valley', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','skykomish','1530-5','Skykomish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','slana','1794-7','Slana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sleetmute','1954-7','Sleetmute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','snohomish','1531-3','Snohomish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','snoqualmie','1532-1','Snoqualmie', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','soboba','1336-7','Soboba', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sokoagon_chippewa','1146-0','Sokoagon Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','solomon','1882-0','Solomon', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','solomon_islander','2103-0','Solomon Islander', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','south_american_indian','1073-6','South American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','south_fork_shoshone','1595-8','South Fork Shoshone', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','south_naknek','2024-8','South Naknek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','southeast_alaska','1811-9','Southeast Alaska', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','southeastern_indians','1244-3','Southeastern Indians', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','southern_arapaho','1023-1','Southern Arapaho', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','southern_cheyenne','1104-9','Southern Cheyenne', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','southern_paiute','1433-2','Southern Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','spanish_american_indian','1074-4','Spanish American Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','spirit_lake_sioux','1632-9','Spirit Lake Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','spokane','1645-1','Spokane', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','squaxin_island','1533-9','Squaxin Island', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sri_lankan','2045-3','Sri Lankan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','st_croix_chippewa','1144-5','St. Croix Chippewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','st_george','2021-4','St. George', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','st_marys','1963-8',"St. Mary's", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','st_michael','1951-3','St. Michael', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','st_paul','2022-2','St. Paul', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','standing_rock_sioux','1633-7','Standing Rock Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','star_clan_of_muscogee_creeks','1203-9','Star Clan of Muscogee Creeks', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stebbins','1955-4','Stebbins', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','steilacoom','1534-7','Steilacoom', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stevens','1796-2','Stevens', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stewart','1647-7','Stewart', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stillaguamish','1535-4','Stillaguamish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stockbridge','1649-3','Stockbridge', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stony_river','1797-0','Stony River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','stonyford','1471-2','Stonyford', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sugpiaq','2002-4','Sugpiaq', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sulphur_bank','1472-0','Sulphur Bank', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','summit_lake','1434-0','Summit Lake', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','suqpigaq','2004-0','Suqpigaq', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','suquamish','1536-2','Suquamish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','susanville','1651-9','Susanville', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','susquehanock','1245-0','Susquehanock', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','swinomish','1537-0','Swinomish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','sycuan','1231-0','Sycuan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','syrian','2125-3','Syrian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','table_bluff','1705-3','Table Bluff', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tachi','1719-4','Tachi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tahitian','2081-8','Tahitian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','taiwanese','2035-4','Taiwanese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','takelma','1063-7','Takelma', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','takotna','1798-8','Takotna', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','talakamish','1397-9','Talakamish', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tanacross','1799-6','Tanacross', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tanaina','1800-2','Tanaina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tanana','1801-0','Tanana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tanana_chiefs','1802-8','Tanana Chiefs', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','taos','1511-5','Taos', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tatitlek','1969-5','Tatitlek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tazlina','1803-6','Tazlina', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','telida','1804-4','Telida', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','teller','1883-8','Teller', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','temecula','1338-3','Temecula', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','te-moak_western_shoshone','1596-6','Te-Moak Western Shoshone', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tenakee_springs','1832-5','Tenakee Springs', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tenino','1398-7','Tenino', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tesuque','1512-3','Tesuque', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tetlin','1805-1','Tetlin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','teton_sioux','1634-5','Teton Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tewa','1513-1','Tewa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','texas_kickapoo','1307-8','Texas Kickapoo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','thai','2046-1','Thai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','thlopthlocco','1204-7','Thlopthlocco', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tigua','1514-9','Tigua', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tillamook','1399-5','Tillamook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','timbi-sha_shoshone','1597-4','Timbi-Sha Shoshone', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tlingit','1833-3','Tlingit', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tlingit-haida','1813-5','Tlingit-Haida', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tobagoan','2073-5','Tobagoan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','togiak','1956-2','Togiak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tohono_oodham','1653-5',"Tohono O'Odham", '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tok','1806-9','Tok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tokelauan','2083-4','Tokelauan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','toksook','1957-0','Toksook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tolowa','1659-2','Tolowa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tonawanda_seneca','1293-0','Tonawanda Seneca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tongan','2082-6','Tongan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tonkawa','1661-8','Tonkawa', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','torres-martinez','1051-2','Torres-Martinez', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','trinidadian','2074-3','Trinidadian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','trinity','1272-4','Trinity', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tsimshian','1837-4','Tsimshian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tuckabachee','1205-4','Tuckabachee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tulalip','1538-8','Tulalip', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tule_river','1720-2','Tule River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tulukskak','1958-8','Tulukskak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tunica_biloxi','1246-8','Tunica Biloxi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tuntutuliak','1959-6','Tuntutuliak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tununak','1960-4','Tununak', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','turtle_mountain','1147-8','Turtle Mountain', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tuscarora','1294-8','Tuscarora', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tuscola','1096-7','Tuscola', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','twenty-nine_palms','1337-5','Twenty-Nine Palms', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','twin_hills','1961-2','Twin Hills', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','two_kettle_sioux','1635-2','Two Kettle Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tygh','1663-4','Tygh', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','tyonek','1807-7','Tyonek', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ugashik','1970-3','Ugashik', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','uintah_ute','1672-5','Uintah Ute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','umatilla','1665-9','Umatilla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','umkumiate','1964-6','Umkumiate', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','umpqua','1667-5','Umpqua', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','unalakleet','1884-6','Unalakleet', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','unalaska','2025-5','Unalaska', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','unangan_aleut','2006-5','Unangan Aleut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','unga','2026-3','Unga', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','united_keetowah_band_of_cherokee','1097-5','United Keetowah Band of Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','upper_chinook','1118-9','Upper Chinook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','upper_sioux','1636-0','Upper Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','upper_skagit','1539-6','Upper Skagit', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ute','1670-9','Ute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','ute_mountain_ute','1673-3','Ute Mountain Ute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','utu_utu_gwaitu_paiute','1435-7','Utu Utu Gwaitu Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','venetie','1808-5','Venetie', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','vietnamese','2047-9','Vietnamese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','waccamaw-siousan','1247-6','Waccamaw-Siousan', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wahpekute_sioux','1637-8','Wahpekute Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wahpeton_sioux','1638-6','Wahpeton Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wailaki','1675-8','Wailaki', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wainwright','1885-3','Wainwright', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wakiakum_chinook','1119-7','Wakiakum Chinook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wales','1886-1','Wales', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','walker_river','1436-5','Walker River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','walla-walla','1677-4','Walla-Walla', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wampanoag','1679-0','Wampanoag', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wappo','1064-5','Wappo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','warm_springs','1683-2','Warm Springs', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wascopum','1685-7','Wascopum', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','washakie','1598-2','Washakie', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','washoe','1687-3','Washoe', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wazhaza_sioux','1639-4','Wazhaza Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wenatchee','1400-1','Wenatchee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','west_indian','2075-0','West Indian', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','western_cherokee','1098-3','Western Cherokee', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','western_chickahominy','1110-6','Western Chickahominy', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','whilkut','1273-2','Whilkut', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','white','2106-3','White', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','white_earth','1148-6','White Earth', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','white_mountain','1887-9','White Mountain', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','white_mountain_apache','1019-9','White Mountain Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','white_mountain_inupiat','1888-7','White Mountain Inupiat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wichita','1692-3','Wichita', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wicomico','1248-4','Wicomico', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','willapa_chinook','1120-5','Willapa Chinook', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wind_river','1694-9','Wind River', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wind_river_arapaho','1024-9','Wind River Arapaho', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wind_river_shoshone','1599-0','Wind River Shoshone', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','winnebago','1696-4','Winnebago', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','winnemucca','1700-4','Winnemucca', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wintun','1702-0','Wintun', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wisconsin_potawatomi','1485-2','Wisconsin Potawatomi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wiseman','1809-3','Wiseman', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wishram','1121-3','Wishram', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wiyot','1704-6','Wiyot', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wrangell','1834-1','Wrangell', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','wyandotte','1295-5','Wyandotte', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yahooskin','1401-9','Yahooskin', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yakama','1707-9','Yakama', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yakama_cowlitz','1709-5','Yakama Cowlitz', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yakutat','1835-8','Yakutat', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yana','1065-2','Yana', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yankton_sioux','1640-2','Yankton Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yanktonai_sioux','1641-0','Yanktonai Sioux', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yapese','2098-2','Yapese', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yaqui','1711-1','Yaqui', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yavapai','1731-9','Yavapai', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yavapai_apache','1715-2','Yavapai Apache', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yerington_paiute','1437-3','Yerington Paiute', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yokuts','1717-8','Yokuts', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yomba','1600-6','Yomba', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yuchi','1722-8','Yuchi', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yuki','1066-0','Yuki', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yuman','1724-4','Yuman', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yupik_eskimo','1896-0','Yupik Eskimo', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','yurok','1732-7','Yurok', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','zairean','2066-9','Zairean', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','zia','1515-6','Zia', '0', '60');
INSERT INTO list_options (list_id, option_id, notes, title, activity, seq) VALUES ('race','zuni','1516-4','Zuni', '0', '60');
#EndIf

#IfMissingColumn lists severity_al
ALTER TABLE lists ADD COLUMN severity_al VARCHAR(50) NULL;
#EndIf

#IfNotRow list_options list_id severity_ccda
INSERT INTO list_options (list_id, option_id, title) VALUES ('lists','severity_ccda','Severity');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','fatal','Fatal','399166001');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','life_threatening_severity','Life threatening severity','442452003');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','mild','Mild','255604002');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','mild_to_moderate','Mild to moderate','371923003');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','moderate','Moderate','6736007');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','moderate_to_severe','Moderate to severe','371924009');
INSERT INTO list_options (list_id, option_id, title, codes) values ('severity_ccda','severe','Severe','24484000');
#EndIf

#IfRow2D list_options list_id drug_route option_id 1
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '1';
#EndIf

#IfRow2D list_options list_id drug_route option_id 2
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '2';
#EndIf

#IfRow2D list_options list_id drug_route option_id 3
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '3';
#EndIf

#IfRow2D list_options list_id drug_route option_id 4
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '4';
#EndIf

#IfRow2D list_options list_id drug_route option_id 5
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '5';
#EndIf

#IfRow2D list_options list_id drug_route option_id 6
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '6';
#EndIf

#IfRow2D list_options list_id drug_route option_id 7
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '7';
#EndIf

#IfRow2D list_options list_id drug_route option_id 8
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '8';
#EndIf

#IfRow2D list_options list_id drug_route option_id 9
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '9';
#EndIf

#IfRow2D list_options list_id drug_route option_id 10
UPDATE list_options SET list_options.notes = 'IM' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '10';
#EndIf

#IfRow2D list_options list_id drug_route option_id 11
UPDATE list_options SET list_options.notes = 'IV' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '11';
#EndIf

#IfRow2D list_options list_id drug_route option_id 12
UPDATE list_options SET list_options.notes = 'NS' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '12';
#EndIf

#IfRow2D list_options list_id drug_route option_id 13
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '13';
#EndIf

#IfRow2D list_options list_id drug_route option_id 14
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '14';
#EndIf

#IfRow2D list_options list_id drug_route option_id 15
UPDATE list_options SET list_options.notes = 'OTH' WHERE list_options.list_id = 'drug_route' AND list_options.option_id = '15';
#EndIf

#IfNotRow2Dx2 list_options list_id drug_route option_id intradermal title Intradermal
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, notes ) VALUES ('drug_route', 'intradermal', 'Intradermal', 16, 0, 'ID');
#EndIf

#IfNotRow2Dx2 list_options list_id drug_route option_id oral title Oral
INSERT INTO list_options( list_id, option_id, title, seq, is_default, notes ) VALUES ('drug_route', 'oral', 'Oral', 17, 0, 'PO');
#EndIf

#IfNotRow2Dx2 list_options list_id drug_route option_id other title Other/Miscellaneous
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, notes ) VALUES ('drug_route', 'other', 'Other/Miscellaneous', 17, 0, 'OTH');
#EndIf

#IfNotRow2Dx2 list_options list_id drug_route option_id subcutaneous title Subcutaneous
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, notes ) VALUES ('drug_route', 'subcutaneous', 'Subcutaneous', 19, 0, 'SC');
#EndIf

#IfNotRow2Dx2 list_options list_id drug_route option_id transdermal title Transdermal
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, notes ) VALUES ('drug_route', 'transdermal', 'Transdermal', 20, 0, 'TD');
#EndIf

#IfNotRow list_options list_id physician_type
INSERT INTO list_options (list_id,option_id,title) VALUES ('lists','physician_type','Physician Type');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','attending_physician_occupation','405279007','Attending physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','audiological_physician_occupation','310172001','Audiological physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','chest_physician_occupation','309345004','Chest physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','community_health_physician_occupation','23278007','Community health physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','consultant_physician_occupation','158967008','Consultant physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','general_physician_occupation','59058001','General physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','genitourinary_medicine_physician_occupation','309358003','Genitourinary medicine physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','occupational_physician_occupation','158973009','Occupational physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','palliative_care_physician_occupation','309359006','Palliative care physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','physician_occupation','309343006','Physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','public_health_physician_occupation','56466003','Public health physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','rehabilitation_physician_occupation','309360001','Rehabilitation physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','resident_physician_occupation','405277009','Resident physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','specialized_physician_occupation','69280009','Specialized physician');
INSERT INTO list_options (list_id, option_id, codes,title) VALUES ('physician_type','thoracic_physician_occupation','309346003','Thoracic physician');
#EndIf

#IfNotRow3D list_options list_id marital option_id married notes M
update list_options set notes = 'M' where list_id = 'marital' and option_id = 'married';
#EndIf

#IfNotRow3D list_options list_id marital option_id single notes S
update list_options set notes = 'S' where list_id = 'marital' and option_id = 'single';
#EndIf

#IfNotRow3D list_options list_id marital option_id divorced notes D
update list_options set notes = 'D' where list_id = 'marital' and option_id = 'divorced';
#EndIf

#IfNotRow3D list_options list_id marital option_id widowed notes W
update list_options set notes = 'W' where list_id = 'marital' and option_id = 'widowed';
#EndIf

#IfNotRow3D list_options list_id marital option_id separated notes L
update list_options set notes = 'L' where list_id = 'marital' and option_id = 'separated';
#EndIf

#IfNotRow3D list_options list_id marital seq 6 notes T
update list_options set notes = 'T' where list_id = 'marital' and option_id = 'domestic partner';
#EndIf

#IfMissingColumn users physician_type
ALTER TABLE users ADD COLUMN physician_type VARCHAR(50);
#EndIf

#IfMissingColumn facility facility_code
ALTER TABLE facility ADD COLUMN facility_code VARCHAR(20);
#EndIf

#IfMissingColumn documents audit_master_approval_status
ALTER TABLE documents ADD COLUMN audit_master_approval_status TINYINT DEFAULT 1 NOT NULL COMMENT 'approval_status from audit_master table';
#EndIf

#IfMissingColumn documents audit_master_id
ALTER TABLE documents ADD COLUMN  audit_master_id int(11) default NULL;
#EndIf

#IfMissingColumn lists severity_al
ALTER TABLE `lists` ADD `severity_al` VARCHAR( 50 ) NULL;
#EndIf

#IfRow2D layout_options field_id religion form_id DEM
UPDATE layout_options SET list_id='religious_affiliation' WHERE field_id='religion' AND form_id='DEM';
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