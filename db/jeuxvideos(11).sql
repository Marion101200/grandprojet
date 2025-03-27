-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 27 mars 2025 à 08:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `jeuxvideos`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id`, `email`, `mdp`) VALUES
(1, 'admin@hotmail.com', '$2y$10$PhbIzkxVEHme9LuaE/qzrOuVEA2ATF6vh84aKe81LoCGumYl0Pkyu'),
(22, 'admin11@hotmail.com', '$2a$11$bwjNUKuPk8luaUkzKcUwwOFz7M72YolnvGHIgRvZcLjNwLM8MVnx6');

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE `adresse` (
  `id` int(11) NOT NULL,
  `id_clients` int(11) NOT NULL,
  `adresse` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `adresse`
--

INSERT INTO `adresse` (`id`, `id_clients`, `adresse`) VALUES
(12, 68, '15 rue de rivoli'),
(13, 68, '15 place jeanne d\'arc'),
(14, 68, '6 rue de Gaulle'),
(15, 68, '6 rue Marie Currie'),
(16, 68, '6 rue Casimir');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `jeux_titre` varchar(200) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `commentaire` text NOT NULL,
  `note` float DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `jeux_titre`, `nom`, `commentaire`, `note`, `date_ajout`) VALUES
(4, 'Baldur\'s Gate 3', 'Marion', 'Excellent jeux, je recommande fortement.', 5, '2025-02-07 14:16:49'),
(5, 'Elden Ring', 'Marion', 'Le jeux est beaucoup trop dur, je ne recommande pas', 1, '2025-02-07 15:11:39'),
(6, 'Shadow of the tomb raider', 'Marion', 'J\'adore énormément ce jeux, un des plus grand chef d\'œuvre de jeux vidéos.', 5, '2025-02-07 15:45:36'),
(7, 'Assassin\'s creed odyssey', 'Lolee', 'L\'histoire est très fun.', 4, '2025-02-07 15:46:10'),
(8, 'Baldur\'s Gate 3', 'Lolee', 'Pas compris le but du jeux.', 1, '2025-02-07 15:46:29'),
(9, 'Cyberpunk 2077', 'Lolee', 'Graphisme très beau mais une histoire pas folle, c\'est dommage', 2, '2025-02-07 15:47:32'),
(10, 'Elden Ring', 'Lolee', 'Je confirme le jeux est trop dur.', 1, '2025-02-07 15:48:39'),
(11, 'Résident Evil 4', 'Lolee', 'Un jeux d\'horreur super flippant, je recommande fortement.', 5, '2025-02-07 15:49:04'),
(12, 'The Witcher 3', 'Lolee', 'Très bon jeux.', 4, '2025-02-07 15:49:18'),
(13, 'Borderland 3', 'Lolee', 'Univers très sympa.', 3, '2025-02-07 15:49:40'),
(16, 'Baldur\'s Gate 3', 'Marion', 'Jeux plutôt beau, avec une belle narration mais le jeux et mal optimiser.', 2, '2025-02-07 16:03:28'),
(17, 'Baldur\'s Gate 3', 'Marion', 'Passable', 4, '2025-02-07 16:03:49'),
(18, 'Baldur\'s Gate 3', 'Marion', 'C\'est bien', 5, '2025-02-07 16:04:08'),
(20, 'Indiana Jones', 'Marion', 'Beau jeux', 3, '2025-02-18 10:52:28');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(150) NOT NULL,
  `date_token` datetime NOT NULL DEFAULT current_timestamp(),
  `etat_token` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `email`, `mdp`, `date_token`, `etat_token`, `token`) VALUES
(68, 'Marion', '', 'marion-trouve@hotmail.com', '$2y$10$W1W9SUmHwA494XtftPylheyEc5EkX5eEq9eqRCXiSDo9ziE0z3Opi', '2025-02-05 16:05:02', 1, 'cd3096b65a84801d326a6152f8872e39');

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `id_clients` int(11) NOT NULL,
  `montant` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_clients`, `montant`) VALUES
