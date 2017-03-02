CREATE TABLE `goods` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`sid` varchar(12) NOT NULL UNIQUE,
	`supply_id` bigint(20) NOT NULL,
	`brand_id` bigint(20),
	`title` varchar(400) NOT NULL,
	`sku` varchar(15) NOT NULL,
	`description` TEXT(500),
	`certificate` TEXT(200),
	`barcode` varchar(11),
	`image` varchar(128),
	`unit` varchar(20),
	`weight` DECIMAL(3,3),
	`depth` DECIMAL(3,2),
	`width` DECIMAL(3,2),
	`height` DECIMAL(3,2),
	`pack` int(6),
	PRIMARY KEY (`id`)
);

CREATE TABLE `suppliers` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`title` varchar(256) NOT NULL,
	`code` varchar(15) NOT NULL,
	`inn` varchar(12) NOT NULL UNIQUE,
	`link` varchar(256),
	PRIMARY KEY (`id`)
);

CREATE TABLE `brands` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`title` varchar(128) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `categories` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestap` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`parent_id` bigint(20) NOT NULL,
	`title` varchar(128) NOT NULL,
	`supply_id` bigint(20) NOT NULL,
	`external_id` bigint(20) NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `goods_categories` (
	`id` bigint NOT NULL,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`good_id` bigint(20) NOT NULL,
	`category_id` bigint(20) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `uploads` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`supply_id` bigint(20) NOT NULL,
	`good_id` bigint(20) NOT NULL,
	`count` int(10) NOT NULL,
	`amount` DECIMAL(10,2) NOT NULL,
	`schedule_id` bigint(20) NOT NULL,
	`time_start` TIMESTAMP,
	`time_end` TIMESTAMP,
	`status` bigint(20) NOT NULL,
	`error_code` bigint(20) NOT NULL DEFAULT '0',
	`error_msg` TEXT,
	PRIMARY KEY (`id`)
);

CREATE TABLE `protocols` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`title` varchar(128) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

CREATE TABLE `schedules` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`protocol_id` bigint(20) NOT NULL,
	`supply_id` bigint(20) NOT NULL,
	`period` int(5) NOT NULL,
	`last` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
);

CREATE TABLE `upload_statuses` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`title` varchar(128) NOT NULL UNIQUE,
	PRIMARY KEY (`id`)
);

ALTER TABLE `goods` ADD CONSTRAINT `goods_fk0` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);

ALTER TABLE `goods` ADD CONSTRAINT `goods_fk1` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`);

ALTER TABLE `categories` ADD CONSTRAINT `categories_fk0` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`);

ALTER TABLE `categories` ADD CONSTRAINT `categories_fk1` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);

ALTER TABLE `goods_categories` ADD CONSTRAINT `goods_categories_fk0` FOREIGN KEY (`good_id`) REFERENCES `goods`(`id`);

ALTER TABLE `goods_categories` ADD CONSTRAINT `goods_categories_fk1` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`);

ALTER TABLE `uploads` ADD CONSTRAINT `uploads_fk0` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);

ALTER TABLE `uploads` ADD CONSTRAINT `uploads_fk1` FOREIGN KEY (`good_id`) REFERENCES `goods`(`id`);

ALTER TABLE `uploads` ADD CONSTRAINT `uploads_fk2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules`(`id`);

ALTER TABLE `uploads` ADD CONSTRAINT `uploads_fk3` FOREIGN KEY (`status`) REFERENCES `upload_statuses`(`id`);

ALTER TABLE `schedules` ADD CONSTRAINT `schedules_fk0` FOREIGN KEY (`protocol_id`) REFERENCES `protocols`(`id`);

ALTER TABLE `schedules` ADD CONSTRAINT `schedules_fk1` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);
