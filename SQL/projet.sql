-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 15 Mars 2019 à 18:36
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
<<<<<<< HEAD
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

=======
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

-- --------------------------------------------------------
>>>>>>> Projet-Programmation-Web

--
-- Structure de la table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id_image` int(11) NOT NULL AUTO_INCREMENT,
  `nom_image` varchar(50) COLLATE utf8_bin NOT NULL,
  `annonce` int(11) NOT NULL,
  PRIMARY KEY (`id_image`),
  KEY `annonce` (`annonce`),
  KEY `annonce_2` (`annonce`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

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

<<<<<<< HEAD
--
-- Contenu de la table `messages_prives`
--


=======
>>>>>>> Projet-Programmation-Web
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

<<<<<<< HEAD

=======
>>>>>>> Projet-Programmation-Web
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
  ADD CONSTRAINT `fk_annonce` FOREIGN KEY (`annonce`) REFERENCES `annonces` (`id_annonce`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `messages_prives`
--
ALTER TABLE `messages_prives`
  ADD CONSTRAINT `messages_prives_ibfk_1` FOREIGN KEY (`destinataire`) REFERENCES `users` (`login`),
  ADD CONSTRAINT `messages_prives_ibfk_2` FOREIGN KEY (`expediteur`) REFERENCES `users` (`login`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
