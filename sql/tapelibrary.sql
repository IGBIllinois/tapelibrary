CREATE TABLE `backupset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT 'untitled',
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `program` varchar(50) DEFAULT NULL,
  `notes` longtext,
  `active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `container_type` (
  `container_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `container` tinyint(4) DEFAULT NULL,
  `can_contain_types` varchar(256) DEFAULT NULL,
  `max_slots` int(11) DEFAULT '-1',
  PRIMARY KEY (`container_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `version` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `tape_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) DEFAULT NULL,
  `tape_label` varchar(50) DEFAULT NULL,
  `container` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `backupset` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `last_update_username` varchar(50) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

