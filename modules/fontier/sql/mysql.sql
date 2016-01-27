
CREATE TABLE `fontier_callbacks` (
  `when` int(12) NOT NULL,
  `uri` varchar(250) NOT NULL DEFAULT '',
  `timeout` int(4) NOT NULL DEFAULT '0',
  `connection` int(4) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  `queries` mediumtext NOT NULL,
  `fails` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`when`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_elements` (
  `id` mediumint(18) NOT NULL AUTO_INCREMENT,
  `theme-id` int(11) NOT NULL DEFAULT '0',
  `fonting-id` mediumint(20) NOT NULL DEFAULT '0',
  `source` enum('theming','html') NOT NULL DEFAULT 'html',
  `element-id` mediumint(26) NOT NULL DEFAULT '0',
  `important` enum('yes','no') NOT NULL DEFAULT 'yes',
  `uid` int(13) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `deleted` int(12) NOT NULL DEFAULT '0',
  `hits` mediumint(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_emails` (
  `id` varchar(32) NOT NULL,
  `emails` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonting` (
  `id` mediumint(20) NOT NULL,
  `mode` enum('nodes','id') NOT NULL DEFAULT 'id',
  `nodes-string` tinytext NOT NULL,
  `font-ids` tinytext NOT NULL,
  `random` enum('yes','no') NOT NULL DEFAULT 'no',
  `current-font-id` varchar(32) NOT NULL DEFAULT '',
  `last-font-id` varchar(32) NOT NULL DEFAULT '',
  `created` int(12) NOT NULL DEFAULT '0',
  `change` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `deleted` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonts` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `peer_id` varchar(32) DEFAULT '',
  `names` int(6) DEFAULT '0',
  `nodes` int(8) DEFAULT '0',
  `created` int(12) DEFAULT '0',
  `accessed` int(12) DEFAULT '0',
  `cached` int(12) DEFAULT '0',
  `failed` int(12) DEFAULT '0',
  `failures` mediumint(20) DEFAULT '0',
  `downloaded` mediumint(20) DEFAULT '0',
  `hits` mediumint(20) DEFAULT '0',
  `normal` enum('yes','no') DEFAULT 'no',
  `italic` enum('yes','no') DEFAULT 'no',
  `bold` enum('yes','no') DEFAULT 'no',
  `wide` enum('yes','no') DEFAULT 'no',
  `condensed` enum('yes','no') DEFAULT 'no',
  `light` enum('yes','no') DEFAULT 'no',
  `semi` enum('yes','no') DEFAULT 'no',
  `book` enum('yes','no') DEFAULT 'no',
  `body` enum('yes','no') DEFAULT 'no',
  `header` enum('yes','no') DEFAULT 'no',
  `heading` enum('yes','no') DEFAULT 'no',
  `footer` enum('yes','no') DEFAULT 'no',
  `graphic` enum('yes','no') DEFAULT 'no',
  `system` enum('yes','no') DEFAULT 'no',
  `quote` enum('yes','no') DEFAULT 'no',
  `block` enum('yes','no') DEFAULT 'no',
  `message` enum('yes','no') DEFAULT 'no',
  `admin` enum('yes','no') DEFAULT 'no',
  `logo` enum('yes','no') DEFAULT 'no',
  `slogon` enum('yes','no') DEFAULT 'no',
  `legal` enum('yes','no') DEFAULT 'no',
  `script` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`names`,`nodes`,`hits`,`failed`,`failures`,`cached`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonts_archiving` (
  `id` mediumint(24) NOT NULL AUTO_INCREMENT,
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `repository` varchar(300) NOT NULL DEFAULT '',
  `files` int(10) NOT NULL DEFAULT '0',
  `bytes` int(18) NOT NULL DEFAULT '0',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `hits` int(24) NOT NULL DEFAULT '0',
  `packing` enum('7z','zip','rar','rar5','zoo','tar.gz','store') NOT NULL DEFAULT 'zip',
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`font_id`(17),`fingerprint`(14),`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonts_callbacks` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `type` enum('upload','archive','fonthit') NOT NULL DEFAULT 'upload',
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `upload_id` int(18) NOT NULL DEFAULT '0',
  `uri` varchar(350) NOT NULL DEFAULT 'http://',
  `email` varchar(198) NOT NULL DEFAULT '',
  `last` int(13) NOT NULL DEFAULT '0',
  `calls` int(20) NOT NULL DEFAULT '0',
  `fails` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`font_id`(12),`upload_id`),
  KEY `SEARCH` (`font_id`(12),`upload_id`,`uri`(12),`last`,`calls`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonts_files` (
  `id` mediumint(24) NOT NULL AUTO_INCREMENT,
  `font_id` varchar(32) NOT NULL DEFAULT '',
  `archive_id` mediumint(24) NOT NULL DEFAULT '0',
  `type` enum('json','diz','pfa','pfb','pt3','t42','sfd','ttf','bdf','otf','otb','cff','cef','gai','woff','svg','ufo','pf3','ttc','gsf','cid','bin','hqx','dfont','mf','ik','fon','fnt','pcf','pmf','pdb','eot','afm','data','css','other') NOT NULL DEFAULT 'other',
  `extension` varchar(12) NOT NULL DEFAULT '',
  `filename` varchar(128) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `bytes` int(12) NOT NULL DEFAULT '0',
  `hits` int(20) NOT NULL DEFAULT '0',
  `created` int(13) NOT NULL DEFAULT '0',
  `accessed` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `SEARCH` (`font_id`(14),`archive_id`,`type`,`extension`,`filename`(12),`path`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_fonts_names` (
  `font_id` varchar(32) DEFAULT '',
  `upload_id` int(18) DEFAULT '0',
  `name` varchar(64) DEFAULT '',
  `longitude` float(12,8) DEFAULT '0.00000000',
  `latitude` float(12,8) DEFAULT '0.00000000',
  `country` varchar(3) DEFAULT 'USA',
  `region` varchar(64) DEFAULT '',
  `city` varchar(64) DEFAULT '',
  KEY `POINTING` (`upload_id`,`font_id`(14),`name`(12)),
  KEY `LOCALITY` (`longitude`,`latitude`,`country`(2),`region`(10),`city`(10),`font_id`(13),`upload_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_html_elements` (
  `id` mediumint(26) NOT NULL AUTO_INCREMENT,
  `position` int(14) NOT NULL DEFAULT '0',
  `value` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

INSERT INTO `fontier_html_elements` VALUES (1,1,'a'),(2,2,'abbr'),(3,3,'acronym'),(4,4,'address'),(5,5,'applet'),(6,6,'area'),(7,7,'b'),(8,8,'base'),(9,9,'basefont'),(10,10,'bdo'),(11,11,'big'),(12,12,'blockquote'),(13,13,'body'),(14,14,'br'),(15,15,'button'),(16,16,'caption'),(17,17,'center'),(18,18,'cite'),(19,19,'code'),(20,20,'col'),(21,21,'colgroup'),(22,22,'dd'),(23,23,'del'),(24,24,'dfn'),(25,25,'dir'),(26,26,'div'),(27,27,'dl'),(28,28,'dt'),(29,29,'em'),(30,30,'fieldset'),(31,31,'font'),(32,32,'form'),(33,33,'frame'),(34,34,'frameset'),(35,35,'h1'),(36,36,'h2'),(37,37,'h3'),(38,38,'h4'),(39,39,'h5'),(40,40,'h6'),(41,41,'head'),(42,42,'hr'),(43,43,'html'),(44,44,'i'),(45,45,'iframe'),(46,46,'img'),(47,47,'input'),(48,48,'ins'),(49,49,'isindex'),(50,50,'kbd'),(51,51,'label'),(52,52,'legend'),(53,53,'li'),(54,54,'link'),(55,55,'map'),(56,56,'menu'),(57,57,'meta'),(58,58,'noframes'),(59,59,'noscript'),(60,60,'object'),(61,61,'ol'),(62,62,'optgroup'),(63,63,'option'),(64,64,'p'),(65,65,'param'),(66,66,'pre'),(67,67,'q'),(68,68,'s'),(69,69,'samp'),(70,70,'script'),(71,71,'select'),(72,72,'small'),(73,73,'span'),(74,74,'strike'),(75,75,'strong'),(76,76,'style'),(77,77,'sub'),(78,78,'sup'),(79,79,'table'),(80,80,'tbody'),(81,81,'td'),(82,82,'textarea'),(83,83,'tfoot'),(84,84,'th'),(85,85,'thead'),(86,86,'title'),(87,87,'tr'),(88,88,'tt'),(89,89,'u'),(90,90,'ul'),(91,91,'var');


CREATE TABLE `fontier_networking` (
  `ip_id` varchar(32) NOT NULL DEFAULT '',
  `type` enum('ipv4','ipv6') NOT NULL DEFAULT 'ipv4',
  `ipaddy` varchar(64) NOT NULL DEFAULT '',
  `netbios` varchar(198) NOT NULL DEFAULT '',
  `domain` varchar(128) NOT NULL DEFAULT '',
  `country` varchar(3) NOT NULL DEFAULT '',
  `region` varchar(128) NOT NULL DEFAULT '',
  `city` varchar(128) NOT NULL DEFAULT '',
  `postcode` varchar(15) NOT NULL DEFAULT '',
  `timezone` varchar(10) NOT NULL DEFAULT '',
  `longitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `latitude` float(12,8) NOT NULL DEFAULT '0.00000000',
  `contributes` int(16) NOT NULL DEFAULT '0',
  `downloads` int(16) NOT NULL DEFAULT '0',
  `uploads` int(16) NOT NULL DEFAULT '0',
  `fonts` int(16) NOT NULL DEFAULT '0',
  `surveys` int(16) NOT NULL DEFAULT '0',
  `created` int(13) NOT NULL DEFAULT '0',
  `last` int(13) NOT NULL DEFAULT '0',
  `data` mediumtext,
  `whois` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`ip_id`,`type`,`ipaddy`(15)),
  KEY `SEARCH` (`type`,`ipaddy`(15),`netbios`(12),`domain`(12),`country`(2),`city`(12),`region`(12),`postcode`(6),`longitude`,`latitude`,`created`,`last`,`timezone`(6))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_nodes` (
  `id` int(23) NOT NULL AUTO_INCREMENT,
  `type` enum('typal','fixes','keys') DEFAULT NULL,
  `node` varchar(64) DEFAULT '0',
  `usage` int(12) DEFAULT '0',
  `weight` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `PINGERING` (`node`(21),`type`,`usage`,`weight`)
) ENGINE=InnoDB AUTO_INCREMENT=388 DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_nodes_linking` (
  `font_id` varchar(32) DEFAULT NULL,
  `node_id` int(23) DEFAULT '0',
  KEY `PINGERING` (`node_id`,`font_id`(11))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_theming` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(45) NOT NULL DEFAULT 'default',
  `elements` int(8) NOT NULL DEFAULT '0',
  `scanned` int(12) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL,
  `last` int(12) NOT NULL DEFAULT '0',
  `deleted` int(12) NOT NULL DEFAULT '0',
  `hits-admin` mediumint(32) NOT NULL DEFAULT '0',
  `hits-user` mediumint(32) NOT NULL DEFAULT '0',
  `hits-guest` mediumint(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_theming_css` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `theme-id` int(11) NOT NULL DEFAULT '0',
  `path` varchar(200) NOT NULL DEFAULT '',
  `file` varchar(200) NOT NULL DEFAULT '',
  `classes` int(14) NOT NULL DEFAULT '0',
  `identities` int(14) NOT NULL DEFAULT '0',
  `elements` int(14) NOT NULL DEFAULT '0',
  `scanned` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_theming_elements` (
  `id` mediumint(26) NOT NULL AUTO_INCREMENT,
  `theme-id` int(11) NOT NULL DEFAULT '0',
  `theme-css-id` int(18) NOT NULL,
  `type` enum('identity','class','element','unknown') NOT NULL DEFAULT 'unknown',
  `position` int(14) NOT NULL DEFAULT '0',
  `value` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_uploads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `session` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `organisation` varchar(200) NOT NULL DEFAULT '',
  `accepted` int(16) NOT NULL DEFAULT '0',
  `rejected` int(16) NOT NULL DEFAULT '0',
  `responses` int(16) NOT NULL DEFAULT '0',
  `uid` int(13) NOT NULL DEFAULT '0',
  `contacted` int(12) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `finished` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_uploads_answers` (
  `id` mediumint(28) NOT NULL AUTO_INCREMENT,
  `upload-id` int(10) NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(198) NOT NULL DEFAULT '',
  `name` varchar(198) NOT NULL DEFAULT '',
  `expires` int(12) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_uploads_files` (
  `id` mediumint(14) NOT NULL AUTO_INCREMENT,
  `upload-id` int(10) NOT NULL,
  `state` enum('ignored','uploaded') NOT NULL DEFAULT 'ignored',
  `file-md5` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(200) NOT NULL DEFAULT '',
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_uploads_surveys` (
  `id` mediumint(28) NOT NULL AUTO_INCREMENT,
  `upload-id` int(10) NOT NULL DEFAULT '0',
  `key` varchar(32) NOT NULL DEFAULT '',
  `fingerprint` varchar(32) NOT NULL DEFAULT '',
  `emails-id` varchar(32) NOT NULL DEFAULT '',
  `scope` varchar(200) NOT NULL DEFAULT '',
  `expires` int(12) NOT NULL DEFAULT '0',
  `subject` varchar(160) NOT NULL DEFAULT '',
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `fontier_whois` (
  `id` varchar(32) NOT NULL,
  `whois` mediumtext NOT NULL,
  `created` int(12) NOT NULL DEFAULT '0',
  `last` int(12) NOT NULL DEFAULT '0',
  `instances` mediumint(18) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

