-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 20, 2025 at 12:13 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(107, '2025B1003', '2025B1003', 'Etudiant');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `connexion_prof`
--

INSERT INTO `connexion_prof` (`id`, `matricule`, `password`) VALUES
(2, 'ENS-20241227233306', 'ENS-20241227233306'),
(3, 'ENS-20241227233348', 'ENS-20241227233348');

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enseignants`
--

INSERT INTO `enseignants` (`id`, `matricule`, `nom`, `prenom`, `photo`, `email`, `fonction`, `date_enregistrement`) VALUES
(23, 'ENS-20241227233306', 'Fomekong', 'Evariste', 'uploads/ENS-20241227233306_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:06'),
(24, 'ENS-20241227233348', 'Batchato', 'Faguy', 'uploads/ENS-20241227233348_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'Docteur', '2024-12-28 00:33:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `matricule`, `photo`, `email`, `niveau`, `montant`, `nom_parent`, `email_parent`, `age`, `montant_paye`, `reste`, `statut`, `date_naissance`) VALUES
(93, 'MBALA', 'victor', '2025B1001', 'ENS-20241217032703_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'MEKONGO', 'aymerickbaba05@gmail.com', 19, 0, 1000000, 'Insolvable', '2011-12-17'),
(94, 'baba', 'Alberto', '2025B1002', 'ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'MEKONGO', 'aymerickbaba05@gmail.com', 19, 0, 1000000, 'Insolvable', '2011-12-17'),
(95, 'Myriam', 'Deffo', '2025B2001', 'ENS-20241230134204_ENS-20241216174220_ENS-20241216174040_graduated (1).png', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'victor baba', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-10'),
(96, 'baba', 'victor', '2025B3001', 'ENS-20241223091912_ENS-20241216173927_graduated (1).png', 'aymerickbaba05@gmail.com', 'B3', 3000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-11'),
(97, 'baba', 'victor', '2025B2002', 'face.jpg', 'aymerickbaba05@gmail.com', 'B2', 2000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 2000000, 'Insolvable', '2011-12-09'),
(99, 'baba', 'victor', '2025B1003', 'ENS-20241227231205_ENS-20241217032016_graduated (1).png', 'aymerickbaba05@gmail.com', 'B1', 1000000.00, 'Marinelle Marinelle', 'aymerickbaba05@gmail.com', 12, 0, 1000000, 'Insolvable', '2011-12-16');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b1`
--

INSERT INTO `exam_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`) VALUES
(14, '2025B1001', 'MBALA', 'victor', '19', '20'),
(15, '2025B1002', 'baba', 'Alberto', '17', '18'),
(16, '2025B1003', 'baba', 'victor', '0', '0');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b2`
--

INSERT INTO `exam_b2` (`id`, `matricule`, `nom`, `prenom`, `python`) VALUES
(10, '2025B2001', 'Myriam', 'Deffo', '18'),
(11, '2025B2002', 'baba', 'victor', '19');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_b3`
--

INSERT INTO `exam_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`) VALUES
(5, '2025B3001', 'baba', 'victor', '19');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matieres`
--

INSERT INTO `matieres` (`id`, `matricule_prof`, `nom_prof`, `nom_matiere`, `niveau_matiere`, `cours`) VALUES
(26, 'ENS-20241227233306', 'Fomekong Evariste', 'Algebre_lineaire', 'B1', 'Programmation Orientée Objet - JAVA.pdf'),
(27, 'ENS-20241227233306', 'Fomekong Evariste', 'travail_public', 'B1', '9.4.1.1 Can You Call Me Now.pdf'),
(28, 'ENS-20241227233348', 'Batchato Faguy', 'python', 'B2', 'Programmation Orientée Objet - JAVA.pdf'),
(29, 'ENS-20241227233348', 'Batchato Faguy', 'Linux', 'B3', 'support_de_cours_synthetisé_keyce_java.pdf');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b1`
--

INSERT INTO `note_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`) VALUES
(6, '2025B1001', 'MBALA', 'victor', '5', '19'),
(7, '2025B1002', 'baba', 'Alberto', '17', '17'),
(8, '2025B1003', 'baba', 'victor', '0', '0');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b2`
--

INSERT INTO `note_b2` (`id`, `matricule`, `nom`, `prenom`, `python`) VALUES
(2, '2025B2001', 'Myriam', 'Deffo', '17'),
(3, '2025B2002', 'baba', 'victor', '13');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `note_b3`
--

INSERT INTO `note_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`) VALUES
(2, '2025B3001', 'baba', 'victor', '18');

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(19, '2025B2002', 'baba', 'victor', 'ef67a0d381717c23cb98ffb36d24683d9c07e599f839432e3c035bf2499b1eb1');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b1`
--

INSERT INTO `tp_b1` (`id`, `matricule`, `nom`, `prenom`, `Algebre_lineaire`, `travail_public`) VALUES
(2, '2025B1001', 'MBALA', 'victor', '19', '15'),
(3, '2025B1002', 'baba', 'Alberto', '17', '16'),
(4, '2025B1003', 'baba', 'victor', '0', '0');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b2`
--

INSERT INTO `tp_b2` (`id`, `matricule`, `nom`, `prenom`, `python`) VALUES
(1, '2025B2001', 'Myriam', 'Deffo', '19'),
(2, '2025B2002', 'baba', 'victor', '17');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tp_b3`
--

INSERT INTO `tp_b3` (`id`, `matricule`, `nom`, `prenom`, `Linux`) VALUES
(1, '2025B3001', 'baba', 'victor', '16');

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(32, '2024B2001', 10000, '2024-12-27', '241227V0007');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
