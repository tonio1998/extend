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

-- Dumping data for table extend.cache: 4 rows
DELETE FROM `cache`;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('extension-management-system-cache-spatie.permission.cache', 'a:3:{s:5:"alias";a:4:{s:1:"a";s:2:"id";s:1:"b";s:4:"name";s:1:"c";s:10:"guard_name";s:1:"r";s:5:"roles";}s:11:"permissions";a:31:{i:0;a:4:{s:1:"a";i:23;s:1:"b";s:23:"system.hierarchy.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:1;a:4:{s:1:"a";i:24;s:1:"b";s:22:"system.theme.customize";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:2;a:4:{s:1:"a";i:25;s:1:"b";s:19:"system.roles.manage";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:3;a:4:{s:1:"a";i:26;s:1:"b";s:19:"system.users.assign";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:4;a:4:{s:1:"a";i:28;s:1:"b";s:14:"rie.plans.view";s:1:"c";s:3:"web";s:1:"r";a:16:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:15;i:5;i:16;i:6;i:18;i:7;i:19;i:8;i:20;i:9;i:21;i:10;i:22;i:11;i:23;i:12;i:24;i:13;i:25;i:14;i:26;i:15;i:27;}}i:5;a:4:{s:1:"a";i:29;s:1:"b";s:16:"rie.plans.create";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:5;i:2;i:16;i:3;i:25;}}i:6;a:4:{s:1:"a";i:30;s:1:"b";s:16:"rie.plans.manage";s:1:"c";s:3:"web";s:1:"r";a:8:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:18;i:5;i:22;i:6;i:26;i:7;i:27;}}i:7;a:4:{s:1:"a";i:31;s:1:"b";s:16:"rie.plans.delete";s:1:"c";s:3:"web";s:1:"r";a:2:{i:0;i:1;i:1;i:5;}}i:8;a:4:{s:1:"a";i:32;s:1:"b";s:18:"rie.proposals.view";s:1:"c";s:3:"web";s:1:"r";a:17:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:15;i:5;i:16;i:6;i:18;i:7;i:19;i:8;i:20;i:9;i:21;i:10;i:22;i:11;i:23;i:12;i:24;i:13;i:25;i:14;i:26;i:15;i:27;i:16;i:28;}}i:9;a:4:{s:1:"a";i:33;s:1:"b";s:20:"rie.proposals.create";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:5;i:2;i:16;i:3;i:25;}}i:10;a:4:{s:1:"a";i:34;s:1:"b";s:20:"rie.proposals.manage";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:11;a:4:{s:1:"a";i:35;s:1:"b";s:20:"rie.proposals.review";s:1:"c";s:3:"web";s:1:"r";a:8:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:18;i:5;i:22;i:6;i:26;i:7;i:28;}}i:12;a:4:{s:1:"a";i:36;s:1:"b";s:16:"rie.budgets.view";s:1:"c";s:3:"web";s:1:"r";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:21;}}i:13;a:4:{s:1:"a";i:37;s:1:"b";s:18:"rie.budgets.manage";s:1:"c";s:3:"web";s:1:"r";a:5:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:21;}}i:14;a:4:{s:1:"a";i:38;s:1:"b";s:17:"rie.tracking.view";s:1:"c";s:3:"web";s:1:"r";a:9:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:18;i:5;i:20;i:6;i:23;i:7;i:26;i:8;i:27;}}i:15;a:4:{s:1:"a";i:39;s:1:"b";s:19:"rie.tracking.manage";s:1:"c";s:3:"web";s:1:"r";a:6:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:20;i:5;i:27;}}i:16;a:4:{s:1:"a";i:40;s:1:"b";s:19:"rie.signatures.sign";s:1:"c";s:3:"web";s:1:"r";a:10:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:16;i:5;i:18;i:6;i:19;i:7;i:24;i:8;i:25;i:9;i:26;}}i:17;a:4:{s:1:"a";i:41;s:1:"b";s:20:"rie.documents.manage";s:1:"c";s:3:"web";s:1:"r";a:13:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:16;i:5;i:18;i:6;i:20;i:7;i:21;i:8;i:22;i:9;i:23;i:10;i:25;i:11;i:26;i:12;i:27;}}i:18;a:4:{s:1:"a";i:42;s:1:"b";s:19:"rie.settings.manage";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:19;a:4:{s:1:"a";i:43;s:1:"b";s:14:"dashboard.view";s:1:"c";s:3:"web";s:1:"r";a:18:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:15;i:5;i:16;i:6;i:18;i:7;i:19;i:8;i:20;i:9;i:21;i:10;i:22;i:11;i:23;i:12;i:24;i:13;i:25;i:14;i:26;i:15;i:27;i:16;i:28;i:17;i:29;}}i:20;a:4:{s:1:"a";i:44;s:1:"b";s:22:"rie.proposals.evaluate";s:1:"c";s:3:"web";s:1:"r";a:5:{i:0;i:1;i:1;i:5;i:2;i:18;i:3;i:26;i:4;i:28;}}i:21;a:4:{s:1:"a";i:45;s:1:"b";s:20:"rie.approvals.manage";s:1:"c";s:3:"web";s:1:"r";a:6:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:19;i:5;i:24;}}i:22;a:4:{s:1:"a";i:46;s:1:"b";s:25:"rie.implementation.manage";s:1:"c";s:3:"web";s:1:"r";a:5:{i:0;i:1;i:1;i:5;i:2;i:18;i:3;i:25;i:4;i:26;}}i:23;a:4:{s:1:"a";i:47;s:1:"b";s:21:"rie.activities.manage";s:1:"c";s:3:"web";s:1:"r";a:4:{i:0;i:1;i:1;i:5;i:2;i:25;i:3;i:27;}}i:24;a:4:{s:1:"a";i:48;s:1:"b";s:24:"rie.beneficiaries.manage";s:1:"c";s:3:"web";s:1:"r";a:6:{i:0;i:1;i:1;i:5;i:2;i:18;i:3;i:25;i:4;i:26;i:5;i:27;}}i:25;a:4:{s:1:"a";i:49;s:1:"b";s:19:"rie.partners.manage";s:1:"c";s:3:"web";s:1:"r";a:6:{i:0;i:1;i:1;i:5;i:2;i:18;i:3;i:25;i:4;i:26;i:5;i:27;}}i:26;a:4:{s:1:"a";i:50;s:1:"b";s:26:"rie.reports.accomplishment";s:1:"c";s:3:"web";s:1:"r";a:8:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:18;i:5;i:22;i:6;i:25;i:7;i:26;}}i:27;a:4:{s:1:"a";i:51;s:1:"b";s:19:"rie.impact.evaluate";s:1:"c";s:3:"web";s:1:"r";a:8:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:18;i:5;i:22;i:6;i:26;i:7;i:28;}}i:28;a:4:{s:1:"a";i:52;s:1:"b";s:18:"rie.documents.view";s:1:"c";s:3:"web";s:1:"r";a:18:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:15;i:5;i:16;i:6;i:18;i:7;i:19;i:8;i:20;i:9;i:21;i:10;i:22;i:11;i:23;i:12;i:24;i:13;i:25;i:14;i:26;i:15;i:27;i:16;i:28;i:17;i:29;}}i:29;a:4:{s:1:"a";i:53;s:1:"b";s:21:"rie.reports.analytics";s:1:"c";s:3:"web";s:1:"r";a:8:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;i:4;i:19;i:5;i:24;i:6;i:27;i:7;i:29;}}i:30;a:4:{s:1:"a";i:54;s:1:"b";s:17:"system.audit.view";s:1:"c";s:3:"web";s:1:"r";a:3:{i:0;i:1;i:1;i:5;i:2;i:29;}}}s:5:"roles";a:18:{i:0;a:3:{s:1:"a";i:1;s:1:"b";s:10:"SuperAdmin";s:1:"c";s:3:"web";}i:1;a:3:{s:1:"a";i:5;s:1:"b";s:5:"Admin";s:1:"c";s:3:"web";}i:2;a:3:{s:1:"a";i:2;s:1:"b";s:9:"President";s:1:"c";s:3:"web";}i:3;a:3:{s:1:"a";i:4;s:1:"b";s:8:"Director";s:1:"c";s:3:"web";}i:4;a:3:{s:1:"a";i:15;s:1:"b";s:8:"Employee";s:1:"c";s:3:"web";}i:5;a:3:{s:1:"a";i:16;s:1:"b";s:7:"Faculty";s:1:"c";s:3:"web";}i:6;a:3:{s:1:"a";i:18;s:1:"b";s:22:"College Extension Head";s:1:"c";s:3:"web";}i:7;a:3:{s:1:"a";i:19;s:1:"b";s:8:"UnitHead";s:1:"c";s:3:"web";}i:8;a:3:{s:1:"a";i:20;s:1:"b";s:26:"Extension Tracking Officer";s:1:"c";s:3:"web";}i:9;a:3:{s:1:"a";i:21;s:1:"b";s:24:"Extension Budget Officer";s:1:"c";s:3:"web";}i:10;a:3:{s:1:"a";i:22;s:1:"b";s:21:"Extension M&E Officer";s:1:"c";s:3:"web";}i:11;a:3:{s:1:"a";i:23;s:1:"b";s:28:"Extension Document Custodian";s:1:"c";s:3:"web";}i:12;a:3:{s:1:"a";i:24;s:1:"b";s:4:"Dean";s:1:"c";s:3:"web";}i:13;a:3:{s:1:"a";i:25;s:1:"b";s:14:"Project Leader";s:1:"c";s:3:"web";}i:14;a:3:{s:1:"a";i:26;s:1:"b";s:21:"Extension Coordinator";s:1:"c";s:3:"web";}i:15;a:3:{s:1:"a";i:27;s:1:"b";s:15:"Extension Staff";s:1:"c";s:3:"web";}i:16;a:3:{s:1:"a";i:28;s:1:"b";s:9:"Evaluator";s:1:"c";s:3:"web";}i:17;a:3:{s:1:"a";i:29;s:1:"b";s:7:"Auditor";s:1:"c";s:3:"web";}}}', 1793489384),
	('extension-management-system-cache-boost:mcp:database-schema:mysql:sys_theme_settings:0:0:0:1', 'a:2:{s:6:"engine";s:5:"mysql";s:6:"tables";a:1:{s:18:"sys_theme_settings";a:5:{s:7:"columns";a:31:{s:2:"id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:1;}s:9:"tenant_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"primary_color";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:0;s:7:"default";s:7:"#047857";s:14:"auto_increment";b:0;}s:15:"secondary_color";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:0;s:7:"default";s:7:"#0284c7";s:14:"auto_increment";b:0;}s:12:"accent_color";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:0;s:7:"default";s:7:"#f59e0b";s:14:"auto_increment";b:0;}s:10:"preset_key";a:4:{s:4:"type";s:11:"varchar(50)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"sidebar_theme";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:0;s:7:"default";s:5:"brand";s:14:"auto_increment";b:0;}s:10:"sidebar_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:12:"sidebar_text";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:17:"sidebar_active_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"sidebar_active_text";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:14:"sidebar_border";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:12:"navbar_theme";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:0;s:7:"default";s:5:"light";s:14:"auto_increment";b:0;}s:9:"topbar_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:11:"topbar_text";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"topbar_border";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"border_radius";a:4:{s:4:"type";s:11:"varchar(20)";s:8:"nullable";b:0;s:7:"default";s:4:"12px";s:14:"auto_increment";b:0;}s:14:"btn_primary_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:16:"btn_primary_text";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:16:"btn_secondary_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:18:"btn_secondary_text";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"btn_radius";a:4:{s:4:"type";s:11:"varchar(20)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"btn_elevation";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:7:"card_bg";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:11:"card_border";a:4:{s:4:"type";s:11:"varchar(30)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:11:"card_radius";a:4:{s:4:"type";s:11:"varchar(20)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:12:"is_dark_mode";a:4:{s:4:"type";s:10:"tinyint(1)";s:8:"nullable";b:0;s:7:"default";s:1:"0";s:14:"auto_increment";b:0;}s:11:"font_family";a:4:{s:4:"type";s:12:"varchar(100)";s:8:"nullable";b:0;s:7:"default";s:31:"\'Plus Jakarta Sans\', sans-serif";s:14:"auto_increment";b:0;}s:10:"custom_css";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"created_at";a:4:{s:4:"type";s:9:"timestamp";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"updated_at";a:4:{s:4:"type";s:9:"timestamp";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}}s:7:"indexes";a:2:{s:7:"primary";a:4:{s:7:"columns";a:1:{i:0;s:2:"id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:1;s:10:"is_primary";b:1;}s:36:"sys_theme_settings_tenant_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:9:"tenant_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}}s:12:"foreign_keys";a:0:{}s:8:"triggers";a:0:{}s:17:"check_constraints";a:0:{}}}}', 1793367940),
	('extension-management-system-cache-roster:project:v3:0ce774e927c0e1978cb3d1cfdfb83c3e', 'O:26:"Laravel\\Roster\\ProjectScan":8:{s:8:"basePath";s:21:"C:\\wamp64\\www\\extend\\";s:3:"php";O:35:"Laravel\\Roster\\Ecosystems\\Ecosystem":2:{s:9:"\0*\0byName";a:132:{s:23:"barryvdh/laravel-dompdf";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"barryvdh/laravel-dompdf";s:10:"\0*\0version";s:5:"3.1.2";s:9:"\0*\0source";E:43:"Laravel\\Roster\\Enums\\PackageSource:Composer";s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^3.1";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\barryvdh\\laravel-dompdf";}s:10:"brick/math";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:10:"brick/math";s:10:"\0*\0version";s:5:"1.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:38:"C:\\wamp64\\www\\extend\\vendor\\brick\\math";}s:31:"carbonphp/carbon-doctrine-types";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:31:"carbonphp/carbon-doctrine-types";s:10:"\0*\0version";s:5:"3.2.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:59:"C:\\wamp64\\www\\extend\\vendor\\carbonphp\\carbon-doctrine-types";}s:20:"codedge/laravel-fpdf";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"codedge/laravel-fpdf";s:10:"\0*\0version";s:6:"1.13.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^1.13";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\codedge\\laravel-fpdf";}s:23:"dflydev/dot-access-data";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"dflydev/dot-access-data";s:10:"\0*\0version";s:5:"3.0.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\dflydev\\dot-access-data";}s:18:"doctrine/inflector";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"doctrine/inflector";s:10:"\0*\0version";s:5:"2.1.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\vendor\\doctrine\\inflector";}s:14:"doctrine/lexer";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"doctrine/lexer";s:10:"\0*\0version";s:5:"3.0.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\doctrine\\lexer";}s:13:"dompdf/dompdf";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"dompdf/dompdf";s:10:"\0*\0version";s:5:"3.1.6";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\dompdf\\dompdf";}s:19:"dompdf/php-font-lib";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"dompdf/php-font-lib";s:10:"\0*\0version";s:5:"1.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\dompdf\\php-font-lib";}s:18:"dompdf/php-svg-lib";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"dompdf/php-svg-lib";s:10:"\0*\0version";s:5:"1.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\vendor\\dompdf\\php-svg-lib";}s:29:"dragonmantank/cron-expression";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:29:"dragonmantank/cron-expression";s:10:"\0*\0version";s:5:"3.6.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:57:"C:\\wamp64\\www\\extend\\vendor\\dragonmantank\\cron-expression";}s:23:"egulias/email-validator";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"egulias/email-validator";s:10:"\0*\0version";s:5:"4.0.4";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\egulias\\email-validator";}s:16:"firebase/php-jwt";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"firebase/php-jwt";s:10:"\0*\0version";s:5:"7.2.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\firebase\\php-jwt";}s:18:"fruitcake/php-cors";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"fruitcake/php-cors";s:10:"\0*\0version";s:5:"1.4.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\vendor\\fruitcake\\php-cors";}s:27:"graham-campbell/result-type";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:27:"graham-campbell/result-type";s:10:"\0*\0version";s:5:"1.2.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:55:"C:\\wamp64\\www\\extend\\vendor\\graham-campbell\\result-type";}s:17:"guzzlehttp/guzzle";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"guzzlehttp/guzzle";s:10:"\0*\0version";s:5:"8.3.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\guzzlehttp\\guzzle";}s:19:"guzzlehttp/promises";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"guzzlehttp/promises";s:10:"\0*\0version";s:5:"3.1.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\guzzlehttp\\promises";}s:15:"guzzlehttp/psr7";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"guzzlehttp/psr7";s:10:"\0*\0version";s:5:"3.2.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\guzzlehttp\\psr7";}s:23:"guzzlehttp/uri-template";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"guzzlehttp/uri-template";s:10:"\0*\0version";s:5:"2.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\guzzlehttp\\uri-template";}s:17:"laravel/framework";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"laravel/framework";s:10:"\0*\0version";s:7:"13.38.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^13.17";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\laravel\\framework";}s:15:"laravel/prompts";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"laravel/prompts";s:10:"\0*\0version";s:6:"0.3.26";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\laravel\\prompts";}s:28:"laravel/serializable-closure";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:28:"laravel/serializable-closure";s:10:"\0*\0version";s:5:"2.1.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:56:"C:\\wamp64\\www\\extend\\vendor\\laravel\\serializable-closure";}s:17:"laravel/socialite";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"laravel/socialite";s:10:"\0*\0version";s:6:"5.31.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^5.31";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\laravel\\socialite";}s:14:"laravel/tinker";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"laravel/tinker";s:10:"\0*\0version";s:5:"3.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^3.0";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\laravel\\tinker";}s:17:"league/commonmark";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"league/commonmark";s:10:"\0*\0version";s:6:"2.10.4";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\league\\commonmark";}s:13:"league/config";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"league/config";s:10:"\0*\0version";s:5:"1.2.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\league\\config";}s:16:"league/flysystem";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"league/flysystem";s:10:"\0*\0version";s:6:"3.36.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\league\\flysystem";}s:22:"league/flysystem-local";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"league/flysystem-local";s:10:"\0*\0version";s:6:"3.35.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\league\\flysystem-local";}s:26:"league/mime-type-detection";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:26:"league/mime-type-detection";s:10:"\0*\0version";s:6:"1.17.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:54:"C:\\wamp64\\www\\extend\\vendor\\league\\mime-type-detection";}s:20:"league/oauth1-client";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"league/oauth1-client";s:10:"\0*\0version";s:6:"1.12.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\league\\oauth1-client";}s:10:"league/uri";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:10:"league/uri";s:10:"\0*\0version";s:5:"7.9.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:38:"C:\\wamp64\\www\\extend\\vendor\\league\\uri";}s:21:"league/uri-interfaces";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"league/uri-interfaces";s:10:"\0*\0version";s:5:"7.9.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\league\\uri-interfaces";}s:17:"masterminds/html5";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"masterminds/html5";s:10:"\0*\0version";s:6:"2.11.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\masterminds\\html5";}s:13:"milon/barcode";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"milon/barcode";s:10:"\0*\0version";s:6:"13.5.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^13.5";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\milon\\barcode";}s:15:"monolog/monolog";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"monolog/monolog";s:10:"\0*\0version";s:6:"3.12.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\monolog\\monolog";}s:13:"nesbot/carbon";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"nesbot/carbon";s:10:"\0*\0version";s:6:"3.15.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\nesbot\\carbon";}s:12:"nette/schema";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"nette/schema";s:10:"\0*\0version";s:5:"1.4.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:40:"C:\\wamp64\\www\\extend\\vendor\\nette\\schema";}s:11:"nette/utils";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"nette/utils";s:10:"\0*\0version";s:5:"4.1.6";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\nette\\utils";}s:16:"nikic/php-parser";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"nikic/php-parser";s:10:"\0*\0version";s:5:"5.9.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\nikic\\php-parser";}s:19:"nunomaduro/termwind";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"nunomaduro/termwind";s:10:"\0*\0version";s:5:"2.4.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\nunomaduro\\termwind";}s:32:"paragonie/constant_time_encoding";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:32:"paragonie/constant_time_encoding";s:10:"\0*\0version";s:5:"3.1.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:60:"C:\\wamp64\\www\\extend\\vendor\\paragonie\\constant_time_encoding";}s:19:"phpoption/phpoption";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"phpoption/phpoption";s:10:"\0*\0version";s:6:"1.10.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\phpoption\\phpoption";}s:19:"phpseclib/phpseclib";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"phpseclib/phpseclib";s:10:"\0*\0version";s:5:"4.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\phpseclib\\phpseclib";}s:9:"psr/clock";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:9:"psr/clock";s:10:"\0*\0version";s:5:"1.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:37:"C:\\wamp64\\www\\extend\\vendor\\psr\\clock";}s:13:"psr/container";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"psr/container";s:10:"\0*\0version";s:5:"2.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\psr\\container";}s:20:"psr/event-dispatcher";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"psr/event-dispatcher";s:10:"\0*\0version";s:5:"1.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\psr\\event-dispatcher";}s:15:"psr/http-client";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"psr/http-client";s:10:"\0*\0version";s:5:"1.0.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\psr\\http-client";}s:16:"psr/http-factory";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"psr/http-factory";s:10:"\0*\0version";s:5:"1.1.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\psr\\http-factory";}s:16:"psr/http-message";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"psr/http-message";s:10:"\0*\0version";s:3:"2.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\psr\\http-message";}s:7:"psr/log";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:7:"psr/log";s:10:"\0*\0version";s:5:"3.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:35:"C:\\wamp64\\www\\extend\\vendor\\psr\\log";}s:16:"psr/simple-cache";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"psr/simple-cache";s:10:"\0*\0version";s:5:"3.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\psr\\simple-cache";}s:9:"psy/psysh";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:9:"psy/psysh";s:10:"\0*\0version";s:7:"0.12.24";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:37:"C:\\wamp64\\www\\extend\\vendor\\psy\\psysh";}s:17:"ramsey/collection";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"ramsey/collection";s:10:"\0*\0version";s:5:"2.1.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\ramsey\\collection";}s:11:"ramsey/uuid";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"ramsey/uuid";s:10:"\0*\0version";s:5:"4.9.4";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\ramsey\\uuid";}s:25:"sabberworm/php-css-parser";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"sabberworm/php-css-parser";s:10:"\0*\0version";s:5:"9.5.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\sabberworm\\php-css-parser";}s:28:"spatie/laravel-package-tools";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:28:"spatie/laravel-package-tools";s:10:"\0*\0version";s:6:"1.93.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:56:"C:\\wamp64\\www\\extend\\vendor\\spatie\\laravel-package-tools";}s:25:"spatie/laravel-permission";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"spatie/laravel-permission";s:10:"\0*\0version";s:5:"8.3.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^8.3";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\spatie\\laravel-permission";}s:13:"symfony/clock";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"symfony/clock";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\symfony\\clock";}s:15:"symfony/console";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"symfony/console";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\symfony\\console";}s:20:"symfony/css-selector";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"symfony/css-selector";s:10:"\0*\0version";s:6:"7.4.18";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\symfony\\css-selector";}s:29:"symfony/deprecation-contracts";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:29:"symfony/deprecation-contracts";s:10:"\0*\0version";s:5:"3.7.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:57:"C:\\wamp64\\www\\extend\\vendor\\symfony\\deprecation-contracts";}s:21:"symfony/error-handler";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"symfony/error-handler";s:10:"\0*\0version";s:6:"7.4.20";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\symfony\\error-handler";}s:24:"symfony/event-dispatcher";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:24:"symfony/event-dispatcher";s:10:"\0*\0version";s:6:"7.4.17";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:52:"C:\\wamp64\\www\\extend\\vendor\\symfony\\event-dispatcher";}s:34:"symfony/event-dispatcher-contracts";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:34:"symfony/event-dispatcher-contracts";s:10:"\0*\0version";s:5:"3.7.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:62:"C:\\wamp64\\www\\extend\\vendor\\symfony\\event-dispatcher-contracts";}s:14:"symfony/finder";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"symfony/finder";s:10:"\0*\0version";s:6:"7.4.20";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\symfony\\finder";}s:23:"symfony/http-foundation";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"symfony/http-foundation";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\symfony\\http-foundation";}s:19:"symfony/http-kernel";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"symfony/http-kernel";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\symfony\\http-kernel";}s:14:"symfony/mailer";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"symfony/mailer";s:10:"\0*\0version";s:6:"7.4.19";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\symfony\\mailer";}s:12:"symfony/mime";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"symfony/mime";s:10:"\0*\0version";s:6:"7.4.19";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:40:"C:\\wamp64\\www\\extend\\vendor\\symfony\\mime";}s:22:"symfony/polyfill-ctype";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-ctype";s:10:"\0*\0version";s:6:"1.37.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-ctype";}s:30:"symfony/polyfill-intl-grapheme";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:30:"symfony/polyfill-intl-grapheme";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:58:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-intl-grapheme";}s:25:"symfony/polyfill-intl-idn";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"symfony/polyfill-intl-idn";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-intl-idn";}s:32:"symfony/polyfill-intl-normalizer";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:32:"symfony/polyfill-intl-normalizer";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:60:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-intl-normalizer";}s:25:"symfony/polyfill-mbstring";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"symfony/polyfill-mbstring";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-mbstring";}s:22:"symfony/polyfill-php80";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php80";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php80";}s:22:"symfony/polyfill-php82";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php82";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php82";}s:22:"symfony/polyfill-php83";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php83";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php83";}s:22:"symfony/polyfill-php84";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php84";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php84";}s:22:"symfony/polyfill-php85";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php85";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php85";}s:22:"symfony/polyfill-php86";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"symfony/polyfill-php86";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-php86";}s:21:"symfony/polyfill-time";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"symfony/polyfill-time";s:10:"\0*\0version";s:6:"1.42.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-time";}s:21:"symfony/polyfill-uuid";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"symfony/polyfill-uuid";s:10:"\0*\0version";s:6:"1.43.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\symfony\\polyfill-uuid";}s:15:"symfony/process";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"symfony/process";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\symfony\\process";}s:15:"symfony/routing";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"symfony/routing";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\symfony\\routing";}s:25:"symfony/service-contracts";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"symfony/service-contracts";s:10:"\0*\0version";s:5:"3.7.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\symfony\\service-contracts";}s:14:"symfony/string";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"symfony/string";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\symfony\\string";}s:19:"symfony/translation";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"symfony/translation";s:10:"\0*\0version";s:6:"7.4.17";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\symfony\\translation";}s:29:"symfony/translation-contracts";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:29:"symfony/translation-contracts";s:10:"\0*\0version";s:5:"3.7.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:57:"C:\\wamp64\\www\\extend\\vendor\\symfony\\translation-contracts";}s:11:"symfony/uid";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"symfony/uid";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\symfony\\uid";}s:18:"symfony/var-dumper";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"symfony/var-dumper";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\vendor\\symfony\\var-dumper";}s:33:"tijsverkoyen/css-to-inline-styles";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:33:"tijsverkoyen/css-to-inline-styles";s:10:"\0*\0version";s:5:"2.4.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:61:"C:\\wamp64\\www\\extend\\vendor\\tijsverkoyen\\css-to-inline-styles";}s:16:"vlucas/phpdotenv";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"vlucas/phpdotenv";s:10:"\0*\0version";s:5:"5.7.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\vlucas\\phpdotenv";}s:19:"voku/portable-ascii";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"voku/portable-ascii";s:10:"\0*\0version";s:5:"2.1.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\voku\\portable-ascii";}s:31:"yajra/laravel-datatables-oracle";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:31:"yajra/laravel-datatables-oracle";s:10:"\0*\0version";s:6:"13.3.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^13.3";s:7:"\0*\0path";s:59:"C:\\wamp64\\www\\extend\\vendor\\yajra\\laravel-datatables-oracle";}s:15:"composer/semver";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"composer/semver";s:10:"\0*\0version";s:5:"3.5.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\composer\\semver";}s:14:"fakerphp/faker";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"fakerphp/faker";s:10:"\0*\0version";s:6:"1.24.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^1.23";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\fakerphp\\faker";}s:11:"filp/whoops";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"filp/whoops";s:10:"\0*\0version";s:6:"2.18.7";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\filp\\whoops";}s:21:"hamcrest/hamcrest-php";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"hamcrest/hamcrest-php";s:10:"\0*\0version";s:5:"3.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\hamcrest\\hamcrest-php";}s:22:"laravel/agent-detector";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"laravel/agent-detector";s:10:"\0*\0version";s:5:"2.0.5";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\laravel\\agent-detector";}s:13:"laravel/boost";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:13:"laravel/boost";s:10:"\0*\0version";s:6:"2.10.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^2.10";s:7:"\0*\0path";s:41:"C:\\wamp64\\www\\extend\\vendor\\laravel\\boost";}s:11:"laravel/mcp";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"laravel/mcp";s:10:"\0*\0version";s:5:"1.1.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\laravel\\mcp";}s:12:"laravel/pail";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"laravel/pail";s:10:"\0*\0version";s:5:"1.2.7";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^1.2.5";s:7:"\0*\0path";s:40:"C:\\wamp64\\www\\extend\\vendor\\laravel\\pail";}s:11:"laravel/pao";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"laravel/pao";s:10:"\0*\0version";s:5:"1.1.5";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^1.0.6";s:7:"\0*\0path";s:39:"C:\\wamp64\\www\\extend\\vendor\\laravel\\pao";}s:12:"laravel/pint";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"laravel/pint";s:10:"\0*\0version";s:6:"1.32.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:5:"^1.27";s:7:"\0*\0path";s:40:"C:\\wamp64\\www\\extend\\vendor\\laravel\\pint";}s:14:"laravel/roster";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"laravel/roster";s:10:"\0*\0version";s:5:"1.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\laravel\\roster";}s:15:"mockery/mockery";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"mockery/mockery";s:10:"\0*\0version";s:6:"1.6.15";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^1.6";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\mockery\\mockery";}s:17:"myclabs/deep-copy";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"myclabs/deep-copy";s:10:"\0*\0version";s:6:"1.14.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\myclabs\\deep-copy";}s:20:"nunomaduro/collision";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"nunomaduro/collision";s:10:"\0*\0version";s:5:"8.9.5";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^8.6";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\nunomaduro\\collision";}s:16:"phar-io/manifest";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:16:"phar-io/manifest";s:10:"\0*\0version";s:5:"2.0.4";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:44:"C:\\wamp64\\www\\extend\\vendor\\phar-io\\manifest";}s:15:"phar-io/version";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"phar-io/version";s:10:"\0*\0version";s:5:"3.2.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\phar-io\\version";}s:25:"phpunit/php-code-coverage";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"phpunit/php-code-coverage";s:10:"\0*\0version";s:6:"12.5.8";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\php-code-coverage";}s:25:"phpunit/php-file-iterator";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"phpunit/php-file-iterator";s:10:"\0*\0version";s:5:"6.0.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\php-file-iterator";}s:19:"phpunit/php-invoker";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"phpunit/php-invoker";s:10:"\0*\0version";s:5:"6.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:47:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\php-invoker";}s:25:"phpunit/php-text-template";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:25:"phpunit/php-text-template";s:10:"\0*\0version";s:5:"5.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\php-text-template";}s:17:"phpunit/php-timer";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"phpunit/php-timer";s:10:"\0*\0version";s:5:"8.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\php-timer";}s:15:"phpunit/phpunit";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:15:"phpunit/phpunit";s:10:"\0*\0version";s:7:"12.5.40";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:8:"^12.5.12";s:7:"\0*\0path";s:43:"C:\\wamp64\\www\\extend\\vendor\\phpunit\\phpunit";}s:20:"sebastian/cli-parser";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"sebastian/cli-parser";s:10:"\0*\0version";s:5:"4.2.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\cli-parser";}s:20:"sebastian/comparator";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"sebastian/comparator";s:10:"\0*\0version";s:5:"7.1.8";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\comparator";}s:20:"sebastian/complexity";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:20:"sebastian/complexity";s:10:"\0*\0version";s:5:"5.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:48:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\complexity";}s:14:"sebastian/diff";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"sebastian/diff";s:10:"\0*\0version";s:5:"7.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\diff";}s:21:"sebastian/environment";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:21:"sebastian/environment";s:10:"\0*\0version";s:5:"8.1.2";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:49:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\environment";}s:18:"sebastian/exporter";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"sebastian/exporter";s:10:"\0*\0version";s:5:"7.0.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\exporter";}s:22:"sebastian/global-state";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:22:"sebastian/global-state";s:10:"\0*\0version";s:5:"8.0.3";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:50:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\global-state";}s:23:"sebastian/lines-of-code";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:23:"sebastian/lines-of-code";s:10:"\0*\0version";s:5:"4.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\lines-of-code";}s:27:"sebastian/object-enumerator";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:27:"sebastian/object-enumerator";s:10:"\0*\0version";s:5:"7.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:55:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\object-enumerator";}s:26:"sebastian/object-reflector";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:26:"sebastian/object-reflector";s:10:"\0*\0version";s:5:"5.0.0";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:54:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\object-reflector";}s:27:"sebastian/recursion-context";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:27:"sebastian/recursion-context";s:10:"\0*\0version";s:5:"7.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:55:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\recursion-context";}s:14:"sebastian/type";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:14:"sebastian/type";s:10:"\0*\0version";s:5:"6.0.4";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:42:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\type";}s:17:"sebastian/version";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"sebastian/version";s:10:"\0*\0version";s:5:"6.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\sebastian\\version";}s:28:"staabm/side-effects-detector";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:28:"staabm/side-effects-detector";s:10:"\0*\0version";s:5:"1.0.5";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:56:"C:\\wamp64\\www\\extend\\vendor\\staabm\\side-effects-detector";}s:12:"symfony/yaml";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"symfony/yaml";s:10:"\0*\0version";s:6:"7.4.21";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:40:"C:\\wamp64\\www\\extend\\vendor\\symfony\\yaml";}s:17:"theseer/tokenizer";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"theseer/tokenizer";s:10:"\0*\0version";s:5:"2.0.1";s:9:"\0*\0source";r:8;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:0;s:13:"\0*\0constraint";s:0:"";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\vendor\\theseer\\tokenizer";}}s:11:"\0*\0packages";O:32:"Laravel\\Roster\\PackageCollection":2:{s:8:"\0*\0items";a:132:{i:0;r:5;i:1;r:13;i:2;r:21;i:3;r:29;i:4;r:37;i:5;r:45;i:6;r:53;i:7;r:61;i:8;r:69;i:9;r:77;i:10;r:85;i:11;r:93;i:12;r:101;i:13;r:109;i:14;r:117;i:15;r:125;i:16;r:133;i:17;r:141;i:18;r:149;i:19;r:157;i:20;r:165;i:21;r:173;i:22;r:181;i:23;r:189;i:24;r:197;i:25;r:205;i:26;r:213;i:27;r:221;i:28;r:229;i:29;r:237;i:30;r:245;i:31;r:253;i:32;r:261;i:33;r:269;i:34;r:277;i:35;r:285;i:36;r:293;i:37;r:301;i:38;r:309;i:39;r:317;i:40;r:325;i:41;r:333;i:42;r:341;i:43;r:349;i:44;r:357;i:45;r:365;i:46;r:373;i:47;r:381;i:48;r:389;i:49;r:397;i:50;r:405;i:51;r:413;i:52;r:421;i:53;r:429;i:54;r:437;i:55;r:445;i:56;r:453;i:57;r:461;i:58;r:469;i:59;r:477;i:60;r:485;i:61;r:493;i:62;r:501;i:63;r:509;i:64;r:517;i:65;r:525;i:66;r:533;i:67;r:541;i:68;r:549;i:69;r:557;i:70;r:565;i:71;r:573;i:72;r:581;i:73;r:589;i:74;r:597;i:75;r:605;i:76;r:613;i:77;r:621;i:78;r:629;i:79;r:637;i:80;r:645;i:81;r:653;i:82;r:661;i:83;r:669;i:84;r:677;i:85;r:685;i:86;r:693;i:87;r:701;i:88;r:709;i:89;r:717;i:90;r:725;i:91;r:733;i:92;r:741;i:93;r:749;i:94;r:757;i:95;r:765;i:96;r:773;i:97;r:781;i:98;r:789;i:99;r:797;i:100;r:805;i:101;r:813;i:102;r:821;i:103;r:829;i:104;r:837;i:105;r:845;i:106;r:853;i:107;r:861;i:108;r:869;i:109;r:877;i:110;r:885;i:111;r:893;i:112;r:901;i:113;r:909;i:114;r:917;i:115;r:925;i:116;r:933;i:117;r:941;i:118;r:949;i:119;r:957;i:120;r:965;i:121;r:973;i:122;r:981;i:123;r:989;i:124;r:997;i:125;r:1005;i:126;r:1013;i:127;r:1021;i:128;r:1029;i:129;r:1037;i:130;r:1045;i:131;r:1053;}s:28:"\0*\0escapeWhenCastingToString";b:0;}}s:2:"js";O:37:"Laravel\\Roster\\Ecosystems\\JsEcosystem":3:{s:9:"\0*\0byName";a:6:{s:17:"@tailwindcss/vite";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:17:"@tailwindcss/vite";s:10:"\0*\0version";s:5:"4.0.0";s:9:"\0*\0source";E:38:"Laravel\\Roster\\Enums\\PackageSource:Npm";s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^4.0.0";s:7:"\0*\0path";s:51:"C:\\wamp64\\www\\extend\\node_modules\\@tailwindcss\\vite";}s:12:"concurrently";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:12:"concurrently";s:10:"\0*\0version";s:6:"10.0.3";s:9:"\0*\0source";r:1201;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:7:"^10.0.3";s:7:"\0*\0path";s:46:"C:\\wamp64\\www\\extend\\node_modules\\concurrently";}s:19:"laravel-vite-plugin";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:19:"laravel-vite-plugin";s:10:"\0*\0version";s:3:"3.1";s:9:"\0*\0source";r:1201;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:4:"^3.1";s:7:"\0*\0path";s:53:"C:\\wamp64\\www\\extend\\node_modules\\laravel-vite-plugin";}s:11:"tailwindcss";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:11:"tailwindcss";s:10:"\0*\0version";s:5:"4.0.0";s:9:"\0*\0source";r:1201;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^4.0.0";s:7:"\0*\0path";s:45:"C:\\wamp64\\www\\extend\\node_modules\\tailwindcss";}s:4:"vite";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:4:"vite";s:10:"\0*\0version";s:5:"8.0.0";s:9:"\0*\0source";r:1201;s:6:"\0*\0dev";b:1;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^8.0.0";s:7:"\0*\0path";s:38:"C:\\wamp64\\www\\extend\\node_modules\\vite";}s:18:"@laravel/multiplex";O:22:"Laravel\\Roster\\Package":7:{s:7:"\0*\0name";s:18:"@laravel/multiplex";s:10:"\0*\0version";s:5:"0.4.1";s:9:"\0*\0source";r:1201;s:6:"\0*\0dev";b:0;s:9:"\0*\0direct";b:1;s:13:"\0*\0constraint";s:6:"^0.4.1";s:7:"\0*\0path";s:52:"C:\\wamp64\\www\\extend\\node_modules\\@laravel\\multiplex";}}s:11:"\0*\0packages";O:32:"Laravel\\Roster\\PackageCollection":2:{s:8:"\0*\0items";a:6:{i:0;r:1198;i:1;r:1206;i:2;r:1214;i:3;r:1222;i:4;r:1230;i:5;r:1238;}s:28:"\0*\0escapeWhenCastingToString";b:0;}s:17:"\0*\0packageManager";N;}s:6:"stacks";O:30:"Laravel\\Roster\\Support\\EnumSet":1:{s:8:"\0*\0cases";a:1:{i:0;E:32:"Laravel\\Roster\\Enums\\Stack:Blade";}}s:21:"browserTestFrameworks";O:30:"Laravel\\Roster\\Support\\EnumSet":1:{s:8:"\0*\0cases";a:0:{}}s:9:"frontends";O:30:"Laravel\\Roster\\Support\\EnumSet":1:{s:8:"\0*\0cases";a:0:{}}s:6:"agents";O:30:"Laravel\\Roster\\Support\\EnumSet":1:{s:8:"\0*\0cases";a:2:{i:0;E:37:"Laravel\\Roster\\Enums\\Agent:ClaudeCode";i:1;E:32:"Laravel\\Roster\\Enums\\Agent:Codex";}}s:7:"editors";O:30:"Laravel\\Roster\\Support\\EnumSet":1:{s:8:"\0*\0cases";a:0:{}}}', 1793375583),
	('extension-management-system-cache-boost:mcp:database-schema:mysql:extension_proposals:0:0:0:1', 'a:2:{s:6:"engine";s:5:"mysql";s:6:"tables";a:1:{s:19:"extension_proposals";a:5:{s:7:"columns";a:65:{s:2:"id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:1;}s:9:"tenant_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:9:"campus_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:7:"unit_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"academic_program_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:17:"extension_plan_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"dts_record_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"proposal_code";a:4:{s:4:"type";s:11:"varchar(50)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:15:"tracking_number";a:4:{s:4:"type";s:11:"varchar(50)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:5:"title";a:4:{s:4:"type";s:12:"varchar(255)";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:0;}s:14:"banner_program";a:4:{s:4:"type";s:12:"varchar(100)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"banner_program_other";a:4:{s:4:"type";s:12:"varchar(255)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:17:"agenda_priorities";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"beneficiaries";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:25:"target_beneficiaries_data";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"implementing_agency";a:4:{s:4:"type";s:12:"varchar(255)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:21:"implementing_colleges";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:11:"lead_agency";a:4:{s:4:"type";s:12:"varchar(255)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:22:"collaborating_agencies";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:27:"collaborating_agencies_data";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:14:"duration_start";a:4:{s:4:"type";s:4:"date";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:12:"duration_end";a:4:{s:4:"type";s:4:"date";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"duration_text";a:4:{s:4:"type";s:12:"varchar(150)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:15:"duration_months";a:4:{s:4:"type";s:3:"int";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:16:"project_location";a:4:{s:4:"type";s:12:"varchar(255)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:24:"budget_institution_share";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"fund_source_internal";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"budget_partner_share";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"fund_source_external";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"budget_total_display";a:4:{s:4:"type";s:12:"varchar(100)";s:8:"nullable";b:0;s:7:"default";s:32:"Grand Total: No Cash Requirement";s:14:"auto_increment";b:0;}s:18:"budget_cash_amount";a:4:{s:4:"type";s:13:"decimal(14,2)";s:8:"nullable";b:0;s:7:"default";s:4:"0.00";s:14:"auto_increment";b:0;}s:22:"budgetary_requirements";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:22:"project_leader_user_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:26:"project_leader_employee_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"project_leader_name";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:0;s:7:"default";N;s:14:"auto_increment";b:0;}s:20:"project_leader_title";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:0;s:7:"default";s:14:"Project Leader";s:14:"auto_increment";b:0;}s:21:"project_leader_gender";a:4:{s:4:"type";s:11:"varchar(20)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:24:"asst_project_leader_name";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:25:"asst_project_leader_title";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:0;s:7:"default";s:24:"Assistant Project Leader";s:14:"auto_increment";b:0;}s:10:"proponents";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:12:"introduction";a:4:{s:4:"type";s:8:"longtext";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"objectives";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:17:"general_objective";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"specific_objectives";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:16:"expected_outputs";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"relevance_narrative";a:4:{s:4:"type";s:8:"longtext";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:14:"sdg_alignments";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:15:"development_gap";a:4:{s:4:"type";s:8:"longtext";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:18:"pdp_responsiveness";a:4:{s:4:"type";s:8:"longtext";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:22:"schedule_of_activities";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:15:"risk_management";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:15:"people_involved";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:13:"org_structure";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:23:"sustainability_measures";a:4:{s:4:"type";s:8:"longtext";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:19:"prepared_by_user_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:23:"prepared_by_employee_id";a:4:{s:4:"type";s:15:"bigint unsigned";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:16:"prepared_by_name";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:23:"prepared_by_designation";a:4:{s:4:"type";s:12:"varchar(191)";s:8:"nullable";b:0;s:7:"default";s:14:"Project Leader";s:14:"auto_increment";b:0;}s:11:"prepared_at";a:4:{s:4:"type";s:8:"datetime";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:6:"status";a:4:{s:4:"type";s:11:"varchar(50)";s:8:"nullable";b:0;s:7:"default";s:5:"draft";s:14:"auto_increment";b:0;}s:7:"remarks";a:4:{s:4:"type";s:4:"text";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:8:"metadata";a:4:{s:4:"type";s:4:"json";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"created_at";a:4:{s:4:"type";s:9:"timestamp";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"updated_at";a:4:{s:4:"type";s:9:"timestamp";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}s:10:"deleted_at";a:4:{s:4:"type";s:9:"timestamp";s:8:"nullable";b:1;s:7:"default";N;s:14:"auto_increment";b:0;}}s:7:"indexes";a:13:{s:47:"extension_proposals_academic_program_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:19:"academic_program_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:37:"extension_proposals_campus_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:9:"campus_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:41:"extension_proposals_dts_record_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:13:"dts_record_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:43:"extension_proposals_extension_plan_id_index";a:4:{s:7:"columns";a:1:{i:0;s:17:"extension_plan_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:51:"extension_proposals_prepared_by_employee_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:23:"prepared_by_employee_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:47:"extension_proposals_prepared_by_user_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:19:"prepared_by_user_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:54:"extension_proposals_project_leader_employee_id_foreign";a:4:{s:7:"columns";a:1:{i:0;s:26:"project_leader_employee_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:48:"extension_proposals_project_leader_user_id_index";a:4:{s:7:"columns";a:1:{i:0;s:22:"project_leader_user_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:40:"extension_proposals_proposal_code_unique";a:4:{s:7:"columns";a:1:{i:0;s:13:"proposal_code";}s:4:"type";s:5:"btree";s:9:"is_unique";b:1;s:10:"is_primary";b:0;}s:42:"extension_proposals_tenant_id_status_index";a:4:{s:7:"columns";a:2:{i:0;s:9:"tenant_id";i:1;s:6:"status";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:42:"extension_proposals_tracking_number_unique";a:4:{s:7:"columns";a:1:{i:0;s:15:"tracking_number";}s:4:"type";s:5:"btree";s:9:"is_unique";b:1;s:10:"is_primary";b:0;}s:33:"extension_proposals_unit_id_index";a:4:{s:7:"columns";a:1:{i:0;s:7:"unit_id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:0;s:10:"is_primary";b:0;}s:7:"primary";a:4:{s:7:"columns";a:1:{i:0;s:2:"id";}s:4:"type";s:5:"btree";s:9:"is_unique";b:1;s:10:"is_primary";b:1;}}s:12:"foreign_keys";a:0:{}s:8:"triggers";a:0:{}s:17:"check_constraints";a:0:{}}}}', 1793372292);
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.designations: 22 rows
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
	(21, 9, 'PROG', 'Programmer', 'Technical', 'Programmer', 'active', '2026-09-16 13:05:05', '2026-09-16 13:05:05', NULL),
	(22, 1, NULL, 'Institutional Officer', 'Administrative', NULL, 'active', '2026-10-29 04:57:27', '2026-10-29 04:57:27', NULL);
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

