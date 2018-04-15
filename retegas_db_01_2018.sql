-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Gen 02, 2018 alle 19:47
-- Versione del server: 5.1.71-community-log
-- PHP Version: 5.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `my_retegas`
--
CREATE DATABASE IF NOT EXISTS `my_retegas` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `my_retegas`;

-- --------------------------------------------------------

--
-- Struttura della tabella `maaking_users`
--

CREATE TABLE IF NOT EXISTS `maaking_users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `site` varchar(105) NOT NULL,
  `country` varchar(200) NOT NULL,
  `city` varchar(200) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `profile` text NOT NULL,
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ipaddress` varchar(50) NOT NULL,
  `lastlogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(10) NOT NULL,
  `id_gas` int(11) NOT NULL DEFAULT '0' COMMENT 'Identifica il gas di appartenenza',
  `consenso` tinyint(1) NOT NULL DEFAULT '0',
  `user_level` int(2) NOT NULL DEFAULT '0',
  `last_activity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_permission` int(11) NOT NULL DEFAULT '0' COMMENT 'Permessi di USER per utilizzo del sito',
  `user_site_option` int(11) NOT NULL DEFAULT '0',
  `user_start_page` varchar(100) NOT NULL DEFAULT '0',
  `user_gc_lat` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `user_gc_lng` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `ci_connected` tinyint(4) NOT NULL DEFAULT '0',
  `tessera` varchar(30) NOT NULL,
  `te_connected` varchar(50) NOT NULL,
  `custom_1` text,
  `custom_2` text,
  `custom_3` text,
  PRIMARY KEY (`userid`),
  KEY `id_gas` (`id_gas`),
  FULLTEXT KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7134 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_amici`
--

