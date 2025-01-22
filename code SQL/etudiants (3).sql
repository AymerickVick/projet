-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 22, 2025 at 08:28 PM
-- Server version: 8.4.3
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etudiants`
--

-- --------------------------------------------------------

--
-- Table structure for table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `statut` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connexion`
--

INSERT INTO `connexion` (`id`, `matricule`, `password`, `statut`) VALUES
(94, 'ENS-20241227233306', 'prof', 'Professeur'),
(99, '2025B1002', 'keyce', 'Etudiant'),
(100, 'Admin', 'admin', 'Administration'),
(101, '2025B2001', 'prof', 'Etudiant'),
(103, '2025B3001', 'keyce', 'Etudiant'),
(104, 'ENS-20241227233348', 'prof', 'Professeur'),
(105, '2025B2002', '2025B2002', 'Etudiant'),
(107, '2025B1003', '2025B1003', 'Etudiant'),
(108, '2025B1004', '2025B1004', 'Etudiant'),
(109, '2025B2003', '2025B2003', 'Etudiant'),
(110, '2025B3002', '2025B3002', 'Etudiant'),
(111, '2025B1005', '2025B1005', 'Etudiant'),
(112, '2025B2004', '2025B2004', 'Etudiant'),
(113, '2025B3003', '2025B3003', 'Etudiant'),
(114, '', '', 'Etudiant'),
(115, '2025B2005', '2025B2005', 'Etudiant'),
(116, '2025B3005', '2025B3005', 'Etudiant'),
(118, '2025B3007', 'keyce', 'Etudiant'),
(119, 'ENS-20250120133823', 'ENS-20250120133823', 'Professeur'),
(120, 'ENS-20250120134338', 'ENS-20250120134338', 'Professeur'),
(121, 'ENS-20250120134936', 'ENS-20250120134936', 'Professeur'),
(122, 'ENS-20250120135120', 'ENS-20250120135120', 'Professeur'),
(123, 'ENS-20250120135307', 'ENS-20250120135307', 'Professeur'),
(124, 'ENS-20250120135407', 'ENS-20250120135407', 'Professeur'),
(125, 'ENS-20250120140410', 'ENS-20250120140410', 'Professeur'),
(126, 'ENS-20250120140447', 'ENS-20250120140447', 'Professeur');

-- --------------------------------------------------------

--
-- Table structure for table `connexion_prof`
--

DROP TABLE IF EXISTS `connexion_prof`;
CREATE TABLE IF NOT EXISTS `connexion_prof` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connexion_prof`
--

INSERT INTO `connexion_prof` (`id`, `matricule`, `password`) VALUES
(2, 'ENS-20241227233306', 'ENS-20241227233306'),
(3, 'ENS-20241227233348', 'ENS-20241227233348'),
(4, 'ENS-20250120133823', 'ENS-20250120133823'),
(5, 'ENS-20250120134338', 'ENS-20250120134338'),
(6, 'ENS-20250120134936', 'ENS-20250120134936'),
(7, 'ENS-20250120135120', 'ENS-20250120135120'),
(8, 'ENS-20250120135307', 'ENS-20250120135307'),
(9, 'ENS-20250120135407', 'ENS-20250120135407'),
(10, 'ENS-20250120140410', 'ENS-20250120140410'),
(11, 'ENS-20250120140447', 'ENS-20250120140447');

-- --------------------------------------------------------

--
-- Table structure for table `coursb1`
--

DROP TABLE IF EXISTS `coursb1`;
CREATE TABLE IF NOT EXISTS `coursb1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jour` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi') NOT NULL,
  `horaire` enum('matin','soir') NOT NULL,
  `cours` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coursb1`
--

INSERT INTO `coursb1` (`id`, `jour`, `horaire`, `cours`) VALUES
(1, 'lundi', 'matin', 'TPE'),
(2, 'lundi', 'soir', 'TPE'),
(3, 'mardi', 'matin', 'TPE'),
(4, 'mardi', 'soir', 'TPE'),
(5, 'mercredi', 'matin', 'Algebre_lineaire'),
(6, 'mercredi', 'soir', 'travail_public'),
(7, 'jeudi', 'matin', 'Algebre_lineaire'),
(8, 'jeudi', 'soir', 'travail_public'),
(9, 'vendredi', 'matin', 'TPE'),
(10, 'vendredi', 'soir', 'TPE'),
(11, 'samedi', 'matin', 'TPE'),
(12, 'samedi', 'soir', 'TPE');

