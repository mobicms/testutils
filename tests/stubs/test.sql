DROP TABLE IF EXISTS `test`;

CREATE TABLE IF NOT EXISTS `test`
(
    `id`   int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255)     NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE = MyISAM;

INSERT INTO `test` (`id`, `name`)
VALUES (1, 'foo'),
       (2, 'bar'),
       (3, 'baz');
