# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.53-0ubuntu0.14.04.1)
# Database: codebasehq-telegram-bot
# Generation Time: 2017-02-28 12:04:03 PM +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table codebasehq_assignments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `codebasehq_assignments`;

CREATE TABLE `codebasehq_assignments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codebasehq_project_id` int(11) unsigned NOT NULL,
  `codebasehq_user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assignment` (`codebasehq_project_id`,`codebasehq_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table codebasehq_projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `codebasehq_projects`;

CREATE TABLE `codebasehq_projects` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `permalink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table codebasehq_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `codebasehq_users`;

CREATE TABLE `codebasehq_users` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table codebasehq_users_projects_events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `codebasehq_users_projects_events`;

CREATE TABLE `codebasehq_users_projects_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codebasehq_user_id` int(11) NOT NULL,
  `codebasehq_project_id` int(11) DEFAULT NULL,
  `codebasehq_event_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table telegram_chats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `telegram_chats`;

CREATE TABLE `telegram_chats` (
  `id` int(11) unsigned NOT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) DEFAULT NULL COMMENT 'delete it',
  `telegram_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table telegram_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `telegram_users`;

CREATE TABLE `telegram_users` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(255) DEFAULT '',
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `codebasehq_user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