-- --------------------------------------------------------

--
-- Table structure for table `coursb2`
--

DROP TABLE IF EXISTS `coursb2`;
CREATE TABLE IF NOT EXISTS `coursb2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jour` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi') NOT NULL,
  `horaire` enum('matin','soir') NOT NULL,
  `cours` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coursb2`
--

INSERT INTO `coursb2` (`id`, `jour`, `horaire`, `cours`) VALUES
(1, 'lundi', 'matin', 'TPE'),
(2, 'lundi', 'soir', 'python'),
(3, 'mardi', 'matin', 'TPE'),
(4, 'mardi', 'soir', 'python'),
(5, 'mercredi', 'matin', 'Gemini'),
(6, 'mercredi', 'soir', 'python'),
(7, 'jeudi', 'matin', 'TPE'),
(8, 'jeudi', 'soir', 'Gemini'),
(9, 'vendredi', 'matin', 'Gemini'),
(10, 'vendredi', 'soir', 'python'),
(11, 'samedi', 'matin', 'TPE'),
(12, 'samedi', 'soir', 'TPE'),
(13, 'lundi', 'matin', 'python'),
(14, 'lundi', 'matin', 'TPE');

-- --------------------------------------------------------

--
-- Table structure for table `coursb3`
--

DROP TABLE IF EXISTS `coursb3`;
CREATE TABLE IF NOT EXISTS `coursb3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jour` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi') NOT NULL,
  `horaire` enum('matin','soir') NOT NULL,
  `cours` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `coursb3`
--

INSERT INTO `coursb3` (`id`, `jour`, `horaire`, `cours`) VALUES
(1, 'lundi', 'matin', 'TPE'),
(2, 'lundi', 'soir', 'Reseau'),
(3, 'lundi', 'matin', 'Linux'),
(4, 'mardi', 'matin', 'TPE'),
(5, 'mardi', 'soir', 'TPE'),
(6, 'mercredi', 'matin', 'TPE'),
(7, 'mercredi', 'soir', 'TPE'),
(8, 'jeudi', 'matin', 'TPE'),
(9, 'jeudi', 'soir', 'Reseau'),
(10, 'vendredi', 'matin', 'Linux'),
(11, 'vendredi', 'soir', 'TPE'),
(12, 'samedi', 'matin', 'TPE'),
(13, 'samedi', 'soir', 'TPE');

-- --------------------------------------------------------

--
-- Table structure for table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `fonction` varchar(50) DEFAULT NULL,
  `date_enregistrement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `photo`, `email`, `fonction`, `date_enregistrement`) VALUES
(23, 'ENS-20241227233306', 'Fomekong', 'Evariste', 'uploads/ENS-20241227233306_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:06'),
(24, 'ENS-20241227233348', 'Batchato', 'Faguy', 'uploads/ENS-20241227233348_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:48'),
(25, 'ENS-20250120133823', 'OLE', 'OLE', 'uploads/ENS-20250120133823_ENS-20241216174701_ENS-20241216173927_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2025-01-20 14:38:23');

-- --------------------------------------------------------

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `niveau` varchar(10) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `nom_parent` varchar(100) NOT NULL,
  `email_parent` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `montant_paye` bigint DEFAULT '0',
  `reste` bigint DEFAULT NULL,
  `statut` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `matricule`, `photo`, `email`, `niveau`, `montant`, `nom_parent`, `email_parent`, `age`, `montant_paye`, `reste`, `statut`, `date_naissance`) VALUES
