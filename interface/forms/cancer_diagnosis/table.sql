--
-- Table structure for table `form_cancer_diagnosis`
--

CREATE TABLE IF NOT EXISTS `form_cancer_diagnosis` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) DEFAULT NULL,
  `encounter` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `authorized` tinyint(4) DEFAULT NULL,
  `date` DATE DEFAULT NULL,  
  `primary_site` varchar(255) DEFAULT NULL,
  `primary_description` text,
  `laterality` varchar(255) DEFAULT NULL,  
  `histology` varchar(255) DEFAULT NULL,
  `histology_description` text,
  `behavior` varchar(255) DEFAULT NULL,
  `diagnostic_confirmation` varchar(255) DEFAULT NULL,
  `stage` varchar(255) DEFAULT NULL,
  `procedure_code` varchar(255) DEFAULT NULL,
  `procedure_description` text,
  `status` INT DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;
