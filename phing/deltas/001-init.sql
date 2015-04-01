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
) ENGINE=InnoDB CHARSET=utf8 COMMENT='[entity]';

DROP TABLE IF EXISTS `KlearSections`;
CREATE TABLE `KlearSections` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL DEFAULT '',
    `identifier` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `identifier` (`identifier`)
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='[entity]';
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

CREATE TABLE changelog (
    change_number BIGINT NOT NULL,
    delta_set VARCHAR(10) NOT NULL,
    start_dt TIMESTAMP NOT NULL,
    complete_dt TIMESTAMP NULL,
    applied_by VARCHAR(100) NOT NULL,
    description VARCHAR(500) NOT NULL
) COMMENT '[ignore]';

LOCK TABLES `changelog` WRITE;
INSERT INTO `changelog` VALUES (1, 'Main', now(), now(), 'dbdeploy', '001-init.sql');
UNLOCK TABLES;
