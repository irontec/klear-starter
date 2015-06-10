CREATE TABLE changelog (
    change_number BIGINT NOT NULL,
    delta_set VARCHAR(10) NOT NULL,
    start_dt TIMESTAMP NOT NULL,
    complete_dt TIMESTAMP NULL,
    applied_by VARCHAR(100) NOT NULL,
    description VARCHAR(500) NOT NULL
) COMMENT '[ignore]';

ALTER TABLE changelog ADD CONSTRAINT Pkchangelog PRIMARY KEY (change_number, delta_set);

CREATE TABLE `EtagVersions` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `table` varchar(55) DEFAULT NULL,
    `etag` varchar(255),
    `lastChange` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