CREATE TABLE IF NOT EXISTS `retegas_amici` (
  `id_amici` int(13) NOT NULL AUTO_INCREMENT,
  `id_referente` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(50) NOT NULL,
  `indirizzo` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `note` text NOT NULL,
  `is_visible` tinyint(4) DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_amici`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Sottogruppi di amici' AUTO_INCREMENT=2889 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_articoli`
--

CREATE TABLE IF NOT EXISTS `retegas_articoli` (
  `id_articoli` int(11) NOT NULL AUTO_INCREMENT,
  `id_listini` int(11) NOT NULL DEFAULT '0',
  `codice` varchar(50) NOT NULL,
  `u_misura` varchar(20) NOT NULL,
  `misura` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descrizione_articoli` varchar(100) NOT NULL,
  `qta_scatola` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `prezzo` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `ingombro` varchar(50) NOT NULL,
  `qta_minima` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `qta_multiplo` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `articoli_note` text,
  `articoli_unico` tinyint(2) DEFAULT NULL,
  `articoli_opz_1` varchar(50) DEFAULT NULL,
  `articoli_opz_2` varchar(50) DEFAULT NULL,
  `articoli_opz_3` varchar(50) DEFAULT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `id_ditta` int(11) DEFAULT NULL,
  `descrizione_ditta` varchar(100) NOT NULL,
  PRIMARY KEY (`id_articoli`),
  KEY `id_listini` (`id_listini`),
  FULLTEXT KEY `articoli_opz_1` (`articoli_opz_1`,`articoli_opz_2`,`articoli_opz_3`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Articoli ' AUTO_INCREMENT=461124 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_bacheca`
--

CREATE TABLE IF NOT EXISTS `retegas_bacheca` (
  `id_bacheca` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL DEFAULT '0',
  `visibility` int(11) NOT NULL DEFAULT '0',
  `code_uno` int(11) NOT NULL DEFAULT '0',
  `code_due` varchar(20) NOT NULL,
  `timbro_bacheca` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `titolo_messaggio` varchar(150) NOT NULL DEFAULT '',
  `messaggio` text NOT NULL,
  `scadenza` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `id_utente_destinatario` int(11) NOT NULL DEFAULT '0',
  `id_ditta` int(11) NOT NULL DEFAULT '0',
  `id_listino` int(11) NOT NULL DEFAULT '0',
  `id_articolo` int(11) NOT NULL DEFAULT '0',
  `id_ordine` int(11) NOT NULL DEFAULT '0',
  `tags` varchar(200) NOT NULL DEFAULT '',
  `id_des` int(11) NOT NULL DEFAULT '0',
  `id_gas` int(11) NOT NULL DEFAULT '0',
  `lat_bacheca` decimal(14,10) NOT NULL DEFAULT '0.0000000000',
  `lng_bacheca` decimal(14,10) NOT NULL DEFAULT '0.0000000000',
  `bacheca_address` varchar(200) NOT NULL,
  `is_hidden` tinyint(4) NOT NULL DEFAULT '0',
  `dataInserimento` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_vetrina` tinyint(4) NOT NULL DEFAULT '0',
  `code` varchar(100) NOT NULL,
  `public_url` varchar(200) NOT NULL,
  PRIMARY KEY (`id_bacheca`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1743 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_bounced`
--

CREATE TABLE IF NOT EXISTS `retegas_bounced` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `bounce_class` int(11) NOT NULL,
  `raw_rcpt_to` varchar(100) NOT NULL,
  `raw_reason` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subject` varchar(100) NOT NULL,
  `userid` int(11) NOT NULL,
  `id_gas` int(11) NOT NULL,
  `provider` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `raw_rcpt_to` (`raw_rcpt_to`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=294 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_cassa_utenti`
--

CREATE TABLE IF NOT EXISTS `retegas_cassa_utenti` (
  `id_cassa_utenti` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Indice',
  `id_utente` int(11) NOT NULL DEFAULT '0' COMMENT 'Utente al quale si riferisce il movimento',
  `id_gas` int(11) NOT NULL DEFAULT '0',
  `id_ditta` int(11) DEFAULT '0',
  `importo` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT 'Importo',
  `segno` enum('+','-') NOT NULL DEFAULT '+',
  `tipo_movimento` tinyint(4) NOT NULL DEFAULT '0',
  `escludi_gas` tinyint(1) NOT NULL DEFAULT '0',
  `descrizione_movimento` varchar(200) DEFAULT NULL,
  `note_movimento` longtext COMMENT 'Note',
  `data_movimento` datetime DEFAULT NULL COMMENT 'Data di inserimento movimento',
  `numero_documento` varchar(100) DEFAULT NULL,
  `id_ordine` int(11) NOT NULL DEFAULT '0' COMMENT 'Ordine al quale si riferisce il movimento',
  `id_cassiere` int(11) NOT NULL DEFAULT '0' COMMENT 'Cassiere che esegue il movimento',
  `registrato` enum('si','no') NOT NULL DEFAULT 'no',
  `data_registrato` datetime DEFAULT NULL,
  `contabilizzato` enum('si','no') NOT NULL DEFAULT 'no',
  `data_contabilizzato` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cassa_utenti`),
  KEY `id_utente` (`id_utente`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296330 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_des`
--

CREATE TABLE IF NOT EXISTS `retegas_des` (
  `id_des` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_referente` int(11) NOT NULL,
  `des_descrizione` varchar(200) NOT NULL,
  `des_lat` decimal(10,6) NOT NULL,
  `des_lng` decimal(10,6) NOT NULL,
  `des_note` text NOT NULL,
  `des_info` varchar(200) NOT NULL,
  `des_zoom` int(11) NOT NULL,
  `des_indirizzo` varchar(200) NOT NULL,
  PRIMARY KEY (`id_des`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_dettaglio_ordini`
--

CREATE TABLE IF NOT EXISTS `retegas_dettaglio_ordini` (
  `id_dettaglio_ordini` int(11) NOT NULL AUTO_INCREMENT,
  `id_utenti` int(11) NOT NULL DEFAULT '0',
  `id_articoli` int(11) NOT NULL DEFAULT '0',
  `id_stati` int(11) NOT NULL DEFAULT '0',
  `data_inserimento` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_convalida` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `qta_ord` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `id_amico` int(11) NOT NULL DEFAULT '0',
  `id_ordine` int(11) NOT NULL DEFAULT '0',
  `qta_conf` decimal(11,2) NOT NULL DEFAULT '0.00',
  `qta_arr` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `timestamp_ord` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prz_dett` decimal(11,4) NOT NULL DEFAULT '0.0000' COMMENT 'Prezzo articolo riferito al dettaglio',
  `prz_dett_arr` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `art_codice` varchar(50) DEFAULT NULL,
  `art_desc` varchar(100) DEFAULT NULL,
  `art_um` varchar(20) DEFAULT NULL,
  `check_code` varchar(20) DEFAULT NULL,
  `id_ditta` int(11) DEFAULT NULL,
  `descrizione_ditta` varchar(100) NOT NULL,
  `art_ingombro` varchar(50) NOT NULL,
  PRIMARY KEY (`id_dettaglio_ordini`),
  KEY `id_utenti` (`id_utenti`,`id_articoli`),
  KEY `id_articoli` (`id_articoli`),
  KEY `id_stati` (`id_stati`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Dettaglio articoli ordinati' AUTO_INCREMENT=618682 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_distribuzione_spesa`
--

CREATE TABLE IF NOT EXISTS `retegas_distribuzione_spesa` (
  `id_distribuzione` int(11) NOT NULL AUTO_INCREMENT,
  `id_riga_dettaglio_ordine` int(11) NOT NULL DEFAULT '0',
  `id_amico` int(11) NOT NULL DEFAULT '0',
  `qta_ord` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `qta_arr` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `data_ins` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_articoli` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_ordine` int(11) NOT NULL DEFAULT '0',
  `id_gas` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_distribuzione`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Distribuzione spesa tra amici' AUTO_INCREMENT=650870 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_ditte`
--

CREATE TABLE IF NOT EXISTS `retegas_ditte` (
  `id_ditte` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione_ditte` varchar(100) NOT NULL,
  `indirizzo` varchar(200) NOT NULL,
  `website` varchar(100) NOT NULL,
  `note_ditte` longtext NOT NULL,
  `id_proponente` int(11) NOT NULL DEFAULT '0',
  `mail_ditte` varchar(100) NOT NULL,
  `tag_ditte` varchar(250) NOT NULL,
  `ditte_gc_lat` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  `ditte_gc_lng` decimal(10,7) NOT NULL DEFAULT '0.0000000',
  `telefono` varchar(20) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `contatto` varchar(100) NOT NULL,
  `street` varchar(120) NOT NULL COMMENT 'Indirizzo',
  `locality` varchar(100) NOT NULL COMMENT 'Città',
  `zipCode` varchar(30) NOT NULL COMMENT 'CAP',
  `country` int(30) NOT NULL COMMENT 'STATO',
  `iban` varchar(50) NOT NULL,
  `P_IVA` varchar(50) NOT NULL,
  PRIMARY KEY (`id_ditte`),
  FULLTEXT KEY `nome_fornitore` (`descrizione_ditte`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1828 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_frasi`
--

CREATE TABLE IF NOT EXISTS `retegas_frasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_header` int(11) NOT NULL,
  `frase` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_frasi_header`
--

CREATE TABLE IF NOT EXISTS `retegas_frasi_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_gas`
--

CREATE TABLE IF NOT EXISTS `retegas_gas` (
  `id_gas` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione_gas` varchar(100) NOT NULL,
  `sede_gas` varchar(200) NOT NULL,
  `nome_gas` text NOT NULL,
  `website_gas` varchar(100) NOT NULL,
  `mail_gas` varchar(100) NOT NULL,
  `id_referente_gas` int(11) NOT NULL DEFAULT '0',
  `id_tipo_gas` int(5) NOT NULL DEFAULT '1',
  `comunicazione_referenti` varchar(200) NOT NULL,
  `maggiorazione_ordini` decimal(5,2) NOT NULL DEFAULT '0.00',
  `gas_gc_lat` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `gas_gc_lng` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `default_permission` int(11) NOT NULL DEFAULT '0',
  `possiede_cassa` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se il gas possiede la cassa',
  `gas_permission` int(11) NOT NULL DEFAULT '0',
  `id_des` int(11) NOT NULL DEFAULT '0',
  `targa_gas` varchar(10) NOT NULL,
  `iban_gas` varchar(50) NOT NULL,
  PRIMARY KEY (`id_gas`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=260 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_listini`
--

CREATE TABLE IF NOT EXISTS `retegas_listini` (
  `id_listini` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione_listini` varchar(200) NOT NULL DEFAULT '',
  `id_utenti` int(11) NOT NULL DEFAULT '0',
  `id_tipologie` int(11) NOT NULL DEFAULT '0',
  `id_ditte` int(11) NOT NULL DEFAULT '0',
  `data_valido` date NOT NULL DEFAULT '0000-00-00',
  `tipo_listino` tinyint(2) NOT NULL DEFAULT '0',
  `is_privato` tinyint(2) NOT NULL DEFAULT '0',
  `opz_usage` tinyint(4) NOT NULL DEFAULT '0',
  `is_multiditta` tinyint(4) NOT NULL DEFAULT '0',
  `note_listino` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_listini`),
  KEY `id_utenti` (`id_utenti`),
  KEY `id_tipologie` (`id_tipologie`),
  KEY `id_ditte` (`id_ditte`),
  FULLTEXT KEY `descrizione_fulltext` (`descrizione_listini`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Lista dei listini disponibili' AUTO_INCREMENT=10104 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_messaggi`
--

CREATE TABLE IF NOT EXISTS `retegas_messaggi` (
  `id_messaggio` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_ordine` int(11) NOT NULL DEFAULT '0',
  `messaggio` text NOT NULL,
  `timbro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tipo` varchar(10) NOT NULL DEFAULT '-',
  `valore` float(10,2) NOT NULL DEFAULT '0.00',
  `tipo2` varchar(10) NOT NULL DEFAULT '-',
  `query` text NOT NULL,
  PRIMARY KEY (`id_messaggio`),
  KEY `indice_ordini` (`id_ordine`),
  KEY `indice_users` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=364140 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_opinioni`
--

CREATE TABLE IF NOT EXISTS `retegas_opinioni` (
  `id_opinione` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `id_gas` int(11) NOT NULL,
  `id_des` int(11) NOT NULL,
  `id_ordine` int(11) NOT NULL,
  `id_ditta` int(11) NOT NULL,
  `chiave` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `valore_int` int(11) NOT NULL,
  `timbro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_opinione`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2390 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_options`
--

CREATE TABLE IF NOT EXISTS `retegas_options` (
  `id_option` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `chiave` varchar(50) NOT NULL,
  `valore_text` text,
  `timbro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valore_int` bigint(20) DEFAULT NULL,
  `valore_real` decimal(10,4) DEFAULT NULL,
  `note_1` text NOT NULL,
  `id_gas` int(11) NOT NULL DEFAULT '0',
  `id_ordine` int(11) NOT NULL DEFAULT '0',
  `id_ditta` int(11) NOT NULL DEFAULT '0',
  `id_listino` int(11) NOT NULL DEFAULT '0',
  `id_des` int(11) NOT NULL DEFAULT '0',
  `id_dettaglio` int(11) NOT NULL DEFAULT '0',
  `id_articolo` int(11) NOT NULL DEFAULT '0',
  `id_bacheca` int(11) NOT NULL DEFAULT '0',
  `valore_data` datetime NOT NULL,
  PRIMARY KEY (`id_option`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=219948 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_ordini`
--

CREATE TABLE IF NOT EXISTS `retegas_ordini` (
  `id_ordini` int(11) NOT NULL AUTO_INCREMENT,
  `id_listini` int(11) NOT NULL DEFAULT '0',
  `id_utente` int(11) NOT NULL DEFAULT '0',
  `descrizione_ordini` varchar(200) NOT NULL,
  `data_scadenza1` date NOT NULL DEFAULT '0000-00-00',
  `data_scadenza2` date NOT NULL DEFAULT '0000-00-00',
  `data_apertura` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_chiusura` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data_merce` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `costo_trasporto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_gestione` decimal(10,2) NOT NULL DEFAULT '0.00',
  `chiuso_ordini` tinyint(1) NOT NULL DEFAULT '0',
  `privato` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ordine riguardante i componenti dello stesso gas',
  `min_articoli` int(11) NOT NULL DEFAULT '0',
  `min_scatola` int(11) NOT NULL DEFAULT '0',
  `id_stato` int(11) NOT NULL DEFAULT '0',
  `senza_prezzo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Per ordini con solo le quantità',
  `is_printable` tinyint(1) NOT NULL DEFAULT '0',
  `mail_level` int(2) NOT NULL DEFAULT '0',
  `note_ordini` text NOT NULL,
  `solo_cassati` enum('SI','NO') NOT NULL DEFAULT 'NO' COMMENT 'Possono partecipare solo se hanno credito sufficiente',
  `calendario` int(11) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `valore_finale` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_ordini`),
  KEY `id_listini` (`id_listini`,`id_utente`),
  FULLTEXT KEY `descrizione_ordini` (`descrizione_ordini`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16333 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_postino`
--

CREATE TABLE IF NOT EXISTS `retegas_postino` (
  `id_postino` int(11) NOT NULL AUTO_INCREMENT,
  `postino_prenotazione` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subject` varchar(250) NOT NULL DEFAULT '',
  `from` varchar(250) NOT NULL DEFAULT '',
  `from_mail` varchar(250) NOT NULL DEFAULT '',
  `to` varchar(250) NOT NULL DEFAULT '',
  `to_mail` varchar(250) NOT NULL DEFAULT '',
  `cc` varchar(250) NOT NULL DEFAULT '',
  `cc_mail` varchar(250) NOT NULL DEFAULT '',
  `ccn` varchar(250) NOT NULL DEFAULT '',
  `ccn_mail` varchar(250) NOT NULL DEFAULT '',
  `plain_text` text NOT NULL,
  `html_text` text NOT NULL,
  `attach_1` varchar(250) NOT NULL DEFAULT '',
  `attach_2` varchar(250) NOT NULL DEFAULT '',
  `attempt` int(5) NOT NULL DEFAULT '0',
  `send_from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `priority` int(5) NOT NULL DEFAULT '0',
  `id_ordine` int(10) DEFAULT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `tag_1` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_postino`),
  KEY `postino_prenotazione` (`postino_prenotazione`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10490 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_referenze`
--

CREATE TABLE IF NOT EXISTS `retegas_referenze` (
  `id_referenze` int(11) NOT NULL AUTO_INCREMENT,
  `id_ordine_referenze` int(11) NOT NULL DEFAULT '0',
  `id_utente_referenze` int(11) NOT NULL DEFAULT '0',
  `id_gas_referenze` int(11) NOT NULL DEFAULT '0',
  `note_referenza` varchar(250) NOT NULL,
  `maggiorazione_referenza` decimal(11,4) NOT NULL DEFAULT '0.0000',
  `maggiorazione_percentuale_referenza` decimal(5,2) NOT NULL DEFAULT '0.00',
  `data_distribuzione_start` datetime NOT NULL,
  `data_distribuzione_end` datetime NOT NULL,
  `luogo_distribuzione` varchar(100) NOT NULL,
  `lat_distribuzione` decimal(10,6) NOT NULL,
  `lng_distribuzione` decimal(10,6) NOT NULL,
  `testo_distribuzione` text NOT NULL,
  `convalida_referenze` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_referenze`),
  KEY `id_ordine_referenze` (`id_ordine_referenze`,`id_utente_referenze`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Referenze per accorpare gli ordini dai vari GAS' AUTO_INCREMENT=27569 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_tags`
--

CREATE TABLE IF NOT EXISTS `retegas_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_tag` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=105 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_telegram`
--

CREATE TABLE IF NOT EXISTS `retegas_telegram` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `messaggio` text COLLATE utf8_unicode_ci NOT NULL,
  `inviato` datetime DEFAULT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5920 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_tipologia`
--

CREATE TABLE IF NOT EXISTS `retegas_tipologia` (
  `id_tipologia` int(11) NOT NULL AUTO_INCREMENT,
  `descrizione_tipologia` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipologia`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Tipologie di prodotti acquistabili' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `retegas_triggers`
--

CREATE TABLE IF NOT EXISTS `retegas_triggers` (
  `id_trigger` int(11) NOT NULL AUTO_INCREMENT,
  `id_owner` int(11) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` int(11) NOT NULL,
  `sottotipo` int(11) NOT NULL,
  `disattivo` tinyint(4) NOT NULL DEFAULT '0',
  `cosa` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `quando` datetime DEFAULT NULL,
  `operatore` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `valore` int(11) NOT NULL,
  `azione` int(11) NOT NULL,
  `testo_azione` text CHARACTER SET ucs2 NOT NULL,
  `ripetibile` tinyint(4) NOT NULL DEFAULT '0',
  `id_utente` int(11) DEFAULT NULL,
  `id_ordine` int(11) DEFAULT NULL,
  `id_articolo` int(11) DEFAULT NULL,
  `id_ditta` int(11) DEFAULT NULL,
  `scattato_il` datetime DEFAULT NULL,
  PRIMARY KEY (`id_trigger`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=49 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
