DROP TABLE IF EXISTS `changelog`;
CREATE TABLE changelog (
    change_number BIGINT NOT NULL,
    delta_set VARCHAR(10) NOT NULL,
    start_dt TIMESTAMP NOT NULL,
    complete_dt TIMESTAMP NULL,
    applied_by VARCHAR(100) NOT NULL,
    description VARCHAR(500) NOT NULL
) COMMENT '[ignore]';

ALTER TABLE changelog ADD CONSTRAINT Pkchangelog PRIMARY KEY (change_number, delta_set);

LOCK TABLES `changelog` WRITE;
INSERT INTO `changelog` VALUES (1, 'Main', now(), now(), 'dbdeploy', '001-init.sql');
UNLOCK TABLES;
