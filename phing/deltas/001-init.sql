DROP TABLE IF EXISTS `KlearRoles`;

CREATE TABLE `KlearRoles` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL DEFAULT '',
    `identifier` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='[entity]';

LOCK TABLES `KlearRoles` WRITE;
INSERT INTO `KlearRoles` VALUES (1,'Administrador','Usuario con permisos de administrador','admin'),(2,'Usuario','Con roles para un usuario normal con restricciones.','user');
UNLOCK TABLES;

DROP TABLE IF EXISTS `KlearSections`;
CREATE TABLE `KlearSections` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL DEFAULT '',
    `identifier` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='[entity]';

DROP TABLE IF EXISTS `KlearRolesSections`;

CREATE TABLE `KlearRolesSections` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `klearRoleId` mediumint(8) unsigned NOT NULL,
    `klearSectionId` mediumint(8) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `klearRoleId` (`klearRoleId`),
    KEY `klearSectionId` (`klearSectionId`),
    CONSTRAINT `KlearRolesSections_ibfk_1` FOREIGN KEY (`klearRoleId`) REFERENCES `KlearRoles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `KlearRolesSections_ibfk_2` FOREIGN KEY (`klearSectionId`) REFERENCES `KlearSections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='[entity]';

DROP TABLE IF EXISTS `KlearUsers`;
CREATE TABLE `KlearUsers` (
    `userId` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `login` varchar(40) NOT NULL,
    `fullName` varchar(255) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `pass` varchar(80) NOT NULL COMMENT '[password]',
    `active` tinyint(1) DEFAULT '1',
    `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`userId`),
    UNIQUE KEY `login` (`login`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='[entity]';
INSERT INTO KlearUsers (login, pass, active, fullName) VALUES ('admin','$2a$08$0hHHBX8So9JhU0a0SNnRCeAZcMEdAfn7T/pl/u/pESzBwztldhRnO', 1, 'Administrador');

DROP TABLE IF EXISTS `KlearUsersRoles`;
CREATE TABLE `KlearUsersRoles` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `klearUserId` mediumint(8) unsigned NOT NULL,
    `klearRoleId` mediumint(8) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `klearUserId` (`klearUserId`),
    KEY `klearRoleId` (`klearRoleId`),
    CONSTRAINT `KlearUsersRoles_ibfk_1` FOREIGN KEY (`klearUserId`) REFERENCES `KlearUsers` (`userId`) ON DELETE CASCADE,
    CONSTRAINT `KlearUsersRoles_ibfk_2` FOREIGN KEY (`klearRoleId`) REFERENCES `KlearRoles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='[entity]';

LOCK TABLES `KlearUsersRoles` WRITE;
INSERT INTO `KlearUsersRoles` VALUES (1,1,1);
UNLOCK TABLES;

LOCK TABLES `KlearSections` WRITE;
INSERT INTO `KlearSections` VALUES (1, "KlearRolesList", "KlearRolesList", "KlearRolesList");
INSERT INTO `KlearSections` VALUES (2, "KlearSectionsList", "KlearSectionsList", "KlearSectionsList");
INSERT INTO `KlearSections` VALUES (3, "KlearUsersList", "KlearUsersList", "KlearUsersList");
UNLOCK TABLES;

LOCK TABLES `KlearRolesSections` WRITE;
INSERT INTO `KlearRolesSections` VALUES (1, 1, 1);
INSERT INTO `KlearRolesSections` VALUES (2, 1, 2);
INSERT INTO `KlearRolesSections` VALUES (3, 1, 3);
UNLOCK TABLES;

CREATE TABLE `EtagVersions` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `table` varchar(55) DEFAULT NULL,
    `etag` varchar(255),
    `lastChange` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