(6, 68, 120),
(7, 68, 120),
(8, 68, 120),
(9, 68, 120),
(10, 68, 120),
(11, 68, 120),
(12, 68, 120),
(13, 68, 120),
(16, 68, 30),
(17, 68, 60),
(18, 68, 30),
(19, 68, 0),
(20, 68, 0),
(21, 68, 0),
(22, 68, 60),
(23, 68, 60),
(24, 68, 60),
(25, 68, 60),
(26, 68, 60),
(27, 68, 60),
(28, 68, 60),
(29, 68, 60),
(30, 68, 60),
(31, 68, 60),
(32, 68, 60),
(33, 68, 60),
(34, 68, 60),
(35, 68, 60),
(36, 68, 60),
(37, 68, 60),
(38, 68, 60),
(39, 68, 60),
(40, 68, 60),
(41, 68, 30),
(42, 68, 30),
(43, 68, 30),
(44, 68, 30),
(45, 68, 30),
(46, 68, 30),
(47, 68, 60),
(48, 68, 60),
(49, 68, 60),
(50, 68, 60),
(51, 68, 60),
(52, 68, 60),
(53, 68, 60),
(54, 68, 60),
(55, 68, 60),
(56, 68, 60),
(57, 68, 60),
(58, 68, 60),
(59, 68, 60),
(60, 68, 60),
(61, 68, 60),
(62, 68, 60),
(63, 68, 60),
(64, 68, 60),
(65, 68, 60),
(66, 68, 60),
(67, 68, 60),
(68, 68, 60),
(69, 68, 60),
(70, 68, 60),
(71, 68, 60),
(72, 68, 60),
(73, 68, 60),
(74, 68, 60),
(75, 68, 60),
(76, 68, 60),
(77, 68, 60),
(78, 68, 60),
(79, 68, 60),
(80, 68, 60),
(81, 68, 60),
(82, 68, 60),
(83, 68, 60),
(84, 68, 60),
(85, 68, 60),
(86, 68, 120),
(87, 68, 120),
(88, 68, 120),
(89, 68, 120),
(90, 68, 120),
(91, 68, 120),
(92, 68, 120),
(93, 68, 120),
(94, 68, 120),
(95, 68, 120),
(96, 68, 120),
(97, 68, 120),
(98, 68, 120),
(99, 68, 120),
(100, 68, 120),
(101, 68, 60),
(102, 68, 60),
(103, 68, 60),
(104, 68, 60),
(105, 68, 60),
(106, 68, 60),
(107, 68, 60),
(108, 68, 60),
(109, 68, 60),
(110, 68, 60),
(111, 68, 60),
(112, 68, 50),
(113, 68, 50),
(114, 68, 50),
(115, 68, 50),
(116, 68, 50),
(117, 68, 50),
(118, 68, 50),
(119, 68, 50),
(120, 68, 60),
(121, 68, 45),
(122, 68, 45),
(123, 68, 45),
(124, 68, 45),
(125, 68, 45),
(126, 68, 45),
(127, 68, 45),
(128, 68, 45),
(129, 68, 45),
(130, 68, 45),
(131, 68, 45),
(132, 68, 45),
(133, 68, 30),
(134, 68, 30),
(135, 68, 30),
(136, 68, 30),
(137, 68, 30),
(138, 68, 30),
(139, 68, 30),
(140, 68, 30),
(141, 68, 30);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_details` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_jeu` int(11) DEFAULT NULL,
  `quantite` decimal(10,0) NOT NULL,
  `id_adresse` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_details`, `id_commande`, `id_jeu`, `quantite`, `id_adresse`) VALUES
(101, 119, 16, 0, 16),
(102, 120, 4, 0, 12),
(113, 131, 18, 0, 16),
(120, 138, 12, 0, 12);

-- --------------------------------------------------------

--
-- Structure de la table `jeux`
--

CREATE TABLE `jeux` (
  `id` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date` varchar(50) NOT NULL,
  `prix` decimal(5,2) NOT NULL,
  `images` varchar(100) NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jeux`
--

INSERT INTO `jeux` (`id`, `titre`, `categorie`, `description`, `date`, `prix`, `images`, `date_ajout`) VALUES
(2, 'Baldur\'s Gate 3', 'RPG', 'Constituez votre groupe et retournez aux Royaumes Oubliés dans une histoire d\'amitié, de trahison, de sacrifice et de survie, sur fond d\'attrait du pouvoir absolu.', '15/08/2023', 59.99, 'img/baldurs-gate-3-header.jpg', '2024-02-18 11:08:45'),
(3, 'Shadow of the tomb raider', 'Aventure', 'Alors que Lara Croft met tout en œuvre pour sauver le monde d\'une apocalypse maya, elle doit suivre son destin et devenir pilleuse de tombeaux.', '14/09/2018', 59.99, 'img/shadow of the tomb raider.avif', '2024-02-18 11:08:45'),
(4, 'Assassin\'s creed odyssey', 'Historique', 'Forgez votre destin dans Assassin\'s Creed Odyssey. Passez du statut de paria à celui de légende vivante au cours d\'une véritable odyssée durant laquelle vous lèverez les secrets sur votre passé et changerez le destin de la Grèce antique.', '05/10/2018', 59.99, 'img/odyssey.jpg', '2024-02-18 11:08:45'),
(6, 'Cyberpunk 2077', 'RPG', 'Cyberpunk 2077 est un JDR d\'action-aventure en monde ouvert, qui se déroule à Night City, une mégalopole futuriste et sombre, obsédée par le pouvoir, la séduction et les modifications corporelles.', '10/12/2020', 59.99, 'img/cyberpunk 2077.jfif', '2024-02-18 11:08:45'),
(7, 'Elden Ring', 'Soulslike', 'LE RPG D\'ACTION FANTASTIQUE ACCLAMÉ PAR LA CRITIQUE. Levez-vous, Sans-éclat, et puisse la grâce guider vos pas. Brandissez la puissance du Cercle d\'Elden. Devenez Seigneur de l\'Entre-terre.', '25/02/2022', 59.99, 'img/Elden Ring.jpg', '2024-02-18 11:08:45'),
(10, 'Résident Evil 4', 'Horreur', 'La survie n\'est que le début. Avec un gameplay modernisé, une histoire revisitée et des graphismes ultra détaillés, Resident Evil 4 signe la renaissance d\'un monstre de l\'industrie. Replongez dans le cauchemar qui a révolutionné les jeux d\'horreur et de survie.', '24/03/2023', 39.99, 'img/re.jpg', '2024-02-18 11:08:45'),
(11, 'The Witcher 3', 'RPG', 'Vous incarnez Geralt de Riv, un tueur de monstres. Devant vous s\'étend un continent en guerre, infesté de monstres, à explorer à votre guise. Votre contrat actuel ? Retrouver Ciri, l\'enfant de la prophétie, une arme vivante capable de changer le monde.', '18/05/2015', 29.99, 'img/The Witcher 3.jpg', '2024-02-18 11:08:45'),
(12, 'Subnautica', 'Survie', 'Descendez dans les profondeurs d\'un monde sous-marin étranger plein de merveilles et périls. Concevez des équipements, pilotez des sous-marins, terraformez un terrain voxel, et adaptez-vous à la vie sauvage afin d’explorer le monde, tout en essayant de survivre.', '23/01/2018', 29.99, 'img/Subnautica.jpg', '2024-02-18 11:08:45'),
(13, 'Bioshock Infinite', 'Action', 'Redevable envers les mauvaises personnes, avec sa vie en jeu, vétéran de la cavalerie américaine et désormais mercenaire, Booker DeWitt dispose d\'une opportunité unique pour effacer son ardoise. Il doit secourir Elizabeth, une mystérieuse jeune femme emprisonnée depuis sa plus tendre enfance et enfermée dans la ville flottante de Columbia. ', '25/03/2013', 29.99, 'img/Bioshock Infinite.jfif', '2024-02-18 11:08:45'),
(14, 'Borderland 3', 'RPG', 'Le shooter-looter est de retour avec ses trilliards de flingues pour une aventure complètement folle ! Affrontez de nouveaux mondes et ennemis dans la peau de l\'un des quatre Chasseurs de l\'Arche proposés, avec chacun ses propres compétences et options de personnalisation.', '13/03/2020', 59.99, 'img/Borderland 3.jfif', '2024-02-18 11:08:45'),
(15, 'Indiana Jones', 'Action', 'Nous sommes en 1937. Des forces sinistres parcourent le globe pour trouver le secret d\'un pouvoir ancestral lié au Cercle Ancien. Une seule personne peut les arrêter : Indiana Jones.', '09/12/2024', 69.99, 'img/Indiana jones.webp', '2025-02-18 11:08:45'),
(16, 'God of war', 'Action', 'Sa vengeance contre les dieux de l\'Olympe étant bien derrière lui, Kratos vit désormais comme un simple habitant du royaume des dieux (et des monstres) nordiques. C\'est dans ce monde inhospitalier et cruel qu\'il doit combattre pour sa survie... et apprendre à son fils à en faire de même.', '14/01/2022', 49.99, 'img/God of war.avif', '2025-02-18 11:08:45'),
(17, 'Spyro Reignited Trilogy', 'Aventure', 'Toujours aussi brûlant et attachant, Spyro revient dans une HD flamboyante avec Spyro Reignited Trilogy ! Rallumez la flamme avec les trois jeux originaux, Spyro the Dragon, Spyro 2: Ripto\'s Rage! et Spyro: Year of the Dragon.', '03/09/2019', 9.99, 'img/Spyro.jpg', '2025-02-18 11:08:45'),
(18, 'ARK: Suvival Ascended', 'Survie', 'Formez une tribu, apprivoisant et reprochent des centaines de dinosaures uniques et de créatures primitives, explorez, élaborez et construisez votre chemin jusqu\'au sommet de la chaîne de nourriture.', '26/10/2023', 44.99, 'img/ARK.jfif', '2025-02-18 11:13:00'),
(19, 'Dead by Daylight', 'Horreur', 'Piégés dans un royaume malfaisant où même la mort n\'est pas une issue, quatre survivants affrontent un tueur dans une épreuve brutale. Choisissez votre camp et pénétrez dans un monde de terreur avec le meilleur jeu multijoueur asymétrique de l\'horreur.', '14/06/2016', 7.99, 'img/dbd.jfif', '2025-02-18 11:24:37'),
(26, 'It takes two', 'Aventure', 'Jeux multi', '10/12/2015', 29.00, 'img/it take too.jpg', '2025-03-11 09:54:16');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `etat_du_ticket` tinyint(1) NOT NULL DEFAULT 0,
  `id_clients` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`, `etat_du_ticket`, `id_clients`) VALUES
