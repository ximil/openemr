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

#IfNotTable report_results
CREATE TABLE `report_results` (
  `report_id` bigint(20) NOT NULL,
  `field_id` varchar(31) NOT NULL default '',
  `field_value` text,
  PRIMARY KEY (`report_id`,`field_id`)
) ENGINE=MyISAM;
#EndIf

#IfMissingColumn version v_acl
ALTER TABLE `version` ADD COLUMN `v_acl` int(11) NOT NULL DEFAULT 0;
#EndIf

#IfMissingColumn documents_legal_detail dld_moved
ALTER TABLE `documents_legal_detail` ADD COLUMN `dld_moved` tinyint(4) NOT NULL DEFAULT '0'; 
#EndIf

#IfMissingColumn documents_legal_detail dld_patient_comments
ALTER TABLE `documents_legal_detail` ADD COLUMN `dld_patient_comments` text COMMENT 'Patient comments stored here';
#EndIf

#IfMissingColumn documents_legal_master dlm_upload_type
ALTER TABLE `documents_legal_master` ADD COLUMN `dlm_upload_type` tinyint(4) DEFAULT '0' COMMENT '0-Provider Uploaded,1-Patient Uploaded';
#EndIf

#IfMissingColumn list_options codes
ALTER TABLE `list_options` ADD COLUMN `codes` varchar(255) NOT NULL DEFAULT '';
UPDATE list_options SET `codes`='SNOMED-CT:449868002' WHERE list_id='smoking_status' AND option_id='1' AND title='Current every day smoker';
UPDATE list_options SET `codes`='SNOMED-CT:428041000124106' WHERE list_id='smoking_status' AND option_id='2' AND title='Current some day smoker';
UPDATE list_options SET `codes`='SNOMED-CT:8517006' WHERE list_id='smoking_status' AND option_id='3' AND title='Former smoker';
UPDATE list_options SET `codes`='SNOMED-CT:266919005' WHERE list_id='smoking_status' AND option_id='4' AND title='Never smoker';
UPDATE list_options SET `codes`='SNOMED-CT:77176002' WHERE list_id='smoking_status' AND option_id='5' AND title='Smoker, current status unknown';
UPDATE list_options SET `codes`='SNOMED-CT:266927001' WHERE list_id='smoking_status' AND option_id='9' AND title='Unknown if ever smoked';
#EndIf

#IfNotRow2Dx2 list_options list_id smoking_status option_id 15 title Heavy tobacco smoker
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, codes ) VALUES ('smoking_status', '15', 'Heavy tobacco smoker', 70, 0, "SNOMED-CT:428071000124103");
#EndIf

#IfNotRow2Dx2 list_options list_id smoking_status option_id 16 title Light tobacco smoker
INSERT INTO list_options ( list_id, option_id, title, seq, is_default, codes ) VALUES ('smoking_status', '16', 'Light tobacco smoker', 80, 0, "SNOMED-CT:428061000124105");
#EndIf

#IfMissingColumn code_types ct_term
ALTER TABLE `code_types` ADD COLUMN ct_term tinyint(1) NOT NULL default 0 COMMENT '1 if this is a clinical term';
#EndIf

#IfNotRow code_types ct_key SNOMED-CT
DROP TABLE IF EXISTS `temp_table_one`;
CREATE TABLE `temp_table_one` (
  `id` int(11) NOT NULL DEFAULT '0',
  `seq` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM ;
INSERT INTO `temp_table_one` (`id`, `seq`) VALUES ( IF( ((SELECT MAX(`ct_id`) FROM `code_types`)>=100), ((SELECT MAX(`ct_id`) FROM `code_types`) + 1), 100 ) , IF( ((SELECT MAX(`ct_seq`) FROM `code_types`)>=100), ((SELECT MAX(`ct_seq`) FROM `code_types`) + 1), 100 )  );
INSERT INTO code_types (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag, ct_active, ct_label, ct_external, ct_claim, ct_proc, ct_term ) VALUES ('SNOMED-CT' , (SELECT MAX(`id`) FROM `temp_table_one`), (SELECT MAX(`seq`) FROM `temp_table_one`), 0, '', 0, 0, 1, 0, 0, 'SNOMED Clinical Term', 7, 0, 0, 1);
DROP TABLE `temp_table_one`;
#EndIf

#IfNotColumnType codes code varchar(25)
ALTER TABLE `codes` CHANGE `code` `code` varchar(25) NOT NULL default '';
#EndIf

#IfNotColumnType billing code varchar(20)
ALTER TABLE `billing` CHANGE `code` `code` varchar(20) default NULL;
#EndIf

#IfNotColumnType ar_activity code varchar(20)
ALTER TABLE `ar_activity` CHANGE `code` `code` varchar(20) NOT NULL COMMENT 'empty means claim level';
#EndIf

#IfNotTable procedure_questions
CREATE TABLE `procedure_questions` (
  `lab_id`              bigint(20)   NOT NULL DEFAULT 0   COMMENT 'references users.id to identify the lab',
  `procedure_code`      varchar(31)  NOT NULL DEFAULT ''  COMMENT 'references procedure_type.procedure_code to identify this order type',
  `question_code`       varchar(31)  NOT NULL DEFAULT ''  COMMENT 'code identifying this question',
  `seq`                 int(11)      NOT NULL default 0   COMMENT 'sequence number for ordering',
  `question_text`       varchar(255) NOT NULL DEFAULT ''  COMMENT 'descriptive text for question_code',
  `required`            tinyint(1)   NOT NULL DEFAULT 0   COMMENT '1 = required, 0 = not',
  `maxsize`             int          NOT NULL DEFAULT 0   COMMENT 'maximum length if text input field',
  `fldtype`             char(1)      NOT NULL DEFAULT 'T' COMMENT 'Text, Number, Select, Multiselect, Date, Gestational-age',
  `options`             text         NOT NULL DEFAULT ''  COMMENT 'choices for fldtype S and T',
  `activity`            tinyint(1)   NOT NULL DEFAULT 1   COMMENT '1 = active, 0 = inactive',
  PRIMARY KEY (`lab_id`, `procedure_code`, `question_code`)
) ENGINE=MyISAM;
#EndIf

#IfMissingColumn procedure_type activity
ALTER TABLE `procedure_type` ADD COLUMN `activity` tinyint(1) NOT NULL default 1;
#EndIf

#IfMissingColumn procedure_type notes
ALTER TABLE `procedure_type` ADD COLUMN `notes` varchar(255) NOT NULL default '';
#EndIf

#IfNotTable procedure_answers
CREATE TABLE `procedure_answers` (
  `procedure_order_id`  bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references procedure_order.procedure_order_id',
  `procedure_order_seq` int(11)      NOT NULL DEFAULT 1  COMMENT 'references procedure_order_code.seq',
  `question_code`       varchar(31)  NOT NULL DEFAULT '' COMMENT 'references procedure_questions.question_code',
  `answer_seq`          int(11)      NOT NULL AUTO_INCREMENT COMMENT 'supports multiple-choice questions',
  `answer`              varchar(255) NOT NULL DEFAULT '' COMMENT 'answer data',
  PRIMARY KEY (`procedure_order_id`, `procedure_order_seq`, `question_code`, `answer_seq`)
) ENGINE=MyISAM;
#EndIf

#IfNotTable procedure_providers
CREATE TABLE `procedure_providers` (
  `ppid`         bigint(20)   NOT NULL auto_increment,
  `name`         varchar(255) NOT NULL DEFAULT '',
  `npi`          varchar(15)  NOT NULL DEFAULT '',
  `protocol`     varchar(15)  NOT NULL DEFAULT 'DL',
  `login`        varchar(255) NOT NULL DEFAULT '',
  `password`     varchar(255) NOT NULL DEFAULT '',
  `orders_path`  varchar(255) NOT NULL DEFAULT '',
  `results_path` varchar(255) NOT NULL DEFAULT '',
  `notes`        text         NOT NULL DEFAULT '',
  PRIMARY KEY (`ppid`)
) ENGINE=MyISAM;
#EndIf

#IfNotTable procedure_order_code
CREATE TABLE `procedure_order_code` (
  `procedure_order_id`  bigint(20)  NOT NULL,
  `procedure_order_seq` int(11)     NOT NULL AUTO_INCREMENT COMMENT 'supports multiple tests per order',
  `procedure_type_id`   bigint(20)  NOT NULL                COMMENT 'references procedure_type.procedure_type_id',
  `procedure_code`      varchar(31) NOT NULL DEFAULT ''     COMMENT 'copy of procedure_type.procedure_code',
  PRIMARY KEY (`procedure_order_id`, `procedure_order_seq`)
) ENGINE=MyISAM;
INSERT INTO procedure_order_code
  SELECT po.procedure_order_id, 1, po.procedure_type_id, pt.procedure_code
  FROM procedure_order AS po
  LEFT JOIN procedure_type AS pt ON pt.procedure_type_id = po.procedure_type_id;
ALTER TABLE `procedure_order`
  DROP COLUMN `procedure_type_id`;
#EndIf

#IfMissingColumn procedure_order lab_id
ALTER TABLE `procedure_order`
  ADD COLUMN `lab_id`            bigint(20)   NOT NULL DEFAULT 0  COMMENT 'references procedure_providers.ppid',
  ADD COLUMN `specimen_type`     varchar(31)  NOT NULL DEFAULT '' COMMENT 'from the Specimen_Type list',
  ADD COLUMN `specimen_location` varchar(31)  NOT NULL DEFAULT '' COMMENT 'from the Specimen_Location list',
  ADD COLUMN `specimen_volume`   varchar(30)  NOT NULL DEFAULT '' COMMENT 'from a text input field';
UPDATE procedure_order AS po, procedure_order_code AS pc, procedure_type AS pt
  SET po.lab_id = pt.lab_id WHERE
  po.lab_id = 0 AND
  pc.procedure_order_id = po.procedure_order_id AND
  pt.procedure_type_id = pc.procedure_type_id AND
  pt.lab_id != 0;
#EndIf

#IfMissingColumn procedure_report procedure_order_seq
ALTER TABLE procedure_report
  ADD COLUMN `procedure_order_seq` int(11) NOT NULL DEFAULT 1 COMMENT 'references procedure_order_code.procedure_order_seq';
#EndIf

#IfMissingColumn procedure_order diagnoses
ALTER TABLE `procedure_order`
  ADD COLUMN `diagnoses` text NOT NULL DEFAULT '' COMMENT 'diagnoses and maybe other coding (e.g. ICD9:111.11)';
#EndIf

#IfMissingColumn procedure_providers remote_host
ALTER TABLE `procedure_providers`
  ADD COLUMN `remote_host` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'IP or hostname of remote server',
  ADD COLUMN `send_app_id` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'Sending application ID (MSH-3.1)',
  ADD COLUMN `send_fac_id` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'Sending facility ID (MSH-4.1)',
  ADD COLUMN `recv_app_id` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'Receiving application ID (MSH-5.1)',
  ADD COLUMN `recv_fac_id` varchar(255)  NOT NULL DEFAULT ''  COMMENT 'Receiving facility ID (MSH-6.1)',
  ADD COLUMN `DorP`        char(1)       NOT NULL DEFAULT 'D' COMMENT 'Debugging or Production (MSH-11)';
#EndIf

#IfMissingColumn procedure_order_code procedure_source
ALTER TABLE `procedure_order_code`
  ADD COLUMN `procedure_source` char(1) NOT NULL DEFAULT '1' COMMENT '1=original order, 2=added after order sent';
#EndIf

#IfMissingColumn procedure_result result_code
ALTER TABLE `procedure_result`
  ADD COLUMN `result_data_type` char(1) NOT NULL DEFAULT 'S' COMMENT
  'N=Numeric, S=String, F=Formatted, E=External, L=Long text as first line of comments',
  ADD COLUMN `result_code` varchar(31) NOT NULL DEFAULT '' COMMENT
  'LOINC code, might match a procedure_type.procedure_code',
  ADD COLUMN `result_text` varchar(255) NOT NULL DEFAULT '' COMMENT
  'Description of result_code';
# This severs the link between procedure_result and procedure_type:
UPDATE procedure_result AS ps, procedure_type AS pt
  SET ps.result_code = pt.procedure_code, ps.result_text = pt.description
  WHERE pt.procedure_type_id = ps.procedure_type_id;
ALTER TABLE `procedure_result` DROP COLUMN procedure_type_id;
#EndIf

#IfMissingColumn procedure_questions tips
ALTER TABLE `procedure_questions`
  ADD COLUMN `tips` varchar(255) NOT NULL DEFAULT '' COMMENT 'Additional instructions for answering the question';
#EndIf

#IfMissingColumn procedure_order_code procedure_name
ALTER TABLE `procedure_order_code`
  ADD COLUMN `procedure_name` varchar(255) NOT NULL DEFAULT '' COMMENT
  'Descriptive name of procedure_code';
# This severs the link between procedure_order_code and procedure_type:
UPDATE procedure_order_code AS pc, procedure_order AS po, procedure_type AS pt
  SET pc.procedure_name = pt.name
  WHERE po.procedure_order_id = pc.procedure_order_id AND
  pt.lab_id = po.lab_id AND
  pt.procedure_code = pc.procedure_code;
ALTER TABLE `procedure_order_code` DROP COLUMN procedure_type_id;
#EndIf

#IfMissingColumn procedure_report report_notes
ALTER TABLE procedure_report
  ADD COLUMN `report_notes` text NOT NULL DEFAULT '' COMMENT 'Notes from the lab';
#EndIf

#IfNotTable modules
CREATE TABLE `modules` (
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_name` varchar(64) NOT NULL DEFAULT '0',
  `mod_directory` varchar(64) NOT NULL DEFAULT '',
  `mod_parent` varchar(64) NOT NULL DEFAULT '',
  `mod_type` varchar(64) NOT NULL DEFAULT '',
  `mod_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_ui_name` varchar(20) NOT NULL DEFAULT '''',
  `mod_relative_link` varchar(64) NOT NULL DEFAULT '',
  `mod_ui_order` tinyint(3) NOT NULL DEFAULT '0',
  `mod_ui_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_description` varchar(255) NOT NULL DEFAULT '',
  `mod_nick_name` varchar(25) NOT NULL DEFAULT '',
  `mod_enc_menu` varchar(10) NOT NULL DEFAULT 'no',
  `permissions_item_table` char(100) DEFAULT NULL,
  `directory` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `sql_run` tinyint(4) DEFAULT '0',
  `type` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`mod_id`,`mod_directory`)
) ENGINE=InnoDB;
#EndIf

#IfMissingColumn procedure_type suffix
ALTER TABLE procedure_type
  ADD COLUMN `suffix`         varchar(50) NOT NULL DEFAULT '',
  ADD COLUMN `pap_indicator`  varchar(5)  DEFAULT NULL,
  ADD COLUMN `specimen_state` varchar(5)  DEFAULT NULL;
INSERT INTO procedure_type SET NAME='AP ACCESSION NO.' ,lab_id='1',procedure_code='%AAN',procedure_type='ord',description='AP ACCESSION NO.';
INSERT INTO procedure_type SET NAME='Antibody Ident' ,lab_id='1',procedure_code='%ABI',procedure_type='ord',description='Antibody Ident';
INSERT INTO procedure_type SET NAME='ABO/RH(D)' ,lab_id='1',procedure_code='%ABR',procedure_type='ord',description='ABO/RH(D)';
INSERT INTO procedure_type SET NAME='AP NUMBER OF SLIDES RESULTED' ,lab_id='1',procedure_code='%ANS',procedure_type='ord',description='AP NUMBER OF SLIDES RESULTED';
INSERT INTO procedure_type SET NAME='UNIT INFO' ,lab_id='1',procedure_code='%AO',procedure_type='ord',description='UNIT INFO';
INSERT INTO procedure_type SET NAME='AP RESULT STATUS' ,lab_id='1',procedure_code='%ARS',procedure_type='ord',description='AP RESULT STATUS';
INSERT INTO procedure_type SET NAME='ANTIBODY SCREEN' ,lab_id='1',procedure_code='%AS',procedure_type='ord',description='ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='AP SPECIMEN DESCRIPTION' ,lab_id='1',procedure_code='%ASD',procedure_type='ord',description='AP SPECIMEN DESCRIPTION';
INSERT INTO procedure_type SET NAME='AP CASE TYPE' ,lab_id='1',procedure_code='%ATY',procedure_type='ord',description='AP CASE TYPE';
INSERT INTO procedure_type SET NAME='UNIT TAG COMMENT' ,lab_id='1',procedure_code='%CM',procedure_type='ord',description='UNIT TAG COMMENT';
INSERT INTO procedure_type SET NAME='Pathology Case Number' ,lab_id='1',procedure_code='%CN',procedure_type='ord',description='Pathology Case Number';
INSERT INTO procedure_type SET NAME='BLOOD COMPONENT TYPE' ,lab_id='1',procedure_code='%CT',procedure_type='ord',description='BLOOD COMPONENT TYPE';
INSERT INTO procedure_type SET NAME='DIRECT ANTIGLOBULIN TEST' ,lab_id='1',procedure_code='%DBS',procedure_type='ord',description='DIRECT ANTIGLOBULIN TEST';
INSERT INTO procedure_type SET NAME='DIRECT COOMBS IGG' ,lab_id='1',procedure_code='%DIG',procedure_type='ord',description='DIRECT COOMBS IGG';
INSERT INTO procedure_type SET NAME='Weak D' ,lab_id='1',procedure_code='%DU',procedure_type='ord',description='Weak D';
INSERT INTO procedure_type SET NAME='ANTIBODY ELUTED' ,lab_id='1',procedure_code='%ELU',procedure_type='ord',description='ANTIBODY ELUTED';
INSERT INTO procedure_type SET NAME='EQUIVALENT UNITS' ,lab_id='1',procedure_code='%EU',procedure_type='ord',description='EQUIVALENT UNITS';
INSERT INTO procedure_type SET NAME='CROSSMATCH EXPIRATION' ,lab_id='1',procedure_code='%EXX',procedure_type='ord',description='CROSSMATCH EXPIRATION';
INSERT INTO procedure_type SET NAME='Pathology Final Status' ,lab_id='1',procedure_code='%FNL',procedure_type='ord',description='Pathology Final Status';
INSERT INTO procedure_type SET NAME='Pathology Last Activity' ,lab_id='1',procedure_code='%LA',procedure_type='ord',description='Pathology Last Activity';
INSERT INTO procedure_type SET NAME='LABEL CHECK' ,lab_id='1',procedure_code='%LC',procedure_type='ord',description='LABEL CHECK';
INSERT INTO procedure_type SET NAME='Mother\'s Pat. No.' ,lab_id='1',procedure_code='%MPN',procedure_type='ord',description='Mother\'s Pat. No.';
INSERT INTO procedure_type SET NAME='STATUS OF UNIT' ,lab_id='1',procedure_code='%ST',procedure_type='ord',description='STATUS OF UNIT';
INSERT INTO procedure_type SET NAME='TRANSFUSION STATUS' ,lab_id='1',procedure_code='%TS',procedure_type='ord',description='TRANSFUSION STATUS';
INSERT INTO procedure_type SET NAME='UNITS ALLOCATED' ,lab_id='1',procedure_code='%UA',procedure_type='ord',description='UNITS ALLOCATED';
INSERT INTO procedure_type SET NAME='UNIT DIVISION' ,lab_id='1',procedure_code='%UDIV',procedure_type='ord',description='UNIT DIVISION';
INSERT INTO procedure_type SET NAME='UNITS ISSUED' ,lab_id='1',procedure_code='%UI',procedure_type='ord',description='UNITS ISSUED';
INSERT INTO procedure_type SET NAME='UNIT NUMBER' ,lab_id='1',procedure_code='%UN',procedure_type='ord',description='UNIT NUMBER';
INSERT INTO procedure_type SET NAME='UNIT TAG REPRINT' ,lab_id='1',procedure_code='%UR',procedure_type='ord',description='UNIT TAG REPRINT';
INSERT INTO procedure_type SET NAME='CROSSMATCH CREDIT' ,lab_id='1',procedure_code='%XMCR',procedure_type='ord',description='CROSSMATCH CREDIT';
INSERT INTO procedure_type SET NAME='17 KETOSTEROIDS' ,lab_id='1',procedure_code='17KETO',procedure_type='ord',description='17 KETOSTEROIDS';
INSERT INTO procedure_type SET NAME='17 OH CORTICOIDS, UR' ,lab_id='1',procedure_code='17OHS',procedure_type='ord',description='17 OH CORTICOIDS, UR';
INSERT INTO procedure_type SET NAME='1 MET HISTIDINE' ,lab_id='1',procedure_code='1MHIST',procedure_type='ord',description='1 MET HISTIDINE';
INSERT INTO procedure_type SET NAME='VITAMIN D, 25 HYDROXY' ,lab_id='1',procedure_code='25HD',procedure_type='ord',description='VITAMIN D, 25 HYDROXY';
INSERT INTO procedure_type SET NAME='CYP 2C19 GENOTYPE' ,lab_id='1',procedure_code='2C19',procedure_type='ord',description='CYP 2C19 GENOTYPE';
INSERT INTO procedure_type SET NAME='3 MET HISTIDINE' ,lab_id='1',procedure_code='3MHIST',procedure_type='ord',description='3 MET HISTIDINE';
INSERT INTO procedure_type SET NAME='FLUCYTOSINE' ,lab_id='1',procedure_code='5FC',procedure_type='ord',description='FLUCYTOSINE';
INSERT INTO procedure_type SET NAME='5 HIAA, QUANTITATIVE' ,lab_id='1',procedure_code='5HIAA',procedure_type='ord',description='5 HIAA, QUANTITATIVE';
INSERT INTO procedure_type SET NAME='5-HIAA, RANDOM' ,lab_id='1',procedure_code='5HQTRU',procedure_type='ord',description='5-HIAA, RANDOM';
INSERT INTO procedure_type SET NAME='BUSULFAN SAMPLE 1:' ,lab_id='1',procedure_code='5S1',procedure_type='ord',description='BUSULFAN SAMPLE 1:';
INSERT INTO procedure_type SET NAME='BUSULFAN SAMPLE 2:' ,lab_id='1',procedure_code='5S2',procedure_type='ord',description='BUSULFAN SAMPLE 2:';
INSERT INTO procedure_type SET NAME='BUSULFAN SAMPLE 3:' ,lab_id='1',procedure_code='5S3',procedure_type='ord',description='BUSULFAN SAMPLE 3:';
INSERT INTO procedure_type SET NAME='BUSULFAN SAMPLE 4:' ,lab_id='1',procedure_code='5S4',procedure_type='ord',description='BUSULFAN SAMPLE 4:';
INSERT INTO procedure_type SET NAME='BUSULFAN SAMPLE 5:' ,lab_id='1',procedure_code='5S5',procedure_type='ord',description='BUSULFAN SAMPLE 5:';
INSERT INTO procedure_type SET NAME='ALPHA1-ANTITRYPSIN, RENDOM STOOL' ,lab_id='1',procedure_code='A1AF',procedure_type='ord',description='ALPHA1-ANTITRYPSIN, RENDOM STOOL';
INSERT INTO procedure_type SET NAME='ALPHA 1 ANTITRYPSIN' ,lab_id='1',procedure_code='A1AT',procedure_type='ord',description='ALPHA 1 ANTITRYPSIN';
INSERT INTO procedure_type SET NAME='ALPHA 1 GLOBULIN' ,lab_id='1',procedure_code='A1G',procedure_type='ord',description='ALPHA 1 GLOBULIN';
INSERT INTO procedure_type SET NAME='ALPHA 1 GLOBULIN, URINE' ,lab_id='1',procedure_code='A1GU',procedure_type='ord',description='ALPHA 1 GLOBULIN, URINE';
INSERT INTO procedure_type SET NAME='ALPHA 2 GLOBULIN' ,lab_id='1',procedure_code='A2G',procedure_type='ord',description='ALPHA 2 GLOBULIN';
INSERT INTO procedure_type SET NAME='ALPHA 2 GLOBULIN, URINE' ,lab_id='1',procedure_code='A2GU',procedure_type='ord',description='ALPHA 2 GLOBULIN, URINE';
INSERT INTO procedure_type SET NAME='ANTIPLASMIN ACTIVITY' ,lab_id='1',procedure_code='A2PI',procedure_type='ord',description='ANTIPLASMIN ACTIVITY';
INSERT INTO procedure_type SET NAME='AMINO ACIDS, CSF' ,lab_id='1',procedure_code='AACSF',procedure_type='ord',description='AMINO ACIDS, CSF';
INSERT INTO procedure_type SET NAME='ENTRY DATE' ,lab_id='1',procedure_code='AAETRY',procedure_type='ord',description='ENTRY DATE';
INSERT INTO procedure_type SET NAME='FINAL ENTRY' ,lab_id='1',procedure_code='AAFNL',procedure_type='ord',description='FINAL ENTRY';
INSERT INTO procedure_type SET NAME='Genetic Counselor:' ,lab_id='1',procedure_code='AAGC',procedure_type='ord',description='Genetic Counselor:';
INSERT INTO procedure_type SET NAME='eGFR AFRICAN AM' ,lab_id='1',procedure_code='AAGFR',procedure_type='ord',description='eGFR AFRICAN AM';
INSERT INTO procedure_type SET NAME='AaDO2' ,lab_id='1',procedure_code='AAOG',procedure_type='ord',description='AaDO2';
INSERT INTO procedure_type SET NAME='AMINO ACIDS(inc.PKU)' ,lab_id='1',procedure_code='AAP',procedure_type='ord',description='AMINO ACIDS(inc.PKU)';
INSERT INTO procedure_type SET NAME='ACETAMINOPHEN' ,lab_id='1',procedure_code='AAPH',procedure_type='ord',description='ACETAMINOPHEN';
INSERT INTO procedure_type SET NAME='PLASMA AMINO ACIDS' ,lab_id='1',procedure_code='AAPL',procedure_type='ord',description='PLASMA AMINO ACIDS';
INSERT INTO procedure_type SET NAME='PRELIMINARY ENTRY' ,lab_id='1',procedure_code='AAPREL',procedure_type='ord',description='PRELIMINARY ENTRY';
INSERT INTO procedure_type SET NAME='AMINO ACIDS, QNT' ,lab_id='1',procedure_code='AAQT',procedure_type='ord',description='AMINO ACIDS, QNT';
INSERT INTO procedure_type SET NAME='AMINO ACIDS PROFILE' ,lab_id='1',procedure_code='AAQTS',procedure_type='ord',description='AMINO ACIDS PROFILE';
INSERT INTO procedure_type SET NAME='AMINO ACIDS, QNT URINE' ,lab_id='1',procedure_code='AAQU',procedure_type='ord',description='AMINO ACIDS, QNT URINE';
INSERT INTO procedure_type SET NAME='AMINO ACID SCREEN, URINE' ,lab_id='1',procedure_code='AAS',procedure_type='ord',description='AMINO ACID SCREEN, URINE';
INSERT INTO procedure_type SET NAME='AMINO ACIDS, URINE' ,lab_id='1',procedure_code='AAUR',procedure_type='ord',description='AMINO ACIDS, URINE';
INSERT INTO procedure_type SET NAME='AB ID Interp (MD):' ,lab_id='1',procedure_code='ABIDPF',procedure_type='ord',description='AB ID Interp (MD):';
INSERT INTO procedure_type SET NAME='ANTIBODY ID-NON PT' ,lab_id='1',procedure_code='ABINP',procedure_type='ord',description='ANTIBODY ID-NON PT';
INSERT INTO procedure_type SET NAME='ABL MUTATION CELL' ,lab_id='1',procedure_code='ABL1',procedure_type='ord',description='ABL MUTATION CELL';
INSERT INTO procedure_type SET NAME='ABL POSS. MUTATIONS' ,lab_id='1',procedure_code='ABL2',procedure_type='ord',description='ABL POSS. MUTATIONS';
INSERT INTO procedure_type SET NAME='ABNORMAL LYMPHS' ,lab_id='1',procedure_code='ABLA',procedure_type='ord',description='ABNORMAL LYMPHS';
INSERT INTO procedure_type SET NAME='ABO/RH COMMENT' ,lab_id='1',procedure_code='ABOC',procedure_type='ord',description='ABO/RH COMMENT';
INSERT INTO procedure_type SET NAME='ABO COMPATIBLE Y/N ?' ,lab_id='1',procedure_code='ABOCK',procedure_type='ord',description='ABO COMPATIBLE Y/N ?';
INSERT INTO procedure_type SET NAME='ABO/RH' ,lab_id='1',procedure_code='ABORH',procedure_type='ord',description='ABO/RH';
INSERT INTO procedure_type SET NAME='INTR-OP PTH BASELINE' ,lab_id='1',procedure_code='ABPTH',procedure_type='ord',description='INTR-OP PTH BASELINE';
INSERT INTO procedure_type SET NAME='ABSTINENCE' ,lab_id='1',procedure_code='ABST',procedure_type='ord',description='ABSTINENCE';
INSERT INTO procedure_type SET NAME='ALBUMIN, URINE' ,lab_id='1',procedure_code='ABU',procedure_type='ord',description='ALBUMIN, URINE';
INSERT INTO procedure_type SET NAME='DRUGS OF ABUSE SCREEN' ,lab_id='1',procedure_code='ABUS',procedure_type='ord',description='DRUGS OF ABUSE SCREEN';
INSERT INTO procedure_type SET NAME='ACCESSION NO.' ,lab_id='1',procedure_code='ACCN',procedure_type='ord',description='ACCESSION NO.';
INSERT INTO procedure_type SET NAME='ANGIOTENSIN CONVERTING ENZ' ,lab_id='1',procedure_code='ACE',procedure_type='ord',description='ANGIOTENSIN CONVERTING ENZ';
INSERT INTO procedure_type SET NAME='KETONES, QUALITATIVE' ,lab_id='1',procedure_code='ACET',procedure_type='ord',description='KETONES, QUALITATIVE';
INSERT INTO procedure_type SET NAME='ARRAY CGH' ,lab_id='1',procedure_code='ACGHC',procedure_type='ord',description='ARRAY CGH';
INSERT INTO procedure_type SET NAME='Array CGH, prenatal' ,lab_id='1',procedure_code='ACGHP',procedure_type='ord',description='Array CGH, prenatal';
INSERT INTO procedure_type SET NAME='ACETYLCHOLINESTERASE,AF' ,lab_id='1',procedure_code='ACHE',procedure_type='ord',description='ACETYLCHOLINESTERASE,AF';
INSERT INTO procedure_type SET NAME='CARDIOLIPIN IgA AB' ,lab_id='1',procedure_code='ACLA',procedure_type='ord',description='CARDIOLIPIN IgA AB';
INSERT INTO procedure_type SET NAME='ANTI-CARDIOLIPIN IGG AB' ,lab_id='1',procedure_code='ACLG',procedure_type='ord',description='ANTI-CARDIOLIPIN IGG AB';
INSERT INTO procedure_type SET NAME='ANTI-CARDIOLIPIN IGM AB' ,lab_id='1',procedure_code='ACLM',procedure_type='ord',description='ANTI-CARDIOLIPIN IGM AB';
INSERT INTO procedure_type SET NAME='RELATED DRUGS' ,lab_id='1',procedure_code='ACMNT',procedure_type='ord',description='RELATED DRUGS';
INSERT INTO procedure_type SET NAME='ACID PHOS PROSTATIC' ,lab_id='1',procedure_code='ACPP',procedure_type='ord',description='ACID PHOS PROSTATIC';
INSERT INTO procedure_type SET NAME='ACID PHOS. TOTAL' ,lab_id='1',procedure_code='ACPT',procedure_type='ord',description='ACID PHOS. TOTAL';
INSERT INTO procedure_type SET NAME='ACETYLCHOLINE RECEPTOR ANTIB' ,lab_id='1',procedure_code='ACRA',procedure_type='ord',description='ACETYLCHOLINE RECEPTOR ANTIB';
INSERT INTO procedure_type SET NAME='ACTIVATED CLOT TIME' ,lab_id='1',procedure_code='ACT',procedure_type='ord',description='ACTIVATED CLOT TIME';
INSERT INTO procedure_type SET NAME='ACTH, PLASMA' ,lab_id='1',procedure_code='ACTHP',procedure_type='ord',description='ACTH, PLASMA';
INSERT INTO procedure_type SET NAME='ACTH SPECIMEN ID' ,lab_id='1',procedure_code='ACTID',procedure_type='ord',description='ACTH SPECIMEN ID';
INSERT INTO procedure_type SET NAME='LYMPHOCYTE AUTOANTB' ,lab_id='1',procedure_code='ACX',procedure_type='ord',description='LYMPHOCYTE AUTOANTB';
INSERT INTO procedure_type SET NAME='ACYLCARNITINE PANEL' ,lab_id='1',procedure_code='ACYL',procedure_type='ord',description='ACYLCARNITINE PANEL';
INSERT INTO procedure_type SET NAME='ACYLCARNITINE' ,lab_id='1',procedure_code='ACYLP',procedure_type='ord',description='ACYLCARNITINE';
INSERT INTO procedure_type SET NAME='DNASE B ANTIBODY' ,lab_id='1',procedure_code='ADAB',procedure_type='ord',description='DNASE B ANTIBODY';
INSERT INTO procedure_type SET NAME='ADDITIONAL INFO' ,lab_id='1',procedure_code='ADDI',procedure_type='ord',description='ADDITIONAL INFO';
INSERT INTO procedure_type SET NAME='ADENOVIRUS CF ANTIB' ,lab_id='1',procedure_code='ADEN',procedure_type='ord',description='ADENOVIRUS CF ANTIB';
INSERT INTO procedure_type SET NAME='DS DNA ANTIBODY' ,lab_id='1',procedure_code='ADNA',procedure_type='ord',description='DS DNA ANTIBODY';
INSERT INTO procedure_type SET NAME='ALDOLASE' ,lab_id='1',procedure_code='ADSE',procedure_type='ord',description='ALDOLASE';
INSERT INTO procedure_type SET NAME='ADENOVIRUS, PCR' ,lab_id='1',procedure_code='ADVP',procedure_type='ord',description='ADENOVIRUS, PCR';
INSERT INTO procedure_type SET NAME='AMNIOTIC FLUID, AFP' ,lab_id='1',procedure_code='AFAFP',procedure_type='ord',description='AMNIOTIC FLUID, AFP';
INSERT INTO procedure_type SET NAME='ACYL/FREE CARN RATIO' ,lab_id='1',procedure_code='AFCR',procedure_type='ord',description='ACYL/FREE CARN RATIO';
INSERT INTO procedure_type SET NAME='ASPERGILLUS FLAVUS' ,lab_id='1',procedure_code='AFLA',procedure_type='ord',description='ASPERGILLUS FLAVUS';
INSERT INTO procedure_type SET NAME='ALPHA FETOPROTEIN,CSF' ,lab_id='1',procedure_code='AFPCSF',procedure_type='ord',description='ALPHA FETOPROTEIN,CSF';
INSERT INTO procedure_type SET NAME='CA AFP SCREEN' ,lab_id='1',procedure_code='AFPOS',procedure_type='ord',description='CA AFP SCREEN';
INSERT INTO procedure_type SET NAME='ALPHA FETOPROTEIN' ,lab_id='1',procedure_code='AFPT',procedure_type='ord',description='ALPHA FETOPROTEIN';
INSERT INTO procedure_type SET NAME='AFB SMEAR' ,lab_id='1',procedure_code='AFS',procedure_type='ord',description='AFB SMEAR';
INSERT INTO procedure_type SET NAME='SA(SURFACT:ALB) RATIO' ,lab_id='1',procedure_code='AFSA',procedure_type='ord',description='SA(SURFACT:ALB) RATIO';
INSERT INTO procedure_type SET NAME='ASPG FUMIGATUS AB' ,lab_id='1',procedure_code='AFUM',procedure_type='ord',description='ASPG FUMIGATUS AB';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG1',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG10',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG11',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG12',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG13',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG14',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG2',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG3',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG4',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG5',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG6',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG7',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG8',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='RBC Antigen' ,lab_id='1',procedure_code='AG9',procedure_type='ord',description='RBC Antigen';
INSERT INTO procedure_type SET NAME='LEUKOCYTES:' ,lab_id='1',procedure_code='AGAL',procedure_type='ord',description='LEUKOCYTES:';
INSERT INTO procedure_type SET NAME='ALPHA GALACTOSIDASE' ,lab_id='1',procedure_code='AGALT',procedure_type='ord',description='ALPHA GALACTOSIDASE';
INSERT INTO procedure_type SET NAME='GLOMERULAR BM ANTIB' ,lab_id='1',procedure_code='AGBM',procedure_type='ord',description='GLOMERULAR BM ANTIB';
INSERT INTO procedure_type SET NAME='AGE' ,lab_id='1',procedure_code='AGE',procedure_type='ord',description='AGE';
INSERT INTO procedure_type SET NAME='AGGLUTINATION' ,lab_id='1',procedure_code='AGGL',procedure_type='ord',description='AGGLUTINATION';
INSERT INTO procedure_type SET NAME='PLATELET AGGREGATION' ,lab_id='1',procedure_code='AGGR',procedure_type='ord',description='PLATELET AGGREGATION';
INSERT INTO procedure_type SET NAME='Plt Agg Interp (MD):' ,lab_id='1',procedure_code='AGGRPF',procedure_type='ord',description='Plt Agg Interp (MD):';
INSERT INTO procedure_type SET NAME='AGE' ,lab_id='1',procedure_code='AGID',procedure_type='ord',description='AGE';
INSERT INTO procedure_type SET NAME='ANDROSTANEDIOL GLUCU' ,lab_id='1',procedure_code='AGLU',procedure_type='ord',description='ANDROSTANEDIOL GLUCU';
INSERT INTO procedure_type SET NAME='ACYLGLYCINE PROFILE' ,lab_id='1',procedure_code='AGLY',procedure_type='ord',description='ACYLGLYCINE PROFILE';
INSERT INTO procedure_type SET NAME='ALTERNATIVE PATHWAY ACT' ,lab_id='1',procedure_code='AH50',procedure_type='ord',description='ALTERNATIVE PATHWAY ACT';
INSERT INTO procedure_type SET NAME='HBe ANTIBODY' ,lab_id='1',procedure_code='AHBE',procedure_type='ord',description='HBe ANTIBODY';
INSERT INTO procedure_type SET NAME='ACID HEMOLYSIS' ,lab_id='1',procedure_code='AHEM',procedure_type='ord',description='ACID HEMOLYSIS';
INSERT INTO procedure_type SET NAME='IgG,ANTI-IgA ABY' ,lab_id='1',procedure_code='AIGA',procedure_type='ord',description='IgG,ANTI-IgA ABY';
INSERT INTO procedure_type SET NAME='IGF-1, ADULT' ,lab_id='1',procedure_code='AIGF1',procedure_type='ord',description='IGF-1, ADULT';
INSERT INTO procedure_type SET NAME='HOMOGENTISIC ACID' ,lab_id='1',procedure_code='AKU',procedure_type='ord',description='HOMOGENTISIC ACID';
INSERT INTO procedure_type SET NAME='ALA Dehydratase' ,lab_id='1',procedure_code='ALAD',procedure_type='ord',description='ALA Dehydratase';
INSERT INTO procedure_type SET NAME='ALANINE' ,lab_id='1',procedure_code='ALAN',procedure_type='ord',description='ALANINE';
INSERT INTO procedure_type SET NAME='AMINOLEVULINIC ACID' ,lab_id='1',procedure_code='ALAQ',procedure_type='ord',description='AMINOLEVULINIC ACID';
INSERT INTO procedure_type SET NAME='ALA, RANDOM URINE' ,lab_id='1',procedure_code='ALAQR',procedure_type='ord',description='ALA, RANDOM URINE';
INSERT INTO procedure_type SET NAME='ALBUMIN' ,lab_id='1',procedure_code='ALB',procedure_type='ord',description='ALBUMIN';
INSERT INTO procedure_type SET NAME='ALBUMIN, BODY FLUID' ,lab_id='1',procedure_code='ALBB',procedure_type='ord',description='ALBUMIN, BODY FLUID';
INSERT INTO procedure_type SET NAME='ALBUMIN, CSF' ,lab_id='1',procedure_code='ALBC',procedure_type='ord',description='ALBUMIN, CSF';
INSERT INTO procedure_type SET NAME='ALBUMIN' ,lab_id='1',procedure_code='ALBL',procedure_type='ord',description='ALBUMIN';
INSERT INTO procedure_type SET NAME='ALBUMIN, SERUM' ,lab_id='1',procedure_code='ALBS',procedure_type='ord',description='ALBUMIN, SERUM';
INSERT INTO procedure_type SET NAME='ETHANOL, SER OR PLS' ,lab_id='1',procedure_code='ALC',procedure_type='ord',description='ETHANOL, SER OR PLS';
INSERT INTO procedure_type SET NAME='ALCOHOL, ETHYL (URINE)' ,lab_id='1',procedure_code='ALCO',procedure_type='ord',description='ALCOHOL, ETHYL (URINE)';
INSERT INTO procedure_type SET NAME='ALDOSTERONE (URINE)' ,lab_id='1',procedure_code='ALD',procedure_type='ord',description='ALDOSTERONE (URINE)';
INSERT INTO procedure_type SET NAME='ALDOSTERONE' ,lab_id='1',procedure_code='ALDT',procedure_type='ord',description='ALDOSTERONE';
INSERT INTO procedure_type SET NAME='ALKALINE PHOSPHATASE' ,lab_id='1',procedure_code='ALKP',procedure_type='ord',description='ALKALINE PHOSPHATASE';
INSERT INTO procedure_type SET NAME='ALLO ISOLEUCINE' ,lab_id='1',procedure_code='ALLOI',procedure_type='ord',description='ALLO ISOLEUCINE';
INSERT INTO procedure_type SET NAME='ALLERGENS' ,lab_id='1',procedure_code='ALRGN',procedure_type='ord',description='ALLERGENS';
INSERT INTO procedure_type SET NAME='ALT' ,lab_id='1',procedure_code='ALT',procedure_type='ord',description='ALT';
INSERT INTO procedure_type SET NAME='ALPHA THAL MUTATIONS' ,lab_id='1',procedure_code='ALTH',procedure_type='ord',description='ALPHA THAL MUTATIONS';
INSERT INTO procedure_type SET NAME='ALUMINUM' ,lab_id='1',procedure_code='ALUM',procedure_type='ord',description='ALUMINUM';
INSERT INTO procedure_type SET NAME='AMOX CLAV' ,lab_id='1',procedure_code='AMCLA',procedure_type='ord',description='AMOX CLAV';
INSERT INTO procedure_type SET NAME='ANTI-MULLERIAN HORMONE' ,lab_id='1',procedure_code='AMH',procedure_type='ord',description='ANTI-MULLERIAN HORMONE';
INSERT INTO procedure_type SET NAME='AMIKACIN' ,lab_id='1',procedure_code='AMI',procedure_type='ord',description='AMIKACIN';
INSERT INTO procedure_type SET NAME='AMIODARONE' ,lab_id='1',procedure_code='AMID',procedure_type='ord',description='AMIODARONE';
INSERT INTO procedure_type SET NAME='AMIKACIN' ,lab_id='1',procedure_code='AMIKA',procedure_type='ord',description='AMIKACIN';
INSERT INTO procedure_type SET NAME='AMIKACIN, PEAK' ,lab_id='1',procedure_code='AMIKP',procedure_type='ord',description='AMIKACIN, PEAK';
INSERT INTO procedure_type SET NAME='AMIKACIN, RANDOM' ,lab_id='1',procedure_code='AMIKRN',procedure_type='ord',description='AMIKACIN, RANDOM';
INSERT INTO procedure_type SET NAME='AMIKACIN, TROUGH' ,lab_id='1',procedure_code='AMIKT',procedure_type='ord',description='AMIKACIN, TROUGH';
INSERT INTO procedure_type SET NAME='AMINO ACID INTERP' ,lab_id='1',procedure_code='AMINT',procedure_type='ord',description='AMINO ACID INTERP';
INSERT INTO procedure_type SET NAME='ASP MIXTURE PPTNS' ,lab_id='1',procedure_code='AMIX',procedure_type='ord',description='ASP MIXTURE PPTNS';
INSERT INTO procedure_type SET NAME='AMORPHOUS' ,lab_id='1',procedure_code='AMO',procedure_type='ord',description='AMORPHOUS';
INSERT INTO procedure_type SET NAME='AMOEBA PRECIPITINS' ,lab_id='1',procedure_code='AMOE',procedure_type='ord',description='AMOEBA PRECIPITINS';
INSERT INTO procedure_type SET NAME='E.HISTOLYTICA AB IGG' ,lab_id='1',procedure_code='AMOEB',procedure_type='ord',description='E.HISTOLYTICA AB IGG';
INSERT INTO procedure_type SET NAME='AMOXICILLIN' ,lab_id='1',procedure_code='AMOX',procedure_type='ord',description='AMOXICILLIN';
INSERT INTO procedure_type SET NAME='AMPICILLIN' ,lab_id='1',procedure_code='AMP',procedure_type='ord',description='AMPICILLIN';
INSERT INTO procedure_type SET NAME='AMPHOTERICIN B' ,lab_id='1',procedure_code='AMPB',procedure_type='ord',description='AMPHOTERICIN B';
INSERT INTO procedure_type SET NAME='AMPHETAMINE BY GC/MS' ,lab_id='1',procedure_code='AMPC',procedure_type='ord',description='AMPHETAMINE BY GC/MS';
INSERT INTO procedure_type SET NAME='AMPHETAMINE, UR' ,lab_id='1',procedure_code='AMPH',procedure_type='ord',description='AMPHETAMINE, UR';
INSERT INTO procedure_type SET NAME='AMP SULBAC' ,lab_id='1',procedure_code='AMPSUL',procedure_type='ord',description='AMP SULBAC';
INSERT INTO procedure_type SET NAME='AMPHETAMINE SCRN. UR.' ,lab_id='1',procedure_code='AMPU',procedure_type='ord',description='AMPHETAMINE SCRN. UR.';
INSERT INTO procedure_type SET NAME='AMITRIPTYLINE' ,lab_id='1',procedure_code='AMT',procedure_type='ord',description='AMITRIPTYLINE';
INSERT INTO procedure_type SET NAME='AMYLASE' ,lab_id='1',procedure_code='AMY',procedure_type='ord',description='AMYLASE';
INSERT INTO procedure_type SET NAME='AMYLASE, BODY FLUID' ,lab_id='1',procedure_code='AMYB',procedure_type='ord',description='AMYLASE, BODY FLUID';
INSERT INTO procedure_type SET NAME='ANTI MYELOPEROXIDASE' ,lab_id='1',procedure_code='AMYE',procedure_type='ord',description='ANTI MYELOPEROXIDASE';
INSERT INTO procedure_type SET NAME='AMYLASE PER HOUR, UR' ,lab_id='1',procedure_code='AMYT',procedure_type='ord',description='AMYLASE PER HOUR, UR';
INSERT INTO procedure_type SET NAME='AMYLASE, URINE' ,lab_id='1',procedure_code='AMYUR',procedure_type='ord',description='AMYLASE, URINE';
INSERT INTO procedure_type SET NAME='ANTHRAQUINOES' ,lab_id='1',procedure_code='AN',procedure_type='ord',description='ANTHRAQUINOES';
INSERT INTO procedure_type SET NAME='ANA' ,lab_id='1',procedure_code='ANA',procedure_type='ord',description='ANA';
INSERT INTO procedure_type SET NAME='A-2 MACROGLOB,QN(02)' ,lab_id='1',procedure_code='ANA1',procedure_type='ord',description='A-2 MACROGLOB,QN(02)';
INSERT INTO procedure_type SET NAME='HAPTOGLOBIN (HCVF)02' ,lab_id='1',procedure_code='ANA2',procedure_type='ord',description='HAPTOGLOBIN (HCVF)02';
INSERT INTO procedure_type SET NAME='APOLIPOPROTEIN A1(02)' ,lab_id='1',procedure_code='ANA3',procedure_type='ord',description='APOLIPOPROTEIN A1(02)';
INSERT INTO procedure_type SET NAME='BILIRUBIN,TOTAL(02)' ,lab_id='1',procedure_code='ANA4',procedure_type='ord',description='BILIRUBIN,TOTAL(02)';
INSERT INTO procedure_type SET NAME='GGT(HCVF)(02):' ,lab_id='1',procedure_code='ANA5',procedure_type='ord',description='GGT(HCVF)(02):';
INSERT INTO procedure_type SET NAME='ALT(SGPT)(HCVF)(02)' ,lab_id='1',procedure_code='ANA6',procedure_type='ord',description='ALT(SGPT)(HCVF)(02)';
INSERT INTO procedure_type SET NAME='ANABOLIC STEROIDS QL' ,lab_id='1',procedure_code='ANAB',procedure_type='ord',description='ANABOLIC STEROIDS QL';
INSERT INTO procedure_type SET NAME='A AMINO ADIPATE' ,lab_id='1',procedure_code='ANAD',procedure_type='ord',description='A AMINO ADIPATE';
INSERT INTO procedure_type SET NAME='ANA PATTERN' ,lab_id='1',procedure_code='ANAP',procedure_type='ord',description='ANA PATTERN';
INSERT INTO procedure_type SET NAME='A.PHAGOCYTOPHIL IGG' ,lab_id='1',procedure_code='ANAPG',procedure_type='ord',description='A.PHAGOCYTOPHIL IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='ANAPI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='A.PHAGOCYTOPHIL IGM' ,lab_id='1',procedure_code='ANAPM',procedure_type='ord',description='A.PHAGOCYTOPHIL IGM';
INSERT INTO procedure_type SET NAME='ANA TITER' ,lab_id='1',procedure_code='ANAT',procedure_type='ord',description='ANA TITER';
INSERT INTO procedure_type SET NAME='ANGLE' ,lab_id='1',procedure_code='ANG',procedure_type='ord',description='ANGLE';
INSERT INTO procedure_type SET NAME='ANION GAP' ,lab_id='1',procedure_code='ANGAP',procedure_type='ord',description='ANION GAP';
INSERT INTO procedure_type SET NAME='ATRIAL NATRIUR HORM' ,lab_id='1',procedure_code='ANH',procedure_type='ord',description='ATRIAL NATRIUR HORM';
INSERT INTO procedure_type SET NAME='A AMINO N BUTYRATE' ,lab_id='1',procedure_code='ANNB',procedure_type='ord',description='A AMINO N BUTYRATE';
INSERT INTO procedure_type SET NAME='Analysis procedure:' ,lab_id='1',procedure_code='ANPROC',procedure_type='ord',description='Analysis procedure:';
INSERT INTO procedure_type SET NAME='ANSERINE' ,lab_id='1',procedure_code='ANSER',procedure_type='ord',description='ANSERINE';
INSERT INTO procedure_type SET NAME='A/DI-OLIGO RATIO' ,lab_id='1',procedure_code='AODR',procedure_type='ord',description='A/DI-OLIGO RATIO';
INSERT INTO procedure_type SET NAME='ANTI PROTEINASE 3 AB' ,lab_id='1',procedure_code='AP3A',procedure_type='ord',description='ANTI PROTEINASE 3 AB';
INSERT INTO procedure_type SET NAME='APPEARANCE' ,lab_id='1',procedure_code='APB',procedure_type='ord',description='APPEARANCE';
INSERT INTO procedure_type SET NAME='PARIETAL CELL ANTIBODY' ,lab_id='1',procedure_code='APC',procedure_type='ord',description='PARIETAL CELL ANTIBODY';
INSERT INTO procedure_type SET NAME='PENICILLIN G' ,lab_id='1',procedure_code='APEN',procedure_type='ord',description='PENICILLIN G';
INSERT INTO procedure_type SET NAME='APPEARANCE' ,lab_id='1',procedure_code='APPC',procedure_type='ord',description='APPEARANCE';
INSERT INTO procedure_type SET NAME='APPEARANCE' ,lab_id='1',procedure_code='APPE',procedure_type='ord',description='APPEARANCE';
INSERT INTO procedure_type SET NAME='RESIDENT APPROVAL' ,lab_id='1',procedure_code='APPR',procedure_type='ord',description='RESIDENT APPROVAL';
INSERT INTO procedure_type SET NAME='APT TEST' ,lab_id='1',procedure_code='APT',procedure_type='ord',description='APT TEST';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH1',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH2',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH3',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH4',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH5',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH6',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH7',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='PTH' ,lab_id='1',procedure_code='APTH8',procedure_type='ord',description='PTH';
INSERT INTO procedure_type SET NAME='ARGININOSUCCINIC AC' ,lab_id='1',procedure_code='ARAC',procedure_type='ord',description='ARGININOSUCCINIC AC';
INSERT INTO procedure_type SET NAME='BODY SURFACE AREA' ,lab_id='1',procedure_code='AREA',procedure_type='ord',description='BODY SURFACE AREA';
INSERT INTO procedure_type SET NAME='ARGATROBAN ASSAY' ,lab_id='1',procedure_code='ARGA',procedure_type='ord',description='ARGATROBAN ASSAY';
INSERT INTO procedure_type SET NAME='ARGININE' ,lab_id='1',procedure_code='ARGN',procedure_type='ord',description='ARGININE';
INSERT INTO procedure_type SET NAME='AUREOBAS. PULLULANS' ,lab_id='1',procedure_code='ARPU',procedure_type='ord',description='AUREOBAS. PULLULANS';
INSERT INTO procedure_type SET NAME='ARSENIC, BLOOD' ,lab_id='1',procedure_code='ASB',procedure_type='ord',description='ARSENIC, BLOOD';
INSERT INTO procedure_type SET NAME='ASCORBIC ACID' ,lab_id='1',procedure_code='ASCA',procedure_type='ord',description='ASCORBIC ACID';
INSERT INTO procedure_type SET NAME='ANDROSTENEDIONE' ,lab_id='1',procedure_code='ASDN',procedure_type='ord',description='ANDROSTENEDIONE';
INSERT INTO procedure_type SET NAME='ASPERGILLUS FUMIGATUS' ,lab_id='1',procedure_code='ASFU',procedure_type='ord',description='ASPERGILLUS FUMIGATUS';
INSERT INTO procedure_type SET NAME='SMOOTH MUSCLE AB' ,lab_id='1',procedure_code='ASM',procedure_type='ord',description='SMOOTH MUSCLE AB';
INSERT INTO procedure_type SET NAME='SMOOTH MUSCLE AB TITER' ,lab_id='1',procedure_code='ASMT',procedure_type='ord',description='SMOOTH MUSCLE AB TITER';
INSERT INTO procedure_type SET NAME='ASPERGILLUS NIGER' ,lab_id='1',procedure_code='ASNI',procedure_type='ord',description='ASPERGILLUS NIGER';
INSERT INTO procedure_type SET NAME='ANTIBODY SCREEN' ,lab_id='1',procedure_code='ASNP',procedure_type='ord',description='ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='ANTI STREPTOLYSIN O' ,lab_id='1',procedure_code='ASO',procedure_type='ord',description='ANTI STREPTOLYSIN O';
INSERT INTO procedure_type SET NAME='ASPARAGINE' ,lab_id='1',procedure_code='ASPARE',procedure_type='ord',description='ASPARAGINE';
INSERT INTO procedure_type SET NAME='ASPARTATE' ,lab_id='1',procedure_code='ASPART',procedure_type='ord',description='ASPARTATE';
INSERT INTO procedure_type SET NAME='ASPG NIGER AB' ,lab_id='1',procedure_code='ASPN',procedure_type='ord',description='ASPG NIGER AB';
INSERT INTO procedure_type SET NAME='AST' ,lab_id='1',procedure_code='AST',procedure_type='ord',description='AST';
INSERT INTO procedure_type SET NAME='ARSENIC, URINE' ,lab_id='1',procedure_code='ASU',procedure_type='ord',description='ARSENIC, URINE';
INSERT INTO procedure_type SET NAME='ALPHA SUBUNIT' ,lab_id='1',procedure_code='ASUB',procedure_type='ord',description='ALPHA SUBUNIT';
INSERT INTO procedure_type SET NAME='ARYLSULFASTASE A' ,lab_id='1',procedure_code='ASUL',procedure_type='ord',description='ARYLSULFASTASE A';
INSERT INTO procedure_type SET NAME='ARSENIC, RANDOM UR' ,lab_id='1',procedure_code='ASUR1',procedure_type='ord',description='ARSENIC, RANDOM UR';
INSERT INTO procedure_type SET NAME='ANTITHROMBIN ACTIVITY' ,lab_id='1',procedure_code='AT',procedure_type='ord',description='ANTITHROMBIN ACTIVITY';
INSERT INTO procedure_type SET NAME='AT3' ,lab_id='1',procedure_code='AT3',procedure_type='ord',description='AT3';
INSERT INTO procedure_type SET NAME='CHROM BREAKAGE STUDY' ,lab_id='1',procedure_code='ATAX',procedure_type='ord',description='CHROM BREAKAGE STUDY';
INSERT INTO procedure_type SET NAME='TESTOSTERONE (TOTAL)' ,lab_id='1',procedure_code='ATES',procedure_type='ord',description='TESTOSTERONE (TOTAL)';
INSERT INTO procedure_type SET NAME='THYROGLOBULIN ANTIBODY' ,lab_id='1',procedure_code='ATG',procedure_type='ord',description='THYROGLOBULIN ANTIBODY';
INSERT INTO procedure_type SET NAME='%T CELLS/ALL LYMPHS' ,lab_id='1',procedure_code='ATGS1',procedure_type='ord',description='%T CELLS/ALL LYMPHS';
INSERT INTO procedure_type SET NAME='ALPHA THAL MUTATIONS' ,lab_id='1',procedure_code='ATHL',procedure_type='ord',description='ALPHA THAL MUTATIONS';
INSERT INTO procedure_type SET NAME='ALPHA THAL BY PCR' ,lab_id='1',procedure_code='ATHPCR',procedure_type='ord',description='ALPHA THAL BY PCR';
INSERT INTO procedure_type SET NAME='ATHAL GENE MAPPING' ,lab_id='1',procedure_code='ATHSB',procedure_type='ord',description='ATHAL GENE MAPPING';
INSERT INTO procedure_type SET NAME='ANTITHROMBIN INTRP(MD)' ,lab_id='1',procedure_code='ATPF',procedure_type='ord',description='ANTITHROMBIN INTRP(MD)';
INSERT INTO procedure_type SET NAME='ANTITRYPSIN PHENOTYPE' ,lab_id='1',procedure_code='ATPN',procedure_type='ord',description='ANTITRYPSIN PHENOTYPE';
INSERT INTO procedure_type SET NAME='THYROPEROXIDASE AB' ,lab_id='1',procedure_code='ATPO',procedure_type='ord',description='THYROPEROXIDASE AB';
INSERT INTO procedure_type SET NAME='AREA UNDER THE CURVE' ,lab_id='1',procedure_code='AUC',procedure_type='ord',description='AREA UNDER THE CURVE';
INSERT INTO procedure_type SET NAME='AU/DD' ,lab_id='1',procedure_code='AUDD',procedure_type='ord',description='AU/DD';
INSERT INTO procedure_type SET NAME='Deviat. Auth. By (MD):' ,lab_id='1',procedure_code='AUTHPF',procedure_type='ord',description='Deviat. Auth. By (MD):';
INSERT INTO procedure_type SET NAME='AVERAGE' ,lab_id='1',procedure_code='AV',procedure_type='ord',description='AVERAGE';
INSERT INTO procedure_type SET NAME='A V DO2' ,lab_id='1',procedure_code='AVDO',procedure_type='ord',description='A V DO2';
INSERT INTO procedure_type SET NAME='AVIAN SERUM' ,lab_id='1',procedure_code='AVNS',procedure_type='ord',description='AVIAN SERUM';
INSERT INTO procedure_type SET NAME='ART VEN O2 DIFF' ,lab_id='1',procedure_code='AVOG',procedure_type='ord',description='ART VEN O2 DIFF';
INSERT INTO procedure_type SET NAME='ANTIDIURETIC HORMONE' ,lab_id='1',procedure_code='AVP',procedure_type='ord',description='ANTIDIURETIC HORMONE';
INSERT INTO procedure_type SET NAME='AZITHROMYCIN' ,lab_id='1',procedure_code='AZITH',procedure_type='ord',description='AZITHROMYCIN';
INSERT INTO procedure_type SET NAME='AZTREONAM' ,lab_id='1',procedure_code='AZTREO',procedure_type='ord',description='AZTREONAM';
INSERT INTO procedure_type SET NAME='Meningitis Antigen x 1' ,lab_id='1',procedure_code='B1341',procedure_type='ord',description='Meningitis Antigen x 1';
INSERT INTO procedure_type SET NAME='Meningitis Antigen x 2' ,lab_id='1',procedure_code='B1342',procedure_type='ord',description='Meningitis Antigen x 2';
INSERT INTO procedure_type SET NAME='Meningitis Antigen x 3' ,lab_id='1',procedure_code='B1343',procedure_type='ord',description='Meningitis Antigen x 3';
INSERT INTO procedure_type SET NAME='ID DNA probe: Mycobacteria spp' ,lab_id='1',procedure_code='B227',procedure_type='ord',description='ID DNA probe: Mycobacteria spp';
INSERT INTO procedure_type SET NAME='HLA B27 TYPING' ,lab_id='1',procedure_code='B27',procedure_type='ord',description='HLA B27 TYPING';
INSERT INTO procedure_type SET NAME='B2 GLYCOPROTEIN AB IgA' ,lab_id='1',procedure_code='B2GA',procedure_type='ord',description='B2 GLYCOPROTEIN AB IgA';
INSERT INTO procedure_type SET NAME='B2 GLYCOPROTEIN AB IgG' ,lab_id='1',procedure_code='B2GG',procedure_type='ord',description='B2 GLYCOPROTEIN AB IgG';
INSERT INTO procedure_type SET NAME='B2 GLYCOPROTEIN AB IgM' ,lab_id='1',procedure_code='B2GM',procedure_type='ord',description='B2 GLYCOPROTEIN AB IgM';
INSERT INTO procedure_type SET NAME='B2 GLYCOPRO AB IgG' ,lab_id='1',procedure_code='B2GPG',procedure_type='ord',description='B2 GLYCOPRO AB IgG';
INSERT INTO procedure_type SET NAME='B2 GLYCOPRO AB IgM' ,lab_id='1',procedure_code='B2GPM',procedure_type='ord',description='B2 GLYCOPRO AB IgM';
INSERT INTO procedure_type SET NAME='BETA-2-MICROGLOBULIN' ,lab_id='1',procedure_code='B2M',procedure_type='ord',description='BETA-2-MICROGLOBULIN';
INSERT INTO procedure_type SET NAME='BETA-2 MICROGLOBULIN' ,lab_id='1',procedure_code='B2MI',procedure_type='ord',description='BETA-2 MICROGLOBULIN';
INSERT INTO procedure_type SET NAME='BETA 2 MICROGLOB UR' ,lab_id='1',procedure_code='B2MU',procedure_type='ord',description='BETA 2 MICROGLOB UR';
INSERT INTO procedure_type SET NAME='TRANSFERRIN,BETA 2' ,lab_id='1',procedure_code='B2TAU',procedure_type='ord',description='TRANSFERRIN,BETA 2';
INSERT INTO procedure_type SET NAME='BILL RSV Direct FA' ,lab_id='1',procedure_code='B334',procedure_type='ord',description='BILL RSV Direct FA';
INSERT INTO procedure_type SET NAME='BASOS' ,lab_id='1',procedure_code='BAB',procedure_type='ord',description='BASOS';
INSERT INTO procedure_type SET NAME='BABESIA MICROTI IGG' ,lab_id='1',procedure_code='BABG',procedure_type='ord',description='BABESIA MICROTI IGG';
INSERT INTO procedure_type SET NAME='BABESIA INTERP:' ,lab_id='1',procedure_code='BABI',procedure_type='ord',description='BABESIA INTERP:';
INSERT INTO procedure_type SET NAME='BABESIA MICROTI IGM' ,lab_id='1',procedure_code='BABM',procedure_type='ord',description='BABESIA MICROTI IGM';
INSERT INTO procedure_type SET NAME='CD19 B CELLS ABS' ,lab_id='1',procedure_code='BABS',procedure_type='ord',description='CD19 B CELLS ABS';
INSERT INTO procedure_type SET NAME='BASOS' ,lab_id='1',procedure_code='BAC',procedure_type='ord',description='BASOS';
INSERT INTO procedure_type SET NAME='ADDITIONAL INFO' ,lab_id='1',procedure_code='BADI',procedure_type='ord',description='ADDITIONAL INFO';
INSERT INTO procedure_type SET NAME='B AMINO ISOBUTYRATE' ,lab_id='1',procedure_code='BAIB',procedure_type='ord',description='B AMINO ISOBUTYRATE';
INSERT INTO procedure_type SET NAME='B ALANINE' ,lab_id='1',procedure_code='BALA',procedure_type='ord',description='B ALANINE';
INSERT INTO procedure_type SET NAME='BANDS' ,lab_id='1',procedure_code='BANDA',procedure_type='ord',description='BANDS';
INSERT INTO procedure_type SET NAME='BARBITURATES SCRN. UR.' ,lab_id='1',procedure_code='BARB',procedure_type='ord',description='BARBITURATES SCRN. UR.';
INSERT INTO procedure_type SET NAME='BARBITURATE, UR' ,lab_id='1',procedure_code='BARBI',procedure_type='ord',description='BARBITURATE, UR';
INSERT INTO procedure_type SET NAME='BARBITURATE, UR CONF' ,lab_id='1',procedure_code='BARBIC',procedure_type='ord',description='BARBITURATE, UR CONF';
INSERT INTO procedure_type SET NAME='BARBITURATES GC/MS' ,lab_id='1',procedure_code='BARC',procedure_type='ord',description='BARBITURATES GC/MS';
INSERT INTO procedure_type SET NAME='BASOS' ,lab_id='1',procedure_code='BASOA',procedure_type='ord',description='BASOS';
INSERT INTO procedure_type SET NAME='BLOOD BANK COMMENT' ,lab_id='1',procedure_code='BBC',procedure_type='ord',description='BLOOD BANK COMMENT';
INSERT INTO procedure_type SET NAME='BBQC SPECIMEN ID' ,lab_id='1',procedure_code='BBID',procedure_type='ord',description='BBQC SPECIMEN ID';
INSERT INTO procedure_type SET NAME='BRILLIANT CRESYL BLU' ,lab_id='1',procedure_code='BCB',procedure_type='ord',description='BRILLIANT CRESYL BLU';
INSERT INTO procedure_type SET NAME='BILIRUBIN CRYSTALS' ,lab_id='1',procedure_code='BCBD',procedure_type='ord',description='BILIRUBIN CRYSTALS';
INSERT INTO procedure_type SET NAME='CD19 B CELLS %' ,lab_id='1',procedure_code='BCEL',procedure_type='ord',description='CD19 B CELLS %';
INSERT INTO procedure_type SET NAME='TRANS 14;18 (BCL1)' ,lab_id='1',procedure_code='BCL1',procedure_type='ord',description='TRANS 14;18 (BCL1)';
INSERT INTO procedure_type SET NAME='TRANS 14;18 (BCL2)' ,lab_id='1',procedure_code='BCL2',procedure_type='ord',description='TRANS 14;18 (BCL2)';
INSERT INTO procedure_type SET NAME='BCL6' ,lab_id='1',procedure_code='BCL6',procedure_type='ord',description='BCL6';
INSERT INTO procedure_type SET NAME='BCPM' ,lab_id='1',procedure_code='BCPM',procedure_type='ord',description='BCPM';
INSERT INTO procedure_type SET NAME='BCR ABL DNA ANALYSIS' ,lab_id='1',procedure_code='BCR',procedure_type='ord',description='BCR ABL DNA ANALYSIS';
INSERT INTO procedure_type SET NAME='BCR/ABL, QUANTITATIVE PCR' ,lab_id='1',procedure_code='BCRABL',procedure_type='ord',description='BCR/ABL, QUANTITATIVE PCR';
INSERT INTO procedure_type SET NAME='B CELL CROSSMATCH' ,lab_id='1',procedure_code='BCX',procedure_type='ord',description='B CELL CROSSMATCH';
INSERT INTO procedure_type SET NAME='Banding Technique(s):' ,lab_id='1',procedure_code='BD',procedure_type='ord',description='Banding Technique(s):';
INSERT INTO procedure_type SET NAME='DONOR ANTIBODY SCN' ,lab_id='1',procedure_code='BDAS',procedure_type='ord',description='DONOR ANTIBODY SCN';
INSERT INTO procedure_type SET NAME='DONOR DIRECT ANTIGLOBULIN TEST' ,lab_id='1',procedure_code='BDDAT',procedure_type='ord',description='DONOR DIRECT ANTIGLOBULIN TEST';
INSERT INTO procedure_type SET NAME='BETA GLOBIN DELETIONS' ,lab_id='1',procedure_code='BDEL',procedure_type='ord',description='BETA GLOBIN DELETIONS';
INSERT INTO procedure_type SET NAME='BETA D GLUCAN' ,lab_id='1',procedure_code='BDGLU',procedure_type='ord',description='BETA D GLUCAN';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='BEID',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='BENZODIAZEPINE CONFIRM' ,lab_id='1',procedure_code='BENC',procedure_type='ord',description='BENZODIAZEPINE CONFIRM';
INSERT INTO procedure_type SET NAME='BENZODIAZEPINE SCRN. UR.' ,lab_id='1',procedure_code='BENZ',procedure_type='ord',description='BENZODIAZEPINE SCRN. UR.';
INSERT INTO procedure_type SET NAME='BENZODIAZEP/METAB' ,lab_id='1',procedure_code='BENZO',procedure_type='ord',description='BENZODIAZEP/METAB';
INSERT INTO procedure_type SET NAME='BENZODIAZAPINE CONF' ,lab_id='1',procedure_code='BENZOC',procedure_type='ord',description='BENZODIAZAPINE CONF';
INSERT INTO procedure_type SET NAME='BASE EXCESS/DEFICIT' ,lab_id='1',procedure_code='BEX',procedure_type='ord',description='BASE EXCESS/DEFICIT';
INSERT INTO procedure_type SET NAME='BODY FLUID TYPE' ,lab_id='1',procedure_code='BFFT',procedure_type='ord',description='BODY FLUID TYPE';
INSERT INTO procedure_type SET NAME='BODY FLUID TYPE' ,lab_id='1',procedure_code='BFLT',procedure_type='ord',description='BODY FLUID TYPE';
INSERT INTO procedure_type SET NAME='BAGS STORED' ,lab_id='1',procedure_code='BFRZ',procedure_type='ord',description='BAGS STORED';
INSERT INTO procedure_type SET NAME='BAGS FROZEN' ,lab_id='1',procedure_code='BFRZEN',procedure_type='ord',description='BAGS FROZEN';
INSERT INTO procedure_type SET NAME='BODY FLUID TYPE' ,lab_id='1',procedure_code='BFT',procedure_type='ord',description='BODY FLUID TYPE';
INSERT INTO procedure_type SET NAME='C Typing' ,lab_id='1',procedure_code='BGC',procedure_type='ord',description='C Typing';
INSERT INTO procedure_type SET NAME='D Typing' ,lab_id='1',procedure_code='BGD',procedure_type='ord',description='D Typing';
INSERT INTO procedure_type SET NAME='E Typing' ,lab_id='1',procedure_code='BGE',procedure_type='ord',description='E Typing';
INSERT INTO procedure_type SET NAME='Ig HEAVY CHAIN GENE' ,lab_id='1',procedure_code='BGEN',procedure_type='ord',description='Ig HEAVY CHAIN GENE';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='BGID',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='BETA GLOBULIN' ,lab_id='1',procedure_code='BGL',procedure_type='ord',description='BETA GLOBULIN';
INSERT INTO procedure_type SET NAME='BETA GLOBULIN, URINE' ,lab_id='1',procedure_code='BGLU',procedure_type='ord',description='BETA GLOBULIN, URINE';
INSERT INTO procedure_type SET NAME='M Typing' ,lab_id='1',procedure_code='BGM',procedure_type='ord',description='M Typing';
INSERT INTO procedure_type SET NAME='N Typing' ,lab_id='1',procedure_code='BGN',procedure_type='ord',description='N Typing';
INSERT INTO procedure_type SET NAME='S Typing' ,lab_id='1',procedure_code='BGS',procedure_type='ord',description='S Typing';
INSERT INTO procedure_type SET NAME='B-GLOBIN DNA SEQUENCING' ,lab_id='1',procedure_code='BGSQ',procedure_type='ord',description='B-GLOBIN DNA SEQUENCING';
INSERT INTO procedure_type SET NAME='BETA HCG, CSF' ,lab_id='1',procedure_code='BHCGC',procedure_type='ord',description='BETA HCG, CSF';
INSERT INTO procedure_type SET NAME='HEMATOCRIT, BODY FLUID' ,lab_id='1',procedure_code='BHCT',procedure_type='ord',description='HEMATOCRIT, BODY FLUID';
INSERT INTO procedure_type SET NAME='HEMATOCRIT, BODY FLUID' ,lab_id='1',procedure_code='BHCTM',procedure_type='ord',description='HEMATOCRIT, BODY FLUID';
INSERT INTO procedure_type SET NAME='B.henselae IgG Screen' ,lab_id='1',procedure_code='BHIGS',procedure_type='ord',description='B.henselae IgG Screen';
INSERT INTO procedure_type SET NAME='B. HENSELAE IGG TITER' ,lab_id='1',procedure_code='BHIGT',procedure_type='ord',description='B. HENSELAE IGG TITER';
INSERT INTO procedure_type SET NAME='B.henselae IgM Screen' ,lab_id='1',procedure_code='BHIMS',procedure_type='ord',description='B.henselae IgM Screen';
INSERT INTO procedure_type SET NAME='B. HENSELAE IGM TITER' ,lab_id='1',procedure_code='BHIMT',procedure_type='ord',description='B. HENSELAE IGM TITER';
INSERT INTO procedure_type SET NAME='BETA-HYDROXYBUTYRATE' ,lab_id='1',procedure_code='BHOB',procedure_type='ord',description='BETA-HYDROXYBUTYRATE';
INSERT INTO procedure_type SET NAME='BILIRUBIN DIRECT' ,lab_id='1',procedure_code='BILD',procedure_type='ord',description='BILIRUBIN DIRECT';
INSERT INTO procedure_type SET NAME='BILE ACIDS, TOTAL' ,lab_id='1',procedure_code='BILET',procedure_type='ord',description='BILE ACIDS, TOTAL';
INSERT INTO procedure_type SET NAME='BILIRUBIN TOTAL' ,lab_id='1',procedure_code='BILT',procedure_type='ord',description='BILIRUBIN TOTAL';
INSERT INTO procedure_type SET NAME='TOTAL BILIRUBIN BF' ,lab_id='1',procedure_code='BILTBF',procedure_type='ord',description='TOTAL BILIRUBIN BF';
INSERT INTO procedure_type SET NAME='BIOTINIDASE DEFIC.' ,lab_id='1',procedure_code='BIOD',procedure_type='ord',description='BIOTINIDASE DEFIC.';
INSERT INTO procedure_type SET NAME='BIOTINIDASE, QL. PLASMA' ,lab_id='1',procedure_code='BIOT',procedure_type='ord',description='BIOTINIDASE, QL. PLASMA';
INSERT INTO procedure_type SET NAME='BIOTINIDASE' ,lab_id='1',procedure_code='BIOTI',procedure_type='ord',description='BIOTINIDASE';
INSERT INTO procedure_type SET NAME='NL BIRTH ORDER' ,lab_id='1',procedure_code='BIRTHO',procedure_type='ord',description='NL BIRTH ORDER';
INSERT INTO procedure_type SET NAME='BISACODYL' ,lab_id='1',procedure_code='BIS',procedure_type='ord',description='BISACODYL';
INSERT INTO procedure_type SET NAME='BILIRUBIN' ,lab_id='1',procedure_code='BIUA',procedure_type='ord',description='BILIRUBIN';
INSERT INTO procedure_type SET NAME='BKV DNA, QUANT PCR' ,lab_id='1',procedure_code='BKV',procedure_type='ord',description='BKV DNA, QUANT PCR';
INSERT INTO procedure_type SET NAME='BKV quant Interp (MD):' ,lab_id='1',procedure_code='BKVPF',procedure_type='ord',description='BKV quant Interp (MD):';
INSERT INTO procedure_type SET NAME='BKV DNA PCR, URINE' ,lab_id='1',procedure_code='BKVU',procedure_type='ord',description='BKV DNA PCR, URINE';
INSERT INTO procedure_type SET NAME='BLASTOMYCES ANTIBODY' ,lab_id='1',procedure_code='BLAS',procedure_type='ord',description='BLASTOMYCES ANTIBODY';
INSERT INTO procedure_type SET NAME='Block number:' ,lab_id='1',procedure_code='BLCKN',procedure_type='ord',description='Block number:';
INSERT INTO procedure_type SET NAME='BLOOM SYNDROME TEST' ,lab_id='1',procedure_code='BLOO',procedure_type='ord',description='BLOOM SYNDROME TEST';
INSERT INTO procedure_type SET NAME='BLASTS' ,lab_id='1',procedure_code='BLSTA',procedure_type='ord',description='BLASTS';
INSERT INTO procedure_type SET NAME='BLOOD TEST' ,lab_id='1',procedure_code='BLT1',procedure_type='ord',description='BLOOD TEST';
INSERT INTO procedure_type SET NAME='By LMP or US:' ,lab_id='1',procedure_code='BLUS',procedure_type='ord',description='By LMP or US:';
INSERT INTO procedure_type SET NAME='bcr/abl TRANSLOCATION BM' ,lab_id='1',procedure_code='BMBCR',procedure_type='ord',description='bcr/abl TRANSLOCATION BM';
INSERT INTO procedure_type SET NAME='DONOR' ,lab_id='1',procedure_code='BMDON',procedure_type='ord',description='DONOR';
INSERT INTO procedure_type SET NAME='TOTAL NUCLEATED CELLS' ,lab_id='1',procedure_code='BMNCT',procedure_type='ord',description='TOTAL NUCLEATED CELLS';
INSERT INTO procedure_type SET NAME='BM NUMBER' ,lab_id='1',procedure_code='BMNUM',procedure_type='ord',description='BM NUMBER';
INSERT INTO procedure_type SET NAME='BONE MARROW SOURCE' ,lab_id='1',procedure_code='BMSRC',procedure_type='ord',description='BONE MARROW SOURCE';
INSERT INTO procedure_type SET NAME='BMT TYPE' ,lab_id='1',procedure_code='BMT',procedure_type='ord',description='BMT TYPE';
INSERT INTO procedure_type SET NAME='B-NATRIURETIC PEPTIDE' ,lab_id='1',procedure_code='BNP',procedure_type='ord',description='B-NATRIURETIC PEPTIDE';
INSERT INTO procedure_type SET NAME='NAFCILLIN' ,lab_id='1',procedure_code='BOXA',procedure_type='ord',description='NAFCILLIN';
INSERT INTO procedure_type SET NAME='BP 180, S' ,lab_id='1',procedure_code='BP180',procedure_type='ord',description='BP 180, S';
INSERT INTO procedure_type SET NAME='BP 230, S' ,lab_id='1',procedure_code='BP230',procedure_type='ord',description='BP 230, S';
INSERT INTO procedure_type SET NAME='IGF BINDING PROTEIN 3' ,lab_id='1',procedure_code='BP3',procedure_type='ord',description='IGF BINDING PROTEIN 3';
INSERT INTO procedure_type SET NAME='B. PERTUSSIS IgA' ,lab_id='1',procedure_code='BPA',procedure_type='ord',description='B. PERTUSSIS IgA';
INSERT INTO procedure_type SET NAME='B. PERTUSSIS, IgG' ,lab_id='1',procedure_code='BPG',procedure_type='ord',description='B. PERTUSSIS, IgG';
INSERT INTO procedure_type SET NAME='B.Quintana IgG' ,lab_id='1',procedure_code='BQIGS',procedure_type='ord',description='B.Quintana IgG';
INSERT INTO procedure_type SET NAME='B. QUINTANA IGG TITER' ,lab_id='1',procedure_code='BQIGT',procedure_type='ord',description='B. QUINTANA IGG TITER';
INSERT INTO procedure_type SET NAME='B.quintana IgM Screen' ,lab_id='1',procedure_code='BQIMS',procedure_type='ord',description='B.quintana IgM Screen';
INSERT INTO procedure_type SET NAME='B. QUINTANA IGM TITER' ,lab_id='1',procedure_code='BQIMT',procedure_type='ord',description='B. QUINTANA IGM TITER';
INSERT INTO procedure_type SET NAME='Banding Resolution:' ,lab_id='1',procedure_code='BR',procedure_type='ord',description='Banding Resolution:';
INSERT INTO procedure_type SET NAME='BREATH HYDROGEN TEST' ,lab_id='1',procedure_code='BRH2',procedure_type='ord',description='BREATH HYDROGEN TEST';
INSERT INTO procedure_type SET NAME='BRUCELLA AB, AGGLUTINATION' ,lab_id='1',procedure_code='BRUA',procedure_type='ord',description='BRUCELLA AB, AGGLUTINATION';
INSERT INTO procedure_type SET NAME='BRUCELLA AGGLUTININS' ,lab_id='1',procedure_code='BRUC',procedure_type='ord',description='BRUCELLA AGGLUTININS';
INSERT INTO procedure_type SET NAME='BRUCELLA IgG' ,lab_id='1',procedure_code='BRUG',procedure_type='ord',description='BRUCELLA IgG';
INSERT INTO procedure_type SET NAME='BRUCELLA IgM' ,lab_id='1',procedure_code='BRUM',procedure_type='ord',description='BRUCELLA IgM';
INSERT INTO procedure_type SET NAME='BONE SPECIFIC ALK PHOS' ,lab_id='1',procedure_code='BSAP',procedure_type='ord',description='BONE SPECIFIC ALK PHOS';
INSERT INTO procedure_type SET NAME='BSL ID NUMBER' ,lab_id='1',procedure_code='BSLID',procedure_type='ord',description='BSL ID NUMBER';
INSERT INTO procedure_type SET NAME='BSL ID STEM DNR' ,lab_id='1',procedure_code='BSLIDX',procedure_type='ord',description='BSL ID STEM DNR';
INSERT INTO procedure_type SET NAME='BLEEDING TIME' ,lab_id='1',procedure_code='BT',procedure_type='ord',description='BLEEDING TIME';
INSERT INTO procedure_type SET NAME='BETA THAL MUTATIONS' ,lab_id='1',procedure_code='BTHL',procedure_type='ord',description='BETA THAL MUTATIONS';
INSERT INTO procedure_type SET NAME='TEST NAME' ,lab_id='1',procedure_code='BTNAME',procedure_type='ord',description='TEST NAME';
INSERT INTO procedure_type SET NAME='REFERRAL LABORATORY' ,lab_id='1',procedure_code='BTREF',procedure_type='ord',description='REFERRAL LABORATORY';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='BTRESU',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='MATURE TRYPTASE' ,lab_id='1',procedure_code='BTRYP',procedure_type='ord',description='MATURE TRYPTASE';
INSERT INTO procedure_type SET NAME='BUSULFAN DOSE' ,lab_id='1',procedure_code='BU1',procedure_type='ord',description='BUSULFAN DOSE';
INSERT INTO procedure_type SET NAME='RECOMMENDED DOSE' ,lab_id='1',procedure_code='BU10',procedure_type='ord',description='RECOMMENDED DOSE';
INSERT INTO procedure_type SET NAME='START DATE' ,lab_id='1',procedure_code='BU2',procedure_type='ord',description='START DATE';
INSERT INTO procedure_type SET NAME='BUSULFAN START TIME' ,lab_id='1',procedure_code='BU3',procedure_type='ord',description='BUSULFAN START TIME';
INSERT INTO procedure_type SET NAME='STOP DATE' ,lab_id='1',procedure_code='BU4',procedure_type='ord',description='STOP DATE';
INSERT INTO procedure_type SET NAME='BUSULFAN STOP TIME' ,lab_id='1',procedure_code='BU5',procedure_type='ord',description='BUSULFAN STOP TIME';
INSERT INTO procedure_type SET NAME='WEIGHT' ,lab_id='1',procedure_code='BU6',procedure_type='ord',description='WEIGHT';
INSERT INTO procedure_type SET NAME='PATIENT AGE' ,lab_id='1',procedure_code='BU7',procedure_type='ord',description='PATIENT AGE';
INSERT INTO procedure_type SET NAME='AREA UNDER THE CURVE' ,lab_id='1',procedure_code='BU8',procedure_type='ord',description='AREA UNDER THE CURVE';
INSERT INTO procedure_type SET NAME='CLEARANCE' ,lab_id='1',procedure_code='BU9',procedure_type='ord',description='CLEARANCE';
INSERT INTO procedure_type SET NAME='DOSE NO:' ,lab_id='1',procedure_code='BUD',procedure_type='ord',description='DOSE NO:';
INSERT INTO procedure_type SET NAME='DOSAGE (mg q 6hr):' ,lab_id='1',procedure_code='BUD1',procedure_type='ord',description='DOSAGE (mg q 6hr):';
INSERT INTO procedure_type SET NAME='INFUSION START TIME:' ,lab_id='1',procedure_code='BUD2',procedure_type='ord',description='INFUSION START TIME:';
INSERT INTO procedure_type SET NAME='INFUSION STOP TIME:' ,lab_id='1',procedure_code='BUD3',procedure_type='ord',description='INFUSION STOP TIME:';
INSERT INTO procedure_type SET NAME='DOSAGE DATE:' ,lab_id='1',procedure_code='BUDD',procedure_type='ord',description='DOSAGE DATE:';
INSERT INTO procedure_type SET NAME='SAMPLE 1 (PRE-DOSE):' ,lab_id='1',procedure_code='BUDS1',procedure_type='ord',description='SAMPLE 1 (PRE-DOSE):';
INSERT INTO procedure_type SET NAME='SAMPLE 2:' ,lab_id='1',procedure_code='BUDS2',procedure_type='ord',description='SAMPLE 2:';
INSERT INTO procedure_type SET NAME='SAMPLE 3:' ,lab_id='1',procedure_code='BUDS3',procedure_type='ord',description='SAMPLE 3:';
INSERT INTO procedure_type SET NAME='SAMPLE 4:' ,lab_id='1',procedure_code='BUDS4',procedure_type='ord',description='SAMPLE 4:';
INSERT INTO procedure_type SET NAME='SAMPLE 5:' ,lab_id='1',procedure_code='BUDS5',procedure_type='ord',description='SAMPLE 5:';
INSERT INTO procedure_type SET NAME='BUN' ,lab_id='1',procedure_code='BUN',procedure_type='ord',description='BUN';
INSERT INTO procedure_type SET NAME='POST DIALYSIS BUN' ,lab_id='1',procedure_code='BUNPST',procedure_type='ord',description='POST DIALYSIS BUN';
INSERT INTO procedure_type SET NAME='BUPROPION' ,lab_id='1',procedure_code='BUPR',procedure_type='ord',description='BUPROPION';
INSERT INTO procedure_type SET NAME='BULSULFAN ZERO RESULT:' ,lab_id='1',procedure_code='BUS0',procedure_type='ord',description='BULSULFAN ZERO RESULT:';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT (1):' ,lab_id='1',procedure_code='BUS1',procedure_type='ord',description='BUSULFAN RESULT (1):';
INSERT INTO procedure_type SET NAME='BUSULFAN, 1 HOUR' ,lab_id='1',procedure_code='BUS1HR',procedure_type='ord',description='BUSULFAN, 1 HOUR';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT (2):' ,lab_id='1',procedure_code='BUS2',procedure_type='ord',description='BUSULFAN RESULT (2):';
INSERT INTO procedure_type SET NAME='BUSULFAN, 2 HOUR' ,lab_id='1',procedure_code='BUS2HR',procedure_type='ord',description='BUSULFAN, 2 HOUR';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT (3):' ,lab_id='1',procedure_code='BUS3',procedure_type='ord',description='BUSULFAN RESULT (3):';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT (4):' ,lab_id='1',procedure_code='BUS4',procedure_type='ord',description='BUSULFAN RESULT (4):';
INSERT INTO procedure_type SET NAME='BUSULFAN, 4 HOUR' ,lab_id='1',procedure_code='BUS4HR',procedure_type='ord',description='BUSULFAN, 4 HOUR';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 1' ,lab_id='1',procedure_code='BUSAR1',procedure_type='ord',description='BUSULFAN RESULT 1';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 2' ,lab_id='1',procedure_code='BUSAR2',procedure_type='ord',description='BUSULFAN RESULT 2';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 3' ,lab_id='1',procedure_code='BUSAR3',procedure_type='ord',description='BUSULFAN RESULT 3';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 4' ,lab_id='1',procedure_code='BUSAR4',procedure_type='ord',description='BUSULFAN RESULT 4';
INSERT INTO procedure_type SET NAME='BUSULFAN, BASELINE' ,lab_id='1',procedure_code='BUSBSL',procedure_type='ord',description='BUSULFAN, BASELINE';
INSERT INTO procedure_type SET NAME='DOSE NUMBER' ,lab_id='1',procedure_code='BUSDN',procedure_type='ord',description='DOSE NUMBER';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 1' ,lab_id='1',procedure_code='BUSRP1',procedure_type='ord',description='BUSULFAN RESULT 1';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 2' ,lab_id='1',procedure_code='BUSRP2',procedure_type='ord',description='BUSULFAN RESULT 2';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 3' ,lab_id='1',procedure_code='BUSRP3',procedure_type='ord',description='BUSULFAN RESULT 3';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 4' ,lab_id='1',procedure_code='BUSRP4',procedure_type='ord',description='BUSULFAN RESULT 4';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 5' ,lab_id='1',procedure_code='BUSRP5',procedure_type='ord',description='BUSULFAN RESULT 5';
INSERT INTO procedure_type SET NAME='BUSULFAN RESULT 6' ,lab_id='1',procedure_code='BUSRP6',procedure_type='ord',description='BUSULFAN RESULT 6';
INSERT INTO procedure_type SET NAME='BUSULFAN START DATE' ,lab_id='1',procedure_code='BUSTDA',procedure_type='ord',description='BUSULFAN START DATE';
INSERT INTO procedure_type SET NAME='BUSULFAN STOP TIME' ,lab_id='1',procedure_code='BUSTT',procedure_type='ord',description='BUSULFAN STOP TIME';
INSERT INTO procedure_type SET NAME='BUSULFAN DOSE' ,lab_id='1',procedure_code='BUSUD',procedure_type='ord',description='BUSULFAN DOSE';
INSERT INTO procedure_type SET NAME='WEIGHT' ,lab_id='1',procedure_code='BUWT',procedure_type='ord',description='WEIGHT';
INSERT INTO procedure_type SET NAME='BECKWITH-WIEDEMANN SYN' ,lab_id='1',procedure_code='BWS',procedure_type='ord',description='BECKWITH-WIEDEMANN SYN';
INSERT INTO procedure_type SET NAME='CONTROL, 0HR' ,lab_id='1',procedure_code='C0',procedure_type='ord',description='CONTROL, 0HR';
INSERT INTO procedure_type SET NAME='CONTROL, 1HR' ,lab_id='1',procedure_code='C1',procedure_type='ord',description='CONTROL, 1HR';
INSERT INTO procedure_type SET NAME='CA 125' ,lab_id='1',procedure_code='C125',procedure_type='ord',description='CA 125';
INSERT INTO procedure_type SET NAME='C1 ESTER INHIB ANTIG' ,lab_id='1',procedure_code='C1EQ',procedure_type='ord',description='C1 ESTER INHIB ANTIG';
INSERT INTO procedure_type SET NAME='C1Q ANTIGEN' ,lab_id='1',procedure_code='C1QQ',procedure_type='ord',description='C1Q ANTIGEN';
INSERT INTO procedure_type SET NAME='C1 ESTER INHIB (C1R)' ,lab_id='1',procedure_code='C1R',procedure_type='ord',description='C1 ESTER INHIB (C1R)';
INSERT INTO procedure_type SET NAME='COMPLEMENT C3, SERUM' ,lab_id='1',procedure_code='C3',procedure_type='ord',description='COMPLEMENT C3, SERUM';
INSERT INTO procedure_type SET NAME='CREDIT: RSV direct FA' ,lab_id='1',procedure_code='C334',procedure_type='ord',description='CREDIT: RSV direct FA';
INSERT INTO procedure_type SET NAME='CD3 DOSE' ,lab_id='1',procedure_code='C3DS',procedure_type='ord',description='CD3 DOSE';
INSERT INTO procedure_type SET NAME='COMPLEMENT C4, SERUM' ,lab_id='1',procedure_code='C4',procedure_type='ord',description='COMPLEMENT C4, SERUM';
INSERT INTO procedure_type SET NAME='CD4 DOSE' ,lab_id='1',procedure_code='C4DS',procedure_type='ord',description='CD4 DOSE';
INSERT INTO procedure_type SET NAME='CALCIUM' ,lab_id='1',procedure_code='CA',procedure_type='ord',description='CALCIUM';
INSERT INTO procedure_type SET NAME='CA 15-3' ,lab_id='1',procedure_code='CA15',procedure_type='ord',description='CA 15-3';
INSERT INTO procedure_type SET NAME='CA 19-9' ,lab_id='1',procedure_code='CA19',procedure_type='ord',description='CA 19-9';
INSERT INTO procedure_type SET NAME='A1A CLEARANCE' ,lab_id='1',procedure_code='CA1A',procedure_type='ord',description='A1A CLEARANCE';
INSERT INTO procedure_type SET NAME='CA 27.29' ,lab_id='1',procedure_code='CA27',procedure_type='ord',description='CA 27.29';
INSERT INTO procedure_type SET NAME='CONGENITAL ADRENAL HYPERPLASIA' ,lab_id='1',procedure_code='CADH',procedure_type='ord',description='CONGENITAL ADRENAL HYPERPLASIA';
INSERT INTO procedure_type SET NAME='ADDITIONAL INFO' ,lab_id='1',procedure_code='CADI',procedure_type='ord',description='ADDITIONAL INFO';
INSERT INTO procedure_type SET NAME='CADAVER DONOR UNOS ID' ,lab_id='1',procedure_code='CADID',procedure_type='ord',description='CADAVER DONOR UNOS ID';
INSERT INTO procedure_type SET NAME='CADMIUM' ,lab_id='1',procedure_code='CADM',procedure_type='ord',description='CADMIUM';
INSERT INTO procedure_type SET NAME='CHLOROACETATE ESTERASE STAIN' ,lab_id='1',procedure_code='CAES',procedure_type='ord',description='CHLOROACETATE ESTERASE STAIN';
INSERT INTO procedure_type SET NAME='CAFFEINE' ,lab_id='1',procedure_code='CAFF',procedure_type='ord',description='CAFFEINE';
INSERT INTO procedure_type SET NAME='COLD AGGLUTININS' ,lab_id='1',procedure_code='CAGG',procedure_type='ord',description='COLD AGGLUTININS';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN, WHOLE BLOOD' ,lab_id='1',procedure_code='CAHB',procedure_type='ord',description='HEMOGLOBIN, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='CALCIUM IONIZED, SERUM' ,lab_id='1',procedure_code='CAI',procedure_type='ord',description='CALCIUM IONIZED, SERUM';
INSERT INTO procedure_type SET NAME='CALCIUM, WHOLE BLOOD' ,lab_id='1',procedure_code='CAIB',procedure_type='ord',description='CALCIUM, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='CALCITONIN' ,lab_id='1',procedure_code='CALCI',procedure_type='ord',description='CALCITONIN';
INSERT INTO procedure_type SET NAME='HGB (CALCULATED)' ,lab_id='1',procedure_code='CALHB',procedure_type='ord',description='HGB (CALCULATED)';
INSERT INTO procedure_type SET NAME='CAP SURVEY ID' ,lab_id='1',procedure_code='CAPID',procedure_type='ord',description='CAP SURVEY ID';
INSERT INTO procedure_type SET NAME='CAPREOMYCIN' ,lab_id='1',procedure_code='CAPRE',procedure_type='ord',description='CAPREOMYCIN';
INSERT INTO procedure_type SET NAME='CARBENICILLIN' ,lab_id='1',procedure_code='CARBEN',procedure_type='ord',description='CARBENICILLIN';
INSERT INTO procedure_type SET NAME='CARBOHYDRATES (TLC)' ,lab_id='1',procedure_code='CARBO',procedure_type='ord',description='CARBOHYDRATES (TLC)';
INSERT INTO procedure_type SET NAME='CARNITINE, ESTERS' ,lab_id='1',procedure_code='CARE',procedure_type='ord',description='CARNITINE, ESTERS';
INSERT INTO procedure_type SET NAME='CARNITINE, FREE' ,lab_id='1',procedure_code='CARF',procedure_type='ord',description='CARNITINE, FREE';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='CARI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='CARNOSINE' ,lab_id='1',procedure_code='CARNO',procedure_type='ord',description='CARNOSINE';
INSERT INTO procedure_type SET NAME='CARNITINE, TOTAL' ,lab_id='1',procedure_code='CARNT',procedure_type='ord',description='CARNITINE, TOTAL';
INSERT INTO procedure_type SET NAME='CAROTENE' ,lab_id='1',procedure_code='CARO',procedure_type='ord',description='CAROTENE';
INSERT INTO procedure_type SET NAME='ACYL/FREE CARN RATIO' ,lab_id='1',procedure_code='CARR',procedure_type='ord',description='ACYL/FREE CARN RATIO';
INSERT INTO procedure_type SET NAME='Case number:' ,lab_id='1',procedure_code='CASEN',procedure_type='ord',description='Case number:';
INSERT INTO procedure_type SET NAME='CASPOFUNGIN' ,lab_id='1',procedure_code='CASP',procedure_type='ord',description='CASPOFUNGIN';
INSERT INTO procedure_type SET NAME='CASTS' ,lab_id='1',procedure_code='CAST',procedure_type='ord',description='CASTS';
INSERT INTO procedure_type SET NAME='CATECHOLAMINES, TOTAL' ,lab_id='1',procedure_code='CATT',procedure_type='ord',description='CATECHOLAMINES, TOTAL';
INSERT INTO procedure_type SET NAME='CALCIUM, URINE' ,lab_id='1',procedure_code='CAUR',procedure_type='ord',description='CALCIUM, URINE';
INSERT INTO procedure_type SET NAME='CALCIUM PER DAY, UR' ,lab_id='1',procedure_code='CAURD',procedure_type='ord',description='CALCIUM PER DAY, UR';
INSERT INTO procedure_type SET NAME='CALCIUM, TOTAL UR' ,lab_id='1',procedure_code='CAUT',procedure_type='ord',description='CALCIUM, TOTAL UR';
INSERT INTO procedure_type SET NAME='CA*PHOS PRODUCT' ,lab_id='1',procedure_code='CAXP',procedure_type='ord',description='CA*PHOS PRODUCT';
INSERT INTO procedure_type SET NAME='CALLED BY' ,lab_id='1',procedure_code='CB',procedure_type='ord',description='CALLED BY';
INSERT INTO procedure_type SET NAME='C BANDING' ,lab_id='1',procedure_code='CBAND',procedure_type='ord',description='C BANDING';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CBCI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='CBCMD',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='Smear sent for review' ,lab_id='1',procedure_code='CBCR',procedure_type='ord',description='Smear sent for review';
INSERT INTO procedure_type SET NAME='CORD BLOOD DONOR' ,lab_id='1',procedure_code='CBDON',procedure_type='ord',description='CORD BLOOD DONOR';
INSERT INTO procedure_type SET NAME='CORD BLOOD NUMBER' ,lab_id='1',procedure_code='CBNUM',procedure_type='ord',description='CORD BLOOD NUMBER';
INSERT INTO procedure_type SET NAME='CORD BLOOD SOURCE' ,lab_id='1',procedure_code='CBSRC',procedure_type='ord',description='CORD BLOOD SOURCE';
INSERT INTO procedure_type SET NAME='CORD BLOOD VOLUME' ,lab_id='1',procedure_code='CBVOL',procedure_type='ord',description='CORD BLOOD VOLUME';
INSERT INTO procedure_type SET NAME='CORD BLOOD WBC COUNT' ,lab_id='1',procedure_code='CBWBC',procedure_type='ord',description='CORD BLOOD WBC COUNT';
INSERT INTO procedure_type SET NAME='CBC WITH DIF' ,lab_id='1',procedure_code='CCBCD',procedure_type='ord',description='CBC WITH DIF';
INSERT INTO procedure_type SET NAME='CHOLESTEROL CRYSTALS' ,lab_id='1',procedure_code='CCBD',procedure_type='ord',description='CHOLESTEROL CRYSTALS';
INSERT INTO procedure_type SET NAME='CUMULATIVE CD34' ,lab_id='1',procedure_code='CCD34D',procedure_type='ord',description='CUMULATIVE CD34';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CCDBI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='CCDBMD',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CCDCI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='CCDCMD',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='PLATELET COUNT' ,lab_id='1',procedure_code='CCP',procedure_type='ord',description='PLATELET COUNT';
INSERT INTO procedure_type SET NAME='CARDIO C REACTIVE PROTEIN' ,lab_id='1',procedure_code='CCRP',procedure_type='ord',description='CARDIO C REACTIVE PROTEIN';
INSERT INTO procedure_type SET NAME='CHROM CULTURE ONLY' ,lab_id='1',procedure_code='CCUL',procedure_type='ord',description='CHROM CULTURE ONLY';
INSERT INTO procedure_type SET NAME='CONT + DEF PL, 0HR' ,lab_id='1',procedure_code='CD0',procedure_type='ord',description='CONT + DEF PL, 0HR';
INSERT INTO procedure_type SET NAME='CONT + DEF PL, 1HR' ,lab_id='1',procedure_code='CD1',procedure_type='ord',description='CONT + DEF PL, 1HR';
INSERT INTO procedure_type SET NAME='CD34 STEM CELLS %' ,lab_id='1',procedure_code='CD34',procedure_type='ord',description='CD34 STEM CELLS %';
INSERT INTO procedure_type SET NAME='CD34 STEM CELLS % 2ND' ,lab_id='1',procedure_code='CD342',procedure_type='ord',description='CD34 STEM CELLS % 2ND';
INSERT INTO procedure_type SET NAME='CD34 CELL DOSE' ,lab_id='1',procedure_code='CD34D',procedure_type='ord',description='CD34 CELL DOSE';
INSERT INTO procedure_type SET NAME='CD34 ENRICHMENT-60' ,lab_id='1',procedure_code='CD34E1',procedure_type='ord',description='CD34 ENRICHMENT-60';
INSERT INTO procedure_type SET NAME='CD34 ENRICHMENT-120' ,lab_id='1',procedure_code='CD34E2',procedure_type='ord',description='CD34 ENRICHMENT-120';
INSERT INTO procedure_type SET NAME='TOTAL CD34 POS CELLS' ,lab_id='1',procedure_code='CD34T',procedure_type='ord',description='TOTAL CD34 POS CELLS';
INSERT INTO procedure_type SET NAME='CD34 DOSE INFUSED' ,lab_id='1',procedure_code='CD34TX',procedure_type='ord',description='CD34 DOSE INFUSED';
INSERT INTO procedure_type SET NAME='CD3 %, Total' ,lab_id='1',procedure_code='CD3T',procedure_type='ord',description='CD3 %, Total';
INSERT INTO procedure_type SET NAME='CD3 %, Total, 2ND' ,lab_id='1',procedure_code='CD3T2',procedure_type='ord',description='CD3 %, Total, 2ND';
INSERT INTO procedure_type SET NAME='CD3 %, Total, 3RD' ,lab_id='1',procedure_code='CD3T3',procedure_type='ord',description='CD3 %, Total, 3RD';
INSERT INTO procedure_type SET NAME='CD3 DOSE INFUSED' ,lab_id='1',procedure_code='CD3TX',procedure_type='ord',description='CD3 DOSE INFUSED';
INSERT INTO procedure_type SET NAME='CD45+ LYMPHS' ,lab_id='1',procedure_code='CD45',procedure_type='ord',description='CD45+ LYMPHS';
INSERT INTO procedure_type SET NAME='CD8 T CELLS (CALC)' ,lab_id='1',procedure_code='CD8C',procedure_type='ord',description='CD8 T CELLS (CALC)';
INSERT INTO procedure_type SET NAME='MORPHOLOGY DESCRIPTION' ,lab_id='1',procedure_code='CDESC',procedure_type='ord',description='MORPHOLOGY DESCRIPTION';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CDTI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='Cells Counted:' ,lab_id='1',procedure_code='CE',procedure_type='ord',description='Cells Counted:';
INSERT INTO procedure_type SET NAME='CARCINOEMBRYONIC AG' ,lab_id='1',procedure_code='CEA',procedure_type='ord',description='CARCINOEMBRYONIC AG';
INSERT INTO procedure_type SET NAME='CEA, PANCREATIC CYST' ,lab_id='1',procedure_code='CEAPC',procedure_type='ord',description='CEA, PANCREATIC CYST';
INSERT INTO procedure_type SET NAME='CEFUROXIME' ,lab_id='1',procedure_code='CEFUR',procedure_type='ord',description='CEFUROXIME';
INSERT INTO procedure_type SET NAME='CELL ISOLATION' ,lab_id='1',procedure_code='CELI',procedure_type='ord',description='CELL ISOLATION';
INSERT INTO procedure_type SET NAME='CELL VIABILITY' ,lab_id='1',procedure_code='CELV',procedure_type='ord',description='CELL VIABILITY';
INSERT INTO procedure_type SET NAME='CENTRIFUGE' ,lab_id='1',procedure_code='CEN',procedure_type='ord',description='CENTRIFUGE';
INSERT INTO procedure_type SET NAME='CEPHALOTHIN' ,lab_id='1',procedure_code='CEPH',procedure_type='ord',description='CEPHALOTHIN';
INSERT INTO procedure_type SET NAME='CERULOPLASMIN' ,lab_id='1',procedure_code='CERU',procedure_type='ord',description='CERULOPLASMIN';
INSERT INTO procedure_type SET NAME='CEFOXITIN' ,lab_id='1',procedure_code='CFOX',procedure_type='ord',description='CEFOXITIN';
INSERT INTO procedure_type SET NAME='CFU PER KG' ,lab_id='1',procedure_code='CFUKG',procedure_type='ord',description='CFU PER KG';
INSERT INTO procedure_type SET NAME='CHROMOGRANIN A' ,lab_id='1',procedure_code='CGA',procedure_type='ord',description='CHROMOGRANIN A';
INSERT INTO procedure_type SET NAME='CGA, CHEMILUMINESCENT' ,lab_id='1',procedure_code='CGACL',procedure_type='ord',description='CGA, CHEMILUMINESCENT';
INSERT INTO procedure_type SET NAME='Calculated Gest.Age:' ,lab_id='1',procedure_code='CGEST',procedure_type='ord',description='Calculated Gest.Age:';
INSERT INTO procedure_type SET NAME='eGFR CAUCASIAN' ,lab_id='1',procedure_code='CGFR',procedure_type='ord',description='eGFR CAUCASIAN';
INSERT INTO procedure_type SET NAME='CHOLYLGLYCINE' ,lab_id='1',procedure_code='CGLY',procedure_type='ord',description='CHOLYLGLYCINE';
INSERT INTO procedure_type SET NAME='COMPLEMENT CH50' ,lab_id='1',procedure_code='CH50R',procedure_type='ord',description='COMPLEMENT CH50';
INSERT INTO procedure_type SET NAME='CHOLINESTERASE RBC' ,lab_id='1',procedure_code='CHER',procedure_type='ord',description='CHOLINESTERASE RBC';
INSERT INTO procedure_type SET NAME='CHLMY' ,lab_id='1',procedure_code='CHLMY',procedure_type='ord',description='CHLMY';
INSERT INTO procedure_type SET NAME='CHLORAMPHENICOL' ,lab_id='1',procedure_code='CHLORO',procedure_type='ord',description='CHLORAMPHENICOL';
INSERT INTO procedure_type SET NAME='CHOLESTEROL, TOTAL' ,lab_id='1',procedure_code='CHOL',procedure_type='ord',description='CHOLESTEROL, TOTAL';
INSERT INTO procedure_type SET NAME='CHOL HDL RATIO' ,lab_id='1',procedure_code='CHOR',procedure_type='ord',description='CHOL HDL RATIO';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANALYSIS' ,lab_id='1',procedure_code='CHRM',procedure_type='ord',description='CHROMOSOME ANALYSIS';
INSERT INTO procedure_type SET NAME='CHROMIUM' ,lab_id='1',procedure_code='CHRO',procedure_type='ord',description='CHROMIUM';
INSERT INTO procedure_type SET NAME='CHROMIUM, RANDOM UR' ,lab_id='1',procedure_code='CHROR1',procedure_type='ord',description='CHROMIUM, RANDOM UR';
INSERT INTO procedure_type SET NAME='CHROMIUM/CREAT RATIO' ,lab_id='1',procedure_code='CHROR2',procedure_type='ord',description='CHROMIUM/CREAT RATIO';
INSERT INTO procedure_type SET NAME='CIPROFLOXACIN' ,lab_id='1',procedure_code='CIPRO',procedure_type='ord',description='CIPROFLOXACIN';
INSERT INTO procedure_type SET NAME='CITRIC ACID, RANDOM UR' ,lab_id='1',procedure_code='CITRR',procedure_type='ord',description='CITRIC ACID, RANDOM UR';
INSERT INTO procedure_type SET NAME='CITRULLINE' ,lab_id='1',procedure_code='CITRUL',procedure_type='ord',description='CITRULLINE';
INSERT INTO procedure_type SET NAME='CITRIC ACID, SERUM' ,lab_id='1',procedure_code='CITS',procedure_type='ord',description='CITRIC ACID, SERUM';
INSERT INTO procedure_type SET NAME='CRYSTALS, JOINT FLUID' ,lab_id='1',procedure_code='CJF',procedure_type='ord',description='CRYSTALS, JOINT FLUID';
INSERT INTO procedure_type SET NAME='CK, TOTAL' ,lab_id='1',procedure_code='CK',procedure_type='ord',description='CK, TOTAL';
INSERT INTO procedure_type SET NAME='CHLORIDE' ,lab_id='1',procedure_code='CL',procedure_type='ord',description='CHLORIDE';
INSERT INTO procedure_type SET NAME='CLARITHROMYCIN' ,lab_id='1',procedure_code='CLARI',procedure_type='ord',description='CLARITHROMYCIN';
INSERT INTO procedure_type SET NAME='CHLORIDE, BODY FLUID' ,lab_id='1',procedure_code='CLB',procedure_type='ord',description='CHLORIDE, BODY FLUID';
INSERT INTO procedure_type SET NAME='CLINICAL INFORMATION' ,lab_id='1',procedure_code='CLIN',procedure_type='ord',description='CLINICAL INFORMATION';
INSERT INTO procedure_type SET NAME='CLINDAMYCIN' ,lab_id='1',procedure_code='CLINDA',procedure_type='ord',description='CLINDAMYCIN';
INSERT INTO procedure_type SET NAME='ANTI CARDIOLIPIN AB' ,lab_id='1',procedure_code='CLIP',procedure_type='ord',description='ANTI CARDIOLIPIN AB';
INSERT INTO procedure_type SET NAME='CLOMIPRAMINE' ,lab_id='1',procedure_code='CLMI',procedure_type='ord',description='CLOMIPRAMINE';
INSERT INTO procedure_type SET NAME='CHLAMYDIA CF ANTIB' ,lab_id='1',procedure_code='CLMY',procedure_type='ord',description='CHLAMYDIA CF ANTIB';
INSERT INTO procedure_type SET NAME='CLOFAZIMIN' ,lab_id='1',procedure_code='CLOF',procedure_type='ord',description='CLOFAZIMIN';
INSERT INTO procedure_type SET NAME='CLONAZEPAM' ,lab_id='1',procedure_code='CLON',procedure_type='ord',description='CLONAZEPAM';
INSERT INTO procedure_type SET NAME='WHOLE BLD CLOT STABILITY' ,lab_id='1',procedure_code='CLOT',procedure_type='ord',description='WHOLE BLD CLOT STABILITY';
INSERT INTO procedure_type SET NAME='CHLORIDE, STOOL' ,lab_id='1',procedure_code='CLST',procedure_type='ord',description='CHLORIDE, STOOL';
INSERT INTO procedure_type SET NAME='CHLORIDE, SWEAT' ,lab_id='1',procedure_code='CLSW',procedure_type='ord',description='CHLORIDE, SWEAT';
INSERT INTO procedure_type SET NAME='CHLORIDE PER DAY, UR' ,lab_id='1',procedure_code='CLUD',procedure_type='ord',description='CHLORIDE PER DAY, UR';
INSERT INTO procedure_type SET NAME='CHLORIDE, URINE' ,lab_id='1',procedure_code='CLUR',procedure_type='ord',description='CHLORIDE, URINE';
INSERT INTO procedure_type SET NAME='CHLORIDE, WHOLE BLOOD' ,lab_id='1',procedure_code='CLWB',procedure_type='ord',description='CHLORIDE, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='CMNT1',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='CMNT2',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='CMNT3',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='CMNT4',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='CELL MORPHOLOGY' ,lab_id='1',procedure_code='CMORP',procedure_type='ord',description='CELL MORPHOLOGY';
INSERT INTO procedure_type SET NAME='CMV quant Interp (MD):' ,lab_id='1',procedure_code='CMQTPF',procedure_type='ord',description='CMV quant Interp (MD):';
INSERT INTO procedure_type SET NAME='CYTOMEGALOVIRUS IgG' ,lab_id='1',procedure_code='CMVAB',procedure_type='ord',description='CYTOMEGALOVIRUS IgG';
INSERT INTO procedure_type SET NAME='CMV ANTIVIRAL RESISTANCE' ,lab_id='1',procedure_code='CMVAVR',procedure_type='ord',description='CMV ANTIVIRAL RESISTANCE';
INSERT INTO procedure_type SET NAME='CMV CID' ,lab_id='1',procedure_code='CMVCID',procedure_type='ord',description='CMV CID';
INSERT INTO procedure_type SET NAME='CYTOMEGALOVIRUS AB' ,lab_id='1',procedure_code='CMVD',procedure_type='ord',description='CYTOMEGALOVIRUS AB';
INSERT INTO procedure_type SET NAME='CMV-ORGAN DONOR' ,lab_id='1',procedure_code='CMVI',procedure_type='ord',description='CMV-ORGAN DONOR';
INSERT INTO procedure_type SET NAME='CYTOMEGALOVIRUS IgM' ,lab_id='1',procedure_code='CMVIGM',procedure_type='ord',description='CYTOMEGALOVIRUS IgM';
INSERT INTO procedure_type SET NAME='CMV ANTIBODY, IgM' ,lab_id='1',procedure_code='CMVM',procedure_type='ord',description='CMV ANTIBODY, IgM';
INSERT INTO procedure_type SET NAME='CMV DNA MISC, PCR' ,lab_id='1',procedure_code='CMVMIS',procedure_type='ord',description='CMV DNA MISC, PCR';
INSERT INTO procedure_type SET NAME='CYTOMEG VIR DNA QUAL' ,lab_id='1',procedure_code='CMVQ',procedure_type='ord',description='CYTOMEG VIR DNA QUAL';
INSERT INTO procedure_type SET NAME='CMV DNA, QUANT. PCR' ,lab_id='1',procedure_code='CMVQT',procedure_type='ord',description='CMV DNA, QUANT. PCR';
INSERT INTO procedure_type SET NAME='DESOXYCORTISOL,NONSP' ,lab_id='1',procedure_code='CNSP',procedure_type='ord',description='DESOXYCORTISOL,NONSP';
INSERT INTO procedure_type SET NAME='CRYOPRECIPITATE READY' ,lab_id='1',procedure_code='CNUR',procedure_type='ord',description='CRYOPRECIPITATE READY';
INSERT INTO procedure_type SET NAME='NONSYNDROMIC DEAFNESS' ,lab_id='1',procedure_code='CNXN',procedure_type='ord',description='NONSYNDROMIC DEAFNESS';
INSERT INTO procedure_type SET NAME='CONNEXIN 30' ,lab_id='1',procedure_code='CNXN30',procedure_type='ord',description='CONNEXIN 30';
INSERT INTO procedure_type SET NAME='Conxn Interp (MD):' ,lab_id='1',procedure_code='CNXNPF',procedure_type='ord',description='Conxn Interp (MD):';
INSERT INTO procedure_type SET NAME='Colonies Counted:' ,lab_id='1',procedure_code='CO',procedure_type='ord',description='Colonies Counted:';
INSERT INTO procedure_type SET NAME='CARBON DIOXIDE, TOTAL' ,lab_id='1',procedure_code='CO2',procedure_type='ord',description='CARBON DIOXIDE, TOTAL';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='COAGI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='COAGMD',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='COBALT, BLOOD' ,lab_id='1',procedure_code='COBL',procedure_type='ord',description='COBALT, BLOOD';
INSERT INTO procedure_type SET NAME='COCAINE METABOLITE' ,lab_id='1',procedure_code='COCA',procedure_type='ord',description='COCAINE METABOLITE';
INSERT INTO procedure_type SET NAME='COCAINE METABOLITE' ,lab_id='1',procedure_code='COCAC',procedure_type='ord',description='COCAINE METABOLITE';
INSERT INTO procedure_type SET NAME='COCCIDIOIDES AB' ,lab_id='1',procedure_code='COCC',procedure_type='ord',description='COCCIDIOIDES AB';
INSERT INTO procedure_type SET NAME='COCCI AB BY COMP FIX' ,lab_id='1',procedure_code='COCF',procedure_type='ord',description='COCCI AB BY COMP FIX';
INSERT INTO procedure_type SET NAME='COCCI IMMITIS CSF AB' ,lab_id='1',procedure_code='COCSF',procedure_type='ord',description='COCCI IMMITIS CSF AB';
INSERT INTO procedure_type SET NAME='COCAINE SCRN. UR.' ,lab_id='1',procedure_code='COCU',procedure_type='ord',description='COCAINE SCRN. UR.';
INSERT INTO procedure_type SET NAME='CARBOXYHEMOGLOBIN' ,lab_id='1',procedure_code='COHB',procedure_type='ord',description='CARBOXYHEMOGLOBIN';
INSERT INTO procedure_type SET NAME='COLISTIN' ,lab_id='1',procedure_code='COL',procedure_type='ord',description='COLISTIN';
INSERT INTO procedure_type SET NAME='FRACTION 1 (COLOR)' ,lab_id='1',procedure_code='COL1',procedure_type='ord',description='FRACTION 1 (COLOR)';
INSERT INTO procedure_type SET NAME='FRACTION 2 (COLOR)' ,lab_id='1',procedure_code='COL2',procedure_type='ord',description='FRACTION 2 (COLOR)';
INSERT INTO procedure_type SET NAME='FRACTION 3 (COLOR)' ,lab_id='1',procedure_code='COL3',procedure_type='ord',description='FRACTION 3 (COLOR)';
INSERT INTO procedure_type SET NAME='FRACTION 4 (COLOR)' ,lab_id='1',procedure_code='COL4',procedure_type='ord',description='FRACTION 4 (COLOR)';
INSERT INTO procedure_type SET NAME='COLLAGEN/ADP' ,lab_id='1',procedure_code='COLADP',procedure_type='ord',description='COLLAGEN/ADP';
INSERT INTO procedure_type SET NAME='COLOR' ,lab_id='1',procedure_code='COLB',procedure_type='ord',description='COLOR';
INSERT INTO procedure_type SET NAME='Cold Autoagglut-Qual' ,lab_id='1',procedure_code='COLD',procedure_type='ord',description='Cold Autoagglut-Qual';
INSERT INTO procedure_type SET NAME='COLLAGEN/EPI' ,lab_id='1',procedure_code='COLEPI',procedure_type='ord',description='COLLAGEN/EPI';
INSERT INTO procedure_type SET NAME='COLOR' ,lab_id='1',procedure_code='COLOR',procedure_type='ord',description='COLOR';
INSERT INTO procedure_type SET NAME='COLLECTION TIME' ,lab_id='1',procedure_code='COLT',procedure_type='ord',description='COLLECTION TIME';
INSERT INTO procedure_type SET NAME='COMMENTS' ,lab_id='1',procedure_code='COM',procedure_type='ord',description='COMMENTS';
INSERT INTO procedure_type SET NAME='COMBINED TOTAL' ,lab_id='1',procedure_code='COMBT',procedure_type='ord',description='COMBINED TOTAL';
INSERT INTO procedure_type SET NAME='COMMENTS' ,lab_id='1',procedure_code='COME',procedure_type='ord',description='COMMENTS';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='COMM',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='COMZ',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='CON A STIMULATION' ,lab_id='1',procedure_code='CONA',procedure_type='ord',description='CON A STIMULATION';
INSERT INTO procedure_type SET NAME='COPPER' ,lab_id='1',procedure_code='COP',procedure_type='ord',description='COPPER';
INSERT INTO procedure_type SET NAME='COPPER, RANDOM UR' ,lab_id='1',procedure_code='COP1',procedure_type='ord',description='COPPER, RANDOM UR';
INSERT INTO procedure_type SET NAME='COPPER, TISSUE' ,lab_id='1',procedure_code='COPT',procedure_type='ord',description='COPPER, TISSUE';
INSERT INTO procedure_type SET NAME='COPPER,URINE' ,lab_id='1',procedure_code='COPU',procedure_type='ord',description='COPPER,URINE';
INSERT INTO procedure_type SET NAME='CORRECTED CLEARANCE' ,lab_id='1',procedure_code='CORC',procedure_type='ord',description='CORRECTED CLEARANCE';
INSERT INTO procedure_type SET NAME='HBc ANTIBODY TOTAL' ,lab_id='1',procedure_code='CORE',procedure_type='ord',description='HBc ANTIBODY TOTAL';
INSERT INTO procedure_type SET NAME='HBc IgM ANTIBODY' ,lab_id='1',procedure_code='CORM',procedure_type='ord',description='HBc IgM ANTIBODY';
INSERT INTO procedure_type SET NAME='CORTISOL' ,lab_id='1',procedure_code='CORT',procedure_type='ord',description='CORTISOL';
INSERT INTO procedure_type SET NAME='CONT + PAT PL, 0HR' ,lab_id='1',procedure_code='CP0',procedure_type='ord',description='CONT + PAT PL, 0HR';
INSERT INTO procedure_type SET NAME='CONT + PAT PL, 1HR' ,lab_id='1',procedure_code='CP1',procedure_type='ord',description='CONT + PAT PL, 1HR';
INSERT INTO procedure_type SET NAME='C PEPTIDE' ,lab_id='1',procedure_code='CPEP',procedure_type='ord',description='C PEPTIDE';
INSERT INTO procedure_type SET NAME='CEFOPERAZONE' ,lab_id='1',procedure_code='CPER',procedure_type='ord',description='CEFOPERAZONE';
INSERT INTO procedure_type SET NAME='CEFEPIME' ,lab_id='1',procedure_code='CPIM',procedure_type='ord',description='CEFEPIME';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGA' ,lab_id='1',procedure_code='CPNA',procedure_type='ord',description='C. PNEUMONIAE IGA';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGG' ,lab_id='1',procedure_code='CPNG',procedure_type='ord',description='C. PNEUMONIAE IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION (CPN):' ,lab_id='1',procedure_code='CPNII',procedure_type='ord',description='INTERPRETATION (CPN):';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGM' ,lab_id='1',procedure_code='CPNM',procedure_type='ord',description='C. PNEUMONIAE IGM';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGA' ,lab_id='1',procedure_code='CPNMA',procedure_type='ord',description='C. PNEUMONIAE IGA';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGG' ,lab_id='1',procedure_code='CPNMG',procedure_type='ord',description='C. PNEUMONIAE IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CPNMI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='C. PNEUMONIAE IGM' ,lab_id='1',procedure_code='CPNMM',procedure_type='ord',description='C. PNEUMONIAE IGM';
INSERT INTO procedure_type SET NAME='CALPROTECTIN, FECAL' ,lab_id='1',procedure_code='CPRN',procedure_type='ord',description='CALPROTECTIN, FECAL';
INSERT INTO procedure_type SET NAME='CENTROMETRIC PROBE' ,lab_id='1',procedure_code='CPROBE',procedure_type='ord',description='CENTROMETRIC PROBE';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGA' ,lab_id='1',procedure_code='CPSA',procedure_type='ord',description='C. PSITTACI IGA';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGG' ,lab_id='1',procedure_code='CPSG',procedure_type='ord',description='C. PSITTACI IGG';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGA' ,lab_id='1',procedure_code='CPSIA',procedure_type='ord',description='C. PSITTACI IGA';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGG' ,lab_id='1',procedure_code='CPSIG',procedure_type='ord',description='C. PSITTACI IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION (CPS):' ,lab_id='1',procedure_code='CPSII',procedure_type='ord',description='INTERPRETATION (CPS):';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGM' ,lab_id='1',procedure_code='CPSIM',procedure_type='ord',description='C. PSITTACI IGM';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CPSIN',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='C. PSITTACI IGM' ,lab_id='1',procedure_code='CPSM',procedure_type='ord',description='C. PSITTACI IGM';
INSERT INTO procedure_type SET NAME='NORMAL (NML), 0 HR' ,lab_id='1',procedure_code='CPT0',procedure_type='ord',description='NORMAL (NML), 0 HR';
INSERT INTO procedure_type SET NAME='NORMAL (NML), 1 HR' ,lab_id='1',procedure_code='CPT1',procedure_type='ord',description='NORMAL (NML), 1 HR';
INSERT INTO procedure_type SET NAME='NORMAL (NML), 0 HR' ,lab_id='1',procedure_code='CPTT0',procedure_type='ord',description='NORMAL (NML), 0 HR';
INSERT INTO procedure_type SET NAME='NORMAL (NML), 1 HR' ,lab_id='1',procedure_code='CPTT1',procedure_type='ord',description='NORMAL (NML), 1 HR';
INSERT INTO procedure_type SET NAME='CREATININE' ,lab_id='1',procedure_code='CR',procedure_type='ord',description='CREATININE';
INSERT INTO procedure_type SET NAME='CREATININE, BF' ,lab_id='1',procedure_code='CRB',procedure_type='ord',description='CREATININE, BF';
INSERT INTO procedure_type SET NAME='Call To Read Back By' ,lab_id='1',procedure_code='CRBB',procedure_type='ord',description='Call To Read Back By';
INSERT INTO procedure_type SET NAME='CRYSTALS' ,lab_id='1',procedure_code='CRBF',procedure_type='ord',description='CRYSTALS';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CRBFI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='CRBFMD',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='CREATININE,SERUM' ,lab_id='1',procedure_code='CREA',procedure_type='ord',description='CREATININE,SERUM';
INSERT INTO procedure_type SET NAME='CREATININE, iSTAT' ,lab_id='1',procedure_code='CREATI',procedure_type='ord',description='CREATININE, iSTAT';
INSERT INTO procedure_type SET NAME='CREATININE (SK)' ,lab_id='1',procedure_code='CREB',procedure_type='ord',description='CREATININE (SK)';
INSERT INTO procedure_type SET NAME='TOTAL FVIII ACTIVITY' ,lab_id='1',procedure_code='CRF8',procedure_type='ord',description='TOTAL FVIII ACTIVITY';
INSERT INTO procedure_type SET NAME='CRYOFIBRINOGEN, QUAL' ,lab_id='1',procedure_code='CRFI',procedure_type='ord',description='CRYOFIBRINOGEN, QUAL';
INSERT INTO procedure_type SET NAME='RISK:' ,lab_id='1',procedure_code='CRISK',procedure_type='ord',description='RISK:';
INSERT INTO procedure_type SET NAME='C REACTIVE PROTEIN' ,lab_id='1',procedure_code='CRP',procedure_type='ord',description='C REACTIVE PROTEIN';
INSERT INTO procedure_type SET NAME='HI SENS C-REACTIVE PROTEIN' ,lab_id='1',procedure_code='CRPH',procedure_type='ord',description='HI SENS C-REACTIVE PROTEIN';
INSERT INTO procedure_type SET NAME='CREAT. RANDOM URINE' ,lab_id='1',procedure_code='CRRU',procedure_type='ord',description='CREAT. RANDOM URINE';
INSERT INTO procedure_type SET NAME='CREAT, RANDOM URINE' ,lab_id='1',procedure_code='CRRUC',procedure_type='ord',description='CREAT, RANDOM URINE';
INSERT INTO procedure_type SET NAME='CREAT, RANDOM URINE' ,lab_id='1',procedure_code='CRRUM',procedure_type='ord',description='CREAT, RANDOM URINE';
INSERT INTO procedure_type SET NAME='CREAT, RANDOM URINE' ,lab_id='1',procedure_code='CRRUMG',procedure_type='ord',description='CREAT, RANDOM URINE';
INSERT INTO procedure_type SET NAME='CREAT, RANDOM URINE' ,lab_id='1',procedure_code='CRRUP',procedure_type='ord',description='CREAT, RANDOM URINE';
INSERT INTO procedure_type SET NAME='CREATINE' ,lab_id='1',procedure_code='CRTE',procedure_type='ord',description='CREATINE';
INSERT INTO procedure_type SET NAME='CREATININE, URINE' ,lab_id='1',procedure_code='CRUR',procedure_type='ord',description='CREATININE, URINE';
INSERT INTO procedure_type SET NAME='CRYOFIBRINOGEN' ,lab_id='1',procedure_code='CRYFI',procedure_type='ord',description='CRYOFIBRINOGEN';
INSERT INTO procedure_type SET NAME='CRYOGLOBULIN, QUALITATIVE' ,lab_id='1',procedure_code='CRYGL',procedure_type='ord',description='CRYOGLOBULIN, QUALITATIVE';
INSERT INTO procedure_type SET NAME='CRYOGLOBULIN, QUANT' ,lab_id='1',procedure_code='CRYQ',procedure_type='ord',description='CRYOGLOBULIN, QUANT';
INSERT INTO procedure_type SET NAME='CRYSTALS' ,lab_id='1',procedure_code='CRYU',procedure_type='ord',description='CRYSTALS';
INSERT INTO procedure_type SET NAME='CONC SMEAR; NO. CELLS' ,lab_id='1',procedure_code='CSB',procedure_type='ord',description='CONC SMEAR; NO. CELLS';
INSERT INTO procedure_type SET NAME='CONC SMEAR; NO. CELLS' ,lab_id='1',procedure_code='CSC',procedure_type='ord',description='CONC SMEAR; NO. CELLS';
INSERT INTO procedure_type SET NAME='IGG INDEX' ,lab_id='1',procedure_code='CSFI',procedure_type='ord',description='IGG INDEX';
INSERT INTO procedure_type SET NAME='CHECK SPECIMEN REQ\'D' ,lab_id='1',procedure_code='CSPC',procedure_type='ord',description='CHECK SPECIMEN REQ\'D';
INSERT INTO procedure_type SET NAME='11 DEOXYCORTISOL, SPECIFIC' ,lab_id='1',procedure_code='CSPE',procedure_type='ord',description='11 DEOXYCORTISOL, SPECIFIC';
INSERT INTO procedure_type SET NAME='STEADY STATE CONCENTRAT.' ,lab_id='1',procedure_code='CSS',procedure_type='ord',description='STEADY STATE CONCENTRAT.';
INSERT INTO procedure_type SET NAME='CANDIDA ANTIGEN STIM' ,lab_id='1',procedure_code='CST',procedure_type='ord',description='CANDIDA ANTIGEN STIM';
INSERT INTO procedure_type SET NAME='MINS POST GLUC DOSE' ,lab_id='1',procedure_code='CT',procedure_type='ord',description='MINS POST GLUC DOSE';
INSERT INTO procedure_type SET NAME='CEFOTETAN' ,lab_id='1',procedure_code='CTAN',procedure_type='ord',description='CEFOTETAN';
INSERT INTO procedure_type SET NAME='CEFTAROLINE' ,lab_id='1',procedure_code='CTAR',procedure_type='ord',description='CEFTAROLINE';
INSERT INTO procedure_type SET NAME='CEFOTAXIME' ,lab_id='1',procedure_code='CTAX',procedure_type='ord',description='CEFOTAXIME';
INSERT INTO procedure_type SET NAME='CEFTAZIDIME' ,lab_id='1',procedure_code='CTAZ',procedure_type='ord',description='CEFTAZIDIME';
INSERT INTO procedure_type SET NAME='CEFTAZ CLAV' ,lab_id='1',procedure_code='CTAZCL',procedure_type='ord',description='CEFTAZ CLAV';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS D-K IGA' ,lab_id='1',procedure_code='CTDKA',procedure_type='ord',description='C. TRACHOMATIS D-K IGA';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS D-K IGG' ,lab_id='1',procedure_code='CTDKG',procedure_type='ord',description='C. TRACHOMATIS D-K IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CTDKI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS D-K IGM' ,lab_id='1',procedure_code='CTDKM',procedure_type='ord',description='C. TRACHOMATIS D-K IGM';
INSERT INTO procedure_type SET NAME='CATEGORY' ,lab_id='1',procedure_code='CTG',procedure_type='ord',description='CATEGORY';
INSERT INTO procedure_type SET NAME='COLLECT TIME DESCRIP' ,lab_id='1',procedure_code='CTID',procedure_type='ord',description='COLLECT TIME DESCRIP';
INSERT INTO procedure_type SET NAME='CEFTIZOXIME' ,lab_id='1',procedure_code='CTIZ',procedure_type='ord',description='CEFTIZOXIME';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS L2 IGA' ,lab_id='1',procedure_code='CTL2A',procedure_type='ord',description='C. TRACHOMATIS L2 IGA';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS L2 IGG' ,lab_id='1',procedure_code='CTL2G',procedure_type='ord',description='C. TRACHOMATIS L2 IGG';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='CTL2I',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS L2 IGM' ,lab_id='1',procedure_code='CTL2M',procedure_type='ord',description='C. TRACHOMATIS L2 IGM';
INSERT INTO procedure_type SET NAME='CUMULATIVE TNC' ,lab_id='1',procedure_code='CTNCKG',procedure_type='ord',description='CUMULATIVE TNC';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS IGA' ,lab_id='1',procedure_code='CTRA',procedure_type='ord',description='C. TRACHOMATIS IGA';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS IGG' ,lab_id='1',procedure_code='CTRG',procedure_type='ord',description='C. TRACHOMATIS IGG';
INSERT INTO procedure_type SET NAME='CEFTRIAXONE' ,lab_id='1',procedure_code='CTRIAX',procedure_type='ord',description='CEFTRIAXONE';
INSERT INTO procedure_type SET NAME='INTERPRETATION (CTR):' ,lab_id='1',procedure_code='CTRII',procedure_type='ord',description='INTERPRETATION (CTR):';
INSERT INTO procedure_type SET NAME='C. TRACHOMATIS IGM' ,lab_id='1',procedure_code='CTRM',procedure_type='ord',description='C. TRACHOMATIS IGM';
INSERT INTO procedure_type SET NAME='CREATININE (NI)' ,lab_id='1',procedure_code='CTU',procedure_type='ord',description='CREATININE (NI)';
INSERT INTO procedure_type SET NAME='CREATININE, RANDOM UR' ,lab_id='1',procedure_code='CTUR',procedure_type='ord',description='CREATININE, RANDOM UR';
INSERT INTO procedure_type SET NAME='C-TELOPEPTIDE' ,lab_id='1',procedure_code='CTX',procedure_type='ord',description='C-TELOPEPTIDE';
INSERT INTO procedure_type SET NAME='COPPER,SERUM' ,lab_id='1',procedure_code='CU',procedure_type='ord',description='COPPER,SERUM';
INSERT INTO procedure_type SET NAME='COMMENTS ON URINALYSIS' ,lab_id='1',procedure_code='CUA',procedure_type='ord',description='COMMENTS ON URINALYSIS';
INSERT INTO procedure_type SET NAME='CREAT PER DAY, UR' ,lab_id='1',procedure_code='CUD',procedure_type='ord',description='CREAT PER DAY, UR';
INSERT INTO procedure_type SET NAME='CU INDEX' ,lab_id='1',procedure_code='CUI',procedure_type='ord',description='CU INDEX';
INSERT INTO procedure_type SET NAME='CYSTINE, 24 HR URINE' ,lab_id='1',procedure_code='CUQNT',procedure_type='ord',description='CYSTINE, 24 HR URINE';
INSERT INTO procedure_type SET NAME='CYSTINE, QNT. UR' ,lab_id='1',procedure_code='CUT',procedure_type='ord',description='CYSTINE, QNT. UR';
INSERT INTO procedure_type SET NAME='COPPER PER DAY, URINE' ,lab_id='1',procedure_code='CUU',procedure_type='ord',description='COPPER PER DAY, URINE';
INSERT INTO procedure_type SET NAME='COEF. OF VARIATION' ,lab_id='1',procedure_code='CV',procedure_type='ord',description='COEF. OF VARIATION';
INSERT INTO procedure_type SET NAME='CREAT. WHOLE BLOOD' ,lab_id='1',procedure_code='CWB',procedure_type='ord',description='CREAT. WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='ALK(ANAPLASTIC NHL)' ,lab_id='1',procedure_code='CYALK',procedure_type='ord',description='ALK(ANAPLASTIC NHL)';
INSERT INTO procedure_type SET NAME='CYANIDE' ,lab_id='1',procedure_code='CYAN',procedure_type='ord',description='CYANIDE';
INSERT INTO procedure_type SET NAME='CYCLOSPORINE' ,lab_id='1',procedure_code='CYCL',procedure_type='ord',description='CYCLOSPORINE';
INSERT INTO procedure_type SET NAME='ANTI-CCP ANTIBODY IgG' ,lab_id='1',procedure_code='CYCP',procedure_type='ord',description='ANTI-CCP ANTIBODY IgG';
INSERT INTO procedure_type SET NAME='CYCLOSERINE' ,lab_id='1',procedure_code='CYCS',procedure_type='ord',description='CYCLOSERINE';
INSERT INTO procedure_type SET NAME='Reviewed on:' ,lab_id='1',procedure_code='CYETRY',procedure_type='ord',description='Reviewed on:';
INSERT INTO procedure_type SET NAME='CYSTIC FIBROSIS (CF)' ,lab_id='1',procedure_code='CYF',procedure_type='ord',description='CYSTIC FIBROSIS (CF)';
INSERT INTO procedure_type SET NAME='FISH DIRECT ANEUPLOIDY' ,lab_id='1',procedure_code='CYFD',procedure_type='ord',description='FISH DIRECT ANEUPLOIDY';
INSERT INTO procedure_type SET NAME='EXTENDED INTERPHASE FISH' ,lab_id='1',procedure_code='CYFE',procedure_type='ord',description='EXTENDED INTERPHASE FISH';
INSERT INTO procedure_type SET NAME='INTERPHASE FISH TEST' ,lab_id='1',procedure_code='CYFI',procedure_type='ord',description='INTERPHASE FISH TEST';
INSERT INTO procedure_type SET NAME='METAPHASE FISH TEST' ,lab_id='1',procedure_code='CYFM',procedure_type='ord',description='METAPHASE FISH TEST';
INSERT INTO procedure_type SET NAME='FISH SUBTELOMERE' ,lab_id='1',procedure_code='CYFST',procedure_type='ord',description='FISH SUBTELOMERE';
INSERT INTO procedure_type SET NAME='CYSTINE HOMOCYSTINE, URINE' ,lab_id='1',procedure_code='CYHO',procedure_type='ord',description='CYSTINE HOMOCYSTINE, URINE';
INSERT INTO procedure_type SET NAME='CLL FISH PANEL' ,lab_id='1',procedure_code='CYLL',procedure_type='ord',description='CLL FISH PANEL';
INSERT INTO procedure_type SET NAME='CYP2D6 RESEARCH' ,lab_id='1',procedure_code='CYP2D6',procedure_type='ord',description='CYP2D6 RESEARCH';
INSERT INTO procedure_type SET NAME='SEND OUT STUDIES' ,lab_id='1',procedure_code='CYSO',procedure_type='ord',description='SEND OUT STUDIES';
INSERT INTO procedure_type SET NAME='SPECIALIZED STAINING' ,lab_id='1',procedure_code='CYSS',procedure_type='ord',description='SPECIALIZED STAINING';
INSERT INTO procedure_type SET NAME='CYSTINE' ,lab_id='1',procedure_code='CYSTIN',procedure_type='ord',description='CYSTINE';
INSERT INTO procedure_type SET NAME='CYSTINE, QUAL, URINE' ,lab_id='1',procedure_code='CYSU',procedure_type='ord',description='CYSTINE, QUAL, URINE';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANALYSIS, TISSUE' ,lab_id='1',procedure_code='CYT',procedure_type='ord',description='CHROMOSOME ANALYSIS, TISSUE';
INSERT INTO procedure_type SET NAME='TC, NON NEONATAL' ,lab_id='1',procedure_code='CYTCNN',procedure_type='ord',description='TC, NON NEONATAL';
INSERT INTO procedure_type SET NAME='TC, PRENATAL' ,lab_id='1',procedure_code='CYTCP',procedure_type='ord',description='TC, PRENATAL';
INSERT INTO procedure_type SET NAME='CYSTATHIONINE' ,lab_id='1',procedure_code='CYTHIO',procedure_type='ord',description='CYSTATHIONINE';
INSERT INTO procedure_type SET NAME='CRYOPRESERVATION' ,lab_id='1',procedure_code='CYTOFZ',procedure_type='ord',description='CRYOPRESERVATION';
INSERT INTO procedure_type SET NAME='Cytogenetics Interp (MD)' ,lab_id='1',procedure_code='CYTOPF',procedure_type='ord',description='Cytogenetics Interp (MD)';
INSERT INTO procedure_type SET NAME='TUMOR CYTOGENETICS' ,lab_id='1',procedure_code='CYTUM',procedure_type='ord',description='TUMOR CYTOGENETICS';
INSERT INTO procedure_type SET NAME='CEFAZOLIN' ,lab_id='1',procedure_code='CZOL',procedure_type='ord',description='CEFAZOLIN';
INSERT INTO procedure_type SET NAME='CARBAMAZEPINE' ,lab_id='1',procedure_code='CZP',procedure_type='ord',description='CARBAMAZEPINE';
INSERT INTO procedure_type SET NAME='VITAMIN 25OH,D3' ,lab_id='1',procedure_code='D3',procedure_type='ord',description='VITAMIN 25OH,D3';
INSERT INTO procedure_type SET NAME='DATA 1' ,lab_id='1',procedure_code='DA1',procedure_type='ord',description='DATA 1';
INSERT INTO procedure_type SET NAME='DATA 2' ,lab_id='1',procedure_code='DA2',procedure_type='ord',description='DATA 2';
INSERT INTO procedure_type SET NAME='DATA 3' ,lab_id='1',procedure_code='DA3',procedure_type='ord',description='DATA 3';
INSERT INTO procedure_type SET NAME='DATA 4' ,lab_id='1',procedure_code='DA4',procedure_type='ord',description='DATA 4';
INSERT INTO procedure_type SET NAME='DATA 5' ,lab_id='1',procedure_code='DA5',procedure_type='ord',description='DATA 5';
INSERT INTO procedure_type SET NAME='ALUMINUM,DIALYSATE' ,lab_id='1',procedure_code='DAL',procedure_type='ord',description='ALUMINUM,DIALYSATE';
INSERT INTO procedure_type SET NAME='DESETHYLAMIODARONE' ,lab_id='1',procedure_code='DAMI',procedure_type='ord',description='DESETHYLAMIODARONE';
INSERT INTO procedure_type SET NAME='DAPTOMYCIN' ,lab_id='1',procedure_code='DAPTO',procedure_type='ord',description='DAPTOMYCIN';
INSERT INTO procedure_type SET NAME='DATE CALLED' ,lab_id='1',procedure_code='DC',procedure_type='ord',description='DATE CALLED';
INSERT INTO procedure_type SET NAME='DEGENERATED CELLS' ,lab_id='1',procedure_code='DCB',procedure_type='ord',description='DEGENERATED CELLS';
INSERT INTO procedure_type SET NAME='DEGENERATED CELLS' ,lab_id='1',procedure_code='DCC',procedure_type='ord',description='DEGENERATED CELLS';
INSERT INTO procedure_type SET NAME='Direct Coombs C3' ,lab_id='1',procedure_code='DCC3',procedure_type='ord',description='Direct Coombs C3';
INSERT INTO procedure_type SET NAME='NOI INTERPRETATION' ,lab_id='1',procedure_code='DCFI',procedure_type='ord',description='NOI INTERPRETATION';
INSERT INTO procedure_type SET NAME='NOI INTERP.BY (MD):' ,lab_id='1',procedure_code='DCFPF',procedure_type='ord',description='NOI INTERP.BY (MD):';
INSERT INTO procedure_type SET NAME='NOI RESULTS' ,lab_id='1',procedure_code='DCFR',procedure_type='ord',description='NOI RESULTS';
INSERT INTO procedure_type SET NAME='Donor T. cruzi Ab' ,lab_id='1',procedure_code='DCHG',procedure_type='ord',description='Donor T. cruzi Ab';
INSERT INTO procedure_type SET NAME='DNR T.cruzi Ab Confirm' ,lab_id='1',procedure_code='DCHGC',procedure_type='ord',description='DNR T.cruzi Ab Confirm';
INSERT INTO procedure_type SET NAME='DONOR CMV' ,lab_id='1',procedure_code='DCMV',procedure_type='ord',description='DONOR CMV';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 0 HR' ,lab_id='1',procedure_code='DCPT0',procedure_type='ord',description='DEFICIENT+NML, 0 HR';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 1 HR' ,lab_id='1',procedure_code='DCPT1',procedure_type='ord',description='DEFICIENT+NML, 1 HR';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 2HR' ,lab_id='1',procedure_code='DCPT2',procedure_type='ord',description='DEFICIENT+NML, 2HR';
INSERT INTO procedure_type SET NAME='D-Dimer for VTE' ,lab_id='1',procedure_code='DDX',procedure_type='ord',description='D-Dimer for VTE';
INSERT INTO procedure_type SET NAME='DELETION 11Q (ATM)' ,lab_id='1',procedure_code='DEL11Q',procedure_type='ord',description='DELETION 11Q (ATM)';
INSERT INTO procedure_type SET NAME='DELETION 13q' ,lab_id='1',procedure_code='DEL13Q',procedure_type='ord',description='DELETION 13q';
INSERT INTO procedure_type SET NAME='DELETION 17P (TP53)' ,lab_id='1',procedure_code='DEL17P',procedure_type='ord',description='DELETION 17P (TP53)';
INSERT INTO procedure_type SET NAME='FISH FOR DELETION 20Q' ,lab_id='1',procedure_code='DEL20Q',procedure_type='ord',description='FISH FOR DELETION 20Q';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC1',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC2',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC3',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC4',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC5',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC6',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC7',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESCRIPTION' ,lab_id='1',procedure_code='DESC8',procedure_type='ord',description='DESCRIPTION';
INSERT INTO procedure_type SET NAME='DESIPRAMINE' ,lab_id='1',procedure_code='DESI',procedure_type='ord',description='DESIPRAMINE';
INSERT INTO procedure_type SET NAME='DENGUE FEVER, IGG' ,lab_id='1',procedure_code='DFG',procedure_type='ord',description='DENGUE FEVER, IGG';
INSERT INTO procedure_type SET NAME='DENGUE FEVER INTERP:' ,lab_id='1',procedure_code='DFI',procedure_type='ord',description='DENGUE FEVER INTERP:';
INSERT INTO procedure_type SET NAME='DENGUE FEVER, IGM' ,lab_id='1',procedure_code='DFM',procedure_type='ord',description='DENGUE FEVER, IGM';
INSERT INTO procedure_type SET NAME='DESALKYFLURAZEPAM' ,lab_id='1',procedure_code='DFPM',procedure_type='ord',description='DESALKYFLURAZEPAM';
INSERT INTO procedure_type SET NAME='DESMOGLEIN 1 ANTIBODY' ,lab_id='1',procedure_code='DGS1',procedure_type='ord',description='DESMOGLEIN 1 ANTIBODY';
INSERT INTO procedure_type SET NAME='DONOR HBsAG' ,lab_id='1',procedure_code='DHBAG',procedure_type='ord',description='DONOR HBsAG';
INSERT INTO procedure_type SET NAME='DONOR HBsAG CONFIRM' ,lab_id='1',procedure_code='DHBAGC',procedure_type='ord',description='DONOR HBsAG CONFIRM';
INSERT INTO procedure_type SET NAME='DONOR HBc ANTIBODY' ,lab_id='1',procedure_code='DHBC',procedure_type='ord',description='DONOR HBc ANTIBODY';
INSERT INTO procedure_type SET NAME='7-DEHYDROCHOLESTEROL' ,lab_id='1',procedure_code='DHC7',procedure_type='ord',description='7-DEHYDROCHOLESTEROL';
INSERT INTO procedure_type SET NAME='DO NOT USE' ,lab_id='1',procedure_code='DHCT',procedure_type='ord',description='DO NOT USE';
INSERT INTO procedure_type SET NAME='DONOR HCV ANTIBODY' ,lab_id='1',procedure_code='DHCV',procedure_type='ord',description='DONOR HCV ANTIBODY';
INSERT INTO procedure_type SET NAME='DONOR HCV AB CONFIRM' ,lab_id='1',procedure_code='DHCVC',procedure_type='ord',description='DONOR HCV AB CONFIRM';
INSERT INTO procedure_type SET NAME='VITAMIN D, 1,25 DI OH' ,lab_id='1',procedure_code='DHD',procedure_type='ord',description='VITAMIN D, 1,25 DI OH';
INSERT INTO procedure_type SET NAME='VIT D2,1,25(OH)2' ,lab_id='1',procedure_code='DHD2',procedure_type='ord',description='VIT D2,1,25(OH)2';
INSERT INTO procedure_type SET NAME='VIT D3,1,25(OH)2' ,lab_id='1',procedure_code='DHD3',procedure_type='ord',description='VIT D3,1,25(OH)2';
INSERT INTO procedure_type SET NAME='VIT D,1,25(OH)2,TOTAL' ,lab_id='1',procedure_code='DHDT',procedure_type='ord',description='VIT D,1,25(OH)2,TOTAL';
INSERT INTO procedure_type SET NAME='DHEA' ,lab_id='1',procedure_code='DHEA',procedure_type='ord',description='DHEA';
INSERT INTO procedure_type SET NAME='DHEA SULFATE' ,lab_id='1',procedure_code='DHES',procedure_type='ord',description='DHEA SULFATE';
INSERT INTO procedure_type SET NAME='DONOR HEMOGLOBIN' ,lab_id='1',procedure_code='DHGB',procedure_type='ord',description='DONOR HEMOGLOBIN';
INSERT INTO procedure_type SET NAME='DONOR HIV AG CONFIRM' ,lab_id='1',procedure_code='DHIAGC',procedure_type='ord',description='DONOR HIV AG CONFIRM';
INSERT INTO procedure_type SET NAME='DONOR HIV 1,2 AB' ,lab_id='1',procedure_code='DHIV',procedure_type='ord',description='DONOR HIV 1,2 AB';
INSERT INTO procedure_type SET NAME='DNR HIV 2 AB' ,lab_id='1',procedure_code='DHIV2',procedure_type='ord',description='DNR HIV 2 AB';
INSERT INTO procedure_type SET NAME='DNR HIV2 AB CONFIRM' ,lab_id='1',procedure_code='DHIV2C',procedure_type='ord',description='DNR HIV2 AB CONFIRM';
INSERT INTO procedure_type SET NAME='DONOR HIV 1 P24 AG' ,lab_id='1',procedure_code='DHIVAG',procedure_type='ord',description='DONOR HIV 1 P24 AG';
INSERT INTO procedure_type SET NAME='DNR HIV 1 AB CONFIRM' ,lab_id='1',procedure_code='DHIVC',procedure_type='ord',description='DNR HIV 1 AB CONFIRM';
INSERT INTO procedure_type SET NAME='DONOR CLASS I & CLASS II HLA ANTIBODIES' ,lab_id='1',procedure_code='DHLA',procedure_type='ord',description='DONOR CLASS I & CLASS II HLA ANTIBODIES';
INSERT INTO procedure_type SET NAME='DIHYDROTESTOSTERONE' ,lab_id='1',procedure_code='DHT',procedure_type='ord',description='DIHYDROTESTOSTERONE';
INSERT INTO procedure_type SET NAME='DONOR HTLV I,II AB' ,lab_id='1',procedure_code='DHTL',procedure_type='ord',description='DONOR HTLV I,II AB';
INSERT INTO procedure_type SET NAME='DONOR HTLV I AB' ,lab_id='1',procedure_code='DHTLV',procedure_type='ord',description='DONOR HTLV I AB';
INSERT INTO procedure_type SET NAME='DNR HTLV AB CONFIRM' ,lab_id='1',procedure_code='DHTLVC',procedure_type='ord',description='DNR HTLV AB CONFIRM';
INSERT INTO procedure_type SET NAME='DIASTOLIC BLD PRESS' ,lab_id='1',procedure_code='DIA',procedure_type='ord',description='DIASTOLIC BLD PRESS';
INSERT INTO procedure_type SET NAME='DIANON REFERRED TESTING' ,lab_id='1',procedure_code='DIAN',procedure_type='ord',description='DIANON REFERRED TESTING';
INSERT INTO procedure_type SET NAME='DIGOXIN' ,lab_id='1',procedure_code='DIG',procedure_type='ord',description='DIGOXIN';
INSERT INTO procedure_type SET NAME='DIGITOXIN' ,lab_id='1',procedure_code='DIGT',procedure_type='ord',description='DIGITOXIN';
INSERT INTO procedure_type SET NAME='DILUTION' ,lab_id='1',procedure_code='DIL',procedure_type='ord',description='DILUTION';
INSERT INTO procedure_type SET NAME='DIBUCAINE INHIBITION' ,lab_id='1',procedure_code='DINO',procedure_type='ord',description='DIBUCAINE INHIBITION';
INSERT INTO procedure_type SET NAME='DIPHTHERIA ANTITOXIN' ,lab_id='1',procedure_code='DIPHT',procedure_type='ord',description='DIPHTHERIA ANTITOXIN';
INSERT INTO procedure_type SET NAME='DISOPYRAMIDE' ,lab_id='1',procedure_code='DISO',procedure_type='ord',description='DISOPYRAMIDE';
INSERT INTO procedure_type SET NAME='RVVT INHB SCREEN' ,lab_id='1',procedure_code='DISS',procedure_type='ord',description='RVVT INHB SCREEN';
INSERT INTO procedure_type SET NAME='DIURETICS SCREEN' ,lab_id='1',procedure_code='DIUS',procedure_type='ord',description='DIURETICS SCREEN';
INSERT INTO procedure_type SET NAME='LACTASE' ,lab_id='1',procedure_code='DLAC',procedure_type='ord',description='LACTASE';
INSERT INTO procedure_type SET NAME='DONOR\'S NAME AND HOSP NUMBER' ,lab_id='1',procedure_code='DLDON',procedure_type='ord',description='DONOR\'S NAME AND HOSP NUMBER';
INSERT INTO procedure_type SET NAME='DNR Leishmania Ab' ,lab_id='1',procedure_code='DLEIS',procedure_type='ord',description='DNR Leishmania Ab';
INSERT INTO procedure_type SET NAME='DONOR LYMPHOCYTE SOURCE' ,lab_id='1',procedure_code='DLS',procedure_type='ord',description='DONOR LYMPHOCYTE SOURCE';
INSERT INTO procedure_type SET NAME='DONOR LYMPHOCYTE SOURCE' ,lab_id='1',procedure_code='DLSRC',procedure_type='ord',description='DONOR LYMPHOCYTE SOURCE';
INSERT INTO procedure_type SET NAME='MALTASE' ,lab_id='1',procedure_code='DMAL',procedure_type='ord',description='MALTASE';
INSERT INTO procedure_type SET NAME='DESMETHYLCLOMIPRAMINE' ,lab_id='1',procedure_code='DMCI',procedure_type='ord',description='DESMETHYLCLOMIPRAMINE';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 1' ,lab_id='1',procedure_code='DNA1',procedure_type='ord',description='CYTO DNA PROBE 1';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 2' ,lab_id='1',procedure_code='DNA2',procedure_type='ord',description='CYTO DNA PROBE 2';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 3' ,lab_id='1',procedure_code='DNA3',procedure_type='ord',description='CYTO DNA PROBE 3';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 4' ,lab_id='1',procedure_code='DNA4',procedure_type='ord',description='CYTO DNA PROBE 4';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 5' ,lab_id='1',procedure_code='DNA5',procedure_type='ord',description='CYTO DNA PROBE 5';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 6' ,lab_id='1',procedure_code='DNA6',procedure_type='ord',description='CYTO DNA PROBE 6';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 7' ,lab_id='1',procedure_code='DNA7',procedure_type='ord',description='CYTO DNA PROBE 7';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 8' ,lab_id='1',procedure_code='DNA8',procedure_type='ord',description='CYTO DNA PROBE 8';
INSERT INTO procedure_type SET NAME='CYTO DNA PROBE 9' ,lab_id='1',procedure_code='DNA9',procedure_type='ord',description='CYTO DNA PROBE 9';
INSERT INTO procedure_type SET NAME='DNA CYTOMETRY (FLOW)' ,lab_id='1',procedure_code='DNAF',procedure_type='ord',description='DNA CYTOMETRY (FLOW)';
INSERT INTO procedure_type SET NAME='DNA CYTOMETRY (IMAGE)' ,lab_id='1',procedure_code='DNAI',procedure_type='ord',description='DNA CYTOMETRY (IMAGE)';
INSERT INTO procedure_type SET NAME='NON HLA DNA TYPING' ,lab_id='1',procedure_code='DNAO',procedure_type='ord',description='NON HLA DNA TYPING';
INSERT INTO procedure_type SET NAME='DNR HCV/HIV NAT' ,lab_id='1',procedure_code='DNAT',procedure_type='ord',description='DNR HCV/HIV NAT';
INSERT INTO procedure_type SET NAME='DNR HIV-1/HCV/HBV NAT' ,lab_id='1',procedure_code='DNAT3',procedure_type='ord',description='DNR HIV-1/HCV/HBV NAT';
INSERT INTO procedure_type SET NAME='DNA EXTRACTION AND HOLD' ,lab_id='1',procedure_code='DNAX',procedure_type='ord',description='DNA EXTRACTION AND HOLD';
INSERT INTO procedure_type SET NAME='HI RES HLA DNA TYPE' ,lab_id='1',procedure_code='DNE1',procedure_type='ord',description='HI RES HLA DNA TYPE';
INSERT INTO procedure_type SET NAME='HLA DNA TYPING FOR CW' ,lab_id='1',procedure_code='DNEC',procedure_type='ord',description='HLA DNA TYPING FOR CW';
INSERT INTO procedure_type SET NAME='HLA DP TYPING BY DNA' ,lab_id='1',procedure_code='DNEP',procedure_type='ord',description='HLA DP TYPING BY DNA';
INSERT INTO procedure_type SET NAME='HLA DQ TYPING BY DNA' ,lab_id='1',procedure_code='DNEQ',procedure_type='ord',description='HLA DQ TYPING BY DNA';
INSERT INTO procedure_type SET NAME='HLA DNA GENERIC DR' ,lab_id='1',procedure_code='DNER',procedure_type='ord',description='HLA DNA GENERIC DR';
INSERT INTO procedure_type SET NAME='DNPH FOR KETOACIDS' ,lab_id='1',procedure_code='DNPH',procedure_type='ord',description='DNPH FOR KETOACIDS';
INSERT INTO procedure_type SET NAME='DONOR ANTIBODY SCREEN' ,lab_id='1',procedure_code='DNRABS',procedure_type='ord',description='DONOR ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='DONOR CMV' ,lab_id='1',procedure_code='DNRCMV',procedure_type='ord',description='DONOR CMV';
INSERT INTO procedure_type SET NAME='DONOR ID' ,lab_id='1',procedure_code='DNRID',procedure_type='ord',description='DONOR ID';
INSERT INTO procedure_type SET NAME='DEOXYCORTICOSTERONE' ,lab_id='1',procedure_code='DOC',procedure_type='ord',description='DEOXYCORTICOSTERONE';
INSERT INTO procedure_type SET NAME='Date of Draw:' ,lab_id='1',procedure_code='DOD',procedure_type='ord',description='Date of Draw:';
INSERT INTO procedure_type SET NAME='Donath-Landsteiner Ab' ,lab_id='1',procedure_code='DONL',procedure_type='ord',description='Donath-Landsteiner Ab';
INSERT INTO procedure_type SET NAME='DONOR BLOOD TYPE' ,lab_id='1',procedure_code='DONRBT',procedure_type='ord',description='DONOR BLOOD TYPE';
INSERT INTO procedure_type SET NAME='DOPAMINE,RANDOM UR' ,lab_id='1',procedure_code='DOPRU',procedure_type='ord',description='DOPAMINE,RANDOM UR';
INSERT INTO procedure_type SET NAME='DOPAMINE,URINE,TOT' ,lab_id='1',procedure_code='DOPU',procedure_type='ord',description='DOPAMINE,URINE,TOT';
INSERT INTO procedure_type SET NAME='DORIPENEM' ,lab_id='1',procedure_code='DORI',procedure_type='ord',description='DORIPENEM';
INSERT INTO procedure_type SET NAME='DOSAGE IN mg PER HR.' ,lab_id='1',procedure_code='DOSE',procedure_type='ord',description='DOSAGE IN mg PER HR.';
INSERT INTO procedure_type SET NAME='DOXEPIN' ,lab_id='1',procedure_code='DOXE',procedure_type='ord',description='DOXEPIN';
INSERT INTO procedure_type SET NAME='COMBINED TOTAL (DOXT)' ,lab_id='1',procedure_code='DOXT',procedure_type='ord',description='COMBINED TOTAL (DOXT)';
INSERT INTO procedure_type SET NAME='DOXYCYCLINE' ,lab_id='1',procedure_code='DOXY',procedure_type='ord',description='DOXYCYCLINE';
INSERT INTO procedure_type SET NAME='PALATINASE' ,lab_id='1',procedure_code='DPAL',procedure_type='ord',description='PALATINASE';
INSERT INTO procedure_type SET NAME='DOPAMINE' ,lab_id='1',procedure_code='DPMN',procedure_type='ord',description='DOPAMINE';
INSERT INTO procedure_type SET NAME='DNA PREPARATION' ,lab_id='1',procedure_code='DPRE',procedure_type='ord',description='DNA PREPARATION';
INSERT INTO procedure_type SET NAME='DONOR PREVIOUS CHAGAS RECORD' ,lab_id='1',procedure_code='DPREC',procedure_type='ord',description='DONOR PREVIOUS CHAGAS RECORD';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 0 HR' ,lab_id='1',procedure_code='DPTT0',procedure_type='ord',description='DEFICIENT+NML, 0 HR';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 1 HR' ,lab_id='1',procedure_code='DPTT1',procedure_type='ord',description='DEFICIENT+NML, 1 HR';
INSERT INTO procedure_type SET NAME='DEFICIENT+NML, 2HR' ,lab_id='1',procedure_code='DPTT2',procedure_type='ord',description='DEFICIENT+NML, 2HR';
INSERT INTO procedure_type SET NAME='See Report from Performing MD:' ,lab_id='1',procedure_code='DR',procedure_type='ord',description='See Report from Performing MD:';
INSERT INTO procedure_type SET NAME='RBC-Assoc\'d Drug Abs' ,lab_id='1',procedure_code='DRBC',procedure_type='ord',description='RBC-Assoc\'d Drug Abs';
INSERT INTO procedure_type SET NAME='DONOR ID' ,lab_id='1',procedure_code='DRID',procedure_type='ord',description='DONOR ID';
INSERT INTO procedure_type SET NAME='DONOR RPR' ,lab_id='1',procedure_code='DRPR',procedure_type='ord',description='DONOR RPR';
INSERT INTO procedure_type SET NAME='DONOR TPAB' ,lab_id='1',procedure_code='DRPRC',procedure_type='ord',description='DONOR TPAB';
INSERT INTO procedure_type SET NAME='DONOR RPR TITER' ,lab_id='1',procedure_code='DRPRT',procedure_type='ord',description='DONOR RPR TITER';
INSERT INTO procedure_type SET NAME='DESMOGLEIN 1 ANTIBODY' ,lab_id='1',procedure_code='DSG1',procedure_type='ord',description='DESMOGLEIN 1 ANTIBODY';
INSERT INTO procedure_type SET NAME='DESMOGLEIN 3 ANTIBODY' ,lab_id='1',procedure_code='DSG3',procedure_type='ord',description='DESMOGLEIN 3 ANTIBODY';
INSERT INTO procedure_type SET NAME='LACTASE' ,lab_id='1',procedure_code='DSLAC',procedure_type='ord',description='LACTASE';
INSERT INTO procedure_type SET NAME='MALTASE' ,lab_id='1',procedure_code='DSMAL',procedure_type='ord',description='MALTASE';
INSERT INTO procedure_type SET NAME='PALATINASE' ,lab_id='1',procedure_code='DSPAL',procedure_type='ord',description='PALATINASE';
INSERT INTO procedure_type SET NAME='SUCRASE' ,lab_id='1',procedure_code='DSSUC',procedure_type='ord',description='SUCRASE';
INSERT INTO procedure_type SET NAME='SUCRASE' ,lab_id='1',procedure_code='DSUC',procedure_type='ord',description='SUCRASE';
INSERT INTO procedure_type SET NAME='ZERO DRAW TIME:' ,lab_id='1',procedure_code='DT0',procedure_type='ord',description='ZERO DRAW TIME:';
INSERT INTO procedure_type SET NAME='DRAW TIME' ,lab_id='1',procedure_code='DT1',procedure_type='ord',description='DRAW TIME';
INSERT INTO procedure_type SET NAME='DRAW TIME' ,lab_id='1',procedure_code='DT2',procedure_type='ord',description='DRAW TIME';
INSERT INTO procedure_type SET NAME='DRAW TIME' ,lab_id='1',procedure_code='DT3',procedure_type='ord',description='DRAW TIME';
INSERT INTO procedure_type SET NAME='DRAW TIME' ,lab_id='1',procedure_code='DT4',procedure_type='ord',description='DRAW TIME';
INSERT INTO procedure_type SET NAME='DRAW TIME' ,lab_id='1',procedure_code='DT5',procedure_type='ord',description='DRAW TIME';
INSERT INTO procedure_type SET NAME='DONOR TREPONEMAL ANTIBODIES' ,lab_id='1',procedure_code='DTPAB',procedure_type='ord',description='DONOR TREPONEMAL ANTIBODIES';
INSERT INTO procedure_type SET NAME='DONOR TREPONEMAL ANTIBODIES G' ,lab_id='1',procedure_code='DTPG',procedure_type='ord',description='DONOR TREPONEMAL ANTIBODIES G';
INSERT INTO procedure_type SET NAME='WEAK D' ,lab_id='1',procedure_code='DUNP',procedure_type='ord',description='WEAK D';
INSERT INTO procedure_type SET NAME='DUPLICATION 1Q' ,lab_id='1',procedure_code='DUP1Q',procedure_type='ord',description='DUPLICATION 1Q';
INSERT INTO procedure_type SET NAME='DONOR UNIT VOLUME' ,lab_id='1',procedure_code='DVOL',procedure_type='ord',description='DONOR UNIT VOLUME';
INSERT INTO procedure_type SET NAME='PHOSPH CONFIRM RATIO' ,lab_id='1',procedure_code='DVVCR',procedure_type='ord',description='PHOSPH CONFIRM RATIO';
INSERT INTO procedure_type SET NAME='RVVT HI PHOSPHOLIPID' ,lab_id='1',procedure_code='DVVCS',procedure_type='ord',description='RVVT HI PHOSPHOLIPID';
INSERT INTO procedure_type SET NAME='RVVT' ,lab_id='1',procedure_code='DVVTS',procedure_type='ord',description='RVVT';
INSERT INTO procedure_type SET NAME='DI WATER RESISTIVITY' ,lab_id='1',procedure_code='DW',procedure_type='ord',description='DI WATER RESISTIVITY';
INSERT INTO procedure_type SET NAME='WBC COUNT IN BAG X10E6' ,lab_id='1',procedure_code='DWBC',procedure_type='ord',description='WBC COUNT IN BAG X10E6';
INSERT INTO procedure_type SET NAME='DNR WNV NAT' ,lab_id='1',procedure_code='DWNV',procedure_type='ord',description='DNR WNV NAT';
INSERT INTO procedure_type SET NAME='DIAGNOSIS' ,lab_id='1',procedure_code='DX',procedure_type='ord',description='DIAGNOSIS';
INSERT INTO procedure_type SET NAME='ESTRONE' ,lab_id='1',procedure_code='E1P',procedure_type='ord',description='ESTRONE';
INSERT INTO procedure_type SET NAME='ESTRADIOL' ,lab_id='1',procedure_code='E2',procedure_type='ord',description='ESTRADIOL';
INSERT INTO procedure_type SET NAME='ESTRADIOL' ,lab_id='1',procedure_code='E2P',procedure_type='ord',description='ESTRADIOL';
INSERT INTO procedure_type SET NAME='EBNA ANTIBODY' ,lab_id='1',procedure_code='EBNA',procedure_type='ord',description='EBNA ANTIBODY';
INSERT INTO procedure_type SET NAME='EBV quant Interp (MD):' ,lab_id='1',procedure_code='EBQTPF',procedure_type='ord',description='EBV quant Interp (MD):';
INSERT INTO procedure_type SET NAME='EBV IGA (ANTI VCA)' ,lab_id='1',procedure_code='EBVCA',procedure_type='ord',description='EBV IGA (ANTI VCA)';
INSERT INTO procedure_type SET NAME='EBV PCR QUANT. MISC' ,lab_id='1',procedure_code='EBVMIS',procedure_type='ord',description='EBV PCR QUANT. MISC';
INSERT INTO procedure_type SET NAME='EB VIRUS BY PCR, QUAL.' ,lab_id='1',procedure_code='EBVQAL',procedure_type='ord',description='EB VIRUS BY PCR, QUAL.';
INSERT INTO procedure_type SET NAME='EB VIRUS BY PCR, QUANT.' ,lab_id='1',procedure_code='EBVQNT',procedure_type='ord',description='EB VIRUS BY PCR, QUANT.';
INSERT INTO procedure_type SET NAME='EB VIRUS BY PCR QUANT' ,lab_id='1',procedure_code='EBVQT',procedure_type='ord',description='EB VIRUS BY PCR QUANT';
INSERT INTO procedure_type SET NAME='ECHINOCOCCUS IGG' ,lab_id='1',procedure_code='ECHINO',procedure_type='ord',description='ECHINOCOCCUS IGG';
INSERT INTO procedure_type SET NAME='METHADONE METAB, UR' ,lab_id='1',procedure_code='EDDP',procedure_type='ord',description='METHADONE METAB, UR';
INSERT INTO procedure_type SET NAME='ESSENTIAL FATTY ACIDS' ,lab_id='1',procedure_code='EFA',procedure_type='ord',description='ESSENTIAL FATTY ACIDS';
INSERT INTO procedure_type SET NAME='PLEASE NOTE:' ,lab_id='1',procedure_code='EGESN',procedure_type='ord',description='PLEASE NOTE:';
INSERT INTO procedure_type SET NAME='ETHYL GLUCURONIDE' ,lab_id='1',procedure_code='EGLU',procedure_type='ord',description='ETHYL GLUCURONIDE';
INSERT INTO procedure_type SET NAME='ETHYL GLUCURONIDE SCREEN' ,lab_id='1',procedure_code='EGLUS',procedure_type='ord',description='ETHYL GLUCURONIDE SCREEN';
INSERT INTO procedure_type SET NAME='ERHLICHIA INTERP:' ,lab_id='1',procedure_code='EHRI',procedure_type='ord',description='ERHLICHIA INTERP:';
INSERT INTO procedure_type SET NAME='E.CHAFFEENSIS AB IGG' ,lab_id='1',procedure_code='EHRLG',procedure_type='ord',description='E.CHAFFEENSIS AB IGG';
INSERT INTO procedure_type SET NAME='E.CHAFFEENSIS AB IGM' ,lab_id='1',procedure_code='EHRLM',procedure_type='ord',description='E.CHAFFEENSIS AB IGM';
INSERT INTO procedure_type SET NAME='EJ AUTOANTIBODIES' ,lab_id='1',procedure_code='EJAB',procedure_type='ord',description='EJ AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='OSMOTIC FRAGILITY BY EKTACYTOMETRY' ,lab_id='1',procedure_code='EKTA',procedure_type='ord',description='OSMOTIC FRAGILITY BY EKTACYTOMETRY';
INSERT INTO procedure_type SET NAME='RBC Frag Interp (MD):' ,lab_id='1',procedure_code='EKTAPF',procedure_type='ord',description='RBC Frag Interp (MD):';
INSERT INTO procedure_type SET NAME='PANCREATIC ELASTASE' ,lab_id='1',procedure_code='ELAS',procedure_type='ord',description='PANCREATIC ELASTASE';
INSERT INTO procedure_type SET NAME='CALCULATED TOTAL (E+N)' ,lab_id='1',procedure_code='ENRU',procedure_type='ord',description='CALCULATED TOTAL (E+N)';
INSERT INTO procedure_type SET NAME='EOS' ,lab_id='1',procedure_code='EOA',procedure_type='ord',description='EOS';
INSERT INTO procedure_type SET NAME='EOS' ,lab_id='1',procedure_code='EOB',procedure_type='ord',description='EOS';
INSERT INTO procedure_type SET NAME='EOS' ,lab_id='1',procedure_code='EOC',procedure_type='ord',description='EOS';
INSERT INTO procedure_type SET NAME='ABBREVIATED SEROL TEST' ,lab_id='1',procedure_code='EOK',procedure_type='ord',description='ABBREVIATED SEROL TEST';
INSERT INTO procedure_type SET NAME='ORGANISM OR AGENT' ,lab_id='1',procedure_code='EORG',procedure_type='ord',description='ORGANISM OR AGENT';
INSERT INTO procedure_type SET NAME='EPINEPHRINE' ,lab_id='1',procedure_code='EPI',procedure_type='ord',description='EPINEPHRINE';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='EPQTI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='PORPHYRINS EVAL, RBC' ,lab_id='1',procedure_code='EPQTS',procedure_type='ord',description='PORPHYRINS EVAL, RBC';
INSERT INTO procedure_type SET NAME='EPINEPHRINE,RANDOM' ,lab_id='1',procedure_code='EPRU',procedure_type='ord',description='EPINEPHRINE,RANDOM';
INSERT INTO procedure_type SET NAME='EPINEPHRINE, UR TOT' ,lab_id='1',procedure_code='EPTU',procedure_type='ord',description='EPINEPHRINE, UR TOT';
INSERT INTO procedure_type SET NAME='Analysis Procedure:' ,lab_id='1',procedure_code='ERBA',procedure_type='ord',description='Analysis Procedure:';
INSERT INTO procedure_type SET NAME='Case #:' ,lab_id='1',procedure_code='ERBC',procedure_type='ord',description='Case #:';
INSERT INTO procedure_type SET NAME='Interpretation:' ,lab_id='1',procedure_code='ERBI',procedure_type='ord',description='Interpretation:';
INSERT INTO procedure_type SET NAME='Reference:' ,lab_id='1',procedure_code='ERBN',procedure_type='ord',description='Reference:';
INSERT INTO procedure_type SET NAME='ERBB Her2 Interp (MD):' ,lab_id='1',procedure_code='ERBPF',procedure_type='ord',description='ERBB Her2 Interp (MD):';
INSERT INTO procedure_type SET NAME='Results:' ,lab_id='1',procedure_code='ERBR',procedure_type='ord',description='Results:';
INSERT INTO procedure_type SET NAME='ERYTHROPOIETIN' ,lab_id='1',procedure_code='ERP',procedure_type='ord',description='ERYTHROPOIETIN';
INSERT INTO procedure_type SET NAME='ERTAPENEM' ,lab_id='1',procedure_code='ERTA',procedure_type='ord',description='ERTAPENEM';
INSERT INTO procedure_type SET NAME='ERYTHROCYTE PORPHYRIN' ,lab_id='1',procedure_code='ERYP',procedure_type='ord',description='ERYTHROCYTE PORPHYRIN';
INSERT INTO procedure_type SET NAME='ERYTHROMYCIN' ,lab_id='1',procedure_code='ERYTH',procedure_type='ord',description='ERYTHROMYCIN';
INSERT INTO procedure_type SET NAME='Cytogeneticist:' ,lab_id='1',procedure_code='ES',procedure_type='ord',description='Cytogeneticist:';
INSERT INTO procedure_type SET NAME='SEDIMENTATION RATE' ,lab_id='1',procedure_code='ESR',procedure_type='ord',description='SEDIMENTATION RATE';
INSERT INTO procedure_type SET NAME='ESTERASE STAIN, NONSPEC (ANBE)' ,lab_id='1',procedure_code='ESTS',procedure_type='ord',description='ESTERASE STAIN, NONSPEC (ANBE)';
INSERT INTO procedure_type SET NAME='ETHYL SULFATE' ,lab_id='1',procedure_code='ESUL',procedure_type='ord',description='ETHYL SULFATE';
INSERT INTO procedure_type SET NAME='ETHAMBUTOL' ,lab_id='1',procedure_code='ETHA',procedure_type='ord',description='ETHAMBUTOL';
INSERT INTO procedure_type SET NAME='ETHIONAMIDE' ,lab_id='1',procedure_code='ETHIO',procedure_type='ord',description='ETHIONAMIDE';
INSERT INTO procedure_type SET NAME='ETHOSUXIMIDE' ,lab_id='1',procedure_code='ETHO',procedure_type='ord',description='ETHOSUXIMIDE';
INSERT INTO procedure_type SET NAME='ETHOTOIN' ,lab_id='1',procedure_code='ETHT',procedure_type='ord',description='ETHOTOIN';
INSERT INTO procedure_type SET NAME='ETHANOLAMINE' ,lab_id='1',procedure_code='ETOAM',procedure_type='ord',description='ETHANOLAMINE';
INSERT INTO procedure_type SET NAME='EUGLOBULIN CLOT LYSIS' ,lab_id='1',procedure_code='EUCL',procedure_type='ord',description='EUGLOBULIN CLOT LYSIS';
INSERT INTO procedure_type SET NAME='EVEROLIMUS' ,lab_id='1',procedure_code='EVRO',procedure_type='ord',description='EVEROLIMUS';
INSERT INTO procedure_type SET NAME='F10 ACT AVERAGE' ,lab_id='1',procedure_code='F10AV',procedure_type='ord',description='F10 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='F11 ACT AVERAGE' ,lab_id='1',procedure_code='F11AV',procedure_type='ord',description='F11 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='FACTOR XI INTERP.(MD)' ,lab_id='1',procedure_code='F11PF',procedure_type='ord',description='FACTOR XI INTERP.(MD)';
INSERT INTO procedure_type SET NAME='FACTOR XII ACTIVITY' ,lab_id='1',procedure_code='F12A',procedure_type='ord',description='FACTOR XII ACTIVITY';
INSERT INTO procedure_type SET NAME='F12 ACT AVERAGE' ,lab_id='1',procedure_code='F12AV',procedure_type='ord',description='F12 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='FACTOR XIII ACTIV SCRN' ,lab_id='1',procedure_code='F13',procedure_type='ord',description='FACTOR XIII ACTIV SCRN';
INSERT INTO procedure_type SET NAME='FACTOR XIII ACTIVITY' ,lab_id='1',procedure_code='F13A',procedure_type='ord',description='FACTOR XIII ACTIVITY';
INSERT INTO procedure_type SET NAME='F2 ACT AVERAGE' ,lab_id='1',procedure_code='F2AV',procedure_type='ord',description='F2 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='F5 ACT AVERAGE' ,lab_id='1',procedure_code='F5AV',procedure_type='ord',description='F5 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='F7 ACT AVERAGE' ,lab_id='1',procedure_code='F7AV',procedure_type='ord',description='F7 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='F8 ACT AVERAGE' ,lab_id='1',procedure_code='F8AV',procedure_type='ord',description='F8 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='F8 ACT. CHROMOGENIC' ,lab_id='1',procedure_code='F8CH',procedure_type='ord',description='F8 ACT. CHROMOGENIC';
INSERT INTO procedure_type SET NAME='F8, NO. OF GOOD DATA' ,lab_id='1',procedure_code='F8ND',procedure_type='ord',description='F8, NO. OF GOOD DATA';
INSERT INTO procedure_type SET NAME='FACTOR 8 INTERP (MD):' ,lab_id='1',procedure_code='F8PF',procedure_type='ord',description='FACTOR 8 INTERP (MD):';
INSERT INTO procedure_type SET NAME='VWF MULTIMERS' ,lab_id='1',procedure_code='F8RM',procedure_type='ord',description='VWF MULTIMERS';
INSERT INTO procedure_type SET NAME='VWF MULTIMERS INTERP.(MD)' ,lab_id='1',procedure_code='F8RMPF',procedure_type='ord',description='VWF MULTIMERS INTERP.(MD)';
INSERT INTO procedure_type SET NAME='F9 ACT AVERAGE' ,lab_id='1',procedure_code='F9AV',procedure_type='ord',description='F9 ACT AVERAGE';
INSERT INTO procedure_type SET NAME='FACTOR IX INTERP.(MD)' ,lab_id='1',procedure_code='F9PF',procedure_type='ord',description='FACTOR IX INTERP.(MD)';
INSERT INTO procedure_type SET NAME='ATA, FECAL' ,lab_id='1',procedure_code='FA1A',procedure_type='ord',description='ATA, FECAL';
INSERT INTO procedure_type SET NAME='FANCONI\'S ANEMIA' ,lab_id='1',procedure_code='FANC',procedure_type='ord',description='FANCONI\'S ANEMIA';
INSERT INTO procedure_type SET NAME='FAT, FECAL' ,lab_id='1',procedure_code='FAT',procedure_type='ord',description='FAT, FECAL';
INSERT INTO procedure_type SET NAME='GLUCOSE, FASTING' ,lab_id='1',procedure_code='FBS',procedure_type='ord',description='GLUCOSE, FASTING';
INSERT INTO procedure_type SET NAME='FETAL CELLS' ,lab_id='1',procedure_code='FC',procedure_type='ord',description='FETAL CELLS';
INSERT INTO procedure_type SET NAME='CORTISOL,FREE' ,lab_id='1',procedure_code='FCRT',procedure_type='ord',description='CORTISOL,FREE';
INSERT INTO procedure_type SET NAME='FREE CARNITINE, UR' ,lab_id='1',procedure_code='FCUR',procedure_type='ord',description='FREE CARNITINE, UR';
INSERT INTO procedure_type SET NAME='FIBRIN D DIMERS' ,lab_id='1',procedure_code='FDD',procedure_type='ord',description='FIBRIN D DIMERS';
INSERT INTO procedure_type SET NAME='D-Dimer for DIC' ,lab_id='1',procedure_code='FDDQ',procedure_type='ord',description='D-Dimer for DIC';
INSERT INTO procedure_type SET NAME='FETAL BLEED TEST' ,lab_id='1',procedure_code='FEBD',procedure_type='ord',description='FETAL BLEED TEST';
INSERT INTO procedure_type SET NAME='FERRIC CHLORIDE, UR' ,lab_id='1',procedure_code='FECU',procedure_type='ord',description='FERRIC CHLORIDE, UR';
INSERT INTO procedure_type SET NAME='HEPATIC IRON INDEX' ,lab_id='1',procedure_code='FEIX',procedure_type='ord',description='HEPATIC IRON INDEX';
INSERT INTO procedure_type SET NAME='FELBAMATE' ,lab_id='1',procedure_code='FELB',procedure_type='ord',description='FELBAMATE';
INSERT INTO procedure_type SET NAME='ZINC PROTOPORPHYRIN' ,lab_id='1',procedure_code='FEP',procedure_type='ord',description='ZINC PROTOPORPHYRIN';
INSERT INTO procedure_type SET NAME='FERRITIN' ,lab_id='1',procedure_code='FERR',procedure_type='ord',description='FERRITIN';
INSERT INTO procedure_type SET NAME='FETAL SAMPLE TESTED' ,lab_id='1',procedure_code='FEST',procedure_type='ord',description='FETAL SAMPLE TESTED';
INSERT INTO procedure_type SET NAME='IRON, LIVER' ,lab_id='1',procedure_code='FETI',procedure_type='ord',description='IRON, LIVER';
INSERT INTO procedure_type SET NAME='IRON, URINE' ,lab_id='1',procedure_code='FEU',procedure_type='ord',description='IRON, URINE';
INSERT INTO procedure_type SET NAME='IRON,URINE' ,lab_id='1',procedure_code='FEUR',procedure_type='ord',description='IRON,URINE';
INSERT INTO procedure_type SET NAME='FIBRINOGEN,FIBRIN DP' ,lab_id='1',procedure_code='FFDP',procedure_type='ord',description='FIBRINOGEN,FIBRIN DP';
INSERT INTO procedure_type SET NAME='FETAL FIBRONECTIN' ,lab_id='1',procedure_code='FFN',procedure_type='ord',description='FETAL FIBRONECTIN';
INSERT INTO procedure_type SET NAME='FHA IgA' ,lab_id='1',procedure_code='FHAA',procedure_type='ord',description='FHA IgA';
INSERT INTO procedure_type SET NAME='FETAL HEMOGLOBIN, AF' ,lab_id='1',procedure_code='FHAF',procedure_type='ord',description='FETAL HEMOGLOBIN, AF';
INSERT INTO procedure_type SET NAME='FHA IgG' ,lab_id='1',procedure_code='FHAG',procedure_type='ord',description='FHA IgG';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN FREE PLASMA' ,lab_id='1',procedure_code='FHGB',procedure_type='ord',description='HEMOGLOBIN FREE PLASMA';
INSERT INTO procedure_type SET NAME='OH PROLINE, FREE' ,lab_id='1',procedure_code='FHP',procedure_type='ord',description='OH PROLINE, FREE';
INSERT INTO procedure_type SET NAME='OH PROLINE, FREE RU' ,lab_id='1',procedure_code='FHPU',procedure_type='ord',description='OH PROLINE, FREE RU';
INSERT INTO procedure_type SET NAME='TOTAL FIBRINOGEN' ,lab_id='1',procedure_code='FIADT',procedure_type='ord',description='TOTAL FIBRINOGEN';
INSERT INTO procedure_type SET NAME='FIBRINOGEN' ,lab_id='1',procedure_code='FIB',procedure_type='ord',description='FIBRINOGEN';
INSERT INTO procedure_type SET NAME='FIBRINOGEN AG BY NEPH' ,lab_id='1',procedure_code='FIBAG',procedure_type='ord',description='FIBRINOGEN AG BY NEPH';
INSERT INTO procedure_type SET NAME='FIBRIN MONOMER' ,lab_id='1',procedure_code='FIBM',procedure_type='ord',description='FIBRIN MONOMER';
INSERT INTO procedure_type SET NAME='FRACTION INSPIRED O2' ,lab_id='1',procedure_code='FIO2',procedure_type='ord',description='FRACTION INSPIRED O2';
INSERT INTO procedure_type SET NAME='FACTOR INHIB TITER' ,lab_id='1',procedure_code='FIT',procedure_type='ord',description='FACTOR INHIB TITER';
INSERT INTO procedure_type SET NAME='FACTOR INHB TITR, PORC' ,lab_id='1',procedure_code='FITP',procedure_type='ord',description='FACTOR INHB TITR, PORC';
INSERT INTO procedure_type SET NAME='FACTOR INHIB TITER INTERP.(MD)' ,lab_id='1',procedure_code='FITPF',procedure_type='ord',description='FACTOR INHIB TITER INTERP.(MD)';
INSERT INTO procedure_type SET NAME='JMML PANEL, FAMILY F/U' ,lab_id='1',procedure_code='FJMML',procedure_type='ord',description='JMML PANEL, FAMILY F/U';
INSERT INTO procedure_type SET NAME='FRACTION 1 (FLUORO)' ,lab_id='1',procedure_code='FL1',procedure_type='ord',description='FRACTION 1 (FLUORO)';
INSERT INTO procedure_type SET NAME='FRACTION 2 (FLUORO)' ,lab_id='1',procedure_code='FL2',procedure_type='ord',description='FRACTION 2 (FLUORO)';
INSERT INTO procedure_type SET NAME='FRACTION 3 (FLOURO)' ,lab_id='1',procedure_code='FL3',procedure_type='ord',description='FRACTION 3 (FLOURO)';
INSERT INTO procedure_type SET NAME='FRACTION 4 (FLUORO)' ,lab_id='1',procedure_code='FL4',procedure_type='ord',description='FRACTION 4 (FLUORO)';
INSERT INTO procedure_type SET NAME='FRACTION 5 (FLUORO)' ,lab_id='1',procedure_code='FL5',procedure_type='ord',description='FRACTION 5 (FLUORO)';
INSERT INTO procedure_type SET NAME='ASPG FLAVUS AB' ,lab_id='1',procedure_code='FLAV',procedure_type='ord',description='ASPG FLAVUS AB';
INSERT INTO procedure_type SET NAME='CREAT POST FILTER' ,lab_id='1',procedure_code='FLCR',procedure_type='ord',description='CREAT POST FILTER';
INSERT INTO procedure_type SET NAME='FLECAINIDE' ,lab_id='1',procedure_code='FLEC',procedure_type='ord',description='FLECAINIDE';
INSERT INTO procedure_type SET NAME='OXYGEN FLOW RATE' ,lab_id='1',procedure_code='FLOW',procedure_type='ord',description='OXYGEN FLOW RATE';
INSERT INTO procedure_type SET NAME='FLT3 MUTATIONS' ,lab_id='1',procedure_code='FLT3',procedure_type='ord',description='FLT3 MUTATIONS';
INSERT INTO procedure_type SET NAME='FLUCONAZOLE' ,lab_id='1',procedure_code='FLU',procedure_type='ord',description='FLUCONAZOLE';
INSERT INTO procedure_type SET NAME='5 FLUOROCYTOSINE' ,lab_id='1',procedure_code='FLU5',procedure_type='ord',description='5 FLUOROCYTOSINE';
INSERT INTO procedure_type SET NAME='INFLUENZA A ANTIBODY' ,lab_id='1',procedure_code='FLUA',procedure_type='ord',description='INFLUENZA A ANTIBODY';
INSERT INTO procedure_type SET NAME='INFLUENZA B CF ANTIB' ,lab_id='1',procedure_code='FLUB',procedure_type='ord',description='INFLUENZA B CF ANTIB';
INSERT INTO procedure_type SET NAME='FISH METAPHASE' ,lab_id='1',procedure_code='FMET',procedure_type='ord',description='FISH METAPHASE';
INSERT INTO procedure_type SET NAME='PLASMA READY' ,lab_id='1',procedure_code='FNUR',procedure_type='ord',description='PLASMA READY';
INSERT INTO procedure_type SET NAME='OCCULT BLOOD,FIT' ,lab_id='1',procedure_code='FOBT',procedure_type='ord',description='OCCULT BLOOD,FIT';
INSERT INTO procedure_type SET NAME='FONDAPARINUX ASSAY' ,lab_id='1',procedure_code='FONDA',procedure_type='ord',description='FONDAPARINUX ASSAY';
INSERT INTO procedure_type SET NAME='ABO/RH w/RBC\'s' ,lab_id='1',procedure_code='FORT',procedure_type='ord',description='ABO/RH w/RBC\'s';
INSERT INTO procedure_type SET NAME='FOSFOMYCIN' ,lab_id='1',procedure_code='FOSFO',procedure_type='ord',description='FOSFOMYCIN';
INSERT INTO procedure_type SET NAME='FLURAZEPAM' ,lab_id='1',procedure_code='FPAM',procedure_type='ord',description='FLURAZEPAM';
INSERT INTO procedure_type SET NAME='PHENYTOIN, FREE' ,lab_id='1',procedure_code='FPHNY',procedure_type='ord',description='PHENYTOIN, FREE';
INSERT INTO procedure_type SET NAME='FREE PROTOPORPHYRIN' ,lab_id='1',procedure_code='FPP',procedure_type='ord',description='FREE PROTOPORPHYRIN';
INSERT INTO procedure_type SET NAME='PSA, FREE' ,lab_id='1',procedure_code='FRPSA',procedure_type='ord',description='PSA, FREE';
INSERT INTO procedure_type SET NAME='FRUCTOSAMINE' ,lab_id='1',procedure_code='FRUT',procedure_type='ord',description='FRUCTOSAMINE';
INSERT INTO procedure_type SET NAME='FRAGILE X BY DNA' ,lab_id='1',procedure_code='FRX',procedure_type='ord',description='FRAGILE X BY DNA';
INSERT INTO procedure_type SET NAME='FSH' ,lab_id='1',procedure_code='FSH',procedure_type='ord',description='FSH';
INSERT INTO procedure_type SET NAME='FREE T3, ADULT' ,lab_id='1',procedure_code='FT3',procedure_type='ord',description='FREE T3, ADULT';
INSERT INTO procedure_type SET NAME='FREE T4' ,lab_id='1',procedure_code='FT4',procedure_type='ord',description='FREE T4';
INSERT INTO procedure_type SET NAME='T4, FREE (DIALYSIS)' ,lab_id='1',procedure_code='FT4D',procedure_type='ord',description='T4, FREE (DIALYSIS)';
INSERT INTO procedure_type SET NAME='FTA-ABS, CSF' ,lab_id='1',procedure_code='FTAC',procedure_type='ord',description='FTA-ABS, CSF';
INSERT INTO procedure_type SET NAME='FRONTOTEMPORAL DEMENTIA' ,lab_id='1',procedure_code='FTD',procedure_type='ord',description='FRONTOTEMPORAL DEMENTIA';
INSERT INTO procedure_type SET NAME='Frnt Dmn Interp (MD):' ,lab_id='1',procedure_code='FTDPF',procedure_type='ord',description='Frnt Dmn Interp (MD):';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, FREE' ,lab_id='1',procedure_code='FTEST',procedure_type='ord',description='TESTOSTERONE, FREE';
INSERT INTO procedure_type SET NAME='FIRST TRIMESTER SCREEN' ,lab_id='1',procedure_code='FTS1',procedure_type='ord',description='FIRST TRIMESTER SCREEN';
INSERT INTO procedure_type SET NAME='CXAFP FORM NUMBER:' ,lab_id='1',procedure_code='FTS2',procedure_type='ord',description='CXAFP FORM NUMBER:';
INSERT INTO procedure_type SET NAME='CARNITINE, FREE UR' ,lab_id='1',procedure_code='FUCA',procedure_type='ord',description='CARNITINE, FREE UR';
INSERT INTO procedure_type SET NAME='FACTOR V (LEIDEN) MUTATION' ,lab_id='1',procedure_code='FVM',procedure_type='ord',description='FACTOR V (LEIDEN) MUTATION';
INSERT INTO procedure_type SET NAME='ThrmbisRsk Interp (MD):' ,lab_id='1',procedure_code='FVMPF',procedure_type='ord',description='ThrmbisRsk Interp (MD):';
INSERT INTO procedure_type SET NAME='FACTOR V LEIDEN MUT.' ,lab_id='1',procedure_code='FVR',procedure_type='ord',description='FACTOR V LEIDEN MUT.';
INSERT INTO procedure_type SET NAME='FACTOR X CHROMOGENIC' ,lab_id='1',procedure_code='FXCH',procedure_type='ord',description='FACTOR X CHROMOGENIC';
INSERT INTO procedure_type SET NAME='FYA Typing' ,lab_id='1',procedure_code='FYA',procedure_type='ord',description='FYA Typing';
INSERT INTO procedure_type SET NAME='FYB Typing' ,lab_id='1',procedure_code='FYB',procedure_type='ord',description='FYB Typing';
INSERT INTO procedure_type SET NAME='G6PD SCREEN WITH QUANT' ,lab_id='1',procedure_code='G6PD',procedure_type='ord',description='G6PD SCREEN WITH QUANT';
INSERT INTO procedure_type SET NAME='OD HGB STANDARD' ,lab_id='1',procedure_code='G6STD',procedure_type='ord',description='OD HGB STANDARD';
INSERT INTO procedure_type SET NAME='GASTRIC ACID, TITRAT' ,lab_id='1',procedure_code='GAAC',procedure_type='ord',description='GASTRIC ACID, TITRAT';
INSERT INTO procedure_type SET NAME='GASTRIC ACID, FREE' ,lab_id='1',procedure_code='GAAF',procedure_type='ord',description='GASTRIC ACID, FREE';
INSERT INTO procedure_type SET NAME='G AMINO BUTYRATE' ,lab_id='1',procedure_code='GABUT',procedure_type='ord',description='G AMINO BUTYRATE';
INSERT INTO procedure_type SET NAME='GAD-65 Ab' ,lab_id='1',procedure_code='GAD',procedure_type='ord',description='GAD-65 Ab';
INSERT INTO procedure_type SET NAME='GLYCOSAMINOGLYCANS' ,lab_id='1',procedure_code='GAGS',procedure_type='ord',description='GLYCOSAMINOGLYCANS';
INSERT INTO procedure_type SET NAME='GAL-1-P URIDYL' ,lab_id='1',procedure_code='GALC',procedure_type='ord',description='GAL-1-P URIDYL';
INSERT INTO procedure_type SET NAME='GALACTOSE 1 PHOS RBC' ,lab_id='1',procedure_code='GALM',procedure_type='ord',description='GALACTOSE 1 PHOS RBC';
INSERT INTO procedure_type SET NAME='GALACTOSEMIA SCREEN' ,lab_id='1',procedure_code='GALT',procedure_type='ord',description='GALACTOSEMIA SCREEN';
INSERT INTO procedure_type SET NAME='GASTRIC FLUID PH:' ,lab_id='1',procedure_code='GAPH',procedure_type='ord',description='GASTRIC FLUID PH:';
INSERT INTO procedure_type SET NAME='GASTRIN' ,lab_id='1',procedure_code='GAST',procedure_type='ord',description='GASTRIN';
INSERT INTO procedure_type SET NAME='GATIFLOXACIN' ,lab_id='1',procedure_code='GATI',procedure_type='ord',description='GATIFLOXACIN';
INSERT INTO procedure_type SET NAME='GD21' ,lab_id='1',procedure_code='GD21',procedure_type='ord',description='GD21';
INSERT INTO procedure_type SET NAME='GENTAMICIN' ,lab_id='1',procedure_code='GEN',procedure_type='ord',description='GENTAMICIN';
INSERT INTO procedure_type SET NAME='GENTAMICIN, PEAK' ,lab_id='1',procedure_code='GENPK',procedure_type='ord',description='GENTAMICIN, PEAK';
INSERT INTO procedure_type SET NAME='GENTAMICIN, RANDOM' ,lab_id='1',procedure_code='GENRN',procedure_type='ord',description='GENTAMICIN, RANDOM';
INSERT INTO procedure_type SET NAME='GENTAMICIN' ,lab_id='1',procedure_code='GENTA',procedure_type='ord',description='GENTAMICIN';
INSERT INTO procedure_type SET NAME='GENTAMICIN, TROUGH' ,lab_id='1',procedure_code='GENTH',procedure_type='ord',description='GENTAMICIN, TROUGH';
INSERT INTO procedure_type SET NAME='GESTATIONAL AGE' ,lab_id='1',procedure_code='GES',procedure_type='ord',description='GESTATIONAL AGE';
INSERT INTO procedure_type SET NAME='Weeks of Gestation:' ,lab_id='1',procedure_code='GEST',procedure_type='ord',description='Weeks of Gestation:';
INSERT INTO procedure_type SET NAME='eGFR if African Amer' ,lab_id='1',procedure_code='GFRAA',procedure_type='ord',description='eGFR if African Amer';
INSERT INTO procedure_type SET NAME='eGFR if Caucasian' ,lab_id='1',procedure_code='GFRC',procedure_type='ord',description='eGFR if Caucasian';
INSERT INTO procedure_type SET NAME='Pediatric eGFR' ,lab_id='1',procedure_code='GFRP',procedure_type='ord',description='Pediatric eGFR';
INSERT INTO procedure_type SET NAME='GAMMA GLOBULIN' ,lab_id='1',procedure_code='GG',procedure_type='ord',description='GAMMA GLOBULIN';
INSERT INTO procedure_type SET NAME='GGT' ,lab_id='1',procedure_code='GGT',procedure_type='ord',description='GGT';
INSERT INTO procedure_type SET NAME='GAMMA GLOBULIN, URINE' ,lab_id='1',procedure_code='GGU',procedure_type='ord',description='GAMMA GLOBULIN, URINE';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='GID',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='GLIADIN AB, IGA' ,lab_id='1',procedure_code='GLA',procedure_type='ord',description='GLIADIN AB, IGA';
INSERT INTO procedure_type SET NAME='GLUCOSE, BLOOD' ,lab_id='1',procedure_code='GLB',procedure_type='ord',description='GLUCOSE, BLOOD';
INSERT INTO procedure_type SET NAME='GLUCOSE, BODY FLUID' ,lab_id='1',procedure_code='GLBF',procedure_type='ord',description='GLUCOSE, BODY FLUID';
INSERT INTO procedure_type SET NAME='GLUCOSE, CSF' ,lab_id='1',procedure_code='GLC',procedure_type='ord',description='GLUCOSE, CSF';
INSERT INTO procedure_type SET NAME='GLUCOSE, FAST\'G PREG' ,lab_id='1',procedure_code='GLFP',procedure_type='ord',description='GLUCOSE, FAST\'G PREG';
INSERT INTO procedure_type SET NAME='GLIADIN AB, IGG' ,lab_id='1',procedure_code='GLG',procedure_type='ord',description='GLIADIN AB, IGG';
INSERT INTO procedure_type SET NAME='GLUCAGON' ,lab_id='1',procedure_code='GLGN',procedure_type='ord',description='GLUCAGON';
INSERT INTO procedure_type SET NAME='GLUCOSE, 1 HR P50' ,lab_id='1',procedure_code='GLT1',procedure_type='ord',description='GLUCOSE, 1 HR P50';
INSERT INTO procedure_type SET NAME='GLUCOSE' ,lab_id='1',procedure_code='GLU',procedure_type='ord',description='GLUCOSE';
INSERT INTO procedure_type SET NAME='GLUCOSE PER DAY, UR' ,lab_id='1',procedure_code='GLUD',procedure_type='ord',description='GLUCOSE PER DAY, UR';
INSERT INTO procedure_type SET NAME='GLUCOSE' ,lab_id='1',procedure_code='GLUI',procedure_type='ord',description='GLUCOSE';
INSERT INTO procedure_type SET NAME='GLUCOSE (POC)' ,lab_id='1',procedure_code='GLUPC',procedure_type='ord',description='GLUCOSE (POC)';
INSERT INTO procedure_type SET NAME='GLUCOSE, URINE QNT' ,lab_id='1',procedure_code='GLUR',procedure_type='ord',description='GLUCOSE, URINE QNT';
INSERT INTO procedure_type SET NAME='GLUTAMINE' ,lab_id='1',procedure_code='GLUTAM',procedure_type='ord',description='GLUTAMINE';
INSERT INTO procedure_type SET NAME='GLUTAMATE' ,lab_id='1',procedure_code='GLUTT',procedure_type='ord',description='GLUTAMATE';
INSERT INTO procedure_type SET NAME='GLUCOSE, WHOLE BLOOD' ,lab_id='1',procedure_code='GLUWB',procedure_type='ord',description='GLUCOSE, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='GLYCINE' ,lab_id='1',procedure_code='GLYC',procedure_type='ord',description='GLYCINE';
INSERT INTO procedure_type SET NAME='GM-1 AB, IGG' ,lab_id='1',procedure_code='GM1G',procedure_type='ord',description='GM-1 AB, IGG';
INSERT INTO procedure_type SET NAME='GM-1 AB, IGM' ,lab_id='1',procedure_code='GM1M',procedure_type='ord',description='GM-1 AB, IGM';
INSERT INTO procedure_type SET NAME='GALACTOMANNAN AG' ,lab_id='1',procedure_code='GMAN',procedure_type='ord',description='GALACTOMANNAN AG';
INSERT INTO procedure_type SET NAME='GLU ORAL TOL 2 HR' ,lab_id='1',procedure_code='GOT2',procedure_type='ord',description='GLU ORAL TOL 2 HR';
INSERT INTO procedure_type SET NAME='GLU ORAL TOL FASTING' ,lab_id='1',procedure_code='GOTF',procedure_type='ord',description='GLU ORAL TOL FASTING';
INSERT INTO procedure_type SET NAME='gp46' ,lab_id='1',procedure_code='GP46',procedure_type='ord',description='gp46';
INSERT INTO procedure_type SET NAME='GASTRIC PARIETAL CELL AB' ,lab_id='1',procedure_code='GPCA',procedure_type='ord',description='GASTRIC PARIETAL CELL AB';
INSERT INTO procedure_type SET NAME='GRAM STAIN' ,lab_id='1',procedure_code='GRAM',procedure_type='ord',description='GRAM STAIN';
INSERT INTO procedure_type SET NAME='GROWTH HORMONE' ,lab_id='1',procedure_code='GRH',procedure_type='ord',description='GROWTH HORMONE';
INSERT INTO procedure_type SET NAME='GRISEOFULVIN' ,lab_id='1',procedure_code='GRIS',procedure_type='ord',description='GRISEOFULVIN';
INSERT INTO procedure_type SET NAME='SPECIMEN NO.:' ,lab_id='1',procedure_code='GSPEC',procedure_type='ord',description='SPECIMEN NO.:';
INSERT INTO procedure_type SET NAME='GLUCOSE, 1 HOUR' ,lab_id='1',procedure_code='GT1',procedure_type='ord',description='GLUCOSE, 1 HOUR';
INSERT INTO procedure_type SET NAME='GLUCOSE, 10 MIN' ,lab_id='1',procedure_code='GT10',procedure_type='ord',description='GLUCOSE, 10 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 2 HOUR' ,lab_id='1',procedure_code='GT2',procedure_type='ord',description='GLUCOSE, 2 HOUR';
INSERT INTO procedure_type SET NAME='GLUCOSE, 20 MIN' ,lab_id='1',procedure_code='GT20',procedure_type='ord',description='GLUCOSE, 20 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 3 HOUR' ,lab_id='1',procedure_code='GT3',procedure_type='ord',description='GLUCOSE, 3 HOUR';
INSERT INTO procedure_type SET NAME='GLUCOSE, 30 MIN' ,lab_id='1',procedure_code='GT30',procedure_type='ord',description='GLUCOSE, 30 MIN';
INSERT INTO procedure_type SET NAME='30 MIN' ,lab_id='1',procedure_code='GT30X',procedure_type='ord',description='30 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 40 MIN' ,lab_id='1',procedure_code='GT40',procedure_type='ord',description='GLUCOSE, 40 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 50 MIN' ,lab_id='1',procedure_code='GT50',procedure_type='ord',description='GLUCOSE, 50 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 60 MIN' ,lab_id='1',procedure_code='GT60',procedure_type='ord',description='GLUCOSE, 60 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, 90 MIN' ,lab_id='1',procedure_code='GT90',procedure_type='ord',description='GLUCOSE, 90 MIN';
INSERT INTO procedure_type SET NAME='GLUCOSE, FASTING' ,lab_id='1',procedure_code='GTFC',procedure_type='ord',description='GLUCOSE, FASTING';
INSERT INTO procedure_type SET NAME='GASTRIC ANALYSIS TIME:' ,lab_id='1',procedure_code='GTIME',procedure_type='ord',description='GASTRIC ANALYSIS TIME:';
INSERT INTO procedure_type SET NAME='GALACTOSE' ,lab_id='1',procedure_code='GTOS',procedure_type='ord',description='GALACTOSE';
INSERT INTO procedure_type SET NAME='GLU TOL POST GLUCOLA' ,lab_id='1',procedure_code='GTPF',procedure_type='ord',description='GLU TOL POST GLUCOLA';
INSERT INTO procedure_type SET NAME='GLUCOSE' ,lab_id='1',procedure_code='GUA',procedure_type='ord',description='GLUCOSE';
INSERT INTO procedure_type SET NAME='HOLD ARRAY CGH' ,lab_id='1',procedure_code='HACGH',procedure_type='ord',description='HOLD ARRAY CGH';
INSERT INTO procedure_type SET NAME='HIV ANTIGEN CONFIRM' ,lab_id='1',procedure_code='HAGC1',procedure_type='ord',description='HIV ANTIGEN CONFIRM';
INSERT INTO procedure_type SET NAME='HIV ANTIGEN CONFIRM' ,lab_id='1',procedure_code='HAGC2',procedure_type='ord',description='HIV ANTIGEN CONFIRM';
INSERT INTO procedure_type SET NAME='HALOPERIDOL' ,lab_id='1',procedure_code='HALO',procedure_type='ord',description='HALOPERIDOL';
INSERT INTO procedure_type SET NAME='HANTAVIRUS IGG' ,lab_id='1',procedure_code='HANTG',procedure_type='ord',description='HANTAVIRUS IGG';
INSERT INTO procedure_type SET NAME='HANTAVIRUS IGM' ,lab_id='1',procedure_code='HANTM',procedure_type='ord',description='HANTAVIRUS IGM';
INSERT INTO procedure_type SET NAME='HAPTOGLOBIN' ,lab_id='1',procedure_code='HAPT',procedure_type='ord',description='HAPTOGLOBIN';
INSERT INTO procedure_type SET NAME='HAV ANTIBODY, TOTAL' ,lab_id='1',procedure_code='HAVG',procedure_type='ord',description='HAV ANTIBODY, TOTAL';
INSERT INTO procedure_type SET NAME='HAV IGM ANTIBODY' ,lab_id='1',procedure_code='HAVM',procedure_type='ord',description='HAV IGM ANTIBODY';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN A1C' ,lab_id='1',procedure_code='HBA1',procedure_type='ord',description='HEMOGLOBIN A1C';
INSERT INTO procedure_type SET NAME='HBs ANTIBODY' ,lab_id='1',procedure_code='HBAB',procedure_type='ord',description='HBs ANTIBODY';
INSERT INTO procedure_type SET NAME='HBsAg' ,lab_id='1',procedure_code='HBAG',procedure_type='ord',description='HBsAg';
INSERT INTO procedure_type SET NAME='HBsAG CONFIRMATION' ,lab_id='1',procedure_code='HBAGC',procedure_type='ord',description='HBsAG CONFIRMATION';
INSERT INTO procedure_type SET NAME='HBsAG (BSI)' ,lab_id='1',procedure_code='HBAGSO',procedure_type='ord',description='HBsAG (BSI)';
INSERT INTO procedure_type SET NAME='HGB CONSTANT SPRING' ,lab_id='1',procedure_code='HBCS',procedure_type='ord',description='HGB CONSTANT SPRING';
INSERT INTO procedure_type SET NAME='HBc AB, TOTAL (BSI)' ,lab_id='1',procedure_code='HBCSO',procedure_type='ord',description='HBc AB, TOTAL (BSI)';
INSERT INTO procedure_type SET NAME='HBeAG' ,lab_id='1',procedure_code='HBE',procedure_type='ord',description='HBeAG';
INSERT INTO procedure_type SET NAME='HEMOGLOBINOPATHY EVAL' ,lab_id='1',procedure_code='HBEV',procedure_type='ord',description='HEMOGLOBINOPATHY EVAL';
INSERT INTO procedure_type SET NAME='Smear held for review' ,lab_id='1',procedure_code='HBF',procedure_type='ord',description='Smear held for review';
INSERT INTO procedure_type SET NAME='ALTERNATE HBV NAT' ,lab_id='1',procedure_code='HBNAT',procedure_type='ord',description='ALTERNATE HBV NAT';
INSERT INTO procedure_type SET NAME='HBV NAT CONFIRM' ,lab_id='1',procedure_code='HBNATC',procedure_type='ord',description='HBV NAT CONFIRM';
INSERT INTO procedure_type SET NAME='HBS ANTIBODY, QUANT' ,lab_id='1',procedure_code='HBSQT',procedure_type='ord',description='HBS ANTIBODY, QUANT';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN' ,lab_id='1',procedure_code='HBUA',procedure_type='ord',description='HEMOGLOBIN';
INSERT INTO procedure_type SET NAME='HBV BCP MUTATIONS' ,lab_id='1',procedure_code='HBVBCP',procedure_type='ord',description='HBV BCP MUTATIONS';
INSERT INTO procedure_type SET NAME='HBV DNA BY bDNA' ,lab_id='1',procedure_code='HBVD',procedure_type='ord',description='HBV DNA BY bDNA';
INSERT INTO procedure_type SET NAME='HBV GENOTYPE' ,lab_id='1',procedure_code='HBVGEN',procedure_type='ord',description='HBV GENOTYPE';
INSERT INTO procedure_type SET NAME='HBV NUCLEIC ACID TEST' ,lab_id='1',procedure_code='HBVNAC',procedure_type='ord',description='HBV NUCLEIC ACID TEST';
INSERT INTO procedure_type SET NAME='HBV POLYMERASE MUTATIONS' ,lab_id='1',procedure_code='HBVPM',procedure_type='ord',description='HBV POLYMERASE MUTATIONS';
INSERT INTO procedure_type SET NAME='HBV PRECORE MUTATIONS' ,lab_id='1',procedure_code='HBVPRM',procedure_type='ord',description='HBV PRECORE MUTATIONS';
INSERT INTO procedure_type SET NAME='HEPATITIS B VIRAL DNA, QUANT' ,lab_id='1',procedure_code='HBVQ',procedure_type='ord',description='HEPATITIS B VIRAL DNA, QUANT';
INSERT INTO procedure_type SET NAME='HEPATITIS B DNA QUAL' ,lab_id='1',procedure_code='HBVQL',procedure_type='ord',description='HEPATITIS B DNA QUAL';
INSERT INTO procedure_type SET NAME='HEP. B VIRAL DNA QUANT' ,lab_id='1',procedure_code='HBVQQ',procedure_type='ord',description='HEP. B VIRAL DNA QUANT';
INSERT INTO procedure_type SET NAME='HBVRT INTERP.BY (MD):' ,lab_id='1',procedure_code='HBVRPF',procedure_type='ord',description='HBVRT INTERP.BY (MD):';
INSERT INTO procedure_type SET NAME='HBV REAL TIME' ,lab_id='1',procedure_code='HBVRT1',procedure_type='ord',description='HBV REAL TIME';
INSERT INTO procedure_type SET NAME='HBV Log IU/mL' ,lab_id='1',procedure_code='HBVRT2',procedure_type='ord',description='HBV Log IU/mL';
INSERT INTO procedure_type SET NAME='HBVT' ,lab_id='1',procedure_code='HBVT',procedure_type='ord',description='HBVT';
INSERT INTO procedure_type SET NAME='HEM A CARRIAGE INTERP' ,lab_id='1',procedure_code='HCARI',procedure_type='ord',description='HEM A CARRIAGE INTERP';
INSERT INTO procedure_type SET NAME='HCG PREGNANCY, SERUM' ,lab_id='1',procedure_code='HCGP',procedure_type='ord',description='HCG PREGNANCY, SERUM';
INSERT INTO procedure_type SET NAME='HCG PREGNANCY, SERUM, >=18 YRS' ,lab_id='1',procedure_code='HCGPA',procedure_type='ord',description='HCG PREGNANCY, SERUM, >=18 YRS';
INSERT INTO procedure_type SET NAME='HCG PREGNANCY, SERUM, <18 YRS' ,lab_id='1',procedure_code='HCGPP',procedure_type='ord',description='HCG PREGNANCY, SERUM, <18 YRS';
INSERT INTO procedure_type SET NAME='HCG FOR TUMOR' ,lab_id='1',procedure_code='HCGT',procedure_type='ord',description='HCG FOR TUMOR';
INSERT INTO procedure_type SET NAME='HCG FOR PREGNANCY,URINE' ,lab_id='1',procedure_code='HCGU',procedure_type='ord',description='HCG FOR PREGNANCY,URINE';
INSERT INTO procedure_type SET NAME='HCG PREGNANCY, URINE, >=18 YRS' ,lab_id='1',procedure_code='HCGUA',procedure_type='ord',description='HCG PREGNANCY, URINE, >=18 YRS';
INSERT INTO procedure_type SET NAME='HCG PREGNANCY, URINE, <18YRS' ,lab_id='1',procedure_code='HCGUPE',procedure_type='ord',description='HCG PREGNANCY, URINE, <18YRS';
INSERT INTO procedure_type SET NAME='HCV NUCLEIC ACID TEST' ,lab_id='1',procedure_code='HCNAC',procedure_type='ord',description='HCV NUCLEIC ACID TEST';
INSERT INTO procedure_type SET NAME='HCV NAT CONFIRM' ,lab_id='1',procedure_code='HCNATC',procedure_type='ord',description='HCV NAT CONFIRM';
INSERT INTO procedure_type SET NAME='BICARBONATE' ,lab_id='1',procedure_code='HCO3',procedure_type='ord',description='BICARBONATE';
INSERT INTO procedure_type SET NAME='Smear held for review' ,lab_id='1',procedure_code='HCSF',procedure_type='ord',description='Smear held for review';
INSERT INTO procedure_type SET NAME='HEMATOCRIT' ,lab_id='1',procedure_code='HCT',procedure_type='ord',description='HEMATOCRIT';
INSERT INTO procedure_type SET NAME='HEMATOCRIT, MANUAL' ,lab_id='1',procedure_code='HCTM',procedure_type='ord',description='HEMATOCRIT, MANUAL';
INSERT INTO procedure_type SET NAME='HEMATOCRIT (POC)' ,lab_id='1',procedure_code='HCTPOC',procedure_type='ord',description='HEMATOCRIT (POC)';
INSERT INTO procedure_type SET NAME='HIV CULTURE' ,lab_id='1',procedure_code='HCUL1',procedure_type='ord',description='HIV CULTURE';
INSERT INTO procedure_type SET NAME='HCV ANTIBODY' ,lab_id='1',procedure_code='HCV',procedure_type='ord',description='HCV ANTIBODY';
INSERT INTO procedure_type SET NAME='HCV RNA, PCR QUANT.' ,lab_id='1',procedure_code='HCV1',procedure_type='ord',description='HCV RNA, PCR QUANT.';
INSERT INTO procedure_type SET NAME='HCV RNA, PCR QUANT.' ,lab_id='1',procedure_code='HCV2',procedure_type='ord',description='HCV RNA, PCR QUANT.';
INSERT INTO procedure_type SET NAME='HCV RNA BY BDNA' ,lab_id='1',procedure_code='HCVB',procedure_type='ord',description='HCV RNA BY BDNA';
INSERT INTO procedure_type SET NAME='HCV Log IU/mL' ,lab_id='1',procedure_code='HCVB3',procedure_type='ord',description='HCV Log IU/mL';
INSERT INTO procedure_type SET NAME='HCV AB CONFIRMATION' ,lab_id='1',procedure_code='HCVC',procedure_type='ord',description='HCV AB CONFIRMATION';
INSERT INTO procedure_type SET NAME='FIBROSIS SCORE(01):' ,lab_id='1',procedure_code='HCVFSC',procedure_type='ord',description='FIBROSIS SCORE(01):';
INSERT INTO procedure_type SET NAME='FIBROSIS STAGE (01):' ,lab_id='1',procedure_code='HCVFST',procedure_type='ord',description='FIBROSIS STAGE (01):';
INSERT INTO procedure_type SET NAME='HEPATITIS C GENOTYPING' ,lab_id='1',procedure_code='HCVG',procedure_type='ord',description='HEPATITIS C GENOTYPING';
INSERT INTO procedure_type SET NAME='HCV Gene Interp (MD):' ,lab_id='1',procedure_code='HCVGPF',procedure_type='ord',description='HCV Gene Interp (MD):';
INSERT INTO procedure_type SET NAME='HCV RNA PCR, QUAL.' ,lab_id='1',procedure_code='HCVPCR',procedure_type='ord',description='HCV RNA PCR, QUAL.';
INSERT INTO procedure_type SET NAME='HCVRT INTERP BY (MD):' ,lab_id='1',procedure_code='HCVRPF',procedure_type='ord',description='HCVRT INTERP BY (MD):';
INSERT INTO procedure_type SET NAME='HCV REAL TIME' ,lab_id='1',procedure_code='HCVRT1',procedure_type='ord',description='HCV REAL TIME';
INSERT INTO procedure_type SET NAME='HCV Log IU/mL' ,lab_id='1',procedure_code='HCVRT2',procedure_type='ord',description='HCV Log IU/mL';
INSERT INTO procedure_type SET NAME='HCV ANTIBODY (BSI)' ,lab_id='1',procedure_code='HCVSO',procedure_type='ord',description='HCV ANTIBODY (BSI)';
INSERT INTO procedure_type SET NAME='HCV RNA TMA' ,lab_id='1',procedure_code='HCVTMA',procedure_type='ord',description='HCV RNA TMA';
INSERT INTO procedure_type SET NAME='HEP C GENOTYPE EXTRACTION FEE' ,lab_id='1',procedure_code='HCVX',procedure_type='ord',description='HEP C GENOTYPE EXTRACTION FEE';
INSERT INTO procedure_type SET NAME='HOMOCYSTEINE, TOTAL' ,lab_id='1',procedure_code='HCYS',procedure_type='ord',description='HOMOCYSTEINE, TOTAL';
INSERT INTO procedure_type SET NAME='HOMOCYST, QUAL, UR' ,lab_id='1',procedure_code='HCYU',procedure_type='ord',description='HOMOCYST, QUAL, UR';
INSERT INTO procedure_type SET NAME='HYDROCODONE' ,lab_id='1',procedure_code='HDCN',procedure_type='ord',description='HYDROCODONE';
INSERT INTO procedure_type SET NAME='HDL CHOLESTEROL' ,lab_id='1',procedure_code='HDL',procedure_type='ord',description='HDL CHOLESTEROL';
INSERT INTO procedure_type SET NAME='HDL2 (Large, Buoyant)' ,lab_id='1',procedure_code='HDL2',procedure_type='ord',description='HDL2 (Large, Buoyant)';
INSERT INTO procedure_type SET NAME='HDL3 (Small, Dense)' ,lab_id='1',procedure_code='HDL3',procedure_type='ord',description='HDL3 (Small, Dense)';
INSERT INTO procedure_type SET NAME='HYDROMORPHONE' ,lab_id='1',procedure_code='HDMN',procedure_type='ord',description='HYDROMORPHONE';
INSERT INTO procedure_type SET NAME='HIV DNA BY PCR' ,lab_id='1',procedure_code='HDNA1',procedure_type='ord',description='HIV DNA BY PCR';
INSERT INTO procedure_type SET NAME='HDV AB (DELTA AB)' ,lab_id='1',procedure_code='HDV',procedure_type='ord',description='HDV AB (DELTA AB)';
INSERT INTO procedure_type SET NAME='H. PYLORI IGG AB' ,lab_id='1',procedure_code='HELI',procedure_type='ord',description='H. PYLORI IGG AB';
INSERT INTO procedure_type SET NAME='NCPL HEMOCUE HB 1' ,lab_id='1',procedure_code='HEMAC1',procedure_type='ord',description='NCPL HEMOCUE HB 1';
INSERT INTO procedure_type SET NAME='NCPL HEMOCUE HB 2' ,lab_id='1',procedure_code='HEMAC2',procedure_type='ord',description='NCPL HEMOCUE HB 2';
INSERT INTO procedure_type SET NAME='HEP ANGLE' ,lab_id='1',procedure_code='HEPANG',procedure_type='ord',description='HEP ANGLE';
INSERT INTO procedure_type SET NAME='HEP CLOT KINETICS' ,lab_id='1',procedure_code='HEPK',procedure_type='ord',description='HEP CLOT KINETICS';
INSERT INTO procedure_type SET NAME='HEP PERCENT LYSIS' ,lab_id='1',procedure_code='HEPLYS',procedure_type='ord',description='HEP PERCENT LYSIS';
INSERT INTO procedure_type SET NAME='HEP MAX AMPLITUDE' ,lab_id='1',procedure_code='HEPMA',procedure_type='ord',description='HEP MAX AMPLITUDE';
INSERT INTO procedure_type SET NAME='HEP REACTION TIME' ,lab_id='1',procedure_code='HEPR',procedure_type='ord',description='HEP REACTION TIME';
INSERT INTO procedure_type SET NAME='HEROINE METAB, UR' ,lab_id='1',procedure_code='HERO',procedure_type='ord',description='HEROINE METAB, UR';
INSERT INTO procedure_type SET NAME='OPIATES,6AM CONFIRM' ,lab_id='1',procedure_code='HEROC',procedure_type='ord',description='OPIATES,6AM CONFIRM';
INSERT INTO procedure_type SET NAME='HEPATITIS E AB (IgM)' ,lab_id='1',procedure_code='HEVM',procedure_type='ord',description='HEPATITIS E AB (IgM)';
INSERT INTO procedure_type SET NAME='LUPUS ANTICOAG HEXA' ,lab_id='1',procedure_code='HEXA',procedure_type='ord',description='LUPUS ANTICOAG HEXA';
INSERT INTO procedure_type SET NAME='LUPUS ANTICOAG HEXA INTERP(MD)' ,lab_id='1',procedure_code='HEXAPF',procedure_type='ord',description='LUPUS ANTICOAG HEXA INTERP(MD)';
INSERT INTO procedure_type SET NAME='Do not use -- typo' ,lab_id='1',procedure_code='HFAFX',procedure_type='ord',description='Do not use -- typo';
INSERT INTO procedure_type SET NAME='HYDROXYETHYLFLURAZEPAM' ,lab_id='1',procedure_code='HFPM',procedure_type='ord',description='HYDROXYETHYLFLURAZEPAM';
INSERT INTO procedure_type SET NAME='MERCURY, BLOOD' ,lab_id='1',procedure_code='HG',procedure_type='ord',description='MERCURY, BLOOD';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN A2' ,lab_id='1',procedure_code='HGA2',procedure_type='ord',description='HEMOGLOBIN A2';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN' ,lab_id='1',procedure_code='HGB',procedure_type='ord',description='HEMOGLOBIN';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN A' ,lab_id='1',procedure_code='HGBA',procedure_type='ord',description='HEMOGLOBIN A';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN C' ,lab_id='1',procedure_code='HGBC',procedure_type='ord',description='HEMOGLOBIN C';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN D OR G' ,lab_id='1',procedure_code='HGBD',procedure_type='ord',description='HEMOGLOBIN D OR G';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN E OR O' ,lab_id='1',procedure_code='HGBE',procedure_type='ord',description='HEMOGLOBIN E OR O';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN F' ,lab_id='1',procedure_code='HGBF',procedure_type='ord',description='HEMOGLOBIN F';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN, FREE' ,lab_id='1',procedure_code='HGBFR',procedure_type='ord',description='HEMOGLOBIN, FREE';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN, MANUAL' ,lab_id='1',procedure_code='HGBM',procedure_type='ord',description='HEMOGLOBIN, MANUAL';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN S' ,lab_id='1',procedure_code='HGBS',procedure_type='ord',description='HEMOGLOBIN S';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN, FAST' ,lab_id='1',procedure_code='HGFA',procedure_type='ord',description='HEMOGLOBIN, FAST';
INSERT INTO procedure_type SET NAME='MERCURY, URINE' ,lab_id='1',procedure_code='HGU',procedure_type='ord',description='MERCURY, URINE';
INSERT INTO procedure_type SET NAME='MERCURY,RANDOM UR' ,lab_id='1',procedure_code='HGU1',procedure_type='ord',description='MERCURY,RANDOM UR';
INSERT INTO procedure_type SET NAME='HEREDITARY HEMOCHROMATOSIS' ,lab_id='1',procedure_code='HHEM',procedure_type='ord',description='HEREDITARY HEMOCHROMATOSIS';
INSERT INTO procedure_type SET NAME='HHV-6 BY PCR' ,lab_id='1',procedure_code='HHV6',procedure_type='ord',description='HHV-6 BY PCR';
INSERT INTO procedure_type SET NAME='HHV-7 BY PCR' ,lab_id='1',procedure_code='HHV7',procedure_type='ord',description='HHV-7 BY PCR';
INSERT INTO procedure_type SET NAME='HHV-8 BY PCR' ,lab_id='1',procedure_code='HHV8',procedure_type='ord',description='HHV-8 BY PCR';
INSERT INTO procedure_type SET NAME='HIV2 AB CONFIRMATION' ,lab_id='1',procedure_code='HI2WB1',procedure_type='ord',description='HIV2 AB CONFIRMATION';
INSERT INTO procedure_type SET NAME='HIV2 AB CONFIRMATION' ,lab_id='1',procedure_code='HI2WB2',procedure_type='ord',description='HIV2 AB CONFIRMATION';
INSERT INTO procedure_type SET NAME='HISTO AB BY COMP FIX' ,lab_id='1',procedure_code='HICF',procedure_type='ord',description='HISTO AB BY COMP FIX';
INSERT INTO procedure_type SET NAME='MYCELIAL PHASE' ,lab_id='1',procedure_code='HICFM',procedure_type='ord',description='MYCELIAL PHASE';
INSERT INTO procedure_type SET NAME='YEAST PHASE' ,lab_id='1',procedure_code='HICFY',procedure_type='ord',description='YEAST PHASE';
INSERT INTO procedure_type SET NAME='HIV NUCLEIC ACID TEST' ,lab_id='1',procedure_code='HINAC',procedure_type='ord',description='HIV NUCLEIC ACID TEST';
INSERT INTO procedure_type SET NAME='HIV1 NAT CONFIRM' ,lab_id='1',procedure_code='HINAC1',procedure_type='ord',description='HIV1 NAT CONFIRM';
INSERT INTO procedure_type SET NAME='HIV1 NAT CONFIRM' ,lab_id='1',procedure_code='HINAC2',procedure_type='ord',description='HIV1 NAT CONFIRM';
INSERT INTO procedure_type SET NAME='HIV NUCLEIC ACID TEST1' ,lab_id='1',procedure_code='HINAT1',procedure_type='ord',description='HIV NUCLEIC ACID TEST1';
INSERT INTO procedure_type SET NAME='HIV NUCLEIC ACID TEST2' ,lab_id='1',procedure_code='HINAT2',procedure_type='ord',description='HIV NUCLEIC ACID TEST2';
INSERT INTO procedure_type SET NAME='HIV NAT CONFIRM' ,lab_id='1',procedure_code='HINATC',procedure_type='ord',description='HIV NAT CONFIRM';
INSERT INTO procedure_type SET NAME='HEPARIN INDUCED PLT AGG' ,lab_id='1',procedure_code='HIPA',procedure_type='ord',description='HEPARIN INDUCED PLT AGG';
INSERT INTO procedure_type SET NAME='HEPARIN INDUCED PLT ANTIBODY' ,lab_id='1',procedure_code='HIPA1',procedure_type='ord',description='HEPARIN INDUCED PLT ANTIBODY';
INSERT INTO procedure_type SET NAME='HIPA OD' ,lab_id='1',procedure_code='HIPAOD',procedure_type='ord',description='HIPA OD';
INSERT INTO procedure_type SET NAME='HISTAMINE' ,lab_id='1',procedure_code='HIST',procedure_type='ord',description='HISTAMINE';
INSERT INTO procedure_type SET NAME='HISTAMINE, URINE' ,lab_id='1',procedure_code='HISTA',procedure_type='ord',description='HISTAMINE, URINE';
INSERT INTO procedure_type SET NAME='CREATININE,24HR URINE' ,lab_id='1',procedure_code='HISTCR',procedure_type='ord',description='CREATININE,24HR URINE';
INSERT INTO procedure_type SET NAME='HISTO AG INTERPRETATION' ,lab_id='1',procedure_code='HISTIN',procedure_type='ord',description='HISTO AG INTERPRETATION';
INSERT INTO procedure_type SET NAME='HISTIDINE' ,lab_id='1',procedure_code='HISTN',procedure_type='ord',description='HISTIDINE';
INSERT INTO procedure_type SET NAME='HISTONE ANTIBODIES' ,lab_id='1',procedure_code='HISTO',procedure_type='ord',description='HISTONE ANTIBODIES';
INSERT INTO procedure_type SET NAME='RESULT (NG/ML)' ,lab_id='1',procedure_code='HISTR',procedure_type='ord',description='RESULT (NG/ML)';
INSERT INTO procedure_type SET NAME='SPECIMEN TYPE' ,lab_id='1',procedure_code='HISTST',procedure_type='ord',description='SPECIMEN TYPE';
INSERT INTO procedure_type SET NAME='HIV 1,2 ANTIBODY' ,lab_id='1',procedure_code='HIV1SO',procedure_type='ord',description='HIV 1,2 ANTIBODY';
INSERT INTO procedure_type SET NAME='HIV 1,2 ANTIBODY (BSI)' ,lab_id='1',procedure_code='HIV2SO',procedure_type='ord',description='HIV 1,2 ANTIBODY (BSI)';
INSERT INTO procedure_type SET NAME='HIV 1,2 ANTIBODY' ,lab_id='1',procedure_code='HIVA1',procedure_type='ord',description='HIV 1,2 ANTIBODY';
INSERT INTO procedure_type SET NAME='HIV 1,2 ANTIBODY' ,lab_id='1',procedure_code='HIVA2',procedure_type='ord',description='HIV 1,2 ANTIBODY';
INSERT INTO procedure_type SET NAME='HIV 1,2 ANTIBODY (BSI)' ,lab_id='1',procedure_code='HIVASO',procedure_type='ord',description='HIV 1,2 ANTIBODY (BSI)';
INSERT INTO procedure_type SET NAME='VIRAL TEST B' ,lab_id='1',procedure_code='HIVB1',procedure_type='ord',description='VIRAL TEST B';
INSERT INTO procedure_type SET NAME='HIV1 RNA, QUANT by bDNA' ,lab_id='1',procedure_code='HIVB2',procedure_type='ord',description='HIV1 RNA, QUANT by bDNA';
INSERT INTO procedure_type SET NAME='HIV1 Log Copies/mL' ,lab_id='1',procedure_code='HIVB3',procedure_type='ord',description='HIV1 Log Copies/mL';
INSERT INTO procedure_type SET NAME='HIV p24 ANTIGEN' ,lab_id='1',procedure_code='HIVC1',procedure_type='ord',description='HIV p24 ANTIGEN';
INSERT INTO procedure_type SET NAME='SCREENING HIV' ,lab_id='1',procedure_code='HIVR1',procedure_type='ord',description='SCREENING HIV';
INSERT INTO procedure_type SET NAME='SCREENING HIV' ,lab_id='1',procedure_code='HIVR2',procedure_type='ord',description='SCREENING HIV';
INSERT INTO procedure_type SET NAME='HIVRT INTERP (MD):' ,lab_id='1',procedure_code='HIVRPF',procedure_type='ord',description='HIVRT INTERP (MD):';
INSERT INTO procedure_type SET NAME='HIV1 REAL TIME' ,lab_id='1',procedure_code='HIVRT1',procedure_type='ord',description='HIV1 REAL TIME';
INSERT INTO procedure_type SET NAME='HIV1 LOG Copies/mL' ,lab_id='1',procedure_code='HIVRT2',procedure_type='ord',description='HIV1 LOG Copies/mL';
INSERT INTO procedure_type SET NAME='HLA ABC TYPING' ,lab_id='1',procedure_code='HLA',procedure_type='ord',description='HLA ABC TYPING';
INSERT INTO procedure_type SET NAME='HLA ANTIBODY SCREEN' ,lab_id='1',procedure_code='HLAX',procedure_type='ord',description='HLA ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='HLA ANTIBODY ID' ,lab_id='1',procedure_code='HLID',procedure_type='ord',description='HLA ANTIBODY ID';
INSERT INTO procedure_type SET NAME='HEMOSIDERIN, URINE' ,lab_id='1',procedure_code='HMSU',procedure_type='ord',description='HEMOSIDERIN, URINE';
INSERT INTO procedure_type SET NAME='HIV AND HCV NAT' ,lab_id='1',procedure_code='HNAT',procedure_type='ord',description='HIV AND HCV NAT';
INSERT INTO procedure_type SET NAME='HOMOGENTISIC ACID' ,lab_id='1',procedure_code='HOMO',procedure_type='ord',description='HOMOGENTISIC ACID';
INSERT INTO procedure_type SET NAME='HOMOCYSTEINE' ,lab_id='1',procedure_code='HOMOCP',procedure_type='ord',description='HOMOCYSTEINE';
INSERT INTO procedure_type SET NAME='HEPARIN/PLT FACTOR 4 ANTIBODY' ,lab_id='1',procedure_code='HPA',procedure_type='ord',description='HEPARIN/PLT FACTOR 4 ANTIBODY';
INSERT INTO procedure_type SET NAME='HELICOBAC PYLORI AG' ,lab_id='1',procedure_code='HPAG',procedure_type='ord',description='HELICOBAC PYLORI AG';
INSERT INTO procedure_type SET NAME='HEMOPOIETIC NUMBER' ,lab_id='1',procedure_code='HPCNUM',procedure_type='ord',description='HEMOPOIETIC NUMBER';
INSERT INTO procedure_type SET NAME='HEPAR PPT FIBRINOGEN' ,lab_id='1',procedure_code='HPF',procedure_type='ord',description='HEPAR PPT FIBRINOGEN';
INSERT INTO procedure_type SET NAME='HEPTACARBOXYLPORPHYRINS' ,lab_id='1',procedure_code='HPPE',procedure_type='ord',description='HEPTACARBOXYLPORPHYRINS';
INSERT INTO procedure_type SET NAME='HEPTAPORPHYRIN' ,lab_id='1',procedure_code='HPPU',procedure_type='ord',description='HEPTAPORPHYRIN';
INSERT INTO procedure_type SET NAME='HEPTAPORPHYRIN' ,lab_id='1',procedure_code='HPPUR',procedure_type='ord',description='HEPTAPORPHYRIN';
INSERT INTO procedure_type SET NAME='17 HYDROXYPREGNENOLONE' ,lab_id='1',procedure_code='HPRE',procedure_type='ord',description='17 HYDROXYPREGNENOLONE';
INSERT INTO procedure_type SET NAME='HYDROXYPROLINE, TOTAL' ,lab_id='1',procedure_code='HPT',procedure_type='ord',description='HYDROXYPROLINE, TOTAL';
INSERT INTO procedure_type SET NAME='OH PROLINE, TOTAL RU' ,lab_id='1',procedure_code='HPTR',procedure_type='ord',description='OH PROLINE, TOTAL RU';
INSERT INTO procedure_type SET NAME='HPV DNA HYBRID CAPTURE 2' ,lab_id='1',procedure_code='HPV',procedure_type='ord',description='HPV DNA HYBRID CAPTURE 2';
INSERT INTO procedure_type SET NAME='HIV RNA QNT by PCR' ,lab_id='1',procedure_code='HRNA1',procedure_type='ord',description='HIV RNA QNT by PCR';
INSERT INTO procedure_type SET NAME='HIV RNA QUANT. PCR' ,lab_id='1',procedure_code='HRNA2A',procedure_type='ord',description='HIV RNA QUANT. PCR';
INSERT INTO procedure_type SET NAME='HOURS POST INFUSION' ,lab_id='1',procedure_code='HRS',procedure_type='ord',description='HOURS POST INFUSION';
INSERT INTO procedure_type SET NAME='HOURS COLLECTED' ,lab_id='1',procedure_code='HRSC',procedure_type='ord',description='HOURS COLLECTED';
INSERT INTO procedure_type SET NAME='HSV 1 ANTIBODY' ,lab_id='1',procedure_code='HS1D',procedure_type='ord',description='HSV 1 ANTIBODY';
INSERT INTO procedure_type SET NAME='HSV 2 ANTIBODY' ,lab_id='1',procedure_code='HS2D',procedure_type='ord',description='HSV 2 ANTIBODY';
INSERT INTO procedure_type SET NAME='HRS SINCE COLLECTION' ,lab_id='1',procedure_code='HSC',procedure_type='ord',description='HRS SINCE COLLECTION';
INSERT INTO procedure_type SET NAME='Hgb S,C AND E MUTANTS' ,lab_id='1',procedure_code='HSCE',procedure_type='ord',description='Hgb S,C AND E MUTANTS';
INSERT INTO procedure_type SET NAME='Hgb S C E Interp (MD):' ,lab_id='1',procedure_code='HSCEPF',procedure_type='ord',description='Hgb S C E Interp (MD):';
INSERT INTO procedure_type SET NAME='HOLD FOR RESIDENT REVIEW' ,lab_id='1',procedure_code='HSEM',procedure_type='ord',description='HOLD FOR RESIDENT REVIEW';
INSERT INTO procedure_type SET NAME='PROCESS AND HOLD' ,lab_id='1',procedure_code='HSFD',procedure_type='ord',description='PROCESS AND HOLD';
INSERT INTO procedure_type SET NAME='HEEL OR FINGERSTICK' ,lab_id='1',procedure_code='HSFS',procedure_type='ord',description='HEEL OR FINGERSTICK';
INSERT INTO procedure_type SET NAME='HSV 1/2 BY PCR' ,lab_id='1',procedure_code='HSPCR',procedure_type='ord',description='HSV 1/2 BY PCR';
INSERT INTO procedure_type SET NAME='CD4 CD8 RATIO' ,lab_id='1',procedure_code='HSR',procedure_type='ord',description='CD4 CD8 RATIO';
INSERT INTO procedure_type SET NAME='CD4 CD8 RATIO (CALC)' ,lab_id='1',procedure_code='HSRC',procedure_type='ord',description='CD4 CD8 RATIO (CALC)';
INSERT INTO procedure_type SET NAME='HISTOPLASMA AB' ,lab_id='1',procedure_code='HSTO',procedure_type='ord',description='HISTOPLASMA AB';
INSERT INTO procedure_type SET NAME='HISTOPLASMA AG' ,lab_id='1',procedure_code='HSTOAU',procedure_type='ord',description='HISTOPLASMA AG';
INSERT INTO procedure_type SET NAME='HSV 1 Ab, IgG' ,lab_id='1',procedure_code='HSV1',procedure_type='ord',description='HSV 1 Ab, IgG';
INSERT INTO procedure_type SET NAME='HSV 1 IGM SCREEN' ,lab_id='1',procedure_code='HSV1M',procedure_type='ord',description='HSV 1 IGM SCREEN';
INSERT INTO procedure_type SET NAME='HSV 1 IGM TITER' ,lab_id='1',procedure_code='HSV1MT',procedure_type='ord',description='HSV 1 IGM TITER';
INSERT INTO procedure_type SET NAME='HSV 1 Ab Index' ,lab_id='1',procedure_code='HSV1X',procedure_type='ord',description='HSV 1 Ab Index';
INSERT INTO procedure_type SET NAME='HSV 2 Ab, IgG' ,lab_id='1',procedure_code='HSV2',procedure_type='ord',description='HSV 2 Ab, IgG';
INSERT INTO procedure_type SET NAME='HSV 2 IGM SCREEN' ,lab_id='1',procedure_code='HSV2M',procedure_type='ord',description='HSV 2 IGM SCREEN';
INSERT INTO procedure_type SET NAME='HSV 2 IGM TITER' ,lab_id='1',procedure_code='HSV2MT',procedure_type='ord',description='HSV 2 IGM TITER';
INSERT INTO procedure_type SET NAME='HSV 2 Ab Index' ,lab_id='1',procedure_code='HSV2X',procedure_type='ord',description='HSV 2 Ab Index';
INSERT INTO procedure_type SET NAME='HERPES SIMPLEX CF AB' ,lab_id='1',procedure_code='HSVI',procedure_type='ord',description='HERPES SIMPLEX CF AB';
INSERT INTO procedure_type SET NAME='HSV IGM AB SCREEN' ,lab_id='1',procedure_code='HSVM',procedure_type='ord',description='HSV IGM AB SCREEN';
INSERT INTO procedure_type SET NAME='HSV IGM TITER' ,lab_id='1',procedure_code='HSVMT',procedure_type='ord',description='HSV IGM TITER';
INSERT INTO procedure_type SET NAME='HEIGHT CM' ,lab_id='1',procedure_code='HT',procedure_type='ord',description='HEIGHT CM';
INSERT INTO procedure_type SET NAME='HTLV1/2 WESTERN BLOT' ,lab_id='1',procedure_code='HT1WB',procedure_type='ord',description='HTLV1/2 WESTERN BLOT';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='HTID',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='HTLV I ANTIBODY' ,lab_id='1',procedure_code='HTLI',procedure_type='ord',description='HTLV I ANTIBODY';
INSERT INTO procedure_type SET NAME='HTLV I AND II ANTIBODY' ,lab_id='1',procedure_code='HTLV',procedure_type='ord',description='HTLV I AND II ANTIBODY';
INSERT INTO procedure_type SET NAME='HTLV I/II AB, EIA' ,lab_id='1',procedure_code='HTLV12',procedure_type='ord',description='HTLV I/II AB, EIA';
INSERT INTO procedure_type SET NAME='HTLV 1 AND II ANTIBODY' ,lab_id='1',procedure_code='HTLVSO',procedure_type='ord',description='HTLV 1 AND II ANTIBODY';
INSERT INTO procedure_type SET NAME='HTLVI,II AB CONFIRM' ,lab_id='1',procedure_code='HTVC',procedure_type='ord',description='HTLVI,II AB CONFIRM';
INSERT INTO procedure_type SET NAME='HEPARIN TYPE' ,lab_id='1',procedure_code='HTYP',procedure_type='ord',description='HEPARIN TYPE';
INSERT INTO procedure_type SET NAME='HEPARIN LEVEL' ,lab_id='1',procedure_code='HUNIT',procedure_type='ord',description='HEPARIN LEVEL';
INSERT INTO procedure_type SET NAME='HOMOVANILLIC ACID' ,lab_id='1',procedure_code='HVA',procedure_type='ord',description='HOMOVANILLIC ACID';
INSERT INTO procedure_type SET NAME='HOMOVANILLIC ACID' ,lab_id='1',procedure_code='HVAN',procedure_type='ord',description='HOMOVANILLIC ACID';
INSERT INTO procedure_type SET NAME='HOMOVANILLIC ACID, RU' ,lab_id='1',procedure_code='HVAR1',procedure_type='ord',description='HOMOVANILLIC ACID, RU';
INSERT INTO procedure_type SET NAME='HISTORY FORM NEEDED' ,lab_id='1',procedure_code='HXCT1',procedure_type='ord',description='HISTORY FORM NEEDED';
INSERT INTO procedure_type SET NAME='HISTORY FORM NEEDED' ,lab_id='1',procedure_code='HXCT2',procedure_type='ord',description='HISTORY FORM NEEDED';
INSERT INTO procedure_type SET NAME='HISTORY FORM NEEDED' ,lab_id='1',procedure_code='HXCT3',procedure_type='ord',description='HISTORY FORM NEEDED';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='HXID',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='HEXACARBOXYLPORPHYRINS' ,lab_id='1',procedure_code='HXPE',procedure_type='ord',description='HEXACARBOXYLPORPHYRINS';
INSERT INTO procedure_type SET NAME='HEXAPORPHYRIN' ,lab_id='1',procedure_code='HXPU',procedure_type='ord',description='HEXAPORPHYRIN';
INSERT INTO procedure_type SET NAME='HEXAPORPHYRIN' ,lab_id='1',procedure_code='HXPUR',procedure_type='ord',description='HEXAPORPHYRIN';
INSERT INTO procedure_type SET NAME='HYDROXYLYSINE' ,lab_id='1',procedure_code='HYDROX',procedure_type='ord',description='HYDROXYLYSINE';
INSERT INTO procedure_type SET NAME='HYDROXYPROLINE' ,lab_id='1',procedure_code='HYPRO',procedure_type='ord',description='HYDROXYPROLINE';
INSERT INTO procedure_type SET NAME='IBUPROFEN' ,lab_id='1',procedure_code='IBUP',procedure_type='ord',description='IBUPROFEN';
INSERT INTO procedure_type SET NAME='IMMUNE COMPLEXES (BY C1q)' ,lab_id='1',procedure_code='IC',procedure_type='ord',description='IMMUNE COMPLEXES (BY C1q)';
INSERT INTO procedure_type SET NAME='ISLET CELL 512 AUTOAB' ,lab_id='1',procedure_code='ICA',procedure_type='ord',description='ISLET CELL 512 AUTOAB';
INSERT INTO procedure_type SET NAME='ISLET CELL ANTIBODY SCREEN' ,lab_id='1',procedure_code='ICAB',procedure_type='ord',description='ISLET CELL ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='ISLET CELL ANTIBODY TITER' ,lab_id='1',procedure_code='ICAT',procedure_type='ord',description='ISLET CELL ANTIBODY TITER';
INSERT INTO procedure_type SET NAME='IMMUNE CELL FUNCTION' ,lab_id='1',procedure_code='ICF',procedure_type='ord',description='IMMUNE CELL FUNCTION';
INSERT INTO procedure_type SET NAME='INTRA-K&L INTERP (MD):' ,lab_id='1',procedure_code='ICKLPF',procedure_type='ord',description='INTRA-K&L INTERP (MD):';
INSERT INTO procedure_type SET NAME='BILIRUBIN' ,lab_id='1',procedure_code='ICTO',procedure_type='ord',description='BILIRUBIN';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ID',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ID1',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ID2',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ID3',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ID4',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='MEMBER OF IDAM' ,lab_id='1',procedure_code='IDA1',procedure_type='ord',description='MEMBER OF IDAM';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='IDD',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='IDHG',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='SPECIMEN ID,SA' ,lab_id='1',procedure_code='IDSA',procedure_type='ord',description='SPECIMEN ID,SA';
INSERT INTO procedure_type SET NAME='INTRINSIC BLOCK AB' ,lab_id='1',procedure_code='IFBA',procedure_type='ord',description='INTRINSIC BLOCK AB';
INSERT INTO procedure_type SET NAME='INFEC. DIS. MISC' ,lab_id='1',procedure_code='IFD1',procedure_type='ord',description='INFEC. DIS. MISC';
INSERT INTO procedure_type SET NAME='IMMUNOFIXATION, SERUM' ,lab_id='1',procedure_code='IFE',procedure_type='ord',description='IMMUNOFIXATION, SERUM';
INSERT INTO procedure_type SET NAME='PARAPROTEIN TYPE, SERUM' ,lab_id='1',procedure_code='IFET',procedure_type='ord',description='PARAPROTEIN TYPE, SERUM';
INSERT INTO procedure_type SET NAME='IMMUNOFIXATION,URINE' ,lab_id='1',procedure_code='IFEU',procedure_type='ord',description='IMMUNOFIXATION,URINE';
INSERT INTO procedure_type SET NAME='PARAPROTEIN TYPE, URINE' ,lab_id='1',procedure_code='IFEUT',procedure_type='ord',description='PARAPROTEIN TYPE, URINE';
INSERT INTO procedure_type SET NAME='IgA' ,lab_id='1',procedure_code='IGA',procedure_type='ord',description='IgA';
INSERT INTO procedure_type SET NAME='IgD, SERUM' ,lab_id='1',procedure_code='IGD',procedure_type='ord',description='IgD, SERUM';
INSERT INTO procedure_type SET NAME='IgE' ,lab_id='1',procedure_code='IGE',procedure_type='ord',description='IgE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE1A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE1I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE1K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE1KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE1R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE2A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE2I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE2K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE2KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE2R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE3A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE3I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE3K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE3KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE3R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE4A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE4I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE4K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE4KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE4R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE5A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE5I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE5K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE5KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE5R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE6A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE6I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE6K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE6KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE6R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE7A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE7I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE7K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE7KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='% RESPONSE' ,lab_id='1',procedure_code='IGE7R',procedure_type='ord',description='% RESPONSE';
INSERT INTO procedure_type SET NAME='ALLERGEN' ,lab_id='1',procedure_code='IGE8A',procedure_type='ord',description='ALLERGEN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE8I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE8K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE8KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='SPECIFIC IGE' ,lab_id='1',procedure_code='IGE9A',procedure_type='ord',description='SPECIFIC IGE';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE9I',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='IGE KU/L' ,lab_id='1',procedure_code='IGE9K',procedure_type='ord',description='IGE KU/L';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='IGE9KI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='mRAST CLASS' ,lab_id='1',procedure_code='IGEC',procedure_type='ord',description='mRAST CLASS';
INSERT INTO procedure_type SET NAME='Description of Class' ,lab_id='1',procedure_code='IGED',procedure_type='ord',description='Description of Class';
INSERT INTO procedure_type SET NAME='Specific IgE' ,lab_id='1',procedure_code='IGER',procedure_type='ord',description='Specific IgE';
INSERT INTO procedure_type SET NAME='IGE RESULTS' ,lab_id='1',procedure_code='IGERES',procedure_type='ord',description='IGE RESULTS';
INSERT INTO procedure_type SET NAME='IGF I' ,lab_id='1',procedure_code='IGF1',procedure_type='ord',description='IGF I';
INSERT INTO procedure_type SET NAME='IGF II' ,lab_id='1',procedure_code='IGF2',procedure_type='ord',description='IGF II';
INSERT INTO procedure_type SET NAME='IgG' ,lab_id='1',procedure_code='IGG',procedure_type='ord',description='IgG';
INSERT INTO procedure_type SET NAME='IGG1 SUBCLASS' ,lab_id='1',procedure_code='IGG1',procedure_type='ord',description='IGG1 SUBCLASS';
INSERT INTO procedure_type SET NAME='IGG2 SUBCLASS' ,lab_id='1',procedure_code='IGG2',procedure_type='ord',description='IGG2 SUBCLASS';
INSERT INTO procedure_type SET NAME='IGG3 SUBCLASS' ,lab_id='1',procedure_code='IGG3',procedure_type='ord',description='IGG3 SUBCLASS';
INSERT INTO procedure_type SET NAME='IGG4 SUBCLASS' ,lab_id='1',procedure_code='IGG4',procedure_type='ord',description='IGG4 SUBCLASS';
INSERT INTO procedure_type SET NAME='IGG, CSF' ,lab_id='1',procedure_code='IGGC',procedure_type='ord',description='IGG, CSF';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='IGGIID',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='IGG, SERUM' ,lab_id='1',procedure_code='IGGS',procedure_type='ord',description='IGG, SERUM';
INSERT INTO procedure_type SET NAME='IGG,TOTAL' ,lab_id='1',procedure_code='IGGT',procedure_type='ord',description='IGG,TOTAL';
INSERT INTO procedure_type SET NAME='LYME DISEASE AB IGG, WB' ,lab_id='1',procedure_code='IGGWB',procedure_type='ord',description='LYME DISEASE AB IGG, WB';
INSERT INTO procedure_type SET NAME='IGH (14q32)' ,lab_id='1',procedure_code='IGH',procedure_type='ord',description='IGH (14q32)';
INSERT INTO procedure_type SET NAME='IMM GRAN, LEFT SHIFT' ,lab_id='1',procedure_code='IGLSA',procedure_type='ord',description='IMM GRAN, LEFT SHIFT';
INSERT INTO procedure_type SET NAME='IgM' ,lab_id='1',procedure_code='IGM',procedure_type='ord',description='IgM';
INSERT INTO procedure_type SET NAME='LYME DISEASE AB IGM, WB' ,lab_id='1',procedure_code='IGMWB',procedure_type='ord',description='LYME DISEASE AB IGM, WB';
INSERT INTO procedure_type SET NAME='IL28B GENOTYPE' ,lab_id='1',procedure_code='IL28B',procedure_type='ord',description='IL28B GENOTYPE';
INSERT INTO procedure_type SET NAME='INTERLEUKIN-6' ,lab_id='1',procedure_code='IL6',procedure_type='ord',description='INTERLEUKIN-6';
INSERT INTO procedure_type SET NAME='% LYMPHOCYTES' ,lab_id='1',procedure_code='ILYM',procedure_type='ord',description='% LYMPHOCYTES';
INSERT INTO procedure_type SET NAME='IMIPENEM' ,lab_id='1',procedure_code='IMIPEN',procedure_type='ord',description='IMIPENEM';
INSERT INTO procedure_type SET NAME='TOTAL IMIP/DESI' ,lab_id='1',procedure_code='IMIPT',procedure_type='ord',description='TOTAL IMIP/DESI';
INSERT INTO procedure_type SET NAME='IMIPRAMINE' ,lab_id='1',procedure_code='IMIR',procedure_type='ord',description='IMIPRAMINE';
INSERT INTO procedure_type SET NAME='IMMUNOLOGY HOLD' ,lab_id='1',procedure_code='IMMH',procedure_type='ord',description='IMMUNOLOGY HOLD';
INSERT INTO procedure_type SET NAME='IMMUNO NUMBER' ,lab_id='1',procedure_code='IMMUNO',procedure_type='ord',description='IMMUNO NUMBER';
INSERT INTO procedure_type SET NAME='IMMOTILE SPERM' ,lab_id='1',procedure_code='IMOT',procedure_type='ord',description='IMMOTILE SPERM';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='INHBI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='INHIBIN B' ,lab_id='1',procedure_code='INHNB',procedure_type='ord',description='INHIBIN B';
INSERT INTO procedure_type SET NAME='INSULIN AUTOANTIBODY' ,lab_id='1',procedure_code='INHS',procedure_type='ord',description='INSULIN AUTOANTIBODY';
INSERT INTO procedure_type SET NAME='INT\'L NORMLIZ RATIO' ,lab_id='1',procedure_code='INR',procedure_type='ord',description='INT\'L NORMLIZ RATIO';
INSERT INTO procedure_type SET NAME='INSULIN' ,lab_id='1',procedure_code='INSN',procedure_type='ord',description='INSULIN';
INSERT INTO procedure_type SET NAME='MARKER INTERPRETATION' ,lab_id='1',procedure_code='INTERP',procedure_type='ord',description='MARKER INTERPRETATION';
INSERT INTO procedure_type SET NAME='INV/TRANSLOCATION 16' ,lab_id='1',procedure_code='INTR16',procedure_type='ord',description='INV/TRANSLOCATION 16';
INSERT INTO procedure_type SET NAME='INV/TRANSL/DEL 16Q22' ,lab_id='1',procedure_code='INV16Q',procedure_type='ord',description='INV/TRANSL/DEL 16Q22';
INSERT INTO procedure_type SET NAME='HEMOPHILIA A INVERSION' ,lab_id='1',procedure_code='INVN',procedure_type='ord',description='HEMOPHILIA A INVERSION';
INSERT INTO procedure_type SET NAME='IODINE, 24 HR URINE' ,lab_id='1',procedure_code='IODI',procedure_type='ord',description='IODINE, 24 HR URINE';
INSERT INTO procedure_type SET NAME='IODINE, RANDOM URINE' ,lab_id='1',procedure_code='IODUR',procedure_type='ord',description='IODINE, RANDOM URINE';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='IPRET',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='IRON' ,lab_id='1',procedure_code='IRON',procedure_type='ord',description='IRON';
INSERT INTO procedure_type SET NAME='ISCN:' ,lab_id='1',procedure_code='ISCN',procedure_type='ord',description='ISCN:';
INSERT INTO procedure_type SET NAME='Isoagglutinin Titer' ,lab_id='1',procedure_code='ISO1',procedure_type='ord',description='Isoagglutinin Titer';
INSERT INTO procedure_type SET NAME='Isoagglutinin Titer' ,lab_id='1',procedure_code='ISO2',procedure_type='ord',description='Isoagglutinin Titer';
INSERT INTO procedure_type SET NAME='Isoagglutinin Titer' ,lab_id='1',procedure_code='ISO3',procedure_type='ord',description='Isoagglutinin Titer';
INSERT INTO procedure_type SET NAME='Isoagglutinin Titer' ,lab_id='1',procedure_code='ISO4',procedure_type='ord',description='Isoagglutinin Titer';
INSERT INTO procedure_type SET NAME='ISOLEUCINE' ,lab_id='1',procedure_code='ISOLEU',procedure_type='ord',description='ISOLEUCINE';
INSERT INTO procedure_type SET NAME='ISONIAZID' ,lab_id='1',procedure_code='ISONI',procedure_type='ord',description='ISONIAZID';
INSERT INTO procedure_type SET NAME='ISOPROPANOL SOLUBILITY' ,lab_id='1',procedure_code='ISOP',procedure_type='ord',description='ISOPROPANOL SOLUBILITY';
INSERT INTO procedure_type SET NAME='SPECIMEN ID' ,lab_id='1',procedure_code='ISPID',procedure_type='ord',description='SPECIMEN ID';
INSERT INTO procedure_type SET NAME='RVVT INHIB RATIO' ,lab_id='1',procedure_code='ISR',procedure_type='ord',description='RVVT INHIB RATIO';
INSERT INTO procedure_type SET NAME='HLA-B*1502 Typing' ,lab_id='1',procedure_code='IT1502',procedure_type='ord',description='HLA-B*1502 Typing';
INSERT INTO procedure_type SET NAME='HLA-B*5701 Typing' ,lab_id='1',procedure_code='IT5701',procedure_type='ord',description='HLA-B*5701 Typing';
INSERT INTO procedure_type SET NAME='HLA-A Typing Int Resolution' ,lab_id='1',procedure_code='ITALD',procedure_type='ord',description='HLA-A Typing Int Resolution';
INSERT INTO procedure_type SET NAME='HLA Ab Screening Mixed Beads' ,lab_id='1',procedure_code='ITANS',procedure_type='ord',description='HLA Ab Screening Mixed Beads';
INSERT INTO procedure_type SET NAME='HLA-B27 Typing' ,lab_id='1',procedure_code='ITB27',procedure_type='ord',description='HLA-B27 Typing';
INSERT INTO procedure_type SET NAME='HLA-B Typing Int Resolution' ,lab_id='1',procedure_code='ITBLD',procedure_type='ord',description='HLA-B Typing Int Resolution';
INSERT INTO procedure_type SET NAME='B-Cell XM by Cytotox Donor' ,lab_id='1',procedure_code='ITBXCD',procedure_type='ord',description='B-Cell XM by Cytotox Donor';
INSERT INTO procedure_type SET NAME='B-Cell XM by Cytotox Recip' ,lab_id='1',procedure_code='ITBXCR',procedure_type='ord',description='B-Cell XM by Cytotox Recip';
INSERT INTO procedure_type SET NAME='B-Cell XM by Cytotox DTT Recip' ,lab_id='1',procedure_code='ITBXDT',procedure_type='ord',description='B-Cell XM by Cytotox DTT Recip';
INSERT INTO procedure_type SET NAME='Cytotoxicity Antibody Screen' ,lab_id='1',procedure_code='ITCAS',procedure_type='ord',description='Cytotoxicity Antibody Screen';
INSERT INTO procedure_type SET NAME='HLA-C Typing Int Resolution' ,lab_id='1',procedure_code='ITCLD',procedure_type='ord',description='HLA-C Typing Int Resolution';
INSERT INTO procedure_type SET NAME='DNA Preparation and Storage' ,lab_id='1',procedure_code='ITCPP',procedure_type='ord',description='DNA Preparation and Storage';
INSERT INTO procedure_type SET NAME='HLA-DRB3 4 5 High Res Typing' ,lab_id='1',procedure_code='ITDDX',procedure_type='ord',description='HLA-DRB3 4 5 High Res Typing';
INSERT INTO procedure_type SET NAME='HLA-DPA1 High Res Typing' ,lab_id='1',procedure_code='ITDPA',procedure_type='ord',description='HLA-DPA1 High Res Typing';
INSERT INTO procedure_type SET NAME='HLA-DPB1 High Res Typing' ,lab_id='1',procedure_code='ITDPB',procedure_type='ord',description='HLA-DPB1 High Res Typing';
INSERT INTO procedure_type SET NAME='HLA-DQA1 High Res Typing' ,lab_id='1',procedure_code='ITDQA',procedure_type='ord',description='HLA-DQA1 High Res Typing';
INSERT INTO procedure_type SET NAME='HLA-DQB1 High Res Typing' ,lab_id='1',procedure_code='ITDQB',procedure_type='ord',description='HLA-DQB1 High Res Typing';
INSERT INTO procedure_type SET NAME='HLA-DR/DQ Typ Int Resolution' ,lab_id='1',procedure_code='ITEXG',procedure_type='ord',description='HLA-DR/DQ Typ Int Resolution';
INSERT INTO procedure_type SET NAME='Immune Cell Function Assay' ,lab_id='1',procedure_code='ITICF',procedure_type='ord',description='Immune Cell Function Assay';
INSERT INTO procedure_type SET NAME='KIR Genotype - Low Resolution' ,lab_id='1',procedure_code='ITKIR',procedure_type='ord',description='KIR Genotype - Low Resolution';
INSERT INTO procedure_type SET NAME='ACD LABEL1' ,lab_id='1',procedure_code='ITLA1',procedure_type='ord',description='ACD LABEL1';
INSERT INTO procedure_type SET NAME='ACD LABEL10' ,lab_id='1',procedure_code='ITLA10',procedure_type='ord',description='ACD LABEL10';
INSERT INTO procedure_type SET NAME='ACD LABEL11' ,lab_id='1',procedure_code='ITLA11',procedure_type='ord',description='ACD LABEL11';
INSERT INTO procedure_type SET NAME='ACD LABEL12' ,lab_id='1',procedure_code='ITLA12',procedure_type='ord',description='ACD LABEL12';
INSERT INTO procedure_type SET NAME='ACD LABEL13' ,lab_id='1',procedure_code='ITLA13',procedure_type='ord',description='ACD LABEL13';
INSERT INTO procedure_type SET NAME='ACD LABEL14' ,lab_id='1',procedure_code='ITLA14',procedure_type='ord',description='ACD LABEL14';
INSERT INTO procedure_type SET NAME='ACD LABEL15' ,lab_id='1',procedure_code='ITLA15',procedure_type='ord',description='ACD LABEL15';
INSERT INTO procedure_type SET NAME='ACD LABEL16' ,lab_id='1',procedure_code='ITLA16',procedure_type='ord',description='ACD LABEL16';
INSERT INTO procedure_type SET NAME='ACD LABEL17' ,lab_id='1',procedure_code='ITLA17',procedure_type='ord',description='ACD LABEL17';
INSERT INTO procedure_type SET NAME='ACD LABEL18' ,lab_id='1',procedure_code='ITLA18',procedure_type='ord',description='ACD LABEL18';
INSERT INTO procedure_type SET NAME='ACD LABEL19' ,lab_id='1',procedure_code='ITLA19',procedure_type='ord',description='ACD LABEL19';
INSERT INTO procedure_type SET NAME='ACD LABEL2' ,lab_id='1',procedure_code='ITLA2',procedure_type='ord',description='ACD LABEL2';
INSERT INTO procedure_type SET NAME='ACD LABEL20' ,lab_id='1',procedure_code='ITLA20',procedure_type='ord',description='ACD LABEL20';
INSERT INTO procedure_type SET NAME='ACD LABEL21' ,lab_id='1',procedure_code='ITLA21',procedure_type='ord',description='ACD LABEL21';
INSERT INTO procedure_type SET NAME='ACD LABEL22' ,lab_id='1',procedure_code='ITLA22',procedure_type='ord',description='ACD LABEL22';
INSERT INTO procedure_type SET NAME='ACD LABEL23' ,lab_id='1',procedure_code='ITLA23',procedure_type='ord',description='ACD LABEL23';
INSERT INTO procedure_type SET NAME='ACD LABEL24' ,lab_id='1',procedure_code='ITLA24',procedure_type='ord',description='ACD LABEL24';
INSERT INTO procedure_type SET NAME='ACD LABEL25' ,lab_id='1',procedure_code='ITLA25',procedure_type='ord',description='ACD LABEL25';
INSERT INTO procedure_type SET NAME='ACD LABEL26' ,lab_id='1',procedure_code='ITLA26',procedure_type='ord',description='ACD LABEL26';
INSERT INTO procedure_type SET NAME='ACD LABEL27' ,lab_id='1',procedure_code='ITLA27',procedure_type='ord',description='ACD LABEL27';
INSERT INTO procedure_type SET NAME='ACD LABEL28' ,lab_id='1',procedure_code='ITLA28',procedure_type='ord',description='ACD LABEL28';
INSERT INTO procedure_type SET NAME='ACD LABEL29' ,lab_id='1',procedure_code='ITLA29',procedure_type='ord',description='ACD LABEL29';
INSERT INTO procedure_type SET NAME='ACD LABEL3' ,lab_id='1',procedure_code='ITLA3',procedure_type='ord',description='ACD LABEL3';
INSERT INTO procedure_type SET NAME='ACD LABEL30' ,lab_id='1',procedure_code='ITLA30',procedure_type='ord',description='ACD LABEL30';
INSERT INTO procedure_type SET NAME='ACD LABEL31' ,lab_id='1',procedure_code='ITLA31',procedure_type='ord',description='ACD LABEL31';
INSERT INTO procedure_type SET NAME='ACD LABEL32' ,lab_id='1',procedure_code='ITLA32',procedure_type='ord',description='ACD LABEL32';
INSERT INTO procedure_type SET NAME='ACD LABEL33' ,lab_id='1',procedure_code='ITLA33',procedure_type='ord',description='ACD LABEL33';
INSERT INTO procedure_type SET NAME='ACD LABEL34' ,lab_id='1',procedure_code='ITLA34',procedure_type='ord',description='ACD LABEL34';
INSERT INTO procedure_type SET NAME='ACD LABEL35' ,lab_id='1',procedure_code='ITLA35',procedure_type='ord',description='ACD LABEL35';
INSERT INTO procedure_type SET NAME='ACD LABEL36' ,lab_id='1',procedure_code='ITLA36',procedure_type='ord',description='ACD LABEL36';
INSERT INTO procedure_type SET NAME='ACD LABEL37' ,lab_id='1',procedure_code='ITLA37',procedure_type='ord',description='ACD LABEL37';
INSERT INTO procedure_type SET NAME='ACD LABEL38' ,lab_id='1',procedure_code='ITLA38',procedure_type='ord',description='ACD LABEL38';
INSERT INTO procedure_type SET NAME='ACD LABEL39' ,lab_id='1',procedure_code='ITLA39',procedure_type='ord',description='ACD LABEL39';
INSERT INTO procedure_type SET NAME='ACD LABEL4' ,lab_id='1',procedure_code='ITLA4',procedure_type='ord',description='ACD LABEL4';
INSERT INTO procedure_type SET NAME='ACD LABEL40' ,lab_id='1',procedure_code='ITLA40',procedure_type='ord',description='ACD LABEL40';
INSERT INTO procedure_type SET NAME='ACD LABEL41' ,lab_id='1',procedure_code='ITLA41',procedure_type='ord',description='ACD LABEL41';
INSERT INTO procedure_type SET NAME='ACD LABEL42' ,lab_id='1',procedure_code='ITLA42',procedure_type='ord',description='ACD LABEL42';
INSERT INTO procedure_type SET NAME='ACD LABEL43' ,lab_id='1',procedure_code='ITLA43',procedure_type='ord',description='ACD LABEL43';
INSERT INTO procedure_type SET NAME='ACD LABEL44' ,lab_id='1',procedure_code='ITLA44',procedure_type='ord',description='ACD LABEL44';
INSERT INTO procedure_type SET NAME='ACD LABEL45' ,lab_id='1',procedure_code='ITLA45',procedure_type='ord',description='ACD LABEL45';
INSERT INTO procedure_type SET NAME='ACD LABEL46' ,lab_id='1',procedure_code='ITLA46',procedure_type='ord',description='ACD LABEL46';
INSERT INTO procedure_type SET NAME='ACD LABEL47' ,lab_id='1',procedure_code='ITLA47',procedure_type='ord',description='ACD LABEL47';
INSERT INTO procedure_type SET NAME='ACD LABEL48' ,lab_id='1',procedure_code='ITLA48',procedure_type='ord',description='ACD LABEL48';
INSERT INTO procedure_type SET NAME='ACD LABEL49' ,lab_id='1',procedure_code='ITLA49',procedure_type='ord',description='ACD LABEL49';
INSERT INTO procedure_type SET NAME='ACD LABEL5' ,lab_id='1',procedure_code='ITLA5',procedure_type='ord',description='ACD LABEL5';
INSERT INTO procedure_type SET NAME='ACD LABEL6' ,lab_id='1',procedure_code='ITLA6',procedure_type='ord',description='ACD LABEL6';
INSERT INTO procedure_type SET NAME='ACD LABEL7' ,lab_id='1',procedure_code='ITLA7',procedure_type='ord',description='ACD LABEL7';
INSERT INTO procedure_type SET NAME='ACD LABEL8' ,lab_id='1',procedure_code='ITLA8',procedure_type='ord',description='ACD LABEL8';
INSERT INTO procedure_type SET NAME='ACD LABEL9' ,lab_id='1',procedure_code='ITLA9',procedure_type='ord',description='ACD LABEL9';
INSERT INTO procedure_type SET NAME='GREEN LABEL1' ,lab_id='1',procedure_code='ITLG1',procedure_type='ord',description='GREEN LABEL1';
INSERT INTO procedure_type SET NAME='RED LABEL1' ,lab_id='1',procedure_code='ITLR1',procedure_type='ord',description='RED LABEL1';
INSERT INTO procedure_type SET NAME='RED LABEL10' ,lab_id='1',procedure_code='ITLR10',procedure_type='ord',description='RED LABEL10';
INSERT INTO procedure_type SET NAME='RED LABEL11' ,lab_id='1',procedure_code='ITLR11',procedure_type='ord',description='RED LABEL11';
INSERT INTO procedure_type SET NAME='RED LABEL12' ,lab_id='1',procedure_code='ITLR12',procedure_type='ord',description='RED LABEL12';
INSERT INTO procedure_type SET NAME='RED LABEL2' ,lab_id='1',procedure_code='ITLR2',procedure_type='ord',description='RED LABEL2';
INSERT INTO procedure_type SET NAME='RED LABEL3' ,lab_id='1',procedure_code='ITLR3',procedure_type='ord',description='RED LABEL3';
INSERT INTO procedure_type SET NAME='RED LABEL4' ,lab_id='1',procedure_code='ITLR4',procedure_type='ord',description='RED LABEL4';
INSERT INTO procedure_type SET NAME='RED LABEL5' ,lab_id='1',procedure_code='ITLR5',procedure_type='ord',description='RED LABEL5';
INSERT INTO procedure_type SET NAME='RED LABEL6' ,lab_id='1',procedure_code='ITLR6',procedure_type='ord',description='RED LABEL6';
INSERT INTO procedure_type SET NAME='RED LABEL7' ,lab_id='1',procedure_code='ITLR7',procedure_type='ord',description='RED LABEL7';
INSERT INTO procedure_type SET NAME='RED LABEL8' ,lab_id='1',procedure_code='ITLR8',procedure_type='ord',description='RED LABEL8';
INSERT INTO procedure_type SET NAME='RED LABEL9' ,lab_id='1',procedure_code='ITLR9',procedure_type='ord',description='RED LABEL9';
INSERT INTO procedure_type SET NAME='HLA Antibody Spec Class I' ,lab_id='1',procedure_code='ITLS1',procedure_type='ord',description='HLA Antibody Spec Class I';
INSERT INTO procedure_type SET NAME='HLA Antibody Spec Class II' ,lab_id='1',procedure_code='ITLS2',procedure_type='ord',description='HLA Antibody Spec Class II';
INSERT INTO procedure_type SET NAME='Chimerism Informatives' ,lab_id='1',procedure_code='ITNH1',procedure_type='ord',description='Chimerism Informatives';
INSERT INTO procedure_type SET NAME='Chimerism CD14 15 Cell Subset' ,lab_id='1',procedure_code='ITNH14',procedure_type='ord',description='Chimerism CD14 15 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism CD19 Cell Subset' ,lab_id='1',procedure_code='ITNH19',procedure_type='ord',description='Chimerism CD19 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism Whole Blood BM' ,lab_id='1',procedure_code='ITNH2',procedure_type='ord',description='Chimerism Whole Blood BM';
INSERT INTO procedure_type SET NAME='Chimerism CD3 Cell Subset' ,lab_id='1',procedure_code='ITNH3',procedure_type='ord',description='Chimerism CD3 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism CD33 Cell Subset' ,lab_id='1',procedure_code='ITNH33',procedure_type='ord',description='Chimerism CD33 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism CD34 Cell Subset' ,lab_id='1',procedure_code='ITNH34',procedure_type='ord',description='Chimerism CD34 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism CD56 Cell Subset' ,lab_id='1',procedure_code='ITNH56',procedure_type='ord',description='Chimerism CD56 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism CD71 Cell Subset' ,lab_id='1',procedure_code='ITNH71',procedure_type='ord',description='Chimerism CD71 Cell Subset';
INSERT INTO procedure_type SET NAME='Chimerism Granulocytes' ,lab_id='1',procedure_code='ITNHGR',procedure_type='ord',description='Chimerism Granulocytes';
INSERT INTO procedure_type SET NAME='HLA Antibody Test Class I PRA' ,lab_id='1',procedure_code='ITPRA1',procedure_type='ord',description='HLA Antibody Test Class I PRA';
INSERT INTO procedure_type SET NAME='HLA Antibody Test Class II PRA' ,lab_id='1',procedure_code='ITPRA2',procedure_type='ord',description='HLA Antibody Test Class II PRA';
INSERT INTO procedure_type SET NAME='ITRACONAZOLE' ,lab_id='1',procedure_code='ITRA',procedure_type='ord',description='ITRACONAZOLE';
INSERT INTO procedure_type SET NAME='HLA-A High Resolution Typing' ,lab_id='1',procedure_code='ITSEA',procedure_type='ord',description='HLA-A High Resolution Typing';
INSERT INTO procedure_type SET NAME='HLA-B High Resolution Typing' ,lab_id='1',procedure_code='ITSEB',procedure_type='ord',description='HLA-B High Resolution Typing';
INSERT INTO procedure_type SET NAME='HLA-C High Resolution Typing' ,lab_id='1',procedure_code='ITSEC',procedure_type='ord',description='HLA-C High Resolution Typing';
INSERT INTO procedure_type SET NAME='HLA-DRB1 High Res Typing' ,lab_id='1',procedure_code='ITSED',procedure_type='ord',description='HLA-DRB1 High Res Typing';
INSERT INTO procedure_type SET NAME='Serum Preparation & Storage' ,lab_id='1',procedure_code='ITSPS',procedure_type='ord',description='Serum Preparation & Storage';
INSERT INTO procedure_type SET NAME='HLA-ABC Typing Int Resolution' ,lab_id='1',procedure_code='ITSSP',procedure_type='ord',description='HLA-ABC Typing Int Resolution';
INSERT INTO procedure_type SET NAME='T-Cell XM by Cytotox Donor' ,lab_id='1',procedure_code='ITTXCD',procedure_type='ord',description='T-Cell XM by Cytotox Donor';
INSERT INTO procedure_type SET NAME='T-Cell XM by Cytotox Recipient' ,lab_id='1',procedure_code='ITTXCR',procedure_type='ord',description='T-Cell XM by Cytotox Recipient';
INSERT INTO procedure_type SET NAME='T-Cell XM by Cytotox DTT Recip' ,lab_id='1',procedure_code='ITTXDT',procedure_type='ord',description='T-Cell XM by Cytotox DTT Recip';
INSERT INTO procedure_type SET NAME='T&B-Cell XM by Flow Donor' ,lab_id='1',procedure_code='ITTXFD',procedure_type='ord',description='T&B-Cell XM by Flow Donor';
INSERT INTO procedure_type SET NAME='T&B-Cell XM by Flow Recipient' ,lab_id='1',procedure_code='ITTXFR',procedure_type='ord',description='T&B-Cell XM by Flow Recipient';
INSERT INTO procedure_type SET NAME='JAK2 MUTATION, QUAL.' ,lab_id='1',procedure_code='JAK2',procedure_type='ord',description='JAK2 MUTATION, QUAL.';
INSERT INTO procedure_type SET NAME='JAK2 MUTATION, QUANT, INTERP' ,lab_id='1',procedure_code='JAK2I',procedure_type='ord',description='JAK2 MUTATION, QUANT, INTERP';
INSERT INTO procedure_type SET NAME='JAK2 MUTATION, QUANTITATIVE' ,lab_id='1',procedure_code='JAK2Q',procedure_type='ord',description='JAK2 MUTATION, QUANTITATIVE';
INSERT INTO procedure_type SET NAME='JC VIRUS, BY PCR' ,lab_id='1',procedure_code='JCV',procedure_type='ord',description='JC VIRUS, BY PCR';
INSERT INTO procedure_type SET NAME='JKA Typing' ,lab_id='1',procedure_code='JKA',procedure_type='ord',description='JKA Typing';
INSERT INTO procedure_type SET NAME='JKB Typing' ,lab_id='1',procedure_code='JKB',procedure_type='ord',description='JKB Typing';
INSERT INTO procedure_type SET NAME='JMML SEQUENCING PANEL' ,lab_id='1',procedure_code='JMML',procedure_type='ord',description='JMML SEQUENCING PANEL';
INSERT INTO procedure_type SET NAME='JO-1 ANTIBODY' ,lab_id='1',procedure_code='JO1',procedure_type='ord',description='JO-1 ANTIBODY';
INSERT INTO procedure_type SET NAME='JO-1 AUTOANTIBODIES' ,lab_id='1',procedure_code='JO1AB',procedure_type='ord',description='JO-1 AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='POTASSIUM, SERUM' ,lab_id='1',procedure_code='K',procedure_type='ord',description='POTASSIUM, SERUM';
INSERT INTO procedure_type SET NAME='KAPPA/LAMBDA RATIO' ,lab_id='1',procedure_code='KALAR',procedure_type='ord',description='KAPPA/LAMBDA RATIO';
INSERT INTO procedure_type SET NAME='KANAMYCIN' ,lab_id='1',procedure_code='KANA',procedure_type='ord',description='KANAMYCIN';
INSERT INTO procedure_type SET NAME='KAPPA LT CHAIN,FREE' ,lab_id='1',procedure_code='KAPF',procedure_type='ord',description='KAPPA LT CHAIN,FREE';
INSERT INTO procedure_type SET NAME='POTASSIUM, BODY FL.' ,lab_id='1',procedure_code='KBF',procedure_type='ord',description='POTASSIUM, BODY FL.';
INSERT INTO procedure_type SET NAME='BCR-ABL KD MUTATIONS' ,lab_id='1',procedure_code='KDSQ',procedure_type='ord',description='BCR-ABL KD MUTATIONS';
INSERT INTO procedure_type SET NAME='KELL Typing' ,lab_id='1',procedure_code='KEL',procedure_type='ord',description='KELL Typing';
INSERT INTO procedure_type SET NAME='KETOCONAZOLE' ,lab_id='1',procedure_code='KETO',procedure_type='ord',description='KETOCONAZOLE';
INSERT INTO procedure_type SET NAME='KETONES' ,lab_id='1',procedure_code='KEUA',procedure_type='ord',description='KETONES';
INSERT INTO procedure_type SET NAME='KIF6 GENOTYPE' ,lab_id='1',procedure_code='KIF6',procedure_type='ord',description='KIF6 GENOTYPE';
INSERT INTO procedure_type SET NAME='KAPPA LIGHT CHAIN,FREE' ,lab_id='1',procedure_code='KLCF',procedure_type='ord',description='KAPPA LIGHT CHAIN,FREE';
INSERT INTO procedure_type SET NAME='KAPPA/LAMBDA,FREE' ,lab_id='1',procedure_code='KLFR',procedure_type='ord',description='KAPPA/LAMBDA,FREE';
INSERT INTO procedure_type SET NAME='FREE KAPPA URINE' ,lab_id='1',procedure_code='KLU',procedure_type='ord',description='FREE KAPPA URINE';
INSERT INTO procedure_type SET NAME='RATIO' ,lab_id='1',procedure_code='KLUR',procedure_type='ord',description='RATIO';
INSERT INTO procedure_type SET NAME='KOH SMEAR' ,lab_id='1',procedure_code='KOH',procedure_type='ord',description='KOH SMEAR';
INSERT INTO procedure_type SET NAME='POTASSIUM, WHOLE BLOOD' ,lab_id='1',procedure_code='KSB',procedure_type='ord',description='POTASSIUM, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='POTASSIUM, STOOL' ,lab_id='1',procedure_code='KST',procedure_type='ord',description='POTASSIUM, STOOL';
INSERT INTO procedure_type SET NAME='KINETICS OF THE CLOT' ,lab_id='1',procedure_code='KTEG',procedure_type='ord',description='KINETICS OF THE CLOT';
INSERT INTO procedure_type SET NAME='KETONES' ,lab_id='1',procedure_code='KTON',procedure_type='ord',description='KETONES';
INSERT INTO procedure_type SET NAME='QUICK Kt/V (JINDAL)' ,lab_id='1',procedure_code='KTV',procedure_type='ord',description='QUICK Kt/V (JINDAL)';
INSERT INTO procedure_type SET NAME='POTASSIUM, URINE' ,lab_id='1',procedure_code='KU',procedure_type='ord',description='POTASSIUM, URINE';
INSERT INTO procedure_type SET NAME='Ku AUTOANTIBODIES' ,lab_id='1',procedure_code='KUAB',procedure_type='ord',description='Ku AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='POTASSIUM PER DAY UR' ,lab_id='1',procedure_code='KUD',procedure_type='ord',description='POTASSIUM PER DAY UR';
INSERT INTO procedure_type SET NAME='LAB NO.:' ,lab_id='1',procedure_code='LABN',procedure_type='ord',description='LAB NO.:';
INSERT INTO procedure_type SET NAME='LACTATE, CSF' ,lab_id='1',procedure_code='LACS',procedure_type='ord',description='LACTATE, CSF';
INSERT INTO procedure_type SET NAME='LACTATE' ,lab_id='1',procedure_code='LACT',procedure_type='ord',description='LACTATE';
INSERT INTO procedure_type SET NAME='LACTOFERRIN,STOOL' ,lab_id='1',procedure_code='LACTOF',procedure_type='ord',description='LACTOFERRIN,STOOL';
INSERT INTO procedure_type SET NAME='LACTATE, WHOLE BLOOD' ,lab_id='1',procedure_code='LACTWB',procedure_type='ord',description='LACTATE, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='LAMBDA LT CHAIN,FREE' ,lab_id='1',procedure_code='LAMF',procedure_type='ord',description='LAMBDA LT CHAIN,FREE';
INSERT INTO procedure_type SET NAME='LAMOTRIGINE' ,lab_id='1',procedure_code='LAMI',procedure_type='ord',description='LAMOTRIGINE';
INSERT INTO procedure_type SET NAME='LYMPHOCYTE ANTI STIM' ,lab_id='1',procedure_code='LANT',procedure_type='ord',description='LYMPHOCYTE ANTI STIM';
INSERT INTO procedure_type SET NAME='LYMPHS' ,lab_id='1',procedure_code='LB',procedure_type='ord',description='LYMPHS';
INSERT INTO procedure_type SET NAME='LAMELLAR BODY COUNT' ,lab_id='1',procedure_code='LBC',procedure_type='ord',description='LAMELLAR BODY COUNT';
INSERT INTO procedure_type SET NAME='L.BRAZILIEN INTERP:' ,lab_id='1',procedure_code='LBI',procedure_type='ord',description='L.BRAZILIEN INTERP:';
INSERT INTO procedure_type SET NAME='L. BRAZILIENSIS IGG' ,lab_id='1',procedure_code='LBRAG',procedure_type='ord',description='L. BRAZILIENSIS IGG';
INSERT INTO procedure_type SET NAME='L. BRAZILIENSIS IGM' ,lab_id='1',procedure_code='LBRAM',procedure_type='ord',description='L. BRAZILIENSIS IGM';
INSERT INTO procedure_type SET NAME='LYMPHS' ,lab_id='1',procedure_code='LC',procedure_type='ord',description='LYMPHS';
INSERT INTO procedure_type SET NAME='LCM VIRUS CF ANTIB' ,lab_id='1',procedure_code='LCM',procedure_type='ord',description='LCM VIRUS CF ANTIB';
INSERT INTO procedure_type SET NAME='LCM IGG (CSF)' ,lab_id='1',procedure_code='LCMCG',procedure_type='ord',description='LCM IGG (CSF)';
INSERT INTO procedure_type SET NAME='LCM CSF INTERP:' ,lab_id='1',procedure_code='LCMCI',procedure_type='ord',description='LCM CSF INTERP:';
INSERT INTO procedure_type SET NAME='LCM IGM (CSF)' ,lab_id='1',procedure_code='LCMCM',procedure_type='ord',description='LCM IGM (CSF)';
INSERT INTO procedure_type SET NAME='LCM IGG (SERUM)' ,lab_id='1',procedure_code='LCMGS',procedure_type='ord',description='LCM IGG (SERUM)';
INSERT INTO procedure_type SET NAME='LCM INTERPRETATION:' ,lab_id='1',procedure_code='LCMI',procedure_type='ord',description='LCM INTERPRETATION:';
INSERT INTO procedure_type SET NAME='LCM SERUM IGM' ,lab_id='1',procedure_code='LCMMS',procedure_type='ord',description='LCM SERUM IGM';
INSERT INTO procedure_type SET NAME='LYMPHOCYTE X MATCH' ,lab_id='1',procedure_code='LCX',procedure_type='ord',description='LYMPHOCYTE X MATCH';
INSERT INTO procedure_type SET NAME='LEUKEMIA CYTOGENETICS' ,lab_id='1',procedure_code='LCYT',procedure_type='ord',description='LEUKEMIA CYTOGENETICS';
INSERT INTO procedure_type SET NAME='LD' ,lab_id='1',procedure_code='LD',procedure_type='ord',description='LD';
INSERT INTO procedure_type SET NAME='18kD(IgG)Band:' ,lab_id='1',procedure_code='LD18',procedure_type='ord',description='18kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='23kD(IgG)Band:' ,lab_id='1',procedure_code='LD23',procedure_type='ord',description='23kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='23kD(IgM)Band:' ,lab_id='1',procedure_code='LD23M',procedure_type='ord',description='23kD(IgM)Band:';
INSERT INTO procedure_type SET NAME='28kD(IgG)Band:' ,lab_id='1',procedure_code='LD28',procedure_type='ord',description='28kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='30kD(IgG)Band:' ,lab_id='1',procedure_code='LD30',procedure_type='ord',description='30kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='39kD(IgG)Band:' ,lab_id='1',procedure_code='LD39',procedure_type='ord',description='39kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='39kD(IgM)Band:' ,lab_id='1',procedure_code='LD39M',procedure_type='ord',description='39kD(IgM)Band:';
INSERT INTO procedure_type SET NAME='41kdD(IgG)Band:' ,lab_id='1',procedure_code='LD41',procedure_type='ord',description='41kdD(IgG)Band:';
INSERT INTO procedure_type SET NAME='41kD(IgM)Band:' ,lab_id='1',procedure_code='LD41M',procedure_type='ord',description='41kD(IgM)Band:';
INSERT INTO procedure_type SET NAME='45kD(IgG)Band:' ,lab_id='1',procedure_code='LD45',procedure_type='ord',description='45kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='58kD(IgG)Band:' ,lab_id='1',procedure_code='LD58',procedure_type='ord',description='58kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='66kD(IgG)Band:' ,lab_id='1',procedure_code='LD66',procedure_type='ord',description='66kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='93kD(IgG)Band:' ,lab_id='1',procedure_code='LD93',procedure_type='ord',description='93kD(IgG)Band:';
INSERT INTO procedure_type SET NAME='LD, BODY FLUID' ,lab_id='1',procedure_code='LDB',procedure_type='ord',description='LD, BODY FLUID';
INSERT INTO procedure_type SET NAME='LD, CSF' ,lab_id='1',procedure_code='LDCF',procedure_type='ord',description='LD, CSF';
INSERT INTO procedure_type SET NAME='L.DONOVANI INTERP:' ,lab_id='1',procedure_code='LDI',procedure_type='ord',description='L.DONOVANI INTERP:';
INSERT INTO procedure_type SET NAME='LDL1' ,lab_id='1',procedure_code='LDL1',procedure_type='ord',description='LDL1';
INSERT INTO procedure_type SET NAME='LDL2' ,lab_id='1',procedure_code='LDL2',procedure_type='ord',description='LDL2';
INSERT INTO procedure_type SET NAME='LDL3' ,lab_id='1',procedure_code='LDL3',procedure_type='ord',description='LDL3';
INSERT INTO procedure_type SET NAME='LDL4' ,lab_id='1',procedure_code='LDL4',procedure_type='ord',description='LDL4';
INSERT INTO procedure_type SET NAME='LDL CHOLESTEROL' ,lab_id='1',procedure_code='LDLC',procedure_type='ord',description='LDL CHOLESTEROL';
INSERT INTO procedure_type SET NAME='LDL-R (REAL)-C' ,lab_id='1',procedure_code='LDLR',procedure_type='ord',description='LDL-R (REAL)-C';
INSERT INTO procedure_type SET NAME='L. DONOVANI IGG' ,lab_id='1',procedure_code='LDONG',procedure_type='ord',description='L. DONOVANI IGG';
INSERT INTO procedure_type SET NAME='L. DONOVANI IGM' ,lab_id='1',procedure_code='LDONM',procedure_type='ord',description='L. DONOVANI IGM';
INSERT INTO procedure_type SET NAME='LEAD, BLOOD' ,lab_id='1',procedure_code='LEAD',procedure_type='ord',description='LEAD, BLOOD';
INSERT INTO procedure_type SET NAME='NL LEFT EAR RESULT' ,lab_id='1',procedure_code='LEAR',procedure_type='ord',description='NL LEFT EAR RESULT';
INSERT INTO procedure_type SET NAME='LEFLUNOMIDE' ,lab_id='1',procedure_code='LEFL',procedure_type='ord',description='LEFLUNOMIDE';
INSERT INTO procedure_type SET NAME='LEGIONELLA IF ANTIB' ,lab_id='1',procedure_code='LEG',procedure_type='ord',description='LEGIONELLA IF ANTIB';
INSERT INTO procedure_type SET NAME='LEGIONELLA AG URINE' ,lab_id='1',procedure_code='LEGAU',procedure_type='ord',description='LEGIONELLA AG URINE';
INSERT INTO procedure_type SET NAME='L.PNEUMO (SEROGRP1)' ,lab_id='1',procedure_code='LEGM1',procedure_type='ord',description='L.PNEUMO (SEROGRP1)';
INSERT INTO procedure_type SET NAME='L.PNEUMO(SERO 2-6,8)' ,lab_id='1',procedure_code='LEGM2',procedure_type='ord',description='L.PNEUMO(SERO 2-6,8)';
INSERT INTO procedure_type SET NAME='LEG SP.(NON PNEUMO)' ,lab_id='1',procedure_code='LEGM3',procedure_type='ord',description='LEG SP.(NON PNEUMO)';
INSERT INTO procedure_type SET NAME='L.PNEUMOPHILA IGM' ,lab_id='1',procedure_code='LEGM4',procedure_type='ord',description='L.PNEUMOPHILA IGM';
INSERT INTO procedure_type SET NAME='LEPTIN' ,lab_id='1',procedure_code='LEPN',procedure_type='ord',description='LEPTIN';
INSERT INTO procedure_type SET NAME='LEPTOSPIRA ANTIBODY' ,lab_id='1',procedure_code='LEPT',procedure_type='ord',description='LEPTOSPIRA ANTIBODY';
INSERT INTO procedure_type SET NAME='LEPTOSPIRA AB TITER' ,lab_id='1',procedure_code='LEPTT',procedure_type='ord',description='LEPTOSPIRA AB TITER';
INSERT INTO procedure_type SET NAME='LEUCINE' ,lab_id='1',procedure_code='LEUCIN',procedure_type='ord',description='LEUCINE';
INSERT INTO procedure_type SET NAME='Interpreted by (MD):' ,lab_id='1',procedure_code='LEUMPF',procedure_type='ord',description='Interpreted by (MD):';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='LEUPF2',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='LEUPF3',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='LEVETIRACETAM' ,lab_id='1',procedure_code='LEV',procedure_type='ord',description='LEVETIRACETAM';
INSERT INTO procedure_type SET NAME='LEVOFLOXACIN' ,lab_id='1',procedure_code='LEVOF',procedure_type='ord',description='LEVOFLOXACIN';
INSERT INTO procedure_type SET NAME='LYMPHOCYTE FUNCTION' ,lab_id='1',procedure_code='LFUNC',procedure_type='ord',description='LYMPHOCYTE FUNCTION';
INSERT INTO procedure_type SET NAME='LH' ,lab_id='1',procedure_code='LH',procedure_type='ord',description='LH';
INSERT INTO procedure_type SET NAME='LITHIUM' ,lab_id='1',procedure_code='LI',procedure_type='ord',description='LITHIUM';
INSERT INTO procedure_type SET NAME='LIDOCAINE' ,lab_id='1',procedure_code='LIDO',procedure_type='ord',description='LIDOCAINE';
INSERT INTO procedure_type SET NAME='LINEZOLID' ,lab_id='1',procedure_code='LINEZ',procedure_type='ord',description='LINEZOLID';
INSERT INTO procedure_type SET NAME='LIPASE' ,lab_id='1',procedure_code='LIPA',procedure_type='ord',description='LIPASE';
INSERT INTO procedure_type SET NAME='LIQUEFACTION' ,lab_id='1',procedure_code='LIQE',procedure_type='ord',description='LIQUEFACTION';
INSERT INTO procedure_type SET NAME='LIVING DONOR MR NO.' ,lab_id='1',procedure_code='LIVEID',procedure_type='ord',description='LIVING DONOR MR NO.';
INSERT INTO procedure_type SET NAME='LIV/KID MICRO ANTIB' ,lab_id='1',procedure_code='LKM',procedure_type='ord',description='LIV/KID MICRO ANTIB';
INSERT INTO procedure_type SET NAME='LARGE LYMPHS' ,lab_id='1',procedure_code='LLC',procedure_type='ord',description='LARGE LYMPHS';
INSERT INTO procedure_type SET NAME='LAMBDA LIGHT CHAIN,FREE' ,lab_id='1',procedure_code='LLCF',procedure_type='ord',description='LAMBDA LIGHT CHAIN,FREE';
INSERT INTO procedure_type SET NAME='FREE LAMBDA URINE' ,lab_id='1',procedure_code='LLU',procedure_type='ord',description='FREE LAMBDA URINE';
INSERT INTO procedure_type SET NAME='L. MEXICANA IGG' ,lab_id='1',procedure_code='LMEXG',procedure_type='ord',description='L. MEXICANA IGG';
INSERT INTO procedure_type SET NAME='L. MEXICANA IGM' ,lab_id='1',procedure_code='LMEXM',procedure_type='ord',description='L. MEXICANA IGM';
INSERT INTO procedure_type SET NAME='L.MEXICANA INTERP:' ,lab_id='1',procedure_code='LMI',procedure_type='ord',description='L.MEXICANA INTERP:';
INSERT INTO procedure_type SET NAME='LYMPHOCYTE MITO STIM' ,lab_id='1',procedure_code='LMIT',procedure_type='ord',description='LYMPHOCYTE MITO STIM';
INSERT INTO procedure_type SET NAME='LMP or LMP EDD:' ,lab_id='1',procedure_code='LMPD',procedure_type='ord',description='LMP or LMP EDD:';
INSERT INTO procedure_type SET NAME='LOW MOL WT HEPARIN' ,lab_id='1',procedure_code='LMWH',procedure_type='ord',description='LOW MOL WT HEPARIN';
INSERT INTO procedure_type SET NAME='LIPOPROTEIN (a)' ,lab_id='1',procedure_code='LPA',procedure_type='ord',description='LIPOPROTEIN (a)';
INSERT INTO procedure_type SET NAME='LYMPH PROLIF ANTIGEN' ,lab_id='1',procedure_code='LPAG',procedure_type='ord',description='LYMPH PROLIF ANTIGEN';
INSERT INTO procedure_type SET NAME='Lp-PLA2' ,lab_id='1',procedure_code='LPLA',procedure_type='ord',description='Lp-PLA2';
INSERT INTO procedure_type SET NAME='LYMPH PROLIF MITOGEN' ,lab_id='1',procedure_code='LPMG',procedure_type='ord',description='LYMPH PROLIF MITOGEN';
INSERT INTO procedure_type SET NAME='c Typing' ,lab_id='1',procedure_code='LTC',procedure_type='ord',description='c Typing';
INSERT INTO procedure_type SET NAME='LTCO' ,lab_id='1',procedure_code='LTCO',procedure_type='ord',description='LTCO';
INSERT INTO procedure_type SET NAME='e Typing' ,lab_id='1',procedure_code='LTE',procedure_type='ord',description='e Typing';
INSERT INTO procedure_type SET NAME='L.TROPICALIS INTERP:' ,lab_id='1',procedure_code='LTI',procedure_type='ord',description='L.TROPICALIS INTERP:';
INSERT INTO procedure_type SET NAME='L. TROPICALIS IGG' ,lab_id='1',procedure_code='LTRG',procedure_type='ord',description='L. TROPICALIS IGG';
INSERT INTO procedure_type SET NAME='L. TROPICALIS IGM' ,lab_id='1',procedure_code='LTRM',procedure_type='ord',description='L. TROPICALIS IGM';
INSERT INTO procedure_type SET NAME='s Typing' ,lab_id='1',procedure_code='LTS',procedure_type='ord',description='s Typing';
INSERT INTO procedure_type SET NAME='LUC, VARIANT LYMPHS' ,lab_id='1',procedure_code='LVLA',procedure_type='ord',description='LUC, VARIANT LYMPHS';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LY1',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LY2',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LY3',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='PERCENT LYSIS' ,lab_id='1',procedure_code='LY30',procedure_type='ord',description='PERCENT LYSIS';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LY4',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LY5',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='LYSOSOMAL DISEASE SCREEN' ,lab_id='1',procedure_code='LYDX',procedure_type='ord',description='LYSOSOMAL DISEASE SCREEN';
INSERT INTO procedure_type SET NAME='LYMPHS' ,lab_id='1',procedure_code='LYMA',procedure_type='ord',description='LYMPHS';
INSERT INTO procedure_type SET NAME='LYME AB, TOTAL' ,lab_id='1',procedure_code='LYME',procedure_type='ord',description='LYME AB, TOTAL';
INSERT INTO procedure_type SET NAME='LYME CSF AB,TOTAL' ,lab_id='1',procedure_code='LYMEC',procedure_type='ord',description='LYME CSF AB,TOTAL';
INSERT INTO procedure_type SET NAME='LYME AB,TOTAL' ,lab_id='1',procedure_code='LYMET',procedure_type='ord',description='LYME AB,TOTAL';
INSERT INTO procedure_type SET NAME='LEUKOCYTE PHENOTYPE' ,lab_id='1',procedure_code='LYP1',procedure_type='ord',description='LEUKOCYTE PHENOTYPE';
INSERT INTO procedure_type SET NAME='LYSINE' ,lab_id='1',procedure_code='LYSN',procedure_type='ord',description='LYSINE';
INSERT INTO procedure_type SET NAME='LYSOZYME' ,lab_id='1',procedure_code='LYSO',procedure_type='ord',description='LYSOZYME';
INSERT INTO procedure_type SET NAME='MLL' ,lab_id='1',procedure_code='M11Q23',procedure_type='ord',description='MLL';
INSERT INTO procedure_type SET NAME='MONOSOMY 13q/del 13' ,lab_id='1',procedure_code='M13Q',procedure_type='ord',description='MONOSOMY 13q/del 13';
INSERT INTO procedure_type SET NAME='MONOSOMY 13Q34' ,lab_id='1',procedure_code='M13Q34',procedure_type='ord',description='MONOSOMY 13Q34';
INSERT INTO procedure_type SET NAME='MONOSOMY 5/DELETION 5Q' ,lab_id='1',procedure_code='M5D5Q',procedure_type='ord',description='MONOSOMY 5/DELETION 5Q';
INSERT INTO procedure_type SET NAME='MONOSOMY 7/DEL 7q' ,lab_id='1',procedure_code='M7D7Q',procedure_type='ord',description='MONOSOMY 7/DEL 7q';
INSERT INTO procedure_type SET NAME='Metaphases Analyzed:' ,lab_id='1',procedure_code='MA',procedure_type='ord',description='Metaphases Analyzed:';
INSERT INTO procedure_type SET NAME='ALBUMIN 24HR EXCR' ,lab_id='1',procedure_code='MA24',procedure_type='ord',description='ALBUMIN 24HR EXCR';
INSERT INTO procedure_type SET NAME='MAG AB (IGM), EIA' ,lab_id='1',procedure_code='MAGM',procedure_type='ord',description='MAG AB (IGM), EIA';
INSERT INTO procedure_type SET NAME='MAGNESIUM' ,lab_id='1',procedure_code='MAGN',procedure_type='ord',description='MAGNESIUM';
INSERT INTO procedure_type SET NAME='ALBUMIN, RANDOM' ,lab_id='1',procedure_code='MALR',procedure_type='ord',description='ALBUMIN, RANDOM';
INSERT INTO procedure_type SET NAME='MALT1 (IN MALT NHL)' ,lab_id='1',procedure_code='MALT1',procedure_type='ord',description='MALT1 (IN MALT NHL)';
INSERT INTO procedure_type SET NAME='METHAMPHETAMINE' ,lab_id='1',procedure_code='MAMP',procedure_type='ord',description='METHAMPHETAMINE';
INSERT INTO procedure_type SET NAME='MEAN AIRWAY PRESSURE' ,lab_id='1',procedure_code='MAP',procedure_type='ord',description='MEAN AIRWAY PRESSURE';
INSERT INTO procedure_type SET NAME='MAXIMUM AMPLITUDE' ,lab_id='1',procedure_code='MATEG',procedure_type='ord',description='MAXIMUM AMPLITUDE';
INSERT INTO procedure_type SET NAME='CK MB, IMMUNOREACTIVE' ,lab_id='1',procedure_code='MBMU',procedure_type='ord',description='CK MB, IMMUNOREACTIVE';
INSERT INTO procedure_type SET NAME='Metaphases Counted:' ,lab_id='1',procedure_code='MC',procedure_type='ord',description='Metaphases Counted:';
INSERT INTO procedure_type SET NAME='MED CHN ACYLDEHYDROGENASE DEF' ,lab_id='1',procedure_code='MCAD',procedure_type='ord',description='MED CHN ACYLDEHYDROGENASE DEF';
INSERT INTO procedure_type SET NAME='MdmChn Acl Interp (MD)' ,lab_id='1',procedure_code='MCADPF',procedure_type='ord',description='MdmChn Acl Interp (MD)';
INSERT INTO procedure_type SET NAME='MATERNAL CELL CONTAMIN' ,lab_id='1',procedure_code='MCC',procedure_type='ord',description='MATERNAL CELL CONTAMIN';
INSERT INTO procedure_type SET NAME='F508 + OTHER CF MUTANTS' ,lab_id='1',procedure_code='MCFM',procedure_type='ord',description='F508 + OTHER CF MUTANTS';
INSERT INTO procedure_type SET NAME='MCH' ,lab_id='1',procedure_code='MCH',procedure_type='ord',description='MCH';
INSERT INTO procedure_type SET NAME='MCHC' ,lab_id='1',procedure_code='MCHC',procedure_type='ord',description='MCHC';
INSERT INTO procedure_type SET NAME='MCV' ,lab_id='1',procedure_code='MCV',procedure_type='ord',description='MCV';
INSERT INTO procedure_type SET NAME='MDA' ,lab_id='1',procedure_code='MDAC',procedure_type='ord',description='MDA';
INSERT INTO procedure_type SET NAME='PHYSICIAN EVALUATION' ,lab_id='1',procedure_code='MDEV',procedure_type='ord',description='PHYSICIAN EVALUATION';
INSERT INTO procedure_type SET NAME='DELETED' ,lab_id='1',procedure_code='MDI',procedure_type='ord',description='DELETED';
INSERT INTO procedure_type SET NAME='MDMA' ,lab_id='1',procedure_code='MDMA',procedure_type='ord',description='MDMA';
INSERT INTO procedure_type SET NAME='MISC DRUG LEVEL NAME' ,lab_id='1',procedure_code='MDNO',procedure_type='ord',description='MISC DRUG LEVEL NAME';
INSERT INTO procedure_type SET NAME='MEASLES ANTIBODY' ,lab_id='1',procedure_code='MEAI',procedure_type='ord',description='MEASLES ANTIBODY';
INSERT INTO procedure_type SET NAME='MEASLES ANTIBODY' ,lab_id='1',procedure_code='MEAS',procedure_type='ord',description='MEASLES ANTIBODY';
INSERT INTO procedure_type SET NAME='MEGAKARYOCYTES' ,lab_id='1',procedure_code='MEGA',procedure_type='ord',description='MEGAKARYOCYTES';
INSERT INTO procedure_type SET NAME='METHEMOGLOBIN' ,lab_id='1',procedure_code='MEHB',procedure_type='ord',description='METHEMOGLOBIN';
INSERT INTO procedure_type SET NAME='Results:' ,lab_id='1',procedure_code='MELRES',procedure_type='ord',description='Results:';
INSERT INTO procedure_type SET NAME='MEPERIDINE' ,lab_id='1',procedure_code='MEPER',procedure_type='ord',description='MEPERIDINE';
INSERT INTO procedure_type SET NAME='MEPHENYTOIN' ,lab_id='1',procedure_code='MEPHE',procedure_type='ord',description='MEPHENYTOIN';
INSERT INTO procedure_type SET NAME='MEROPENEM' ,lab_id='1',procedure_code='MEROP',procedure_type='ord',description='MEROPENEM';
INSERT INTO procedure_type SET NAME='METANEPHRINES' ,lab_id='1',procedure_code='META',procedure_type='ord',description='METANEPHRINES';
INSERT INTO procedure_type SET NAME='METANEPHRINES,TOTAL' ,lab_id='1',procedure_code='METAN',procedure_type='ord',description='METANEPHRINES,TOTAL';
INSERT INTO procedure_type SET NAME='METANEPHRINES,TOTAL' ,lab_id='1',procedure_code='METANR',procedure_type='ord',description='METANEPHRINES,TOTAL';
INSERT INTO procedure_type SET NAME='METANEPHRINES' ,lab_id='1',procedure_code='METAR',procedure_type='ord',description='METANEPHRINES';
INSERT INTO procedure_type SET NAME='METHADONE UR CONFIRM' ,lab_id='1',procedure_code='METCON',procedure_type='ord',description='METHADONE UR CONFIRM';
INSERT INTO procedure_type SET NAME='METHOD' ,lab_id='1',procedure_code='METD',procedure_type='ord',description='METHOD';
INSERT INTO procedure_type SET NAME='METHADONE SCREEN' ,lab_id='1',procedure_code='METHA',procedure_type='ord',description='METHADONE SCREEN';
INSERT INTO procedure_type SET NAME='METHADONE SCREEN' ,lab_id='1',procedure_code='METHAD',procedure_type='ord',description='METHADONE SCREEN';
INSERT INTO procedure_type SET NAME='METHIONINE' ,lab_id='1',procedure_code='METHI',procedure_type='ord',description='METHIONINE';
INSERT INTO procedure_type SET NAME='METANEPHRINE' ,lab_id='1',procedure_code='METP',procedure_type='ord',description='METANEPHRINE';
INSERT INTO procedure_type SET NAME='METANEPHRINE, FREE' ,lab_id='1',procedure_code='METPF',procedure_type='ord',description='METANEPHRINE, FREE';
INSERT INTO procedure_type SET NAME='METRONIDAZOLE' ,lab_id='1',procedure_code='METRO',procedure_type='ord',description='METRONIDAZOLE';
INSERT INTO procedure_type SET NAME='TOTAL METANEPHRINES' ,lab_id='1',procedure_code='METT',procedure_type='ord',description='TOTAL METANEPHRINES';
INSERT INTO procedure_type SET NAME='TOTAL, FREE (MN + NMN)' ,lab_id='1',procedure_code='METTF',procedure_type='ord',description='TOTAL, FREE (MN + NMN)';
INSERT INTO procedure_type SET NAME='MEXILETINE' ,lab_id='1',procedure_code='MEXL',procedure_type='ord',description='MEXILETINE';
INSERT INTO procedure_type SET NAME='MEZLOCILLIN' ,lab_id='1',procedure_code='MEZLO',procedure_type='ord',description='MEZLOCILLIN';
INSERT INTO procedure_type SET NAME='MICROPOLYSP. FAENI' ,lab_id='1',procedure_code='MFAE',procedure_type='ord',description='MICROPOLYSP. FAENI';
INSERT INTO procedure_type SET NAME='MAGNESIUM' ,lab_id='1',procedure_code='MG',procedure_type='ord',description='MAGNESIUM';
INSERT INTO procedure_type SET NAME='MAGNESIUM,RANDOM UR' ,lab_id='1',procedure_code='MGU1',procedure_type='ord',description='MAGNESIUM,RANDOM UR';
INSERT INTO procedure_type SET NAME='MAGNESIUM, URINE' ,lab_id='1',procedure_code='MGUR',procedure_type='ord',description='MAGNESIUM, URINE';
INSERT INTO procedure_type SET NAME='MONO, HISTIOCYTES' ,lab_id='1',procedure_code='MHC',procedure_type='ord',description='MONO, HISTIOCYTES';
INSERT INTO procedure_type SET NAME='MONO,HISTIO,MESOTHEL' ,lab_id='1',procedure_code='MHMB',procedure_type='ord',description='MONO,HISTIO,MESOTHEL';
INSERT INTO procedure_type SET NAME='MI-2 AUTOANTIBODIES' ,lab_id='1',procedure_code='MI2',procedure_type='ord',description='MI-2 AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='MI-2 AUTOANTIBODIES' ,lab_id='1',procedure_code='MI2AB',procedure_type='ord',description='MI-2 AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='MARROW ID' ,lab_id='1',procedure_code='MID',procedure_type='ord',description='MARROW ID';
INSERT INTO procedure_type SET NAME='MINOCYCLINE' ,lab_id='1',procedure_code='MINO',procedure_type='ord',description='MINOCYCLINE';
INSERT INTO procedure_type SET NAME='ISCN:' ,lab_id='1',procedure_code='MISCN',procedure_type='ord',description='ISCN:';
INSERT INTO procedure_type SET NAME='MISC TEST NAME' ,lab_id='1',procedure_code='MIST',procedure_type='ord',description='MISC TEST NAME';
INSERT INTO procedure_type SET NAME='MITOCHONDRIAL AB' ,lab_id='1',procedure_code='MITO',procedure_type='ord',description='MITOCHONDRIAL AB';
INSERT INTO procedure_type SET NAME='MITOCHONDRIAL AB TITER' ,lab_id='1',procedure_code='MITOT',procedure_type='ord',description='MITOCHONDRIAL AB TITER';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 0 HR' ,lab_id='1',procedure_code='MIX0',procedure_type='ord',description='PATIENT+NML, 0 HR';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 1 HR' ,lab_id='1',procedure_code='MIX1',procedure_type='ord',description='PATIENT+NML, 1 HR';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 2HR' ,lab_id='1',procedure_code='MIX2',procedure_type='ord',description='PATIENT+NML, 2HR';
INSERT INTO procedure_type SET NAME='Metaphases Karyotyped:' ,lab_id='1',procedure_code='MK',procedure_type='ord',description='Metaphases Karyotyped:';
INSERT INTO procedure_type SET NAME='FISH FOR MLL 11Q23' ,lab_id='1',procedure_code='MLLQ23',procedure_type='ord',description='FISH FOR MLL 11Q23';
INSERT INTO procedure_type SET NAME='METHYLMALONIC ACID' ,lab_id='1',procedure_code='MMA',procedure_type='ord',description='METHYLMALONIC ACID';
INSERT INTO procedure_type SET NAME='MANGANESE' ,lab_id='1',procedure_code='MN',procedure_type='ord',description='MANGANESE';
INSERT INTO procedure_type SET NAME='MONOS' ,lab_id='1',procedure_code='MOA',procedure_type='ord',description='MONOS';
INSERT INTO procedure_type SET NAME='MONO-DI OLIGO RATIO' ,lab_id='1',procedure_code='MODIO',procedure_type='ord',description='MONO-DI OLIGO RATIO';
INSERT INTO procedure_type SET NAME='MODIFIED INHIB TITER' ,lab_id='1',procedure_code='MODIT',procedure_type='ord',description='MODIFIED INHIB TITER';
INSERT INTO procedure_type SET NAME='MOD INHB TITR INTP(MD)' ,lab_id='1',procedure_code='MODPF',procedure_type='ord',description='MOD INHB TITR INTP(MD)';
INSERT INTO procedure_type SET NAME='MONO/DI-OLIGO RATIO' ,lab_id='1',procedure_code='MODR',procedure_type='ord',description='MONO/DI-OLIGO RATIO';
INSERT INTO procedure_type SET NAME='CDT INTERPRETATION:' ,lab_id='1',procedure_code='MODRI',procedure_type='ord',description='CDT INTERPRETATION:';
INSERT INTO procedure_type SET NAME='MOLYBDENUM' ,lab_id='1',procedure_code='MOLY',procedure_type='ord',description='MOLYBDENUM';
INSERT INTO procedure_type SET NAME='MOLYBDENUM (CR COR)' ,lab_id='1',procedure_code='MOLYBC',procedure_type='ord',description='MOLYBDENUM (CR COR)';
INSERT INTO procedure_type SET NAME='MOLYBDENUM, RU' ,lab_id='1',procedure_code='MOLYBU',procedure_type='ord',description='MOLYBDENUM, RU';
INSERT INTO procedure_type SET NAME='CREATININE (MOLYU)' ,lab_id='1',procedure_code='MOLYC',procedure_type='ord',description='CREATININE (MOLYU)';
INSERT INTO procedure_type SET NAME='              MOM:' ,lab_id='1',procedure_code='MOM',procedure_type='ord',description='              MOM:';
INSERT INTO procedure_type SET NAME='HETEROPHILE AGGLUTININS' ,lab_id='1',procedure_code='MONO',procedure_type='ord',description='HETEROPHILE AGGLUTININS';
INSERT INTO procedure_type SET NAME='MORPHINE' ,lab_id='1',procedure_code='MORPH',procedure_type='ord',description='MORPHINE';
INSERT INTO procedure_type SET NAME='% MOTILE SPERM' ,lab_id='1',procedure_code='MOTI',procedure_type='ord',description='% MOTILE SPERM';
INSERT INTO procedure_type SET NAME='MOTILE SPERM' ,lab_id='1',procedure_code='MOTS',procedure_type='ord',description='MOTILE SPERM';
INSERT INTO procedure_type SET NAME='MOXIFLOXACIN' ,lab_id='1',procedure_code='MOX',procedure_type='ord',description='MOXIFLOXACIN';
INSERT INTO procedure_type SET NAME='MYCOPHENOLIC ACID' ,lab_id='1',procedure_code='MPA',procedure_type='ord',description='MYCOPHENOLIC ACID';
INSERT INTO procedure_type SET NAME='MPA GLUCURONIDE' ,lab_id='1',procedure_code='MPAG',procedure_type='ord',description='MPA GLUCURONIDE';
INSERT INTO procedure_type SET NAME='MYCOPLASMA PNEUMONIAE, IGG' ,lab_id='1',procedure_code='MPIG',procedure_type='ord',description='MYCOPLASMA PNEUMONIAE, IGG';
INSERT INTO procedure_type SET NAME='MYCOPLASMA PNEUM. IGM' ,lab_id='1',procedure_code='MPIGM',procedure_type='ord',description='MYCOPLASMA PNEUM. IGM';
INSERT INTO procedure_type SET NAME='MYCOPLAS PNEUMON AB' ,lab_id='1',procedure_code='MPNU',procedure_type='ord',description='MYCOPLAS PNEUMON AB';
INSERT INTO procedure_type SET NAME='MPO Interpreted by(MD):' ,lab_id='1',procedure_code='MPOPF',procedure_type='ord',description='MPO Interpreted by(MD):';
INSERT INTO procedure_type SET NAME='MUCOPOLYSACCHARIDES, QNT' ,lab_id='1',procedure_code='MPSQNT',procedure_type='ord',description='MUCOPOLYSACCHARIDES, QNT';
INSERT INTO procedure_type SET NAME='MUCOPOLYSACCHARIDES' ,lab_id='1',procedure_code='MPSTLC',procedure_type='ord',description='MUCOPOLYSACCHARIDES';
INSERT INTO procedure_type SET NAME='MUCOPOLYSACCHARIDES, QUAL.' ,lab_id='1',procedure_code='MPSU',procedure_type='ord',description='MUCOPOLYSACCHARIDES, QUAL.';
INSERT INTO procedure_type SET NAME='MSC GVH STUDY' ,lab_id='1',procedure_code='MSCS',procedure_type='ord',description='MSC GVH STUDY';
INSERT INTO procedure_type SET NAME='METHSUXIMIDE' ,lab_id='1',procedure_code='MSUX',procedure_type='ord',description='METHSUXIMIDE';
INSERT INTO procedure_type SET NAME='MATCH ID' ,lab_id='1',procedure_code='MTCHID',procedure_type='ord',description='MATCH ID';
INSERT INTO procedure_type SET NAME='MTHFR MUTATION' ,lab_id='1',procedure_code='MTR',procedure_type='ord',description='MTHFR MUTATION';
INSERT INTO procedure_type SET NAME='MISC TOXICOLOGY NAME' ,lab_id='1',procedure_code='MTTN',procedure_type='ord',description='MISC TOXICOLOGY NAME';
INSERT INTO procedure_type SET NAME='METHOTREXATE' ,lab_id='1',procedure_code='MTXE',procedure_type='ord',description='METHOTREXATE';
INSERT INTO procedure_type SET NAME='MAGNESIUM PER DAY, UR' ,lab_id='1',procedure_code='MUD',procedure_type='ord',description='MAGNESIUM PER DAY, UR';
INSERT INTO procedure_type SET NAME='MUMPS AB IGG, EIA' ,lab_id='1',procedure_code='MUIG',procedure_type='ord',description='MUMPS AB IGG, EIA';
INSERT INTO procedure_type SET NAME='MULTIPLE OF MEDIAN' ,lab_id='1',procedure_code='MULT',procedure_type='ord',description='MULTIPLE OF MEDIAN';
INSERT INTO procedure_type SET NAME='MUMPS AB IgG' ,lab_id='1',procedure_code='MUMG',procedure_type='ord',description='MUMPS AB IgG';
INSERT INTO procedure_type SET NAME='MUMPS VIRUS CF ANTIB' ,lab_id='1',procedure_code='MUMP',procedure_type='ord',description='MUMPS VIRUS CF ANTIB';
INSERT INTO procedure_type SET NAME='MYC (BURKITT)' ,lab_id='1',procedure_code='MYC',procedure_type='ord',description='MYC (BURKITT)';
INSERT INTO procedure_type SET NAME='MYOGLOBIN' ,lab_id='1',procedure_code='MYOG',procedure_type='ord',description='MYOGLOBIN';
INSERT INTO procedure_type SET NAME='MYELOMA P RISK SCORE' ,lab_id='1',procedure_code='MYPRS',procedure_type='ord',description='MYELOMA P RISK SCORE';
INSERT INTO procedure_type SET NAME='Bone Marrow ID#:' ,lab_id='1',procedure_code='MZBM',procedure_type='ord',description='Bone Marrow ID#:';
INSERT INTO procedure_type SET NAME='SODIUM, SERUM' ,lab_id='1',procedure_code='NA',procedure_type='ord',description='SODIUM, SERUM';
INSERT INTO procedure_type SET NAME='SODIUM, BODY FLUID' ,lab_id='1',procedure_code='NABF',procedure_type='ord',description='SODIUM, BODY FLUID';
INSERT INTO procedure_type SET NAME='DRUG SCREEN, ROUTINE NEONATAL' ,lab_id='1',procedure_code='NABU',procedure_type='ord',description='DRUG SCREEN, ROUTINE NEONATAL';
INSERT INTO procedure_type SET NAME='N ACETYL PROCAINAMIDE' ,lab_id='1',procedure_code='NACP',procedure_type='ord',description='N ACETYL PROCAINAMIDE';
INSERT INTO procedure_type SET NAME='NALADIXIC ACID' ,lab_id='1',procedure_code='NAL',procedure_type='ord',description='NALADIXIC ACID';
INSERT INTO procedure_type SET NAME='ALT (NICHOLS)' ,lab_id='1',procedure_code='NALT',procedure_type='ord',description='ALT (NICHOLS)';
INSERT INTO procedure_type SET NAME='TEST NAME' ,lab_id='1',procedure_code='NAME',procedure_type='ord',description='TEST NAME';
INSERT INTO procedure_type SET NAME='SODIUM, STOOL' ,lab_id='1',procedure_code='NASTL',procedure_type='ord',description='SODIUM, STOOL';
INSERT INTO procedure_type SET NAME='HCV AND HIV NAT' ,lab_id='1',procedure_code='NAT',procedure_type='ord',description='HCV AND HIV NAT';
INSERT INTO procedure_type SET NAME='HCV AND HIV NAT' ,lab_id='1',procedure_code='NAT1',procedure_type='ord',description='HCV AND HIV NAT';
INSERT INTO procedure_type SET NAME='NEO ALLO THROMBOCYTO' ,lab_id='1',procedure_code='NATP',procedure_type='ord',description='NEO ALLO THROMBOCYTO';
INSERT INTO procedure_type SET NAME='HBV,HCV AND HIV NAT' ,lab_id='1',procedure_code='NATT',procedure_type='ord',description='HBV,HCV AND HIV NAT';
INSERT INTO procedure_type SET NAME='SODIUM, URINE' ,lab_id='1',procedure_code='NAU',procedure_type='ord',description='SODIUM, URINE';
INSERT INTO procedure_type SET NAME='SODIUM PER DAY, UR' ,lab_id='1',procedure_code='NAUD',procedure_type='ord',description='SODIUM PER DAY, UR';
INSERT INTO procedure_type SET NAME='SODIUM, WHOLE BLOOD' ,lab_id='1',procedure_code='NAWB',procedure_type='ord',description='SODIUM, WHOLE BLOOD';
INSERT INTO procedure_type SET NAME='Number of Cultures:' ,lab_id='1',procedure_code='NB',procedure_type='ord',description='Number of Cultures:';
INSERT INTO procedure_type SET NAME='NECK DEFECT' ,lab_id='1',procedure_code='NDEF',procedure_type='ord',description='NECK DEFECT';
INSERT INTO procedure_type SET NAME='N-DESMETHYL DOXEPIN' ,lab_id='1',procedure_code='NDOX',procedure_type='ord',description='N-DESMETHYL DOXEPIN';
INSERT INTO procedure_type SET NAME='NECROINFLAM ACT.GRADE' ,lab_id='1',procedure_code='NEAF',procedure_type='ord',description='NECROINFLAM ACT.GRADE';
INSERT INTO procedure_type SET NAME='NECROINFLAM GRADE(01):' ,lab_id='1',procedure_code='NEAG',procedure_type='ord',description='NECROINFLAM GRADE(01):';
INSERT INTO procedure_type SET NAME='NECROINFLAM SCORE(01):' ,lab_id='1',procedure_code='NEAS',procedure_type='ord',description='NECROINFLAM SCORE(01):';
INSERT INTO procedure_type SET NAME='FATTY ACIDS, NONESTERIFIED' ,lab_id='1',procedure_code='NEFA',procedure_type='ord',description='FATTY ACIDS, NONESTERIFIED';
INSERT INTO procedure_type SET NAME='NOREPINEPHRINE,RANDUR' ,lab_id='1',procedure_code='NERU',procedure_type='ord',description='NOREPINEPHRINE,RANDUR';
INSERT INTO procedure_type SET NAME='NETILMICIN' ,lab_id='1',procedure_code='NETIL',procedure_type='ord',description='NETILMICIN';
INSERT INTO procedure_type SET NAME='NOREPHINEPH,UR TOT' ,lab_id='1',procedure_code='NETU',procedure_type='ord',description='NOREPHINEPH,UR TOT';
INSERT INTO procedure_type SET NAME='NEUTROPHIL ANTIBODY' ,lab_id='1',procedure_code='NEUAB',procedure_type='ord',description='NEUTROPHIL ANTIBODY';
INSERT INTO procedure_type SET NAME='NEUTS' ,lab_id='1',procedure_code='NEUTA',procedure_type='ord',description='NEUTS';
INSERT INTO procedure_type SET NAME='GALACTOSE 1 URIDYL TRANSFERASE' ,lab_id='1',procedure_code='NGAL',procedure_type='ord',description='GALACTOSE 1 URIDYL TRANSFERASE';
INSERT INTO procedure_type SET NAME='AMMONIA' ,lab_id='1',procedure_code='NH3',procedure_type='ord',description='AMMONIA';
INSERT INTO procedure_type SET NAME='HCT FROM HB' ,lab_id='1',procedure_code='NHCT',procedure_type='ord',description='HCT FROM HB';
INSERT INTO procedure_type SET NAME='NON HDL CHOLESTEROL' ,lab_id='1',procedure_code='NHDL',procedure_type='ord',description='NON HDL CHOLESTEROL';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN PATTERN' ,lab_id='1',procedure_code='NHGB',procedure_type='ord',description='HEMOGLOBIN PATTERN';
INSERT INTO procedure_type SET NAME='URINE CREAT (NICVA)' ,lab_id='1',procedure_code='NICUC',procedure_type='ord',description='URINE CREAT (NICVA)';
INSERT INTO procedure_type SET NAME='NIRVANOL' ,lab_id='1',procedure_code='NIRV',procedure_type='ord',description='NIRVANOL';
INSERT INTO procedure_type SET NAME='NITROFURANTOIN' ,lab_id='1',procedure_code='NITRO',procedure_type='ord',description='NITROFURANTOIN';
INSERT INTO procedure_type SET NAME='NITRITE' ,lab_id='1',procedure_code='NIUA',procedure_type='ord',description='NITRITE';
INSERT INTO procedure_type SET NAME='CREATININE (UR)' ,lab_id='1',procedure_code='NIUC',procedure_type='ord',description='CREATININE (UR)';
INSERT INTO procedure_type SET NAME='CD16,CD56 NK ABS' ,lab_id='1',procedure_code='NKAB',procedure_type='ord',description='CD16,CD56 NK ABS';
INSERT INTO procedure_type SET NAME='CD16,CD56 NK CELLS %' ,lab_id='1',procedure_code='NKCEL',procedure_type='ord',description='CD16,CD56 NK CELLS %';
INSERT INTO procedure_type SET NAME='NORMEPERIDINE' ,lab_id='1',procedure_code='NMEP',procedure_type='ord',description='NORMEPERIDINE';
INSERT INTO procedure_type SET NAME='NORMETANEPHRINES' ,lab_id='1',procedure_code='NMET',procedure_type='ord',description='NORMETANEPHRINES';
INSERT INTO procedure_type SET NAME='NORMETANEPHRINE' ,lab_id='1',procedure_code='NMETP',procedure_type='ord',description='NORMETANEPHRINE';
INSERT INTO procedure_type SET NAME='NORMETANEPHRINE, FREE' ,lab_id='1',procedure_code='NMETPF',procedure_type='ord',description='NORMETANEPHRINE, FREE';
INSERT INTO procedure_type SET NAME='NORMETANEPHRINE' ,lab_id='1',procedure_code='NMETR',procedure_type='ord',description='NORMETANEPHRINE';
INSERT INTO procedure_type SET NAME='NMO/AQP-4 IGG' ,lab_id='1',procedure_code='NMO',procedure_type='ord',description='NMO/AQP-4 IGG';
INSERT INTO procedure_type SET NAME='1 HR MICROBICID PWR' ,lab_id='1',procedure_code='NMP1',procedure_type='ord',description='1 HR MICROBICID PWR';
INSERT INTO procedure_type SET NAME='2 HR MICROBICID PWR' ,lab_id='1',procedure_code='NMP2',procedure_type='ord',description='2 HR MICROBICID PWR';
INSERT INTO procedure_type SET NAME='NORTRIPTYLINE' ,lab_id='1',procedure_code='NNRT',procedure_type='ord',description='NORTRIPTYLINE';
INSERT INTO procedure_type SET NAME='NORMAL-OVAL FORMS' ,lab_id='1',procedure_code='NOF',procedure_type='ord',description='NORMAL-OVAL FORMS';
INSERT INTO procedure_type SET NAME='NOI INTERPRETATION:' ,lab_id='1',procedure_code='NOII',procedure_type='ord',description='NOI INTERPRETATION:';
INSERT INTO procedure_type SET NAME='NOI INTERP BY (MD):' ,lab_id='1',procedure_code='NOIPF',procedure_type='ord',description='NOI INTERP BY (MD):';
INSERT INTO procedure_type SET NAME='NOI RESULT' ,lab_id='1',procedure_code='NOIR',procedure_type='ord',description='NOI RESULT';
INSERT INTO procedure_type SET NAME='PMN OPSONIZING/CIDY INTERP' ,lab_id='1',procedure_code='NOMPI',procedure_type='ord',description='PMN OPSONIZING/CIDY INTERP';
INSERT INTO procedure_type SET NAME='INTERPRETED BY (MD):' ,lab_id='1',procedure_code='NOMPPF',procedure_type='ord',description='INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='1 HR OPSONIZING PWR' ,lab_id='1',procedure_code='NOP1',procedure_type='ord',description='1 HR OPSONIZING PWR';
INSERT INTO procedure_type SET NAME='2 HR OPSONIZING PWR' ,lab_id='1',procedure_code='NOP2',procedure_type='ord',description='2 HR OPSONIZING PWR';
INSERT INTO procedure_type SET NAME='NOR STAINING' ,lab_id='1',procedure_code='NOR',procedure_type='ord',description='NOR STAINING';
INSERT INTO procedure_type SET NAME='CLORAZEPATE' ,lab_id='1',procedure_code='NORD',procedure_type='ord',description='CLORAZEPATE';
INSERT INTO procedure_type SET NAME='NORFLOXACIN' ,lab_id='1',procedure_code='NORFLO',procedure_type='ord',description='NORFLOXACIN';
INSERT INTO procedure_type SET NAME='NOTE' ,lab_id='1',procedure_code='NOTE',procedure_type='ord',description='NOTE';
INSERT INTO procedure_type SET NAME='NOVOBIOCIN' ,lab_id='1',procedure_code='NOVOB',procedure_type='ord',description='NOVOBIOCIN';
INSERT INTO procedure_type SET NAME='PHENOBARBITAL (NICHOLS)' ,lab_id='1',procedure_code='NPBA',procedure_type='ord',description='PHENOBARBITAL (NICHOLS)';
INSERT INTO procedure_type SET NAME='PHENOBARBITAL' ,lab_id='1',procedure_code='NPBAR',procedure_type='ord',description='PHENOBARBITAL';
INSERT INTO procedure_type SET NAME='PHENYLALANINE' ,lab_id='1',procedure_code='NPHE',procedure_type='ord',description='PHENYLALANINE';
INSERT INTO procedure_type SET NAME='NPM1 MUTATIONS' ,lab_id='1',procedure_code='NPM1',procedure_type='ord',description='NPM1 MUTATIONS';
INSERT INTO procedure_type SET NAME='PROGESTERONE' ,lab_id='1',procedure_code='NPROG',procedure_type='ord',description='PROGESTERONE';
INSERT INTO procedure_type SET NAME='NUCLEATED RBCS' ,lab_id='1',procedure_code='NRB',procedure_type='ord',description='NUCLEATED RBCS';
INSERT INTO procedure_type SET NAME='NUCLEATED RBCS' ,lab_id='1',procedure_code='NRBC',procedure_type='ord',description='NUCLEATED RBCS';
INSERT INTO procedure_type SET NAME='NUCLEATED RBCS' ,lab_id='1',procedure_code='NRC',procedure_type='ord',description='NUCLEATED RBCS';
INSERT INTO procedure_type SET NAME='NOREPINEPHRINE' ,lab_id='1',procedure_code='NREP',procedure_type='ord',description='NOREPINEPHRINE';
INSERT INTO procedure_type SET NAME='NORTRIPTYLINE' ,lab_id='1',procedure_code='NRT',procedure_type='ord',description='NORTRIPTYLINE';
INSERT INTO procedure_type SET NAME='NEURON SPEC ENOLASE' ,lab_id='1',procedure_code='NSE',procedure_type='ord',description='NEURON SPEC ENOLASE';
INSERT INTO procedure_type SET NAME='NSE, SPINAL FLUID' ,lab_id='1',procedure_code='NSECSF',procedure_type='ord',description='NSE, SPINAL FLUID';
INSERT INTO procedure_type SET NAME='15N SPUN HCT 1' ,lab_id='1',procedure_code='NSH1',procedure_type='ord',description='15N SPUN HCT 1';
INSERT INTO procedure_type SET NAME='15N SPUN HCT 2' ,lab_id='1',procedure_code='NSH2',procedure_type='ord',description='15N SPUN HCT 2';
INSERT INTO procedure_type SET NAME='PRIMARY HYPOTHYROIDISM SCRN' ,lab_id='1',procedure_code='NT4',procedure_type='ord',description='PRIMARY HYPOTHYROIDISM SCRN';
INSERT INTO procedure_type SET NAME='N-Telopeptide (NTx)' ,lab_id='1',procedure_code='NTEL',procedure_type='ord',description='N-Telopeptide (NTx)';
INSERT INTO procedure_type SET NAME='N-TELOPEPTIDE, 24 HR' ,lab_id='1',procedure_code='NTELD',procedure_type='ord',description='N-TELOPEPTIDE, 24 HR';
INSERT INTO procedure_type SET NAME='TSH' ,lab_id='1',procedure_code='NTSH',procedure_type='ord',description='TSH';
INSERT INTO procedure_type SET NAME='TYROSINE' ,lab_id='1',procedure_code='NTYR',procedure_type='ord',description='TYROSINE';
INSERT INTO procedure_type SET NAME='NUCLEIC ACID' ,lab_id='1',procedure_code='NUCL',procedure_type='ord',description='NUCLEIC ACID';
INSERT INTO procedure_type SET NAME='NUMBER OF UNITS READY' ,lab_id='1',procedure_code='NUR',procedure_type='ord',description='NUMBER OF UNITS READY';
INSERT INTO procedure_type SET NAME='NORVERAPAMIL' ,lab_id='1',procedure_code='NVER',procedure_type='ord',description='NORVERAPAMIL';
INSERT INTO procedure_type SET NAME='OXYHEMOGLOBIN' ,lab_id='1',procedure_code='O2HB',procedure_type='ord',description='OXYHEMOGLOBIN';
INSERT INTO procedure_type SET NAME='OXYGEN CONTENT' ,lab_id='1',procedure_code='O2HX',procedure_type='ord',description='OXYGEN CONTENT';
INSERT INTO procedure_type SET NAME='ORGANIC ACID SCREEN' ,lab_id='1',procedure_code='OAX',procedure_type='ord',description='ORGANIC ACID SCREEN';
INSERT INTO procedure_type SET NAME='OR COMMENT' ,lab_id='1',procedure_code='OBBC',procedure_type='ord',description='OR COMMENT';
INSERT INTO procedure_type SET NAME='OSTEOCALCIN' ,lab_id='1',procedure_code='OCAL',procedure_type='ord',description='OSTEOCALCIN';
INSERT INTO procedure_type SET NAME='OTHER CELLS' ,lab_id='1',procedure_code='OCB',procedure_type='ord',description='OTHER CELLS';
INSERT INTO procedure_type SET NAME='OCCULT BLOOD WITH CONFIRMATION' ,lab_id='1',procedure_code='OCBC',procedure_type='ord',description='OCCULT BLOOD WITH CONFIRMATION';
INSERT INTO procedure_type SET NAME='OCCULT BLOOD, STOOL' ,lab_id='1',procedure_code='OCCB',procedure_type='ord',description='OCCULT BLOOD, STOOL';
INSERT INTO procedure_type SET NAME='OCTACARBOXYLPORPHYRINS' ,lab_id='1',procedure_code='OCPE',procedure_type='ord',description='OCTACARBOXYLPORPHYRINS';
INSERT INTO procedure_type SET NAME='UROPORPHYRIN' ,lab_id='1',procedure_code='OCPU',procedure_type='ord',description='UROPORPHYRIN';
INSERT INTO procedure_type SET NAME='UROPORPHYRIN' ,lab_id='1',procedure_code='OCPUR',procedure_type='ord',description='UROPORPHYRIN';
INSERT INTO procedure_type SET NAME='DELTA O.D. 450 PEAK' ,lab_id='1',procedure_code='OD45',procedure_type='ord',description='DELTA O.D. 450 PEAK';
INSERT INTO procedure_type SET NAME='ORGAN DONOR' ,lab_id='1',procedure_code='ODNR',procedure_type='ord',description='ORGAN DONOR';
INSERT INTO procedure_type SET NAME='OFLOXACIN' ,lab_id='1',procedure_code='OFLOX',procedure_type='ord',description='OFLOXACIN';
INSERT INTO procedure_type SET NAME='OLIGOSACCHARIDES' ,lab_id='1',procedure_code='OGSA',procedure_type='ord',description='OLIGOSACCHARIDES';
INSERT INTO procedure_type SET NAME='OGT GLUCOLA DOSE' ,lab_id='1',procedure_code='OGTD',procedure_type='ord',description='OGT GLUCOLA DOSE';
INSERT INTO procedure_type SET NAME='OGTP GLUCOLA DOSE' ,lab_id='1',procedure_code='OGTPD',procedure_type='ord',description='OGTP GLUCOLA DOSE';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='OINTR',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='OJ AUTOANTIBODIES' ,lab_id='1',procedure_code='OJAB',procedure_type='ord',description='OJ AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='HELPER SUBSET' ,lab_id='1',procedure_code='OKT3',procedure_type='ord',description='HELPER SUBSET';
INSERT INTO procedure_type SET NAME='PRODUCT TESTING' ,lab_id='1',procedure_code='OKTR',procedure_type='ord',description='PRODUCT TESTING';
INSERT INTO procedure_type SET NAME='OIL' ,lab_id='1',procedure_code='OL',procedure_type='ord',description='OIL';
INSERT INTO procedure_type SET NAME='OLIGOCLONAL Ig BANDS' ,lab_id='1',procedure_code='OLIC',procedure_type='ord',description='OLIGOCLONAL Ig BANDS';
INSERT INTO procedure_type SET NAME='Olig Bands Interp (MD):' ,lab_id='1',procedure_code='OLIGPF',procedure_type='ord',description='Olig Bands Interp (MD):';
INSERT INTO procedure_type SET NAME='SERUM IN PARALLEL' ,lab_id='1',procedure_code='OLIS',procedure_type='ord',description='SERUM IN PARALLEL';
INSERT INTO procedure_type SET NAME='MORPHOLOGY (WBC, MISC)' ,lab_id='1',procedure_code='OMOR',procedure_type='ord',description='MORPHOLOGY (WBC, MISC)';
INSERT INTO procedure_type SET NAME='OPERATOR' ,lab_id='1',procedure_code='OPER',procedure_type='ord',description='OPERATOR';
INSERT INTO procedure_type SET NAME='OPIATES SCRN. UR.' ,lab_id='1',procedure_code='OPI',procedure_type='ord',description='OPIATES SCRN. UR.';
INSERT INTO procedure_type SET NAME='OPIATES, UR' ,lab_id='1',procedure_code='OPIA',procedure_type='ord',description='OPIATES, UR';
INSERT INTO procedure_type SET NAME='OPIATES BY GC/MS' ,lab_id='1',procedure_code='OPIAC',procedure_type='ord',description='OPIATES BY GC/MS';
INSERT INTO procedure_type SET NAME='OR COMMENT' ,lab_id='1',procedure_code='ORC',procedure_type='ord',description='OR COMMENT';
INSERT INTO procedure_type SET NAME='ORGAN RECIPIENT' ,lab_id='1',procedure_code='ORCP',procedure_type='ord',description='ORGAN RECIPIENT';
INSERT INTO procedure_type SET NAME='ORGANISM' ,lab_id='1',procedure_code='ORG',procedure_type='ord',description='ORGANISM';
INSERT INTO procedure_type SET NAME='ORGANIC ACIDS, QUANT' ,lab_id='1',procedure_code='ORGU',procedure_type='ord',description='ORGANIC ACIDS, QUANT';
INSERT INTO procedure_type SET NAME='HCT, CONDUCTIVITY' ,lab_id='1',procedure_code='ORHT',procedure_type='ord',description='HCT, CONDUCTIVITY';
INSERT INTO procedure_type SET NAME='ORNITHINE' ,lab_id='1',procedure_code='ORN',procedure_type='ord',description='ORNITHINE';
INSERT INTO procedure_type SET NAME='OROTIC ACID, URINE' ,lab_id='1',procedure_code='OROT',procedure_type='ord',description='OROTIC ACID, URINE';
INSERT INTO procedure_type SET NAME='OR SPEC REQ\'D' ,lab_id='1',procedure_code='ORS',procedure_type='ord',description='OR SPEC REQ\'D';
INSERT INTO procedure_type SET NAME='OSMOLALITY, SERUM' ,lab_id='1',procedure_code='OSM',procedure_type='ord',description='OSMOLALITY, SERUM';
INSERT INTO procedure_type SET NAME='OSMOLALITY, STOOL' ,lab_id='1',procedure_code='OSMST',procedure_type='ord',description='OSMOLALITY, STOOL';
INSERT INTO procedure_type SET NAME='OSMOLALITY, URINE' ,lab_id='1',procedure_code='OSMU',procedure_type='ord',description='OSMOLALITY, URINE';
INSERT INTO procedure_type SET NAME='OLIGOSACCHARIDES' ,lab_id='1',procedure_code='OSTLC',procedure_type='ord',description='OLIGOSACCHARIDES';
INSERT INTO procedure_type SET NAME='OTHER CELLS' ,lab_id='1',procedure_code='OTHC',procedure_type='ord',description='OTHER CELLS';
INSERT INTO procedure_type SET NAME='OXALIC ACID, URINE' ,lab_id='1',procedure_code='OXAL',procedure_type='ord',description='OXALIC ACID, URINE';
INSERT INTO procedure_type SET NAME='OXALIC ACID, RANDOM UR' ,lab_id='1',procedure_code='OXAL1',procedure_type='ord',description='OXALIC ACID, RANDOM UR';
INSERT INTO procedure_type SET NAME='OXALATE, PLASMA' ,lab_id='1',procedure_code='OXALP',procedure_type='ord',description='OXALATE, PLASMA';
INSERT INTO procedure_type SET NAME='OXAZEPAM' ,lab_id='1',procedure_code='OXAZ',procedure_type='ord',description='OXAZEPAM';
INSERT INTO procedure_type SET NAME='OXCARBAZEPINE' ,lab_id='1',procedure_code='OXCBP',procedure_type='ord',description='OXCARBAZEPINE';
INSERT INTO procedure_type SET NAME='OXYCODONE SCR/CONF' ,lab_id='1',procedure_code='OXYC',procedure_type='ord',description='OXYCODONE SCR/CONF';
INSERT INTO procedure_type SET NAME='OXYCODONE, CONFIRM' ,lab_id='1',procedure_code='OXYCC',procedure_type='ord',description='OXYCODONE, CONFIRM';
INSERT INTO procedure_type SET NAME='OXYCODONE, UR' ,lab_id='1',procedure_code='OXYCO',procedure_type='ord',description='OXYCODONE, UR';
INSERT INTO procedure_type SET NAME='OXYMORPHONE' ,lab_id='1',procedure_code='OXYM',procedure_type='ord',description='OXYMORPHONE';
INSERT INTO procedure_type SET NAME='OXYPHENISATIN' ,lab_id='1',procedure_code='OXYP',procedure_type='ord',description='OXYPHENISATIN';
INSERT INTO procedure_type SET NAME='OXYCODONE URINE' ,lab_id='1',procedure_code='OXYU',procedure_type='ord',description='OXYCODONE URINE';
INSERT INTO procedure_type SET NAME='PATIENT, 0HR' ,lab_id='1',procedure_code='P0',procedure_type='ord',description='PATIENT, 0HR';
INSERT INTO procedure_type SET NAME='PATIENT, 1HR' ,lab_id='1',procedure_code='P1',procedure_type='ord',description='PATIENT, 1HR';
INSERT INTO procedure_type SET NAME='p19' ,lab_id='1',procedure_code='P19',procedure_type='ord',description='p19';
INSERT INTO procedure_type SET NAME='PARVOVIRUS AB B19 IgG' ,lab_id='1',procedure_code='P19G',procedure_type='ord',description='PARVOVIRUS AB B19 IgG';
INSERT INTO procedure_type SET NAME='P19 I/II' ,lab_id='1',procedure_code='P19I',procedure_type='ord',description='P19 I/II';
INSERT INTO procedure_type SET NAME='PARVOVIRUS AB B19 IgM' ,lab_id='1',procedure_code='P19M',procedure_type='ord',description='PARVOVIRUS AB B19 IgM';
INSERT INTO procedure_type SET NAME='p26' ,lab_id='1',procedure_code='P26',procedure_type='ord',description='p26';
INSERT INTO procedure_type SET NAME='p28' ,lab_id='1',procedure_code='P28',procedure_type='ord',description='p28';
INSERT INTO procedure_type SET NAME='p32' ,lab_id='1',procedure_code='P32',procedure_type='ord',description='p32';
INSERT INTO procedure_type SET NAME='p36' ,lab_id='1',procedure_code='P36',procedure_type='ord',description='p36';
INSERT INTO procedure_type SET NAME='p53' ,lab_id='1',procedure_code='P53',procedure_type='ord',description='p53';
INSERT INTO procedure_type SET NAME='PREALBUMIN' ,lab_id='1',procedure_code='PAB',procedure_type='ord',description='PREALBUMIN';
INSERT INTO procedure_type SET NAME='POST ASA BLEEDING TIME' ,lab_id='1',procedure_code='PABT',procedure_type='ord',description='POST ASA BLEEDING TIME';
INSERT INTO procedure_type SET NAME='PARANEOPLASTIC AB PANE' ,lab_id='1',procedure_code='PAE',procedure_type='ord',description='PARANEOPLASTIC AB PANE';
INSERT INTO procedure_type SET NAME='PARANEO AB PANEL, CSF' ,lab_id='1',procedure_code='PAECSF',procedure_type='ord',description='PARANEO AB PANEL, CSF';
INSERT INTO procedure_type SET NAME='PLT ASSOC IG, INDIR' ,lab_id='1',procedure_code='PAGI',procedure_type='ord',description='PLT ASSOC IG, INDIR';
INSERT INTO procedure_type SET NAME='PAI-1 ACTIVITY' ,lab_id='1',procedure_code='PAI1',procedure_type='ord',description='PAI-1 ACTIVITY';
INSERT INTO procedure_type SET NAME='PAI-1 ANTIGEN' ,lab_id='1',procedure_code='PAI1AG',procedure_type='ord',description='PAI-1 ANTIGEN';
INSERT INTO procedure_type SET NAME='PLT ASSOC IG, DIR' ,lab_id='1',procedure_code='PAIG',procedure_type='ord',description='PLT ASSOC IG, DIR';
INSERT INTO procedure_type SET NAME='PHENYLALANINE' ,lab_id='1',procedure_code='PALA',procedure_type='ord',description='PHENYLALANINE';
INSERT INTO procedure_type SET NAME='PATIENT BLD TYPE' ,lab_id='1',procedure_code='PATBT',procedure_type='ord',description='PATIENT BLD TYPE';
INSERT INTO procedure_type SET NAME='PATIENT CMV' ,lab_id='1',procedure_code='PATCMV',procedure_type='ord',description='PATIENT CMV';
INSERT INTO procedure_type SET NAME='NEUTS' ,lab_id='1',procedure_code='PB',procedure_type='ord',description='NEUTS';
INSERT INTO procedure_type SET NAME='PROMETAPHASE BANDING' ,lab_id='1',procedure_code='PBAN',procedure_type='ord',description='PROMETAPHASE BANDING';
INSERT INTO procedure_type SET NAME='PHENOBARBITAL' ,lab_id='1',procedure_code='PBAR',procedure_type='ord',description='PHENOBARBITAL';
INSERT INTO procedure_type SET NAME='PB DONOR\'S NAME AND HOSP #' ,lab_id='1',procedure_code='PBDON',procedure_type='ord',description='PB DONOR\'S NAME AND HOSP #';
INSERT INTO procedure_type SET NAME='NEUTS' ,lab_id='1',procedure_code='PBN',procedure_type='ord',description='NEUTS';
INSERT INTO procedure_type SET NAME='Stem Cell Identification Number' ,lab_id='1',procedure_code='PBNUM',procedure_type='ord',description='Stem Cell Identification Number';
INSERT INTO procedure_type SET NAME='PORPHOBILINOGEN, QNT' ,lab_id='1',procedure_code='PBQT',procedure_type='ord',description='PORPHOBILINOGEN, QNT';
INSERT INTO procedure_type SET NAME='PORPHOBILINOGEN,RU' ,lab_id='1',procedure_code='PBQTR',procedure_type='ord',description='PORPHOBILINOGEN,RU';
INSERT INTO procedure_type SET NAME='NEUTS' ,lab_id='1',procedure_code='PBSP',procedure_type='ord',description='NEUTS';
INSERT INTO procedure_type SET NAME='STEM CELL SOURCE' ,lab_id='1',procedure_code='PBSRC',procedure_type='ord',description='STEM CELL SOURCE';
INSERT INTO procedure_type SET NAME='PRE ASA BLEEDING TIME' ,lab_id='1',procedure_code='PBT',procedure_type='ord',description='PRE ASA BLEEDING TIME';
INSERT INTO procedure_type SET NAME='PBTV' ,lab_id='1',procedure_code='PBTV',procedure_type='ord',description='PBTV';
INSERT INTO procedure_type SET NAME='LEAD, URINE' ,lab_id='1',procedure_code='PBU',procedure_type='ord',description='LEAD, URINE';
INSERT INTO procedure_type SET NAME='LEAD, RANDOM URINE' ,lab_id='1',procedure_code='PBU1',procedure_type='ord',description='LEAD, RANDOM URINE';
INSERT INTO procedure_type SET NAME='CALCIUM, IONIZED, POST FILTER' ,lab_id='1',procedure_code='PCAI',procedure_type='ord',description='CALCIUM, IONIZED, POST FILTER';
INSERT INTO procedure_type SET NAME='PCD34 STEM CELLS %' ,lab_id='1',procedure_code='PCD34',procedure_type='ord',description='PCD34 STEM CELLS %';
INSERT INTO procedure_type SET NAME='PCD34 CELL DOSE' ,lab_id='1',procedure_code='PCD34D',procedure_type='ord',description='PCD34 CELL DOSE';
INSERT INTO procedure_type SET NAME='PCD3 %, Total' ,lab_id='1',procedure_code='PCD3T',procedure_type='ord',description='PCD3 %, Total';
INSERT INTO procedure_type SET NAME='PCD3 % NEG FRAC, TOTAL' ,lab_id='1',procedure_code='PCD3TN',procedure_type='ord',description='PCD3 % NEG FRAC, TOTAL';
INSERT INTO procedure_type SET NAME='CHOLINESTERASE' ,lab_id='1',procedure_code='PCE',procedure_type='ord',description='CHOLINESTERASE';
INSERT INTO procedure_type SET NAME='ARRAY CGH, FAMILY FOLLOW-UP' ,lab_id='1',procedure_code='PCGH',procedure_type='ord',description='ARRAY CGH, FAMILY FOLLOW-UP';
INSERT INTO procedure_type SET NAME='ARRAY PARENT Int (MD):' ,lab_id='1',procedure_code='PCGHPF',procedure_type='ord',description='ARRAY PARENT Int (MD):';
INSERT INTO procedure_type SET NAME='CHOLINESTERASE PLASMA' ,lab_id='1',procedure_code='PCHE',procedure_type='ord',description='CHOLINESTERASE PLASMA';
INSERT INTO procedure_type SET NAME='PCO2' ,lab_id='1',procedure_code='PCO2',procedure_type='ord',description='PCO2';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 0 HR' ,lab_id='1',procedure_code='PCPT0',procedure_type='ord',description='PATIENT+NML, 0 HR';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 1 HR' ,lab_id='1',procedure_code='PCPT1',procedure_type='ord',description='PATIENT+NML, 1 HR';
INSERT INTO procedure_type SET NAME='PATIENT+NML, 2HR' ,lab_id='1',procedure_code='PCPT2',procedure_type='ord',description='PATIENT+NML, 2HR';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT1',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT2',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT3',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT4',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT5',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT6',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT7',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='% DECREASE' ,lab_id='1',procedure_code='PCT8',procedure_type='ord',description='% DECREASE';
INSERT INTO procedure_type SET NAME='DHEA-S, PEDIATRIC' ,lab_id='1',procedure_code='PDHES',procedure_type='ord',description='DHEA-S, PEDIATRIC';
INSERT INTO procedure_type SET NAME='DONOR VS RECIPIENT' ,lab_id='1',procedure_code='PDVR',procedure_type='ord',description='DONOR VS RECIPIENT';
INSERT INTO procedure_type SET NAME='POS END EXPIRA PRESS' ,lab_id='1',procedure_code='PEEP',procedure_type='ord',description='POS END EXPIRA PRESS';
INSERT INTO procedure_type SET NAME='PENICILLIN G' ,lab_id='1',procedure_code='PENOXA',procedure_type='ord',description='PENICILLIN G';
INSERT INTO procedure_type SET NAME='PENTOBARBITAL' ,lab_id='1',procedure_code='PENT',procedure_type='ord',description='PENTOBARBITAL';
INSERT INTO procedure_type SET NAME='PROT ELECTROPHORESIS' ,lab_id='1',procedure_code='PEP',procedure_type='ord',description='PROT ELECTROPHORESIS';
INSERT INTO procedure_type SET NAME='PERCENT POSITIVE CELLS' ,lab_id='1',procedure_code='PERCNT',procedure_type='ord',description='PERCENT POSITIVE CELLS';
INSERT INTO procedure_type SET NAME='PEROXIDASE STAIN' ,lab_id='1',procedure_code='PERX',procedure_type='ord',description='PEROXIDASE STAIN';
INSERT INTO procedure_type SET NAME='PHOS ETHANOLAMINE' ,lab_id='1',procedure_code='PETH',procedure_type='ord',description='PHOS ETHANOLAMINE';
INSERT INTO procedure_type SET NAME='PROT ELECTROPHOR, UR' ,lab_id='1',procedure_code='PEUR',procedure_type='ord',description='PROT ELECTROPHOR, UR';
INSERT INTO procedure_type SET NAME='PARAINFLUENZA 1 CFAB' ,lab_id='1',procedure_code='PFL1A',procedure_type='ord',description='PARAINFLUENZA 1 CFAB';
INSERT INTO procedure_type SET NAME='PARAINFLUENZA 2 CFAB' ,lab_id='1',procedure_code='PFL2',procedure_type='ord',description='PARAINFLUENZA 2 CFAB';
INSERT INTO procedure_type SET NAME='PARAINFLUENZA 3 CFAB' ,lab_id='1',procedure_code='PFL3',procedure_type='ord',description='PARAINFLUENZA 3 CFAB';
INSERT INTO procedure_type SET NAME='PSA, % FREE' ,lab_id='1',procedure_code='PFPSA',procedure_type='ord',description='PSA, % FREE';
INSERT INTO procedure_type SET NAME='PFA SELF TEST' ,lab_id='1',procedure_code='PFSELF',procedure_type='ord',description='PFA SELF TEST';
INSERT INTO procedure_type SET NAME='FSH' ,lab_id='1',procedure_code='PFSH',procedure_type='ord',description='FSH';
INSERT INTO procedure_type SET NAME='FREE T3, PEDIATRIC' ,lab_id='1',procedure_code='PFT3',procedure_type='ord',description='FREE T3, PEDIATRIC';
INSERT INTO procedure_type SET NAME='PLT. GLYCOPROTEIN AB' ,lab_id='1',procedure_code='PGAB',procedure_type='ord',description='PLT. GLYCOPROTEIN AB';
INSERT INTO procedure_type SET NAME='PG (BY AGGLUTINAT.)' ,lab_id='1',procedure_code='PGAG',procedure_type='ord',description='PG (BY AGGLUTINAT.)';
INSERT INTO procedure_type SET NAME='GROWTH HORMONE, PEDS' ,lab_id='1',procedure_code='PGH',procedure_type='ord',description='GROWTH HORMONE, PEDS';
INSERT INTO procedure_type SET NAME='PH' ,lab_id='1',procedure_code='PH',procedure_type='ord',description='PH';
INSERT INTO procedure_type SET NAME='PH' ,lab_id='1',procedure_code='PH37',procedure_type='ord',description='PH';
INSERT INTO procedure_type SET NAME='PHA STIMULATION' ,lab_id='1',procedure_code='PHA',procedure_type='ord',description='PHA STIMULATION';
INSERT INTO procedure_type SET NAME='PHA STIMULATION' ,lab_id='1',procedure_code='PHAS',procedure_type='ord',description='PHA STIMULATION';
INSERT INTO procedure_type SET NAME='pH, BODY FLUID' ,lab_id='1',procedure_code='PHB',procedure_type='ord',description='pH, BODY FLUID';
INSERT INTO procedure_type SET NAME='pH, Body temp' ,lab_id='1',procedure_code='PHCOR',procedure_type='ord',description='pH, Body temp';
INSERT INTO procedure_type SET NAME='PHENYLALANINE' ,lab_id='1',procedure_code='PHENYL',procedure_type='ord',description='PHENYLALANINE';
INSERT INTO procedure_type SET NAME='PH, STOOL' ,lab_id='1',procedure_code='PHF',procedure_type='ord',description='PH, STOOL';
INSERT INTO procedure_type SET NAME='PHENYTOIN' ,lab_id='1',procedure_code='PHNY',procedure_type='ord',description='PHENYTOIN';
INSERT INTO procedure_type SET NAME='PHENYTOIN, FREE' ,lab_id='1',procedure_code='PHNYF',procedure_type='ord',description='PHENYTOIN, FREE';
INSERT INTO procedure_type SET NAME='PHOSPHORUS' ,lab_id='1',procedure_code='PHO',procedure_type='ord',description='PHOSPHORUS';
INSERT INTO procedure_type SET NAME='pH QC for MICRO (Z)' ,lab_id='1',procedure_code='PHQC',procedure_type='ord',description='pH QC for MICRO (Z)';
INSERT INTO procedure_type SET NAME='pH, URINE' ,lab_id='1',procedure_code='PHU',procedure_type='ord',description='pH, URINE';
INSERT INTO procedure_type SET NAME='PH' ,lab_id='1',procedure_code='PHUA',procedure_type='ord',description='PH';
INSERT INTO procedure_type SET NAME='IGF-1, PEDIATRICS' ,lab_id='1',procedure_code='PIGF1',procedure_type='ord',description='IGF-1, PEDIATRICS';
INSERT INTO procedure_type SET NAME='POS INSPIRA PRESS' ,lab_id='1',procedure_code='PINP',procedure_type='ord',description='POS INSPIRA PRESS';
INSERT INTO procedure_type SET NAME='PIPERACILLIN' ,lab_id='1',procedure_code='PIP',procedure_type='ord',description='PIPERACILLIN';
INSERT INTO procedure_type SET NAME='PIPER TAZOBACTAM' ,lab_id='1',procedure_code='PIPTAZ',procedure_type='ord',description='PIPER TAZOBACTAM';
INSERT INTO procedure_type SET NAME='PYRUVATE KINASE, RBC' ,lab_id='1',procedure_code='PKIN',procedure_type='ord',description='PYRUVATE KINASE, RBC';
INSERT INTO procedure_type SET NAME='PKU SCREEN' ,lab_id='1',procedure_code='PKUX',procedure_type='ord',description='PKU SCREEN';
INSERT INTO procedure_type SET NAME='PL-12 AUTOANTIBODIES' ,lab_id='1',procedure_code='PL12',procedure_type='ord',description='PL-12 AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='PL-7 AUTOANTIBODIES' ,lab_id='1',procedure_code='PL7',procedure_type='ord',description='PL-7 AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='LH, ULTRASENSITIVE' ,lab_id='1',procedure_code='PLH',procedure_type='ord',description='LH, ULTRASENSITIVE';
INSERT INTO procedure_type SET NAME='PHOSPHOLIPIDS' ,lab_id='1',procedure_code='PLIP',procedure_type='ord',description='PHOSPHOLIPIDS';
INSERT INTO procedure_type SET NAME='PLASMINOGEN ACTIVITY' ,lab_id='1',procedure_code='PLMA',procedure_type='ord',description='PLASMINOGEN ACTIVITY';
INSERT INTO procedure_type SET NAME='PLASMINOGEN ANTIGEN' ,lab_id='1',procedure_code='PLMI',procedure_type='ord',description='PLASMINOGEN ANTIGEN';
INSERT INTO procedure_type SET NAME='PLREM' ,lab_id='1',procedure_code='PLREM',procedure_type='ord',description='PLREM';
INSERT INTO procedure_type SET NAME='PLATELET SIZING' ,lab_id='1',procedure_code='PLSZ',procedure_type='ord',description='PLATELET SIZING';
INSERT INTO procedure_type SET NAME='PLATELETS' ,lab_id='1',procedure_code='PLT',procedure_type='ord',description='PLATELETS';
INSERT INTO procedure_type SET NAME='DO NOT USE' ,lab_id='1',procedure_code='PLTD',procedure_type='ord',description='DO NOT USE';
INSERT INTO procedure_type SET NAME='PLATELET CNT, PHASE' ,lab_id='1',procedure_code='PLTM',procedure_type='ord',description='PLATELET CNT, PHASE';
INSERT INTO procedure_type SET NAME='MIXED LYMPH CULT, PI' ,lab_id='1',procedure_code='PMLC',procedure_type='ord',description='MIXED LYMPH CULT, PI';
INSERT INTO procedure_type SET NAME='PML-RARA PCR QUANT.' ,lab_id='1',procedure_code='PMLQNT',procedure_type='ord',description='PML-RARA PCR QUANT.';
INSERT INTO procedure_type SET NAME='PML-RARA BY PCR' ,lab_id='1',procedure_code='PMLR',procedure_type='ord',description='PML-RARA BY PCR';
INSERT INTO procedure_type SET NAME='NEUTS' ,lab_id='1',procedure_code='PMNC',procedure_type='ord',description='NEUTS';
INSERT INTO procedure_type SET NAME='MNC PER KG' ,lab_id='1',procedure_code='PMNCKG',procedure_type='ord',description='MNC PER KG';
INSERT INTO procedure_type SET NAME='PMNS' ,lab_id='1',procedure_code='PMNS',procedure_type='ord',description='PMNS';
INSERT INTO procedure_type SET NAME='PM SCL ANTIBODY' ,lab_id='1',procedure_code='PMSCL',procedure_type='ord',description='PM SCL ANTIBODY';
INSERT INTO procedure_type SET NAME='PIPET NUMBER' ,lab_id='1',procedure_code='PN',procedure_type='ord',description='PIPET NUMBER';
INSERT INTO procedure_type SET NAME='PNH CELL MARKERS' ,lab_id='1',procedure_code='PNHM',procedure_type='ord',description='PNH CELL MARKERS';
INSERT INTO procedure_type SET NAME='PHENOLPHTHALEIN' ,lab_id='1',procedure_code='PNOL',procedure_type='ord',description='PHENOLPHTHALEIN';
INSERT INTO procedure_type SET NAME='PLATELET READY' ,lab_id='1',procedure_code='PNUR',procedure_type='ord',description='PLATELET READY';
INSERT INTO procedure_type SET NAME='PO2' ,lab_id='1',procedure_code='PO2',procedure_type='ord',description='PO2';
INSERT INTO procedure_type SET NAME='PHOSPHORUS' ,lab_id='1',procedure_code='PO4',procedure_type='ord',description='PHOSPHORUS';
INSERT INTO procedure_type SET NAME='PHOSPHORUS, URINE' ,lab_id='1',procedure_code='PO4UR',procedure_type='ord',description='PHOSPHORUS, URINE';
INSERT INTO procedure_type SET NAME='POSTTRANSF DAT' ,lab_id='1',procedure_code='PODAT',procedure_type='ord',description='POSTTRANSF DAT';
INSERT INTO procedure_type SET NAME='POSTTRANSF DAT IGG' ,lab_id='1',procedure_code='POIG',procedure_type='ord',description='POSTTRANSF DAT IGG';
INSERT INTO procedure_type SET NAME='POLIOVIRUS TYPE 2' ,lab_id='1',procedure_code='POL2',procedure_type='ord',description='POLIOVIRUS TYPE 2';
INSERT INTO procedure_type SET NAME='POLIOVIRUS TYPE 3' ,lab_id='1',procedure_code='POL3',procedure_type='ord',description='POLIOVIRUS TYPE 3';
INSERT INTO procedure_type SET NAME='POLIOVIRUS TYPE 1' ,lab_id='1',procedure_code='POLI',procedure_type='ord',description='POLIOVIRUS TYPE 1';
INSERT INTO procedure_type SET NAME='POLM' ,lab_id='1',procedure_code='POLM',procedure_type='ord',description='POLM';
INSERT INTO procedure_type SET NAME='CF POLY T' ,lab_id='1',procedure_code='POLT',procedure_type='ord',description='CF POLY T';
INSERT INTO procedure_type SET NAME='PORCINE CONT, 0HR' ,lab_id='1',procedure_code='PORC0',procedure_type='ord',description='PORCINE CONT, 0HR';
INSERT INTO procedure_type SET NAME='PORCINE CONT, 2HR' ,lab_id='1',procedure_code='PORC2',procedure_type='ord',description='PORCINE CONT, 2HR';
INSERT INTO procedure_type SET NAME='PORC CONT + DEF, 0HR' ,lab_id='1',procedure_code='PORCD0',procedure_type='ord',description='PORC CONT + DEF, 0HR';
INSERT INTO procedure_type SET NAME='PORC CONT + DEF, 2HR' ,lab_id='1',procedure_code='PORCD2',procedure_type='ord',description='PORC CONT + DEF, 2HR';
INSERT INTO procedure_type SET NAME='PORC CONT + PAT, 0HR' ,lab_id='1',procedure_code='PORCP0',procedure_type='ord',description='PORC CONT + PAT, 0HR';
INSERT INTO procedure_type SET NAME='PORC CONT + PAT, 2HR' ,lab_id='1',procedure_code='PORCP2',procedure_type='ord',description='PORC CONT + PAT, 2HR';
INSERT INTO procedure_type SET NAME='PATIENT, 0HR' ,lab_id='1',procedure_code='PORP0',procedure_type='ord',description='PATIENT, 0HR';
INSERT INTO procedure_type SET NAME='PATIENT, 2HR' ,lab_id='1',procedure_code='PORP2',procedure_type='ord',description='PATIENT, 2HR';
INSERT INTO procedure_type SET NAME='TOTAL PORPHYRIN' ,lab_id='1',procedure_code='PORPU',procedure_type='ord',description='TOTAL PORPHYRIN';
INSERT INTO procedure_type SET NAME='TOTAL PORPHYRINS' ,lab_id='1',procedure_code='PORPUR',procedure_type='ord',description='TOTAL PORPHYRINS';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='PORSI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='POSACONAZOLE' ,lab_id='1',procedure_code='POSA',procedure_type='ord',description='POSACONAZOLE';
INSERT INTO procedure_type SET NAME='POSACONAZOLE LEVEL' ,lab_id='1',procedure_code='POSAC',procedure_type='ord',description='POSACONAZOLE LEVEL';
INSERT INTO procedure_type SET NAME='POSTTRANSF DAT-C\'' ,lab_id='1',procedure_code='POSTC',procedure_type='ord',description='POSTTRANSF DAT-C\'';
INSERT INTO procedure_type SET NAME='p24' ,lab_id='1',procedure_code='PP24',procedure_type='ord',description='p24';
INSERT INTO procedure_type SET NAME='PANCREATIC POLYPEP' ,lab_id='1',procedure_code='PPEP',procedure_type='ord',description='PANCREATIC POLYPEP';
INSERT INTO procedure_type SET NAME='INTERPRETATION:' ,lab_id='1',procedure_code='PPFXI',procedure_type='ord',description='INTERPRETATION:';
INSERT INTO procedure_type SET NAME='PLTPopS' ,lab_id='1',procedure_code='PPOPS',procedure_type='ord',description='PLTPopS';
INSERT INTO procedure_type SET NAME='PARAPROTEIN CONCENTRAT.' ,lab_id='1',procedure_code='PPSK',procedure_type='ord',description='PARAPROTEIN CONCENTRAT.';
INSERT INTO procedure_type SET NAME='PATIENT, 0 HR' ,lab_id='1',procedure_code='PPT0',procedure_type='ord',description='PATIENT, 0 HR';
INSERT INTO procedure_type SET NAME='PATIENT, 1 HR' ,lab_id='1',procedure_code='PPT1',procedure_type='ord',description='PATIENT, 1 HR';
INSERT INTO procedure_type SET NAME='PATIENT, 0 HR' ,lab_id='1',procedure_code='PPTT0',procedure_type='ord',description='PATIENT, 0 HR';
INSERT INTO procedure_type SET NAME='PATIENT, 1 HR' ,lab_id='1',procedure_code='PPTT1',procedure_type='ord',description='PATIENT, 1 HR';
INSERT INTO procedure_type SET NAME='PARAPROTEIN %' ,lab_id='1',procedure_code='PPU',procedure_type='ord',description='PARAPROTEIN %';
INSERT INTO procedure_type SET NAME='PROTEIN C ACTIVITY' ,lab_id='1',procedure_code='PRC',procedure_type='ord',description='PROTEIN C ACTIVITY';
INSERT INTO procedure_type SET NAME='PROTEIN C ANTIGEN' ,lab_id='1',procedure_code='PRCI',procedure_type='ord',description='PROTEIN C ANTIGEN';
INSERT INTO procedure_type SET NAME='PROTEIN C INTERP (MD)' ,lab_id='1',procedure_code='PRCPF',procedure_type='ord',description='PROTEIN C INTERP (MD)';
INSERT INTO procedure_type SET NAME='PRETRANSF DAT' ,lab_id='1',procedure_code='PRDAT',procedure_type='ord',description='PRETRANSF DAT';
INSERT INTO procedure_type SET NAME='Pre Proc CD34 %' ,lab_id='1',procedure_code='PRE34',procedure_type='ord',description='Pre Proc CD34 %';
INSERT INTO procedure_type SET NAME='PRETRANSF DAT-C\'' ,lab_id='1',procedure_code='PREC',procedure_type='ord',description='PRETRANSF DAT-C\'';
INSERT INTO procedure_type SET NAME='Pre Proc CD3 %, Total' ,lab_id='1',procedure_code='PRECD3',procedure_type='ord',description='Pre Proc CD3 %, Total';
INSERT INTO procedure_type SET NAME='PRECM' ,lab_id='1',procedure_code='PRECM',procedure_type='ord',description='PRECM';
INSERT INTO procedure_type SET NAME='PRICE' ,lab_id='1',procedure_code='PRIC',procedure_type='ord',description='PRICE';
INSERT INTO procedure_type SET NAME='PRETRANSF DAT IGG' ,lab_id='1',procedure_code='PRIG',procedure_type='ord',description='PRETRANSF DAT IGG';
INSERT INTO procedure_type SET NAME='PRIMIDONE' ,lab_id='1',procedure_code='PRIMI',procedure_type='ord',description='PRIMIDONE';
INSERT INTO procedure_type SET NAME='PROINSULIN' ,lab_id='1',procedure_code='PRINS',procedure_type='ord',description='PROINSULIN';
INSERT INTO procedure_type SET NAME='PROMYELOCYTES' ,lab_id='1',procedure_code='PRMYA',procedure_type='ord',description='PROMYELOCYTES';
INSERT INTO procedure_type SET NAME='PROTHROMBIN FRAG 1+2' ,lab_id='1',procedure_code='PROF',procedure_type='ord',description='PROTHROMBIN FRAG 1+2';
INSERT INTO procedure_type SET NAME='PROGESTERONE' ,lab_id='1',procedure_code='PROG',procedure_type='ord',description='PROGESTERONE';
INSERT INTO procedure_type SET NAME='PROINSULIN' ,lab_id='1',procedure_code='PROINS',procedure_type='ord',description='PROINSULIN';
INSERT INTO procedure_type SET NAME='PROLACTIN' ,lab_id='1',procedure_code='PROL',procedure_type='ord',description='PROLACTIN';
INSERT INTO procedure_type SET NAME='PROLINE' ,lab_id='1',procedure_code='PROLN',procedure_type='ord',description='PROLINE';
INSERT INTO procedure_type SET NAME='PROCAINAMIDE' ,lab_id='1',procedure_code='PRON',procedure_type='ord',description='PROCAINAMIDE';
INSERT INTO procedure_type SET NAME='PROPRANOLOL' ,lab_id='1',procedure_code='PROP',procedure_type='ord',description='PROPRANOLOL';
INSERT INTO procedure_type SET NAME='PROTOPORPHYRIN' ,lab_id='1',procedure_code='PROR',procedure_type='ord',description='PROTOPORPHYRIN';
INSERT INTO procedure_type SET NAME='PT' ,lab_id='1',procedure_code='PROTH',procedure_type='ord',description='PT';
INSERT INTO procedure_type SET NAME='PROSTATE SPECIFIC ANTIGEN' ,lab_id='1',procedure_code='PRSA',procedure_type='ord',description='PROSTATE SPECIFIC ANTIGEN';
INSERT INTO procedure_type SET NAME='PROTEIN S, FREE' ,lab_id='1',procedure_code='PRSI',procedure_type='ord',description='PROTEIN S, FREE';
INSERT INTO procedure_type SET NAME='FREE PROT S INTRP (MD)' ,lab_id='1',procedure_code='PRSIPF',procedure_type='ord',description='FREE PROT S INTRP (MD)';
INSERT INTO procedure_type SET NAME='PROTEIN' ,lab_id='1',procedure_code='PRTE',procedure_type='ord',description='PROTEIN';
INSERT INTO procedure_type SET NAME='PREGNANETRIOL,URINE' ,lab_id='1',procedure_code='PRTL1',procedure_type='ord',description='PREGNANETRIOL,URINE';
INSERT INTO procedure_type SET NAME='PREGNANETRIOL, UR' ,lab_id='1',procedure_code='PRTL2',procedure_type='ord',description='PREGNANETRIOL, UR';
INSERT INTO procedure_type SET NAME='PROTEIN' ,lab_id='1',procedure_code='PRUA',procedure_type='ord',description='PROTEIN';
INSERT INTO procedure_type SET NAME='PROT TOTAL PER DAY, UR' ,lab_id='1',procedure_code='PRUT',procedure_type='ord',description='PROT TOTAL PER DAY, UR';
INSERT INTO procedure_type SET NAME='RECIPIENT VS DONOR' ,lab_id='1',procedure_code='PRVD',procedure_type='ord',description='RECIPIENT VS DONOR';
INSERT INTO procedure_type SET NAME='PRE PROCESSING VOLUME' ,lab_id='1',procedure_code='PRVOL',procedure_type='ord',description='PRE PROCESSING VOLUME';
INSERT INTO procedure_type SET NAME='PS341                                 (MD)' ,lab_id='1',procedure_code='PS341',procedure_type='ord',description='PS341                                 (MD)';
INSERT INTO procedure_type SET NAME='PROTEIN S ACTIVITY' ,lab_id='1',procedure_code='PSACT',procedure_type='ord',description='PROTEIN S ACTIVITY';
INSERT INTO procedure_type SET NAME='ADDITIONAL INFO' ,lab_id='1',procedure_code='PSADI',procedure_type='ord',description='ADDITIONAL INFO';
INSERT INTO procedure_type SET NAME='PSA, % FREE' ,lab_id='1',procedure_code='PSAFP',procedure_type='ord',description='PSA, % FREE';
INSERT INTO procedure_type SET NAME='PSA, FREE' ,lab_id='1',procedure_code='PSAFR',procedure_type='ord',description='PSA, FREE';
INSERT INTO procedure_type SET NAME='PROSTATE CANCER RISK' ,lab_id='1',procedure_code='PSARSK',procedure_type='ord',description='PROSTATE CANCER RISK';
INSERT INTO procedure_type SET NAME='PSA ANNUAL SCREENING' ,lab_id='1',procedure_code='PSAS',procedure_type='ord',description='PSA ANNUAL SCREENING';
INSERT INTO procedure_type SET NAME='PSA, TOTAL' ,lab_id='1',procedure_code='PSATOT',procedure_type='ord',description='PSA, TOTAL';
INSERT INTO procedure_type SET NAME='PSA, TOTAL ULTRASENS.' ,lab_id='1',procedure_code='PSAU',procedure_type='ord',description='PSA, TOTAL ULTRASENS.';
INSERT INTO procedure_type SET NAME='POSACONAZOLE' ,lab_id='1',procedure_code='PSCA',procedure_type='ord',description='POSACONAZOLE';
INSERT INTO procedure_type SET NAME='MONITOR CO2 VALUE' ,lab_id='1',procedure_code='PSCO2',procedure_type='ord',description='MONITOR CO2 VALUE';
INSERT INTO procedure_type SET NAME='NL PSCO2 21-24 HRS' ,lab_id='1',procedure_code='PSCO2F',procedure_type='ord',description='NL PSCO2 21-24 HRS';
INSERT INTO procedure_type SET NAME='MONITOR CO2 VALUE' ,lab_id='1',procedure_code='PSCO3',procedure_type='ord',description='MONITOR CO2 VALUE';
INSERT INTO procedure_type SET NAME='PHOSPHATIDYL SERINE' ,lab_id='1',procedure_code='PSER',procedure_type='ord',description='PHOSPHATIDYL SERINE';
INSERT INTO procedure_type SET NAME='CHECK PLT SMR VS CNT' ,lab_id='1',procedure_code='PSMR',procedure_type='ord',description='CHECK PLT SMR VS CNT';
INSERT INTO procedure_type SET NAME='POST MNC COUNT' ,lab_id='1',procedure_code='PSTMNC',procedure_type='ord',description='POST MNC COUNT';
INSERT INTO procedure_type SET NAME='PCTL STERILITY CHECK' ,lab_id='1',procedure_code='PSTR',procedure_type='ord',description='PCTL STERILITY CHECK';
INSERT INTO procedure_type SET NAME='PROTAMINE SULFATE' ,lab_id='1',procedure_code='PSUL',procedure_type='ord',description='PROTAMINE SULFATE';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 1' ,lab_id='1',procedure_code='PT1',procedure_type='ord',description='Pneumo IgG Type 1';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 12' ,lab_id='1',procedure_code='PT12',procedure_type='ord',description='Pneumo IgG Type 12';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 14' ,lab_id='1',procedure_code='PT14',procedure_type='ord',description='Pneumo IgG Type 14';
INSERT INTO procedure_type SET NAME='Pneumo IgG 17 (17F)' ,lab_id='1',procedure_code='PT17',procedure_type='ord',description='Pneumo IgG 17 (17F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 19(19F)' ,lab_id='1',procedure_code='PT19',procedure_type='ord',description='Pneumo IgG 19(19F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 2 (2)' ,lab_id='1',procedure_code='PT2',procedure_type='ord',description='Pneumo IgG 2 (2)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 20 (20)' ,lab_id='1',procedure_code='PT20',procedure_type='ord',description='Pneumo IgG 20 (20)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 22 (22F)' ,lab_id='1',procedure_code='PT22',procedure_type='ord',description='Pneumo IgG 22 (22F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 23(23F)' ,lab_id='1',procedure_code='PT23',procedure_type='ord',description='Pneumo IgG 23(23F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 26 (6B)' ,lab_id='1',procedure_code='PT26',procedure_type='ord',description='Pneumo IgG 26 (6B)';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 3' ,lab_id='1',procedure_code='PT3',procedure_type='ord',description='Pneumo IgG Type 3';
INSERT INTO procedure_type SET NAME='Pneumo IgG 34 (10A)' ,lab_id='1',procedure_code='PT34',procedure_type='ord',description='Pneumo IgG 34 (10A)';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 4' ,lab_id='1',procedure_code='PT4',procedure_type='ord',description='Pneumo IgG Type 4';
INSERT INTO procedure_type SET NAME='Pneumo IgG 43 (11A)' ,lab_id='1',procedure_code='PT43',procedure_type='ord',description='Pneumo IgG 43 (11A)';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 5' ,lab_id='1',procedure_code='PT5',procedure_type='ord',description='Pneumo IgG Type 5';
INSERT INTO procedure_type SET NAME='Pneumo IgG 51(7F)' ,lab_id='1',procedure_code='PT51',procedure_type='ord',description='Pneumo IgG 51(7F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 54 (15B)' ,lab_id='1',procedure_code='PT54',procedure_type='ord',description='Pneumo IgG 54 (15B)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 56(18C)' ,lab_id='1',procedure_code='PT56',procedure_type='ord',description='Pneumo IgG 56(18C)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 57 (19A)' ,lab_id='1',procedure_code='PT57',procedure_type='ord',description='Pneumo IgG 57 (19A)';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 68 (9V)' ,lab_id='1',procedure_code='PT68',procedure_type='ord',description='Pneumo IgG Type 68 (9V)';
INSERT INTO procedure_type SET NAME='Pneumo IgG 70 (33F)' ,lab_id='1',procedure_code='PT70',procedure_type='ord',description='Pneumo IgG 70 (33F)';
INSERT INTO procedure_type SET NAME='Pneumo IgG Type 8' ,lab_id='1',procedure_code='PT8',procedure_type='ord',description='Pneumo IgG Type 8';
INSERT INTO procedure_type SET NAME='Pneumo IgG Typ 9(9N)' ,lab_id='1',procedure_code='PT9',procedure_type='ord',description='Pneumo IgG Typ 9(9N)';
INSERT INTO procedure_type SET NAME='PATIENT AGE' ,lab_id='1',procedure_code='PTA',procedure_type='ord',description='PATIENT AGE';
INSERT INTO procedure_type SET NAME='PATIENT ANTIBODY SCREEN' ,lab_id='1',procedure_code='PTABS',procedure_type='ord',description='PATIENT ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='PLATELET AG TYPING' ,lab_id='1',procedure_code='PTAG',procedure_type='ord',description='PLATELET AG TYPING';
INSERT INTO procedure_type SET NAME='PROTHROMBIN CONSUMPT' ,lab_id='1',procedure_code='PTCS',procedure_type='ord',description='PROTHROMBIN CONSUMPT';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, TOTAL' ,lab_id='1',procedure_code='PTES',procedure_type='ord',description='TESTOSTERONE, TOTAL';
INSERT INTO procedure_type SET NAME='PTH, BODY FLUID' ,lab_id='1',procedure_code='PTHBF',procedure_type='ord',description='PTH, BODY FLUID';
INSERT INTO procedure_type SET NAME='PARATHORMONE' ,lab_id='1',procedure_code='PTHI',procedure_type='ord',description='PARATHORMONE';
INSERT INTO procedure_type SET NAME='PTH SPECIMEN ID' ,lab_id='1',procedure_code='PTHID',procedure_type='ord',description='PTH SPECIMEN ID';
INSERT INTO procedure_type SET NAME='IOPTH POST' ,lab_id='1',procedure_code='PTHPOS',procedure_type='ord',description='IOPTH POST';
INSERT INTO procedure_type SET NAME='IOPTH PRE (BASELINE)' ,lab_id='1',procedure_code='PTHPRE',procedure_type='ord',description='IOPTH PRE (BASELINE)';
INSERT INTO procedure_type SET NAME='PTH RELATED PROTEIN' ,lab_id='1',procedure_code='PTHR',procedure_type='ord',description='PTH RELATED PROTEIN';
INSERT INTO procedure_type SET NAME='PTH RELATED PEPTIDE' ,lab_id='1',procedure_code='PTHRP',procedure_type='ord',description='PTH RELATED PEPTIDE';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='PTI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='POST TRANSF PERPURA' ,lab_id='1',procedure_code='PTP',procedure_type='ord',description='POST TRANSF PERPURA';
INSERT INTO procedure_type SET NAME='PENTACARBOXYLPORPHYRINS' ,lab_id='1',procedure_code='PTPE',procedure_type='ord',description='PENTACARBOXYLPORPHYRINS';
INSERT INTO procedure_type SET NAME='PENTAPORPHYRIN' ,lab_id='1',procedure_code='PTPU',procedure_type='ord',description='PENTAPORPHYRIN';
INSERT INTO procedure_type SET NAME='PENTAPORPHYRIN' ,lab_id='1',procedure_code='PTPUR',procedure_type='ord',description='PENTAPORPHYRIN';
INSERT INTO procedure_type SET NAME='PARTIAL THROMBOPLASTIN' ,lab_id='1',procedure_code='PTT',procedure_type='ord',description='PARTIAL THROMBOPLASTIN';
INSERT INTO procedure_type SET NAME='INTERPRETATION' ,lab_id='1',procedure_code='PTTI',procedure_type='ord',description='INTERPRETATION';
INSERT INTO procedure_type SET NAME='PROTHROMBIN MUTATION' ,lab_id='1',procedure_code='PTTR',procedure_type='ord',description='PROTHROMBIN MUTATION';
INSERT INTO procedure_type SET NAME='PHENYLALANINE TYROSINE RATIO' ,lab_id='1',procedure_code='PTYR',procedure_type='ord',description='PHENYLALANINE TYROSINE RATIO';
INSERT INTO procedure_type SET NAME='PHOS PER DAY, UR' ,lab_id='1',procedure_code='PUD',procedure_type='ord',description='PHOS PER DAY, UR';
INSERT INTO procedure_type SET NAME='PRADER-WILLI/ANGELMAN' ,lab_id='1',procedure_code='PWA',procedure_type='ord',description='PRADER-WILLI/ANGELMAN';
INSERT INTO procedure_type SET NAME='POKEWEED STIMULATION' ,lab_id='1',procedure_code='PWM',procedure_type='ord',description='POKEWEED STIMULATION';
INSERT INTO procedure_type SET NAME='PLATELET AB STUDIES' ,lab_id='1',procedure_code='PXMS',procedure_type='ord',description='PLATELET AB STUDIES';
INSERT INTO procedure_type SET NAME='PYRAZINAMIDE' ,lab_id='1',procedure_code='PYRAZ',procedure_type='ord',description='PYRAZINAMIDE';
INSERT INTO procedure_type SET NAME='PYRUVATE' ,lab_id='1',procedure_code='PYRC',procedure_type='ord',description='PYRUVATE';
INSERT INTO procedure_type SET NAME='PYRUVATE, CSF' ,lab_id='1',procedure_code='PYRUC',procedure_type='ord',description='PYRUVATE, CSF';
INSERT INTO procedure_type SET NAME='Q FEV IGG I SCREEN' ,lab_id='1',procedure_code='QFG1',procedure_type='ord',description='Q FEV IGG I SCREEN';
INSERT INTO procedure_type SET NAME='Q FEV IGG II SCREEN' ,lab_id='1',procedure_code='QFG2',procedure_type='ord',description='Q FEV IGG II SCREEN';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE 1 IGA' ,lab_id='1',procedure_code='QFIA',procedure_type='ord',description='Q FEVER PHASE 1 IGA';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE I IGG' ,lab_id='1',procedure_code='QFIG',procedure_type='ord',description='Q FEVER PHASE I IGG';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE II IGA' ,lab_id='1',procedure_code='QFIIA',procedure_type='ord',description='Q FEVER PHASE II IGA';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE II IGG' ,lab_id='1',procedure_code='QFIIG',procedure_type='ord',description='Q FEVER PHASE II IGG';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE II IGM' ,lab_id='1',procedure_code='QFIIM',procedure_type='ord',description='Q FEVER PHASE II IGM';
INSERT INTO procedure_type SET NAME='Q FEVER PHASE I IGM' ,lab_id='1',procedure_code='QFIM',procedure_type='ord',description='Q FEVER PHASE I IGM';
INSERT INTO procedure_type SET NAME='Q FEV IGM I SCREEN' ,lab_id='1',procedure_code='QFM1',procedure_type='ord',description='Q FEV IGM I SCREEN';
INSERT INTO procedure_type SET NAME='Q FEV IGM II SCREEN' ,lab_id='1',procedure_code='QFM2',procedure_type='ord',description='Q FEV IGM II SCREEN';
INSERT INTO procedure_type SET NAME='MITOGEN CONTROL' ,lab_id='1',procedure_code='QFTMIT',procedure_type='ord',description='MITOGEN CONTROL';
INSERT INTO procedure_type SET NAME='NIL CONTROL' ,lab_id='1',procedure_code='QFTNIL',procedure_type='ord',description='NIL CONTROL';
INSERT INTO procedure_type SET NAME='TB ANTIGEN' ,lab_id='1',procedure_code='QFTTB',procedure_type='ord',description='TB ANTIGEN';
INSERT INTO procedure_type SET NAME='QUANTIFERON TB INC' ,lab_id='1',procedure_code='QTFN',procedure_type='ord',description='QUANTIFERON TB INC';
INSERT INTO procedure_type SET NAME='QUINIDINE' ,lab_id='1',procedure_code='QUIN',procedure_type='ord',description='QUINIDINE';
INSERT INTO procedure_type SET NAME='QUINDINE' ,lab_id='1',procedure_code='QUIND',procedure_type='ord',description='QUINDINE';
INSERT INTO procedure_type SET NAME='QUINUPRISTIN DAL' ,lab_id='1',procedure_code='QUINU',procedure_type='ord',description='QUINUPRISTIN DAL';
INSERT INTO procedure_type SET NAME='RABIES ANTIBODY' ,lab_id='1',procedure_code='RAB',procedure_type='ord',description='RABIES ANTIBODY';
INSERT INTO procedure_type SET NAME='RABIES ANTIBODY EMPL' ,lab_id='1',procedure_code='RABE',procedure_type='ord',description='RABIES ANTIBODY EMPL';
INSERT INTO procedure_type SET NAME='RABIES VAC RESPONSE' ,lab_id='1',procedure_code='RABET',procedure_type='ord',description='RABIES VAC RESPONSE';
INSERT INTO procedure_type SET NAME='RABIES ANTIBODY EMPL' ,lab_id='1',procedure_code='RABI',procedure_type='ord',description='RABIES ANTIBODY EMPL';
INSERT INTO procedure_type SET NAME='%CD4+CD45RA+' ,lab_id='1',procedure_code='RACD4',procedure_type='ord',description='%CD4+CD45RA+';
INSERT INTO procedure_type SET NAME='CD4+CD45RA+' ,lab_id='1',procedure_code='RACD4A',procedure_type='ord',description='CD4+CD45RA+';
INSERT INTO procedure_type SET NAME='%CD8+CD45RA+' ,lab_id='1',procedure_code='RACD8',procedure_type='ord',description='%CD8+CD45RA+';
INSERT INTO procedure_type SET NAME='CD8+CD45RA+' ,lab_id='1',procedure_code='RACD8A',procedure_type='ord',description='CD8+CD45RA+';
INSERT INTO procedure_type SET NAME='AMPHETAMINES' ,lab_id='1',procedure_code='RAMP',procedure_type='ord',description='AMPHETAMINES';
INSERT INTO procedure_type SET NAME='REPEAT ANTIBODY SCREEN' ,lab_id='1',procedure_code='RAS',procedure_type='ord',description='REPEAT ANTIBODY SCREEN';
INSERT INTO procedure_type SET NAME='BREATHS PER  MIN' ,lab_id='1',procedure_code='RATE',procedure_type='ord',description='BREATHS PER  MIN';
INSERT INTO procedure_type SET NAME='Result:' ,lab_id='1',procedure_code='RB',procedure_type='ord',description='Result:';
INSERT INTO procedure_type SET NAME='RBC COUNT' ,lab_id='1',procedure_code='RBC',procedure_type='ord',description='RBC COUNT';
INSERT INTO procedure_type SET NAME='RBC, MANUAL' ,lab_id='1',procedure_code='RBCM',procedure_type='ord',description='RBC, MANUAL';
INSERT INTO procedure_type SET NAME='RBC ANTIBODY STUDIES' ,lab_id='1',procedure_code='RBCS',procedure_type='ord',description='RBC ANTIBODY STUDIES';
INSERT INTO procedure_type SET NAME='BENZODIAZEPINES' ,lab_id='1',procedure_code='RBNZ',procedure_type='ord',description='BENZODIAZEPINES';
INSERT INTO procedure_type SET NAME='BARBITURATES' ,lab_id='1',procedure_code='RBRB',procedure_type='ord',description='BARBITURATES';
INSERT INTO procedure_type SET NAME='RBCS' ,lab_id='1',procedure_code='RCB',procedure_type='ord',description='RBCS';
INSERT INTO procedure_type SET NAME='RBCS' ,lab_id='1',procedure_code='RCC',procedure_type='ord',description='RBCS';
INSERT INTO procedure_type SET NAME='RBCS FOR SH CONTS' ,lab_id='1',procedure_code='RCCS',procedure_type='ord',description='RBCS FOR SH CONTS';
INSERT INTO procedure_type SET NAME='ABBREV CHROMS ANAL' ,lab_id='1',procedure_code='RCHR',procedure_type='ord',description='ABBREV CHROMS ANAL';
INSERT INTO procedure_type SET NAME='COCAINE' ,lab_id='1',procedure_code='RCOC',procedure_type='ord',description='COCAINE';
INSERT INTO procedure_type SET NAME='RISTOCETIN COFACTOR' ,lab_id='1',procedure_code='RCOF',procedure_type='ord',description='RISTOCETIN COFACTOR';
INSERT INTO procedure_type SET NAME='RISTOCETIN COFACT INTERP.(MD)' ,lab_id='1',procedure_code='RCOFPF',procedure_type='ord',description='RISTOCETIN COFACT INTERP.(MD)';
INSERT INTO procedure_type SET NAME='RBCS, SEMEN' ,lab_id='1',procedure_code='RCS',procedure_type='ord',description='RBCS, SEMEN';
INSERT INTO procedure_type SET NAME='RBCS' ,lab_id='1',procedure_code='RCU',procedure_type='ord',description='RBCS';
INSERT INTO procedure_type SET NAME='ROUND EPITH CELLS' ,lab_id='1',procedure_code='RDEU',procedure_type='ord',description='ROUND EPITH CELLS';
INSERT INTO procedure_type SET NAME='ESTRADIOL RAPID' ,lab_id='1',procedure_code='RE2',procedure_type='ord',description='ESTRADIOL RAPID';
INSERT INTO procedure_type SET NAME='RAPID ESTRADIOL' ,lab_id='1',procedure_code='RE2S',procedure_type='ord',description='RAPID ESTRADIOL';
INSERT INTO procedure_type SET NAME='NL RIGHT EAR RESULT' ,lab_id='1',procedure_code='REAR',procedure_type='ord',description='NL RIGHT EAR RESULT';
INSERT INTO procedure_type SET NAME='INDICATION:' ,lab_id='1',procedure_code='REAS',procedure_type='ord',description='INDICATION:';
INSERT INTO procedure_type SET NAME='REDUCING SUBSTANCES' ,lab_id='1',procedure_code='REDS',procedure_type='ord',description='REDUCING SUBSTANCES';
INSERT INTO procedure_type SET NAME='REFERRAL LABORATORY' ,lab_id='1',procedure_code='REF',procedure_type='ord',description='REFERRAL LABORATORY';
INSERT INTO procedure_type SET NAME='REFERRAL LAB' ,lab_id='1',procedure_code='REF1',procedure_type='ord',description='REFERRAL LAB';
INSERT INTO procedure_type SET NAME='REFERRAL LABORATORY' ,lab_id='1',procedure_code='REF2',procedure_type='ord',description='REFERRAL LABORATORY';
INSERT INTO procedure_type SET NAME='RUBELLA EIA INDEX' ,lab_id='1',procedure_code='REIA',procedure_type='ord',description='RUBELLA EIA INDEX';
INSERT INTO procedure_type SET NAME='PLASMA RENIN ACTIVITY' ,lab_id='1',procedure_code='REN',procedure_type='ord',description='PLASMA RENIN ACTIVITY';
INSERT INTO procedure_type SET NAME='REPTILASE TIME' ,lab_id='1',procedure_code='REPT',procedure_type='ord',description='REPTILASE TIME';
INSERT INTO procedure_type SET NAME='RECOVERY ERROR' ,lab_id='1',procedure_code='RER',procedure_type='ord',description='RECOVERY ERROR';
INSERT INTO procedure_type SET NAME='RESULTS' ,lab_id='1',procedure_code='RESU',procedure_type='ord',description='RESULTS';
INSERT INTO procedure_type SET NAME='RESULTS' ,lab_id='1',procedure_code='RESU1',procedure_type='ord',description='RESULTS';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='RESU2',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='PTXID-Confirm Result' ,lab_id='1',procedure_code='RESUL',procedure_type='ord',description='PTXID-Confirm Result';
INSERT INTO procedure_type SET NAME='PTXID-Supple. Result' ,lab_id='1',procedure_code='RESULS',procedure_type='ord',description='PTXID-Supple. Result';
INSERT INTO procedure_type SET NAME='RETIC COUNT' ,lab_id='1',procedure_code='RETA',procedure_type='ord',description='RETIC COUNT';
INSERT INTO procedure_type SET NAME='RETICULOCYTE COUNT' ,lab_id='1',procedure_code='RETH',procedure_type='ord',description='RETICULOCYTE COUNT';
INSERT INTO procedure_type SET NAME='RETIC COUNT' ,lab_id='1',procedure_code='RETMA',procedure_type='ord',description='RETIC COUNT';
INSERT INTO procedure_type SET NAME='RET PROTO-ONCOGENE' ,lab_id='1',procedure_code='RETPO',procedure_type='ord',description='RET PROTO-ONCOGENE';
INSERT INTO procedure_type SET NAME='ABO Group w/Serum' ,lab_id='1',procedure_code='REVT',procedure_type='ord',description='ABO Group w/Serum';
INSERT INTO procedure_type SET NAME='RHEUMATOID FACTOR' ,lab_id='1',procedure_code='RF',procedure_type='ord',description='RHEUMATOID FACTOR';
INSERT INTO procedure_type SET NAME='FOLATE, RBC' ,lab_id='1',procedure_code='RFOL',procedure_type='ord',description='FOLATE, RBC';
INSERT INTO procedure_type SET NAME='RHEUM FACTOR, TITER' ,lab_id='1',procedure_code='RFT',procedure_type='ord',description='RHEUM FACTOR, TITER';
INSERT INTO procedure_type SET NAME='rgp46-I' ,lab_id='1',procedure_code='RGP46I',procedure_type='ord',description='rgp46-I';
INSERT INTO procedure_type SET NAME='RH TYPE' ,lab_id='1',procedure_code='RH0',procedure_type='ord',description='RH TYPE';
INSERT INTO procedure_type SET NAME='Interpretation:' ,lab_id='1',procedure_code='RI',procedure_type='ord',description='Interpretation:';
INSERT INTO procedure_type SET NAME='       Interpretation:' ,lab_id='1',procedure_code='RI2',procedure_type='ord',description='       Interpretation:';
INSERT INTO procedure_type SET NAME='RIFAMPIN' ,lab_id='1',procedure_code='RIF',procedure_type='ord',description='RIFAMPIN';
INSERT INTO procedure_type SET NAME='RIFABUTIN' ,lab_id='1',procedure_code='RIFA',procedure_type='ord',description='RIFABUTIN';
INSERT INTO procedure_type SET NAME='RISTOCETIN AGGREGATION' ,lab_id='1',procedure_code='RIST',procedure_type='ord',description='RISTOCETIN AGGREGATION';
INSERT INTO procedure_type SET NAME='Rist Agg Interp (MD):' ,lab_id='1',procedure_code='RISTPF',procedure_type='ord',description='Rist Agg Interp (MD):';
INSERT INTO procedure_type SET NAME='REAL LDL SIZE PATTERN' ,lab_id='1',procedure_code='RLDL',procedure_type='ord',description='REAL LDL SIZE PATTERN';
INSERT INTO procedure_type SET NAME='LH,RAPID' ,lab_id='1',procedure_code='RLH',procedure_type='ord',description='LH,RAPID';
INSERT INTO procedure_type SET NAME='REMNANT LIPO' ,lab_id='1',procedure_code='RLIP',procedure_type='ord',description='REMNANT LIPO';
INSERT INTO procedure_type SET NAME='RBC MORPHOLOGY' ,lab_id='1',procedure_code='RMOR',procedure_type='ord',description='RBC MORPHOLOGY';
INSERT INTO procedure_type SET NAME='RNA POLYMERASE III AB' ,lab_id='1',procedure_code='RNAP',procedure_type='ord',description='RNA POLYMERASE III AB';
INSERT INTO procedure_type SET NAME='ROUND CELLS' ,lab_id='1',procedure_code='RND',procedure_type='ord',description='ROUND CELLS';
INSERT INTO procedure_type SET NAME='REFERENCE/NEG RESULT' ,lab_id='1',procedure_code='RNEG',procedure_type='ord',description='REFERENCE/NEG RESULT';
INSERT INTO procedure_type SET NAME='SM/RNP ANTIBODY' ,lab_id='1',procedure_code='RNP',procedure_type='ord',description='SM/RNP ANTIBODY';
INSERT INTO procedure_type SET NAME='ANTI SM RNP ANTIBODY' ,lab_id='1',procedure_code='RNPQ',procedure_type='ord',description='ANTI SM RNP ANTIBODY';
INSERT INTO procedure_type SET NAME='%CD4+CD45RO+' ,lab_id='1',procedure_code='ROCD4',procedure_type='ord',description='%CD4+CD45RO+';
INSERT INTO procedure_type SET NAME='CD4+CD45RO+' ,lab_id='1',procedure_code='ROCD4A',procedure_type='ord',description='CD4+CD45RO+';
INSERT INTO procedure_type SET NAME='%CD8+CD45RO+' ,lab_id='1',procedure_code='ROCD8',procedure_type='ord',description='%CD8+CD45RO+';
INSERT INTO procedure_type SET NAME='CD8+CD45RO+' ,lab_id='1',procedure_code='ROCD8A',procedure_type='ord',description='CD8+CD45RO+';
INSERT INTO procedure_type SET NAME='OPIATES' ,lab_id='1',procedure_code='ROPT',procedure_type='ord',description='OPIATES';
INSERT INTO procedure_type SET NAME='rgp46-II' ,lab_id='1',procedure_code='RP46II',procedure_type='ord',description='rgp46-II';
INSERT INTO procedure_type SET NAME='PHENCYCLIDINE' ,lab_id='1',procedure_code='RPCP',procedure_type='ord',description='PHENCYCLIDINE';
INSERT INTO procedure_type SET NAME='RBCPopS' ,lab_id='1',procedure_code='RPOPS',procedure_type='ord',description='RBCPopS';
INSERT INTO procedure_type SET NAME='RPR' ,lab_id='1',procedure_code='RPR',procedure_type='ord',description='RPR';
INSERT INTO procedure_type SET NAME='RPR, PRENATAL' ,lab_id='1',procedure_code='RPRN',procedure_type='ord',description='RPR, PRENATAL';
INSERT INTO procedure_type SET NAME='RPR, PRENATAL' ,lab_id='1',procedure_code='RPRNO',procedure_type='ord',description='RPR, PRENATAL';
INSERT INTO procedure_type SET NAME='RPR' ,lab_id='1',procedure_code='RPRO',procedure_type='ord',description='RPR';
INSERT INTO procedure_type SET NAME='RPR TITER' ,lab_id='1',procedure_code='RPRT',procedure_type='ord',description='RPR TITER';
INSERT INTO procedure_type SET NAME='RISK' ,lab_id='1',procedure_code='RRSK',procedure_type='ord',description='RISK';
INSERT INTO procedure_type SET NAME='REDUCING SUBS, STOOL' ,lab_id='1',procedure_code='RSF',procedure_type='ord',description='REDUCING SUBS, STOOL';
INSERT INTO procedure_type SET NAME='ANTIBODY RESULT' ,lab_id='1',procedure_code='RSLT',procedure_type='ord',description='ANTIBODY RESULT';
INSERT INTO procedure_type SET NAME='REDUCING SUBSTANCES, STOOL' ,lab_id='1',procedure_code='RSST',procedure_type='ord',description='REDUCING SUBSTANCES, STOOL';
INSERT INTO procedure_type SET NAME='REDUCING SUBSTANCES' ,lab_id='1',procedure_code='RSUR',procedure_type='ord',description='REDUCING SUBSTANCES';
INSERT INTO procedure_type SET NAME='RS VIRUS ANTIBODY' ,lab_id='1',procedure_code='RSV',procedure_type='ord',description='RS VIRUS ANTIBODY';
INSERT INTO procedure_type SET NAME='RS VIRUS ANTIBODY' ,lab_id='1',procedure_code='RSVA',procedure_type='ord',description='RS VIRUS ANTIBODY';
INSERT INTO procedure_type SET NAME='TETRAHYDROCANNABINOL' ,lab_id='1',procedure_code='RTHC',procedure_type='ord',description='TETRAHYDROCANNABINOL';
INSERT INTO procedure_type SET NAME='REACTION TIME' ,lab_id='1',procedure_code='RTIME',procedure_type='ord',description='REACTION TIME';
INSERT INTO procedure_type SET NAME='RUBELLA ANTIBODY' ,lab_id='1',procedure_code='RUBI',procedure_type='ord',description='RUBELLA ANTIBODY';
INSERT INTO procedure_type SET NAME='RUSSELL\'S VIPER TEST' ,lab_id='1',procedure_code='RVVT',procedure_type='ord',description='RUSSELL\'S VIPER TEST';
INSERT INTO procedure_type SET NAME='RVVT INTERPRETATION' ,lab_id='1',procedure_code='RVVTI',procedure_type='ord',description='RVVT INTERPRETATION';
INSERT INTO procedure_type SET NAME='RUSSELL\'S VIPER INTERP.(MD)' ,lab_id='1',procedure_code='RVVTPF',procedure_type='ord',description='RUSSELL\'S VIPER INTERP.(MD)';
INSERT INTO procedure_type SET NAME='RECIPIENT\'S WEIGHT' ,lab_id='1',procedure_code='RWT',procedure_type='ord',description='RECIPIENT\'S WEIGHT';
INSERT INTO procedure_type SET NAME='REFLEX CHARGE FOR S13INH' ,lab_id='1',procedure_code='S131IB',procedure_type='ord',description='REFLEX CHARGE FOR S13INH';
INSERT INTO procedure_type SET NAME='ADAMTS13 ACTIVITY' ,lab_id='1',procedure_code='S13ACT',procedure_type='ord',description='ADAMTS13 ACTIVITY';
INSERT INTO procedure_type SET NAME='REFLEX CHARGE FOR S13INH' ,lab_id='1',procedure_code='S13IB',procedure_type='ord',description='REFLEX CHARGE FOR S13INH';
INSERT INTO procedure_type SET NAME='ADAMTS13 INHIBITOR' ,lab_id='1',procedure_code='S13INH',procedure_type='ord',description='ADAMTS13 INHIBITOR';
INSERT INTO procedure_type SET NAME='SERUM A1A' ,lab_id='1',procedure_code='SA1A',procedure_type='ord',description='SERUM A1A';
INSERT INTO procedure_type SET NAME='ADDITIONAL INFO' ,lab_id='1',procedure_code='SADI',procedure_type='ord',description='ADDITIONAL INFO';
INSERT INTO procedure_type SET NAME='SALICYLATE' ,lab_id='1',procedure_code='SAL',procedure_type='ord',description='SALICYLATE';
INSERT INTO procedure_type SET NAME='OXYGEN SATURATION' ,lab_id='1',procedure_code='SAO2',procedure_type='ord',description='OXYGEN SATURATION';
INSERT INTO procedure_type SET NAME='SARCOSINE' ,lab_id='1',procedure_code='SARC',procedure_type='ord',description='SARCOSINE';
INSERT INTO procedure_type SET NAME='% SATURATION' ,lab_id='1',procedure_code='SAT',procedure_type='ord',description='% SATURATION';
INSERT INTO procedure_type SET NAME='STUDY, BLUE TOP TUBE' ,lab_id='1',procedure_code='SBLU',procedure_type='ord',description='STUDY, BLUE TOP TUBE';
INSERT INTO procedure_type SET NAME='NON-CLONAL ABNL' ,lab_id='1',procedure_code='SCA',procedure_type='ord',description='NON-CLONAL ABNL';
INSERT INTO procedure_type SET NAME='CELLS SEEDED' ,lab_id='1',procedure_code='SCELLS',procedure_type='ord',description='CELLS SEEDED';
INSERT INTO procedure_type SET NAME='SCHISTOSOMA IGG AB' ,lab_id='1',procedure_code='SCHISS',procedure_type='ord',description='SCHISTOSOMA IGG AB';
INSERT INTO procedure_type SET NAME='SCHISTOSOMIASIS, CSF' ,lab_id='1',procedure_code='SCHIST',procedure_type='ord',description='SCHISTOSOMIASIS, CSF';
INSERT INTO procedure_type SET NAME='STEM CELL IDENTIFICATION' ,lab_id='1',procedure_code='SCID',procedure_type='ord',description='STEM CELL IDENTIFICATION';
INSERT INTO procedure_type SET NAME='HGB SOLUBILITY SCREEN' ,lab_id='1',procedure_code='SCKL',procedure_type='ord',description='HGB SOLUBILITY SCREEN';
INSERT INTO procedure_type SET NAME='SCL-70 ANTIBODY' ,lab_id='1',procedure_code='SCL70',procedure_type='ord',description='SCL-70 ANTIBODY';
INSERT INTO procedure_type SET NAME='THIOCYANATE' ,lab_id='1',procedure_code='SCN',procedure_type='ord',description='THIOCYANATE';
INSERT INTO procedure_type SET NAME='TRANSPORT IN SF, SAME DAY' ,lab_id='1',procedure_code='SDEL',procedure_type='ord',description='TRANSPORT IN SF, SAME DAY';
INSERT INTO procedure_type SET NAME='SPECIMEN' ,lab_id='1',procedure_code='SDES',procedure_type='ord',description='SPECIMEN';
INSERT INTO procedure_type SET NAME='SELENIUM' ,lab_id='1',procedure_code='SE',procedure_type='ord',description='SELENIUM';
INSERT INTO procedure_type SET NAME='CONF SELF EXCLUSION' ,lab_id='1',procedure_code='SELFEX',procedure_type='ord',description='CONF SELF EXCLUSION';
INSERT INTO procedure_type SET NAME='SELENIUM, RU' ,lab_id='1',procedure_code='SELRRU',procedure_type='ord',description='SELENIUM, RU';
INSERT INTO procedure_type SET NAME='SELENIUM (CR CORR)' ,lab_id='1',procedure_code='SELRUC',procedure_type='ord',description='SELENIUM (CR CORR)';
INSERT INTO procedure_type SET NAME='SELENIUM, RU' ,lab_id='1',procedure_code='SELUR',procedure_type='ord',description='SELENIUM, RU';
INSERT INTO procedure_type SET NAME='SERINE' ,lab_id='1',procedure_code='SERN',procedure_type='ord',description='SERINE';
INSERT INTO procedure_type SET NAME='SEROTONIN' ,lab_id='1',procedure_code='SERO',procedure_type='ord',description='SEROTONIN';
INSERT INTO procedure_type SET NAME='CREATININE (SELRU)' ,lab_id='1',procedure_code='SEUCR',procedure_type='ord',description='CREATININE (SELRU)';
INSERT INTO procedure_type SET NAME='STUDY, GOLD TOP TUBE' ,lab_id='1',procedure_code='SGOLD',procedure_type='ord',description='STUDY, GOLD TOP TUBE';
INSERT INTO procedure_type SET NAME='STUDY, GRAY TOP TUBE' ,lab_id='1',procedure_code='SGRAY',procedure_type='ord',description='STUDY, GRAY TOP TUBE';
INSERT INTO procedure_type SET NAME='STUDY, GREEN TOP TUBE' ,lab_id='1',procedure_code='SGRN',procedure_type='ord',description='STUDY, GREEN TOP TUBE';
INSERT INTO procedure_type SET NAME='SPECIFIC GRAVITY' ,lab_id='1',procedure_code='SGUA',procedure_type='ord',description='SPECIFIC GRAVITY';
INSERT INTO procedure_type SET NAME='SEX HORMONE BNDG GLOBULIN' ,lab_id='1',procedure_code='SHBG',procedure_type='ord',description='SEX HORMONE BNDG GLOBULIN';
INSERT INTO procedure_type SET NAME='HEPATITIS C PCR, QL' ,lab_id='1',procedure_code='SHECQL',procedure_type='ord',description='HEPATITIS C PCR, QL';
INSERT INTO procedure_type SET NAME='SOURCE OF SPECIMEN' ,lab_id='1',procedure_code='SID',procedure_type='ord',description='SOURCE OF SPECIMEN';
INSERT INTO procedure_type SET NAME='SIROLIMUS' ,lab_id='1',procedure_code='SIRO',procedure_type='ord',description='SIROLIMUS';
INSERT INTO procedure_type SET NAME='SITE' ,lab_id='1',procedure_code='SITE',procedure_type='ord',description='SITE';
INSERT INTO procedure_type SET NAME='STUDY, LAVENDAR TOP TUBE' ,lab_id='1',procedure_code='SLAV',procedure_type='ord',description='STUDY, LAVENDAR TOP TUBE';
INSERT INTO procedure_type SET NAME='ST LOUIS EQUINE AB' ,lab_id='1',procedure_code='SLOU',procedure_type='ord',description='ST LOUIS EQUINE AB';
INSERT INTO procedure_type SET NAME='ST LOUIS EQUINE AB' ,lab_id='1',procedure_code='SLOUA',procedure_type='ord',description='ST LOUIS EQUINE AB';
INSERT INTO procedure_type SET NAME='ST.LOUIS IGG AB' ,lab_id='1',procedure_code='SLOUG',procedure_type='ord',description='ST.LOUIS IGG AB';
INSERT INTO procedure_type SET NAME='ST.LOUIS INTERPRET:' ,lab_id='1',procedure_code='SLOUI',procedure_type='ord',description='ST.LOUIS INTERPRET:';
INSERT INTO procedure_type SET NAME='ST.LOUIS IGM AB' ,lab_id='1',procedure_code='SLOUM',procedure_type='ord',description='ST.LOUIS IGM AB';
INSERT INTO procedure_type SET NAME='STUDY, LIGHT GREEN TOP TUBE' ,lab_id='1',procedure_code='SLTGRN',procedure_type='ord',description='STUDY, LIGHT GREEN TOP TUBE';
INSERT INTO procedure_type SET NAME='SM ANTIBODY' ,lab_id='1',procedure_code='SM',procedure_type='ord',description='SM ANTIBODY';
INSERT INTO procedure_type SET NAME='SPINAL MUSC. ATROPHY' ,lab_id='1',procedure_code='SMAPCR',procedure_type='ord',description='SPINAL MUSC. ATROPHY';
INSERT INTO procedure_type SET NAME='ANTI SM ANTIBODY' ,lab_id='1',procedure_code='SMQ',procedure_type='ord',description='ANTI SM ANTIBODY';
INSERT INTO procedure_type SET NAME='SULFAMETHOXAZOLE' ,lab_id='1',procedure_code='SMZ',procedure_type='ord',description='SULFAMETHOXAZOLE';
INSERT INTO procedure_type SET NAME='Source' ,lab_id='1',procedure_code='SOU1',procedure_type='ord',description='Source';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='SOU2',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='SOURCE',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='FORM ID' ,lab_id='1',procedure_code='SPECID',procedure_type='ord',description='FORM ID';
INSERT INTO procedure_type SET NAME='SPERM COUNT' ,lab_id='1',procedure_code='SPER',procedure_type='ord',description='SPERM COUNT';
INSERT INTO procedure_type SET NAME='SPERM COUNT' ,lab_id='1',procedure_code='SPERM',procedure_type='ord',description='SPERM COUNT';
INSERT INTO procedure_type SET NAME='ART SAT PULSE OXIM' ,lab_id='1',procedure_code='SPO2',procedure_type='ord',description='ART SAT PULSE OXIM';
INSERT INTO procedure_type SET NAME='NL O2SAT SPOT CHECK' ,lab_id='1',procedure_code='SPOTCK',procedure_type='ord',description='NL O2SAT SPOT CHECK';
INSERT INTO procedure_type SET NAME='# SPERM/TOT VOL PELLET' ,lab_id='1',procedure_code='SPP',procedure_type='ord',description='# SPERM/TOT VOL PELLET';
INSERT INTO procedure_type SET NAME='SPECIMEN TYPE' ,lab_id='1',procedure_code='SPTP',procedure_type='ord',description='SPECIMEN TYPE';
INSERT INTO procedure_type SET NAME='SQUAM EPITH CELLS' ,lab_id='1',procedure_code='SQEU',procedure_type='ord',description='SQUAM EPITH CELLS';
INSERT INTO procedure_type SET NAME='Specimen Type:' ,lab_id='1',procedure_code='SR',procedure_type='ord',description='Specimen Type:';
INSERT INTO procedure_type SET NAME='Specimen Type:' ,lab_id='1',procedure_code='SR2',procedure_type='ord',description='Specimen Type:';
INSERT INTO procedure_type SET NAME='Specimen Type:' ,lab_id='1',procedure_code='SR3',procedure_type='ord',description='Specimen Type:';
INSERT INTO procedure_type SET NAME='SEROTONIN RELEASE ASSY' ,lab_id='1',procedure_code='SRA',procedure_type='ord',description='SEROTONIN RELEASE ASSY';
INSERT INTO procedure_type SET NAME='SOURCE OF SPECIMEN' ,lab_id='1',procedure_code='SRCE',procedure_type='ord',description='SOURCE OF SPECIMEN';
INSERT INTO procedure_type SET NAME='SPECIAL REQUIREMENTS' ,lab_id='1',procedure_code='SRCHK',procedure_type='ord',description='SPECIAL REQUIREMENTS';
INSERT INTO procedure_type SET NAME='COMMENTS' ,lab_id='1',procedure_code='SREQ',procedure_type='ord',description='COMMENTS';
INSERT INTO procedure_type SET NAME='SRP AUTOANTIBODIES' ,lab_id='1',procedure_code='SRPAB',procedure_type='ord',description='SRP AUTOANTIBODIES';
INSERT INTO procedure_type SET NAME='SSA ANTIBODY' ,lab_id='1',procedure_code='SSA',procedure_type='ord',description='SSA ANTIBODY';
INSERT INTO procedure_type SET NAME='SSB ANTIBODY' ,lab_id='1',procedure_code='SSB',procedure_type='ord',description='SSB ANTIBODY';
INSERT INTO procedure_type SET NAME='SEROTYPE 1 (1)' ,lab_id='1',procedure_code='ST1',procedure_type='ord',description='SEROTYPE 1 (1)';
INSERT INTO procedure_type SET NAME='SEROTYPE 14 (14)' ,lab_id='1',procedure_code='ST14',procedure_type='ord',description='SEROTYPE 14 (14)';
INSERT INTO procedure_type SET NAME='SEROTYPE 19 (19F)' ,lab_id='1',procedure_code='ST19',procedure_type='ord',description='SEROTYPE 19 (19F)';
INSERT INTO procedure_type SET NAME='SEROTYPE 23 (23F)' ,lab_id='1',procedure_code='ST23',procedure_type='ord',description='SEROTYPE 23 (23F)';
INSERT INTO procedure_type SET NAME='SEROTYPE 3 (3)' ,lab_id='1',procedure_code='ST3',procedure_type='ord',description='SEROTYPE 3 (3)';
INSERT INTO procedure_type SET NAME='SEROTYPE 51 (7F)' ,lab_id='1',procedure_code='ST51',procedure_type='ord',description='SEROTYPE 51 (7F)';
INSERT INTO procedure_type SET NAME='START TIME' ,lab_id='1',procedure_code='STARTT',procedure_type='ord',description='START TIME';
INSERT INTO procedure_type SET NAME='STAT RES SURCHARGE IMM' ,lab_id='1',procedure_code='STIM',procedure_type='ord',description='STAT RES SURCHARGE IMM';
INSERT INTO procedure_type SET NAME='SUM TOTAL LDL-C' ,lab_id='1',procedure_code='STLD',procedure_type='ord',description='SUM TOTAL LDL-C';
INSERT INTO procedure_type SET NAME='STONE ANALYSIS' ,lab_id='1',procedure_code='STON',procedure_type='ord',description='STONE ANALYSIS';
INSERT INTO procedure_type SET NAME='STONE ANALYSIS' ,lab_id='1',procedure_code='STONE',procedure_type='ord',description='STONE ANALYSIS';
INSERT INTO procedure_type SET NAME='STOP DATE' ,lab_id='1',procedure_code='STOPTD',procedure_type='ord',description='STOP DATE';
INSERT INTO procedure_type SET NAME='STREPTOMYCIN' ,lab_id='1',procedure_code='STREPT',procedure_type='ord',description='STREPTOMYCIN';
INSERT INTO procedure_type SET NAME='STRONGYLOIDES IGG AB' ,lab_id='1',procedure_code='STRONG',procedure_type='ord',description='STRONGYLOIDES IGG AB';
INSERT INTO procedure_type SET NAME='SECOND TRIMESTER SCREEN' ,lab_id='1',procedure_code='STS1',procedure_type='ord',description='SECOND TRIMESTER SCREEN';
INSERT INTO procedure_type SET NAME='CXAFP FORM NUMBER:' ,lab_id='1',procedure_code='STS2',procedure_type='ord',description='CXAFP FORM NUMBER:';
INSERT INTO procedure_type SET NAME='STAT ASSAY SURCHARGE BY SKBL' ,lab_id='1',procedure_code='STSK',procedure_type='ord',description='STAT ASSAY SURCHARGE BY SKBL';
INSERT INTO procedure_type SET NAME='TRANSPORT TO AND BY SKBL, STAT' ,lab_id='1',procedure_code='STTX',procedure_type='ord',description='TRANSPORT TO AND BY SKBL, STAT';
INSERT INTO procedure_type SET NAME='SUBSTRATE TYPE' ,lab_id='1',procedure_code='SUBT',procedure_type='ord',description='SUBSTRATE TYPE';
INSERT INTO procedure_type SET NAME='SUCCINYL PURINES' ,lab_id='1',procedure_code='SUCC',procedure_type='ord',description='SUCCINYL PURINES';
INSERT INTO procedure_type SET NAME='SULFAMETHOXAZOLE' ,lab_id='1',procedure_code='SULF',procedure_type='ord',description='SULFAMETHOXAZOLE';
INSERT INTO procedure_type SET NAME='SUMMARY: SURFACE MARKERS' ,lab_id='1',procedure_code='SUM',procedure_type='ord',description='SUMMARY: SURFACE MARKERS';
INSERT INTO procedure_type SET NAME='SACCHAROMONAS VIRIDIS' ,lab_id='1',procedure_code='SVIR',procedure_type='ord',description='SACCHAROMONAS VIRIDIS';
INSERT INTO procedure_type SET NAME='VISCOSITY' ,lab_id='1',procedure_code='SVIS',procedure_type='ord',description='VISCOSITY';
INSERT INTO procedure_type SET NAME='VOL SUPERNATANT' ,lab_id='1',procedure_code='SVOL',procedure_type='ord',description='VOL SUPERNATANT';
INSERT INTO procedure_type SET NAME='VOLUME OF SWEAT COLL\'D' ,lab_id='1',procedure_code='SW',procedure_type='ord',description='VOLUME OF SWEAT COLL\'D';
INSERT INTO procedure_type SET NAME='TMP SMZ' ,lab_id='1',procedure_code='SXT',procedure_type='ord',description='TMP SMZ';
INSERT INTO procedure_type SET NAME='SYP (BSI)' ,lab_id='1',procedure_code='SYPSO',procedure_type='ord',description='SYP (BSI)';
INSERT INTO procedure_type SET NAME='SYSTOLIC BLD PRESS' ,lab_id='1',procedure_code='SYS',procedure_type='ord',description='SYSTOLIC BLD PRESS';
INSERT INTO procedure_type SET NAME='BACTERICIDAL TITER' ,lab_id='1',procedure_code='T035',procedure_type='ord',description='BACTERICIDAL TITER';
INSERT INTO procedure_type SET NAME='SPORE TEST' ,lab_id='1',procedure_code='T036',procedure_type='ord',description='SPORE TEST';
INSERT INTO procedure_type SET NAME='L PNEUMOPHILA TYPE 1 ANTIGEN' ,lab_id='1',procedure_code='T050',procedure_type='ord',description='L PNEUMOPHILA TYPE 1 ANTIGEN';
INSERT INTO procedure_type SET NAME='HISTOPLASMA ANTIGEN' ,lab_id='1',procedure_code='T052',procedure_type='ord',description='HISTOPLASMA ANTIGEN';
INSERT INTO procedure_type SET NAME='GROUP A STREP ANTIGEN' ,lab_id='1',procedure_code='T116',procedure_type='ord',description='GROUP A STREP ANTIGEN';
INSERT INTO procedure_type SET NAME='B PERTUSSIS ANTIGEN' ,lab_id='1',procedure_code='T122',procedure_type='ord',description='B PERTUSSIS ANTIGEN';
INSERT INTO procedure_type SET NAME='LEGIONELLA ANTIGEN' ,lab_id='1',procedure_code='T124',procedure_type='ord',description='LEGIONELLA ANTIGEN';
INSERT INTO procedure_type SET NAME='H PYLORI ANTIGEN' ,lab_id='1',procedure_code='T133',procedure_type='ord',description='H PYLORI ANTIGEN';
INSERT INTO procedure_type SET NAME='MENINGITIS ANTIGEN(S)' ,lab_id='1',procedure_code='T134',procedure_type='ord',description='MENINGITIS ANTIGEN(S)';
INSERT INTO procedure_type SET NAME='GROUP B STREP ANTIGEN' ,lab_id='1',procedure_code='T136',procedure_type='ord',description='GROUP B STREP ANTIGEN';
INSERT INTO procedure_type SET NAME='WBC SMEAR' ,lab_id='1',procedure_code='T221',procedure_type='ord',description='WBC SMEAR';
INSERT INTO procedure_type SET NAME='CRYPTOCOCCAL ANTIGEN' ,lab_id='1',procedure_code='T253',procedure_type='ord',description='CRYPTOCOCCAL ANTIGEN';
INSERT INTO procedure_type SET NAME='MTB complex PCR' ,lab_id='1',procedure_code='T290',procedure_type='ord',description='MTB complex PCR';
INSERT INTO procedure_type SET NAME='T3, TOTAL' ,lab_id='1',procedure_code='T3',procedure_type='ord',description='T3, TOTAL';
INSERT INTO procedure_type SET NAME='SHIGA TOXIN' ,lab_id='1',procedure_code='T315',procedure_type='ord',description='SHIGA TOXIN';
INSERT INTO procedure_type SET NAME='TEST REQUESTED' ,lab_id='1',procedure_code='T319',procedure_type='ord',description='TEST REQUESTED';
INSERT INTO procedure_type SET NAME='Clostridium difficile' ,lab_id='1',procedure_code='T328',procedure_type='ord',description='Clostridium difficile';
INSERT INTO procedure_type SET NAME='C DIFFICILE CYTOTOXIN' ,lab_id='1',procedure_code='T330',procedure_type='ord',description='C DIFFICILE CYTOTOXIN';
INSERT INTO procedure_type SET NAME='ROTAVIRUS ANTIGEN' ,lab_id='1',procedure_code='T333',procedure_type='ord',description='ROTAVIRUS ANTIGEN';
INSERT INTO procedure_type SET NAME='RSV ANTIGEN' ,lab_id='1',procedure_code='T334',procedure_type='ord',description='RSV ANTIGEN';
INSERT INTO procedure_type SET NAME='HERPESVIRUS DNA' ,lab_id='1',procedure_code='T337',procedure_type='ord',description='HERPESVIRUS DNA';
INSERT INTO procedure_type SET NAME='PARVOVIRUS DNA' ,lab_id='1',procedure_code='T338',procedure_type='ord',description='PARVOVIRUS DNA';
INSERT INTO procedure_type SET NAME='VARICELLA ZOSTER DNA' ,lab_id='1',procedure_code='T339',procedure_type='ord',description='VARICELLA ZOSTER DNA';
INSERT INTO procedure_type SET NAME='RESPIRATORY VIRUS ANTIGEN(S)' ,lab_id='1',procedure_code='T341',procedure_type='ord',description='RESPIRATORY VIRUS ANTIGEN(S)';
INSERT INTO procedure_type SET NAME='CMV ANTIGEN' ,lab_id='1',procedure_code='T342',procedure_type='ord',description='CMV ANTIGEN';
INSERT INTO procedure_type SET NAME='B PERTUSSIS DNA' ,lab_id='1',procedure_code='T344',procedure_type='ord',description='B PERTUSSIS DNA';
INSERT INTO procedure_type SET NAME='HERPESVIRUS ANTIGEN(S)' ,lab_id='1',procedure_code='T345',procedure_type='ord',description='HERPESVIRUS ANTIGEN(S)';
INSERT INTO procedure_type SET NAME='VARICELLA ZOSTER ANTIGEN:' ,lab_id='1',procedure_code='T346',procedure_type='ord',description='VARICELLA ZOSTER ANTIGEN:';
INSERT INTO procedure_type SET NAME='HERPES SIMPLEX ANTIGEN:' ,lab_id='1',procedure_code='T347',procedure_type='ord',description='HERPES SIMPLEX ANTIGEN:';
INSERT INTO procedure_type SET NAME='RESPIRATORY VIRAL PANEL PCR' ,lab_id='1',procedure_code='T350',procedure_type='ord',description='RESPIRATORY VIRAL PANEL PCR';
INSERT INTO procedure_type SET NAME='INFLUENZA A/H1N1 PCR' ,lab_id='1',procedure_code='T351',procedure_type='ord',description='INFLUENZA A/H1N1 PCR';
INSERT INTO procedure_type SET NAME='Rapid FLU/RSV PCR' ,lab_id='1',procedure_code='T352',procedure_type='ord',description='Rapid FLU/RSV PCR';
INSERT INTO procedure_type SET NAME='B PERTUSSIS/PARAPERTUSSIS DNA' ,lab_id='1',procedure_code='T355',procedure_type='ord',description='B PERTUSSIS/PARAPERTUSSIS DNA';
INSERT INTO procedure_type SET NAME='CHLAMYDIA TRACHOMATIS' ,lab_id='1',procedure_code='T360',procedure_type='ord',description='CHLAMYDIA TRACHOMATIS';
INSERT INTO procedure_type SET NAME='NEISSERIA GONORRHOEAE' ,lab_id='1',procedure_code='T361',procedure_type='ord',description='NEISSERIA GONORRHOEAE';
INSERT INTO procedure_type SET NAME='ANTI MOUSE OKT3 AB' ,lab_id='1',procedure_code='T3AB',procedure_type='ord',description='ANTI MOUSE OKT3 AB';
INSERT INTO procedure_type SET NAME='T3, FREE' ,lab_id='1',procedure_code='T3FR',procedure_type='ord',description='T3, FREE';
INSERT INTO procedure_type SET NAME='T3, REVERSE' ,lab_id='1',procedure_code='T3RV',procedure_type='ord',description='T3, REVERSE';
INSERT INTO procedure_type SET NAME='O AND P RESULT' ,lab_id='1',procedure_code='T401',procedure_type='ord',description='O AND P RESULT';
INSERT INTO procedure_type SET NAME='PNEUMOCYSTIS CARINII' ,lab_id='1',procedure_code='T402',procedure_type='ord',description='PNEUMOCYSTIS CARINII';
INSERT INTO procedure_type SET NAME='GIEMSA SMEAR(S)' ,lab_id='1',procedure_code='T403',procedure_type='ord',description='GIEMSA SMEAR(S)';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='T404',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='PINWORMS' ,lab_id='1',procedure_code='T405',procedure_type='ord',description='PINWORMS';
INSERT INTO procedure_type SET NAME='CULTURE' ,lab_id='1',procedure_code='T406',procedure_type='ord',description='CULTURE';
INSERT INTO procedure_type SET NAME='ACID FAST SMEAR' ,lab_id='1',procedure_code='T407',procedure_type='ord',description='ACID FAST SMEAR';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='T408',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='GIEMSA SMEAR(S)' ,lab_id='1',procedure_code='T409',procedure_type='ord',description='GIEMSA SMEAR(S)';
INSERT INTO procedure_type SET NAME='CULTURE' ,lab_id='1',procedure_code='T410',procedure_type='ord',description='CULTURE';
INSERT INTO procedure_type SET NAME='MEAT STAIN' ,lab_id='1',procedure_code='T411',procedure_type='ord',description='MEAT STAIN';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='T412',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='GIARDIA LAMBLIA ANTIGEN' ,lab_id='1',procedure_code='T413',procedure_type='ord',description='GIARDIA LAMBLIA ANTIGEN';
INSERT INTO procedure_type SET NAME='MICROSPORIDIA' ,lab_id='1',procedure_code='T414',procedure_type='ord',description='MICROSPORIDIA';
INSERT INTO procedure_type SET NAME='TRICHOMONAS' ,lab_id='1',procedure_code='T415',procedure_type='ord',description='TRICHOMONAS';
INSERT INTO procedure_type SET NAME='RESULT' ,lab_id='1',procedure_code='T416',procedure_type='ord',description='RESULT';
INSERT INTO procedure_type SET NAME='C TRACHOMATIS ANTIGEN' ,lab_id='1',procedure_code='T700',procedure_type='ord',description='C TRACHOMATIS ANTIGEN';
INSERT INTO procedure_type SET NAME='C TRACHOMATIS DNA' ,lab_id='1',procedure_code='T701',procedure_type='ord',description='C TRACHOMATIS DNA';
INSERT INTO procedure_type SET NAME='C. trachomatis DNA' ,lab_id='1',procedure_code='T702',procedure_type='ord',description='C. trachomatis DNA';
INSERT INTO procedure_type SET NAME='ABO Group' ,lab_id='1',procedure_code='TABO',procedure_type='ord',description='ABO Group';
INSERT INTO procedure_type SET NAME='CD3 T CELLS ABS' ,lab_id='1',procedure_code='TABS',procedure_type='ord',description='CD3 T CELLS ABS';
INSERT INTO procedure_type SET NAME='TACROLIMUS' ,lab_id='1',procedure_code='TAC',procedure_type='ord',description='TACROLIMUS';
INSERT INTO procedure_type SET NAME='TOTAL apoB100-Calc' ,lab_id='1',procedure_code='TAPO',procedure_type='ord',description='TOTAL apoB100-Calc';
INSERT INTO procedure_type SET NAME='TAURINE' ,lab_id='1',procedure_code='TAUR',procedure_type='ord',description='TAURINE';
INSERT INTO procedure_type SET NAME='T4 BINDING GLOBULIN' ,lab_id='1',procedure_code='TBG',procedure_type='ord',description='T4 BINDING GLOBULIN';
INSERT INTO procedure_type SET NAME='TBII' ,lab_id='1',procedure_code='TBII',procedure_type='ord',description='TBII';
INSERT INTO procedure_type SET NAME='TOT BILI, WHL BLD' ,lab_id='1',procedure_code='TBILWB',procedure_type='ord',description='TOT BILI, WHL BLD';
INSERT INTO procedure_type SET NAME='TIME CALLED' ,lab_id='1',procedure_code='TC',procedure_type='ord',description='TIME CALLED';
INSERT INTO procedure_type SET NAME='T. CRUZI ANTIBODY' ,lab_id='1',procedure_code='TCAB',procedure_type='ord',description='T. CRUZI ANTIBODY';
INSERT INTO procedure_type SET NAME='THERMOACT. CANDIDUS' ,lab_id='1',procedure_code='TCAN',procedure_type='ord',description='THERMOACT. CANDIDUS';
INSERT INTO procedure_type SET NAME='CD3 T CELLS %' ,lab_id='1',procedure_code='TCEL',procedure_type='ord',description='CD3 T CELLS %';
INSERT INTO procedure_type SET NAME='SUM TOTAL CHOLESTEROL' ,lab_id='1',procedure_code='TCHOL',procedure_type='ord',description='SUM TOTAL CHOLESTEROL';
INSERT INTO procedure_type SET NAME='COMBINED TOTAL (TCLM)' ,lab_id='1',procedure_code='TCLM',procedure_type='ord',description='COMBINED TOTAL (TCLM)';
INSERT INTO procedure_type SET NAME='TOTAL CO2' ,lab_id='1',procedure_code='TCO2',procedure_type='ord',description='TOTAL CO2';
INSERT INTO procedure_type SET NAME='CLARIFICATION:' ,lab_id='1',procedure_code='TCOM',procedure_type='ord',description='CLARIFICATION:';
INSERT INTO procedure_type SET NAME='TCR REARRANGE, QUAL, PCR' ,lab_id='1',procedure_code='TCRR',procedure_type='ord',description='TCR REARRANGE, QUAL, PCR';
INSERT INTO procedure_type SET NAME='T.CRUZI AB EIA (BSI)' ,lab_id='1',procedure_code='TCRZ',procedure_type='ord',description='T.CRUZI AB EIA (BSI)';
INSERT INTO procedure_type SET NAME='TOTAL CARNITINE, UR' ,lab_id='1',procedure_code='TCUR',procedure_type='ord',description='TOTAL CARNITINE, UR';
INSERT INTO procedure_type SET NAME='T CELL CROSSMATCH' ,lab_id='1',procedure_code='TCX',procedure_type='ord',description='T CELL CROSSMATCH';
INSERT INTO procedure_type SET NAME='TAIL DEFECT' ,lab_id='1',procedure_code='TDEF',procedure_type='ord',description='TAIL DEFECT';
INSERT INTO procedure_type SET NAME='TDT INTERPRETATION' ,lab_id='1',procedure_code='TDTI',procedure_type='ord',description='TDT INTERPRETATION';
INSERT INTO procedure_type SET NAME='TDT INTERPRETED BY (MD):' ,lab_id='1',procedure_code='TDTPF',procedure_type='ord',description='TDT INTERPRETED BY (MD):';
INSERT INTO procedure_type SET NAME='TEICOPLANIN' ,lab_id='1',procedure_code='TEICO',procedure_type='ord',description='TEICOPLANIN';
INSERT INTO procedure_type SET NAME='TERBINAFINE' ,lab_id='1',procedure_code='TER',procedure_type='ord',description='TERBINAFINE';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, ULTRAS' ,lab_id='1',procedure_code='TESP',procedure_type='ord',description='TESTOSTERONE, ULTRAS';
INSERT INTO procedure_type SET NAME='PTXID-Confirm Test' ,lab_id='1',procedure_code='TESTSO',procedure_type='ord',description='PTXID-Confirm Test';
INSERT INTO procedure_type SET NAME='PTXID-Supple. Test' ,lab_id='1',procedure_code='TESTSU',procedure_type='ord',description='PTXID-Supple. Test';
INSERT INTO procedure_type SET NAME='TETANUS ANTITOXOID' ,lab_id='1',procedure_code='TET',procedure_type='ord',description='TETANUS ANTITOXOID';
INSERT INTO procedure_type SET NAME='TETRACYCLINE' ,lab_id='1',procedure_code='TETRA',procedure_type='ord',description='TETRACYCLINE';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, % FREE' ,lab_id='1',procedure_code='TFP',procedure_type='ord',description='TESTOSTERONE, % FREE';
INSERT INTO procedure_type SET NAME='TGLB ANTIBODIES' ,lab_id='1',procedure_code='TGAB',procedure_type='ord',description='TGLB ANTIBODIES';
INSERT INTO procedure_type SET NAME='TRIGLYCERIDE BODY FLUID' ,lab_id='1',procedure_code='TGBF',procedure_type='ord',description='TRIGLYCERIDE BODY FLUID';
INSERT INTO procedure_type SET NAME='T CELL RECEPTOR GENE' ,lab_id='1',procedure_code='TGEN',procedure_type='ord',description='T CELL RECEPTOR GENE';
INSERT INTO procedure_type SET NAME='THYROGLOBULIN' ,lab_id='1',procedure_code='TGL',procedure_type='ord',description='THYROGLOBULIN';
INSERT INTO procedure_type SET NAME='TGLB, ULTRASENSITIVE' ,lab_id='1',procedure_code='TGLU',procedure_type='ord',description='TGLB, ULTRASENSITIVE';
INSERT INTO procedure_type SET NAME='TGLB AB SCREEN' ,lab_id='1',procedure_code='TGTM1',procedure_type='ord',description='TGLB AB SCREEN';
INSERT INTO procedure_type SET NAME='TGLB,TUMOR MARKER' ,lab_id='1',procedure_code='TGTM2',procedure_type='ord',description='TGLB,TUMOR MARKER';
INSERT INTO procedure_type SET NAME='CD4 T CELLS %' ,lab_id='1',procedure_code='TH',procedure_type='ord',description='CD4 T CELLS %';
INSERT INTO procedure_type SET NAME='CD4 T CELLS ABS' ,lab_id='1',procedure_code='THAB',procedure_type='ord',description='CD4 T CELLS ABS';
INSERT INTO procedure_type SET NAME='THALLIUM, RANDOM UR' ,lab_id='1',procedure_code='THAL1',procedure_type='ord',description='THALLIUM, RANDOM UR';
INSERT INTO procedure_type SET NAME='CANNABINOIDS SCRN. UR.' ,lab_id='1',procedure_code='THC',procedure_type='ord',description='CANNABINOIDS SCRN. UR.';
INSERT INTO procedure_type SET NAME='THC CONFIRMATION' ,lab_id='1',procedure_code='THCC',procedure_type='ord',description='THC CONFIRMATION';
INSERT INTO procedure_type SET NAME='TOTAL HDL-CHOL DIR' ,lab_id='1',procedure_code='THDL',procedure_type='ord',description='TOTAL HDL-CHOL DIR';
INSERT INTO procedure_type SET NAME='THEOPHYLLINE' ,lab_id='1',procedure_code='THEO',procedure_type='ord',description='THEOPHYLLINE';
INSERT INTO procedure_type SET NAME='THREONINE' ,lab_id='1',procedure_code='THREO',procedure_type='ord',description='THREONINE';
INSERT INTO procedure_type SET NAME='TH/TO AUTOANTIBODY' ,lab_id='1',procedure_code='THTO',procedure_type='ord',description='TH/TO AUTOANTIBODY';
INSERT INTO procedure_type SET NAME='INSPIRATORY TIME' ,lab_id='1',procedure_code='TI',procedure_type='ord',description='INSPIRATORY TIME';
INSERT INTO procedure_type SET NAME='TICARCILLIN' ,lab_id='1',procedure_code='TICAR',procedure_type='ord',description='TICARCILLIN';
INSERT INTO procedure_type SET NAME='TICAR CLAV' ,lab_id='1',procedure_code='TICCLA',procedure_type='ord',description='TICAR CLAV';
INSERT INTO procedure_type SET NAME='TETANUS ID' ,lab_id='1',procedure_code='TID',procedure_type='ord',description='TETANUS ID';
INSERT INTO procedure_type SET NAME='IDL CHOLESTEROL' ,lab_id='1',procedure_code='TIDL',procedure_type='ord',description='IDL CHOLESTEROL';
INSERT INTO procedure_type SET NAME='TIGECYCLINE' ,lab_id='1',procedure_code='TIG',procedure_type='ord',description='TIGECYCLINE';
INSERT INTO procedure_type SET NAME='TOXOPLASMA IGM' ,lab_id='1',procedure_code='TIGM',procedure_type='ord',description='TOXOPLASMA IGM';
INSERT INTO procedure_type SET NAME='BASELINE (HBT)' ,lab_id='1',procedure_code='TIN',procedure_type='ord',description='BASELINE (HBT)';
INSERT INTO procedure_type SET NAME='10 MIN. (HBT)' ,lab_id='1',procedure_code='TIN1',procedure_type='ord',description='10 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='180 MIN. (HBT)' ,lab_id='1',procedure_code='TIN10',procedure_type='ord',description='180 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='TIME (HBT)' ,lab_id='1',procedure_code='TIN11',procedure_type='ord',description='TIME (HBT)';
INSERT INTO procedure_type SET NAME='TIME (HBT)' ,lab_id='1',procedure_code='TIN12',procedure_type='ord',description='TIME (HBT)';
INSERT INTO procedure_type SET NAME='20 MIN. (HBT)' ,lab_id='1',procedure_code='TIN2',procedure_type='ord',description='20 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='30 MIN. (HBT)' ,lab_id='1',procedure_code='TIN3',procedure_type='ord',description='30 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='40 MIN. (HBT)' ,lab_id='1',procedure_code='TIN4',procedure_type='ord',description='40 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='50 MIN. (HBT)' ,lab_id='1',procedure_code='TIN5',procedure_type='ord',description='50 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='60 MIN. (HBT)' ,lab_id='1',procedure_code='TIN6',procedure_type='ord',description='60 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='90 MIN. (HBT)' ,lab_id='1',procedure_code='TIN7',procedure_type='ord',description='90 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='120 MIN. (HBT)' ,lab_id='1',procedure_code='TIN8',procedure_type='ord',description='120 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='150 MIN. (HBT)' ,lab_id='1',procedure_code='TIN9',procedure_type='ord',description='150 MIN. (HBT)';
INSERT INTO procedure_type SET NAME='Antibody Titer' ,lab_id='1',procedure_code='TITR1',procedure_type='ord',description='Antibody Titer';
INSERT INTO procedure_type SET NAME='Antibody Titer' ,lab_id='1',procedure_code='TITR2',procedure_type='ord',description='Antibody Titer';
INSERT INTO procedure_type SET NAME='TOTAL LDL-CHOL DIR' ,lab_id='1',procedure_code='TLDL',procedure_type='ord',description='TOTAL LDL-CHOL DIR';
INSERT INTO procedure_type SET NAME='Lp(a) CHOLESTEROL' ,lab_id='1',procedure_code='TLP',procedure_type='ord',description='Lp(a) CHOLESTEROL';
INSERT INTO procedure_type SET NAME='MNC PER KG' ,lab_id='1',procedure_code='TMNCKG',procedure_type='ord',description='MNC PER KG';
INSERT INTO procedure_type SET NAME='TOTAL NCC/KG' ,lab_id='1',procedure_code='TNCKG',procedure_type='ord',description='TOTAL NCC/KG';
INSERT INTO procedure_type SET NAME='TNC TRANSPLANTED' ,lab_id='1',procedure_code='TNCTX',procedure_type='ord',description='TNC TRANSPLANTED';
INSERT INTO procedure_type SET NAME='NOTE' ,lab_id='1',procedure_code='TNDT',procedure_type='ord',description='NOTE';
INSERT INTO procedure_type SET NAME='TOBRAMYCIN' ,lab_id='1',procedure_code='TOB',procedure_type='ord',description='TOBRAMYCIN';
INSERT INTO procedure_type SET NAME='TOCOPH. BETA GAMMA' ,lab_id='1',procedure_code='TOBG',procedure_type='ord',description='TOCOPH. BETA GAMMA';
INSERT INTO procedure_type SET NAME='TOBRAMYCIN, PEAK' ,lab_id='1',procedure_code='TOBPK',procedure_type='ord',description='TOBRAMYCIN, PEAK';
INSERT INTO procedure_type SET NAME='TOBRAMYCIN' ,lab_id='1',procedure_code='TOBRA',procedure_type='ord',description='TOBRAMYCIN';
INSERT INTO procedure_type SET NAME='TOBRAMYCIN, RANDOM' ,lab_id='1',procedure_code='TOBRN',procedure_type='ord',description='TOBRAMYCIN, RANDOM';
INSERT INTO procedure_type SET NAME='TOBRAMYCIN, TROUGH' ,lab_id='1',procedure_code='TOBTH',procedure_type='ord',description='TOBRAMYCIN, TROUGH';
INSERT INTO procedure_type SET NAME='TOTAL CELLS COUNTED' ,lab_id='1',procedure_code='TOCC',procedure_type='ord',description='TOTAL CELLS COUNTED';
INSERT INTO procedure_type SET NAME='TOPIRAMATE' ,lab_id='1',procedure_code='TOPA',procedure_type='ord',description='TOPIRAMATE';
INSERT INTO procedure_type SET NAME='TOTAL NORE AND EPI' ,lab_id='1',procedure_code='TOTAL',procedure_type='ord',description='TOTAL NORE AND EPI';
INSERT INTO procedure_type SET NAME='TOXOPLASMA IGG' ,lab_id='1',procedure_code='TOXO',procedure_type='ord',description='TOXOPLASMA IGG';
INSERT INTO procedure_type SET NAME='TOXOCARA ANTIBODY' ,lab_id='1',procedure_code='TOXOC',procedure_type='ord',description='TOXOCARA ANTIBODY';
INSERT INTO procedure_type SET NAME='TOTAL PROTEIN' ,lab_id='1',procedure_code='TP',procedure_type='ord',description='TOTAL PROTEIN';
INSERT INTO procedure_type SET NAME='TREPONEMAL ANTIBODY' ,lab_id='1',procedure_code='TPAB',procedure_type='ord',description='TREPONEMAL ANTIBODY';
INSERT INTO procedure_type SET NAME='PROTEIN, TOTAL, BF' ,lab_id='1',procedure_code='TPBF',procedure_type='ord',description='PROTEIN, TOTAL, BF';
INSERT INTO procedure_type SET NAME='PROTEIN, TOTAL, CSF' ,lab_id='1',procedure_code='TPCF',procedure_type='ord',description='PROTEIN, TOTAL, CSF';
INSERT INTO procedure_type SET NAME='TOTAL PROTEIN' ,lab_id='1',procedure_code='TPE',procedure_type='ord',description='TOTAL PROTEIN';
INSERT INTO procedure_type SET NAME='PLT IN BAG X10E11/ML' ,lab_id='1',procedure_code='TPLTBG',procedure_type='ord',description='PLT IN BAG X10E11/ML';
INSERT INTO procedure_type SET NAME='THIOPURINE(TPMT),RBC' ,lab_id='1',procedure_code='TPMT',procedure_type='ord',description='THIOPURINE(TPMT),RBC';
INSERT INTO procedure_type SET NAME='TPMT GENOTYPE' ,lab_id='1',procedure_code='TPMTGN',procedure_type='ord',description='TPMT GENOTYPE';
INSERT INTO procedure_type SET NAME='PROT CONCENTRATION, UR' ,lab_id='1',procedure_code='TPUR',procedure_type='ord',description='PROT CONCENTRATION, UR';
INSERT INTO procedure_type SET NAME='TEST:' ,lab_id='1',procedure_code='TQ',procedure_type='ord',description='TEST:';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 11;14' ,lab_id='1',procedure_code='TR1114',procedure_type='ord',description='TRANSLOCATION 11;14';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 12;21' ,lab_id='1',procedure_code='TR1221',procedure_type='ord',description='TRANSLOCATION 12;21';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 14;16' ,lab_id='1',procedure_code='TR1416',procedure_type='ord',description='TRANSLOCATION 14;16';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 15;17' ,lab_id='1',procedure_code='TR1517',procedure_type='ord',description='TRANSLOCATION 15;17';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 4;14' ,lab_id='1',procedure_code='TR414',procedure_type='ord',description='TRANSLOCATION 4;14';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 8;14' ,lab_id='1',procedure_code='TR814',procedure_type='ord',description='TRANSLOCATION 8;14';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 8;21' ,lab_id='1',procedure_code='TR821',procedure_type='ord',description='TRANSLOCATION 8;21';
INSERT INTO procedure_type SET NAME='TRANSLOCATION 9,22' ,lab_id='1',procedure_code='TR922',procedure_type='ord',description='TRANSLOCATION 9,22';
INSERT INTO procedure_type SET NAME='TARTRATE RES ACID PTASE STAIN' ,lab_id='1',procedure_code='TRAP',procedure_type='ord',description='TARTRATE RES ACID PTASE STAIN';
INSERT INTO procedure_type SET NAME='TRAP ENZYMATIC' ,lab_id='1',procedure_code='TRAPE',procedure_type='ord',description='TRAP ENZYMATIC';
INSERT INTO procedure_type SET NAME='TRAZODONE' ,lab_id='1',procedure_code='TRAZ',procedure_type='ord',description='TRAZODONE';
INSERT INTO procedure_type SET NAME='TRANSFERRIN' ,lab_id='1',procedure_code='TRFN',procedure_type='ord',description='TRANSFERRIN';
INSERT INTO procedure_type SET NAME='TRICHINELLA IgG AB' ,lab_id='1',procedure_code='TRICG',procedure_type='ord',description='TRICHINELLA IgG AB';
INSERT INTO procedure_type SET NAME='TRIGLYCERIDES-DIRECT' ,lab_id='1',procedure_code='TRID',procedure_type='ord',description='TRIGLYCERIDES-DIRECT';
INSERT INTO procedure_type SET NAME='TRIGLYCERIDE' ,lab_id='1',procedure_code='TRIG',procedure_type='ord',description='TRIGLYCERIDE';
INSERT INTO procedure_type SET NAME='TRIMETHOPRIM' ,lab_id='1',procedure_code='TRIM',procedure_type='ord',description='TRIMETHOPRIM';
INSERT INTO procedure_type SET NAME='TRISOMY 10' ,lab_id='1',procedure_code='TRIS10',procedure_type='ord',description='TRISOMY 10';
INSERT INTO procedure_type SET NAME='TRISOMY 11' ,lab_id='1',procedure_code='TRIS11',procedure_type='ord',description='TRISOMY 11';
INSERT INTO procedure_type SET NAME='TRISOMY 12' ,lab_id='1',procedure_code='TRIS12',procedure_type='ord',description='TRISOMY 12';
INSERT INTO procedure_type SET NAME='TRISOMY 17' ,lab_id='1',procedure_code='TRIS17',procedure_type='ord',description='TRISOMY 17';
INSERT INTO procedure_type SET NAME='TRISOMY 21' ,lab_id='1',procedure_code='TRIS21',procedure_type='ord',description='TRISOMY 21';
INSERT INTO procedure_type SET NAME='TRISOMY 3' ,lab_id='1',procedure_code='TRIS3',procedure_type='ord',description='TRISOMY 3';
INSERT INTO procedure_type SET NAME='TRISOMY 4' ,lab_id='1',procedure_code='TRIS4',procedure_type='ord',description='TRISOMY 4';
INSERT INTO procedure_type SET NAME='TRISOMY 6' ,lab_id='1',procedure_code='TRIS6',procedure_type='ord',description='TRISOMY 6';
INSERT INTO procedure_type SET NAME='TRISOMY 7' ,lab_id='1',procedure_code='TRIS7',procedure_type='ord',description='TRISOMY 7';
INSERT INTO procedure_type SET NAME='TRISOMY 8' ,lab_id='1',procedure_code='TRIS8',procedure_type='ord',description='TRISOMY 8';
INSERT INTO procedure_type SET NAME='TRISOMY 9' ,lab_id='1',procedure_code='TRIS9',procedure_type='ord',description='TRISOMY 9';
INSERT INTO procedure_type SET NAME='TROPONIN MT ZION' ,lab_id='1',procedure_code='TRIZ',procedure_type='ord',description='TROPONIN MT ZION';
INSERT INTO procedure_type SET NAME='THROMBOSIS RISK MUTATIONS' ,lab_id='1',procedure_code='TRM',procedure_type='ord',description='THROMBOSIS RISK MUTATIONS';
INSERT INTO procedure_type SET NAME='TROVAFLOXACIN' ,lab_id='1',procedure_code='TROVA',procedure_type='ord',description='TROVAFLOXACIN';
INSERT INTO procedure_type SET NAME='TROPONIN I' ,lab_id='1',procedure_code='TRPI',procedure_type='ord',description='TROPONIN I';
INSERT INTO procedure_type SET NAME='POC TROPONIN' ,lab_id='1',procedure_code='TRPPC',procedure_type='ord',description='POC TROPONIN';
INSERT INTO procedure_type SET NAME='Trnsf Rxn Interp (MD):' ,lab_id='1',procedure_code='TRXNPF',procedure_type='ord',description='Trnsf Rxn Interp (MD):';
INSERT INTO procedure_type SET NAME='TRYPTOPHANE' ,lab_id='1',procedure_code='TRYPTO',procedure_type='ord',description='TRYPTOPHANE';
INSERT INTO procedure_type SET NAME='CD8 T CELLS ABS' ,lab_id='1',procedure_code='TSAB',procedure_type='ord',description='CD8 T CELLS ABS';
INSERT INTO procedure_type SET NAME='CYSTICERCUS AB, ELISA' ,lab_id='1',procedure_code='TSABS',procedure_type='ord',description='CYSTICERCUS AB, ELISA';
INSERT INTO procedure_type SET NAME='THERMOACT. SACCHARI' ,lab_id='1',procedure_code='TSAC',procedure_type='ord',description='THERMOACT. SACCHARI';
INSERT INTO procedure_type SET NAME='CYSTICERCUS IGG WB' ,lab_id='1',procedure_code='TSAS',procedure_type='ord',description='CYSTICERCUS IGG WB';
INSERT INTO procedure_type SET NAME='CYSTICERCUS BANDS:' ,lab_id='1',procedure_code='TSAWB',procedure_type='ord',description='CYSTICERCUS BANDS:';
INSERT INTO procedure_type SET NAME='CYSTICERCUS AB (CSF)' ,lab_id='1',procedure_code='TSCSF',procedure_type='ord',description='CYSTICERCUS AB (CSF)';
INSERT INTO procedure_type SET NAME='CYSTICER CSF BANDS:' ,lab_id='1',procedure_code='TSCWB',procedure_type='ord',description='CYSTICER CSF BANDS:';
INSERT INTO procedure_type SET NAME='CD7 NEG T CELLS %' ,lab_id='1',procedure_code='TSEZ',procedure_type='ord',description='CD7 NEG T CELLS %';
INSERT INTO procedure_type SET NAME='CD7 NEG T HELPER %' ,lab_id='1',procedure_code='TSEZ4',procedure_type='ord',description='CD7 NEG T HELPER %';
INSERT INTO procedure_type SET NAME='CD7 NEG T HELPER ABS' ,lab_id='1',procedure_code='TSEZ4A',procedure_type='ord',description='CD7 NEG T HELPER ABS';
INSERT INTO procedure_type SET NAME='CD7 NEG T CELLS ABS' ,lab_id='1',procedure_code='TSEZA',procedure_type='ord',description='CD7 NEG T CELLS ABS';
INSERT INTO procedure_type SET NAME='TSH' ,lab_id='1',procedure_code='TSH',procedure_type='ord',description='TSH';
INSERT INTO procedure_type SET NAME='TSH RECEPTOR AB' ,lab_id='1',procedure_code='TSHR',procedure_type='ord',description='TSH RECEPTOR AB';
INSERT INTO procedure_type SET NAME='TSI' ,lab_id='1',procedure_code='TSI',procedure_type='ord',description='TSI';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, TOTAL' ,lab_id='1',procedure_code='TSTT',procedure_type='ord',description='TESTOSTERONE, TOTAL';
INSERT INTO procedure_type SET NAME='CD8 T CELLS %' ,lab_id='1',procedure_code='TSUP',procedure_type='ord',description='CD8 T CELLS %';
INSERT INTO procedure_type SET NAME='CYSTICERCUS IGG WB' ,lab_id='1',procedure_code='TSWB',procedure_type='ord',description='CYSTICERCUS IGG WB';
INSERT INTO procedure_type SET NAME='THROMBIN TIME' ,lab_id='1',procedure_code='TT',procedure_type='ord',description='THROMBIN TIME';
INSERT INTO procedure_type SET NAME='T4, TOTAL' ,lab_id='1',procedure_code='TT4',procedure_type='ord',description='T4, TOTAL';
INSERT INTO procedure_type SET NAME='TESTOSTERONE, TOTAL' ,lab_id='1',procedure_code='TTES',procedure_type='ord',description='TESTOSTERONE, TOTAL';
INSERT INTO procedure_type SET NAME='TISS.TRANSGLUTAMIN.IgA' ,lab_id='1',procedure_code='TTGT',procedure_type='ord',description='TISS.TRANSGLUTAMIN.IgA';
INSERT INTO procedure_type SET NAME='TISS.TRANSGLUTAMIN.IgG' ,lab_id='1',procedure_code='TTGTGG',procedure_type='ord',description='TISS.TRANSGLUTAMIN.IgG';
INSERT INTO procedure_type SET NAME='TTL NON-HDL CHOL' ,lab_id='1',procedure_code='TTLN',procedure_type='ord',description='TTL NON-HDL CHOL';
INSERT INTO procedure_type SET NAME='TETANUS TOXOID STIM' ,lab_id='1',procedure_code='TTOX',procedure_type='ord',description='TETANUS TOXOID STIM';
INSERT INTO procedure_type SET NAME='TETRACARBOXYLPORPHYRINS' ,lab_id='1',procedure_code='TTPE',procedure_type='ord',description='TETRACARBOXYLPORPHYRINS';
INSERT INTO procedure_type SET NAME='COPROPORPHYRIN' ,lab_id='1',procedure_code='TTPU',procedure_type='ord',description='COPROPORPHYRIN';
INSERT INTO procedure_type SET NAME='COPROPORPHYRIN' ,lab_id='1',procedure_code='TTPUR',procedure_type='ord',description='COPROPORPHYRIN';
INSERT INTO procedure_type SET NAME='TRIENE TETRAENE RATIO' ,lab_id='1',procedure_code='TTR',procedure_type='ord',description='TRIENE TETRAENE RATIO';
INSERT INTO procedure_type SET NAME='UniCAP TRYPTASE' ,lab_id='1',procedure_code='TTRYP',procedure_type='ord',description='UniCAP TRYPTASE';
INSERT INTO procedure_type SET NAME='TUBE NUMBER' ,lab_id='1',procedure_code='TUBN',procedure_type='ord',description='TUBE NUMBER';
INSERT INTO procedure_type SET NAME='TULAREMIA AGGLUTININS' ,lab_id='1',procedure_code='TULA',procedure_type='ord',description='TULAREMIA AGGLUTININS';
INSERT INTO procedure_type SET NAME='TOTAL VOLUME COLLECTED' ,lab_id='1',procedure_code='TV',procedure_type='ord',description='TOTAL VOLUME COLLECTED';
INSERT INTO procedure_type SET NAME='TOTAL 24 HR. URINE:' ,lab_id='1',procedure_code='TV17HS',procedure_type='ord',description='TOTAL 24 HR. URINE:';
INSERT INTO procedure_type SET NAME='TOTAL VIABLE NCC/KG' ,lab_id='1',procedure_code='TVNCKG',procedure_type='ord',description='TOTAL VIABLE NCC/KG';
INSERT INTO procedure_type SET NAME='THERMOACT. VULGARIS' ,lab_id='1',procedure_code='TVUL',procedure_type='ord',description='THERMOACT. VULGARIS';
INSERT INTO procedure_type SET NAME='TOTAL CELLS STORED' ,lab_id='1',procedure_code='TWBC',procedure_type='ord',description='TOTAL CELLS STORED';
INSERT INTO procedure_type SET NAME='TOXOPLASMA CSF IGG' ,lab_id='1',procedure_code='TXCG',procedure_type='ord',description='TOXOPLASMA CSF IGG';
INSERT INTO procedure_type SET NAME='TOXOPLASMA CSF IGM' ,lab_id='1',procedure_code='TXCM',procedure_type='ord',description='TOXOPLASMA CSF IGM';
INSERT INTO procedure_type SET NAME='TRANSPLANT DATE' ,lab_id='1',procedure_code='TXDATE',procedure_type='ord',description='TRANSPLANT DATE';
INSERT INTO procedure_type SET NAME='TOXOPLASMA GONDII, PCR' ,lab_id='1',procedure_code='TXPCR',procedure_type='ord',description='TOXOPLASMA GONDII, PCR';
INSERT INTO procedure_type SET NAME='ABO/RH' ,lab_id='1',procedure_code='TYPE',procedure_type='ord',description='ABO/RH';
INSERT INTO procedure_type SET NAME='TYROSINE' ,lab_id='1',procedure_code='TYRO',procedure_type='ord',description='TYROSINE';
INSERT INTO procedure_type SET NAME='TYROSINE' ,lab_id='1',procedure_code='TYSINE',procedure_type='ord',description='TYROSINE';
INSERT INTO procedure_type SET NAME='U3 RNP AUTOANTIBODY' ,lab_id='1',procedure_code='U3RNP',procedure_type='ord',description='U3 RNP AUTOANTIBODY';
INSERT INTO procedure_type SET NAME='UA MICRO ADD\'L INFO' ,lab_id='1',procedure_code='UADI',procedure_type='ord',description='UA MICRO ADD\'L INFO';
INSERT INTO procedure_type SET NAME='AMPHETAMINES' ,lab_id='1',procedure_code='UAMP',procedure_type='ord',description='AMPHETAMINES';
INSERT INTO procedure_type SET NAME='URIC ACID PER DAY UR' ,lab_id='1',procedure_code='UAUD',procedure_type='ord',description='URIC ACID PER DAY UR';
INSERT INTO procedure_type SET NAME='URIC ACID, URINE' ,lab_id='1',procedure_code='UAUR',procedure_type='ord',description='URIC ACID, URINE';
INSERT INTO procedure_type SET NAME='BLOOD BANK' ,lab_id='1',procedure_code='UBBS',procedure_type='ord',description='BLOOD BANK';
INSERT INTO procedure_type SET NAME='CARNITINE, ESTERS UR' ,lab_id='1',procedure_code='UCAE',procedure_type='ord',description='CARNITINE, ESTERS UR';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL. AMNIO' ,lab_id='1',procedure_code='UCAMN',procedure_type='ord',description='CHROMOSOME ANAL. AMNIO';
INSERT INTO procedure_type SET NAME='CARNITINE, TOTAL UR' ,lab_id='1',procedure_code='UCAR',procedure_type='ord',description='CARNITINE, TOTAL UR';
INSERT INTO procedure_type SET NAME='CHROMOS.ANAL.HIGH-RES' ,lab_id='1',procedure_code='UCBDHR',procedure_type='ord',description='CHROMOS.ANAL.HIGH-RES';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANALYSIS' ,lab_id='1',procedure_code='UCBLDR',procedure_type='ord',description='CHROMOSOME ANALYSIS';
INSERT INTO procedure_type SET NAME='TISSUE CULTURE CRYO' ,lab_id='1',procedure_code='UCCRYO',procedure_type='ord',description='TISSUE CULTURE CRYO';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL. CVS' ,lab_id='1',procedure_code='UCCVS',procedure_type='ord',description='CHROMOSOME ANAL. CVS';
INSERT INTO procedure_type SET NAME='CODEINE' ,lab_id='1',procedure_code='UCDN',procedure_type='ord',description='CODEINE';
INSERT INTO procedure_type SET NAME='CHROM. ANAL. DNA PREP' ,lab_id='1',procedure_code='UCDNAE',procedure_type='ord',description='CHROM. ANAL. DNA PREP';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL. FB' ,lab_id='1',procedure_code='UCFB',procedure_type='ord',description='CHROMOSOME ANAL. FB';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL. FISH' ,lab_id='1',procedure_code='UCFISH',procedure_type='ord',description='CHROMOSOME ANAL. FISH';
INSERT INTO procedure_type SET NAME='CIPROFLOXACIN' ,lab_id='1',procedure_code='UCIPRO',procedure_type='ord',description='CIPROFLOXACIN';
INSERT INTO procedure_type SET NAME='CITRATE, URINE' ,lab_id='1',procedure_code='UCIT',procedure_type='ord',description='CITRATE, URINE';
INSERT INTO procedure_type SET NAME='CORRECTED UREA CLEARANCE' ,lab_id='1',procedure_code='UCORC',procedure_type='ord',description='CORRECTED UREA CLEARANCE';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL. UMBIL' ,lab_id='1',procedure_code='UCPUB',procedure_type='ord',description='CHROMOSOME ANAL. UMBIL';
INSERT INTO procedure_type SET NAME='CREATININE (SK),UR' ,lab_id='1',procedure_code='UCRE',procedure_type='ord',description='CREATININE (SK),UR';
INSERT INTO procedure_type SET NAME='CREATINE, URINE' ,lab_id='1',procedure_code='UCRT',procedure_type='ord',description='CREATINE, URINE';
INSERT INTO procedure_type SET NAME='Urea Clot Solubility Assay' ,lab_id='1',procedure_code='UCSOL',procedure_type='ord',description='Urea Clot Solubility Assay';
INSERT INTO procedure_type SET NAME='CHROMOSOME ANAL.TISSUE' ,lab_id='1',procedure_code='UCTIS',procedure_type='ord',description='CHROMOSOME ANAL.TISSUE';
INSERT INTO procedure_type SET NAME='DOXYCYCLINE' ,lab_id='1',procedure_code='UDOXY',procedure_type='ord',description='DOXYCYCLINE';
INSERT INTO procedure_type SET NAME='UNFRACT HEP LEVEL' ,lab_id='1',procedure_code='UFHEP',procedure_type='ord',description='UNFRACT HEP LEVEL';
INSERT INTO procedure_type SET NAME='UFH HIGH DOSE,100IU/mL' ,lab_id='1',procedure_code='UFHHI',procedure_type='ord',description='UFH HIGH DOSE,100IU/mL';
INSERT INTO procedure_type SET NAME='UFH LOW DOSE,0.1 IU/mL' ,lab_id='1',procedure_code='UFHLO1',procedure_type='ord',description='UFH LOW DOSE,0.1 IU/mL';
INSERT INTO procedure_type SET NAME='UFH LOW DOSE,0.5 IU/mL' ,lab_id='1',procedure_code='UFHLO5',procedure_type='ord',description='UFH LOW DOSE,0.5 IU/mL';
INSERT INTO procedure_type SET NAME='UFH SRA RESULT' ,lab_id='1',procedure_code='UFHSRA',procedure_type='ord',description='UFH SRA RESULT';
INSERT INTO procedure_type SET NAME='GLUCURONOSYLTRAN 1A1' ,lab_id='1',procedure_code='UGT1A1',procedure_type='ord',description='GLUCURONOSYLTRAN 1A1';
INSERT INTO procedure_type SET NAME='HEMOGLOBIN, UNSTABLE' ,lab_id='1',procedure_code='UHGBI',procedure_type='ord',description='HEMOGLOBIN, UNSTABLE';
INSERT INTO procedure_type SET NAME='UROPORPHYRINOGEN I SYNTHASE' ,lab_id='1',procedure_code='UIS',procedure_type='ord',description='UROPORPHYRINOGEN I SYNTHASE';
INSERT INTO procedure_type SET NAME='UL54 MUTATION' ,lab_id='1',procedure_code='UL54',procedure_type='ord',description='UL54 MUTATION';
INSERT INTO procedure_type SET NAME='UL97 MUTATION' ,lab_id='1',procedure_code='UL97',procedure_type='ord',description='UL97 MUTATION';
INSERT INTO procedure_type SET NAME='UREA NITROGEN, BF' ,lab_id='1',procedure_code='UNB',procedure_type='ord',description='UREA NITROGEN, BF';
INSERT INTO procedure_type SET NAME='UNCORRECTED CLEAR.' ,lab_id='1',procedure_code='UNCL',procedure_type='ord',description='UNCORRECTED CLEAR.';
INSERT INTO procedure_type SET NAME='DONOR UNOS ID' ,lab_id='1',procedure_code='UNOSID',procedure_type='ord',description='DONOR UNOS ID';
INSERT INTO procedure_type SET NAME='UREA NIT. PER DAY,UR' ,lab_id='1',procedure_code='UNUD',procedure_type='ord',description='UREA NIT. PER DAY,UR';
INSERT INTO procedure_type SET NAME='UREA NITROGEN, URINE' ,lab_id='1',procedure_code='UNUR',procedure_type='ord',description='UREA NITROGEN, URINE';
INSERT INTO procedure_type SET NAME='OXYCODONE' ,lab_id='1',procedure_code='UOXY',procedure_type='ord',description='OXYCODONE';
INSERT INTO procedure_type SET NAME='PROTEIN CREAT RATIO' ,lab_id='1',procedure_code='UPCR',procedure_type='ord',description='PROTEIN CREAT RATIO';
INSERT INTO procedure_type SET NAME='PbG DEAMINASE, RBC' ,lab_id='1',procedure_code='UPGS',procedure_type='ord',description='PbG DEAMINASE, RBC';
INSERT INTO procedure_type SET NAME='UREA CLEARANCE' ,lab_id='1',procedure_code='URC',procedure_type='ord',description='UREA CLEARANCE';
INSERT INTO procedure_type SET NAME='URIC ACID' ,lab_id='1',procedure_code='URIC',procedure_type='ord',description='URIC ACID';
INSERT INTO procedure_type SET NAME='UROBILINOGEN' ,lab_id='1',procedure_code='UROB',procedure_type='ord',description='UROBILINOGEN';
INSERT INTO procedure_type SET NAME='UROVYSION' ,lab_id='1',procedure_code='UROV',procedure_type='ord',description='UROVYSION';
INSERT INTO procedure_type SET NAME='URR %' ,lab_id='1',procedure_code='URRP',procedure_type='ord',description='URR %';
INSERT INTO procedure_type SET NAME='UROBILINOGEN' ,lab_id='1',procedure_code='URUA',procedure_type='ord',description='UROBILINOGEN';
INSERT INTO procedure_type SET NAME='US Date or US EDD:' ,lab_id='1',procedure_code='USD',procedure_type='ord',description='US Date or US EDD:';
INSERT INTO procedure_type SET NAME='UNITS' ,lab_id='1',procedure_code='UTS',procedure_type='ord',description='UNITS';
INSERT INTO procedure_type SET NAME='VALINE' ,lab_id='1',procedure_code='VALINE',procedure_type='ord',description='VALINE';
INSERT INTO procedure_type SET NAME='VALPROIC ACID' ,lab_id='1',procedure_code='VALP',procedure_type='ord',description='VALPROIC ACID';
INSERT INTO procedure_type SET NAME='VANCOMYCIN' ,lab_id='1',procedure_code='VAN',procedure_type='ord',description='VANCOMYCIN';
INSERT INTO procedure_type SET NAME='VANCOMYCIN' ,lab_id='1',procedure_code='VANCO',procedure_type='ord',description='VANCOMYCIN';
INSERT INTO procedure_type SET NAME='VASCULAR ULTRASOUND' ,lab_id='1',procedure_code='VASUS',procedure_type='ord',description='VASCULAR ULTRASOUND';
INSERT INTO procedure_type SET NAME='VITAMIN B12' ,lab_id='1',procedure_code='VB12',procedure_type='ord',description='VITAMIN B12';
INSERT INTO procedure_type SET NAME='EBV IGA (ANTI VCA)' ,lab_id='1',procedure_code='VCAA',procedure_type='ord',description='EBV IGA (ANTI VCA)';
INSERT INTO procedure_type SET NAME='EBV IGM (ANTI VCA)' ,lab_id='1',procedure_code='VCAM',procedure_type='ord',description='EBV IGM (ANTI VCA)';
INSERT INTO procedure_type SET NAME='VITAMIN 25OH,D2' ,lab_id='1',procedure_code='VD2',procedure_type='ord',description='VITAMIN 25OH,D2';
INSERT INTO procedure_type SET NAME='VDRL, CSF' ,lab_id='1',procedure_code='VDRL',procedure_type='ord',description='VDRL, CSF';
INSERT INTO procedure_type SET NAME='VDRL, CSF, TITER' ,lab_id='1',procedure_code='VDRLT',procedure_type='ord',description='VDRL, CSF, TITER';
INSERT INTO procedure_type SET NAME='VDVT' ,lab_id='1',procedure_code='VDVT',procedure_type='ord',description='VDVT';
INSERT INTO procedure_type SET NAME='VERAPAMIL' ,lab_id='1',procedure_code='VERA',procedure_type='ord',description='VERAPAMIL';
INSERT INTO procedure_type SET NAME='VITAMIN B1' ,lab_id='1',procedure_code='VIB1',procedure_type='ord',description='VITAMIN B1';
INSERT INTO procedure_type SET NAME='VITAMIN B6' ,lab_id='1',procedure_code='VIB6',procedure_type='ord',description='VITAMIN B6';
INSERT INTO procedure_type SET NAME='VASOACTIVE INTESTINAL PEPTIDE' ,lab_id='1',procedure_code='VIP',procedure_type='ord',description='VASOACTIVE INTESTINAL PEPTIDE';
INSERT INTO procedure_type SET NAME='VISCOSITY' ,lab_id='1',procedure_code='VISB',procedure_type='ord',description='VISCOSITY';
INSERT INTO procedure_type SET NAME='VISCOSITY, SERUM' ,lab_id='1',procedure_code='VISC',procedure_type='ord',description='VISCOSITY, SERUM';
INSERT INTO procedure_type SET NAME='VITAMIN A' ,lab_id='1',procedure_code='VITA',procedure_type='ord',description='VITAMIN A';
INSERT INTO procedure_type SET NAME='ALPHA TOCOPHEROL' ,lab_id='1',procedure_code='VITEA',procedure_type='ord',description='ALPHA TOCOPHEROL';
INSERT INTO procedure_type SET NAME='VITAMIN K' ,lab_id='1',procedure_code='VITK',procedure_type='ord',description='VITAMIN K';
INSERT INTO procedure_type SET NAME='FATTY ACIDS, VERY LONG CHAIN' ,lab_id='1',procedure_code='VLCF',procedure_type='ord',description='FATTY ACIDS, VERY LONG CHAIN';
INSERT INTO procedure_type SET NAME='TOTAL VLDL-CHOL DIR' ,lab_id='1',procedure_code='VLDL',procedure_type='ord',description='TOTAL VLDL-CHOL DIR';
INSERT INTO procedure_type SET NAME='VLDL3 (Remnant Lipo)' ,lab_id='1',procedure_code='VLDL3',procedure_type='ord',description='VLDL3 (Remnant Lipo)';
INSERT INTO procedure_type SET NAME='VANILLYLMANDELIC ACID' ,lab_id='1',procedure_code='VMAU',procedure_type='ord',description='VANILLYLMANDELIC ACID';
INSERT INTO procedure_type SET NAME='VOLUME' ,lab_id='1',procedure_code='VOL',procedure_type='ord',description='VOLUME';
INSERT INTO procedure_type SET NAME='Volume infused' ,lab_id='1',procedure_code='VOLINF',procedure_type='ord',description='Volume infused';
INSERT INTO procedure_type SET NAME='TOTAL VOLUME PELLET' ,lab_id='1',procedure_code='VOLP',procedure_type='ord',description='TOTAL VOLUME PELLET';
INSERT INTO procedure_type SET NAME='VOLUME' ,lab_id='1',procedure_code='VOLS',procedure_type='ord',description='VOLUME';
INSERT INTO procedure_type SET NAME='VORICONAZOLE' ,lab_id='1',procedure_code='VORI',procedure_type='ord',description='VORICONAZOLE';
INSERT INTO procedure_type SET NAME='VORICONAZOLE LEVEL' ,lab_id='1',procedure_code='VORIC',procedure_type='ord',description='VORICONAZOLE LEVEL';
INSERT INTO procedure_type SET NAME='AGENT TESTED' ,lab_id='1',procedure_code='VT',procedure_type='ord',description='AGENT TESTED';
INSERT INTO procedure_type SET NAME='VWF-F8 BINDING RATIO' ,lab_id='1',procedure_code='VW2NR',procedure_type='ord',description='VWF-F8 BINDING RATIO';
INSERT INTO procedure_type SET NAME='VON WILLEBRAND ANTGN' ,lab_id='1',procedure_code='VWAG',procedure_type='ord',description='VON WILLEBRAND ANTGN';
INSERT INTO procedure_type SET NAME='COAG F8 ACTIVITY' ,lab_id='1',procedure_code='VWCF',procedure_type='ord',description='COAG F8 ACTIVITY';
INSERT INTO procedure_type SET NAME='vWF-F8 BINDING RATIO' ,lab_id='1',procedure_code='VWF8',procedure_type='ord',description='vWF-F8 BINDING RATIO';
INSERT INTO procedure_type SET NAME='vWF AG' ,lab_id='1',procedure_code='VWFA',procedure_type='ord',description='vWF AG';
INSERT INTO procedure_type SET NAME='VWF ANTIGEN' ,lab_id='1',procedure_code='VWFAG',procedure_type='ord',description='VWF ANTIGEN';
INSERT INTO procedure_type SET NAME='VARICELLA CF ANTIB' ,lab_id='1',procedure_code='VZ',procedure_type='ord',description='VARICELLA CF ANTIB';
INSERT INTO procedure_type SET NAME='VARICELLA ZOSTER AB' ,lab_id='1',procedure_code='VZI',procedure_type='ord',description='VARICELLA ZOSTER AB';
INSERT INTO procedure_type SET NAME='VZ VIRUS BY PCR' ,lab_id='1',procedure_code='VZVPCR',procedure_type='ord',description='VZ VIRUS BY PCR';
INSERT INTO procedure_type SET NAME='AMIK CRIT VALUE CALL' ,lab_id='1',procedure_code='WAMICV',procedure_type='ord',description='AMIK CRIT VALUE CALL';
INSERT INTO procedure_type SET NAME='WARFARIN PHARMACOGEN' ,lab_id='1',procedure_code='WARF',procedure_type='ord',description='WARFARIN PHARMACOGEN';
INSERT INTO procedure_type SET NAME='WBC COUNT' ,lab_id='1',procedure_code='WBC',procedure_type='ord',description='WBC COUNT';
INSERT INTO procedure_type SET NAME='WBC, MANUAL' ,lab_id='1',procedure_code='WBCM',procedure_type='ord',description='WBC, MANUAL';
INSERT INTO procedure_type SET NAME='WBCS FOR SH CONTS' ,lab_id='1',procedure_code='WBCS',procedure_type='ord',description='WBCS FOR SH CONTS';
INSERT INTO procedure_type SET NAME='WHITE CELL AB SCREEN' ,lab_id='1',procedure_code='WCAX',procedure_type='ord',description='WHITE CELL AB SCREEN';
INSERT INTO procedure_type SET NAME='WBCS' ,lab_id='1',procedure_code='WCB',procedure_type='ord',description='WBCS';
INSERT INTO procedure_type SET NAME='WHITE BLOOD CELLS' ,lab_id='1',procedure_code='WCBD',procedure_type='ord',description='WHITE BLOOD CELLS';
INSERT INTO procedure_type SET NAME='WBCS' ,lab_id='1',procedure_code='WCC',procedure_type='ord',description='WBCS';
INSERT INTO procedure_type SET NAME='WBCS, SEMEN' ,lab_id='1',procedure_code='WCS',procedure_type='ord',description='WBCS, SEMEN';
INSERT INTO procedure_type SET NAME='WBCS' ,lab_id='1',procedure_code='WCU',procedure_type='ord',description='WBCS';
INSERT INTO procedure_type SET NAME='WESTERN EQUINE CF AB' ,lab_id='1',procedure_code='WEE',procedure_type='ord',description='WESTERN EQUINE CF AB';
INSERT INTO procedure_type SET NAME='WESTERN EQUINE AB' ,lab_id='1',procedure_code='WEEA',procedure_type='ord',description='WESTERN EQUINE AB';
INSERT INTO procedure_type SET NAME='WEST.EQUINE IGG AB' ,lab_id='1',procedure_code='WEEG',procedure_type='ord',description='WEST.EQUINE IGG AB';
INSERT INTO procedure_type SET NAME='W.EQUINE INTERPRET:' ,lab_id='1',procedure_code='WEEI',procedure_type='ord',description='W.EQUINE INTERPRET:';
INSERT INTO procedure_type SET NAME='W.EQUINE IGM AB' ,lab_id='1',procedure_code='WEEM',procedure_type='ord',description='W.EQUINE IGM AB';
INSERT INTO procedure_type SET NAME='WBC ESTERASE' ,lab_id='1',procedure_code='WEUA',procedure_type='ord',description='WBC ESTERASE';
INSERT INTO procedure_type SET NAME='WEST NILE CSF IgG' ,lab_id='1',procedure_code='WNCG',procedure_type='ord',description='WEST NILE CSF IgG';
INSERT INTO procedure_type SET NAME='WEST NILE CSF IgM' ,lab_id='1',procedure_code='WNCM',procedure_type='ord',description='WEST NILE CSF IgM';
INSERT INTO procedure_type SET NAME='WEST NILE VIRUS IgG' ,lab_id='1',procedure_code='WNSG',procedure_type='ord',description='WEST NILE VIRUS IgG';
INSERT INTO procedure_type SET NAME='WEST NILE VIRUS IgM' ,lab_id='1',procedure_code='WNSM',procedure_type='ord',description='WEST NILE VIRUS IgM';
INSERT INTO procedure_type SET NAME='WEST NILE VIRUS' ,lab_id='1',procedure_code='WNV',procedure_type='ord',description='WEST NILE VIRUS';
INSERT INTO procedure_type SET NAME='WNV NAT' ,lab_id='1',procedure_code='WNVN',procedure_type='ord',description='WNV NAT';
INSERT INTO procedure_type SET NAME='WBCPopS' ,lab_id='1',procedure_code='WPOPS',procedure_type='ord',description='WBCPopS';
INSERT INTO procedure_type SET NAME='RBCS, SMEAR ESTIMATE' ,lab_id='1',procedure_code='WSRB',procedure_type='ord',description='RBCS, SMEAR ESTIMATE';
INSERT INTO procedure_type SET NAME='WBCS, SMEAR ESTIMATE' ,lab_id='1',procedure_code='WSWB',procedure_type='ord',description='WBCS, SMEAR ESTIMATE';
INSERT INTO procedure_type SET NAME='WEIGHT IN KG' ,lab_id='1',procedure_code='WT',procedure_type='ord',description='WEIGHT IN KG';
INSERT INTO procedure_type SET NAME='WEIGHT OF BAG' ,lab_id='1',procedure_code='WTB',procedure_type='ord',description='WEIGHT OF BAG';
INSERT INTO procedure_type SET NAME='WEIGHT OF BAG' ,lab_id='1',procedure_code='WTBG',procedure_type='ord',description='WEIGHT OF BAG';
INSERT INTO procedure_type SET NAME='WEIGHT OF BAG+PLSMA' ,lab_id='1',procedure_code='WTBP',procedure_type='ord',description='WEIGHT OF BAG+PLSMA';
INSERT INTO procedure_type SET NAME='TOTAL WEIGHT, STOOL' ,lab_id='1',procedure_code='WTF',procedure_type='ord',description='TOTAL WEIGHT, STOOL';
INSERT INTO procedure_type SET NAME='WEIGHT OF PLASMA+BAG' ,lab_id='1',procedure_code='WTPB',procedure_type='ord',description='WEIGHT OF PLASMA+BAG';
INSERT INTO procedure_type SET NAME='MEAN BLD PRESS' ,lab_id='1',procedure_code='X',procedure_type='ord',description='MEAN BLD PRESS';
INSERT INTO procedure_type SET NAME='XANTHOCHROMIA' ,lab_id='1',procedure_code='XANC',procedure_type='ord',description='XANTHOCHROMIA';
INSERT INTO procedure_type SET NAME='CROSSMATCH COMMENT' ,lab_id='1',procedure_code='XMC',procedure_type='ord',description='CROSSMATCH COMMENT';
INSERT INTO procedure_type SET NAME='PLATELET XM EVALUATION' ,lab_id='1',procedure_code='XPLTEV',procedure_type='ord',description='PLATELET XM EVALUATION';
INSERT INTO procedure_type SET NAME='SOURCE' ,lab_id='1',procedure_code='XSRC',procedure_type='ord',description='SOURCE';
INSERT INTO procedure_type SET NAME='XX/XY PROBE' ,lab_id='1',procedure_code='XXXY',procedure_type='ord',description='XX/XY PROBE';
INSERT INTO procedure_type SET NAME='IN SITU XY HYBRIDIZ' ,lab_id='1',procedure_code='XXYY1',procedure_type='ord',description='IN SITU XY HYBRIDIZ';
INSERT INTO procedure_type SET NAME='XYLOSE' ,lab_id='1',procedure_code='XYL1',procedure_type='ord',description='XYLOSE';
INSERT INTO procedure_type SET NAME='ZAP 70 RESULT' ,lab_id='1',procedure_code='Z70R',procedure_type='ord',description='ZAP 70 RESULT';
INSERT INTO procedure_type SET NAME='ZAP 70 SOURCE' ,lab_id='1',procedure_code='Z70S',procedure_type='ord',description='ZAP 70 SOURCE';
INSERT INTO procedure_type SET NAME='ZAP 70 VIABILITY' ,lab_id='1',procedure_code='Z70V',procedure_type='ord',description='ZAP 70 VIABILITY';
INSERT INTO procedure_type SET NAME='COMMENT' ,lab_id='1',procedure_code='ZCOM',procedure_type='ord',description='COMMENT';
INSERT INTO procedure_type SET NAME='COMMENT #2' ,lab_id='1',procedure_code='ZCOM2',procedure_type='ord',description='COMMENT #2';
INSERT INTO procedure_type SET NAME='COMMENT #3' ,lab_id='1',procedure_code='ZCOM3',procedure_type='ord',description='COMMENT #3';
INSERT INTO procedure_type SET NAME='COMMENT #4' ,lab_id='1',procedure_code='ZCOM4',procedure_type='ord',description='COMMENT #4';
INSERT INTO procedure_type SET NAME='ZINC, RANDOM URINE' ,lab_id='1',procedure_code='ZIN1',procedure_type='ord',description='ZINC, RANDOM URINE';
INSERT INTO procedure_type SET NAME='ZINC,PLASMA' ,lab_id='1',procedure_code='ZINC',procedure_type='ord',description='ZINC,PLASMA';
INSERT INTO procedure_type SET NAME='ZINC,URINE 24HR' ,lab_id='1',procedure_code='ZINU',procedure_type='ord',description='ZINC,URINE 24HR';
INSERT INTO procedure_type SET NAME='ZINC PROTOPORPHYRIN' ,lab_id='1',procedure_code='ZIPP',procedure_type='ord',description='ZINC PROTOPORPHYRIN';
INSERT INTO procedure_type SET NAME='ZINC' ,lab_id='1',procedure_code='ZN',procedure_type='ord',description='ZINC';
INSERT INTO procedure_type SET NAME='ZINC PER DAY, URINE' ,lab_id='1',procedure_code='ZNU',procedure_type='ord',description='ZINC PER DAY, URINE';
INSERT INTO procedure_type SET NAME='ZONISAMIDE' ,lab_id='1',procedure_code='ZONI',procedure_type='ord',description='ZONISAMIDE';
INSERT INTO procedure_type SET NAME='ZINC PROTOPORPHYRIN' ,lab_id='1',procedure_code='ZPP',procedure_type='ord',description='ZINC PROTOPORPHYRIN';
UPDATE procedure_type SET parent=procedure_type_id;
#EndIf

#IfMissingColumn procedure_questions question_component
ALTER TABLE procedure_questions
  ADD COLUMN `question_component` varchar(255) DEFAULT NULL;
#EndIf

#IfMissingColumn procedure_order psc_hold
ALTER TABLE procedure_order
  ADD COLUMN `psc_hold`             varchar(30)       DEFAULT NULL,
  ADD COLUMN `requisition_file_url` varchar(50)       DEFAULT NULL,
  ADD COLUMN `result_file_url`      varchar(50)       DEFAULT NULL,
  ADD COLUMN `billto`               varchar(5)        DEFAULT NULL,
  ADD COLUMN `internal_comments`    text,
  ADD COLUMN `return_comments`      text,
  ADD COLUMN `ord_group`            int(10) 		  DEFAULT 0,
  ADD COLUMN `date_transmitted` 	datetime 	      DEFAULT NULL 		COMMENT 'time of order transmission, null if unsent';
#EndIf

#IfMissingColumn procedure_order_code procedure_suffix
ALTER TABLE procedure_order_code
  ADD COLUMN `procedure_suffix`     varchar(50) DEFAULT NULL,
  ADD COLUMN `diagnoses`            text        NOT NULL      COMMENT 'diagnoses and maybe other coding (e.g. ICD9:111.11)',
  ADD COLUMN `patient_instructions` text;
UPDATE procedure_order AS po, procedure_order_code AS poc
  SET poc.patient_instructions = po.patient_instructions
  WHERE po.procedure_order_id = poc.procedure_order_id;
ALTER TABLE procedure_order DROP COLUMN patient_instructions;
#EndIf

#IfMissingColumn procedure_result order_title
ALTER TABLE procedure_result
  ADD COLUMN `order_title`    varchar(255) DEFAULT NULL,
  ADD COLUMN `code_suffix`     varchar(255) DEFAULT NULL,
  ADD COLUMN `profile_title`  varchar(255) DEFAULT NULL;
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

#IfNotTable modules_settings
CREATE TABLE `modules_settings` (
  `mod_id` int(11) DEFAULT NULL,
  `fld_type` smallint(6) DEFAULT NULL COMMENT '1=>ACL,2=>preferences,3=>hooks',
  `obj_name` varchar(255) DEFAULT NULL,
  `menu_name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL
);
#EndIf

#IfNotTable modules_pref_settings
CREATE TABLE `modules_pref_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_id` int(11) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
#EndIf

#IfNotTable modules_hooks_settings
CREATE TABLE `modules_hooks_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_id` int(11) DEFAULT NULL,
  `enabled_hooks` varchar(255) DEFAULT NULL,
  `attached_to` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
#EndIf

#IfMissingColumn form_encounter n_notes
  ALTER TABLE `form_encounter` ADD COLUMN `n_notes` TEXT NOT NULL;
#EndIf