-- Exportiere Struktur von Tabelle newsletter_impl.newsletter
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `preview_text` varchar(80) DEFAULT NULL,
  `n2n_locale` varchar(12) DEFAULT NULL,
  `sent` tinyint(3) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `last_mod` datetime DEFAULT NULL,
  `last_mod_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_blacklisted
CREATE TABLE IF NOT EXISTS `newsletter_blacklisted` (
  `email` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_ci
CREATE TABLE IF NOT EXISTS `newsletter_ci` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_index` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_history
CREATE TABLE IF NOT EXISTS `newsletter_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsletter_id` int(10) unsigned DEFAULT NULL,
  `prepared_date` datetime DEFAULT NULL,
  `newsletter_html` text,
  `newsletter_text` text,
  PRIMARY KEY (`id`),
  KEY `newsletter_history_index_1` (`newsletter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_history_entry
CREATE TABLE IF NOT EXISTS `newsletter_history_entry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('prepared','in-progress','sent','read','error') DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `sent_date` datetime DEFAULT NULL,
  `history_id` int(10) unsigned DEFAULT NULL,
  `salutation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `newsletter_history_entry_index_1` (`history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_history_link
CREATE TABLE IF NOT EXISTS `newsletter_history_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_id` int(10) unsigned DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `newsletter_ci_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `newsletter_history_link_index_1` (`history_id`),
  KEY `newsletter_history_link_index_2` (`newsletter_ci_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_history_link_click
CREATE TABLE IF NOT EXISTS `newsletter_history_link_click` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_entry_id` int(10) unsigned DEFAULT NULL,
  `history_link_id` int(10) unsigned DEFAULT NULL,
  `recipient_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `newsletter_history_link_click_index_1` (`history_entry_id`),
  KEY `newsletter_history_link_click_index_2` (`history_link_id`),
  KEY `newsletter_history_link_click_index_3` (`recipient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_newsletter_cis
CREATE TABLE IF NOT EXISTS `newsletter_newsletter_cis` (
  `newsletter_id` int(10) unsigned NOT NULL,
  `newsletter_ci_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`newsletter_id`,`newsletter_ci_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_recipient
CREATE TABLE IF NOT EXISTS `newsletter_recipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `salute_with` varchar(255) DEFAULT NULL,
  `confirmation_code` varchar(255) DEFAULT NULL,
  `n2n_locale` varchar(12) DEFAULT NULL,
  `last_mod` datetime DEFAULT NULL,
  `last_mod_by` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_recipient_categories
CREATE TABLE IF NOT EXISTS `newsletter_recipient_categories` (
  `newsletter_id` int(10) unsigned NOT NULL,
  `recipient_category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`newsletter_id`,`recipient_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_recipient_category
CREATE TABLE IF NOT EXISTS `newsletter_recipient_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `last_mod` datetime DEFAULT NULL,
  `last_mod_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `newsletter_recipient_recipient_categories` (
	`recipient_id` INT UNSIGNED NOT NULL,
	`recipient_category_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`recipient_id`, `recipient_category_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

-- Daten Export vom Benutzer nicht ausgewählt
-- Exportiere Struktur von Tabelle newsletter_impl.newsletter_recipient_history_entry_clicks
CREATE TABLE IF NOT EXISTS `newsletter_recipient_history_entry_clicks` (
  `recipient_id` int(10) unsigned NOT NULL,
  `history_link_click_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`recipient_id`,`history_link_click_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `newsletter_history_link`
	CHANGE COLUMN `link` `link` TEXT NULL DEFAULT NULL AFTER `history_id`;