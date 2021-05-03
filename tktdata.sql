-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 06. Apr 2021 um 22:48
-- Server-Version: 10.4.13-MariaDB
-- PHP-Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tktdata`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_livedata_archive`
--

CREATE TABLE `tktdata_livedata_archive` (
  `id` int(11) NOT NULL,
  `archive_timestamp` datetime DEFAULT NULL,
  `liveAction` tinyint(1) DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_livedata_live`
--

CREATE TABLE `tktdata_livedata_live` (
  `id` int(11) NOT NULL,
  `liveAction` tinyint(1) DEFAULT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_menu`
--

CREATE TABLE `tktdata_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `submenu` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `layout` int(11) DEFAULT NULL,
  `plugin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `tktdata_menu`
--

INSERT INTO `tktdata_menu` (`id`, `name`, `submenu`, `image`, `layout`, `plugin`) VALUES
(1, 'Ticket', 0, NULL, 1, NULL),
(2, 'Coupons', 0, NULL, 2, NULL),
(3, 'Scanner', 0, NULL, 3, NULL),
(4, 'Live', 0, NULL, 4, NULL),
(5, 'Benutzer', 0, NULL, 5, NULL),
(6, 'Alle Tickets', 1, 'ticket.svg', 1, NULL),
(7, 'Gruppen', 1, 'group.svg', 2, NULL),
(8, 'Alle Coupons', 2, 'coupon.svg', 1, NULL),
(9, 'Informationen', 3, 'info.svg', 1, NULL),
(10, 'QR-Scanner', 3, 'qr.svg', 2, NULL),
(11, 'Code-Scanner', 3, 'code.svg', 3, NULL),
(12, 'Manuell', 4, 'livedata_manually.svg', 3, NULL),
(13, 'Live', 4, 'livedata_live.svg', 1, NULL),
(14, 'Archiv', 4, 'livedata_archiv.svg', 2, NULL),
(15, 'Alle Benutzer', 5, 'user.svg', 1, NULL),
(16, 'Aktivitäten', 5, 'activites.svg', 2, NULL),
(65, 'Testpage', 0, NULL, 6, 'autogenerate-tickets'),
(66, 'sub', 65, NULL, 6, 'autogenerate-tickets'),
(67, 'sub 2', 65, NULL, 6, 'autogenerate-tickets');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_tickets`
--

CREATE TABLE `tktdata_tickets` (
  `ticketKey` varchar(255) NOT NULL,
  `groupID` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `payment` int(11) DEFAULT NULL,
  `payrexx_gateway` int(11) DEFAULT NULL,
  `payrexx_transaction` int(11) DEFAULT NULL,
  `purchase_time` datetime DEFAULT NULL,
  `payment_time` datetime DEFAULT NULL,
  `employ_time` datetime DEFAULT NULL,
  `coupon` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `custom` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_tickets_coupons`
--

CREATE TABLE `tktdata_tickets_coupons` (
  `couponID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `groupID` int(11) NOT NULL,
  `used` int(11) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT NULL,
  `discount_absolute` int(11) DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_tickets_groups`
--

CREATE TABLE `tktdata_tickets_groups` (
  `groupID` int(11) NOT NULL,
  `maxTickets` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `tpu` int(11) DEFAULT NULL,
  `mail_from` varchar(255) DEFAULT NULL,
  `mail_displayName` varchar(255) DEFAULT NULL,
  `mail_subject` varchar(255) DEFAULT NULL,
  `mail_msg` text DEFAULT NULL,
  `payment_mail_msg` text DEFAULT NULL,
  `payment_store` int(11) DEFAULT NULL,
  `adfs` int(11) DEFAULT NULL,
  `adfs_custom` text DEFAULT NULL,
  `payment_payrexx_instance` varchar(255) DEFAULT NULL,
  `payment_payrexx_secret` varchar(255) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `custom` text DEFAULT NULL,
  `sdk_secret_key` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_user`
--

CREATE TABLE `tktdata_user` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_user_actions`
--

CREATE TABLE `tktdata_user_actions` (
  `id` int(11) NOT NULL,
  `userID` varchar(255) DEFAULT NULL,
  `print_message` text DEFAULT NULL,
  `affected_table` varchar(255) DEFAULT NULL,
  `id_cell` varchar(255) DEFAULT NULL,
  `sql_modification` varchar(255) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `modification_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tktdata_user_rights`
--

CREATE TABLE `tktdata_user_rights` (
  `id` int(11) NOT NULL,
  `userId` varchar(255) DEFAULT NULL,
  `page` int(11) DEFAULT NULL,
  `r` tinyint(1) DEFAULT NULL,
  `w` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `tktdata_user_rights`
--

INSERT INTO `tktdata_user_rights` (`id`, `userId`, `page`, `r`, `w`) VALUES
(1, 'admin', 6, 1, 1),
(2, 'admin', 7, 1, 1),
(3, 'admin', 8, 1, 1),
(4, 'admin', 9, 1, 1),
(5, 'admin', 10, 1, 1),
(6, 'admin', 11, 1, 1),
(7, 'admin', 12, 1, 1),
(8, 'admin', 13, 1, 1),
(9, 'admin', 14, 1, 1),
(10, 'admin', 15, 1, 1),
(11, 'admin', 16, 1, 1);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tktdata_livedata_archive`
--
ALTER TABLE `tktdata_livedata_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tktdata_livedata_live`
--
ALTER TABLE `tktdata_livedata_live`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tktdata_menu`
--
ALTER TABLE `tktdata_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tktdata_tickets`
--
ALTER TABLE `tktdata_tickets`
  ADD PRIMARY KEY (`ticketKey`);

--
-- Indizes für die Tabelle `tktdata_tickets_coupons`
--
ALTER TABLE `tktdata_tickets_coupons`
  ADD PRIMARY KEY (`couponID`),
  ADD UNIQUE KEY `name` (`name`,`groupID`);

--
-- Indizes für die Tabelle `tktdata_tickets_groups`
--
ALTER TABLE `tktdata_tickets_groups`
  ADD PRIMARY KEY (`groupID`);

--
-- Indizes für die Tabelle `tktdata_user`
--
ALTER TABLE `tktdata_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `tktdata_user_actions`
--
ALTER TABLE `tktdata_user_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `tktdata_user_rights`
--
ALTER TABLE `tktdata_user_rights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId` (`userId`,`page`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tktdata_livedata_archive`
--
ALTER TABLE `tktdata_livedata_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tktdata_livedata_live`
--
ALTER TABLE `tktdata_livedata_live`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tktdata_menu`
--
ALTER TABLE `tktdata_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT für Tabelle `tktdata_tickets_coupons`
--
ALTER TABLE `tktdata_tickets_coupons`
  MODIFY `couponID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tktdata_tickets_groups`
--
ALTER TABLE `tktdata_tickets_groups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tktdata_user_actions`
--
ALTER TABLE `tktdata_user_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `tktdata_user_rights`
--
ALTER TABLE `tktdata_user_rights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
