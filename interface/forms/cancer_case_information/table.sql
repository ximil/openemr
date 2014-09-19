--
-- Table structure for table `form_cancer_case_information`
--

CREATE TABLE IF NOT EXISTS `form_cancer_case_information` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) DEFAULT NULL,
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `authorized` tinyint(4) DEFAULT NULL,
  `digital_rectal_exam` varchar(255) DEFAULT NULL,
  `sextant_biopsy` varchar(255) DEFAULT NULL,
  `diagnostic_tests` varchar(255) DEFAULT NULL,  
  `diagnosis_code` varchar(255) DEFAULT NULL,
  `diagnosis_description` text,
  `chest_xray` varchar(255) DEFAULT NULL,
  `reported_symptoms` varchar(255) DEFAULT NULL,
  `plan` varchar(255) DEFAULT NULL,
  `history` varchar(255) DEFAULT NULL,
  `history_reported_symptoms` varchar(255) DEFAULT NULL,
  `findings` varchar(255) DEFAULT NULL,
  `procedure_performed` varchar(255) DEFAULT NULL,
  `treatement_provided` varchar(255) DEFAULT NULL, 
  `date` DATE DEFAULT NULL,   
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
