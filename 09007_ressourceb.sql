-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- HÃ´te : 127.0.0.1
-- GÃ©nÃ©rÃ© le : mer. 01 mars 2023 Ã  11:32
-- Version du serveur : 10.4.22-MariaDB
-- Version de PHP : 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de donnÃ©es : `brie`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `prenom` varchar(35) NOT NULL,
  `nom` varchar(35) NOT NULL,
  `login` varchar(10) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- DÃ©chargement des donnÃ©es de la table `admin`
--

INSERT INTO `admin` (`id`, `prenom`, `nom`, `login`, `pass`) VALUES
(1, 'test', 'test', 'test', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `article_accueil`
--

CREATE TABLE `article_accueil` (
  `id` int(11) NOT NULL,
  `article` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bilan`
--

CREATE TABLE `bilan` (
  `id` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `Timestamp` int(11) NOT NULL,
  `nombre_vente` int(11) NOT NULL,
  `poids` int(11) NOT NULL,
  `prix_total` int(11) NOT NULL,
  `prix_total_espece` int(11) NOT NULL,
  `prix_total_cheque` int(11) NOT NULL,
  `prix_total_carte` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `mail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `date_users`
--

CREATE TABLE `date_users` (
  `id_date` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `date_derniere_visite` datetime NOT NULL,
  `date_dernier_creneau` int(11) DEFAULT NULL,
  `date_prochain_creneau` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- DÃ©chargement des donnÃ©es de la table `date_users`
--

INSERT INTO `date_users` (`id_date`, `id_user`, `date_inscription`, `date_derniere_visite`, `date_dernier_creneau`, `date_prochain_creneau`) VALUES
(1, 1, '2023-02-07 17:40:00', '2023-02-09 09:21:00', NULL, NULL),
(2, 2, '2023-02-08 14:50:00', '2023-02-08 14:50:00', NULL, NULL),
(3, 3, '2023-02-09 10:32:00', '2023-03-01 11:21:00', NULL, NULL),
(4, 4, '2023-02-09 12:06:00', '2023-02-28 17:55:00', NULL, NULL),
(5, 5, '2023-02-28 18:35:00', '2023-02-28 18:35:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `cat_creneau` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- DÃ©chargement des donnÃ©es de la table `events`
--

INSERT INTO `events` (`id`, `cat_creneau`, `name`, `description`, `start`, `end`) VALUES
(3, 1, 'test2', '', '2022-03-02 13:58:00', '2022-03-02 15:58:00'),
(7, 1, 'test3', '', '2023-03-16 12:00:00', '2023-03-16 14:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `fonction`
--

CREATE TABLE `fonction` (
  `id` int(11) NOT NULL,
  `fonction` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- DÃ©chargement des donnÃ©es de la table `fonction`
--

INSERT INTO `fonction` (`id`, `fonction`, `description`) VALUES
(1, 'tester', 'tester');

-- --------------------------------------------------------

--
-- Structure de la table `inscription_creneau`
--

CREATE TABLE `inscription_creneau` (
  `id_inscription` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `fonction` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- DÃ©chargement des donnÃ©es de la table `inscription_creneau`
--

INSERT INTO `inscription_creneau` (`id_inscription`, `id_user`, `id_event`, `fonction`) VALUES
(2, 3, 3, 'tester'),
(3, 4, 3, 'tester'),
(4, 3, 5, 'tester'),
(5, 3, 6, 'tester'),
(6, 3, 5, 'tester'),
(7, 3, 6, 'tester'),
(9, 4, 7, 'tester'),
(11, 5, 9, 'tester'),
(12, 3, 7, 'tester'),
(13, 5, 7, 'N/A');

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

CREATE TABLE `membres` (
  `id` int(11) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `naissance` date NOT NULL,
  `tel` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `objets_collectes`
--

CREATE TABLE `objets_collectes` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `souscat` varchar(255) NOT NULL,
  `poids` int(11) NOT NULL,
  `date` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  `vendu` tinyint(1) NOT NULL,
  `flux` varchar(255) NOT NULL,
  `saisipar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `objets_vendus`
--

CREATE TABLE `objets_vendus` (
  `id_achat` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `nom_vendeur` varchar(255) NOT NULL,
  `id_vendeur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `souscat` varchar(255) NOT NULL,
  `date_achat` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  `prix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `paiement_mixte`
--

CREATE TABLE `paiement_mixte` (
  `id_paiement_mixte` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `espece` int(11) NOT NULL,
  `carte` int(11) NOT NULL,
  `cheque` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `id` int(6) NOT NULL,
  `txnid` varchar(20) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `itemid` varchar(25) NOT NULL,
  `createdtime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `reparation`
--

CREATE TABLE `reparation` (
  `id_rep` int(11) NOT NULL,
  `id_objet` int(11) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `souscat` varchar(255) NOT NULL,
  `reparation` text NOT NULL,
  `saisipar` varchar(255) NOT NULL,
  `reparepar` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `daterep` varchar(255) NOT NULL,
  `timestamprep` int(11) NOT NULL,
  `end` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ticketdecaisse`
--

CREATE TABLE `ticketdecaisse` (
  `id_ticket` int(11) NOT NULL,
  `nom_vendeur` varchar(255) NOT NULL,
  `id_vendeur` int(11) NOT NULL,
  `date_achat_dt` datetime DEFAULT NULL,
  `nbr_objet` int(11) NOT NULL,
  `moyen_paiement` text NOT NULL,
  `num_cheque` text DEFAULT NULL,
  `banque` varchar(255) DEFAULT NULL,
  `num_transac` varchar(255) DEFAULT NULL,
  `prix_total` int(11) NOT NULL,
  `lien` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ticketdecaissetemp`
--

CREATE TABLE `ticketdecaissetemp` (
  `id_temp_vente` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `nom_vendeur` varchar(255) NOT NULL,
  `id_vendeur` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `souscat` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `uuid_users` int(11) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- DÃ©chargement des donnÃ©es de la table `users`
--

INSERT INTO `users` (`uuid_users`, `prenom`, `nom`, `pseudo`, `password`, `admin`, `mail`, `tel`) VALUES
(3, 'test', 'test', 'test', '$2y$10$6dHioE/yVOfFenoyZWHV/ux3PmgLFDZw/9BYjoYiWeCqiEu5xRaMa', 2, NULL, NULL),
(5, 'test3', 'test3', 'test3', '$2y$10$q3tHQfKNyrwnLAtr835FseVodAe0951LjGljh0X2hWN1TbIqyf2km', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

CREATE TABLE `vente` (
  `id_temp_vente` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `dateheure` varchar(255) NOT NULL,
  `id_vendeur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables dÃ©chargÃ©es
--

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `article_accueil`
--
ALTER TABLE `article_accueil`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `bilan`
--
ALTER TABLE `bilan`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `date_users`
--
ALTER TABLE `date_users`
  ADD PRIMARY KEY (`id_date`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fonction`
--
ALTER TABLE `fonction`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `inscription_creneau`
--
ALTER TABLE `inscription_creneau`
  ADD PRIMARY KEY (`id_inscription`);

--
-- Index pour la table `membres`
--
ALTER TABLE `membres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `objets_collectes`
--
ALTER TABLE `objets_collectes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `objets_vendus`
--
ALTER TABLE `objets_vendus`
  ADD PRIMARY KEY (`id_achat`);

--
-- Index pour la table `paiement_mixte`
--
ALTER TABLE `paiement_mixte`
  ADD PRIMARY KEY (`id_paiement_mixte`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reparation`
--
ALTER TABLE `reparation`
  ADD PRIMARY KEY (`id_rep`);

--
-- Index pour la table `ticketdecaisse`
--
ALTER TABLE `ticketdecaisse`
  ADD PRIMARY KEY (`id_ticket`);

--
-- Index pour la table `ticketdecaissetemp`
--
ALTER TABLE `ticketdecaissetemp`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uuid_users`);

--
-- Index pour la table `vente`
--
ALTER TABLE `vente`
  ADD PRIMARY KEY (`id_temp_vente`);

--
-- AUTO_INCREMENT pour les tables dÃ©chargÃ©es
--

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `article_accueil`
--
ALTER TABLE `article_accueil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bilan`
--
ALTER TABLE `bilan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `date_users`
--
ALTER TABLE `date_users`
  MODIFY `id_date` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `fonction`
--
ALTER TABLE `fonction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `inscription_creneau`
--
ALTER TABLE `inscription_creneau`
  MODIFY `id_inscription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `membres`
--
ALTER TABLE `membres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `objets_collectes`
--
ALTER TABLE `objets_collectes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `objets_vendus`
--
ALTER TABLE `objets_vendus`
  MODIFY `id_achat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiement_mixte`
--
ALTER TABLE `paiement_mixte`
  MODIFY `id_paiement_mixte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reparation`
--
ALTER TABLE `reparation`
  MODIFY `id_rep` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ticketdecaisse`
--
ALTER TABLE `ticketdecaisse`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ticketdecaissetemp`
--
ALTER TABLE `ticketdecaissetemp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `uuid_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `vente`
--
ALTER TABLE `vente`
  MODIFY `id_temp_vente` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

--
--requête pour la somme de la duréé des créneaux sur lesquels se sont inscrits les benevoles
--

SELECT USERS.nom,USERS.prenom,USERS.pseudo,SUM(TIMESTAMPDIFF(MINUTE,EVENTS.start,EVENTS.end)) DIV 60 as Heuretotal
    FROM EVENTS
     JOIN INSCRIPTION_CRENEAU ON INSCRIPTION_CRENEAU.id_event=EVENTS.id
     JOIN USERS ON USERS.id=INSCRIPTION_CRENEAU.id_user
     where YEAR(EVENTS.start) = YEAR(CURDATE()) and YEAR(EVENTS.end) = YEAR(CURDATE())
    GROUP BY INSCRIPTION_CRENEAU.id_user; 


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
