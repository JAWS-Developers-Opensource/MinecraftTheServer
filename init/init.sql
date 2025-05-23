CREATE TABLE `log`(
    `action_id` VARCHAR(128) NOT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), `user_id` INT(11) NOT NULL, `action` VARCHAR(100) NOT NULL, `other_data` TEXT NULL DEFAULT 'DEFAULT NULL', `ip` VARCHAR(16) NOT NULL, `affected_association` INT(11) NOT NULL, PRIMARY KEY(`action_id`));
CREATE TABLE `service`(
    `id_name` VARCHAR(10) NOT NULL,
    `status` TINYINT(4) NOT NULL,
    `last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY(`id_name`));
CREATE TABLE `session`(
    `token` VARCHAR(128) NOT NULL,
    `session_id` VARCHAR(128) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `type` VARCHAR(6) NOT NULL,
    `expire` DATETIME NOT NULL,
    `ip` VARCHAR(15) NOT NULL
);
CREATE TABLE `user`(
    `id` INT(11) NOT NULL,
    `name` VARCHAR(75) NOT NULL,
    `surname` VARCHAR(75) NOT NULL,
    `username` VARCHAR(75) NOT NULL,
    `password` VARCHAR(114) NOT NULL,
    `status` TINYINT(4) NOT NULL DEFAULT '1',
    `creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), `last_login` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY(`id`));
CREATE TABLE `mc_server`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` LINESTRING NOT NULL
);
CREATE TABLE `mc_server_role`(
    `sever_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT NOT NULL,
    `permission` BIGINT NOT NULL,
    PRIMARY KEY(`user_id`)
);
ALTER TABLE
    `mc_server_role` ADD CONSTRAINT `mc_server_role_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `user`(`id`);
ALTER TABLE
    `session` ADD CONSTRAINT `session_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `user`(`id`);
ALTER TABLE
    `mc_server_role` ADD CONSTRAINT `mc_server_role_sever_id_foreign` FOREIGN KEY(`sever_id`) REFERENCES `mc_server`(`id`);