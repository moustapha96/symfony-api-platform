-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 15 oct. 2024 à 21:37
-- Version du serveur : 8.3.0
-- Version de PHP : 7.4.33

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `adn_db_actif`
--

-- --------------------------------------------------------

--
-- Structure de la table `adn_abonnements`
--

DROP TABLE IF EXISTS `adn_abonnements`;
CREATE TABLE IF NOT EXISTS `adn_abonnements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_debut` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `montant` double NOT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `institut_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2F9288C6ACF64F5F` (`institut_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adn_configurations`
--

DROP TABLE IF EXISTS `adn_configurations`;
CREATE TABLE IF NOT EXISTS `adn_configurations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `valeur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_configurations`
--

INSERT INTO `adn_configurations` (`id`, `valeur`, `cle`) VALUES
(1, 'cobtact@authentic.com', 'email'),
(2, 'authenticPage', 'name');

-- --------------------------------------------------------

--
-- Structure de la table `adn_demandes`
--

DROP TABLE IF EXISTS `adn_demandes`;
CREATE TABLE IF NOT EXISTS `adn_demandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `demandeur_id` int DEFAULT NULL,
  `date_demande` datetime NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee_obtention` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_institut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse_institut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_institut` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_institut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resultat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pays_institut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institut_id` int DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `institut_demandeur_id` int DEFAULT NULL,
  `document_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_925C6A04C33F7837` (`document_id`),
  KEY `IDX_925C6A0495A6EE59` (`demandeur_id`),
  KEY `IDX_925C6A04ACF64F5F` (`institut_id`),
  KEY `IDX_925C6A04A83446A7` (`institut_demandeur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_demandes`
--

INSERT INTO `adn_demandes` (`id`, `demandeur_id`, `date_demande`, `intitule`, `annee_obtention`, `name_institut`, `adresse_institut`, `phone_institut`, `email_institut`, `resultat`, `pays_institut`, `institut_id`, `is_deleted`, `institut_demandeur_id`, `document_id`) VALUES
(1, 5, '2024-09-28 17:02:33', 'Demande de diplôme', '2019', '', '', '', '', 'Accepted', '', 2, NULL, 2, 17),
(2, 5, '2024-10-02 20:18:21', 'Demande de diplôme', '2012', 'Institut de Formation', '123 Rue , Grenoble', '+33123456780', 'xar@yopmail.com', 'En attente', 'Etat Unis', NULL, NULL, 2, 3),
(3, 5, '2024-10-02 20:19:41', 'Demande de diplôme', '2012', 'Institut de Formation', '123 Rue , Grenoble', '+33123456780', 'xar@yopmail.com', 'En attente', 'Etat Unis', NULL, NULL, 2, NULL),
(4, 5, '2024-10-02 20:23:53', 'Demande de diplôme', '2012', 'Institut de Formation', '123 Rue , Grenoble', '+33123456780', 'xar@yopmail.com', 'En attente', 'Etat Unis', NULL, NULL, NULL, NULL),
(5, 5, '2024-10-06 17:55:27', 'Demande diplome de lience', '2015', 'Autre', 'Dakar , Boulevard Habib Bourguiba', '784537547', 'alhusseinkhouma0@gmail.com', 'Pending', 'Sénégal', NULL, NULL, NULL, NULL),
(6, 5, '2024-10-07 19:53:24', 'Diplome de master', '2020', 'Autre', 'Dakar', '883009876', 'xuma@yopmail.com', 'Pending', 'Senegal', NULL, NULL, NULL, NULL),
(7, 5, '2024-10-14 21:49:41', 'Diplome de master', '2016', '', '', '', '', 'Pending', '', NULL, NULL, 2, NULL),
(8, 5, '2024-10-14 21:51:37', 'Diplome de licence', '2024', 'INSTITUT DIABELOU FAYINKE', '65 Rue Des Trois Fontanot', '783012940', 'khouma964@gmail.com', 'Pending', 'Sénégal', NULL, NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `adn_demandeurs`
--

DROP TABLE IF EXISTS `adn_demandeurs`;
CREATE TABLE IF NOT EXISTS `adn_demandeurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compte_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profession` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pays_residence` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `code_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_585A8AA1F2C56620` (`compte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_demandeurs`
--

INSERT INTO `adn_demandeurs` (`id`, `compte_id`, `name`, `phone`, `adresse`, `email`, `intitule`, `date_naissance`, `lieu_naissance`, `profession`, `sexe`, `pays_residence`, `is_deleted`, `code_user`) VALUES
(4, 8, 'John Doe', '+2217712345&1', '123 Rue Example, Dakar', 'deman@demandeur.com', 'Monsieur', '1985-05-15', 'Dakar', 'Développeur', 'Homme', 'Sénégal', NULL, NULL),
(5, 9, 'Fallou Fall', '+221771234501', '123 Rue Example, Dakar', 'demandeur2@gmail.com', 'Monsieur', '1985-05-15', 'Rufique dakar ', 'Informaticien', 'Homme', 'Sénégal', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `adn_documents`
--

DROP TABLE IF EXISTS `adn_documents`;
CREATE TABLE IF NOT EXISTS `adn_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_adn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_obtention` datetime NOT NULL,
  `annee_obtention` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intitule` longtext COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `mention` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_documents`
--

INSERT INTO `adn_documents` (`id`, `code_adn`, `type_document`, `date_obtention`, `annee_obtention`, `statut`, `intitule`, `url`, `is_deleted`, `mention`) VALUES
(1, 'C-2024-28091728', 'Diplôme', '2023-06-15 17:08:28', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-20240928170828.pdf', 1, NULL),
(2, 'C-2024-28091701', 'Diplôme', '2023-06-15 17:09:01', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-20240928170901.pdf', 1, NULL),
(3, 'C-2024-28091707', 'Diplôme', '2023-06-15 17:12:07', '2020', 'Vérifié', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-20240928171207-2020.pdf', 1, NULL),
(4, 'C-2024-28091718', 'Diplôme', '2023-06-15 17:12:18', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-2020-20240928171218.pdf', 1, NULL),
(5, 'C-2024-28091707', 'Diplôme', '2023-06-15 17:14:07', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-2020-demande_de_diplôme.pdf', 1, NULL),
(6, 'C-2024-28091703', 'Diplôme', '2023-06-15 17:15:03', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-2020-john_doe.pdf', NULL, NULL),
(7, 'C-2024-28091703', 'Diplôme', '2023-06-15 17:17:03', '2020', 'Créé', 'Diplôme-2020', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', NULL, NULL),
(8, 'C-2024-28091726', 'Diplôme', '2023-06-15 17:19:26', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', NULL, NULL),
(9, 'C-2024-28091723', 'Diplôme', '2023-06-15 17:27:23', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(10, 'C-2024-28091733', 'Diplôme', '2023-06-15 17:28:33', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(11, 'C-2024-28091734', 'Diplôme', '2023-06-15 17:30:34', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(12, 'C-2024-28091754', 'Diplôme', '2023-06-15 17:32:54', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(13, 'C-2024-28091730', 'Diplôme', '2023-06-15 17:35:30', '2020', 'Créé', 'Diplôme-2020-Demande de diplôme-demande_de_diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(14, 'C-2024-28091753', 'Diplôme', '2023-06-15 17:35:53', '2020', 'Créé', 'Diplôme-2020-demande_de_diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande_de_diplôme-2020-john_doe.pdf', 1, NULL),
(15, 'C-2024-28091735', 'Diplôme', '2023-06-15 17:36:35', '2020', 'Créé', 'Diplôme-2020-demande-de-diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande-de-diplôme-2020-john-doe.pdf', 1, NULL),
(16, 'ADN-JO-2024-28091705', 'Diplôme', '2023-06-15 17:43:05', '2020', 'Créé', 'Diplôme-2020-demande-de-diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande-de-diplôme-2020-john-doe.pdf', 1, NULL),
(17, 'ADN-JO-2024092817452', 'Diplôme', '2023-06-15 17:45:27', '2020', 'Créé', 'Diplôme-2020-demande-de-diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\symfony-api-platform/public/documents/diplôme-demande-de-diplôme-2020-john-doe.pdf', 1, NULL),
(18, 'ADN-202410112249', 'Relevé', '2024-10-11 22:49:33', '2013', 'Créé', 'Relevé-2013-demande-de-diplôme', 'C:\\Users\\User\\Documents\\Authentic projet\\code\\symfony-api-platform/public/documents/relevé-demande-de-diplôme-2013-fallou-fall.pdf', NULL, 'Passable');

-- --------------------------------------------------------

--
-- Structure de la table `adn_instituts`
--

DROP TABLE IF EXISTS `adn_instituts`;
CREATE TABLE IF NOT EXISTS `adn_instituts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compte_id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_web` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pays_residence` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `code_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_657066FAF2C56620` (`compte_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_instituts`
--

INSERT INTO `adn_instituts` (`id`, `compte_id`, `email`, `phone`, `adresse`, `site_web`, `intitule`, `pays_residence`, `name`, `logo`, `type`, `is_deleted`, `code_user`) VALUES
(2, 7, 'contact@institut-tech.com', '+1234567890', '123 Rue de l\'Innovation, Paris', 'https://www.institut-tech.com', 'Institut Supérieur de Technologie', 'France', 'Institut de Technologie', 'https://www.institut-tech.com/logo.png', 'Université', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `adn_payment`
--

DROP TABLE IF EXISTS `adn_payment`;
CREATE TABLE IF NOT EXISTS `adn_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_payment`
--

INSERT INTO `adn_payment` (`id`, `user_id`, `amount`, `currency`, `payment_intent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '9', 500, 'eur', 'pi_3QAIH1JKwZ36wwZj1Wp5g7KO', 'pending', '2024-10-15 21:27:24', NULL),
(2, '9', 500, 'eur', 'pi_3QAIH3JKwZ36wwZj13F1qQsT', 'pending', '2024-10-15 21:27:25', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `adn_transactions`
--

DROP TABLE IF EXISTS `adn_transactions`;
CREATE TABLE IF NOT EXISTS `adn_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `montant` double NOT NULL,
  `date_transaction` datetime NOT NULL,
  `type_paiement` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_transaction` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `demande_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4B11930D80E95E18` (`demande_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_transactions`
--

INSERT INTO `adn_transactions` (`id`, `montant`, `date_transaction`, `type_paiement`, `type_transaction`, `is_deleted`, `etat`, `demande_id`) VALUES
(1, 500, '2024-10-13 13:29:32', 'Stripe', 'paid', NULL, 'Payer', 5);

-- --------------------------------------------------------

--
-- Structure de la table `adn_users`
--

DROP TABLE IF EXISTS `adn_users`;
CREATE TABLE IF NOT EXISTS `adn_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `activeted` tinyint(1) DEFAULT NULL,
  `token_activeted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D40F066F85E0677` (`username`),
  UNIQUE KEY `UNIQ_D40F066E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adn_users`
--

INSERT INTO `adn_users` (`id`, `username`, `roles`, `password`, `email`, `avatar`, `reset_token`, `enabled`, `activeted`, `token_activeted`) VALUES
(7, 'institut@yopmail.com\n', '[\"ROLE_INSTITUT\"]', '$2y$13$gT9w6GeF7Patqz6iWzjNLeYG5pQG5a9y09JbRYkdqp50aQ0ALjmbm', 'institut@yopmail.com', NULL, NULL, 0, 0, '00509654c68c1d045f32940d86612f19a518ca16e5bf4693eb16ce0bdc3a095f'),
(8, 'deman@demandeur.com', '[\"ROLE_DEMANDEUR\"]', '$2y$13$gT9w6GeF7Patqz6iWzjNLeYG5pQG5a9y09JbRYkdqp50aQ0ALjmbm', 'deman@demandeur.com', NULL, NULL, 1, 1, 'bd582531207909e383163374d2755bfafb11297101100cc8ea962ff77f9a9629'),
(9, 'demandeur2@gmail.com', '[\"ROLE_DEMANDEUR\"]', '$2y$13$gT9w6GeF7Patqz6iWzjNLeYG5pQG5a9y09JbRYkdqp50aQ0ALjmbm', 'demandeur2@gmail.com', NULL, NULL, 1, 1, '34b0bb0a485842226e3f0b1003d4e927d2b120850d84049fe2da0fd2218645c7'),
(10, 'admin@admin.com', '[\"ROLE_ADMIN\"]', '$2y$13$gT9w6GeF7Patqz6iWzjNLeYG5pQG5a9y09JbRYkdqp50aQ0ALjmbm', 'admin@admin.com', NULL, NULL, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240926193805', '2024-09-26 19:38:10', 1410),
('DoctrineMigrations\\Version20240926194149', '2024-09-26 19:43:20', 139),
('DoctrineMigrations\\Version20240926201017', '2024-09-26 20:10:25', 39),
('DoctrineMigrations\\Version20240926212214', '2024-09-26 21:22:23', 222),
('DoctrineMigrations\\Version20240927204326', '2024-09-27 20:44:15', 122),
('DoctrineMigrations\\Version20240927212800', '2024-09-27 21:28:23', 95),
('DoctrineMigrations\\Version20240927213042', '2024-09-27 21:30:47', 111),
('DoctrineMigrations\\Version20240927213529', '2024-09-27 21:35:32', 82),
('DoctrineMigrations\\Version20240928152726', '2024-09-28 15:27:38', 175),
('DoctrineMigrations\\Version20240928180354', '2024-09-28 18:03:57', 45),
('DoctrineMigrations\\Version20241002151043', '2024-10-02 15:10:51', 52),
('DoctrineMigrations\\Version20241003080249', '2024-10-03 08:02:59', 347),
('DoctrineMigrations\\Version20241003123538', '2024-10-03 12:35:48', 99),
('DoctrineMigrations\\Version20241003162207', '2024-10-03 16:22:16', 45),
('DoctrineMigrations\\Version20241003162259', '2024-10-03 16:23:03', 36),
('DoctrineMigrations\\Version20241003163738', '2024-10-03 16:37:51', 33),
('DoctrineMigrations\\Version20241003163842', '2024-10-03 16:38:48', 36),
('DoctrineMigrations\\Version20241004170557', '2024-10-04 17:06:08', 328),
('DoctrineMigrations\\Version20241006191156', '2024-10-06 19:12:05', 329),
('DoctrineMigrations\\Version20241007194456', '2024-10-07 19:45:03', 163),
('DoctrineMigrations\\Version20241007203311', '2024-10-07 20:33:19', 244),
('DoctrineMigrations\\Version20241007225032', '2024-10-07 22:52:00', 149),
('DoctrineMigrations\\Version20241008144724', '2024-10-08 14:47:32', 231),
('DoctrineMigrations\\Version20241008150530', '2024-10-08 15:05:33', 114),
('DoctrineMigrations\\Version20241009085307', '2024-10-09 08:53:16', 279),
('DoctrineMigrations\\Version20241009092512', '2024-10-09 09:25:15', 110),
('DoctrineMigrations\\Version20241009093630', '2024-10-09 09:36:37', 183),
('DoctrineMigrations\\Version20241011223640', '2024-10-11 22:37:00', 103),
('DoctrineMigrations\\Version20241015212527', '2024-10-15 21:25:36', 128);

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adn_abonnements`
--
ALTER TABLE `adn_abonnements`
  ADD CONSTRAINT `FK_2F9288C6ACF64F5F` FOREIGN KEY (`institut_id`) REFERENCES `adn_instituts` (`id`);

--
-- Contraintes pour la table `adn_demandes`
--
ALTER TABLE `adn_demandes`
  ADD CONSTRAINT `FK_925C6A0495A6EE59` FOREIGN KEY (`demandeur_id`) REFERENCES `adn_demandeurs` (`id`),
  ADD CONSTRAINT `FK_925C6A04A83446A7` FOREIGN KEY (`institut_demandeur_id`) REFERENCES `adn_instituts` (`id`),
  ADD CONSTRAINT `FK_925C6A04ACF64F5F` FOREIGN KEY (`institut_id`) REFERENCES `adn_instituts` (`id`),
  ADD CONSTRAINT `FK_925C6A04C33F7837` FOREIGN KEY (`document_id`) REFERENCES `adn_documents` (`id`);

--
-- Contraintes pour la table `adn_demandeurs`
--
ALTER TABLE `adn_demandeurs`
  ADD CONSTRAINT `FK_585A8AA1F2C56620` FOREIGN KEY (`compte_id`) REFERENCES `adn_users` (`id`);

--
-- Contraintes pour la table `adn_instituts`
--
ALTER TABLE `adn_instituts`
  ADD CONSTRAINT `FK_657066FAF2C56620` FOREIGN KEY (`compte_id`) REFERENCES `adn_users` (`id`);

--
-- Contraintes pour la table `adn_transactions`
--
ALTER TABLE `adn_transactions`
  ADD CONSTRAINT `FK_4B11930D80E95E18` FOREIGN KEY (`demande_id`) REFERENCES `adn_demandes` (`id`);

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `adn_users` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
