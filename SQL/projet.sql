-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Lun 11 Mars 2019 à 11:30
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE IF NOT EXISTS `annonces` (
  `id_annonce` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `nbr_votant` int(11) NOT NULL,
  `note` int(11) NOT NULL,
  `ville` varchar(50) COLLATE utf8_bin NOT NULL,
  `prix` decimal(10,0) NOT NULL,
  `date_publication` datetime NOT NULL,
  `auteur` varchar(20) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_annonce`),
  KEY `proprietaire` (`auteur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Contenu de la table `annonces`
--

INSERT INTO `annonces` (`id_annonce`, `type`, `nbr_votant`, `note`, `ville`, `prix`, `date_publication`, `auteur`) VALUES
(1, 'maison', 0, 0, 'Amiens', '20', '2019-03-11 10:14:04', 'mama');

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id_image` int(11) NOT NULL,
  `nom_image` varchar(50) COLLATE utf8_bin NOT NULL,
  `annonce` int(11) NOT NULL,
  PRIMARY KEY (`id_image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `messages_prives`
--

CREATE TABLE IF NOT EXISTS `messages_prives` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `expediteur` varchar(20) COLLATE utf8_bin NOT NULL,
  `destinataire` varchar(20) COLLATE utf8_bin NOT NULL,
  `titre` varchar(40) COLLATE utf8_bin NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `date_envoi` datetime NOT NULL,
  PRIMARY KEY (`id_message`),
  KEY `destinataire` (`destinataire`),
  KEY `expediteur` (`expediteur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `messages_prives`
--

INSERT INTO `messages_prives` (`id_message`, `expediteur`, `destinataire`, `titre`, `message`, `date_envoi`) VALUES
(1, 'mama', 'mama', 'texte', 'dfcbgnh', '2019-03-08 14:28:25'),
(2, 'mama', 'mama', 'texte', 'ddb n rf', '2019-03-08 14:39:52');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(20) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `nom` varchar(30) COLLATE utf8_bin NOT NULL,
  `prenom` varchar(30) COLLATE utf8_bin NOT NULL,
  `date_naissance` date NOT NULL,
  `date_inscription` datetime NOT NULL,
  `derniere_connexion` datetime DEFAULT NULL,
  PRIMARY KEY (`login`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`login`, `password`, `nom`, `prenom`, `date_naissance`, `date_inscription`, `derniere_connexion`) VALUES
('mama', '$2y$10$tDwVmL.Qa3v.t6Vj9PoBOujb2am00qR9UnXNyc0q6.uuSKL9lEE1K', 'mama', 'mama', '2019-03-01', '2019-03-08 14:27:35', '2019-03-11 09:28:41');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `fknom` FOREIGN KEY (`auteur`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `fk_image` FOREIGN KEY (`id_image`) REFERENCES `annonces` (`id_annonce`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `messages_prives`
--
ALTER TABLE `messages_prives`
  ADD CONSTRAINT `messages_prives_ibfk_1` FOREIGN KEY (`destinataire`) REFERENCES `users` (`login`),
  ADD CONSTRAINT `messages_prives_ibfk_2` FOREIGN KEY (`expediteur`) REFERENCES `users` (`login`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
