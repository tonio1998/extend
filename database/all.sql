-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               9.1.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for extend
DROP DATABASE IF EXISTS `extend`;
CREATE DATABASE IF NOT EXISTS `extend` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `extend`;

-- Dumping structure for table extend.academic_programs
DROP TABLE IF EXISTS `academic_programs`;
CREATE TABLE IF NOT EXISTS `academic_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `program_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `major` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `degree_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Undergraduate',
  `discipline_cluster` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_instrument_template` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AACCUP_STANDARD_10',
  `standard_years` smallint unsigned NOT NULL DEFAULT '4',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `academic_programs_campus_id_foreign` (`campus_id`),
  KEY `academic_programs_department_id_foreign` (`department_id`),
  KEY `academic_programs_tenant_id_program_code_index` (`tenant_id`,`program_code`),
  KEY `academic_programs_degree_level_discipline_cluster_index` (`degree_level`,`discipline_cluster`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.academic_programs: 103 rows
DELETE FROM `academic_programs`;
/*!40000 ALTER TABLE `academic_programs` DISABLE KEYS */;
INSERT INTO `academic_programs` (`id`, `tenant_id`, `campus_id`, `department_id`, `program_code`, `program_name`, `major`, `degree_level`, `discipline_cluster`, `default_instrument_template`, `standard_years`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 17, 'BSIT', 'Bachelor of Science in Information Technology', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Equips students with comprehensive skills in enterprise software development, network infrastructure, database administration, web applications, and cyber-defense systems.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(2, 1, NULL, 17, 'BSCS', 'Bachelor of Science in Computer Science', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on computing theory, mathematical foundations, algorithm design, intelligent systems, and software engineering.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(3, 1, NULL, 17, 'BSIS', 'Bachelor of Science in Information Systems', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Bridges business management processes with technology deployment, enterprise architecture, and data analytics.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(4, 1, NULL, NULL, 'BSCpE', 'Bachelor of Science in Computer Engineering', NULL, 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Integrates electrical engineering and computer science for embedded systems, IoT sensors, hardware architecture, and robotics.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 1, NULL, 17, 'MIT', 'Master in Information Technology', NULL, 'Graduate', 'Information and Communications Technology', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Professional graduate program developing high-level IT leaders, system architects, and university ICT faculty.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(6, 1, NULL, 17, 'MSIT', 'Master of Science in Information Technology', NULL, 'Graduate', 'Information and Communications Technology', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Research-driven graduate program focusing on peer-reviewed research publications, algorithm innovation, and technology patents.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(7, 1, NULL, NULL, 'BSCE', 'Bachelor of Science in Civil Engineering', 'Structural & Construction Engineering', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Covers structural engineering, geotechnical analysis, water resources, transportation engineering, and public infrastructure design.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(8, 1, NULL, NULL, 'BSEE', 'Bachelor of Science in Electrical Engineering', 'Power Systems & Energy Management', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on power generation, grid transmission, renewable energy systems, and industrial electrical machinery.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(9, 1, NULL, NULL, 'BSME', 'Bachelor of Science in Mechanical Engineering', NULL, 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Provides foundations in thermodynamics, fluid mechanics, machine design, HVAC systems, and manufacturing robotics.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(10, 1, NULL, 19, 'BSABE', 'Bachelor of Science in Agricultural and Biosystems Engineering', 'Land and Water Resources Engineering', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Applies engineering science to agricultural production, processing of food, renewable energy, and biological materials.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(11, 1, NULL, NULL, 'BSMinE', 'Bachelor of Science in Mining Engineering', 'Mineral Processing & Sustainable Extraction', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Specialized program for Caraga region mineral extraction, mine safety engineering, geomechanics, and ecological mine rehabilitation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(12, 1, NULL, NULL, 'BSElecE', 'Bachelor of Science in Electronics Engineering', 'Telecommunications & Industrial Automation', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Specializes in telecommunications, wireless transmission, microelectronics, RF systems, and smart automation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(13, 1, NULL, 20, 'BEEd', 'Bachelor of Elementary Education', 'General Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares pedagogical experts for kindergarten and primary grade basic education teaching.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(14, 1, NULL, 20, 'BSEd-Eng', 'Bachelor of Secondary Education', 'English Language Teaching', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains secondary high school educators in English linguistics, literature pedagogy, and communication skills.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(15, 1, NULL, 20, 'BSEd-Math', 'Bachelor of Secondary Education', 'Mathematics', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Develops secondary high school educators with mastery in calculus, linear algebra, statistics, and mathematics teaching methods.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(16, 1, NULL, 20, 'BSEd-Sci', 'Bachelor of Secondary Education', 'General Sciences', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains science educators across physics, chemistry, biology, earth science, and inquiry-based STEM laboratory teaching.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(17, 1, NULL, 20, 'BSEd-Soc', 'Bachelor of Secondary Education', 'Social Studies', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares educators in history, geography, Philippine governance, world civilizations, and social development theories.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(18, 1, NULL, 20, 'BPEd', 'Bachelor of Physical Education', 'Sports & Health Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Develops leaders in athletic coaching, fitness management, physical literacy, biomechanics, and sports administration.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(19, 1, NULL, 19, 'BTLEd', 'Bachelor of Technology and Livelihood Education', 'Home Economics & Industrial Arts', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares educators in TVET, culinary arts, consumer education, handicrafts, drafting, and vocational entrepreneurship.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(20, 1, NULL, 18, 'MAEd', 'Master of Arts in Education', 'Educational Management', 'Graduate', 'Teacher Education', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Advanced postgraduate curriculum for school principals, head teachers, supervisors, and educational leaders.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(21, 1, NULL, 18, 'PhD-Ed', 'Doctor of Philosophy in Education', 'Educational Leadership and Policy Studies', 'Graduate', 'Teacher Education', 'AACCUP_LEVEL_4_DISTINCTION', 3, 1, 'Terminal doctoral degree preparing superintendent candidates, university deans, and educational policy researchers.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(22, 1, NULL, 19, 'BSBA-FM', 'Bachelor of Science in Business Administration', 'Financial Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Prepares professionals for corporate finance, banking, credit analysis, investment management, and microfinance institutions.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(23, 1, NULL, 19, 'BSBA-MM', 'Bachelor of Science in Business Administration', 'Marketing Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Develops strategic brand managers, digital marketers, market researchers, advertising executives, and sales directors.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(24, 1, NULL, 19, 'BSBA-HRM', 'Bachelor of Science in Business Administration', 'Human Resource Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on talent acquisition, compensation & benefits, organizational development, labor law, and industrial relations.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(25, 1, NULL, 19, 'BSA-Acctg', 'Bachelor of Science in Accountancy', 'CPA Licensure Track', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Rigorous CPA board preparation covering financial accounting, audit assurance, taxation, regulatory frameworks, and management advisory.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(26, 1, NULL, 19, 'BSHM', 'Bachelor of Science in Hospitality Management', 'Hotel and Restaurant Administration', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Provides world-class training in front office operations, culinary arts, banqueting, hotel accommodation, and beverage management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(27, 1, NULL, 19, 'BSTM', 'Bachelor of Science in Tourism Management', 'Sustainable Tourism & Heritage Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on sustainable eco-tourism, airline ticketing, travel operations, cultural heritage conservation, and MICE management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(28, 1, NULL, 18, 'MBA', 'Master in Business Administration', 'Strategic Management & Innovation', 'Graduate', 'Business and Management', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Executive postgraduate degree developing corporate executives, entrepreneurs, and management consultants.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(29, 1, NULL, 18, 'MPA', 'Master in Public Administration', 'Public Policy & Fiscal Administration', 'Graduate', 'Public Administration and Governance', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Designed for civil servants, local government officials, and NGO leaders focused on public policy, budgeting, and ethical governance.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(30, 1, NULL, 18, 'DPA', 'Doctor of Public Administration', 'Public Policy & Development Governance', 'Graduate', 'Public Administration and Governance', 'AACCUP_LEVEL_4_DISTINCTION', 3, 1, 'Highest academic degree in public administration, advancing regional development theories and high-level governance reforms.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(31, 1, NULL, NULL, 'BSCrim', 'Bachelor of Science in Criminology', 'Law Enforcement & Forensic Science', 'Undergraduate', 'Criminal Justice Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares licensed criminologists for the PNP, BJMP, BFP, NBI, forensic crime laboratories, and penal administration.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(32, 1, NULL, 21, 'BA-Comm', 'Bachelor of Arts in Communication', 'Broadcasting & Multi-Platform Journalism', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Develops broadcast journalists, public relations specialists, digital media content producers, and corporate communications officers.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(33, 1, NULL, 21, 'BA-PolSci', 'Bachelor of Arts in Political Science', 'Pre-Law & International Relations', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Premier pre-law program focusing on constitutional law, political philosophy, comparative government, and foreign policy.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(34, 1, NULL, NULL, 'BS-Psych', 'Bachelor of Science in Psychology', 'Clinical & Industrial Psychology', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Prepares psychometricians and counselors with competencies in behavioral assessment, psychological testing, and mental health interventions.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(35, 1, NULL, 21, 'BS-Bio', 'Bachelor of Science in Biology', 'Medical & Environmental Biology', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Rigorous pre-medicine foundation program covering microbiology, genetics, anatomy, physiology, and ecological research.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(36, 1, NULL, 21, 'BS-Math', 'Bachelor of Science in Applied Mathematics', 'Actuarial & Statistical Computing', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on numerical analysis, operations research, actuarial science, financial risk analysis, and big data modeling.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(37, 1, NULL, 19, 'BSAgri', 'Bachelor of Science in Agriculture', 'Agronomy & Sustainable Crop Production', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Specializes in soil science, crop protection, post-harvest technology, organic agriculture, and agribusiness management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(38, 1, NULL, 19, 'BAT', 'Bachelor in Agricultural Technology', 'Crop and Animal Production', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Hands-on agricultural technology training for farm mechanization, seed production, horticulture, and commercial livestock management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(39, 1, NULL, NULL, 'BSF', 'Bachelor of Science in Forestry', 'Forest Resource Management', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Covers silviculture, forest inventory, watershed management, dendrology, wildlife protection, and biodiversity conservation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(40, 1, NULL, NULL, 'BSES', 'Bachelor of Science in Environmental Science', 'Ecosystem Management', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Interdisciplinary study of ecological systems, climate mitigation, watershed conservation, and environmental policy compliance.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(41, 1, NULL, NULL, 'BSFish', 'Bachelor of Science in Fisheries', 'Aquaculture & Coastal Resource Management', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on fish hatchery management, marine aquaculture, aquatic pathology, oceanography, and post-harvest fish processing.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(42, 1, NULL, NULL, 'BS-Agroforestry', 'Bachelor of Science in Agroforestry', 'Upland Farming Systems', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Integrates trees with agricultural crops and livestock to enhance biodiversity, soil fertility, and rural economic resilience.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(43, 1, 1, 17, 'BSIT', 'Bachelor of Science in Information Technology', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Equips students with comprehensive skills in enterprise software development, network infrastructure, database administration, web applications, and cyber-defense systems.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(44, 1, 1, 17, 'BSCS', 'Bachelor of Science in Computer Science', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on computing theory, mathematical foundations, algorithm design, intelligent systems, and software engineering.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(45, 1, 1, 17, 'BSIS', 'Bachelor of Science in Information Systems', NULL, 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Bridges business management processes with technology deployment, enterprise architecture, and data analytics.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(46, 1, 1, NULL, 'BSCpE', 'Bachelor of Science in Computer Engineering', NULL, 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Integrates electrical engineering and computer science for embedded systems, IoT sensors, hardware architecture, and robotics.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(47, 1, 1, 17, 'MIT', 'Master in Information Technology', NULL, 'Graduate', 'Information and Communications Technology', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Professional graduate program developing high-level IT leaders, system architects, and university ICT faculty.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(48, 1, 1, 17, 'MSIT', 'Master of Science in Information Technology', NULL, 'Graduate', 'Information and Communications Technology', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Research-driven graduate program focusing on peer-reviewed research publications, algorithm innovation, and technology patents.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(49, 1, 1, NULL, 'BSCE', 'Bachelor of Science in Civil Engineering', 'Structural & Construction Engineering', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Covers structural engineering, geotechnical analysis, water resources, transportation engineering, and public infrastructure design.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(50, 1, 1, NULL, 'BSEE', 'Bachelor of Science in Electrical Engineering', 'Power Systems & Energy Management', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on power generation, grid transmission, renewable energy systems, and industrial electrical machinery.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(51, 1, 1, NULL, 'BSME', 'Bachelor of Science in Mechanical Engineering', NULL, 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Provides foundations in thermodynamics, fluid mechanics, machine design, HVAC systems, and manufacturing robotics.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(52, 1, 1, 19, 'BSABE', 'Bachelor of Science in Agricultural and Biosystems Engineering', 'Land and Water Resources Engineering', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Applies engineering science to agricultural production, processing of food, renewable energy, and biological materials.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(53, 1, 1, NULL, 'BSMinE', 'Bachelor of Science in Mining Engineering', 'Mineral Processing & Sustainable Extraction', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Specialized program for Caraga region mineral extraction, mine safety engineering, geomechanics, and ecological mine rehabilitation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(54, 1, 1, NULL, 'BSElecE', 'Bachelor of Science in Electronics Engineering', 'Telecommunications & Industrial Automation', 'Undergraduate', 'Engineering and Technology', 'AACCUP_STANDARD_10', 4, 1, 'Specializes in telecommunications, wireless transmission, microelectronics, RF systems, and smart automation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(55, 1, 1, 20, 'BEEd', 'Bachelor of Elementary Education', 'General Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares pedagogical experts for kindergarten and primary grade basic education teaching.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(56, 1, 1, 20, 'BSEd-Eng', 'Bachelor of Secondary Education', 'English Language Teaching', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains secondary high school educators in English linguistics, literature pedagogy, and communication skills.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(57, 1, 1, 20, 'BSEd-Math', 'Bachelor of Secondary Education', 'Mathematics', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Develops secondary high school educators with mastery in calculus, linear algebra, statistics, and mathematics teaching methods.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(58, 1, 1, 20, 'BSEd-Sci', 'Bachelor of Secondary Education', 'General Sciences', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains science educators across physics, chemistry, biology, earth science, and inquiry-based STEM laboratory teaching.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(59, 1, 1, 20, 'BSEd-Soc', 'Bachelor of Secondary Education', 'Social Studies', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares educators in history, geography, Philippine governance, world civilizations, and social development theories.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(60, 1, 1, 20, 'BPEd', 'Bachelor of Physical Education', 'Sports & Health Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Develops leaders in athletic coaching, fitness management, physical literacy, biomechanics, and sports administration.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(61, 1, 1, 19, 'BTLEd', 'Bachelor of Technology and Livelihood Education', 'Home Economics & Industrial Arts', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares educators in TVET, culinary arts, consumer education, handicrafts, drafting, and vocational entrepreneurship.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(62, 1, 1, 18, 'MAEd', 'Master of Arts in Education', 'Educational Management', 'Graduate', 'Teacher Education', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Advanced postgraduate curriculum for school principals, head teachers, supervisors, and educational leaders.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(63, 1, 1, 18, 'PhD-Ed', 'Doctor of Philosophy in Education', 'Educational Leadership and Policy Studies', 'Graduate', 'Teacher Education', 'AACCUP_LEVEL_4_DISTINCTION', 3, 1, 'Terminal doctoral degree preparing superintendent candidates, university deans, and educational policy researchers.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(64, 1, 1, 19, 'BSBA-FM', 'Bachelor of Science in Business Administration', 'Financial Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Prepares professionals for corporate finance, banking, credit analysis, investment management, and microfinance institutions.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(65, 1, 1, 19, 'BSBA-MM', 'Bachelor of Science in Business Administration', 'Marketing Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Develops strategic brand managers, digital marketers, market researchers, advertising executives, and sales directors.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(66, 1, 1, 19, 'BSBA-HRM', 'Bachelor of Science in Business Administration', 'Human Resource Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on talent acquisition, compensation & benefits, organizational development, labor law, and industrial relations.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(67, 1, 1, 19, 'BSA-Acctg', 'Bachelor of Science in Accountancy', 'CPA Licensure Track', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Rigorous CPA board preparation covering financial accounting, audit assurance, taxation, regulatory frameworks, and management advisory.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(68, 1, 1, 19, 'BSHM', 'Bachelor of Science in Hospitality Management', 'Hotel and Restaurant Administration', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Provides world-class training in front office operations, culinary arts, banqueting, hotel accommodation, and beverage management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(69, 1, 1, 19, 'BSTM', 'Bachelor of Science in Tourism Management', 'Sustainable Tourism & Heritage Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on sustainable eco-tourism, airline ticketing, travel operations, cultural heritage conservation, and MICE management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(70, 1, 1, 18, 'MBA', 'Master in Business Administration', 'Strategic Management & Innovation', 'Graduate', 'Business and Management', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Executive postgraduate degree developing corporate executives, entrepreneurs, and management consultants.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(71, 1, 1, 18, 'MPA', 'Master in Public Administration', 'Public Policy & Fiscal Administration', 'Graduate', 'Public Administration and Governance', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Designed for civil servants, local government officials, and NGO leaders focused on public policy, budgeting, and ethical governance.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(72, 1, 1, 18, 'DPA', 'Doctor of Public Administration', 'Public Policy & Development Governance', 'Graduate', 'Public Administration and Governance', 'AACCUP_LEVEL_4_DISTINCTION', 3, 1, 'Highest academic degree in public administration, advancing regional development theories and high-level governance reforms.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(73, 1, 1, NULL, 'BSCrim', 'Bachelor of Science in Criminology', 'Law Enforcement & Forensic Science', 'Undergraduate', 'Criminal Justice Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares licensed criminologists for the PNP, BJMP, BFP, NBI, forensic crime laboratories, and penal administration.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(74, 1, 1, 21, 'BA-Comm', 'Bachelor of Arts in Communication', 'Broadcasting & Multi-Platform Journalism', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Develops broadcast journalists, public relations specialists, digital media content producers, and corporate communications officers.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(75, 1, 1, 21, 'BA-PolSci', 'Bachelor of Arts in Political Science', 'Pre-Law & International Relations', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Premier pre-law program focusing on constitutional law, political philosophy, comparative government, and foreign policy.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(76, 1, 1, NULL, 'BS-Psych', 'Bachelor of Science in Psychology', 'Clinical & Industrial Psychology', 'Undergraduate', 'Humanities and Social Sciences', 'AACCUP_STANDARD_10', 4, 1, 'Prepares psychometricians and counselors with competencies in behavioral assessment, psychological testing, and mental health interventions.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(77, 1, 1, 21, 'BS-Bio', 'Bachelor of Science in Biology', 'Medical & Environmental Biology', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Rigorous pre-medicine foundation program covering microbiology, genetics, anatomy, physiology, and ecological research.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(78, 1, 1, 21, 'BS-Math', 'Bachelor of Science in Applied Mathematics', 'Actuarial & Statistical Computing', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on numerical analysis, operations research, actuarial science, financial risk analysis, and big data modeling.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(79, 1, 1, 19, 'BSAgri', 'Bachelor of Science in Agriculture', 'Agronomy & Sustainable Crop Production', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Specializes in soil science, crop protection, post-harvest technology, organic agriculture, and agribusiness management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(80, 1, 1, 19, 'BAT', 'Bachelor in Agricultural Technology', 'Crop and Animal Production', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Hands-on agricultural technology training for farm mechanization, seed production, horticulture, and commercial livestock management.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(81, 1, 1, NULL, 'BSF', 'Bachelor of Science in Forestry', 'Forest Resource Management', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Covers silviculture, forest inventory, watershed management, dendrology, wildlife protection, and biodiversity conservation.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(82, 1, 1, NULL, 'BSES', 'Bachelor of Science in Environmental Science', 'Ecosystem Management', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Interdisciplinary study of ecological systems, climate mitigation, watershed conservation, and environmental policy compliance.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(83, 1, 1, NULL, 'BSFish', 'Bachelor of Science in Fisheries', 'Aquaculture & Coastal Resource Management', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on fish hatchery management, marine aquaculture, aquatic pathology, oceanography, and post-harvest fish processing.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(84, 1, 1, NULL, 'BS-Agroforestry', 'Bachelor of Science in Agroforestry', 'Upland Farming Systems', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Integrates trees with agricultural crops and livestock to enhance biodiversity, soil fertility, and rural economic resilience.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(85, 1, 2, 19, 'BSAgri', 'Bachelor of Science in Agriculture', 'Crop Science & Organic Farming', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on sustainable food production, rural farm extension, and organic agro-ecosystems for southern Agusan del Sur.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(86, 1, 2, 19, 'BAT', 'Bachelor in Agricultural Technology', 'Farm Mechanization & Post-Harvest', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Applied agricultural technology training for southern Agusan farmers, cooperatives, and agribusiness entrepreneurs.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(87, 1, 2, NULL, 'BS-Agroforestry', 'Bachelor of Science in Agroforestry', 'Watershed & Agro-Ecological Systems', 'Undergraduate', 'Agriculture, Forestry and Fisheries', 'AACCUP_STANDARD_10', 4, 1, 'Specialized upland agroforestry and rubber/cacao agro-ecosystems for Trento and neighboring agrarian communities.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(88, 1, 2, NULL, 'BSES', 'Bachelor of Science in Environmental Science', 'Resource Conservation', 'Undergraduate', 'Natural Sciences and Environment', 'AACCUP_STANDARD_10', 4, 1, 'Environmental monitoring, marshland protection, and forest canopy conservation in the Agusan Marsh wildlife corridor.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(89, 1, 2, 20, 'BEEd', 'Bachelor of Elementary Education', 'General Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains primary and elementary educators serving rural public and indigenous schools in Trento and southern Caraga.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(90, 1, 2, 20, 'BSEd-Eng', 'Bachelor of Secondary Education', 'English Language Teaching', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares secondary school English language teachers for southern district high schools.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(91, 1, 2, 20, 'BSEd-Math', 'Bachelor of Secondary Education', 'Mathematics', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Prepares secondary school mathematics educators with modern STEM instructional methodologies.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(92, 1, 2, 17, 'BSIT', 'Bachelor of Science in Information Technology', 'Network & Systems Administration', 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Provides essential computing competencies, network operations, and local government computerization skills in Trento.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(93, 1, 2, 19, 'BSBA-FM', 'Bachelor of Science in Business Administration', 'Microfinance & Rural Banking', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Specializes in rural cooperative management, agricultural credit, and small-to-medium enterprise financial operations.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(94, 1, 3, 17, 'BSIT', 'Bachelor of Science in Information Technology', 'Web & Mobile Application Development', 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'High-demand urban IT program focusing on full-stack web engineering, mobile development, and cloud services for Bayugan City enterprises.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(95, 1, 3, 17, 'BSIS', 'Bachelor of Science in Information Systems', 'Enterprise E-Commerce Systems', 'Undergraduate', 'Information and Communications Technology', 'AACCUP_STANDARD_10', 4, 1, 'Prepares analysts in ERP platforms, retail inventory systems, and digital transactions for urban trade and commercial businesses.', '2026-09-16 04:06:48', '2026-10-26 06:46:20'),
	(96, 1, 3, 19, 'BSBA-MM', 'Bachelor of Science in Business Administration', 'Marketing & Digital Commerce', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Prepares digital marketing specialists, brand builders, and commercial retail entrepreneurs in Bayugan City.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(97, 1, 3, 19, 'BSBA-HRM', 'Bachelor of Science in Business Administration', 'Human Resource Management', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Focuses on talent acquisition, corporate human resources, labor relations, and organizational training.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(98, 1, 3, 19, 'BSHM', 'Bachelor of Science in Hospitality Management', 'Hotel & Culinary Operations', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Equips students with hands-on competencies in hotel reception, culinary management, resort operations, and event staging.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(99, 1, 3, 19, 'BSTM', 'Bachelor of Science in Tourism Management', 'Eco-Tourism & Travel Services', 'Undergraduate', 'Business and Management', 'AACCUP_STANDARD_10', 4, 1, 'Covers local eco-tourism planning, tour guiding, travel agency management, and heritage preservation in northern Agusan.', '2026-09-16 04:06:48', '2026-10-26 06:46:21'),
	(100, 1, 3, 20, 'BEEd', 'Bachelor of Elementary Education', 'General Education', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Develops basic education primary educators serving the Division of Bayugan City schools.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(101, 1, 3, 20, 'BSEd-Eng', 'Bachelor of Secondary Education', 'English Language Teaching', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains secondary school educators in modern communicative English instruction.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(102, 1, 3, 20, 'BSEd-Math', 'Bachelor of Secondary Education', 'Mathematics', 'Undergraduate', 'Teacher Education', 'AACCUP_STANDARD_10', 4, 1, 'Trains secondary mathematics educators in quantitative instruction and mathematical reasoning.', '2026-09-16 04:06:48', '2026-10-26 06:46:22'),
	(103, 1, 3, 18, 'MBA', 'Master in Business Administration', 'Executive Management & Entrepreneurship', 'Graduate', 'Business and Management', 'AACCUP_LEVEL_3_PHASE_1', 2, 1, 'Offered as an executive weekend graduate program for Bayugan City professionals, bank managers, and government leaders.', '2026-09-16 04:06:48', '2026-10-26 06:46:21');
/*!40000 ALTER TABLE `academic_programs` ENABLE KEYS */;

-- Dumping structure for table extend.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.cache: 0 rows
DELETE FROM `cache`;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;

-- Dumping structure for table extend.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.cache_locks: 0 rows
DELETE FROM `cache_locks`;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;

-- Dumping structure for table extend.designations
DROP TABLE IF EXISTS `designations`;
CREATE TABLE IF NOT EXISTS `designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Administrative',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designations_tenant_id_index` (`tenant_id`),
  KEY `designations_code_index` (`code`),
  KEY `designations_name_index` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.designations: 21 rows
DELETE FROM `designations`;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` (`id`, `tenant_id`, `code`, `name`, `category`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 9, 'PROG', 'Programmer', 'Technical', 'Software and institutional system application developer', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(2, 9, 'FAC', 'Faculty', 'Academic', 'Instructional teaching personnel and professors', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(3, 9, 'DH', 'Department Head', 'Executive', 'Head of academic department or administrative office', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(4, 9, 'DEAN', 'Dean', 'Executive', 'College Dean overseeing collegiate faculty and programs', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(5, 9, 'MIS-DIR', 'Management Information Systems Director', 'Executive', 'Director of institutional MIS and ICT services', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(6, 9, 'PRES', 'SUC President III', 'Executive', 'University President and Chief Executive Officer', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(7, 9, 'CAMP-DIR', 'Campus Director', 'Executive', 'Director overseeing branch campus operations', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(8, 9, 'BAC-CHAIR', 'Bids & Awards Committee Chairperson', 'Administrative', 'Chairperson of institutional procurement and BAC', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(9, 9, 'AO', 'Administrative Officer', 'Administrative', 'General institutional administrative officer', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(10, 9, 'AIDE-VI', 'Administrative Aide VI', 'Administrative', 'Office clerical and clerical support aide', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(11, 9, 'CISA', 'Campus Information Systems Administrator', 'Technical', 'Branch campus ICT and systems administrator', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(12, 9, 'PC', 'Property Custodian', 'Administrative', 'Supply, assets, and institutional property custodian', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(13, 9, 'VPL', 'Visiting Professional Lecturer', 'Academic', 'Part-time or visiting lecturer', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(14, 9, 'RES-COORD', 'Research Coordinator', 'Academic', 'Coordinator of institutional research and development', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(15, 9, 'QA-LEAD', 'Quality Assurance Lead', 'Administrative', 'Institutional quality management and accreditation lead', 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(16, 9, NULL, 'Campus Director, Trento Campus', 'Administrative', NULL, 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(17, 9, NULL, 'Institutional Officer', 'Administrative', NULL, 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(18, 9, NULL, 'Platform Super Administrator / Chief Enterprise Architect', 'Administrative', NULL, 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(19, 9, NULL, 'Property Custodian / Laboratory Aide', 'Administrative', NULL, 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(20, 9, NULL, 'Campus Information Systems Administrator', 'Administrative', NULL, 'active', '2026-09-16 13:02:24', '2026-09-16 13:02:24', NULL),
	(21, 9, 'PROG', 'Programmer', 'Technical', 'Programmer', 'active', '2026-09-16 13:05:05', '2026-09-16 13:05:05', NULL);
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;

-- Dumping structure for table extend.dss_evaluations
DROP TABLE IF EXISTS `dss_evaluations`;
CREATE TABLE IF NOT EXISTS `dss_evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `evaluation_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Procurement Supplier Selection',
  `model_type` enum('AHP','TOPSIS','Hybrid-AHP-TOPSIS') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hybrid-AHP-TOPSIS',
  `criteria_data` json NOT NULL,
  `pairwise_matrix` json DEFAULT NULL,
  `calculated_weights` json DEFAULT NULL,
  `consistency_ratio` decimal(8,4) DEFAULT NULL,
  `is_consistent` tinyint(1) NOT NULL DEFAULT '1',
  `alternatives_data` json NOT NULL,
  `decision_matrix` json DEFAULT NULL,
  `topsis_scores` json DEFAULT NULL,
  `recommended_alternative` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `executive_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dss_evaluations_evaluation_code_unique` (`evaluation_code`),
  KEY `dss_evaluations_tenant_id_foreign` (`tenant_id`),
  KEY `dss_evaluations_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.dss_evaluations: 1 rows
DELETE FROM `dss_evaluations`;
/*!40000 ALTER TABLE `dss_evaluations` DISABLE KEYS */;
INSERT INTO `dss_evaluations` (`id`, `tenant_id`, `user_id`, `evaluation_code`, `title`, `category`, `model_type`, `criteria_data`, `pairwise_matrix`, `calculated_weights`, `consistency_ratio`, `is_consistent`, `alternatives_data`, `decision_matrix`, `topsis_scores`, `recommended_alternative`, `executive_summary`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'MCDA-ADSSU-2026', 'FY 2026 Agricultural Drone & Smart Sensors Procurement Multi-Criteria Evaluation', 'Procurement Supplier Selection', 'Hybrid-AHP-TOPSIS', '["Cost Efficiency & Price Competitiveness", "Technical Specifications & Sensor Precision", "Delivery Lead Time & Warranty SLA", "Vendor Track Record & Local Support"]', '[[1, 0.5, 3, 2], [2, 1, 4, 3], [0.3333, 0.25, 1, 0.5], [0.5, 0.3333, 2, 1]]', '[0.28, 0.48, 0.1, 0.14]', 0.0412, 1, '["Supplier A (Caraga Agri-Tech Solutions)", "Supplier B (National Drone Systems Corp)", "Supplier C (Mindanao Precision Instruments)"]', '[[85, 92, 7, 88], [74, 96, 4, 94], [95, 84, 12, 80]]', '[{"rank": 1, "closeness": 0.7415, "alternative": "Supplier B (National Drone Systems Corp)"}, {"rank": 2, "closeness": 0.5571, "alternative": "Supplier A (Caraga Agri-Tech Solutions)"}, {"rank": 3, "closeness": 0.3308, "alternative": "Supplier C (Mindanao Precision Instruments)"}]', 'Supplier B (National Drone Systems Corp)', 'Optimal recommendation produced by combining Analytic Hierarchy Process eigenvector criteria weighting with TOPSIS Euclidean proximity scoring.', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 1, 7, 'MCDA-4X8BO8', 'BAC 2026 IT Equipment Supplier Selection', 'Procurement Supplier Selection', 'Hybrid-AHP-TOPSIS', '["Cost Efficiency & Price Competitiveness", "Technical Specifications Compliance", "Delivery Lead Time & SLA", "Supplier Reliability & Track Record"]', '[[1, 0.5, 3, 2], [2, 1, 4, 3], [0.3333, 0.25, 1, 0.5], [0.5, 0.3333, 2, 1]]', '[0.2771, 0.4658, 0.096, 0.1611]', 0.0115, 1, '["Supplier A (Local Surigao Enterprise)", "Supplier B (National Tech Distro Corp)", "Supplier C (Direct Manufacturer Importer)"]', '[[85, 92, 7, 88], [74, 96, 4, 94], [95, 84, 12, 80]]', NULL, 'Supplier B (National Tech Distro Corp)', 'Scoring conducted via Saaty AHP and Hwang-Yoon TOPSIS method in compliance with institutional procurement standards.', '2026-10-26 04:42:36', '2026-10-26 04:42:36'),
	(3, 1, 7, 'MCDA-PB2AU2', 'BAC 2026 IT Equipment Supplier Selection', 'Capital Expenditure Prioritization', 'Hybrid-AHP-TOPSIS', '["Cost Efficiency & Price Competitiveness", "Technical Specifications Compliance", "Delivery Lead Time & SLA", "Supplier Reliability & Track Record"]', '[[1, 0.5, 3, 2], [2, 1, 4, 3], [0.3333, 0.25, 1, 0.5], [0.5, 0.3333, 2, 1]]', '[0.2771, 0.4658, 0.096, 0.1611]', 0.0115, 1, '["Supplier A (Local Surigao Enterprise)", "Supplier B (National Tech Distro Corp)", "Supplier C (Direct Manufacturer Importer)"]', '[[85, 92, 7, 88], [74, 96, 4, 94], [95, 84, 12, 80]]', NULL, 'Supplier B (National Tech Distro Corp)', 'Scoring conducted via Saaty AHP and Hwang-Yoon TOPSIS method in compliance with institutional procurement standards.', '2026-10-26 04:43:02', '2026-10-26 04:43:02');
/*!40000 ALTER TABLE `dss_evaluations` ENABLE KEYS */;

-- Dumping structure for table extend.dts_document_types
DROP TABLE IF EXISTS `dts_document_types`;
CREATE TABLE IF NOT EXISTS `dts_document_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `citizen_charter_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `standard_sla_days` smallint unsigned NOT NULL DEFAULT '3',
  `complexity` enum('Simple','Complex','Highly Technical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Simple',
  `client_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'G2G - Government to Government',
  `who_may_avail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checklist_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fees` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Free / None',
  `legal_basis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_unit_id` bigint unsigned DEFAULT NULL,
  `default_priority` tinyint unsigned NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '0',
  `template_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mdi-file-document-outline',
  `badge_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#047857',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dts_document_types_tenant_id_index` (`tenant_id`),
  KEY `dts_document_types_code_index` (`code`),
  KEY `dts_document_types_created_by_index` (`created_by`),
  KEY `dts_document_types_updated_by_index` (`updated_by`),
  KEY `dts_document_types_primary_unit_id_index` (`primary_unit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.dts_document_types: 9 rows
DELETE FROM `dts_document_types`;
/*!40000 ALTER TABLE `dts_document_types` DISABLE KEYS */;
INSERT INTO `dts_document_types` (`id`, `tenant_id`, `code`, `citizen_charter_code`, `name`, `category`, `standard_sla_days`, `complexity`, `client_type`, `who_may_avail`, `checklist_requirements`, `fees`, `legal_basis`, `primary_unit_id`, `default_priority`, `requires_approval`, `template_code`, `description`, `icon`, `badge_color`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'PR', 'CC-PROC-01', 'Purchase Request (PR)', 'Procurement', 7, 'Complex', 'G2G - Government to Government / Internal', 'Colleges, Academic Departments, Administrative Units, and Research Project Leaders', '1. Approved Purchase Request Form (3 copies) â€” Originating Unit\n2. Project Procurement Management Plan (PPMP) Excerpt â€” BAC Secretariat\n3. Market Canvass / Price Quotation (min. 3 quotations) â€” Supply & Property Office\n4. Certificate of Allotment Availability â€” Budget Management Office', 'Free / None', 'RA 9184 (Government Procurement Reform Act) & RA 11032 (Ease of Doing Business)', 8, 2, 1, NULL, 'Formal institutional requisition of supplies, laboratory equipment, materials, or services.', 'mdi-cart-outline', '#d97706', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(2, 1, 'DV', 'CC-FIN-01', 'Disbursement Voucher (DV)', 'Financial', 7, 'Complex', 'G2C / G2B / G2G', 'University Employees, Faculty, Suppliers, Contractors, and Official Claimants', '1. Duly signed Disbursement Voucher (3 copies) â€” Accounting Office\n2. Approved Obligation Request and Status (ORS) â€” Budget Management Office\n3. Original Sales Invoice / Official Receipt / Billing Statement â€” Claimant / Supplier\n4. Inspection and Acceptance Report (IAR) â€” Property & Supply Office\n5. Notice of Award / Purchase Order / Approved Contract â€” BAC', 'Free / None', 'COA Circular 2012-001 (Revised Documentary Requirements for Common Government Transactions) & RA 11032', 6, 2, 1, NULL, 'Official payment processing voucher for claims, salaries, honoraria, or supplier billings.', 'mdi-cash-multiple', '#059669', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(3, 1, 'TOR', 'CC-REG-01', 'Transcript of Records (TOR) & Certifications', 'Academic', 3, 'Simple', 'G2C - Government to Citizen', 'Enrolled Students, Graduated Alumni, and Former Students', '1. Duly accomplished Student Records Request Slip â€” Registrar\'s Office\n2. University Clearance Certificate â€” Student Affairs / Library / Accounting\n3. Valid Government / Student ID (Original & Photocopy) â€” Requesting Student\n4. Official Receipt of Processing Fee â€” Cashiering Unit', 'PHP 100.00 / page', 'CHED Memorandum Orders & University Code Art. 210', 10, 1, 1, NULL, 'Official academic transcript, certificate of grades, graduation certificate, or honorable dismissal.', 'mdi-school-outline', '#0284c7', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(4, 1, 'TO', 'CC-HR-01', 'Travel Order (Official Business / Time)', 'Administrative', 3, 'Simple', 'G2G - Government to Government / Internal', 'All Faculty Members, Academic Staff, and Administrative Personnel', '1. Duly accomplished Travel Authority Form â€” HRMO / Portal\n2. Official Letter of Invitation / Notice of Meeting â€” Sponsoring Host / Agency\n3. Approved Itinerary of Travel â€” Unit Head\n4. Work Substitution / Load Clearance â€” College Dean / Department Chair', 'Free / None', 'Executive Order No. 77 (Official Local and Foreign Travels) & CSC Rules', 9, 1, 1, NULL, 'Official travel authorization, seminar attendance, off-campus meetings, and field duties.', 'mdi-airplane-takeoff', '#6366f1', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(5, 1, 'SO', 'CC-ADM-01', 'Special Order', 'Administrative', 3, 'Simple', 'G2G - Government to Government / Internal', 'University Units, Academic Departments, and Committees', '1. Formal Justification Memorandum â€” Proponent Unit / Dean\n2. Proposed Committee Terms of Reference / Duties â€” Proponent\n3. Endorsement of Supervising Vice President â€” Office of the VP', 'Free / None', 'SUC Charter & Civil Service Commission Omnibus Rules', 1, 1, 1, NULL, 'Official personnel assignment orders, committee designations, and official university duties.', 'mdi-certificate-outline', '#0284c7', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(6, 1, 'MEMO', 'CC-ADM-02', 'Office Memorandum', 'Administrative', 3, 'Simple', 'G2G - Government to Government / Internal', 'University Officials, Deans, Directors, Faculty, and Staff', '1. Draft Memorandum with Complete Particulars â€” Issuing Official\n2. Supporting Documentation or Legal Reference â€” Issuing Office', 'Free / None', 'University Code & RA 11032', 1, 1, 1, NULL, 'Official institutional circulars, internal directives, and general administrative memos.', 'mdi-file-document-outline', '#047857', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(7, 1, 'CONTR', 'CC-LEG-01', 'Contract / MOA / MOU Review', 'Legal', 20, 'Highly Technical', 'G2C / G2B / G2G', 'Colleges, Research Institutes, External Partner Agencies, LGUs, and Private Industry', '1. Draft Memorandum of Agreement (MOA) / Memorandum of Understanding (MOU) â€” Proponent\n2. Legal Brief & Background Justification â€” College Dean / Research Director\n3. Financial Allotment Certification (if with financial obligations) â€” Budget Office\n4. Supervising Vice President Endorsement â€” Office of the VP', 'Free / None', 'RA 11032 (Highly Technical Services), Civil Code of the Philippines, and University Charter', 12, 3, 1, NULL, 'Institutional partnership agreements, research collaborations, deeds, and multi-party legal contracts.', 'mdi-handshake-outline', '#7c3aed', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(8, 1, 'BOR_RES', 'CC-GOV-01', 'Board of Regents Resolution', 'Governance', 20, 'Highly Technical', 'G2G - Government to Government', 'Administrative Council, Academic Council, and University President', '1. Approved Proposal by University Academic / Administrative Council â€” Council Secretariat\n2. Comprehensive Financial & Legal Viability Brief â€” VPAF & Legal Counsel\n3. Excerpt from Board Agenda & Board Committee Endorsement â€” Board Secretary', 'Free / None', 'Republic Act 8292 (Higher Education Modernization Act) & SUC Charter', 1, 3, 1, NULL, 'Highest institutional policy, budget confirmation, or charter authority from Governing Board.', 'mdi-gavel', '#4338ca', 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(9, 1, 'tyerty', 'ertyrty', 'yertyertyerty', 'Administrative', 3, 'Simple', 'G2G - Government to Government', 'All Institutional Personnel & Authorized Stakeholders', NULL, 'Free / None', NULL, 2, 1, 1, NULL, NULL, 'mdi-file-document-outline', '#116600', 1, 11, NULL, '2026-09-16 15:33:38', '2026-09-16 15:33:38', NULL);
/*!40000 ALTER TABLE `dts_document_types` ENABLE KEYS */;

-- Dumping structure for table extend.employees
DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `employee_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suffix` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `designation_id` bigint unsigned DEFAULT NULL,
  `designation` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Permanent',
  `employment_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `date_hired` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_tenant_id_foreign` (`tenant_id`),
  KEY `employees_campus_id_foreign` (`campus_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_employee_no_index` (`employee_no`),
  KEY `employees_email_index` (`email`),
  KEY `employees_designation_id_foreign` (`designation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.employees: 10 rows
DELETE FROM `employees`;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `tenant_id`, `employee_no`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `phone_number`, `gender`, `campus_id`, `department_id`, `designation_id`, `designation`, `employment_type`, `employment_status`, `date_hired`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'EMP-2026-0001', 'ADSSU System', '', 'Administrator', '', 'admin@adssu.edu.ph', '09170000001', 'Other', 1, 1, 5, 'Management Information Systems Director', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(2, 1, 'EMP-2026-0002', 'Dr. Joy C.', '', 'Capistrano', '', 'president@adssu.edu.ph', '09170000002', 'Other', 1, 1, 6, 'SUC President III', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(3, 1, 'EMP-2026-0003', 'Engr. Nelson V.', '', 'Robles', '', 'bac@adssu.edu.ph', '09170000003', 'Other', 1, 2, 8, 'Bids & Awards Committee Chairperson', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(4, 1, 'EMP-2026-0004', 'Dr. Alan P.', '', 'Martinez', '', 'director@adssu.edu.ph', '09170000004', 'Other', 2, 1, 16, 'Campus Director, Trento Campus', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(5, 1, 'EMP-2026-0005', 'Dr. QA Admin', '', 'Lead', '', 'admin_test_qa@iipm.edu.ph', '09170000005', 'Other', 1, 1, 17, 'Institutional Officer', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(6, 1, 'EMP-2026-0006', 'Dr. Diane B. Remot', '', '(TL)', '', 'auditor_faculty_test@iipm.edu.ph', '09170000006', 'Other', 1, 1, 17, 'Institutional Officer', 'Permanent', 'On Leave', '2023-09-16', '2026-09-16 08:09:00', '2026-10-16 07:09:42', NULL),
	(7, 1, 'EMP-2026-0007', 'Antonio Jr.', 'Luib', 'Piloton', '', 'apiloton1@snsu.edu.ph', '09128941731', 'Male', 11, 15, 21, 'Programmer', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:28:44', NULL),
	(8, 1, 'EMP-2024-0080', 'Maria Elena', 'Santos', 'Villanueva', '', 'm.villanueva@adssu.edu.ph', '09175551234', 'Female', 1, 8, 19, 'Property Custodian / Laboratory Aide', 'Permanent', 'Active', '2022-06-15', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(9, 1, 'EMP-2024-0081', 'Rodrigo', 'Tee', 'Dela Cruz', 'Jr.', 'r.delacruz@adssu.edu.ph', '09185552345', 'Male', 1, 9, 10, 'Administrative Aide VI', 'Contractual', 'Active', '2023-01-10', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(10, 1, 'EMP-2024-0082', 'Jocelyn', 'Mendoza', 'Fernandez', '', 'j.fernandez@adssu.edu.ph', '09195553456', 'Female', 2, 4, 13, 'Visiting Professional Lecturer', 'Part-time', 'Active', '2024-08-01', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(11, 1, 'EMP-ZCOHX', 'Virnille', NULL, 'Francisco', NULL, 'v.francisco@adssu.edu.ph', NULL, NULL, 1, 17, NULL, 'College of Computer and Information Sciences Dean', 'Permanent', 'Active', NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20', NULL),
	(12, 1, 'EMP-M9FKD', 'Jessa', NULL, 'Hambre', NULL, 'j.hambre@adssu.edu.ph', NULL, NULL, 1, 17, NULL, 'College of Computer and Information Sciences Head for Extension', 'Permanent', 'Active', NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20', NULL),
	(13, 1, 'EMP-WRDNE', 'Merlyn', NULL, 'Estoque', NULL, 'm.estoque@adssu.edu.ph', NULL, NULL, 1, 18, NULL, 'Graduate School Dean', 'Permanent', 'Active', NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20', NULL),
	(14, 1, 'EMP-J84JR', 'Louida', NULL, 'Patac', NULL, 'l.patac@adssu.edu.ph', NULL, NULL, 1, 18, NULL, 'Graduate School Head for Extension', 'Permanent', 'Active', NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21', NULL),
	(15, 1, 'EMP-RPWXL', 'Marilou', NULL, 'Carnicer', NULL, 'm.carnicer@adssu.edu.ph', NULL, NULL, 1, 19, NULL, 'College of Business and Technology Dean', 'Permanent', 'Active', NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21', NULL),
	(16, 1, 'EMP-H0DEX', 'Kenneth Charls', NULL, 'Ferol', NULL, 'k.ferol@adssu.edu.ph', NULL, NULL, 1, 19, NULL, 'College of Business and Technology Head for Extension', 'Permanent', 'Active', NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21', NULL),
	(17, 1, 'EMP-RBSMZ', 'Elvis', NULL, 'Patulin', NULL, 'e.patulin@adssu.edu.ph', NULL, NULL, 1, 20, NULL, 'College of Teacher Education Dean', 'Permanent', 'Active', NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21', NULL),
	(18, 1, 'EMP-JEUZK', 'Marijul', NULL, 'Jovero', NULL, 'm.jovero@adssu.edu.ph', NULL, NULL, 1, 20, NULL, 'College of Teacher Education Head for Extension', 'Permanent', 'Active', NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL),
	(19, 1, 'EMP-YZZM7', 'Louida', NULL, 'Patac', NULL, 'l.patac.cas@adssu.edu.ph', NULL, NULL, 1, 21, NULL, 'College of Arts and Sciences Dean', 'Permanent', 'Active', NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL),
	(20, 1, 'EMP-ODGQF', 'Luis', NULL, 'Cuabo', NULL, 'l.cuabo@adssu.edu.ph', NULL, NULL, 1, 21, NULL, 'College of Arts and Sciences Head for Extension', 'Permanent', 'Active', NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL),
	(21, 1, 'EMP-YCPGG', 'Ever Joy', NULL, 'Rabadan', NULL, 'e.rabadan@adssu.edu.ph', NULL, NULL, 1, 22, NULL, 'University Extension Specialist V', 'Permanent', 'Active', NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL),
	(22, 1, 'EMP-X0QVM', 'Alvie Elma', NULL, 'Plaza', NULL, 'a.plaza@adssu.edu.ph', NULL, NULL, 1, 22, NULL, 'University Extension Director', 'Permanent', 'Active', NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;

-- Dumping structure for table extend.employee_designations
DROP TABLE IF EXISTS `employee_designations`;
CREATE TABLE IF NOT EXISTS `employee_designations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `employee_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `designation_id` bigint unsigned DEFAULT NULL,
  `designation` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `effective_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_designations_tenant_id_foreign` (`tenant_id`),
  KEY `user_designations_employee_id_index` (`employee_id`),
  KEY `user_designations_user_id_index` (`user_id`),
  KEY `user_designations_campus_id_index` (`campus_id`),
  KEY `user_designations_department_id_index` (`department_id`),
  KEY `user_designations_designation_id_foreign` (`designation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.employee_designations: 12 rows
DELETE FROM `employee_designations`;
/*!40000 ALTER TABLE `employee_designations` DISABLE KEYS */;
INSERT INTO `employee_designations` (`id`, `tenant_id`, `employee_id`, `user_id`, `campus_id`, `department_id`, `designation_id`, `designation`, `is_primary`, `status`, `effective_date`, `end_date`, `remarks`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 1, 5, 'Management Information Systems Director', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(2, 1, 2, 2, 1, 1, 6, 'SUC President III', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(3, 1, 3, 3, 1, 2, 8, 'Bids & Awards Committee Chairperson', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(4, 1, 4, 4, 2, 1, 16, 'Campus Director, Trento Campus', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(5, 1, 5, 5, 1, 1, 17, 'Institutional Officer', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-10-26 04:27:08'),
	(6, 1, 6, 6, 1, 1, 17, 'Institutional Officer', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-10-25 23:47:09'),
	(16, 9, 7, 7, 11, 15, 21, 'Programmer', 1, 'active', NULL, NULL, NULL, '2026-09-16 13:50:54', '2026-09-16 13:50:54'),
	(8, 1, 8, 12, 1, 8, 19, 'Property Custodian / Laboratory Aide', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 14:11:06'),
	(9, 1, 9, 14, 1, 9, 10, 'Administrative Aide VI', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 14:13:06'),
	(10, 1, 10, 13, 2, 4, 13, 'Visiting Professional Lecturer', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 14:12:46'),
	(11, 8, NULL, 10, 10, 14, 20, 'Campus Information Systems Administrator', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(17, 9, 7, 7, 11, 15, 8, 'Bids & Awards Committee Chairperson', 0, 'active', NULL, NULL, NULL, '2026-09-16 13:50:54', '2026-09-16 13:50:54');
/*!40000 ALTER TABLE `employee_designations` ENABLE KEYS */;

-- Dumping structure for table extend.extension_agendas
DROP TABLE IF EXISTS `extension_agendas`;
CREATE TABLE IF NOT EXISTS `extension_agendas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extension_agendas_code_index` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_agendas: 5 rows
DELETE FROM `extension_agendas`;
/*!40000 ALTER TABLE `extension_agendas` DISABLE KEYS */;
INSERT INTO `extension_agendas` (`id`, `code`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'EA-1', 'Transformative Learning', NULL, 1, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(2, 'EA-2', 'Livelihood Generation', NULL, 2, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(3, 'EA-3', 'Biodiversity Conservation', NULL, 3, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(4, 'EA-4', 'Climate Change Adoption & Risk Management', NULL, 4, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(5, 'EA-5', 'Good Governance', NULL, 5, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48');
/*!40000 ALTER TABLE `extension_agendas` ENABLE KEYS */;

-- Dumping structure for table extend.extension_banner_programs
DROP TABLE IF EXISTS `extension_banner_programs`;
CREATE TABLE IF NOT EXISTS `extension_banner_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension_banner_programs_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_banner_programs: 16 rows
DELETE FROM `extension_banner_programs`;
/*!40000 ALTER TABLE `extension_banner_programs` DISABLE KEYS */;
INSERT INTO `extension_banner_programs` (`id`, `code`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'TECHASSIST', 'TECHASSIST', 'edited', 1, 1, '2026-10-16 06:55:48', '2026-10-26 05:07:28'),
	(2, 'STEAMUP', 'STEAMUP', NULL, 2, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(3, 'FOODPRO', 'FOODPRO', NULL, 3, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(4, 'TOURGABAY', 'TOURGABAY', NULL, 4, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(5, 'AGRIYAMAN', 'AGRIYAMAN', NULL, 5, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(6, 'MSMEntri', 'MSMEntri', NULL, 6, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(7, 'MODFARM 360', 'MODFARM 360', NULL, 7, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(8, 'KATUTUBO', 'KATUTUBO', NULL, 8, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(9, 'GOVeRN', 'GOVeRN', NULL, 9, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(10, 'ECOREVIVE', 'ECOREVIVE', NULL, 10, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(11, 'SUSTINAHAN', 'SUSTINAHAN', NULL, 11, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(12, 'GADLIGHT', 'GADLIGHT', NULL, 12, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(13, 'RENEWTECH', 'RENEWTECH', NULL, 13, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(14, 'WELLNESS', 'WELLNESS', NULL, 14, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(15, 'ReStat-QUIIP', 'ReStat-QUIIP', NULL, 15, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48'),
	(16, 'TUBIG-SIGURADO', 'TUBIG-SIGURADO', NULL, 16, 1, '2026-10-16 06:55:48', '2026-10-16 06:55:48');
/*!40000 ALTER TABLE `extension_banner_programs` ENABLE KEYS */;

-- Dumping structure for table extend.extension_budgets
DROP TABLE IF EXISTS `extension_budgets`;
CREATE TABLE IF NOT EXISTS `extension_budgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `campus_id` bigint unsigned DEFAULT NULL,
  `fiscal_year` smallint unsigned NOT NULL,
  `budget_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fund_cluster` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '01 - Regular Agency Fund (GAA)',
  `total_allocation` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allocated_gaa` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allocated_stf` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allocated_external` decimal(15,2) NOT NULL DEFAULT '0.00',
  `realigned_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `board_resolution_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension_budgets_budget_code_unique` (`budget_code`),
  KEY `extension_budgets_tenant_id_index` (`tenant_id`),
  KEY `extension_budgets_campus_id_index` (`campus_id`),
  KEY `extension_budgets_fiscal_year_index` (`fiscal_year`),
  KEY `extension_budgets_status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_budgets: 1 rows
DELETE FROM `extension_budgets`;
/*!40000 ALTER TABLE `extension_budgets` DISABLE KEYS */;
INSERT INTO `extension_budgets` (`id`, `tenant_id`, `campus_id`, `fiscal_year`, `budget_code`, `title`, `fund_cluster`, `total_allocation`, `allocated_gaa`, `allocated_stf`, `allocated_external`, `realigned_amount`, `status`, `board_resolution_no`, `approved_by`, `approved_at`, `notes`, `created_by_user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, NULL, 2026, 'EXT-BUD-2026-MAIN', 'FY 2026 SNSU Institutional Extension & Community Development Operating Fund', '01 - Regular Agency Fund (GAA)', 5200000.00, 3500000.00, 1200000.00, 500000.00, 0.00, 'active', 'BOR Resolution No. 12-2026', 'SNSU Board of Regents / Dr. Gregorio Z. Gamboa Jr.', '2026-01-15', 'Official annual appropriation for community extension, technology transfer, and livelihood projects across all SNSU campuses.', 1, '2026-10-23 20:11:58', '2026-10-23 20:11:58', NULL);
/*!40000 ALTER TABLE `extension_budgets` ENABLE KEYS */;

-- Dumping structure for table extend.extension_budget_allocations
DROP TABLE IF EXISTS `extension_budget_allocations`;
CREATE TABLE IF NOT EXISTS `extension_budget_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `extension_budget_id` bigint unsigned NOT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `category_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extension_budget_allocations_extension_budget_id_index` (`extension_budget_id`),
  KEY `extension_budget_allocations_campus_id_index` (`campus_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_budget_allocations: 6 rows
DELETE FROM `extension_budget_allocations`;
/*!40000 ALTER TABLE `extension_budget_allocations` DISABLE KEYS */;
INSERT INTO `extension_budget_allocations` (`id`, `extension_budget_id`, `campus_id`, `category_name`, `allocated_amount`, `remarks`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'Direct Community Extension Activities', 2200000.00, 'Barangay outreach, livelihood, and technology transfer', '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(2, 1, NULL, 'Skills & Livelihood Capacity Training', 1200000.00, 'Workshops, farmer/fisherfolk training modules', '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(3, 1, NULL, 'IEC Materials & Toolkits', 600000.00, 'Extension manuals, infographics, and project publications', '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(4, 1, NULL, 'Extension Logistics & Field Subsistence', 500000.00, 'Travel, transport, and community visitation logistics', '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(5, 1, NULL, 'Supplies & Training Consumables', 400000.00, 'Project consumables and demonstration materials', '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(6, 1, NULL, 'Impact Assessment & Community Monitoring', 300000.00, 'SLA compliance, feedback assessment, and terminal reports', '2026-10-23 20:11:58', '2026-10-23 20:11:58');
/*!40000 ALTER TABLE `extension_budget_allocations` ENABLE KEYS */;

-- Dumping structure for table extend.extension_budget_transactions
DROP TABLE IF EXISTS `extension_budget_transactions`;
CREATE TABLE IF NOT EXISTS `extension_budget_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `extension_budget_id` bigint unsigned NOT NULL,
  `transaction_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_no` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `authorized_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `extension_budget_transactions_extension_budget_id_index` (`extension_budget_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_budget_transactions: 1 rows
DELETE FROM `extension_budget_transactions`;
/*!40000 ALTER TABLE `extension_budget_transactions` DISABLE KEYS */;
INSERT INTO `extension_budget_transactions` (`id`, `extension_budget_id`, `transaction_type`, `amount`, `reference_no`, `reason`, `authorized_by`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 'initial', 5200000.00, 'BOR Res. No. 12-2026', 'Approved Initial Annual Extension Appropriation for FY 2026', 'SNSU Board of Regents', 1, '2026-10-23 20:11:58', '2026-10-23 20:11:58');
/*!40000 ALTER TABLE `extension_budget_transactions` ENABLE KEYS */;

-- Dumping structure for table extend.extension_plans
DROP TABLE IF EXISTS `extension_plans`;
CREATE TABLE IF NOT EXISTS `extension_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `academic_program_id` bigint unsigned DEFAULT NULL,
  `dts_record_id` bigint unsigned DEFAULT NULL,
  `plan_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension_activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objectives` text COLLATE utf8mb4_unicode_ci,
  `sub_extension_proposal_date` date DEFAULT NULL,
  `sub_activity_design_date` date DEFAULT NULL,
  `sub_purchase_request_date` date DEFAULT NULL,
  `moa_signing_date` date DEFAULT NULL,
  `materials_arrival_mode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'range',
  `materials_arrival_start` date DEFAULT NULL,
  `materials_arrival_end` date DEFAULT NULL,
  `materials_arrival_dates` json DEFAULT NULL,
  `materials_arrival_date` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conduct_activity_mode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'range',
  `conduct_activity_start` date DEFAULT NULL,
  `conduct_activity_end` date DEFAULT NULL,
  `conduct_activity_dates` json DEFAULT NULL,
  `conduct_activity_date` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_narrative_report_date` date DEFAULT NULL,
  `monitoring_feedback_date` date DEFAULT NULL,
  `monitoring_feedback_end_date` date DEFAULT NULL,
  `beneficiaries` text COLLATE utf8mb4_unicode_ci,
  `target_beneficiaries_count` int unsigned NOT NULL DEFAULT '0',
  `proposed_budget` decimal(14,2) NOT NULL DEFAULT '0.00',
  `extensionists_count` int unsigned NOT NULL DEFAULT '0',
  `prepared_by_user_id` bigint unsigned DEFAULT NULL,
  `prepared_by_employee_id` bigint unsigned DEFAULT NULL,
  `prepared_by_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prepared_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Project Leader',
  `prepared_at` datetime DEFAULT NULL,
  `reviewed_by_user_id` bigint unsigned DEFAULT NULL,
  `reviewed_by_employee_id` bigint unsigned DEFAULT NULL,
  `reviewed_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `noted_by_employee_id` bigint unsigned DEFAULT NULL,
  `noted_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `noted_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Extension Head',
  `noted_at` datetime DEFAULT NULL,
  `approved_by_employee_id` bigint unsigned DEFAULT NULL,
  `approved_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dean',
  `approved_at` datetime DEFAULT NULL,
  `concurred_by_employee_id` bigint unsigned DEFAULT NULL,
  `concurred_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concurred_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concurred_at` datetime DEFAULT NULL,
  `concurred_by_2_employee_id` bigint unsigned DEFAULT NULL,
  `concurred_by_2_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concurred_by_2_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `concurred_by_2_at` datetime DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension_plans_plan_code_unique` (`plan_code`),
  KEY `extension_plans_campus_id_foreign` (`campus_id`),
  KEY `extension_plans_academic_program_id_foreign` (`academic_program_id`),
  KEY `extension_plans_dts_record_id_foreign` (`dts_record_id`),
  KEY `extension_plans_prepared_by_employee_id_foreign` (`prepared_by_employee_id`),
  KEY `extension_plans_noted_by_employee_id_foreign` (`noted_by_employee_id`),
  KEY `extension_plans_approved_by_employee_id_foreign` (`approved_by_employee_id`),
  KEY `extension_plans_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `extension_plans_prepared_by_user_id_index` (`prepared_by_user_id`),
  KEY `ext_conduct_idx` (`conduct_activity_start`,`conduct_activity_end`),
  KEY `ext_materials_idx` (`materials_arrival_start`,`materials_arrival_end`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_plans: 6 rows
DELETE FROM `extension_plans`;
/*!40000 ALTER TABLE `extension_plans` DISABLE KEYS */;
INSERT INTO `extension_plans` (`id`, `tenant_id`, `campus_id`, `academic_program_id`, `dts_record_id`, `plan_code`, `program_name`, `extension_activity`, `objectives`, `sub_extension_proposal_date`, `sub_activity_design_date`, `sub_purchase_request_date`, `moa_signing_date`, `materials_arrival_mode`, `materials_arrival_start`, `materials_arrival_end`, `materials_arrival_dates`, `materials_arrival_date`, `conduct_activity_mode`, `conduct_activity_start`, `conduct_activity_end`, `conduct_activity_dates`, `conduct_activity_date`, `sub_narrative_report_date`, `monitoring_feedback_date`, `monitoring_feedback_end_date`, `beneficiaries`, `target_beneficiaries_count`, `proposed_budget`, `extensionists_count`, `prepared_by_user_id`, `prepared_by_employee_id`, `prepared_by_name`, `prepared_by_designation`, `prepared_at`, `reviewed_by_user_id`, `reviewed_by_employee_id`, `reviewed_by_name`, `reviewed_by_designation`, `reviewed_at`, `noted_by_employee_id`, `noted_by_name`, `noted_by_designation`, `noted_at`, `approved_by_employee_id`, `approved_by_name`, `approved_by_designation`, `approved_at`, `concurred_by_employee_id`, `concurred_by_name`, `concurred_by_designation`, `concurred_at`, `concurred_by_2_employee_id`, `concurred_by_2_name`, `concurred_by_2_designation`, `concurred_by_2_at`, `status`, `remarks`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 1, NULL, 'SNSU-EXT-2026-0001', 'Bachelor of Science in Information Technology', 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays', '1. Train 100 barangay functionaries on basic computer operations and secure data entry.\n2. Deploy a lightweight, web-based barangay recordkeeping and certification system.\n3. Establish ongoing technical support and community feedback mechanisms.', '2026-08-25', '2026-09-04', '2026-09-14', '2026-09-24', 'range', '2026-09-29', '2026-10-04', NULL, NULL, 'range', '2026-10-09', '2026-10-14', NULL, NULL, '2026-10-19', '2026-11-03', NULL, 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City', 100, 95000.00, 6, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-08-20 13:20:33', NULL, 12, 'JESSA G. HAMBRE', 'College of Computer and Information Sciences Head for Extension', NULL, 11, 'VIRNILLE C. FRANCISCO, PhEdD', 'College of Computer and Information Sciences Dean', '2026-08-22 13:20:33', 4, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', '2026-08-25 13:20:33', 21, 'EVER JOY G. RABADAN, MAEM, MSc.', 'University Extension Specialist V', NULL, 22, 'ALVIE ELMA C. PLAZA, MA', 'University Extension Director', NULL, 'approved', 'Approved and included in the Official Annual RIE Extension Calendar.', NULL, '2026-10-24 05:20:33', '2026-10-26 06:46:22', NULL),
	(2, 1, 1, 7, NULL, 'SNSU-EXT-2026-0002', 'Bachelor of Science in Civil Engineering', 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning', '1. Inspect structural integrity of 5 coastal public elementary schools.\n2. Design cost-effective retrofitting solutions for storm surge vulnerability.\n3. Train barangay DRRM volunteers on structural hazard identification.', '2026-09-09', '2026-09-19', '2026-09-29', '2026-10-04', 'range', '2026-10-09', '2026-10-14', NULL, NULL, 'range', '2026-10-19', '2026-10-29', NULL, NULL, '2026-11-08', '2026-11-23', NULL, 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua', 60, 115000.00, 5, 12, 8, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', '2026-09-06 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-09-08 13:20:33', 4, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', '2026-09-09 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 'Endorsed for high-priority funding due to climate disaster vulnerability.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(3, 1, 2, 10, NULL, 'SNSU-EXT-2026-0003', 'Bachelor of Science in Agricultural and Biosystems Engineering', 'Buhay Mangrove: Community-Based Mangrove Reforestation & Coastal Bio-Shield Protocol', '1. Propagate 10,000 Rhizophora mangrove propagules in Del Carmen, Siargao.\n2. Organize and train 40 fisherfolk families as environmental stewards.\n3. Establish permanent ecological nursery sites.', '2026-10-04', '2026-10-09', '2026-10-14', '2026-10-19', 'range', '2026-10-26', '2026-10-31', NULL, NULL, 'range', '2026-11-05', '2026-11-11', NULL, NULL, '2026-11-23', '2026-12-23', NULL, 'Del Carmen Small Fisherfolk Federation and Women Seaweed Growers Guild', 85, 140000.00, 7, 14, 9, 'Rodrigo T. Dela Cruz Jr.', 'Faculty Researcher / Extensionist', '2026-09-29 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-10-02 13:20:33', 4, 'Dr. Alan P. Martinez', 'Campus Director, Del Carmen Campus', '2026-10-04 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 'Approved Plan: Ready for Extension Proposal creation by proponent.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(4, 1, 3, 8, NULL, 'SNSU-EXT-2026-0004', 'Bachelor of Science in Electrical Engineering', 'Solar-Kuryente: Off-Grid Solar PV Installation & Battery Maintenance for Island Communities', '1. Train 50 youth and island residents on solar panel wiring and DC inverter maintenance.\n2. Install pilot solar charging stations in off-grid fishing sitios.\n3. Distribute safety and troubleshooting quick-reference kits.', '2026-10-29', '2026-11-08', '2026-11-18', '2026-11-28', 'range', '2026-12-03', '2026-12-08', NULL, NULL, 'range', '2026-12-13', '2026-12-18', NULL, NULL, '2026-12-28', '2027-01-22', NULL, 'Off-grid Fisherfolk Families and Youth Electricians of Tagana-an', 75, 88000.00, 4, 13, 10, 'Jocelyn M. Fernandez', 'Associate Professor / Project Leader', '2026-10-19 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-10-22 13:20:33', NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'noted', 'Noted by Extension Coordinator. Currently pending Dean review and approval before proposals can be initiated.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(5, 1, 4, 2, NULL, 'SNSU-EXT-2026-0005', 'Bachelor of Science in Computer Science', 'CodeKids: Scratch Programming & STEM Discovery Camp for Remote Elementary Schools', '1. Introduce 150 public school children to logic, computational thinking, and block-based coding.\n2. Equip teachers with modular coding curricula and offline learning kits.', '2026-11-13', '2026-11-23', '2026-12-03', '2026-12-13', 'range', '2026-12-18', '2026-12-23', NULL, NULL, 'range', '2026-12-28', '2027-01-02', NULL, NULL, '2027-01-12', '2027-02-11', NULL, 'Grade 5 & 6 Pupils and Science Teachers of Malimono Central Elementary School', 150, 65000.00, 5, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-10-23 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Extension Coordinator', NULL, NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'submitted', 'Submitted by faculty end-user. In-transit to Extension Coordinator.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(6, 1, 1, 1, NULL, 'SNSU-EXT-2026-0006', 'Bachelor of Science in Information Technology', 'CyberLigtas: Cyber Hygiene & Anti-Scam Orientation for Public Market Merchants and Senior Citizens', '1. Protect 120 senior citizens and market vendors against phishing, SMS fraud, and illegal mobile lending schemes.', '2026-11-28', '2026-12-08', '2026-12-18', '2026-12-28', 'range', '2027-01-02', '2027-01-07', NULL, NULL, 'range', '2027-01-12', '2027-01-17', NULL, NULL, '2027-01-27', '2027-02-26', NULL, 'Surigao City Public Market Vendors Association and Senior Citizens Federation', 120, 50000.00, 4, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Extension Coordinator', NULL, NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL);
/*!40000 ALTER TABLE `extension_plans` ENABLE KEYS */;

-- Dumping structure for table extend.extension_proposals
DROP TABLE IF EXISTS `extension_proposals`;
CREATE TABLE IF NOT EXISTS `extension_proposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `academic_program_id` bigint unsigned DEFAULT NULL,
  `extension_plan_id` bigint unsigned NOT NULL,
  `dts_record_id` bigint unsigned DEFAULT NULL,
  `proposal_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_program` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_program_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agenda_priorities` json DEFAULT NULL,
  `beneficiaries` text COLLATE utf8mb4_unicode_ci,
  `target_beneficiaries_data` json DEFAULT NULL,
  `implementing_agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `implementing_colleges` json DEFAULT NULL,
  `lead_agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collaborating_agencies` text COLLATE utf8mb4_unicode_ci,
  `collaborating_agencies_data` json DEFAULT NULL,
  `duration_start` date DEFAULT NULL,
  `duration_end` date DEFAULT NULL,
  `duration_text` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_months` int DEFAULT NULL,
  `project_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_institution_share` text COLLATE utf8mb4_unicode_ci,
  `fund_source_internal` text COLLATE utf8mb4_unicode_ci,
  `budget_partner_share` text COLLATE utf8mb4_unicode_ci,
  `fund_source_external` text COLLATE utf8mb4_unicode_ci,
  `budget_total_display` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Grand Total: No Cash Requirement',
  `budget_cash_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `budgetary_requirements` json DEFAULT NULL,
  `project_leader_user_id` bigint unsigned DEFAULT NULL,
  `project_leader_employee_id` bigint unsigned DEFAULT NULL,
  `project_leader_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_leader_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Project Leader',
  `project_leader_gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asst_project_leader_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asst_project_leader_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Assistant Project Leader',
  `proponents` json DEFAULT NULL,
  `introduction` longtext COLLATE utf8mb4_unicode_ci,
  `objectives` json DEFAULT NULL,
  `general_objective` text COLLATE utf8mb4_unicode_ci,
  `specific_objectives` json DEFAULT NULL,
  `expected_outputs` json DEFAULT NULL,
  `relevance_narrative` longtext COLLATE utf8mb4_unicode_ci,
  `sdg_alignments` json DEFAULT NULL,
  `development_gap` longtext COLLATE utf8mb4_unicode_ci,
  `pdp_responsiveness` longtext COLLATE utf8mb4_unicode_ci,
  `schedule_of_activities` json DEFAULT NULL,
  `risk_management` json DEFAULT NULL,
  `people_involved` json DEFAULT NULL,
  `org_structure` json DEFAULT NULL,
  `sustainability_measures` longtext COLLATE utf8mb4_unicode_ci,
  `prepared_by_user_id` bigint unsigned DEFAULT NULL,
  `prepared_by_employee_id` bigint unsigned DEFAULT NULL,
  `prepared_by_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prepared_by_designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Project Leader',
  `prepared_at` datetime DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extension_proposals_proposal_code_unique` (`proposal_code`),
  UNIQUE KEY `extension_proposals_tracking_number_unique` (`tracking_number`),
  KEY `extension_proposals_campus_id_foreign` (`campus_id`),
  KEY `extension_proposals_academic_program_id_foreign` (`academic_program_id`),
  KEY `extension_proposals_dts_record_id_foreign` (`dts_record_id`),
  KEY `extension_proposals_project_leader_employee_id_foreign` (`project_leader_employee_id`),
  KEY `extension_proposals_prepared_by_user_id_foreign` (`prepared_by_user_id`),
  KEY `extension_proposals_prepared_by_employee_id_foreign` (`prepared_by_employee_id`),
  KEY `extension_proposals_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `extension_proposals_extension_plan_id_index` (`extension_plan_id`),
  KEY `extension_proposals_project_leader_user_id_index` (`project_leader_user_id`),
  KEY `extension_proposals_unit_id_index` (`unit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_proposals: 2 rows
DELETE FROM `extension_proposals`;
/*!40000 ALTER TABLE `extension_proposals` DISABLE KEYS */;
INSERT INTO `extension_proposals` (`id`, `tenant_id`, `campus_id`, `unit_id`, `academic_program_id`, `extension_plan_id`, `dts_record_id`, `proposal_code`, `tracking_number`, `title`, `banner_program`, `banner_program_other`, `agenda_priorities`, `beneficiaries`, `target_beneficiaries_data`, `implementing_agency`, `implementing_colleges`, `lead_agency`, `collaborating_agencies`, `collaborating_agencies_data`, `duration_start`, `duration_end`, `duration_text`, `duration_months`, `project_location`, `budget_institution_share`, `fund_source_internal`, `budget_partner_share`, `fund_source_external`, `budget_total_display`, `budget_cash_amount`, `budgetary_requirements`, `project_leader_user_id`, `project_leader_employee_id`, `project_leader_name`, `project_leader_title`, `project_leader_gender`, `asst_project_leader_name`, `asst_project_leader_title`, `proponents`, `introduction`, `objectives`, `general_objective`, `specific_objectives`, `expected_outputs`, `relevance_narrative`, `sdg_alignments`, `development_gap`, `pdp_responsiveness`, `schedule_of_activities`, `risk_management`, `people_involved`, `org_structure`, `sustainability_measures`, `prepared_by_user_id`, `prepared_by_employee_id`, `prepared_by_name`, `prepared_by_designation`, `prepared_at`, `status`, `remarks`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 15, 1, 1, NULL, 'SNSU-PROP-2026-0001', '202610240001', 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays', 'Digital Literacy & Information Technology Transfer', NULL, '["Smart Technologies & Digital Transformation", "Community Empowerment & Governance"]', 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City', '{"direct": 100, "sectors": ["Barangay Officials", "Barangay Health Workers", "Youth & SK"], "indirect": 3500}', 'Surigao del Norte State University (SNSU)', '["College of Engineering and Technology", "College of Information & Communications Technology"]', 'Surigao del Norte State University', 'City Government of Surigao, Department of Information and Communications Technology (DICT - Caraga)', '[{"name": "City Government of Surigao", "role": "Logistical support and community venue coordination"}, {"name": "DICT Caraga", "role": "Free Wi-Fi access points and technical assistance"}]', '2026-10-09', '2026-12-08', '2 Months (October 2026 – December 2026)', 2, 'Barangay Lipata Gymnasium, Surigao City', '75,000.00', '75,000.00', '20,000.00', '20,000.00', '95,000.00', 95000.00, '{"sections": {"A": {"code": "A", "items": [{"item": "Expert Resource Speaker Honoraria", "basis": "40 hrs @ 500/hr", "amount": 20000}, {"item": "Technical Facilitator Assistance", "basis": "40 hrs @ 250/hr", "amount": 10000}], "title": "Personal Services / Honoraria"}, "D": {"code": "D", "items": [{"item": "Documentation, Certificates & Transportation", "basis": "Lump sum", "amount": 10000}], "title": "Contingency & Miscellaneous"}, "B_TRAIN": {"code": "B_TRAIN", "items": [{"item": "Participant Training Kits & Workbooks", "basis": "100 sets @ 250/set", "amount": 25000}, {"item": "Training Venue Meals & Refreshments", "basis": "100 pax @ 300/pax", "amount": 30000}], "title": "Training Supplies & Materials"}}}', 7, 7, 'Antonio Jr. L. Piloton', 'Associate Professor / Project Leader', 'Male', 'Maria Elena S. Villanueva', 'Assistant Project Leader', '[{"name": "Antonio Jr. L. Piloton", "role": "Project Leader", "type": "faculty", "employee_id": 7}, {"name": "Maria Elena S. Villanueva", "role": "Assistant Project Leader", "type": "faculty", "employee_id": 8}, {"name": "Rodrigo T. Dela Cruz Jr.", "role": "Technical Facilitator", "type": "faculty", "employee_id": 9}]', 'Information and Communications Technology (ICT) plays an indispensable role in community-level governance. In coastal barangays of Surigao City, manual recordkeeping continues to present bottlenecks in resident profiling, health records management, and disaster aid allocation. This project equips local officials with modern digital tools to enhance administrative transparency and service delivery.', '[{"title": "Capacity Development", "description": "Conduct 40 hours of hands-on training on digital literacy and office automation."}, {"title": "System Deployment", "description": "Install and configure a localized electronic barangay record system."}, {"title": "Impact Assessment", "description": "Evaluate citizen satisfaction and operational turnaround time before and after implementation."}]', 'To modernize frontline public service delivery and administrative recordkeeping across coastal barangays in Surigao City through digital literacy training and automated local governance systems.', '["Train 100 barangay staff and SK leaders on digital document creation and spreadsheet management.", "Establish a secure offline-capable database of residents, health indices, and clearance issuances.", "Formulate a community-led maintenance protocol to ensure continuous operation."]', '["100 fully certified barangay personnel in digital public administration.", "1 fully operational e-Barangay Records Information System.", "1 signed Memorandum of Agreement with LGU Surigao City for sustained tech support."]', 'Directly aligns with the Philippine Development Plan (PDP) 2023-2028 Chapter on Expanding Digital Infrastructure and Enhancing Social Inclusion, alongside the Regional Development Plan of Caraga.', '{"selected": [4, 9, 11], "narrative": "Addresses SDG 4 (Quality Education) through practical digital skills training, SDG 9 (Industry, Innovation & Infrastructure) by providing IT infrastructure, and SDG 11 (Sustainable Communities) through resilient municipal e-governance."}', 'Disparity in digital literacy between urban centers and coastal frontline communities, resulting in backlogs, inaccurate resident profiles, and delays during emergency disaster response.', 'Supports Chapter 14: Promoting Inclusive and Sustainable Digital Infrastructure and Transforming Governance.', '[{"phase": "Phase I: Needs Assessment & LGU Consultation", "outputs": "Hardware readiness audit, signed MOA", "target_month": "Week 1-2"}, {"phase": "Phase II: Hands-on ICT Workshop", "outputs": "Completed modular training, skill assessment", "target_month": "Week 3-5"}, {"phase": "Phase III: System Rollout & Data Migration", "outputs": "Encoded resident database, pilot operations", "target_month": "Week 6-7"}, {"phase": "Phase IV: Monitoring & Closeout", "outputs": "Final evaluation report, user turnover", "target_month": "Week 8"}]', NULL, '{"rows": [{"id": "r1", "name": "Antonio Jr. L. Piloton", "role": "Project Leader", "responsibilities": "Overall project oversight, curriculum formulation, and institutional coordination."}, {"id": "r2", "name": "Maria Elena S. Villanueva", "role": "Assistant Project Leader", "responsibilities": "Field logistics, training materials preparation, and participant assessment."}, {"id": "r3", "name": "Rodrigo T. Dela Cruz Jr.", "role": "Technical Facilitator", "responsibilities": "Network setup, software installation, and lab support."}], "columns": [{"id": "name", "label": "Extensionist / Collaborator", "width": 35}, {"id": "role", "label": "Project Role / Title", "width": 25}, {"id": "responsibilities", "label": "Specific Responsibilities & Tasks", "width": 40}]}', '{"mode": "dynamic", "nodes": [{"id": "lead", "name": "Antonio Jr. L. Piloton", "title": "Project Leader", "parent": null}, {"id": "asst", "name": "Maria Elena S. Villanueva", "title": "Assistant Project Leader", "parent": "lead"}, {"id": "tech", "name": "Rodrigo T. Dela Cruz Jr.", "title": "Technical Lead", "parent": "asst"}]}', 'The project turnover includes a complete administrator manual, video tutorials, and a dedicated student volunteer helpdesk hosted at the SNSU College of Engineering and Technology.', 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-10-09 13:20:33', 'approved', 'Fully approved by the University President with allocation from GAA Extension Allotment.', '{"accomplishment_report": {"updated_at": "2026-10-26T13:39:06+00:00", "date_completed": "2026-10-26", "impact_feedback": "Community Impact & Beneficiary TestimonialsCommunity Impact & Beneficiary TestimonialsCommunity Impact & Beneficiary Testimonials", "updated_by_name": "Engr. Antonio Piloton Jr.", "solutions_adopted": "Problems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted\\r\\nProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted\\r\\nProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted", "updated_by_user_id": 7, "actual_budget_spent": "95000", "problems_encountered": "Problems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered", "implementation_status": "Completed", "summary_accomplishments": "Summary of Key Accomplishments & MilestonesSummary of Key Accomplishments & MilestonesSummary of Key Accomplishments & MilestonesSummary of Key Accomplishments & Milestones", "actual_beneficiaries_male": "45", "actual_beneficiaries_total": "100", "actual_beneficiaries_female": "55", "actual_beneficiaries_sectors": "Specific Community Sectors / Demographics"}}', '2026-10-24 05:20:33', '2026-10-26 05:39:06', NULL),
	(2, 1, 1, 8, 7, 2, NULL, 'SNSU-PROP-2026-0002', '202610240002', 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning', 'Disaster Risk Reduction & Climate Change Adaptation', NULL, '["Climate Resilience & Environmental Protection", "Community Safety & Infrastructure"]', 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua', '{"direct": 60, "indirect": 2000}', 'Surigao del Norte State University (SNSU)', NULL, 'Surigao del Norte State University', 'MDRRMO Surigao City, Department of Public Works and Highways (DPWH)', NULL, '2026-10-19', '2026-12-18', '2 Months', 2, 'Barangay Mabua, Surigao City', '90,000.00', '90,000.00', '25,000.00', '25,000.00', '115,000.00', 115000.00, NULL, 12, 8, 'Maria Elena S. Villanueva', 'Assistant Professor / Project Leader', 'Female', NULL, 'Assistant Project Leader', '[{"name": "Maria Elena S. Villanueva", "role": "Project Leader", "type": "faculty", "employee_id": 8}, {"name": "Antonio Jr. L. Piloton", "role": "Co-Proponent", "type": "faculty", "employee_id": 7}]', 'Coastal public infrastructure in Northern Mindanao faces recurrent degradation from seismic activity, tropical cyclones, and storm surges. Through comprehensive non-destructive structural diagnostics and community evacuation routing, this program safeguards lives and critical educational assets.', '[{"title": "Structural Assessment", "description": "Conduct rebound hammer and ultrasonic pulse velocity tests on 5 evacuation facilities."}, {"title": "Retrofitting Blueprints", "description": "Deliver schematic structural improvement recommendations."}]', 'To enhance structural resilience and community disaster evacuation preparedness of public educational buildings in Barangay Mabua, Surigao City.', '["Evaluate masonry, reinforcement, and roofing vulnerabilities of 5 public structures.", "Establish localized evacuation route mappings with community DRRM personnel."]', '["5 complete structural engineering vulnerability assessments.", "1 evacuation roadmap handbook distributed to the barangay council."]', 'Addresses SDG 11 (Resilient Infrastructure) and SDG 13 (Climate Action).', '{"selected": [11, 13], "narrative": "Targets building safety and disaster risk mitigation."}', NULL, NULL, '[{"phase": "Field Testing & Structural Diagnostics", "outputs": "Testing data sheets", "target_month": "Week 1-3"}, {"phase": "Engineering Computation & Report Writing", "outputs": "Draft structural reports", "target_month": "Week 4-6"}, {"phase": "Stakeholders Turnover Workshop", "outputs": "Disaster blueprint handover", "target_month": "Week 7-8"}]', NULL, NULL, NULL, NULL, 12, 8, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', '2026-10-19 13:20:33', 'concurred', 'Signed by Project Leader, Dean, and Extension Director. Currently on VP RIE desk for administrative endorsement.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL);
/*!40000 ALTER TABLE `extension_proposals` ENABLE KEYS */;

-- Dumping structure for table extend.extension_proposal_sdgs
DROP TABLE IF EXISTS `extension_proposal_sdgs`;
CREATE TABLE IF NOT EXISTS `extension_proposal_sdgs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `extension_proposal_id` bigint unsigned NOT NULL,
  `sdg_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `relevance_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_prop_sdg_unique` (`extension_proposal_id`,`sdg_id`),
  KEY `sdg_ext_prop_idx` (`sdg_id`,`extension_proposal_id`),
  KEY `extension_proposal_sdgs_extension_proposal_id_index` (`extension_proposal_id`),
  KEY `extension_proposal_sdgs_sdg_id_index` (`sdg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_proposal_sdgs: 5 rows
DELETE FROM `extension_proposal_sdgs`;
/*!40000 ALTER TABLE `extension_proposal_sdgs` DISABLE KEYS */;
INSERT INTO `extension_proposal_sdgs` (`id`, `extension_proposal_id`, `sdg_id`, `is_primary`, `relevance_notes`, `created_at`, `updated_at`) VALUES
	(1, 1, 4, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(2, 1, 9, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(3, 1, 11, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(4, 2, 11, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(5, 2, 13, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33');
/*!40000 ALTER TABLE `extension_proposal_sdgs` ENABLE KEYS */;

-- Dumping structure for table extend.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.failed_jobs: 0 rows
DELETE FROM `failed_jobs`;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

-- Dumping structure for table extend.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.jobs: 0 rows
DELETE FROM `jobs`;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

-- Dumping structure for table extend.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.job_batches: 0 rows
DELETE FROM `job_batches`;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;

-- Dumping structure for table extend.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.migrations: 67 rows
DELETE FROM `migrations`;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_09_10_000001_create_iipm_dss_saas_core_tables', 1),
	(5, '2026_09_10_000002_add_google_id_to_users_table', 1),
	(6, '2026_09_10_034636_create_permission_tables', 1),
	(7, '2026_09_10_040000_create_accreditation_tables', 1),
	(8, '2026_09_10_042926_create_accreditation_task_force_assignments_tables', 1),
	(9, '2026_09_10_050000_add_assigned_users_and_mov_submissions_tables', 1),
	(10, '2026_09_10_050102_add_user_ids_to_accreditation_programs', 1),
	(11, '2026_09_10_060000_create_accreditation_benchmark_comments_table', 1),
	(12, '2026_09_10_073114_create_notifications_table', 1),
	(13, '2026_09_10_083247_add_logo_variants_to_sys_institution_settings_table', 1),
	(14, '2026_09_10_093529_create_ipm_institutional_policies_tables', 1),
	(15, '2026_09_10_123542_create_dts_records_and_logs_tables', 1),
	(16, '2026_09_10_123753_add_dts_record_id_to_ipm_policies_table', 1),
	(17, '2026_09_10_144918_add_document_settings_to_sys_institution_settings', 1),
	(18, '2026_09_10_144927_create_ipm_policy_signatories_table', 1),
	(19, '2026_09_10_150631_add_signing_columns_to_ipm_policy_signatories_table', 1),
	(20, '2026_09_10_151128_add_user_id_to_ipm_policy_signatories_table', 1),
	(21, '2026_09_10_153500_add_col_span_to_ipm_policy_signatories_table', 1),
	(22, '2026_09_10_154100_add_alignment_to_ipm_policy_signatories_table', 1),
	(23, '2026_09_11_034256_add_pdf_path_and_routing_as_to_dts_records_table', 1),
	(24, '2026_09_11_043201_create_dts_files_table', 1),
	(25, '2026_09_11_172500_create_dts_reference_and_master_tables', 1),
	(26, '2026_09_11_174000_create_dts_citizen_charter_tables', 1),
	(27, '2026_09_11_193500_add_sla_duration_and_unit_to_dts_routing_template_steps', 1),
	(28, '2026_09_11_195000_add_doc_type_id_to_dts_records_table', 1),
	(29, '2026_09_11_223500_create_dts_record_routes_table', 1),
	(30, '2026_09_12_111500_add_detailed_styling_to_sys_theme_settings_table', 1),
	(31, '2026_09_12_120000_create_ipm_memorandums_table', 1),
	(32, '2026_09_12_123000_add_dynamic_table_and_recipients_to_ipm_memorandums', 1),
	(33, '2026_09_12_133000_add_template_and_paper_to_ipm_memorandums', 1),
	(34, '2026_09_12_140000_add_template_and_paper_to_ipm_policies', 1),
	(35, '2026_09_13_130000_create_sys_iso_document_controls_table', 1),
	(36, '2026_09_13_141000_remove_office_columns_from_sys_iso_document_controls_table', 1),
	(37, '2026_09_16_105233_add_evaluator_fields_to_accreditation_survey_areas_table', 1),
	(38, '2026_09_16_111500_create_accreditation_mock_audits_tables', 1),
	(39, '2026_09_16_120000_create_academic_programs_table', 1),
	(40, '2026_09_16_161000_create_employees_and_link_users_table', 2),
	(41, '2026_09_16_175620_remove_team_id_from_permission_tables_make_roles_global', 3),
	(42, '2026_09_16_180044_create_user_designations_table', 4),
	(43, '2026_09_16_210102_create_designations_table', 5),
	(44, '2026_09_16_211617_rename_user_designations_to_employee_designations_table', 6),
	(45, '2026_09_16_222218_alter_status_column_in_users_table', 7),
	(46, '2026_09_16_235500_add_head_employee_id_to_dts_units_table', 8),
	(47, '2026_09_16_235600_add_employee_ids_to_accreditation_tables', 8),
	(48, '2026_09_17_002627_make_user_id_nullable_in_accreditation_pivots', 9),
	(49, '2026_09_17_113300_drop_sys_departments_and_rename_dts_units_to_units', 10),
	(50, '2026_09_17_161500_create_extension_plans_table', 10),
	(51, '2026_09_17_162500_add_calendar_dates_to_extension_plans_table', 10),
	(52, '2026_09_17_180000_create_sys_document_signatory_matrices_table', 10),
	(53, '2026_09_17_183500_create_sys_document_signatories_table', 10),
	(54, '2026_09_18_130000_create_sys_document_comments_table', 10),
	(55, '2026_09_18_140000_create_sys_comments_table', 10),
	(56, '2026_09_18_150000_create_sys_uploaded_documents_table', 10),
	(57, '2026_09_18_170656_create_sys_document_audit_trails_table', 10),
	(58, '2026_09_18_183000_create_extension_proposals_table', 10),
	(59, '2026_09_19_082717_add_tracking_number_to_extension_proposals_table', 10),
	(60, '2026_09_19_084126_add_budgetary_requirements_to_extension_proposals_table', 10),
	(61, '2026_09_19_093000_create_sustainable_development_goals_tables', 10),
	(62, '2026_09_19_154148_make_role_label_nullable_in_sys_document_signatories_table', 10),
	(63, '2026_09_19_164800_seed_extension_proposal_signatory_matrices', 10),
	(64, '2026_09_19_171500_add_unit_id_to_extension_proposals_table', 10),
	(65, '2026_09_22_190000_update_extension_proposals_to_new_format', 10),
	(66, '2026_09_23_090000_create_extension_banner_programs_and_agendas_tables', 10),
	(67, '2026_10_24_040108_create_extension_budgets_table', 11),
	(68, '2026_10_26_144310_add_extension_signatories_to_units_and_plans_table', 12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table extend.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.model_has_permissions: 4 rows
DELETE FROM `model_has_permissions`;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(1, 'App\\Models\\User', 2),
	(6, 'App\\Models\\User', 11),
	(8, 'App\\Models\\User', 11);
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;

-- Dumping structure for table extend.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.model_has_roles: 14 rows
DELETE FROM `model_has_roles`;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 7),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(3, 'App\\Models\\User', 6),
	(3, 'App\\Models\\User', 11),
	(4, 'App\\Models\\User', 4),
	(4, 'App\\Models\\User', 11),
	(5, 'App\\Models\\User', 1),
	(5, 'App\\Models\\User', 5),
	(5, 'App\\Models\\User', 10),
	(5, 'App\\Models\\User', 11),
	(5, 'App\\Models\\User', 13),
	(15, 'App\\Models\\User', 11),
	(15, 'App\\Models\\User', 12),
	(15, 'App\\Models\\User', 14),
	(18, 'App\\Models\\User', 16),
	(18, 'App\\Models\\User', 18),
	(18, 'App\\Models\\User', 20),
	(18, 'App\\Models\\User', 22),
	(18, 'App\\Models\\User', 24),
	(19, 'App\\Models\\User', 15),
	(19, 'App\\Models\\User', 17),
	(19, 'App\\Models\\User', 19),
	(19, 'App\\Models\\User', 21),
	(19, 'App\\Models\\User', 23);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;

-- Dumping structure for table extend.notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.notifications: 6 rows
DELETE FROM `notifications`;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('d1328f5c-61d6-4286-aa1f-9314cbc76f2d', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 1, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"jlz-iUg4hXek8Pn_KyG2AR1U5PCNwLE","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('7a25441e-06d9-43e1-8aae-853f77975f80', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 2, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"dSAuZo9goBnS7f14WQgoAHGr4bkKqbc","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('608a4633-2b5a-40cb-857e-363f625a3825', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 3, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"lseqJcyqZYL0qZXkHv6rRlSe_fX0R-o","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('276e7b2f-4955-4c07-a975-1dc1cb78cc32', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 4, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"8_0jOPGq_7odHB1pVb8oA2C5AqWPYdQ","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('ea51b181-a105-4528-a3ec-ce094a89f186', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 1, '{"title":"P.1.1 \\u2014 Mock Audit Scored (4.20)","message":"ADSSU System Administrator evaluated score at 4.20 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/YfrrdK1gy4ubQsSfx4_kstdVEDv5zSk\\/audit","benchmark_code":"P.1.1","benchmark_id":"iPIAgkSmPLHuRIiWzspnCVUHRreZYN8","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 05:16:15', '2026-09-16 05:16:15'),
	('5d99f9e9-bb1f-4da2-871b-590b09b759b5', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 6, '{"title":"P.1.1 \\u2014 Mock Audit Scored (4.20)","message":"ADSSU System Administrator evaluated score at 4.20 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/YfrrdK1gy4ubQsSfx4_kstdVEDv5zSk\\/audit","benchmark_code":"P.1.1","benchmark_id":"RBc60TrrZhoa42MV5Q1b2DntyC1aPBQ","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 05:16:15', '2026-09-16 05:16:15');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

-- Dumping structure for table extend.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.password_reset_tokens: 0 rows
DELETE FROM `password_reset_tokens`;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

-- Dumping structure for table extend.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.permissions: 27 rows
DELETE FROM `permissions`;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'dss.dashboard.view', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 'dss.ahp.compute', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(3, 'dss.topsis.compute', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(4, 'dss.evaluations.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 'accreditation.survey.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(6, 'accreditation.iqa.audit', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(7, 'accreditation.vpaa.endorse', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(8, 'accreditation.ched.apply', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(9, 'procurement.pr.create', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(10, 'procurement.budget.clear', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(11, 'procurement.bac.evaluate', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(12, 'procurement.po.approve', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(13, 'procurement.iar.inspect', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(14, 'financials.dv.prepare', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(15, 'financials.box_b.certify', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(16, 'financials.payment.approve', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(17, 'financials.cashier.release', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(18, 'faculty.nbc461.submit', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(19, 'faculty.crec.score', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(20, 'faculty.iec.validate', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(21, 'faculty.scholarship.apply', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(22, 'faculty.travel.authorize', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(23, 'system.hierarchy.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(24, 'system.theme.customize', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(25, 'system.roles.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(26, 'system.users.assign', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(27, 'custom.institutional.audit_signoff', 'web', '2026-09-16 07:43:32', '2026-09-16 07:43:32');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table extend.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.roles: 8 rows
DELETE FROM `roles`;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'SuperAdmin', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 'President', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(3, 'BACMember', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(4, 'Director', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 'Admin', 'web', '2026-09-16 07:59:10', '2026-09-16 07:59:10'),
	(15, 'Employee', 'web', NULL, NULL),
	(16, 'Faculty', 'web', NULL, NULL),
	(17, 'new', 'web', NULL, NULL),
	(18, 'College Extension Head', 'web', '2026-10-26 06:46:20', '2026-10-26 06:46:20'),
	(19, 'UnitHead', 'web', '2026-10-26 06:46:20', '2026-10-26 06:46:20');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table extend.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.role_has_permissions: 75 rows
DELETE FROM `role_has_permissions`;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 4),
	(1, 5),
	(2, 1),
	(2, 3),
	(2, 5),
	(3, 1),
	(3, 3),
	(3, 5),
	(4, 1),
	(4, 2),
	(4, 3),
	(4, 5),
	(5, 1),
	(5, 2),
	(5, 4),
	(5, 5),
	(6, 1),
	(6, 5),
	(7, 1),
	(7, 2),
	(7, 5),
	(8, 1),
	(8, 5),
	(9, 1),
	(9, 3),
	(9, 4),
	(9, 5),
	(10, 1),
	(10, 5),
	(11, 1),
	(11, 3),
	(11, 5),
	(12, 1),
	(12, 2),
	(12, 5),
	(13, 1),
	(13, 3),
	(13, 5),
	(14, 1),
	(14, 5),
	(15, 1),
	(15, 5),
	(16, 1),
	(16, 2),
	(16, 5),
	(17, 1),
	(17, 5),
	(18, 1),
	(18, 5),
	(19, 1),
	(19, 4),
	(19, 5),
	(20, 1),
	(20, 2),
	(20, 5),
	(21, 1),
	(21, 5),
	(22, 1),
	(22, 5),
	(23, 1),
	(23, 2),
	(23, 5),
	(24, 1),
	(24, 5),
	(25, 1),
	(25, 2),
	(25, 5),
	(26, 1),
	(26, 2),
	(26, 5),
	(27, 1),
	(27, 5);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;

-- Dumping structure for table extend.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sessions: 1 rows
DELETE FROM `sessions`;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('0fWqNnQZfW6IYCu6mHwnuibagDqMfjGEzRMR4CdZ', 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'eyJfdG9rZW4iOiJWYzdNYzZvQ0RIUTlLQ1NyUWVTalA1a2tSa3dIckc3a29CdG02WVMxIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjcsIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2V4dGVuZC5kZXYuY29tXC9kc3NcL3JpZVwvcGxhbnNcL3RsZ3ZCVWRmWDlETmlGNmo1TklWUVpjTmRtWEVcL3BkZj9kb3dubG9hZD0xIiwicm91dGUiOiJkc3MucmllLnBsYW5zLnBkZiJ9fQ==', 1793026244);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

-- Dumping structure for table extend.sustainable_development_goals
DROP TABLE IF EXISTS `sustainable_development_goals`;
CREATE TABLE IF NOT EXISTS `sustainable_development_goals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `goal_number` tinyint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color_hex` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#19486A',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mdi-star-outline',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sustainable_development_goals_goal_number_unique` (`goal_number`),
  UNIQUE KEY `sustainable_development_goals_code_unique` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sustainable_development_goals: 17 rows
DELETE FROM `sustainable_development_goals`;
/*!40000 ALTER TABLE `sustainable_development_goals` DISABLE KEYS */;
INSERT INTO `sustainable_development_goals` (`id`, `goal_number`, `code`, `name`, `short_name`, `description`, `color_hex`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 1, 'SDG 1', 'No Poverty', 'No Poverty', 'End poverty in all its forms everywhere.', '#E5243B', 'mdi-home-heart', 1, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(2, 2, 'SDG 2', 'Zero Hunger', 'Zero Hunger', 'End hunger, achieve food security and improved nutrition and promote sustainable agriculture.', '#DDA63A', 'mdi-food-apple-outline', 2, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(3, 3, 'SDG 3', 'Good Health & Well-being', 'Good Health', 'Ensure healthy lives and promote well-being for all at all ages.', '#4C9F38', 'mdi-heart-pulse', 3, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(4, 4, 'SDG 4', 'Quality Education', 'Quality Education', 'Ensure inclusive and equitable quality education and promote lifelong learning opportunities for all.', '#C5192D', 'mdi-school-outline', 4, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(5, 5, 'SDG 5', 'Gender Equality', 'Gender Equality', 'Achieve gender equality and empower all women and girls.', '#FF3A21', 'mdi-gender-male-female', 5, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(6, 6, 'SDG 6', 'Clean Water & Sanitation', 'Clean Water', 'Ensure availability and sustainable management of water and sanitation for all.', '#26BDE2', 'mdi-water-outline', 6, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(7, 7, 'SDG 7', 'Affordable & Clean Energy', 'Clean Energy', 'Ensure access to affordable, reliable, sustainable and modern energy for all.', '#FCC30B', 'mdi-solar-power-variant-outline', 7, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(8, 8, 'SDG 8', 'Decent Work & Economic Growth', 'Decent Work', 'Promote sustained, inclusive and sustainable economic growth, full and productive employment and decent work for all.', '#A21942', 'mdi-chart-line', 8, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(9, 9, 'SDG 9', 'Industry, Innovation & Infrastructure', 'Industry & Innovation', 'Build resilient infrastructure, promote inclusive and sustainable industrialization and foster innovation.', '#FD6925', 'mdi-lightbulb-on-outline', 9, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(10, 10, 'SDG 10', 'Reduced Inequalities', 'Reduced Inequalities', 'Reduce inequality within and among countries.', '#DD1367', 'mdi-scale-balance', 10, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(11, 11, 'SDG 11', 'Sustainable Cities & Communities', 'Sustainable Cities', 'Make cities and human settlements inclusive, safe, resilient and sustainable.', '#FD9D24', 'mdi-city-variant-outline', 11, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(12, 12, 'SDG 12', 'Responsible Consumption & Production', 'Responsible Consumption', 'Ensure sustainable consumption and production patterns.', '#BF8B2E', 'mdi-recycle', 12, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(13, 13, 'SDG 13', 'Climate Action', 'Climate Action', 'Take urgent action to combat climate change and its impacts.', '#3F7E44', 'mdi-weather-partly-cloudy', 13, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(14, 14, 'SDG 14', 'Life Below Water', 'Life Below Water', 'Conserve and sustainably use the oceans, seas and marine resources for sustainable development.', '#0A97D9', 'mdi-fish', 14, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(15, 15, 'SDG 15', 'Life on Land', 'Life on Land', 'Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt biodiversity loss.', '#56C02B', 'mdi-tree-outline', 15, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(16, 16, 'SDG 16', 'Peace, Justice & Strong Institutions', 'Peace & Justice', 'Promote peaceful and inclusive societies for sustainable development, provide access to justice for all and build effective, accountable and inclusive institutions.', '#00689D', 'mdi-shield-check-outline', 16, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47'),
	(17, 17, 'SDG 17', 'Partnerships for the Goals', 'Partnerships', 'Strengthen the means of implementation and revitalize the Global Partnership for Sustainable Development.', '#19486A', 'mdi-handshake-outline', 17, 1, '2026-10-16 06:55:47', '2026-10-16 06:55:47');
/*!40000 ALTER TABLE `sustainable_development_goals` ENABLE KEYS */;

-- Dumping structure for table extend.sys_campuses
DROP TABLE IF EXISTS `sys_campuses`;
CREATE TABLE IF NOT EXISTS `sys_campuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `campus_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `campus_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `campus_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Main Campus',
  `location` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_campuses_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_campuses: 6 rows
DELETE FROM `sys_campuses`;
/*!40000 ALTER TABLE `sys_campuses` DISABLE KEYS */;
INSERT INTO `sys_campuses` (`id`, `tenant_id`, `campus_code`, `campus_name`, `campus_type`, `location`, `head_name`, `contact_no`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'MAIN', 'Surigao City Main Campus', 'Main Campus', 'Narciso Street, Surigao City', 'Dr. Joy C. Capistrano', NULL, 'active', '2026-09-16 04:06:48', '2026-10-24 05:17:10'),
	(2, 1, 'DEL_CARMEN', 'Del Carmen Campus', 'Satellite Campus', 'Del Carmen, Siargao Island, Surigao del Norte', 'Dr. Alan P. Martinez', NULL, 'active', '2026-09-16 04:06:48', '2026-10-24 05:17:10'),
	(3, 1, 'TAGANA_AN', 'Tagana-an Campus', 'Satellite Campus', 'Tagana-an, Surigao del Norte', 'Dr. Diane B. Remot', NULL, 'active', '2026-09-16 04:06:48', '2026-10-24 05:17:10'),
	(11, 9, 'MAIN', 'Main Campus', 'Main Campus', NULL, NULL, NULL, 'active', '2026-09-16 10:03:24', '2026-09-16 10:03:24'),
	(10, 8, 'MAIN', 'Main Campus', 'Main Campus', NULL, NULL, NULL, 'active', '2026-09-16 09:17:57', '2026-09-16 09:17:57'),
	(4, 1, 'MALIMONO', 'Malimono Campus', 'Satellite Campus', 'Malimono, Surigao del Norte', 'Engr. Nelson V. Robles', NULL, 'active', '2026-10-24 05:17:10', '2026-10-24 05:17:10');
/*!40000 ALTER TABLE `sys_campuses` ENABLE KEYS */;

-- Dumping structure for table extend.sys_comments
DROP TABLE IF EXISTS `sys_comments`;
CREATE TABLE IF NOT EXISTS `sys_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `commentable_type` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentable_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating_at_time` decimal(4,2) DEFAULT NULL,
  `stage_at_time` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_comments_tenant_id_commentable_type_commentable_id_index` (`tenant_id`,`commentable_type`,`commentable_id`),
  KEY `sys_comments_user_id_index` (`user_id`),
  KEY `sys_comments_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_comments: 2 rows
DELETE FROM `sys_comments`;
/*!40000 ALTER TABLE `sys_comments` DISABLE KEYS */;
INSERT INTO `sys_comments` (`id`, `tenant_id`, `commentable_type`, `commentable_id`, `user_id`, `author_name`, `author_designation`, `action_type`, `comment`, `rating_at_time`, `stage_at_time`, `metadata`, `created_at`, `updated_at`) VALUES
	(1, 1, 'memo', 204, 11, 'ANTONIO JR. L. PILOTON', 'Campus Information Systems Administrator', 'comment', 'hello', NULL, NULL, NULL, '2026-10-16 07:11:34', '2026-10-16 07:11:34'),
	(2, 1, 'memo', 204, 11, 'ANTONIO JR. L. PILOTON', 'Campus Information Systems Administrator', 'endorsed', 'hahahahaahhahaah', NULL, NULL, NULL, '2026-10-16 07:11:45', '2026-10-16 07:11:45');
/*!40000 ALTER TABLE `sys_comments` ENABLE KEYS */;

-- Dumping structure for table extend.sys_document_audit_trails
DROP TABLE IF EXISTS `sys_document_audit_trails`;
CREATE TABLE IF NOT EXISTS `sys_document_audit_trails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `campus_id` bigint unsigned DEFAULT NULL,
  `module` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `uploaded_document_id` bigint unsigned DEFAULT NULL,
  `documentable_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documentable_id` bigint unsigned DEFAULT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `previous_version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_notes` text COLLATE utf8mb4_unicode_ci,
  `sha256_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_document_audit_trails_user_id_foreign` (`user_id`),
  KEY `idx_audit_tenant_module` (`tenant_id`,`module`),
  KEY `idx_audit_polymorphic` (`documentable_type`,`documentable_id`),
  KEY `idx_audit_doc_action` (`uploaded_document_id`,`action`),
  KEY `idx_audit_created_at` (`created_at`),
  KEY `sys_document_audit_trails_tenant_id_index` (`tenant_id`),
  KEY `sys_document_audit_trails_campus_id_index` (`campus_id`),
  KEY `sys_document_audit_trails_module_index` (`module`),
  KEY `sys_document_audit_trails_action_index` (`action`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_document_audit_trails: 0 rows
DELETE FROM `sys_document_audit_trails`;
/*!40000 ALTER TABLE `sys_document_audit_trails` DISABLE KEYS */;
INSERT INTO `sys_document_audit_trails` (`id`, `tenant_id`, `campus_id`, `module`, `uploaded_document_id`, `documentable_type`, `documentable_id`, `action`, `version`, `previous_version`, `summary`, `change_notes`, `sha256_hash`, `filename`, `file_size`, `user_id`, `user_name`, `user_designation`, `ip_address`, `user_agent`, `metadata`, `created_at`, `updated_at`) VALUES
	(1, 1, 11, 'general', 1, NULL, NULL, 'physically_signed_tagged', '1.0', NULL, 'Uploaded official physically signed plan scan (INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf)', NULL, 'dbed21ea048be301b327f8f98386945244360c27f9ede9ff87367d984441a0fd', 'INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf', 90361, 7, 'Engr. Antonio Piloton Jr.', 'Platform Super Administrator / Chief Enterprise Architect', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-10-25 19:24:42', '2026-10-25 19:24:42'),
	(2, 1, 11, 'general', 2, 'App\\Models\\RIE\\ExtensionProposal', 1, 'physically_signed_tagged', '1.0', NULL, 'Uploaded official physically signed plan scan (INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf)', NULL, 'dbed21ea048be301b327f8f98386945244360c27f9ede9ff87367d984441a0fd', 'INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf', 90361, 7, 'Engr. Antonio Piloton Jr.', 'Platform Super Administrator / Chief Enterprise Architect', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-10-26 05:39:27', '2026-10-26 05:39:27'),
	(3, 1, 11, 'general', 3, 'App\\Models\\RIE\\ExtensionProposal', 1, 'uploaded', '1.0', NULL, 'Uploaded new document (Ma\'am jane.pdf)', NULL, 'b97a9fd77d44b49c61bd69c5d916c188b8a0576f5491e97c5934728d44655d1b', 'Ma\'am jane.pdf', 1221228, 7, 'Engr. Antonio Piloton Jr.', 'Platform Super Administrator / Chief Enterprise Architect', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-10-26 05:39:44', '2026-10-26 05:39:44');
/*!40000 ALTER TABLE `sys_document_audit_trails` ENABLE KEYS */;

-- Dumping structure for table extend.sys_document_signatories
DROP TABLE IF EXISTS `sys_document_signatories`;
CREATE TABLE IF NOT EXISTS `sys_document_signatories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `matrix_id` bigint unsigned DEFAULT NULL COMMENT 'Source master matrix rule id',
  `doc_type_id` bigint unsigned DEFAULT NULL COMMENT 'FK to dts_document_types',
  `document_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. extension_plan, memo, policy, or Model class',
  `document_id` bigint unsigned NOT NULL COMMENT 'Primary key of document model',
  `dts_record_id` bigint unsigned DEFAULT NULL COMMENT 'Linked DTS record id if routed',
  `tier_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'noted' COMMENT 'prepared, reviewed, noted, recommending_approval, approved, etc.',
  `role_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assignment_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed_employee' COMMENT 'fixed_employee, manual_signatory, dynamic_author, etc.',
  `employee_id` bigint unsigned DEFAULT NULL COMMENT 'Target Employee',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'Target User Account who signs',
  `signatory_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Display full legal name of signatory',
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Designation / Official Title',
  `office` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Department / Office assignment',
  `sort_order` smallint unsigned NOT NULL DEFAULT '1',
  `position_column` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1: Left, 2: Center/Right, 3: Right',
  `col_span` tinyint unsigned NOT NULL DEFAULT '1',
  `alignment` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'pending, signed, rejected, bypassed',
  `requires_digital_signature` tinyint(1) NOT NULL DEFAULT '1',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `signed_at` timestamp NULL DEFAULT NULL,
  `signed_by_user_id` bigint unsigned DEFAULT NULL COMMENT 'Actual user who affixed signature',
  `signature_img` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Base64 digital signature image',
  `signature_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'drawn, font, upload',
  `signature_hash` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cryptographic audit stamp (e.g. SIG-ADSSU-XXXXXX)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_for_sig_user` (`tenant_id`,`user_id`,`status`),
  KEY `idx_for_sig_employee` (`tenant_id`,`employee_id`,`status`),
  KEY `idx_doc_lookup` (`document_type`,`document_id`),
  KEY `idx_tenant_campus` (`tenant_id`,`campus_id`),
  KEY `idx_dts_record` (`dts_record_id`),
  KEY `idx_doc_type` (`doc_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_document_signatories: 44 rows
DELETE FROM `sys_document_signatories`;
/*!40000 ALTER TABLE `sys_document_signatories` DISABLE KEYS */;
INSERT INTO `sys_document_signatories` (`id`, `tenant_id`, `campus_id`, `matrix_id`, `doc_type_id`, `document_type`, `document_id`, `dts_record_id`, `tier_role`, `role_label`, `assignment_type`, `employee_id`, `user_id`, `signatory_name`, `designation`, `office`, `sort_order`, `position_column`, `col_span`, `alignment`, `status`, `requires_digital_signature`, `is_mandatory`, `signed_at`, `signed_by_user_id`, `signature_img`, `signature_type`, `signature_hash`, `ip_address`, `user_agent`, `remarks`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'prepared', 'Prepared by', 'specific_employee', 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', 'Office of the President', 1, 1, 1, 'left', 'signed', 1, 1, '2026-08-20 05:17:10', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:10', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(2, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-08-22 05:17:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:10', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(3, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-08-25 05:17:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:10', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(4, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'prepared', 'Prepared by', 'specific_employee', 7, 7, 'Antonio Jr. L. Piloton', 'Programmer', 'Office of the President', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-09 05:17:10', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:10', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(5, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'noted', 'Noted by', 'specific_employee', 4, 4, 'Dr. Alan P. Martinez', 'Campus Director, Trento Campus', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-12 05:17:10', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:10', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(6, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'concurred', 'Concurred by', 'specific_employee', 5, 5, 'Dr. QA Admin Lead', 'Institutional Officer', 'Office of the University President', 3, 1, 1, 'left', 'signed', 1, 1, '2026-10-14 05:17:10', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(7, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'recommended', 'Recommended by', 'specific_employee', 3, 3, 'Engr. Nelson V. Robles', 'Bids & Awards Committee Chairperson', 'Bids and Awards Committee (BAC) Secretariat', 4, 2, 1, 'right', 'signed', 1, 1, '2026-10-17 05:17:11', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(8, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'approved', 'Approved by', 'specific_employee', 2, 2, 'Dr. Joy C. Capistrano', 'SUC President III', 'Office of the University President', 5, 1, 1, 'center', 'signed', 1, 1, '2026-10-21 05:17:11', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(9, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'prepared', 'Prepared by', 'specific_employee', 8, 12, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', 'Supply and Property Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-09-06 05:17:11', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(10, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-09-08 05:17:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(11, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-09-09 05:17:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(12, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'prepared', 'Prepared by', 'specific_employee', 8, 12, 'Maria Elena S. Villanueva', 'Property Custodian / Laboratory Aide', 'Supply and Property Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-19 05:17:11', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(13, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'noted', 'Noted by', 'specific_employee', 4, 4, 'Dr. Alan P. Martinez', 'Campus Director, Trento Campus', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-20 05:17:11', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(14, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'concurred', 'Concurred by', 'specific_employee', 5, 5, 'Dr. QA Admin Lead', 'Institutional Officer', 'Office of the University President', 3, 1, 1, 'left', 'signed', 1, 1, '2026-10-22 05:17:11', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(15, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'recommended', 'Recommended by', 'specific_employee', 3, 3, 'Engr. Nelson V. Robles', 'Bids & Awards Committee Chairperson', 'Bids and Awards Committee (BAC) Secretariat', 4, 2, 1, 'right', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(16, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'approved', 'Approved by', 'specific_employee', 2, 2, 'Dr. Joy C. Capistrano', 'SUC President III', 'Office of the University President', 5, 1, 1, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(17, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'prepared', 'Prepared by', 'specific_employee', 9, 14, 'Rodrigo T. Dela Cruz Jr.', 'Faculty Researcher / Extensionist', 'Human Resource Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-09-29 05:17:11', 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(18, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-02 05:17:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(19, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'Campus Director, Del Carmen Campus', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-10-04 05:17:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:17:11', '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(20, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'prepared', 'Prepared by', 'specific_employee', 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', 'Office of the President', 1, 1, 1, 'left', 'signed', 1, 1, '2026-08-20 05:20:33', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(21, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-08-22 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(22, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 1, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-08-25 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(23, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'prepared', 'Prepared by', 'specific_employee', 7, 7, 'Antonio Jr. L. Piloton', 'Programmer', 'Office of the President', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-09 05:20:33', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(24, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'noted', 'Noted by', 'specific_employee', 4, 4, 'Dr. Alan P. Martinez', 'Campus Director, Trento Campus', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-12 05:20:33', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(25, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'concurred', 'Concurred by', 'specific_employee', 5, 5, 'Dr. QA Admin Lead', 'Institutional Officer', 'Office of the University President', 3, 1, 1, 'left', 'signed', 1, 1, '2026-10-14 05:20:33', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(26, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'recommended', 'Recommended by', 'specific_employee', 3, 3, 'Engr. Nelson V. Robles', 'Bids & Awards Committee Chairperson', 'Bids and Awards Committee (BAC) Secretariat', 4, 2, 1, 'right', 'signed', 1, 1, '2026-10-17 05:20:33', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(27, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 1, NULL, 'approved', 'Approved by', 'specific_employee', 2, 2, 'Dr. Joy C. Capistrano', 'SUC President III', 'Office of the University President', 5, 1, 1, 'center', 'signed', 1, 1, '2026-10-21 05:20:33', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(28, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'prepared', 'Prepared by', 'specific_employee', 8, 12, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', 'Supply and Property Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-09-06 05:20:33', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(29, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-09-08 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(30, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 2, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-09-09 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(31, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'prepared', 'Prepared by', 'specific_employee', 8, 12, 'Maria Elena S. Villanueva', 'Property Custodian / Laboratory Aide', 'Supply and Property Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-19 05:20:33', 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(32, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'noted', 'Noted by', 'specific_employee', 4, 4, 'Dr. Alan P. Martinez', 'Campus Director, Trento Campus', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-20 05:20:33', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(33, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'concurred', 'Concurred by', 'specific_employee', 5, 5, 'Dr. QA Admin Lead', 'Institutional Officer', 'Office of the University President', 3, 1, 1, 'left', 'signed', 1, 1, '2026-10-22 05:20:33', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(34, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'recommended', 'Recommended by', 'specific_employee', 3, 3, 'Engr. Nelson V. Robles', 'Bids & Awards Committee Chairperson', 'Bids and Awards Committee (BAC) Secretariat', 4, 2, 1, 'right', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(35, 1, 1, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 2, NULL, 'approved', 'Approved by', 'specific_employee', 2, 2, 'Dr. Joy C. Capistrano', 'SUC President III', 'Office of the University President', 5, 1, 1, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(36, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'prepared', 'Prepared by', 'specific_employee', 9, 14, 'Rodrigo T. Dela Cruz Jr.', 'Faculty Researcher / Extensionist', 'Human Resource Management Office', 1, 1, 1, 'left', 'signed', 1, 1, '2026-09-29 05:20:33', 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(37, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-02 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(38, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 3, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'Campus Director, Del Carmen Campus', 'Office of the University President', 3, 1, 2, 'center', 'signed', 1, 1, '2026-10-04 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(39, 1, 3, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 4, NULL, 'prepared', 'Prepared by', 'specific_employee', 10, 13, 'Jocelyn M. Fernandez', 'Associate Professor / Project Leader', 'Office of the Vice President for Academic Affairs', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-19 05:20:33', 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(40, 1, 3, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 4, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'signed', 1, 1, '2026-10-22 05:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(41, 1, 3, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 4, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'Dean', 'Office of the University President', 3, 1, 2, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(42, 1, 4, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 5, NULL, 'prepared', 'Prepared by', 'specific_employee', 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', 'Office of the President', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-23 05:20:33', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(43, 1, 4, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 5, NULL, 'noted', 'Noted by', 'specific_employee', 6, NULL, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', 'Office of the University President', 2, 2, 1, 'right', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(44, 1, 4, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 5, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'Dean', 'Office of the University President', 3, 1, 2, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL);
/*!40000 ALTER TABLE `sys_document_signatories` ENABLE KEYS */;

-- Dumping structure for table extend.sys_document_signatory_matrices
DROP TABLE IF EXISTS `sys_document_signatory_matrices`;
CREATE TABLE IF NOT EXISTS `sys_document_signatory_matrices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `doc_type_id` bigint unsigned DEFAULT NULL,
  `process_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tier_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prepared',
  `role_label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assignment_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed_employee',
  `employee_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `signatory_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '1',
  `position_column` tinyint unsigned NOT NULL DEFAULT '1',
  `col_span` tinyint unsigned NOT NULL DEFAULT '1',
  `alignment` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left',
  `requires_digital_signature` tinyint(1) NOT NULL DEFAULT '1',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `signature_instructions` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_document_signatory_matrices_campus_id_foreign` (`campus_id`),
  KEY `sys_document_signatory_matrices_employee_id_foreign` (`employee_id`),
  KEY `sys_document_signatory_matrices_user_id_foreign` (`user_id`),
  KEY `sys_document_signatory_matrices_tenant_id_process_type_index` (`tenant_id`,`process_type`),
  KEY `sys_document_signatory_matrices_tenant_id_campus_id_index` (`tenant_id`,`campus_id`),
  KEY `sys_document_signatory_matrices_doc_type_id_index` (`doc_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_document_signatory_matrices: 24 rows
DELETE FROM `sys_document_signatory_matrices`;
/*!40000 ALTER TABLE `sys_document_signatory_matrices` DISABLE KEYS */;
INSERT INTO `sys_document_signatory_matrices` (`id`, `tenant_id`, `campus_id`, `doc_type_id`, `process_type`, `tier_role`, `role_label`, `assignment_type`, `employee_id`, `user_id`, `signatory_name`, `designation`, `office`, `sort_order`, `position_column`, `col_span`, `alignment`, `requires_digital_signature`, `is_mandatory`, `is_active`, `signature_instructions`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(48, 1, NULL, 8, 'Board of Regents Resolution', 'approved', 'Approved by:', 'fixed_employee', 1, NULL, 'ADSSU System Administrator', 'Management Information Systems Director', 'Office of the University President', 3, 3, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 07:12:38', '2026-10-16 07:12:38', NULL),
	(31, 1, NULL, 43, 'EXTENSION PROPOSAL', 'approved_vpie', 'Approved:', 'specific_employee', NULL, NULL, 'ROMY JUN A. SUNICO, DTE', 'Vice President for Research, Innovation and Extension', 'Office of the Vice President for RIE', 7, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(30, 1, NULL, 43, 'EXTENSION PROPOSAL', 'recommended_director', 'Recommending Approval:', 'specific_employee', NULL, NULL, 'ALVIE ELMA C. PLAZA, MA', 'Extension Director', 'University Extension Directorate', 6, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(29, 1, NULL, 43, 'EXTENSION PROPOSAL', 'noted_director', 'Noted:', 'role_based', NULL, NULL, '', 'Campus Director', 'Office of the Campus Director', 5, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(28, 1, NULL, 43, 'EXTENSION PROPOSAL', 'noted_dean', 'Noted:', 'dynamic_college_dean', NULL, NULL, '', 'College Dean/Program Chair', 'Office of the College Dean', 4, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(26, 1, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_focal', 'Reviewed by:', 'role_based', NULL, NULL, '', 'Focal Person', 'Extension Unit', 2, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(27, 1, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_coordinator', 'Reviewed by:', 'role_based', NULL, NULL, '', 'College/Campus Research/Extension Coordinator', 'College Research and Extension Unit', 3, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(25, 1, NULL, 43, 'EXTENSION PROPOSAL', 'prepared', 'Prepared by:', 'dynamic_author', NULL, NULL, '[Project Leader / Proponent]', 'Project Leader', 'Project Proponent', 1, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(47, 1, NULL, 8, 'Board of Regents Resolution', 'noted', 'Noted by:', 'dynamic_dept_head', NULL, NULL, 'Reynaldo L. Alonzo EnP', 'Extension Head', 'Extension Services Office', 2, 2, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 07:12:38', '2026-10-16 07:12:38', NULL),
	(38, 8, NULL, 43, 'EXTENSION PROPOSAL', 'approved_vpie', 'Approved:', 'specific_employee', NULL, NULL, 'ROMY JUN A. SUNICO, DTE', 'Vice President for Research, Innovation and Extension', 'Office of the Vice President for RIE', 7, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(37, 8, NULL, 43, 'EXTENSION PROPOSAL', 'recommended_director', 'Recommending Approval:', 'specific_employee', NULL, NULL, 'ALVIE ELMA C. PLAZA, MA', 'Extension Director', 'University Extension Directorate', 6, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(36, 8, NULL, 43, 'EXTENSION PROPOSAL', 'noted_director', 'Noted:', 'role_based', NULL, NULL, '', 'Campus Director', 'Office of the Campus Director', 5, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(35, 8, NULL, 43, 'EXTENSION PROPOSAL', 'noted_dean', 'Noted:', 'dynamic_college_dean', NULL, NULL, '', 'College Dean/Program Chair', 'Office of the College Dean', 4, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(33, 8, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_focal', 'Reviewed by:', 'role_based', NULL, NULL, '', 'Focal Person', 'Extension Unit', 2, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(34, 8, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_coordinator', 'Reviewed by:', 'role_based', NULL, NULL, '', 'College/Campus Research/Extension Coordinator', 'College Research and Extension Unit', 3, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(32, 8, NULL, 43, 'EXTENSION PROPOSAL', 'prepared', 'Prepared by:', 'dynamic_author', NULL, NULL, '[Project Leader / Proponent]', 'Project Leader', 'Project Proponent', 1, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(46, 1, NULL, 8, 'Board of Regents Resolution', 'prepared', 'Prepared by:', 'dynamic_author', NULL, NULL, '[Project Leader / Author]', 'Project Leader', NULL, 1, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 07:12:38', '2026-10-16 07:12:38', NULL),
	(45, 9, NULL, 43, 'EXTENSION PROPOSAL', 'approved_vpie', 'Approved:', 'specific_employee', NULL, NULL, 'ROMY JUN A. SUNICO, DTE', 'Vice President for Research, Innovation and Extension', 'Office of the Vice President for RIE', 7, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(44, 9, NULL, 43, 'EXTENSION PROPOSAL', 'recommended_director', 'Recommending Approval:', 'specific_employee', NULL, NULL, 'ALVIE ELMA C. PLAZA, MA', 'Extension Director', 'University Extension Directorate', 6, 2, 1, 'center', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(43, 9, NULL, 43, 'EXTENSION PROPOSAL', 'noted_director', 'Noted:', 'role_based', NULL, NULL, '', 'Campus Director', 'Office of the Campus Director', 5, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(42, 9, NULL, 43, 'EXTENSION PROPOSAL', 'noted_dean', 'Noted:', 'dynamic_college_dean', NULL, NULL, '', 'College Dean/Program Chair', 'Office of the College Dean', 4, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(40, 9, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_focal', 'Reviewed by:', 'role_based', NULL, NULL, '', 'Focal Person', 'Extension Unit', 2, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(41, 9, NULL, 43, 'EXTENSION PROPOSAL', 'reviewed_coordinator', 'Reviewed by:', 'role_based', NULL, NULL, '', 'College/Campus Research/Extension Coordinator', 'College Research and Extension Unit', 3, 2, 1, 'right', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL),
	(39, 9, NULL, 43, 'EXTENSION PROPOSAL', 'prepared', 'Prepared by:', 'dynamic_author', NULL, NULL, '[Project Leader / Proponent]', 'Project Leader', 'Project Proponent', 1, 1, 1, 'left', 1, 1, 1, NULL, NULL, '2026-10-16 06:55:48', '2026-10-16 06:55:48', NULL);
/*!40000 ALTER TABLE `sys_document_signatory_matrices` ENABLE KEYS */;

-- Dumping structure for table extend.sys_institution_settings
DROP TABLE IF EXISTS `sys_institution_settings`;
CREATE TABLE IF NOT EXISTS `sys_institution_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `school_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_acronym` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'State University and College (SUC)',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_fy` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2026',
  `charter_basis` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seal_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watermark_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_variants` json DEFAULT NULL,
  `vision_statement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mission_statement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `document_settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_institution_settings_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_institution_settings: 1 rows
DELETE FROM `sys_institution_settings`;
/*!40000 ALTER TABLE `sys_institution_settings` DISABLE KEYS */;
INSERT INTO `sys_institution_settings` (`id`, `tenant_id`, `school_name`, `school_acronym`, `school_type`, `address`, `contact_email`, `contact_phone`, `website_url`, `current_fy`, `charter_basis`, `logo_path`, `seal_path`, `watermark_path`, `logo_variants`, `vision_statement`, `mission_statement`, `document_settings`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Surigao del Norte State University', 'SNSU', 'State University and College (SUC)', 'Narciso Street, Surigao City, Surigao del Norte, Philippines 8400', 'op@snsu.edu.ph', '(086) 826-1738', 'https://snsu.edu.ph', '2026', NULL, 'storage/tenants/1/branding/navbar-header-280x80.png', 'storage/tenants/1/branding/print-seal-1024x1024.png', 'storage/tenants/1/branding/watermark-translucent-600x600.png', '{"master": {"key": "master", "url": "http://extend.dev.com/storage/tenants/1/branding/master-512x512.png?v=1792980817", "name": "Master High-Resolution Logo", "badge": "512 × 512", "width": 512, "height": 512, "purpose": "High-resolution logo for institutional profile, portal landing, and login screen.", "file_name": "master-512x512.png", "storage_path": "tenants/1/branding/master-512x512.png", "relative_path": "storage/tenants/1/branding/master-512x512.png", "size_formatted": "199.9 KB"}, "navbar": {"key": "navbar", "url": "http://extend.dev.com/storage/tenants/1/branding/navbar-header-280x80.png?v=1792980816", "name": "Horizontal Navbar Header", "badge": "280 × 80", "width": 280, "height": 80, "purpose": "Primary brand logo for top application navigation bar and header banners.", "file_name": "navbar-header-280x80.png", "storage_path": "tenants/1/branding/navbar-header-280x80.png", "relative_path": "storage/tenants/1/branding/navbar-header-280x80.png", "size_formatted": "14.1 KB"}, "favicon": {"key": "favicon", "url": "http://extend.dev.com/storage/tenants/1/branding/favicon-32x32.png?v=1792980816", "name": "Browser Favicon", "badge": "32 × 32", "width": 32, "height": 32, "purpose": "Browser tab icon, bookmark shortcut, and mobile address bar icon.", "file_name": "favicon-32x32.png", "storage_path": "tenants/1/branding/favicon-32x32.png", "relative_path": "storage/tenants/1/branding/favicon-32x32.png", "size_formatted": "2.5 KB"}, "sidebar": {"key": "sidebar", "url": "http://extend.dev.com/storage/tenants/1/branding/sidebar-compact-120x120.png?v=1792980816", "name": "Compact Sidebar Mark", "badge": "120 × 120", "width": 120, "height": 120, "purpose": "Compact sidebar brand badge, collapsed menu icon, and small header mark.", "file_name": "sidebar-compact-120x120.png", "storage_path": "tenants/1/branding/sidebar-compact-120x120.png", "relative_path": "storage/tenants/1/branding/sidebar-compact-120x120.png", "size_formatted": "21.4 KB"}, "app_icon": {"key": "app_icon", "url": "http://extend.dev.com/storage/tenants/1/branding/app-icon-192x192.png?v=1792980816", "name": "PWA / Mobile App Icon", "badge": "192 × 192", "width": 192, "height": 192, "purpose": "Apple Touch icon, Android manifest launcher, and mobile home screen shortcut.", "file_name": "app-icon-192x192.png", "storage_path": "tenants/1/branding/app-icon-192x192.png", "relative_path": "storage/tenants/1/branding/app-icon-192x192.png", "size_formatted": "43 KB"}, "original": {"key": "original", "url": "http://extend.dev.com/storage/tenants/1/branding/original.png", "name": "Original Uploaded Master", "badge": "Original Master", "width": null, "height": null, "purpose": "Raw unprocessed master file uploaded by administrator.", "file_name": "original.png", "storage_path": "tenants/1/branding/original.png", "relative_path": "storage/tenants/1/branding/original.png", "size_formatted": "358.8 KB"}, "watermark": {"key": "watermark", "url": "http://extend.dev.com/storage/tenants/1/branding/watermark-translucent-600x600.png?v=1792980819", "name": "Security Document Watermark", "badge": "600 × 600 (22% Opacity)", "width": 600, "height": 600, "purpose": "Security watermark stamp for background printing across official documents and PDF exports.", "file_name": "watermark-translucent-600x600.png", "storage_path": "tenants/1/branding/watermark-translucent-600x600.png", "relative_path": "storage/tenants/1/branding/watermark-translucent-600x600.png", "size_formatted": "259.4 KB"}, "print_seal": {"key": "print_seal", "url": "http://extend.dev.com/storage/tenants/1/branding/print-seal-1024x1024.png?v=1792980818", "name": "Official SSR Print Seal (300 DPI)", "badge": "1024 × 1024", "width": 1024, "height": 1024, "purpose": "Ultra-crisp seal for A4 & Long SSR binder covers, certificates, and institutional reports.", "file_name": "print-seal-1024x1024.png", "storage_path": "tenants/1/branding/print-seal-1024x1024.png", "relative_path": "storage/tenants/1/branding/print-seal-1024x1024.png", "size_formatted": "652.4 KB"}}', 'A premier state university in ASEAN recognized for academic excellence, innovative green research, and responsive community extension.', 'To provide high-quality tertiary education, advanced research in agriculture, forestry, technology, and environmental governance, and empower communities in Agusan del Sur and Caraga Region.', NULL, '2026-09-16 04:06:48', '2026-10-25 18:13:39');
/*!40000 ALTER TABLE `sys_institution_settings` ENABLE KEYS */;

-- Dumping structure for table extend.sys_iso_document_controls
DROP TABLE IF EXISTS `sys_iso_document_controls`;
CREATE TABLE IF NOT EXISTS `sys_iso_document_controls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `department_id` bigint unsigned DEFAULT NULL,
  `document_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `doc_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rev_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `effective_date` date DEFAULT NULL,
  `page_no_format` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1 of {nb}',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_iso_document_controls_tenant_id_index` (`tenant_id`),
  KEY `sys_iso_document_controls_department_id_index` (`department_id`),
  KEY `sys_iso_document_controls_document_type_index` (`document_type`),
  KEY `sys_iso_document_controls_doc_no_index` (`doc_no`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_iso_document_controls: 13 rows
DELETE FROM `sys_iso_document_controls`;
/*!40000 ALTER TABLE `sys_iso_document_controls` DISABLE KEYS */;
INSERT INTO `sys_iso_document_controls` (`id`, `tenant_id`, `department_id`, `document_type`, `doc_no`, `rev_no`, `effective_date`, `page_no_format`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'University Memorandum', 'FM-ADSSU-OP-01-01', '1', '2026-01-15', '1 of {nb}', 'Executive presidential issuances and university-wide official memoranda.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 1, 2, 'University Memorandum', 'FM-ADSSU-BAC-01-01', '2', '2026-03-01', '1 of {nb}', 'Procurement notices, bidding timelines, and RA 9184 compliance memorandums.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(3, 1, 3, 'University Memorandum', 'FM-ADSSU-BUDGET-01-01', '1', '2026-01-20', '1 of {nb}', 'Budget allocation releases, expenditure advisories, and fiscal programming guidelines.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(4, 1, 4, 'University Memorandum', 'FM-ADSSU-OVPAA-01-01', '1', '2026-02-01', '1 of {nb}', 'Academic directives, curriculum standards, and faculty administrative circulars.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 1, 5, 'University Memorandum', 'FM-ADSSU-OVPAF-01-01', '1', '2026-02-01', '1 of {nb}', 'Administrative operations, physical plant governance, and fiscal guidelines.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(6, 1, 6, 'University Memorandum', 'FM-ADSSU-ACCT-01-01', '1', '2026-02-10', '1 of {nb}', 'COA compliance advisories, liquidation notices, and financial accounting standards.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(7, 1, 7, 'University Memorandum', 'FM-ADSSU-CASH-01-01', '1', '2026-01-15', '1 of {nb}', 'Disbursement schedules, payroll release advisories, and collection protocols.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(8, 1, 8, 'University Memorandum', 'FM-ADSSU-SPMO-01-01', '2', '2026-03-15', '1 of {nb}', 'Property custodianship, inventory inspection, and asset disposition advisories.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(9, 1, 9, 'University Memorandum', 'FM-ADSSU-HRMO-01-01', '3', '2026-04-13', '1 of {nb}', 'Civil Service Commission regulations, personnel notices, performance evaluations, and HR advisories.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(10, 1, 10, 'University Memorandum', 'FM-ADSSU-REG-01-01', '1', '2026-01-10', '1 of {nb}', 'Academic calendar advisories, enrollment directives, and graduation protocols.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(11, 1, 11, 'University Memorandum', 'FM-ADSSU-RECORDS-01-01', '1', '2026-01-05', '1 of {nb}', 'Document management circulars, DTS dispatch policies, and archiving standards.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(12, 1, 12, 'University Memorandum', 'FM-ADSSU-LEGAL-01-01', '1', '2026-02-15', '1 of {nb}', 'Legal opinions, compliance directives, and statutory governance circulars.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(13, 1, 13, 'University Memorandum', 'FM-ADSSU-TEST-01-01', '1', '2026-06-15', '1 of {nb}', 'Quality Assurance test control template.', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:49');
/*!40000 ALTER TABLE `sys_iso_document_controls` ENABLE KEYS */;

-- Dumping structure for table extend.sys_signatory_matrices
DROP TABLE IF EXISTS `sys_signatory_matrices`;
CREATE TABLE IF NOT EXISTS `sys_signatory_matrices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `process_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `max_amount` decimal(15,2) DEFAULT NULL,
  `step_order` smallint unsigned NOT NULL DEFAULT '1',
  `role_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_signatory_matrices_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_signatory_matrices: 6 rows
DELETE FROM `sys_signatory_matrices`;
/*!40000 ALTER TABLE `sys_signatory_matrices` DISABLE KEYS */;
INSERT INTO `sys_signatory_matrices` (`id`, `tenant_id`, `process_type`, `min_amount`, `max_amount`, `step_order`, `role_title`, `employee_name`, `designation`, `is_mandatory`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Purchase Request', 0.00, 5000000.00, 1, 'Requested by', 'Department Chair / Unit Head', 'Originating Unit Head', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 1, 'Purchase Request', 0.00, 5000000.00, 2, 'Allotment Clearance', 'Clarissa M. Gomez, CPA', 'Supervising Budget Officer', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(3, 1, 'Purchase Request', 50000.00, NULL, 3, 'Approved by', 'Dr. Joy C. Capistrano', 'SUC President III', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(4, 1, 'Purchase Order', 0.00, NULL, 1, 'Prepared by', 'Procurement Officer IV', 'Procurement Office', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 1, 'Purchase Order', 0.00, NULL, 2, 'Funds Available', 'Chief Accountant', 'Accounting Office', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(6, 1, 'Purchase Order', 0.00, NULL, 3, 'Approved by', 'Dr. Joy C. Capistrano', 'SUC President III', 1, '2026-09-16 04:06:48', '2026-09-16 04:06:48');
/*!40000 ALTER TABLE `sys_signatory_matrices` ENABLE KEYS */;

-- Dumping structure for table extend.sys_tenants
DROP TABLE IF EXISTS `sys_tenants`;
CREATE TABLE IF NOT EXISTS `sys_tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Institutional Enterprise',
  `status` enum('active','suspended','trial') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sys_tenants_uuid_unique` (`uuid`),
  UNIQUE KEY `sys_tenants_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_tenants: 1 rows
DELETE FROM `sys_tenants`;
/*!40000 ALTER TABLE `sys_tenants` DISABLE KEYS */;
INSERT INTO `sys_tenants` (`id`, `uuid`, `name`, `slug`, `domain`, `plan`, `status`, `created_at`, `updated_at`) VALUES
	(1, '6757c1de-ea03-4a9c-a8bb-0f32ed505919', 'Surigao del Norte State University', 'snsu', 'snsu.edu.ph', 'Institutional Enterprise', 'active', '2026-09-16 04:06:48', '2026-10-24 05:49:14');
/*!40000 ALTER TABLE `sys_tenants` ENABLE KEYS */;

-- Dumping structure for table extend.sys_theme_settings
DROP TABLE IF EXISTS `sys_theme_settings`;
CREATE TABLE IF NOT EXISTS `sys_theme_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `primary_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#047857',
  `secondary_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0284c7',
  `accent_color` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#f59e0b',
  `preset_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar_theme` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brand',
  `sidebar_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar_text` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar_active_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar_active_text` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sidebar_border` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `navbar_theme` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'light',
  `topbar_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topbar_text` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topbar_border` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `border_radius` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '12px',
  `btn_primary_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_primary_text` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_secondary_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_secondary_text` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_radius` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btn_elevation` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_bg` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_border` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_radius` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_dark_mode` tinyint(1) NOT NULL DEFAULT '0',
  `font_family` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''Plus Jakarta Sans'', sans-serif',
  `custom_css` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_theme_settings_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_theme_settings: 3 rows
DELETE FROM `sys_theme_settings`;
/*!40000 ALTER TABLE `sys_theme_settings` DISABLE KEYS */;
INSERT INTO `sys_theme_settings` (`id`, `tenant_id`, `primary_color`, `secondary_color`, `accent_color`, `preset_key`, `sidebar_theme`, `sidebar_bg`, `sidebar_text`, `sidebar_active_bg`, `sidebar_active_text`, `sidebar_border`, `navbar_theme`, `topbar_bg`, `topbar_text`, `topbar_border`, `border_radius`, `btn_primary_bg`, `btn_primary_text`, `btn_secondary_bg`, `btn_secondary_text`, `btn_radius`, `btn_elevation`, `card_bg`, `card_border`, `card_radius`, `is_dark_mode`, `font_family`, `custom_css`, `created_at`, `updated_at`) VALUES
	(1, 1, '#0a2400', '#0284c7', '#f59e0b', 'adssu_emerald', 'light', '#ffffff', '#1e293b', '#ecfdf5', '#047857', '#e2e8f0', 'light', '#ffffff', '#0f172a', '#e2e8f0', '10px', '#047857', '#ffffff', '#0284c7', '#ffffff', '8px', 'subtle', '#ffffff', '#e2e8f0', '12px', 0, 'Inter', NULL, '2026-09-16 04:06:48', '2026-09-16 15:32:51'),
	(9, 9, '#047857', '#0284c7', '#f59e0b', NULL, 'brand', NULL, NULL, NULL, NULL, NULL, 'light', NULL, NULL, NULL, '10px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '\'Poppins\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif', NULL, '2026-09-16 10:03:24', '2026-09-16 10:03:24'),
	(8, 8, '#047857', '#0284c7', '#f59e0b', 'adssu_emerald', 'brand', '#ffffff', '#1e293b', '#ecfdf5', '#047857', '#e2e8f0', 'light', '#ffffff', '#0f172a', '#e2e8f0', '10px', '#047857', '#ffffff', '#0284c7', '#ffffff', '8px', 'subtle', '#ffffff', '#e2e8f0', '12px', 0, 'Inter', NULL, '2026-09-16 09:17:57', '2026-09-16 09:38:12');
/*!40000 ALTER TABLE `sys_theme_settings` ENABLE KEYS */;

-- Dumping structure for table extend.sys_uploaded_documents
DROP TABLE IF EXISTS `sys_uploaded_documents`;
CREATE TABLE IF NOT EXISTS `sys_uploaded_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `module` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `documentable_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documentable_id` bigint unsigned DEFAULT NULL,
  `category` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `sha256_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_physically_signed` tinyint(1) NOT NULL DEFAULT '0',
  `signed_date` date DEFAULT NULL,
  `signatory_notes` text COLLATE utf8mb4_unicode_ci,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `status` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `uploaded_by_user_id` bigint unsigned DEFAULT NULL,
  `uploader_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sys_uploaded_documents_campus_id_foreign` (`campus_id`),
  KEY `sys_uploaded_documents_tenant_id_module_index` (`tenant_id`,`module`),
  KEY `sys_uploaded_documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  KEY `sys_uploaded_documents_is_physically_signed_index` (`is_physically_signed`),
  KEY `sys_uploaded_documents_category_index` (`category`),
  KEY `sys_uploaded_documents_uploaded_by_user_id_index` (`uploaded_by_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_uploaded_documents: 0 rows
DELETE FROM `sys_uploaded_documents`;
/*!40000 ALTER TABLE `sys_uploaded_documents` DISABLE KEYS */;
INSERT INTO `sys_uploaded_documents` (`id`, `tenant_id`, `campus_id`, `module`, `documentable_type`, `documentable_id`, `category`, `title`, `original_filename`, `file_path`, `file_size`, `mime_type`, `extension`, `disk`, `sha256_hash`, `is_physically_signed`, `signed_date`, `signatory_notes`, `version`, `status`, `uploaded_by_user_id`, `uploader_name`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 11, 'general', NULL, NULL, 'physically_signed_plan', 'Physically Signed Copy — INTERMEDIATE-PROGRAMMING-FINAL-EXAM', 'INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf', 'documents/general/2026/10/sjEL9aYIrAYcdrrw7FL3GgBh.pdf', 90361, 'application/pdf', 'pdf', 'public', 'dbed21ea048be301b327f8f98386945244360c27f9ede9ff87367d984441a0fd', 1, '2026-10-26', NULL, '1.0', 'active', 7, 'Antonio Jr. L. Piloton', '{"version": "1.0", "client_ip": "::1", "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0", "uploaded_at": "2026-10-26T03:24:42+00:00", "previous_version": null}', '2026-10-25 19:24:42', '2026-10-25 19:24:42', NULL),
	(2, 1, 11, 'general', 'App\\Models\\RIE\\ExtensionProposal', 1, 'physically_signed_plan', 'Physically Signed Copy — INTERMEDIATE-PROGRAMMING-FINAL-EXAM', 'INTERMEDIATE-PROGRAMMING-FINAL-EXAM.pdf', 'documents/general/2026/10/CUcDNHDYwbwyQOpbx3xxhFo2.pdf', 90361, 'application/pdf', 'pdf', 'public', 'dbed21ea048be301b327f8f98386945244360c27f9ede9ff87367d984441a0fd', 1, '2026-10-26', NULL, '1.0', 'active', 7, 'Antonio Jr. L. Piloton', '{"version": "1.0", "client_ip": "::1", "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0", "uploaded_at": "2026-10-26T13:39:27+00:00", "previous_version": null}', '2026-10-26 05:39:27', '2026-10-26 05:39:27', NULL),
	(3, 1, 11, 'general', 'App\\Models\\RIE\\ExtensionProposal', 1, 'general', 'Ma\'am jane', 'Ma\'am jane.pdf', 'documents/general/2026/10/YztZ4JOZzvZrZJy4VnqBeQBG.pdf', 1221228, 'application/pdf', 'pdf', 'public', 'b97a9fd77d44b49c61bd69c5d916c188b8a0576f5491e97c5934728d44655d1b', 0, '2026-10-26', NULL, '1.0', 'active', 7, 'Antonio Jr. L. Piloton', '{"version": "1.0", "client_ip": "::1", "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0", "uploaded_at": "2026-10-26T13:39:44+00:00", "previous_version": null}', '2026-10-26 05:39:44', '2026-10-26 05:39:44', NULL);
/*!40000 ALTER TABLE `sys_uploaded_documents` ENABLE KEYS */;

-- Dumping structure for table extend.units
DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL DEFAULT '1',
  `campus_id` bigint unsigned DEFAULT NULL,
  `dept_id` bigint unsigned DEFAULT NULL,
  `unit_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Administrative',
  `building` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `head_employee_id` bigint unsigned DEFAULT NULL,
  `head_name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension_head_employee_id` bigint unsigned DEFAULT NULL,
  `extension_head_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension_head_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_receiving_station` tinyint(1) NOT NULL DEFAULT '1',
  `is_originating_station` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dts_units_tenant_id_index` (`tenant_id`),
  KEY `dts_units_campus_id_index` (`campus_id`),
  KEY `dts_units_dept_id_index` (`dept_id`),
  KEY `dts_units_unit_code_index` (`unit_code`),
  KEY `dts_units_created_by_index` (`created_by`),
  KEY `dts_units_updated_by_index` (`updated_by`),
  KEY `dts_units_head_employee_id_foreign` (`head_employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.units: 16 rows
DELETE FROM `units`;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` (`id`, `tenant_id`, `campus_id`, `dept_id`, `unit_code`, `unit_name`, `short_name`, `unit_type`, `building`, `room_number`, `location_description`, `latitude`, `longitude`, `head_employee_id`, `head_name`, `head_title`, `extension_head_employee_id`, `extension_head_name`, `extension_head_title`, `email`, `contact_number`, `is_receiving_station`, `is_originating_station`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, NULL, 'OP', 'Office of the University President', 'OP', 'Executive', 'Administration Building', 'Executive Suite 301', NULL, 14.59951200, 120.98422200, NULL, 'Dr. Institutional President', 'University President', NULL, NULL, NULL, 'op@university.edu.ph', 'Loc. 1001', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(2, 1, 1, NULL, 'BAC', 'Bids and Awards Committee (BAC) Secretariat', 'BAC', 'Procurement', 'Procurement Building', 'Room 101', NULL, 14.59935000, 120.98410000, NULL, 'BAC Chairperson', 'Head, BAC Secretariat', NULL, NULL, NULL, 'bac@university.edu.ph', 'Loc. 1020', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(3, 1, 1, NULL, 'BUDGET', 'Budget Management Office', 'Budget Office', 'Financial', 'Finance & Accounting Complex', 'Room 102', NULL, 14.59942000, 120.98415000, NULL, 'Budget Officer IV', 'Director, Budget Management', NULL, NULL, NULL, 'budget@university.edu.ph', 'Loc. 1010', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(4, 1, 1, NULL, 'OVPAA', 'Office of the Vice President for Academic Affairs', 'OVPAA', 'Executive', 'Administration Building', 'Room 205', NULL, 14.59955000, 120.98425000, NULL, 'Dr. Vice President Academic', 'VP for Academic Affairs', NULL, NULL, NULL, 'ovpaa@university.edu.ph', 'Loc. 1002', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(5, 1, 1, NULL, 'OVPAF', 'Office of the Vice President for Admin & Finance', 'OVPAF', 'Executive', 'Administration Building', 'Room 201', NULL, 14.59957000, 120.98427000, NULL, 'Atty. Vice President Admin', 'VP for Administration and Finance', NULL, NULL, NULL, 'ovpaf@university.edu.ph', 'Loc. 1003', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(6, 1, 1, NULL, 'ACCT', 'Accounting Office', 'Accounting', 'Financial', 'Finance & Accounting Complex', 'Room 104', NULL, 14.59944000, 120.98418000, NULL, 'Chief Accountant CPA', 'Chief Accountant', NULL, NULL, NULL, 'accounting@university.edu.ph', 'Loc. 1012', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(7, 1, 1, NULL, 'CASH', 'Cashiering & Treasury Unit', 'Cashier', 'Financial', 'Finance & Accounting Complex', 'Window 1-4 Ground Floor', NULL, 14.59940000, 120.98413000, NULL, 'University Cashier', 'Head Cashier', NULL, NULL, NULL, 'cashier@university.edu.ph', 'Loc. 1014', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(8, 1, 1, NULL, 'SPMO', 'Supply and Property Management Office', 'SPMO', 'Procurement', 'University Logistics Center', 'Ground Floor', NULL, 14.59930000, 120.98405000, NULL, 'Property Custodian', 'Director, SPMO', NULL, NULL, NULL, 'supply@university.edu.ph', 'Loc. 1022', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(9, 1, 1, NULL, 'HRMO', 'Human Resource Management Office', 'HRMO', 'Administrative', 'Administration Building', 'Room 106', NULL, 14.59948000, 120.98421000, NULL, 'Director for HR', 'Chief HR Officer', NULL, NULL, NULL, 'hrmo@university.edu.ph', 'Loc. 1030', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(10, 1, 1, NULL, 'REG', 'Office of the University Registrar', 'Registrar', 'Academic', 'Student Services Center', 'Ground Floor Windows 1-8', NULL, 14.59960000, 120.98430000, NULL, 'University Registrar', 'University Registrar', NULL, NULL, NULL, 'registrar@university.edu.ph', 'Loc. 1040', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(11, 1, 1, NULL, 'RECORDS', 'Central Records and Archives Section', 'Records', 'Records', 'Administration Building', 'Room 101 Receiving & Mail', NULL, 14.59950000, 120.98420000, NULL, 'Records Management Officer', 'Head, Central Records', NULL, NULL, NULL, 'records@university.edu.ph', 'Loc. 1050', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(12, 1, 1, NULL, 'LEGAL', 'University Legal Services Office', 'Legal', 'Administrative', 'Administration Building', 'Room 304', NULL, 14.59952000, 120.98424000, NULL, 'University Legal Counsel', 'Chief Legal Officer', NULL, NULL, NULL, 'legal@university.edu.ph', 'Loc. 1060', 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(13, 1, NULL, NULL, 'TEST_OFFICE', 'Test Quality Assurance Office', 'TEST_OFFICE', 'Administrative', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 04:06:48', NULL),
	(14, 8, 10, NULL, 'OP', 'Office of the President', 'OP', 'Executive', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, '2026-09-16 09:17:57', '2026-09-16 09:17:57', NULL),
	(15, 1, 11, NULL, 'OP', 'Office of the President', 'OP', 'Executive', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, '2026-09-16 10:03:24', '2026-09-16 10:03:24', NULL),
	(16, 1, NULL, NULL, 'dfgdfg', 'grfesg', NULL, 'Administrative', NULL, NULL, NULL, NULL, NULL, 10, 'Jocelyn M. Fernandez', 'Visiting Professional Lecturer', NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 11, NULL, '2026-09-16 16:17:10', '2026-09-16 16:17:10', NULL),
	(17, 1, 1, NULL, 'CCIS', 'College of Computer and Information Sciences', 'CCIS', 'Academic', NULL, NULL, NULL, NULL, NULL, 11, 'VIRNILLE C. FRANCISCO, PhEdD', 'College of Computer and Information Sciences Dean', 12, 'JESSA G. HAMBRE', 'College of Computer and Information Sciences Head for Extension', NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20', NULL),
	(18, 1, 1, NULL, 'GS', 'Graduate School', 'GS', 'Academic', NULL, NULL, NULL, NULL, NULL, 13, 'MERLYN L. ESTOQUE, PhD', 'Graduate School Dean', 14, 'LOUIDA P. PATAC, PhD', 'Graduate School Head for Extension', NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:21', NULL),
	(19, 1, 1, NULL, 'CBT', 'College of Business and Technology', 'CBT', 'Academic', NULL, NULL, NULL, NULL, NULL, 15, 'MARILOU B. CARNICER, PhD', 'College of Business and Technology Dean', 16, 'MR. KENNETH CHARLS E. FEROL', 'College of Business and Technology Head for Extension', NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21', NULL),
	(20, 1, 1, NULL, 'CTE', 'College of Teacher Education', 'CTE', 'Academic', NULL, NULL, NULL, NULL, NULL, 17, 'ELVIS P. PATULIN, PhD, EdD', 'College of Teacher Education Dean', 18, 'MARIJUL B. JOVERO', 'College of Teacher Education Head for Extension', NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:22', NULL),
	(21, 1, 1, NULL, 'CAS', 'College of Arts and Sciences', 'CAS', 'Academic', NULL, NULL, NULL, NULL, NULL, 19, 'LOUIDA P. PATAC, PhD', 'College of Arts and Sciences Dean', 20, 'LUIS E. CUABO', 'College of Arts and Sciences Head for Extension', NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL),
	(22, 1, 1, NULL, 'UESO', 'University Extension Services Office', 'UESO', 'Administrative', NULL, NULL, NULL, NULL, NULL, 22, 'ALVIE ELMA C. PLAZA, MA', 'University Extension Director', NULL, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22', NULL);
/*!40000 ALTER TABLE `units` ENABLE KEYS */;

-- Dumping structure for table extend.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `campus_id` bigint unsigned DEFAULT NULL,
  `dept_id` bigint unsigned DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Staff',
  `designation` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  KEY `users_campus_id_foreign` (`campus_id`),
  KEY `users_dept_id_foreign` (`dept_id`),
  KEY `users_employee_id_foreign` (`employee_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.users: 12 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `employee_id`, `tenant_id`, `campus_id`, `dept_id`, `name`, `email`, `role`, `designation`, `phone_number`, `avatar_url`, `status`, `email_verified_at`, `password`, `google_id`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 'ADSSU System Administrator', 'admin@adssu.edu.ph', 'Admin', 'Management Information Systems Director', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, 'Rcr0Q6ITqXeVsk0auzFirafRmwlk9mn6mXw6WUI8x8PmfxjBgdDLKEd8Dt9g', '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(2, 2, 1, 1, 1, 'Dr. Joy C. Capistrano', 'president@adssu.edu.ph', 'President', 'SUC President III', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, 'QbBfBBenyZEJBZ3WbRgBuj6tANv4evPLb1I7jtZ6hiwrZRD69ri282pRA5c9', '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(3, 3, 1, 1, 2, 'Engr. Nelson V. Robles', 'bac@adssu.edu.ph', 'BACMember', 'Bids & Awards Committee Chairperson', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(4, 4, 1, 2, 1, 'Dr. Alan P. Martinez', 'director@adssu.edu.ph', 'Director', 'Campus Director, Trento Campus', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(5, 5, 1, 1, 1, 'Dr. QA Admin Lead', 'admin_test_qa@iipm.edu.ph', 'Admin', 'Institutional Officer', '09170000005', NULL, 'active', NULL, '$2y$12$AKVF9LRG4vEGZL7pkTTGAuKHWOMKZws0Y.AjikOF/Ik5W2UQhuW4i', NULL, NULL, '2026-09-16 04:14:14', '2026-10-26 04:27:08'),
	(6, 6, 1, 1, 1, 'Dr. Diane B. Remot (TL)', 'auditor_faculty_test@iipm.edu.ph', 'BACMember', 'Institutional Officer', '09170000006', NULL, 'active', NULL, '$2y$12$9LjhqU4tCWRRCA8Kd8YIg.9Rp/.SRANEk4wUrebJMhkHo2g9NsHH2', NULL, NULL, '2026-09-16 04:14:14', '2026-10-25 23:47:09'),
	(7, 7, 1, 11, 15, 'Engr. Antonio Piloton Jr.', 'apiloton1@snsu.edu.ph', 'SuperAdmin', 'Platform Super Administrator / Chief Enterprise Architect', '09128941731', 'https://lh3.googleusercontent.com/a/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c', 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, 'D0jgwrHK1LBcRQFO5PKXwx8nyx3WR7lb45agb5oVP7joL1aTMlNhuyh4JSnH', '2026-09-16 07:59:10', '2026-10-23 19:22:50'),
	(10, NULL, 8, 10, 14, 'ANTONIO JR. L. PILOTON', 'apiloton@ssct.edu.ph', 'SuperAdmin', 'Campus Information Systems Administrator', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocL3wAymfBAhWbSdEodGOmAd_GQYm5owScFjk3IjUnV-AxNh7o8=s96-c', 'active', NULL, '$2y$12$Qt1ZMLGQIgZ9UMrvkNE2KuAXFh.gcP/Z/tfBmqA.t0pTbq6ulSLzq', NULL, 'lK0AsujsPwhO9GwS5zyJuUjX1m0qcY75AtNIvrQKphm5X01tgtrEFU0F5awu', '2026-09-16 09:17:57', '2026-09-16 09:46:23'),
	(11, NULL, 1, 11, 15, 'ANTONIO JR. L. PILOTON', 'antoniojrpiloton@gmail.com', 'Director', 'Campus Information Systems Administrator', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c', 'active', NULL, '$2y$12$eiK2UctO6bAmF/WQ/Q5bg.fiDtUakJ3.LMS00GYU3WKIeAMtSkFr6', NULL, 'cpFQtNGdcelo7KGUdRPFiIYnbv0CoG1mT5c2YdL0qTJcBhgO3AIUFqp6ZAJc', '2026-09-16 10:03:25', '2026-09-16 14:58:53'),
	(12, 8, 1, 1, 8, 'Maria Elena S. Villanueva', 'm.villanueva@adssu.edu.ph', 'Employee', 'Property Custodian / Laboratory Aide', '09175551234', NULL, 'active', NULL, '$2y$12$hHIN/UeMG7ytOOA1dE9BOO.hCBbnm3/lFQJBmU63nfo4YmIl5Vjha', NULL, NULL, '2026-09-16 14:11:06', '2026-09-16 14:24:58'),
	(13, 10, 1, 2, 4, 'Jocelyn M. Fernandez', 'j.fernandez@adssu.edu.ph', 'Admin', 'Visiting Professional Lecturer', '09195553456', NULL, 'active', NULL, '$2y$12$1ei3sS7qrR1Jr9WEKVwkM.SzC8/48RmoXM7o5eSB/vCv0xN8XzkU6', NULL, NULL, '2026-09-16 14:12:46', '2026-09-16 14:12:46'),
	(14, 9, 1, 1, 9, 'Rodrigo T. Dela Cruz Jr.', 'r.delacruz@adssu.edu.ph', 'Employee', 'Administrative Aide VI', '09185552345', NULL, 'active', NULL, '$2y$12$EbgMRZTZumNx6DOmX3sUN.3ae07th1euFdUjOWwi.MgKGBpyZltp6', NULL, NULL, '2026-09-16 14:13:06', '2026-09-16 14:13:06'),
	(15, 11, 1, 1, 17, 'VIRNILLE C. FRANCISCO, PhEdD', 'v.francisco@adssu.edu.ph', 'Staff', 'College of Computer and Information Sciences Dean', NULL, NULL, 'active', NULL, '$2y$12$6R.qF1hrGy0tB/f7FPT30.eYtUfyTxBZ5SmBz6HLXy/khfOGmMe8q', NULL, NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20'),
	(16, 12, 1, 1, 17, 'JESSA G. HAMBRE', 'j.hambre@adssu.edu.ph', 'Staff', 'College of Computer and Information Sciences Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$J4wAhAFt/bdRbmt4AcxJguyJu9tlYzf.vLt4UO5CV6o714USL4Eiq', NULL, NULL, '2026-10-26 06:46:20', '2026-10-26 06:46:20'),
	(17, 13, 1, 1, 18, 'MERLYN L. ESTOQUE, PhD', 'm.estoque@adssu.edu.ph', 'Staff', 'Graduate School Dean', NULL, NULL, 'active', NULL, '$2y$12$2vtXv0gXVjR.QVAiq1NhauszxyNJihkaD9NpNrNFHnr0ryb20sN8m', NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21'),
	(18, 14, 1, 1, 18, 'LOUIDA P. PATAC, PhD', 'l.patac@adssu.edu.ph', 'Staff', 'Graduate School Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$tGmc8g.0/W7p/2M1SISP9us8VlxZOkyDtj6qo2EmhRiaSS4w3xMxa', NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21'),
	(19, 15, 1, 1, 19, 'MARILOU B. CARNICER, PhD', 'm.carnicer@adssu.edu.ph', 'Staff', 'College of Business and Technology Dean', NULL, NULL, 'active', NULL, '$2y$12$I6N/D4rcW9ZUleP3fbygQejVO4XmzSbSmAE9BjtlP0/soSjPl3z26', NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21'),
	(20, 16, 1, 1, 19, 'MR. KENNETH CHARLS E. FEROL', 'k.ferol@adssu.edu.ph', 'Staff', 'College of Business and Technology Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$yv/dAX1RAhhyvHCDYAERGOocQM6zW19hvSVfyQfbaGe.ZZaCEV/Mi', NULL, NULL, '2026-10-26 06:46:21', '2026-10-26 06:46:21'),
	(21, 17, 1, 1, 20, 'ELVIS P. PATULIN, PhD, EdD', 'e.patulin@adssu.edu.ph', 'Staff', 'College of Teacher Education Dean', NULL, NULL, 'active', NULL, '$2y$12$Rt0/ydTlUMUj1PNAPNDXN.cFxWzhQGz.p1fbYen00MfOXw4jVJX5C', NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22'),
	(22, 18, 1, 1, 20, 'MARIJUL B. JOVERO', 'm.jovero@adssu.edu.ph', 'Staff', 'College of Teacher Education Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$dxSbFvtWLnxpzAG1HXhZaOJsInh2V98dceibb2SdD0ZS7uLHce4K2', NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22'),
	(23, 19, 1, 1, 21, 'LOUIDA P. PATAC, PhD', 'l.patac.cas@adssu.edu.ph', 'Staff', 'College of Arts and Sciences Dean', NULL, NULL, 'active', NULL, '$2y$12$WhX5p/D0wUGXDjEWBSHA4uCFGhK6CigNT/uK2iDW2Y/C3370aCXYG', NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22'),
	(24, 20, 1, 1, 21, 'LUIS E. CUABO', 'l.cuabo@adssu.edu.ph', 'Staff', 'College of Arts and Sciences Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$S5MQjImllW3B1xQfLN3.SO/1M1Zy7zQW6sODf3sHJQJRlNbWEwZ16', NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