(10, 'contact.kcc0@gmail.com', 'c526073eb6c89f0540047e070b4e0952', '2025-01-28 12:37:13', '2025-01-28 11:37:13', 1, NULL),
(11, 'marion.trouve101200@gmail.com', 'a40de9e34a8b2b2b83daaa33c193267a', '2025-01-28 12:48:36', '2025-01-28 11:48:36', 1, NULL),
(12, 'marion.trouve101200@gmail.com', 'ff35debdc644aed1a3ac852c5ae87a2c', '2025-01-28 12:57:56', '2025-01-28 11:57:56', 1, NULL),
(13, 'marion.trouve101200@gmail.com', '167ee58ec57f0eec84302ac932d53302', '2025-01-28 12:59:12', '2025-01-28 11:59:12', 1, NULL),
(14, 'marion.trouve101200@gmail.com', '37a1062eea5786dd046e042056e6d3e7', '2025-01-28 13:14:51', '2025-01-28 12:14:51', 1, NULL),
(15, 'marion.trouve101200@gmail.com', '3ba0a3954fd7e278a6eb1205f5b9e6c0', '2025-01-28 13:19:35', '2025-01-28 12:19:35', 1, NULL),
(16, 'marion.trouve101200@gmail.com', 'b0cb31f0a06d2e94677e636b4ac6f9d0', '2025-01-28 13:26:31', '2025-01-28 12:26:31', 1, NULL),
(17, 'marion.trouve101200@gmail.com', 'b158a03ec06730199ab2712c6e818407', '2025-01-28 13:30:38', '2025-01-28 12:30:38', 1, NULL),
(18, 'marion.trouve101200@gmail.com', '74435d849e12d6a8bd6514ed690b6274', '2025-01-28 13:36:49', '2025-01-28 12:36:49', 1, NULL),
(19, 'marion.trouve101200@gmail.com', '378b20c5cf22fe551b3ac3099fe5d51f', '2025-01-28 13:40:53', '2025-01-28 12:40:53', 1, NULL),
(20, 'marion.trouve101200@gmail.com', '194979ce3a7672e974d57966dd22328b', '2025-01-28 13:48:40', '2025-01-28 12:48:40', 1, NULL),
(21, 'marion.trouve101200@gmail.com', 'bbd63d6f562b988c440f4e32547e73d0', '2025-01-28 13:50:23', '2025-01-28 12:50:23', 1, NULL),
(25, 'marion-trouve@hotmail.com', 'b2ace44accc0bc8b4a3ec88412f13dfe', '2025-02-14 17:11:44', '2025-02-14 16:11:44', 1, NULL),
(27, 'marion-trouve@hotmail.com', '0e8bbc7aabe3dd0b2dd0eebdbbee218c', '2025-02-14 18:20:48', '2025-02-14 17:20:48', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `clients_id` int(11) NOT NULL,
  `jeux_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `clients_id`, `jeux_id`) VALUES
(13, 68, 3),
(14, 68, 2),
(17, 68, 14);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_adresse_clients` (`id_clients`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `fk_id_clients` (`id_clients`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_details`),
  ADD KEY `fk_commande` (`id_commande`),
  ADD KEY `fk_commande_jeu` (`id_jeu`),
  ADD KEY `fk_adresseid` (`id_adresse`);

--
-- Index pour la table `jeux`
--
ALTER TABLE `jeux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_clients` (`id_clients`);

--
-- Index pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_id` (`clients_id`),
  ADD KEY `jeux_id` (`jeux_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_details` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT pour la table `jeux`
--
ALTER TABLE `jeux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `fk_adresse_clients` FOREIGN KEY (`id_clients`) REFERENCES `clients` (`id`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `fk_id_clients` FOREIGN KEY (`id_clients`) REFERENCES `clients` (`id`);

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `fk_adresseid` FOREIGN KEY (`id_adresse`) REFERENCES `adresse` (`id`),
  ADD CONSTRAINT `fk_commande` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `fk_commande_jeu` FOREIGN KEY (`id_jeu`) REFERENCES `jeux` (`id`);

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_clients` FOREIGN KEY (`id_clients`) REFERENCES `clients` (`id`);

--
-- Contraintes pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_ibfk_1` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favorites_ibfk_2` FOREIGN KEY (`jeux_id`) REFERENCES `jeux` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
