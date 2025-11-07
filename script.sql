DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `code_dep` int NOT NULL,
  `nom_dep` varchar(23) DEFAULT NULL,
  `code_region` int DEFAULT NULL,
  `nbr_hab` int DEFAULT NULL,
  `densite` float(6,1) DEFAULT NULL,
  `pourcpopvingt` float(18,1) DEFAULT NULL,
  `pourcpopsoixante` float(18,1) DEFAULT NULL,
  `taux_chomage` float(18,1) DEFAULT NULL,
  `taux_pauvrete` float(18,1) DEFAULT NULL,
  `nbr_foyer_salarie` int DEFAULT NULL,
  `montant_salarie` bigint DEFAULT NULL,
  `nbr_foyer_retraite` int DEFAULT NULL,
  `montant_retraite` bigint DEFAULT NULL,
  PRIMARY KEY (`code_dep`),
  KEY `code_region` (`code_region`)
)

DROP TABLE IF EXISTS `etablissement`;
CREATE TABLE IF NOT EXISTS `etablissement` (
  `id_eta` int NOT NULL,
  `code_dep` int DEFAULT NULL,
  `nbr_t_eta` int DEFAULT NULL,
  `nbr_eta_2018` int DEFAULT NULL,
  PRIMARY KEY (`id_eta`),
  KEY `code_dep` (`code_dep`)
)

DROP TABLE IF EXISTS `eta_superieur`;
CREATE TABLE IF NOT EXISTS `eta_superieur` (
  `id_eta_sup` int NOT NULL,
  `nom` varchar(198) DEFAULT NULL,
  `type d'etablissement` varchar(60) DEFAULT NULL,
  `statut` varchar(24) DEFAULT NULL,
  `adresse` varchar(127) DEFAULT NULL,
  `Code postal` int DEFAULT NULL,
  `commune` varchar(29) DEFAULT NULL,
  `code_dep` int DEFAULT NULL,
  `code_reg` int DEFAULT NULL,
  PRIMARY KEY (`id_eta_sup`),
  KEY `fk_eta_dep` (`code_dep`)
)

DROP TABLE IF EXISTS `logement`;
CREATE TABLE IF NOT EXISTS `logement` (
  `Id_log` int NOT NULL,
  `code_dep` int DEFAULT NULL,
  `nbr_log` int DEFAULT NULL,
  `taux_log_sociaux` float(6,1) DEFAULT NULL,
  `taux_log_ind` float(6,1) DEFAULT NULL,
  PRIMARY KEY (`Id_log`),
  KEY `code_dep` (`code_dep`)
)

DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `code_region` int NOT NULL,
  `nom_region` varchar(26) DEFAULT NULL,
  PRIMARY KEY (`code_region`)
)

ALTER TABLE `departement`
  ADD CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`code_region`) REFERENCES `region` (`code_region`);

--
-- Contraintes pour la table `etablissement`
--
ALTER TABLE `etablissement`
  ADD CONSTRAINT `etablissement_ibfk_1` FOREIGN KEY (`code_dep`) REFERENCES `departement` (`code_dep`);

--
-- Contraintes pour la table `logement`
--
ALTER TABLE `logement`
  ADD CONSTRAINT `logement_ibfk_1` FOREIGN KEY (`code_dep`) REFERENCES `departement` (`code_dep`);