-- Dumping data for table extend.dss_evaluations: 3 rows
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

-- Dumping data for table extend.employees: 22 rows
DELETE FROM `employees`;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` (`id`, `tenant_id`, `employee_no`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `phone_number`, `gender`, `campus_id`, `department_id`, `designation_id`, `designation`, `employment_type`, `employment_status`, `date_hired`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'EMP-2026-0001', 'ADSSU System', '', 'Administrator', '', 'admin@adssu.edu.ph', '09170000001', 'Other', 1, 1, 5, 'Management Information Systems Director', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(2, 1, 'EMP-2026-0002', 'Dr. Joy C.', '', 'Capistrano', '', 'president@adssu.edu.ph', '09170000002', 'Other', 1, 1, 6, 'SUC President III', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(3, 1, 'EMP-2026-0003', 'Engr. Nelson V.', '', 'Robles', '', 'bac@adssu.edu.ph', '09170000003', 'Other', 1, 2, 8, 'Bids & Awards Committee Chairperson', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(4, 1, 'EMP-2026-0004', 'Dr. Alan P.', '', 'Martinez', '', 'director@adssu.edu.ph', '09170000004', 'Other', 2, 1, 16, 'Campus Director, Trento Campus', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(5, 1, 'EMP-2026-0005', 'Dr. QA Admin', '', 'Lead', '', 'admin_test_qa@iipm.edu.ph', '09170000005', 'Other', 1, 1, 17, 'Institutional Officer', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-09-16 13:02:24', NULL),
	(6, 1, 'EMP-2026-0006', 'Dr. Diane B. Remot', '', '(TL)', '', 'auditor_faculty_test@iipm.edu.ph', '09170000006', 'Other', 1, 1, 22, 'Institutional Officer', 'Permanent', 'Active', '2023-09-16', '2026-09-16 08:09:00', '2026-10-29 04:57:27', NULL),
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.employee_designations: 12 rows
DELETE FROM `employee_designations`;
/*!40000 ALTER TABLE `employee_designations` DISABLE KEYS */;
INSERT INTO `employee_designations` (`id`, `tenant_id`, `employee_id`, `user_id`, `campus_id`, `department_id`, `designation_id`, `designation`, `is_primary`, `status`, `effective_date`, `end_date`, `remarks`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 1, 5, 'Management Information Systems Director', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(2, 1, 2, 2, 1, 1, 6, 'SUC President III', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(3, 1, 3, 3, 1, 2, 8, 'Bids & Awards Committee Chairperson', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(4, 1, 4, 4, 2, 1, 16, 'Campus Director, Trento Campus', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-09-16 13:02:24'),
	(5, 1, 5, 5, 1, 1, 17, 'Institutional Officer', 1, 'active', NULL, NULL, NULL, '2026-09-16 10:01:40', '2026-10-26 04:27:08'),
	(18, 1, 6, 6, 1, 1, 22, 'Institutional Officer', 1, 'active', NULL, NULL, NULL, '2026-10-29 04:57:27', '2026-10-29 04:57:27'),
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_budgets: 2 rows
DELETE FROM `extension_budgets`;
/*!40000 ALTER TABLE `extension_budgets` DISABLE KEYS */;
INSERT INTO `extension_budgets` (`id`, `tenant_id`, `campus_id`, `fiscal_year`, `budget_code`, `title`, `fund_cluster`, `total_allocation`, `allocated_gaa`, `allocated_stf`, `allocated_external`, `realigned_amount`, `status`, `board_resolution_no`, `approved_by`, `approved_at`, `notes`, `created_by_user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, NULL, 2026, 'EXT-BUD-2026-MAIN', 'FY 2026 SNSU Institutional Extension & Community Development Operating Fund', '01 - Regular Agency Fund (GAA)', 5200000.00, 3500000.00, 1200000.00, 500000.00, 1000.00, 'active', 'BOR Resolution No. 12-2026', 'SNSU Board of Regents / Dr. Gregorio Z. Gamboa Jr.', '2026-01-15', 'Official annual appropriation for community extension, technology transfer, and livelihood projects across all SNSU campuses.', 1, '2026-10-23 20:11:58', '2026-10-29 05:26:11', NULL),
	(2, 1, NULL, 2026, 'EXT-BUD-2026-9D02', 'FY 2026 Annual Extension Operational & Community Program Budget', '05 - Internally Generated Income (STF / Fiduciary)', 4000000.00, 2500000.00, 1000000.00, 500000.00, 0.00, 'active', NULL, 'University President / Board of Regents', '2026-10-30', NULL, 7, '2026-10-30 05:13:02', '2026-10-30 05:13:02', NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_budget_transactions: 3 rows
DELETE FROM `extension_budget_transactions`;
/*!40000 ALTER TABLE `extension_budget_transactions` DISABLE KEYS */;
INSERT INTO `extension_budget_transactions` (`id`, `extension_budget_id`, `transaction_type`, `amount`, `reference_no`, `reason`, `authorized_by`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 'initial', 5200000.00, 'BOR Res. No. 12-2026', 'Approved Initial Annual Extension Appropriation for FY 2026', 'SNSU Board of Regents', 1, '2026-10-23 20:11:58', '2026-10-23 20:11:58'),
	(2, 1, 'augmentation', 1000.00, NULL, 'hgfhfh', 'Engr. Antonio Piloton Jr.', 7, '2026-10-29 05:26:11', '2026-10-29 05:26:11'),
	(3, 2, 'initial', 4000000.00, 'EXT-BUD-2026-9D02', 'Approved Initial Annual Extension Appropriation for FY 2026', 'University President / Board of Regents', 7, '2026-10-30 05:13:02', '2026-10-30 05:13:02');
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
  `tracking_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  KEY `ext_materials_idx` (`materials_arrival_start`,`materials_arrival_end`),
  KEY `extension_plans_tracking_number_index` (`tracking_number`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_plans: 7 rows
DELETE FROM `extension_plans`;
/*!40000 ALTER TABLE `extension_plans` DISABLE KEYS */;
INSERT INTO `extension_plans` (`id`, `tenant_id`, `campus_id`, `academic_program_id`, `dts_record_id`, `plan_code`, `tracking_number`, `program_name`, `extension_activity`, `objectives`, `sub_extension_proposal_date`, `sub_activity_design_date`, `sub_purchase_request_date`, `moa_signing_date`, `materials_arrival_mode`, `materials_arrival_start`, `materials_arrival_end`, `materials_arrival_dates`, `materials_arrival_date`, `conduct_activity_mode`, `conduct_activity_start`, `conduct_activity_end`, `conduct_activity_dates`, `conduct_activity_date`, `sub_narrative_report_date`, `monitoring_feedback_date`, `monitoring_feedback_end_date`, `beneficiaries`, `target_beneficiaries_count`, `proposed_budget`, `extensionists_count`, `prepared_by_user_id`, `prepared_by_employee_id`, `prepared_by_name`, `prepared_by_designation`, `prepared_at`, `reviewed_by_user_id`, `reviewed_by_employee_id`, `reviewed_by_name`, `reviewed_by_designation`, `reviewed_at`, `noted_by_employee_id`, `noted_by_name`, `noted_by_designation`, `noted_at`, `approved_by_employee_id`, `approved_by_name`, `approved_by_designation`, `approved_at`, `concurred_by_employee_id`, `concurred_by_name`, `concurred_by_designation`, `concurred_at`, `concurred_by_2_employee_id`, `concurred_by_2_name`, `concurred_by_2_designation`, `concurred_by_2_at`, `status`, `remarks`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 1, NULL, 'SNSU-EXT-2026-0001', NULL, 'Bachelor of Science in Information Technology', 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays', '1. Train 100 barangay functionaries on basic computer operations and secure data entry.\n2. Deploy a lightweight, web-based barangay recordkeeping and certification system.\n3. Establish ongoing technical support and community feedback mechanisms.', '2026-08-25', '2026-09-04', '2026-09-14', '2026-09-24', 'range', '2026-09-29', '2026-10-04', NULL, NULL, 'range', '2026-10-09', '2026-10-14', NULL, NULL, '2026-10-19', '2026-11-03', NULL, 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City', 100, 95000.00, 6, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-08-20 13:20:33', NULL, 12, 'JESSA G. HAMBRE', 'College of Computer and Information Sciences Head for Extension', NULL, 11, 'VIRNILLE C. FRANCISCO, PhEdD', 'College of Computer and Information Sciences Dean', '2026-08-22 13:20:33', 4, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', '2026-08-25 13:20:33', 21, 'EVER JOY G. RABADAN, MAEM, MSc.', 'University Extension Specialist V', NULL, 22, 'ALVIE ELMA C. PLAZA, MA', 'University Extension Director', NULL, 'approved', 'Approved and included in the Official Annual RIE Extension Calendar.', NULL, '2026-10-24 05:20:33', '2026-10-26 06:46:22', NULL),
	(2, 1, 1, 7, NULL, 'SNSU-EXT-2026-0002', NULL, 'Bachelor of Science in Civil Engineering', 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning', '1. Inspect structural integrity of 5 coastal public elementary schools.\n2. Design cost-effective retrofitting solutions for storm surge vulnerability.\n3. Train barangay DRRM volunteers on structural hazard identification.', '2026-09-09', '2026-09-19', '2026-09-29', '2026-10-04', 'range', '2026-10-09', '2026-10-14', NULL, NULL, 'range', '2026-10-19', '2026-10-29', NULL, NULL, '2026-11-08', '2026-11-23', NULL, 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua', 60, 115000.00, 5, 12, 8, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', '2026-09-06 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-09-08 13:20:33', 4, 'Dr. Alan P. Martinez', 'College Dean, College of Engineering and Technology', '2026-09-09 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 'Endorsed for high-priority funding due to climate disaster vulnerability.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(3, 1, 2, 10, NULL, 'SNSU-EXT-2026-0003', NULL, 'Bachelor of Science in Agricultural and Biosystems Engineering', 'Buhay Mangrove: Community-Based Mangrove Reforestation & Coastal Bio-Shield Protocol', '1. Propagate 10,000 Rhizophora mangrove propagules in Del Carmen, Siargao.\n2. Organize and train 40 fisherfolk families as environmental stewards.\n3. Establish permanent ecological nursery sites.', '2026-10-04', '2026-10-09', '2026-10-14', '2026-10-19', 'range', '2026-10-26', '2026-10-31', NULL, NULL, 'range', '2026-11-05', '2026-11-11', NULL, NULL, '2026-11-23', '2026-12-23', NULL, 'Del Carmen Small Fisherfolk Federation and Women Seaweed Growers Guild', 85, 140000.00, 7, 14, 9, 'Rodrigo T. Dela Cruz Jr.', 'Faculty Researcher / Extensionist', '2026-09-29 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-10-02 13:20:33', 4, 'Dr. Alan P. Martinez', 'Campus Director, Del Carmen Campus', '2026-10-04 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 'Approved Plan: Ready for Extension Proposal creation by proponent.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(4, 1, 3, 8, NULL, 'SNSU-EXT-2026-0004', NULL, 'Bachelor of Science in Electrical Engineering', 'Solar-Kuryente: Off-Grid Solar PV Installation & Battery Maintenance for Island Communities', '1. Train 50 youth and island residents on solar panel wiring and DC inverter maintenance.\n2. Install pilot solar charging stations in off-grid fishing sitios.\n3. Distribute safety and troubleshooting quick-reference kits.', '2026-10-29', '2026-11-08', '2026-11-18', '2026-11-28', 'range', '2026-12-03', '2026-12-08', NULL, NULL, 'range', '2026-12-13', '2026-12-18', NULL, NULL, '2026-12-28', '2027-01-22', NULL, 'Off-grid Fisherfolk Families and Youth Electricians of Tagana-an', 75, 88000.00, 4, 13, 10, 'Jocelyn M. Fernandez', 'Associate Professor / Project Leader', '2026-10-19 13:20:33', NULL, NULL, NULL, NULL, NULL, 6, 'Dr. Diane B. Remot (TL)', 'Extension Coordinator', '2026-10-22 13:20:33', NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'noted', 'Noted by Extension Coordinator. Currently pending Dean review and approval before proposals can be initiated.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(5, 1, 4, 2, NULL, 'SNSU-EXT-2026-0005', NULL, 'Bachelor of Science in Computer Science', 'CodeKids: Scratch Programming & STEM Discovery Camp for Remote Elementary Schools', '1. Introduce 150 public school children to logic, computational thinking, and block-based coding.\n2. Equip teachers with modular coding curricula and offline learning kits.', '2026-11-13', '2026-11-23', '2026-12-03', '2026-12-13', 'range', '2026-12-18', '2026-12-23', NULL, NULL, 'range', '2026-12-28', '2027-01-02', NULL, NULL, '2027-01-12', '2027-02-11', NULL, 'Grade 5 & 6 Pupils and Science Teachers of Malimono Central Elementary School', 150, 65000.00, 5, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-10-23 13:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Extension Coordinator', NULL, NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'submitted', 'Submitted by faculty end-user. In-transit to Extension Coordinator.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(6, 1, 1, 1, NULL, 'SNSU-EXT-2026-0006', NULL, 'Bachelor of Science in Information Technology', 'CyberLigtas: Cyber Hygiene & Anti-Scam Orientation for Public Market Merchants and Senior Citizens', '1. Protect 120 senior citizens and market vendors against phishing, SMS fraud, and illegal mobile lending schemes.', '2026-11-28', '2026-12-08', '2026-12-18', '2026-12-28', 'range', '2027-01-02', '2027-01-07', NULL, NULL, 'range', '2027-01-12', '2027-01-17', NULL, NULL, '2027-01-27', '2027-02-26', NULL, 'Surigao City Public Market Vendors Association and Senior Citizens Federation', 120, 50000.00, 4, 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Extension Coordinator', NULL, NULL, NULL, 'Dean', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(7, 1, 11, 32, NULL, 'SNSU-EXT-2026-0007', '12345', 'Main Campus — BA-Comm (Bachelor of Arts in Communication)', 'yuuytutyu', 'yurtyu', NULL, NULL, NULL, NULL, 'range', NULL, NULL, NULL, NULL, 'range', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tyutryu', 50, 80000.00, 15, 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', '2026-10-29 13:51:16', 24, 20, 'College Head for Extension', 'College Head for Extension', '2026-10-30 14:03:09', 19, 'College Dean', 'College Dean', NULL, 19, 'College Dean', 'College Dean', NULL, 21, 'Ever Joy Rabadan', 'University Extension Specialist V', NULL, 22, 'Alvie Elma Plaza', 'University Extension Director', NULL, 'submitted', NULL, '{"signatures": {"prepared": {"signed": true, "remarks": "Verified and electronically endorsed.", "signed_at": "2026-10-29T13:51:16+00:00", "ip_address": "::1", "role_label": "Prepared by:", "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0", "designation": "Project Leader", "signer_name": "Antonio Jr. L. Piloton", "signature_img": "data:image/png;base64,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", "signature_hash": "SIG-SNSU-EXT-2026-0007-93578CDC7E99B243", "signature_type": "draw", "signed_by_user_id": 7, "signed_by_employee_id": 7}, "reviewed": {"signed": true, "remarks": "Verified and electronically endorsed.", "signed_at": "2026-10-30T14:03:09+00:00", "ip_address": "::1", "role_label": "Reviewed by:", "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0", "designation": "College Head for Extension", "signer_name": "Antonio Jr. L. Piloton", "signature_img": "data:image/png;base64,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", "signature_hash": "SIG-SNSU-EXT-2026-0007-9D47415C8A367232", "signature_type": "draw", "signed_by_user_id": 7, "signed_by_employee_id": 7}}}', '2026-10-26 07:10:38', '2026-10-30 06:03:09', NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_proposals: 3 rows
DELETE FROM `extension_proposals`;
/*!40000 ALTER TABLE `extension_proposals` DISABLE KEYS */;
INSERT INTO `extension_proposals` (`id`, `tenant_id`, `campus_id`, `unit_id`, `academic_program_id`, `extension_plan_id`, `dts_record_id`, `proposal_code`, `tracking_number`, `title`, `banner_program`, `banner_program_other`, `agenda_priorities`, `beneficiaries`, `target_beneficiaries_data`, `implementing_agency`, `implementing_colleges`, `lead_agency`, `collaborating_agencies`, `collaborating_agencies_data`, `duration_start`, `duration_end`, `duration_text`, `duration_months`, `project_location`, `budget_institution_share`, `fund_source_internal`, `budget_partner_share`, `fund_source_external`, `budget_total_display`, `budget_cash_amount`, `budgetary_requirements`, `project_leader_user_id`, `project_leader_employee_id`, `project_leader_name`, `project_leader_title`, `project_leader_gender`, `asst_project_leader_name`, `asst_project_leader_title`, `proponents`, `introduction`, `objectives`, `general_objective`, `specific_objectives`, `expected_outputs`, `relevance_narrative`, `sdg_alignments`, `development_gap`, `pdp_responsiveness`, `schedule_of_activities`, `risk_management`, `people_involved`, `org_structure`, `sustainability_measures`, `prepared_by_user_id`, `prepared_by_employee_id`, `prepared_by_name`, `prepared_by_designation`, `prepared_at`, `status`, `remarks`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 15, 1, 1, NULL, 'SNSU-PROP-2026-0001', '202610240001', 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays', 'Digital Literacy & Information Technology Transfer', NULL, '["Smart Technologies & Digital Transformation", "Community Empowerment & Governance"]', 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City', '{"direct": 100, "sectors": ["Barangay Officials", "Barangay Health Workers", "Youth & SK"], "indirect": 3500}', 'Surigao del Norte State University (SNSU)', '["College of Engineering and Technology", "College of Information & Communications Technology"]', 'Surigao del Norte State University', 'City Government of Surigao, Department of Information and Communications Technology (DICT - Caraga)', '[{"name": "City Government of Surigao", "role": "Logistical support and community venue coordination"}, {"name": "DICT Caraga", "role": "Free Wi-Fi access points and technical assistance"}]', '2026-10-09', '2026-12-08', '2 Months (October 2026 – December 2026)', 2, 'Barangay Lipata Gymnasium, Surigao City', '75,000.00', '75,000.00', '20,000.00', '20,000.00', '95,000.00', 95000.00, '{"sections": {"A": {"code": "A", "items": [{"item": "Expert Resource Speaker Honoraria", "basis": "40 hrs @ 500/hr", "amount": 20000}, {"item": "Technical Facilitator Assistance", "basis": "40 hrs @ 250/hr", "amount": 10000}], "title": "Personal Services / Honoraria"}, "D": {"code": "D", "items": [{"item": "Documentation, Certificates & Transportation", "basis": "Lump sum", "amount": 10000}], "title": "Contingency & Miscellaneous"}, "B_TRAIN": {"code": "B_TRAIN", "items": [{"item": "Participant Training Kits & Workbooks", "basis": "100 sets @ 250/set", "amount": 25000}, {"item": "Training Venue Meals & Refreshments", "basis": "100 pax @ 300/pax", "amount": 30000}], "title": "Training Supplies & Materials"}}}', 7, 7, 'Antonio Jr. L. Piloton', 'Associate Professor / Project Leader', 'Male', 'Maria Elena S. Villanueva', 'Assistant Project Leader', '[{"name": "Antonio Jr. L. Piloton", "role": "Project Leader", "type": "faculty", "employee_id": 7}, {"name": "Maria Elena S. Villanueva", "role": "Assistant Project Leader", "type": "faculty", "employee_id": 8}, {"name": "Rodrigo T. Dela Cruz Jr.", "role": "Technical Facilitator", "type": "faculty", "employee_id": 9}]', 'Information and Communications Technology (ICT) plays an indispensable role in community-level governance. In coastal barangays of Surigao City, manual recordkeeping continues to present bottlenecks in resident profiling, health records management, and disaster aid allocation. This project equips local officials with modern digital tools to enhance administrative transparency and service delivery.', '[{"title": "Capacity Development", "description": "Conduct 40 hours of hands-on training on digital literacy and office automation."}, {"title": "System Deployment", "description": "Install and configure a localized electronic barangay record system."}, {"title": "Impact Assessment", "description": "Evaluate citizen satisfaction and operational turnaround time before and after implementation."}]', 'To modernize frontline public service delivery and administrative recordkeeping across coastal barangays in Surigao City through digital literacy training and automated local governance systems.', '["Train 100 barangay staff and SK leaders on digital document creation and spreadsheet management.", "Establish a secure offline-capable database of residents, health indices, and clearance issuances.", "Formulate a community-led maintenance protocol to ensure continuous operation."]', '["100 fully certified barangay personnel in digital public administration.", "1 fully operational e-Barangay Records Information System.", "1 signed Memorandum of Agreement with LGU Surigao City for sustained tech support."]', 'Directly aligns with the Philippine Development Plan (PDP) 2023-2028 Chapter on Expanding Digital Infrastructure and Enhancing Social Inclusion, alongside the Regional Development Plan of Caraga.', '{"selected": [4, 9, 11], "narrative": "Addresses SDG 4 (Quality Education) through practical digital skills training, SDG 9 (Industry, Innovation & Infrastructure) by providing IT infrastructure, and SDG 11 (Sustainable Communities) through resilient municipal e-governance."}', 'Disparity in digital literacy between urban centers and coastal frontline communities, resulting in backlogs, inaccurate resident profiles, and delays during emergency disaster response.', 'Supports Chapter 14: Promoting Inclusive and Sustainable Digital Infrastructure and Transforming Governance.', '[{"phase": "Phase I: Needs Assessment & LGU Consultation", "outputs": "Hardware readiness audit, signed MOA", "target_month": "Week 1-2"}, {"phase": "Phase II: Hands-on ICT Workshop", "outputs": "Completed modular training, skill assessment", "target_month": "Week 3-5"}, {"phase": "Phase III: System Rollout & Data Migration", "outputs": "Encoded resident database, pilot operations", "target_month": "Week 6-7"}, {"phase": "Phase IV: Monitoring & Closeout", "outputs": "Final evaluation report, user turnover", "target_month": "Week 8"}]', NULL, '{"rows": [{"id": "r1", "name": "Antonio Jr. L. Piloton", "role": "Project Leader", "responsibilities": "Overall project oversight, curriculum formulation, and institutional coordination."}, {"id": "r2", "name": "Maria Elena S. Villanueva", "role": "Assistant Project Leader", "responsibilities": "Field logistics, training materials preparation, and participant assessment."}, {"id": "r3", "name": "Rodrigo T. Dela Cruz Jr.", "role": "Technical Facilitator", "responsibilities": "Network setup, software installation, and lab support."}], "columns": [{"id": "name", "label": "Extensionist / Collaborator", "width": 35}, {"id": "role", "label": "Project Role / Title", "width": 25}, {"id": "responsibilities", "label": "Specific Responsibilities & Tasks", "width": 40}]}', '{"mode": "dynamic", "nodes": [{"id": "lead", "name": "Antonio Jr. L. Piloton", "title": "Project Leader", "parent": null}, {"id": "asst", "name": "Maria Elena S. Villanueva", "title": "Assistant Project Leader", "parent": "lead"}, {"id": "tech", "name": "Rodrigo T. Dela Cruz Jr.", "title": "Technical Lead", "parent": "asst"}]}', 'The project turnover includes a complete administrator manual, video tutorials, and a dedicated student volunteer helpdesk hosted at the SNSU College of Engineering and Technology.', 7, 7, 'Antonio Jr. L. Piloton', 'Faculty Proponent / Project Leader', '2026-10-09 13:20:33', 'approved', 'Fully approved by the University President with allocation from GAA Extension Allotment.', '{"accomplishment_report": {"updated_at": "2026-10-26T13:39:06+00:00", "date_completed": "2026-10-26", "impact_feedback": "Community Impact & Beneficiary TestimonialsCommunity Impact & Beneficiary TestimonialsCommunity Impact & Beneficiary Testimonials", "updated_by_name": "Engr. Antonio Piloton Jr.", "solutions_adopted": "Problems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted\\r\\nProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted\\r\\nProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered\\r\\nSolutions & Remedial Measures Adopted", "updated_by_user_id": 7, "actual_budget_spent": "95000", "problems_encountered": "Problems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints EncounteredProblems / Constraints Encountered", "implementation_status": "Completed", "summary_accomplishments": "Summary of Key Accomplishments & MilestonesSummary of Key Accomplishments & MilestonesSummary of Key Accomplishments & MilestonesSummary of Key Accomplishments & Milestones", "actual_beneficiaries_male": "45", "actual_beneficiaries_total": "100", "actual_beneficiaries_female": "55", "actual_beneficiaries_sectors": "Specific Community Sectors / Demographics"}}', '2026-10-24 05:20:33', '2026-10-30 16:41:07', NULL),
	(2, 1, 1, 8, 7, 2, NULL, 'SNSU-PROP-2026-0002', '202610240002', 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning', 'Disaster Risk Reduction & Climate Change Adaptation', NULL, '["Climate Resilience & Environmental Protection", "Community Safety & Infrastructure"]', 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua', '{"direct": 60, "indirect": 2000}', 'Surigao del Norte State University (SNSU)', NULL, 'Surigao del Norte State University', 'MDRRMO Surigao City, Department of Public Works and Highways (DPWH)', NULL, '2026-10-19', '2026-12-18', '2 Months', 2, 'Barangay Mabua, Surigao City', '90,000.00', '90,000.00', '25,000.00', '25,000.00', '115,000.00', 115000.00, NULL, 12, 8, 'Maria Elena S. Villanueva', 'Assistant Professor / Project Leader', 'Female', NULL, 'Assistant Project Leader', '[{"name": "Maria Elena S. Villanueva", "role": "Project Leader", "type": "faculty", "employee_id": 8}, {"name": "Antonio Jr. L. Piloton", "role": "Co-Proponent", "type": "faculty", "employee_id": 7}]', 'Coastal public infrastructure in Northern Mindanao faces recurrent degradation from seismic activity, tropical cyclones, and storm surges. Through comprehensive non-destructive structural diagnostics and community evacuation routing, this program safeguards lives and critical educational assets.', '[{"title": "Structural Assessment", "description": "Conduct rebound hammer and ultrasonic pulse velocity tests on 5 evacuation facilities."}, {"title": "Retrofitting Blueprints", "description": "Deliver schematic structural improvement recommendations."}]', 'To enhance structural resilience and community disaster evacuation preparedness of public educational buildings in Barangay Mabua, Surigao City.', '["Evaluate masonry, reinforcement, and roofing vulnerabilities of 5 public structures.", "Establish localized evacuation route mappings with community DRRM personnel."]', '["5 complete structural engineering vulnerability assessments.", "1 evacuation roadmap handbook distributed to the barangay council."]', 'Addresses SDG 11 (Resilient Infrastructure) and SDG 13 (Climate Action).', '{"selected": [11, 13], "narrative": "Targets building safety and disaster risk mitigation."}', NULL, NULL, '[{"phase": "Field Testing & Structural Diagnostics", "outputs": "Testing data sheets", "target_month": "Week 1-3"}, {"phase": "Engineering Computation & Report Writing", "outputs": "Draft structural reports", "target_month": "Week 4-6"}, {"phase": "Stakeholders Turnover Workshop", "outputs": "Disaster blueprint handover", "target_month": "Week 7-8"}]', NULL, NULL, NULL, NULL, 12, 8, 'Maria Elena S. Villanueva', 'Faculty Proponent / Lead Engineer', '2026-10-19 13:20:33', 'concurred', 'Signed by Project Leader, Dean, and Extension Director. Currently on VP RIE desk for administrative endorsement.', NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(3, 1, 2, 19, 10, 3, NULL, 'PROP-PROP-2026-0003', NULL, 'Buhay Mangrove: Community-Based Mangrove Reforestation & Coastal Bio-Shield Protocol', 'STEAMUP', NULL, '["Transformative Learning"]', 'Del Carmen Small Fisherfolk Federation and Women Seaweed Growers Guild', '[{"male": "30", "name": "Del Carmen Small Fisherfolk Federation and Women Seaweed Growers Guild", "total": "50", "female": "20"}]', 'Del Carmen Campus', '["College of Business and Technology"]', 'Del Carmen Campus', NULL, '[{"role": null, "agency": null}]', '2026-11-05', '2026-11-11', NULL, 1, NULL, 'In-Kind (University Service Vehicle & Faculty Expertise)', 'In-Kind (University Service Vehicle & Faculty Expertise)', NULL, 'In-Kind (Venue & Community Accommodations)', 'Grand Total: No Cash Requirement', 140000.00, '{"A": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "Salary"}, {"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "Honoraria"}]}, "C": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "D": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "B_COMM": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "B_SUPP": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "B_OTHER": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "B_TRAIN": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}, "B_TRANS": {"items": [{"qty": "N/A", "cost": "N/A", "unit": "N/A", "amount": "N/A", "particulars": "N/A"}]}}', 7, 9, 'Rodrigo T. Dela Cruz Jr.', 'Project Leader', 'Male', NULL, 'Assistant Project Leader', '[{"name": "Rodrigo T. Dela Cruz Jr.", "role": "Project Leader", "type": "faculty", "employee_id": null}]', '', '["1. Propagate 10,000 Rhizophora mangrove propagules in Del Carmen, Siargao.", "2. Organize and train 40 fisherfolk families as environmental stewards.", "3. Establish permanent ecological nursery sites."]', '<ol><li><br></li></ol>', '[]', '[{"target": "0", "category": "Product", "description": ""}, {"target": "0", "category": "People", "description": ""}, {"target": "0", "category": "Service", "description": ""}, {"target": "0", "category": "Partnership", "description": ""}, {"target": "0", "category": "Patent/IP", "description": ""}, {"target": "1", "category": "Publication", "description": ""}, {"target": "0", "category": "Policy", "description": ""}]', '', '{"indirect": [], "selected": [1, 2, 3, 4, 9, 16, 17], "narrative": ""}', NULL, NULL, '{"rows": [{"no": "1", "agency": "Project Leader / Team", "months": {"Sep": 1}, "activity": "Coordination with Partner Community & LGU"}, {"no": "2", "agency": "Extension Faculty", "months": {"Oct": 1, "Sep": 1}, "activity": "Preparation of Training Modules and Instructional Materials"}, {"no": "3", "agency": "Project Team & Partners", "months": {"Oct": 1}, "activity": "Conduct of Capability Training Workshops"}, {"no": "4", "agency": "Project Proponents", "months": {"Nov": 1}, "activity": "Post-Activity Evaluation & Terminal Reporting"}]}', '[{"risk": "Inclement weather or typhoon disruption", "prevention": "Coordinate backup indoor venue and monitor weather advisories.", "risk_level": "Low", "activity_no": "1", "objective_no": "1"}, {"risk": "Intermittent internet connectivity during practical demo", "prevention": "Pre-download offline presentation modules and localized toolkits.", "risk_level": "Medium", "activity_no": "3", "objective_no": "2"}, {"risk": "Low participant turnout", "prevention": "Coordinate actively with community LGU leaders and provide certificates of completion.", "risk_level": "High", "activity_no": "4", "objective_no": "3"}]', '{"rows": [{"name": "Rodrigo T. Dela Cruz Jr.", "role": "Project Leader", "responsibilities": "Overall project management, institutional coordination, and reporting"}]}', '{"mode": "dynamic", "dean_name": null, "coord_name": null, "dean_title": null, "focal_name": null, "coord_title": null, "focal_title": null, "beneficiaries_name": null, "beneficiaries_title": null}', NULL, 7, 7, 'Rodrigo T. Dela Cruz Jr.', 'Project Leader', '2026-10-30 15:03:47', 'submitted', NULL, NULL, '2026-10-30 07:02:08', '2026-10-30 07:03:47', NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.extension_proposal_sdgs: 12 rows
DELETE FROM `extension_proposal_sdgs`;
/*!40000 ALTER TABLE `extension_proposal_sdgs` DISABLE KEYS */;
INSERT INTO `extension_proposal_sdgs` (`id`, `extension_proposal_id`, `sdg_id`, `is_primary`, `relevance_notes`, `created_at`, `updated_at`) VALUES
	(1, 1, 4, 1, NULL, '2026-10-24 05:20:33', '2026-10-30 16:41:07'),
	(2, 1, 9, 1, NULL, '2026-10-24 05:20:33', '2026-10-30 16:41:07'),
	(3, 1, 11, 0, NULL, '2026-10-24 05:20:33', '2026-10-30 16:41:07'),
	(4, 2, 11, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(5, 2, 13, 0, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33'),
	(6, 3, 1, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(7, 3, 2, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(8, 3, 3, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(9, 3, 4, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(10, 3, 9, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(11, 3, 16, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08'),
	(12, 3, 17, 0, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08');
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
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.migrations: 70 rows
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
	(68, '2026_10_26_144310_add_extension_signatories_to_units_and_plans_table', 12),
	(69, '2026_10_29_204100_add_tracking_number_to_extension_plans_table', 13),
	(70, '2026_10_30_134557_add_ui_template_to_sys_theme_settings_table', 14);
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

-- Dumping data for table extend.model_has_roles: 23 rows
DELETE FROM `model_has_roles`;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 7),
	(2, 'App\\Models\\User', 2),
	(3, 'App\\Models\\User', 3),
	(3, 'App\\Models\\User', 6),
	(4, 'App\\Models\\User', 4),
	(5, 'App\\Models\\User', 1),
	(5, 'App\\Models\\User', 5),
	(5, 'App\\Models\\User', 10),
	(5, 'App\\Models\\User', 11),
	(5, 'App\\Models\\User', 13),
	(5, 'App\\Models\\User', 25),
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

-- Dumping data for table extend.notifications: 9 rows
DELETE FROM `notifications`;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('d1328f5c-61d6-4286-aa1f-9314cbc76f2d', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 1, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"jlz-iUg4hXek8Pn_KyG2AR1U5PCNwLE","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('7a25441e-06d9-43e1-8aae-853f77975f80', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 2, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"dSAuZo9goBnS7f14WQgoAHGr4bkKqbc","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('608a4633-2b5a-40cb-857e-363f625a3825', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 3, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"lseqJcyqZYL0qZXkHv6rRlSe_fX0R-o","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('276e7b2f-4955-4c07-a975-1dc1cb78cc32', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 4, '{"title":"P.1.A \\u2014 Mock Audit Scored (4.25)","message":"ADSSU System Administrator evaluated score at 4.25 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/hEia9nxSJG4WU0sBBu-DL0tIVfqErOo\\/audit","benchmark_code":"P.1.A","benchmark_id":"8_0jOPGq_7odHB1pVb8oA2C5AqWPYdQ","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 04:06:49', '2026-09-16 04:06:49'),
	('ea51b181-a105-4528-a3ec-ce094a89f186', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 1, '{"title":"P.1.1 \\u2014 Mock Audit Scored (4.20)","message":"ADSSU System Administrator evaluated score at 4.20 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/YfrrdK1gy4ubQsSfx4_kstdVEDv5zSk\\/audit","benchmark_code":"P.1.1","benchmark_id":"iPIAgkSmPLHuRIiWzspnCVUHRreZYN8","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 05:16:15', '2026-09-16 05:16:15'),
	('5d99f9e9-bb1f-4da2-871b-590b09b759b5', 'App\\Notifications\\AccreditationAuditNotification', 'App\\Models\\User', 6, '{"title":"P.1.1 \\u2014 Mock Audit Scored (4.20)","message":"ADSSU System Administrator evaluated score at 4.20 with remarks.","action_type":"mock_audit_scored","url":"http:\\/\\/iipm-dss.dev.com\\/dss\\/accreditation\\/benchmarks\\/YfrrdK1gy4ubQsSfx4_kstdVEDv5zSk\\/audit","benchmark_code":"P.1.1","benchmark_id":"RBc60TrrZhoa42MV5Q1b2DntyC1aPBQ","sender_name":"ADSSU System Administrator","sender_id":1,"created_at_human":"0 seconds ago"}', NULL, '2026-09-16 05:16:15', '2026-09-16 05:16:15'),
	('835d1fb3-2ca1-4705-8f3a-4fee30457ede', 'App\\Notifications\\DocumentWorkflowNotification', 'App\\Models\\User', 1, '{"title":"Test Title","message":"Test Message","action_type":"general","url":null,"document_type":null,"document_id":null,"reference_code":null,"sender_name":null,"sender_id":null}', NULL, '2026-10-29 05:51:11', '2026-10-29 05:51:11'),
	('18ebf4fe-f024-45da-a3f6-4064977a8a66', 'App\\Notifications\\DocumentWorkflowNotification', 'App\\Models\\User', 24, '{"title":"Ready For Your Signature: yuuytutyu","message":"Document has advanced in routing and is ready for your endorsement as Reviewed by:.","action_type":"signature_required","url":"http:\\/\\/extend.dev.com\\/dss\\/documents\\/extension_plan\\/7\\/view","document_type":"extension_plan","document_id":"7","reference_code":"SNSU-EXT-2026-0007","sender_name":"Engr. Antonio Piloton Jr.","sender_id":7}', NULL, '2026-10-29 05:51:16', '2026-10-29 05:51:16'),
	('83cae840-abcf-4b66-a369-70e6c0ee7aa3', 'App\\Notifications\\DocumentWorkflowNotification', 'App\\Models\\User', 23, '{"title":"Ready For Your Signature: yuuytutyu","message":"Document has advanced in routing and is ready for your endorsement as Noted by:.","action_type":"signature_required","url":"http:\\/\\/extend.dev.com\\/dss\\/documents\\/extension_plan\\/7\\/view","document_type":"extension_plan","document_id":"7","reference_code":"SNSU-EXT-2026-0007","sender_name":"Engr. Antonio Piloton Jr.","sender_id":7}', NULL, '2026-10-30 06:03:09', '2026-10-30 06:03:09');
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
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.permissions: 31 rows
DELETE FROM `permissions`;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(23, 'system.hierarchy.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(24, 'system.theme.customize', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(25, 'system.roles.manage', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(26, 'system.users.assign', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(28, 'rie.plans.view', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(29, 'rie.plans.create', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(30, 'rie.plans.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(31, 'rie.plans.delete', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(32, 'rie.proposals.view', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(33, 'rie.proposals.create', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(34, 'rie.proposals.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(35, 'rie.proposals.review', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(36, 'rie.budgets.view', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(37, 'rie.budgets.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(38, 'rie.tracking.view', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(39, 'rie.tracking.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(40, 'rie.signatures.sign', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(41, 'rie.documents.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(42, 'rie.settings.manage', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(43, 'dashboard.view', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(44, 'rie.proposals.evaluate', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(45, 'rie.approvals.manage', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(46, 'rie.implementation.manage', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(47, 'rie.activities.manage', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(48, 'rie.beneficiaries.manage', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(49, 'rie.partners.manage', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(50, 'rie.reports.accomplishment', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(51, 'rie.impact.evaluate', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(52, 'rie.documents.view', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(53, 'rie.reports.analytics', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14'),
	(54, 'system.audit.view', 'web', '2026-10-29 07:07:14', '2026-10-29 07:07:14');
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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.roles: 18 rows
DELETE FROM `roles`;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'SuperAdmin', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(2, 'President', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(4, 'Director', 'web', '2026-09-16 04:06:48', '2026-09-16 04:06:48'),
	(5, 'Admin', 'web', '2026-09-16 07:59:10', '2026-09-16 07:59:10'),
	(15, 'Employee', 'web', NULL, NULL),
	(16, 'Faculty', 'web', NULL, NULL),
	(18, 'College Extension Head', 'web', '2026-10-26 06:46:20', '2026-10-26 06:46:20'),
	(19, 'UnitHead', 'web', '2026-10-26 06:46:20', '2026-10-26 06:46:20'),
	(20, 'Extension Tracking Officer', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(21, 'Extension Budget Officer', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(22, 'Extension M&E Officer', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(23, 'Extension Document Custodian', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(24, 'Dean', 'web', '2026-10-29 06:49:03', '2026-10-29 06:49:03'),
	(25, 'Project Leader', 'web', '2026-10-29 06:49:04', '2026-10-29 06:49:04'),
	(26, 'Extension Coordinator', 'web', '2026-10-29 07:07:15', '2026-10-29 07:07:15'),
	(27, 'Extension Staff', 'web', '2026-10-29 07:07:15', '2026-10-29 07:07:15'),
	(28, 'Evaluator', 'web', '2026-10-29 07:07:15', '2026-10-29 07:07:15'),
	(29, 'Auditor', 'web', '2026-10-29 07:07:16', '2026-10-29 07:07:16');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table extend.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.role_has_permissions: 225 rows
DELETE FROM `role_has_permissions`;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
	(1, 3),
	(2, 3),
	(3, 3),
	(4, 3),
	(9, 3),
	(11, 3),
	(13, 3),
	(23, 1),
	(23, 5),
	(24, 1),
	(24, 5),
	(25, 1),
	(25, 5),
	(26, 1),
	(26, 5),
	(28, 1),
	(28, 2),
	(28, 4),
	(28, 5),
	(28, 15),
	(28, 16),
	(28, 18),
	(28, 19),
	(28, 20),
	(28, 21),
	(28, 22),
	(28, 23),
	(28, 24),
	(28, 25),
	(28, 26),
	(28, 27),
	(29, 1),
	(29, 5),
	(29, 16),
	(29, 25),
	(30, 1),
	(30, 2),
	(30, 4),
	(30, 5),
	(30, 18),
	(30, 22),
	(30, 26),
	(30, 27),
	(31, 1),
	(31, 5),
	(32, 1),
	(32, 2),
	(32, 4),
	(32, 5),
	(32, 15),
	(32, 16),
	(32, 18),
	(32, 19),
	(32, 20),
	(32, 21),
	(32, 22),
	(32, 23),
	(32, 24),
	(32, 25),
	(32, 26),
	(32, 27),
	(32, 28),
	(33, 1),
	(33, 5),
	(33, 16),
	(33, 25),
	(34, 1),
	(34, 2),
	(34, 4),
	(34, 5),
	(35, 1),
	(35, 2),
	(35, 4),
	(35, 5),
	(35, 18),
	(35, 22),
	(35, 26),
	(35, 28),
	(36, 1),
	(36, 2),
	(36, 4),
	(36, 5),
	(36, 21),
	(37, 1),
	(37, 2),
	(37, 4),
	(37, 5),
	(37, 21),
	(38, 1),
	(38, 2),
	(38, 4),
	(38, 5),
	(38, 18),
	(38, 20),
	(38, 23),
	(38, 26),
	(38, 27),
	(39, 1),
	(39, 2),
	(39, 4),
	(39, 5),
	(39, 20),
	(39, 27),
	(40, 1),
	(40, 2),
	(40, 4),
	(40, 5),
	(40, 16),
	(40, 18),
	(40, 19),
	(40, 24),
	(40, 25),
	(40, 26),
	(41, 1),
	(41, 2),
	(41, 4),
	(41, 5),
	(41, 16),
	(41, 18),
	(41, 20),
	(41, 21),
	(41, 22),
	(41, 23),
	(41, 25),
	(41, 26),
	(41, 27),
	(42, 1),
	(42, 2),
	(42, 4),
	(42, 5),
	(43, 1),
	(43, 2),
	(43, 4),
	(43, 5),
	(43, 15),
	(43, 16),
	(43, 18),
	(43, 19),
	(43, 20),
	(43, 21),
	(43, 22),
	(43, 23),
	(43, 24),
	(43, 25),
	(43, 26),
	(43, 27),
	(43, 28),
	(43, 29),
	(44, 1),
	(44, 5),
	(44, 18),
	(44, 26),
	(44, 28),
	(45, 1),
	(45, 2),
	(45, 4),
	(45, 5),
	(45, 19),
	(45, 24),
	(46, 1),
	(46, 5),
	(46, 18),
	(46, 25),
	(46, 26),
	(47, 1),
	(47, 5),
	(47, 25),
	(47, 27),
	(48, 1),
	(48, 5),
	(48, 18),
	(48, 25),
	(48, 26),
	(48, 27),
	(49, 1),
	(49, 5),
	(49, 18),
	(49, 25),
	(49, 26),
	(49, 27),
	(50, 1),
	(50, 2),
	(50, 4),
	(50, 5),
	(50, 18),
	(50, 22),
	(50, 25),
	(50, 26),
	(51, 1),
	(51, 2),
	(51, 4),
	(51, 5),
	(51, 18),
	(51, 22),
	(51, 26),
	(51, 28),
	(52, 1),
	(52, 2),
	(52, 4),
	(52, 5),
	(52, 15),
	(52, 16),
	(52, 18),
	(52, 19),
	(52, 20),
	(52, 21),
	(52, 22),
	(52, 23),
	(52, 24),
	(52, 25),
	(52, 26),
	(52, 27),
	(52, 28),
	(52, 29),
	(53, 1),
	(53, 2),
	(53, 4),
	(53, 5),
	(53, 19),
	(53, 24),
	(53, 27),
	(53, 29),
	(54, 1),
	(54, 5),
	(54, 29);
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
	('BQ6g7LA4KGN9Vy5pplWoWXlk9OIphQrXAZgGnMjI', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'eyJfdG9rZW4iOiIyWkg5NTJ4MFBzdnhST0puMUE3ekM4U3FoTVdBVzltYUFDMXZ2U0FYIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvZXh0ZW5kLmRldi5jb21cL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1793407207);
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_comments: 7 rows
DELETE FROM `sys_comments`;
/*!40000 ALTER TABLE `sys_comments` DISABLE KEYS */;
INSERT INTO `sys_comments` (`id`, `tenant_id`, `commentable_type`, `commentable_id`, `user_id`, `author_name`, `author_designation`, `action_type`, `comment`, `rating_at_time`, `stage_at_time`, `metadata`, `created_at`, `updated_at`) VALUES
	(1, 1, 'memo', 204, 11, 'ANTONIO JR. L. PILOTON', 'Campus Information Systems Administrator', 'comment', 'hello', NULL, NULL, NULL, '2026-10-16 07:11:34', '2026-10-16 07:11:34'),
	(2, 1, 'memo', 204, 11, 'ANTONIO JR. L. PILOTON', 'Campus Information Systems Administrator', 'endorsed', 'hahahahaahhahaah', NULL, NULL, NULL, '2026-10-16 07:11:45', '2026-10-16 07:11:45'),
	(3, 1, 'extension_plan', 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', 'endorsed', 'Affixed digital signature & seal as Prepared by: [SIG-SNSU-EXT-2026-0007-1AE3A0BC3AAAE406].', NULL, NULL, NULL, '2026-10-29 05:38:50', '2026-10-29 05:38:50'),
	(4, 1, 'extension_plan', 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', 'endorsed', 'Affixed digital signature & seal as Prepared by: [SIG-SNSU-EXT-2026-0007-C80EE61704B0E835].', NULL, NULL, NULL, '2026-10-29 05:38:55', '2026-10-29 05:38:55'),
	(5, 1, 'extension_plan', 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', 'endorsed', 'Affixed digital signature & seal as Prepared by: [SIG-SNSU-EXT-2026-0007-C20C28BAE4896A88].', NULL, NULL, NULL, '2026-10-29 05:42:49', '2026-10-29 05:42:49'),
	(6, 1, 'extension_plan', 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', 'endorsed', 'Affixed digital signature & seal as Prepared by: [SIG-SNSU-EXT-2026-0007-93578CDC7E99B243].', NULL, NULL, NULL, '2026-10-29 05:51:16', '2026-10-29 05:51:16'),
	(7, 1, 'extension_plan', 7, 7, 'Antonio Jr. L. Piloton', 'College Head for Extension', 'endorsed', 'Affixed digital signature & seal as Reviewed by: [SIG-SNSU-EXT-2026-0007-9D47415C8A367232].', NULL, NULL, NULL, '2026-10-30 06:03:09', '2026-10-30 06:03:09');
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

-- Dumping data for table extend.sys_document_audit_trails: 3 rows
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
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.sys_document_signatories: 53 rows
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
	(44, 1, 4, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 5, NULL, 'approved', 'Approved by', 'specific_employee', 4, NULL, 'Dr. Alan P. Martinez', 'Dean', 'Office of the University President', 3, 1, 2, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-24 05:20:33', '2026-10-24 05:20:33', NULL),
	(45, 1, 11, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 7, NULL, 'prepared', 'Prepared by:', 'creator_selected', 7, 7, 'Antonio Jr. L. Piloton', 'Project Leader', NULL, 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-29 05:51:16', 7, 'data:image/png;base64,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', 'draw', 'SIG-SNSU-EXT-2026-0007-93578CDC7E99B243', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'Verified and electronically endorsed.', NULL, '2026-10-26 07:10:39', '2026-10-29 05:51:16', NULL),
	(46, 1, 11, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 7, NULL, 'reviewed', 'Reviewed by:', 'creator_selected', 20, 24, 'College Head for Extension', 'College Head for Extension', 'College of Arts and Sciences', 1, 2, 1, 'left', 'signed', 1, 1, '2026-10-30 06:03:09', 7, 'data:image/png;base64,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', 'draw', 'SIG-SNSU-EXT-2026-0007-9D47415C8A367232', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', 'Verified and electronically endorsed.', NULL, '2026-10-26 07:10:39', '2026-10-30 06:03:09', NULL),
	(47, 1, 11, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 7, NULL, 'noted', 'Noted by:', 'creator_selected', 19, 23, 'College Dean', 'College Dean', 'College of Arts and Sciences', 1, 3, 1, 'left', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 07:10:39', '2026-10-26 07:10:39', NULL),
	(48, 1, 11, NULL, NULL, 'App\\Models\\RIE\\ExtensionPlan', 7, NULL, 'concurred', 'Concurred by:', 'creator_selected', 21, NULL, 'Ever Joy Rabadan', 'University Extension Specialist V', 'University Extension Services Office', 1, 4, 1, 'left', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 07:10:39', '2026-10-26 07:10:39', NULL),
	(49, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 3, NULL, 'prepared', 'Prepared by', 'specific_employee', 9, NULL, 'Rodrigo T. Dela Cruz Jr.', 'Project Leader', 'College of Business and Technology', 1, 1, 1, 'left', 'signed', 1, 1, '2026-10-30 07:03:47', 7, NULL, NULL, NULL, NULL, NULL, 'Proposal officially submitted by Project Leader.', NULL, '2026-10-30 07:02:08', '2026-10-30 07:03:47', NULL),
	(50, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 3, NULL, 'noted', 'Noted by:', 'specific_employee', 15, NULL, 'Marilou Carnicer', 'College of Business and Technology Dean', 'College of Business and Technology', 2, 1, 1, 'left', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08', NULL),
	(51, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 3, NULL, 'concurred', 'Concurred by', 'specific_employee', 5, NULL, 'Dr. QA Admin Lead', 'Institutional Officer', 'Office of the University President', 3, 1, 1, 'left', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 07:02:08', '2026-10-30 07:02:08', NULL),
	(52, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 3, NULL, 'recommended', 'Recommended by', 'specific_employee', 3, NULL, 'Engr. Nelson V. Robles', 'Bids & Awards Committee Chairperson', 'Bids and Awards Committee (BAC) Secretariat', 4, 2, 1, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 07:02:08', '2026-10-30 07:03:23', NULL),
	(53, 1, 2, NULL, NULL, 'App\\Models\\RIE\\ExtensionProposal', 3, NULL, 'approved', 'Approved by', 'specific_employee', 2, NULL, 'Dr. Joy C. Capistrano', 'SUC President III', 'Office of the University President', 5, 2, 1, 'center', 'pending', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-30 07:02:08', '2026-10-30 07:03:23', NULL);
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
  `ui_template` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'flup_minimal',
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
INSERT INTO `sys_theme_settings` (`id`, `tenant_id`, `primary_color`, `secondary_color`, `accent_color`, `preset_key`, `ui_template`, `sidebar_theme`, `sidebar_bg`, `sidebar_text`, `sidebar_active_bg`, `sidebar_active_text`, `sidebar_border`, `navbar_theme`, `topbar_bg`, `topbar_text`, `topbar_border`, `border_radius`, `btn_primary_bg`, `btn_primary_text`, `btn_secondary_bg`, `btn_secondary_text`, `btn_radius`, `btn_elevation`, `card_bg`, `card_border`, `card_radius`, `is_dark_mode`, `font_family`, `custom_css`, `created_at`, `updated_at`) VALUES
	(1, 1, '#2563eb', '#64748b', '#f59e0b', 'minimal_frost', 'flup_minimal', 'light', '#f8fafc', '#334155', '#eff6ff', '#2563eb', '#e2e8f0', 'light', '#ffffff', '#0f172a', '#e2e8f0', '12px', '#2563eb', '#ffffff', '#64748b', '#ffffff', '50px', 'subtle', '#ffffff', '#e2e8f0', '12px', 0, 'Inter', NULL, '2026-09-16 04:06:48', '2026-10-30 15:43:26'),
	(9, 9, '#047857', '#0284c7', '#f59e0b', NULL, 'flup_minimal', 'brand', NULL, NULL, NULL, NULL, NULL, 'light', NULL, NULL, NULL, '10px', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '\'Poppins\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif', NULL, '2026-09-16 10:03:24', '2026-09-16 10:03:24'),
	(8, 8, '#047857', '#0284c7', '#f59e0b', 'adssu_emerald', 'flup_minimal', 'brand', '#ffffff', '#1e293b', '#ecfdf5', '#047857', '#e2e8f0', 'light', '#ffffff', '#0f172a', '#e2e8f0', '10px', '#047857', '#ffffff', '#0284c7', '#ffffff', '8px', 'subtle', '#ffffff', '#e2e8f0', '12px', 0, 'Inter', NULL, '2026-09-16 09:17:57', '2026-09-16 09:38:12');
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

-- Dumping data for table extend.sys_uploaded_documents: 3 rows
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

-- Dumping data for table extend.units: 22 rows
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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table extend.users: 23 rows
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `employee_id`, `tenant_id`, `campus_id`, `dept_id`, `name`, `email`, `role`, `designation`, `phone_number`, `avatar_url`, `status`, `email_verified_at`, `password`, `google_id`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 'ADSSU System Administrator', 'admin@adssu.edu.ph', 'Admin', 'Management Information Systems Director', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, 'Rcr0Q6ITqXeVsk0auzFirafRmwlk9mn6mXw6WUI8x8PmfxjBgdDLKEd8Dt9g', '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(2, 2, 1, 1, 1, 'Dr. Joy C. Capistrano', 'president@adssu.edu.ph', 'President', 'SUC President III', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, 'QbBfBBenyZEJBZ3WbRgBuj6tANv4evPLb1I7jtZ6hiwrZRD69ri282pRA5c9', '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(3, 3, 1, 1, 2, 'Engr. Nelson V. Robles', 'bac@adssu.edu.ph', 'BACMember', 'Bids & Awards Committee Chairperson', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(4, 4, 1, 2, 1, 'Dr. Alan P. Martinez', 'director@adssu.edu.ph', 'Director', 'Campus Director, Trento Campus', NULL, NULL, 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, NULL, '2026-09-16 04:06:48', '2026-09-16 15:30:09'),
	(5, 5, 1, 1, 1, 'Dr. QA Admin Lead', 'admin_test_qa@iipm.edu.ph', 'Admin', 'Institutional Officer', '09170000005', NULL, 'active', NULL, '$2y$12$AKVF9LRG4vEGZL7pkTTGAuKHWOMKZws0Y.AjikOF/Ik5W2UQhuW4i', NULL, NULL, '2026-09-16 04:14:14', '2026-10-26 04:27:08'),
	(6, 6, 1, 1, 1, 'Dr. Diane B. Remot (TL)', 'auditor_faculty_test@iipm.edu.ph', 'BACMember', 'Institutional Officer', '09170000006', NULL, 'active', NULL, '$2y$12$9LjhqU4tCWRRCA8Kd8YIg.9Rp/.SRANEk4wUrebJMhkHo2g9NsHH2', NULL, NULL, '2026-09-16 04:14:14', '2026-10-25 23:47:09'),
	(7, 7, 1, 11, 15, 'Antonio Piloton Jr.', 'apiloton1@snsu.edu.ph', 'SuperAdmin', 'Platform Super Administrator / Chief Enterprise Architect', '09128941731', 'https://lh3.googleusercontent.com/a/ACg8ocICxZuvbmnED8B5vH9nAzZ75oj4EyyJOq04YOMO7jxaSaNGZqsv=s96-c', 'active', NULL, '$2y$12$1o2Ma0ddNXSkUP/em5QYhOeOZFK7Xh9kGoiLbpUGs6rlaj2t5f1LS', NULL, '0vqryDDF2B8WyOtvyPYgFsvZRr4h6lWX0YGf5rRpcLeczq2cCUkjR5oelyyH', '2026-09-16 07:59:10', '2026-10-23 19:22:50'),
	(10, NULL, 8, 10, 14, 'ANTONIO JR. L. PILOTON', 'apiloton@ssct.edu.ph', 'SuperAdmin', 'Campus Information Systems Administrator', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocL3wAymfBAhWbSdEodGOmAd_GQYm5owScFjk3IjUnV-AxNh7o8=s96-c', 'active', NULL, '$2y$12$Qt1ZMLGQIgZ9UMrvkNE2KuAXFh.gcP/Z/tfBmqA.t0pTbq6ulSLzq', NULL, 'lK0AsujsPwhO9GwS5zyJuUjX1m0qcY75AtNIvrQKphm5X01tgtrEFU0F5awu', '2026-09-16 09:17:57', '2026-09-16 09:46:23'),
	(11, NULL, 1, 11, 15, 'ANTONIO JR. L. PILOTON', 'antoniojrpiloton@gmail.com', 'Admin', 'Campus Information Systems Administrator', NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKkyDZe64PBZKwzpp0RZycR0EkIrvLozpRb9PuWO14e-MU6Upk=s96-c', 'active', NULL, '$2y$12$eiK2UctO6bAmF/WQ/Q5bg.fiDtUakJ3.LMS00GYU3WKIeAMtSkFr6', NULL, 'YTFSpCsFlWKeoMOz9xWMryX8zLIOj66KCmsVaeVipluxpN3uyjhTRjB65zv9', '2026-09-16 10:03:25', '2026-10-29 05:15:37'),
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
	(24, 20, 1, 1, 21, 'LUIS E. CUABO', 'l.cuabo@adssu.edu.ph', 'Staff', 'College of Arts and Sciences Head for Extension', NULL, NULL, 'active', NULL, '$2y$12$S5MQjImllW3B1xQfLN3.SO/1M1Zy7zQW6sODf3sHJQJRlNbWEwZ16', NULL, NULL, '2026-10-26 06:46:22', '2026-10-26 06:46:22'),
	(25, 22, 1, 1, 22, 'Alvie Elma Plaza', 'a.plaza@adssu.edu.ph', 'Admin', 'University Extension Director', NULL, NULL, 'active', NULL, '$2y$12$qfJnxgQw3XIjp/b58sANru3ASVkh4/L.RGjcYUFBJP9iB/sZUzuQ6', NULL, NULL, '2026-10-29 04:57:43', '2026-10-29 04:57:43');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
