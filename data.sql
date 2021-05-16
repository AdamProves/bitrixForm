CREATE DATABASE `abiturients` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `abiturients`;

CREATE TABLE `baccalaureate`
(
    `id`         INT      NOT NULL AUTO_INCREMENT,
    `iin`        BIGINT(12)  unique not null,
    `first_name` varchar(30) not null,
    `last_name`  varchar(30) not null,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

ALTER TABLE `baccalaureate`
    ADD INDEX abiturinet_iin_ix_inn (`iin`),
    LOCK = NONE;


CREATE USER 'abiturient_adm'@'%' IDENTIFIED BY '17O4zJn6BbhqgV';
GRANT SELECT, INSERT, UPDATE ON `abiturients`.* TO 'abiturient_adm'@'%';

INSERT INTO `abiturients`.`baccalaureate` (iin, first_name, last_name)
VALUES (?, ?, ?);