(93, 'MBALA', 'victor', '2025B1001', 'ENS-20241217032703_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'MEKONGO', 'aymerickbaba05@gmail.com', 19, 0, 1000000, 'Insolvable', '2011-12-17'),
(94, 'baba', 'Alberto', '2025B1002', 'ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'MEKONGO', 'aymerickbaba05@gmail.com', 19, 1000000, 0, 'Solvable', '2011-12-17'),
(95, 'Myriam', 'Deffo', '2025B2001', 'ENS-20241230134204_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'victor baba', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-10'),
(96, 'baba', 'victor', '2025B3001', 'ENS-20241223091912_ENS-20241216173927_graduated (1).png', 'aymerickbaba05@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-11'),
(97, 'baba', 'victor', '2025B2002', 'face.jpg', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-09'),
(99, 'baba', 'victor', '2025B1003', 'ENS-20241227231205_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 1000000, 'Insolvable', '2011-12-16'),
(100, 'baba', 'victor', '2025B1004', 'ENS-20241227231125_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 1000000, 'Insolvable', '2011-12-09'),
(101, 'baba', 'victor', '2025B2003', 'ENS-20241223091912_ENS-20241216173927_graduated (1).png', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-04'),
(102, 'baba', 'victor', '2025B3002', 'ENS-20241227233306_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 3000000, 'Insolvable', '2011-12-09'),
(103, 'baba', 'victor', '2025B1005', 'graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 1000000, 'Insolvable', '2011-12-03'),
(104, 'baba', 'victor', '2025B2004', 'ENS-20241227233306_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'victor baba', 'aymerickbaba05@gmail.com', 19, 0, 2000000, 'Insolvable', '2011-12-10'),
(105, 'Marinelle', 'Marinelle', '2025B3003', 'graduated (1).png', 'marinelleebiane1@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 19, 0, 3000000, 'Insolvable', '2011-12-16'),
(107, 'Marinelle', 'Marinelle', '2025B2005', 'ENS-20241223091912_ENS-20241216173927_graduated (1).png', 'marinelleebiane1@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 19, 0, 2000000, 'Insolvable', '2011-12-10'),
(108, 'Marinelle', 'Marinelle', '2025B3005', 'ENS-20241227231205_ENS-20241217032016_graduated (1).png', 'marinelleebiane1@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 19, 2500000, 500000, 'En cours', '2011-12-23'),
(110, 'baba', 'victor', '2025B3007', 'ENS-20241230134212_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 20, 0, 3000000, 'Insolvable', '2011-12-22'),
(111, 'baba', 'victor', '2025B2006', 'IMG_7533.PNG', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'marinelleebiane1@gmail.com', 20, 0, 2000000, 'Insolvable', '2011-12-09');

-- --------------------------------------------------------

--
-- Table structure for table `exam_b1`
--

DROP TABLE IF EXISTS `exam_b1`;
CREATE TABLE IF NOT EXISTS `exam_b1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Algebre_lineaire` varchar(60) DEFAULT '0',
  `travail_public` varchar(60) DEFAULT '0',
  `open_AI` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b1`
--

INSERT INTO `exam_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`, `open_AI`) VALUES
(14, '2025B1001', 'MBALA', 'victor', '19', '20', '0'),
(15, '2025B1002', 'baba', 'Alberto', '17', '18', '0'),
(16, '2025B1003', 'baba', 'victor', '0', '0', '0'),
(17, '2025B1004', 'baba', 'victor', '0', '0', '0'),
(18, '2025B1005', 'baba', 'victor', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `exam_b2`
--

DROP TABLE IF EXISTS `exam_b2`;
CREATE TABLE IF NOT EXISTS `exam_b2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `python` varchar(60) DEFAULT '0',
  `Gemini` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b2`
--

INSERT INTO `exam_b2` (`id`, `matricule`, `nom`, `prenom`, `python`, `Gemini`) VALUES
(10, '2025B2001', 'Myriam', 'Deffo', '18', '0'),
(11, '2025B2002', 'baba', 'victor', '19', '0'),
(13, '2025B2003', 'baba', 'victor', '12', '0'),
(14, '2025B2004', 'baba', 'victor', '18', '0'),
(15, '2025B2005', 'Marinelle', 'Marinelle', '16', '0');

-- --------------------------------------------------------

--
-- Table structure for table `exam_b3`
--

DROP TABLE IF EXISTS `exam_b3`;
CREATE TABLE IF NOT EXISTS `exam_b3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Linux` varchar(60) DEFAULT '0',
  `Reseau` varchar(60) DEFAULT '0',
  `CCNA` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b3`
--

INSERT INTO `exam_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`, `Reseau`, `CCNA`) VALUES
(5, '2025B3001', 'baba', 'victor', '19', '5', '12'),
(6, '2025B3002', 'baba', 'victor', '15', '14', '12'),
(7, '2025B3003', 'Marinelle', 'Marinelle', '17', '20', '13'),
(9, '2025B3005', 'Marinelle', 'Marinelle', '12', '16', '20'),
(11, '2025B3007', 'baba', 'victor', '14', '17', '17');

-- --------------------------------------------------------

--
-- Table structure for table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule_prof` varchar(50) NOT NULL,
  `nom_prof` varchar(100) NOT NULL,
  `nom_matiere` varchar(100) NOT NULL,
  `niveau_matiere` varchar(100) NOT NULL,
  `cours` varchar(45) DEFAULT '',
  `nombre_seance` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matieres`
--

INSERT INTO `matieres` (`id`, `matricule_prof`, `nom_prof`, `nom_matiere`, `niveau_matiere`, `cours`, `nombre_seance`) VALUES
(26, 'ENS-20241227233306', 'Fomekong Evariste', 'Algebre_lineaire', 'B1', 'Programmation Orientée Objet - JAVA.pdf', 6),
(27, 'ENS-20241227233306', 'Fomekong Evariste', 'travail_public', 'B1', '9.4.1.1 Can You Call Me Now.pdf', 4),
(28, 'ENS-20241227233348', 'Batchato Faguy', 'python', 'B2', 'Programmation Orientée Objet - JAVA.pdf', 3),
(29, 'ENS-20241227233348', 'Batchato Faguy', 'Linux', 'B3', 'support_de_cours_synthetisé_keyce_java.pdf', -1),
(30, 'ENS-20241227233348', 'Batchato Faguy', 'Reseau', 'B3', '', 6),
(31, 'ENS-20241227233348', 'Batchato Faguy', 'CCNA', 'B3', '', 6),
(32, 'ENS-20241227233348', 'Batchato Faguy', 'Gemini', 'B2', '', 7),
(33, 'ENS-20250120133823', 'OLE OLE', 'open_AI', 'B1', '', 10);

-- --------------------------------------------------------

--
-- Table structure for table `note_b1`
--

DROP TABLE IF EXISTS `note_b1`;
CREATE TABLE IF NOT EXISTS `note_b1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(45) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Algebre_lineaire` varchar(60) DEFAULT '0',
  `travail_public` varchar(60) DEFAULT '0',
  `open_AI` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b1`
--

INSERT INTO `note_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`, `open_AI`) VALUES
(6, '2025B1001', 'MBALA', 'victor', '5', '19', '0'),
(7, '2025B1002', 'baba', 'Alberto', '17', '17', '0'),
(8, '2025B1003', 'baba', 'victor', '0', '0', '0'),
(9, '2025B1004', 'baba', 'victor', '0', '0', '0'),
(10, '2025B1005', 'baba', 'victor', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `note_b2`
--

DROP TABLE IF EXISTS `note_b2`;
CREATE TABLE IF NOT EXISTS `note_b2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(45) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `python` varchar(60) DEFAULT '0',
  `Gemini` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b2`
--

INSERT INTO `note_b2` (`id`, `matricule`, `nom`, `prenom`, `python`, `Gemini`) VALUES
(2, '2025B2001', 'Myriam', 'Deffo', '17', '0'),
(3, '2025B2002', 'baba', 'victor', '13', '0'),
(5, '2025B2003', 'baba', 'victor', '15', '0'),
(6, '2025B2004', 'baba', 'victor', '15', '0'),
(7, '2025B2005', 'Marinelle', 'Marinelle', '17', '0');

-- --------------------------------------------------------

--
-- Table structure for table `note_b3`
--

DROP TABLE IF EXISTS `note_b3`;
CREATE TABLE IF NOT EXISTS `note_b3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(45) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Linux` varchar(60) DEFAULT '0',
  `Reseau` varchar(60) DEFAULT '0',
  `CCNA` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b3`
--

INSERT INTO `note_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`, `Reseau`, `CCNA`) VALUES
(2, '2025B3001', 'baba', 'victor', '18', '12', '15'),
(3, '2025B3002', 'baba', 'victor', '12', '15', '18'),
(4, '2025B3003', 'Marinelle', 'Marinelle', '18', '16', '15'),
(6, '2025B3005', 'Marinelle', 'Marinelle', '13', '14', '19'),
(8, '2025B3007', 'baba', 'victor', '15', '18', '14');

-- --------------------------------------------------------

--
-- Table structure for table `releve_hash`
--

DROP TABLE IF EXISTS `releve_hash`;
CREATE TABLE IF NOT EXISTS `releve_hash` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(45) NOT NULL,
  `nom` varchar(85) NOT NULL,
  `prenom` varchar(85) NOT NULL,
  `hash` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `releve_hash`
--

INSERT INTO `releve_hash` (`id`, `matricule`, `nom`, `prenom`, `hash`) VALUES
(1, '2025B1001', 'MBALA', 'victor', 'c186aab3f71587ef56c4cf53dff6f72f'),
(2, '2025B1001', 'MBALA', 'victor', '485efa262489f370e3814c5d3fd5370b'),
(3, '2025B1001', 'MBALA', 'victor', '4775a14e76a2d856b28aaf5a7b609199'),
(4, '2025B1001', 'MBALA', 'victor', '3e137cd62d32eec5f73a6d29af378261'),
(5, '2025B1001', 'MBALA', 'victor', '3e137cd62d32eec5f73a6d29af378261'),
(6, '2025B1001', 'MBALA', 'victor', 'a135f871ad9276b3fa3ad855c6cc436f'),
(7, '2025B1001', 'MBALA', 'victor', 'a135f871ad9276b3fa3ad855c6cc436f'),
(8, '2025B1001', 'MBALA', 'victor', '2b70122d1fefdba19885b245bc243bb1'),
(9, '2025B1001', 'MBALA', 'victor', '9c1290b609bd791e8f8b30e589cad423');

-- --------------------------------------------------------

--
-- Table structure for table `releve_signature`
--

DROP TABLE IF EXISTS `releve_signature`;
CREATE TABLE IF NOT EXISTS `releve_signature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(45) NOT NULL,
  `nom` varchar(45) NOT NULL,
  `prenom` varchar(45) NOT NULL,
  `signature` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `releve_signature`
--

INSERT INTO `releve_signature` (`id`, `matricule`, `nom`, `prenom`, `signature`) VALUES
(1, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(2, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(3, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(4, '2025B1002', 'baba', 'Alberto', 'b70689313b587b7e19095658ceb63c62c0907dae1b4ecbfd7ef3c71bdf6c5951'),
(5, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(6, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(7, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(8, '2025B1001', 'MBALA', 'victor', 'e560e223de01c66219692cd63bf2d6d2a10b01087111bc2f2f72f82217e56c96'),
(9, '2025B1002', 'baba', 'Alberto', 'b70689313b587b7e19095658ceb63c62c0907dae1b4ecbfd7ef3c71bdf6c5951'),
(10, '2025B1001', 'MBALA', 'victor', '71024d3cdaa33d8889fde6cda6a1519d3db8cba4896f97d2df6f0660587a34ea'),
(11, '2025B1002', 'baba', 'Alberto', '74efea0d4dce2bd0237d7fbf6282274048ea1a104d41c42e991a9452fe572da0'),
(12, '2025B1001', 'MBALA', 'victor', '71024d3cdaa33d8889fde6cda6a1519d3db8cba4896f97d2df6f0660587a34ea'),
(13, '2025B1002', 'baba', 'Alberto', '74efea0d4dce2bd0237d7fbf6282274048ea1a104d41c42e991a9452fe572da0'),
(14, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(15, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(16, '2025B2001', 'Myriam', 'Deffo', '2bc14195ccbced61d1c65ab18fcb3e6139192e11fad3fa9f343edeed35da5a93'),
(17, '2025B2002', 'baba', 'victor', 'ef67a0d381717c23cb98ffb36d24683d9c07e599f839432e3c035bf2499b1eb1'),
(18, '2025B2001', 'Myriam', 'Deffo', '2bc14195ccbced61d1c65ab18fcb3e6139192e11fad3fa9f343edeed35da5a93'),
(19, '2025B2002', 'baba', 'victor', 'ef67a0d381717c23cb98ffb36d24683d9c07e599f839432e3c035bf2499b1eb1'),
(20, '2025B3001', 'baba', 'victor', 'e5156ce747f618549b86386475449ed0479b78b6f26eb2a60c79085925a75396'),
(21, '2025B3001', 'baba', 'victor', 'e5156ce747f618549b86386475449ed0479b78b6f26eb2a60c79085925a75396'),
(22, '2025B3003', 'Marinelle', 'Marinelle', '78508f2b69a13718e03d90d39d213ab42fb20291ff99c726c506a453fc2437f0'),
(23, '2025B3002', 'baba', 'victor', '10942c055fa207b982bec8aeec7c557611ed6094533ad0e05866a9c25f6a75c5'),
(24, '2025B3007', 'baba', 'victor', 'ddce77913729a15b08f73d427681ad228c882c361b1fb99e955eb248f79a8682'),
(25, '2025B3005', 'Marinelle', 'Marinelle', 'f0727ed5b93954bd836c9b3a05d3c76c5bdb5a6990c5a855e2ff775130d2bdde'),
(26, '2025B3003', 'Marinelle', 'Marinelle', '0cde876e29bbec0eaf4e313b338904360ce6f3534f44a6806aee0c4df409f683'),
(27, '2025B3005', 'Marinelle', 'Marinelle', 'c6e0801df01cb620f47bd5d711be710eb54ccc3b28ff73de756e523311abaf80'),
(28, '2025B3007', 'baba', 'victor', 'ce5a9281442f762fb1b92026cbc96eb5728aae8e3b2c9f99e3c197ad4f1756b2'),
(29, '2025B3002', 'baba', 'victor', '5751f8379619dc7eaea52b6e5e2e077dc83762c6768c2489e8c68d4df9f8989f'),
(30, '2025B3001', 'baba', 'victor', 'c7f382a66316edde372d503140a0c2079fbd6905be15cc539e8dcb85188dcf8c'),
(31, '2025B3003', 'Marinelle', 'Marinelle', '0cde876e29bbec0eaf4e313b338904360ce6f3534f44a6806aee0c4df409f683'),
(32, '2025B3005', 'Marinelle', 'Marinelle', 'c6e0801df01cb620f47bd5d711be710eb54ccc3b28ff73de756e523311abaf80'),
(33, '2025B3007', 'baba', 'victor', 'ce5a9281442f762fb1b92026cbc96eb5728aae8e3b2c9f99e3c197ad4f1756b2'),
(34, '2025B3002', 'baba', 'victor', '5751f8379619dc7eaea52b6e5e2e077dc83762c6768c2489e8c68d4df9f8989f'),
(35, '2025B3001', 'baba', 'victor', 'c7f382a66316edde372d503140a0c2079fbd6905be15cc539e8dcb85188dcf8c'),
(36, '2025B3003', 'Marinelle', 'Marinelle', '0cde876e29bbec0eaf4e313b338904360ce6f3534f44a6806aee0c4df409f683'),
(37, '2025B3005', 'Marinelle', 'Marinelle', 'c6e0801df01cb620f47bd5d711be710eb54ccc3b28ff73de756e523311abaf80'),
(38, '2025B3007', 'baba', 'victor', 'ce5a9281442f762fb1b92026cbc96eb5728aae8e3b2c9f99e3c197ad4f1756b2'),
(39, '2025B3002', 'baba', 'victor', '5751f8379619dc7eaea52b6e5e2e077dc83762c6768c2489e8c68d4df9f8989f'),
(40, '2025B3001', 'baba', 'victor', 'c7f382a66316edde372d503140a0c2079fbd6905be15cc539e8dcb85188dcf8c'),
(41, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(42, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(43, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(44, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(45, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(46, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(47, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(48, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(49, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(50, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(51, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(52, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(53, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(54, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(55, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(56, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(57, '2025B3003', 'Marinelle', 'Marinelle', '0cde876e29bbec0eaf4e313b338904360ce6f3534f44a6806aee0c4df409f683'),
(58, '2025B3005', 'Marinelle', 'Marinelle', 'c6e0801df01cb620f47bd5d711be710eb54ccc3b28ff73de756e523311abaf80'),
(59, '2025B3007', 'baba', 'victor', 'ce5a9281442f762fb1b92026cbc96eb5728aae8e3b2c9f99e3c197ad4f1756b2'),
(60, '2025B3002', 'baba', 'victor', '5751f8379619dc7eaea52b6e5e2e077dc83762c6768c2489e8c68d4df9f8989f'),
(61, '2025B3001', 'baba', 'victor', 'c7f382a66316edde372d503140a0c2079fbd6905be15cc539e8dcb85188dcf8c'),
(62, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(63, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(64, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(65, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(66, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(67, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(68, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(69, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(70, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(71, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(72, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(73, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(74, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(75, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c'),
(76, '2025B1002', 'baba', 'Alberto', '24fa3af1358ffdfc48fb51dd3c0c0b6a2f6b30e0bd2988f71cf4f124dea538ab'),
(77, '2025B1001', 'MBALA', 'victor', '72bc2d78da77d303868de7d5daa3e4af550933a895a0dbaef6ae3c8d91747e0c');

-- --------------------------------------------------------

--
-- Table structure for table `tp_b1`
--

DROP TABLE IF EXISTS `tp_b1`;
CREATE TABLE IF NOT EXISTS `tp_b1` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Algebre_lineaire` varchar(60) DEFAULT '0',
  `travail_public` varchar(60) DEFAULT '0',
  `open_AI` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b1`
--

INSERT INTO `tp_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`, `open_AI`) VALUES
(2, '2025B1001', 'MBALA', 'victor', '19', '15', '0'),
(3, '2025B1002', 'baba', 'Alberto', '17', '16', '0'),
(4, '2025B1003', 'baba', 'victor', '0', '0', '0'),
(5, '2025B1004', 'baba', 'victor', '0', '0', '0'),
(6, '2025B1005', 'baba', 'victor', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tp_b2`
--

DROP TABLE IF EXISTS `tp_b2`;
CREATE TABLE IF NOT EXISTS `tp_b2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `python` varchar(60) DEFAULT '0',
  `Gemini` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b2`
--

INSERT INTO `tp_b2` (`id`, `matricule`, `nom`, `prenom`, `python`, `Gemini`) VALUES
(1, '2025B2001', 'Myriam', 'Deffo', '19', '0'),
(2, '2025B2002', 'baba', 'victor', '17', '0'),
(4, '2025B2003', 'baba', 'victor', '16', '0'),
(5, '2025B2004', 'baba', 'victor', '20', '0'),
(6, '2025B2005', 'Marinelle', 'Marinelle', '18', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tp_b3`
--

DROP TABLE IF EXISTS `tp_b3`;
CREATE TABLE IF NOT EXISTS `tp_b3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `Linux` varchar(60) DEFAULT '0',
  `Reseau` varchar(60) DEFAULT '0',
  `CCNA` varchar(60) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b3`
--

INSERT INTO `tp_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`, `Reseau`, `CCNA`) VALUES
(1, '2025B3001', 'baba', 'victor', '16', '15', '16'),
(2, '2025B3002', 'baba', 'victor', '16', '13', '19'),
(3, '2025B3003', 'Marinelle', 'Marinelle', '16', '18', '14'),
(5, '2025B3005', 'Marinelle', 'Marinelle', '15', '18', '18'),
(7, '2025B3007', 'baba', 'victor', '13', '15', '16');

-- --------------------------------------------------------

--
-- Table structure for table `versements`
--

DROP TABLE IF EXISTS `versements`;
CREATE TABLE IF NOT EXISTS `versements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(20) DEFAULT NULL,
  `montant` bigint DEFAULT NULL,
  `date_versement` date DEFAULT NULL,
  `numero_versement` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `versements`
--

INSERT INTO `versements` (`id`, `matricule`, `montant`, `date_versement`, `numero_versement`) VALUES
(23, '2024B2001', 1000000, '2024-12-17', '241217V0001'),
(24, '2024B1001', 1000000, '2024-12-17', '241217V0002'),
(25, '2024B2001', 1000000, '2024-12-17', '241217V0003'),
(26, '2024B2001', 1000000, '2024-12-17', '241217V0004'),
(27, '2024B1001', 1000000, '2024-12-17', '241217V0005'),
(28, '2024B2002', 1000000, '2024-12-17', '241217V0006'),
(32, '2024B2001', 10000, '2024-12-27', '241227V0007'),
(33, '2025B3005', 2500000, '2025-01-22', '250122V0008'),
(34, '2025B1002', 1000000, '2025-01-22', '250122V0009');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
