drop table goods;
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

drop table categories;
CREATE TABLE `categories` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`timestap` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`parent_id` bigint(20) NOT NULL,
	`title` varchar(128) NOT NULL,
	`supply_id` bigint(20) NOT NULL,
	`external_id` bigint(20) NULL,
	PRIMARY KEY (`id`)
);

drop TABLE `goods_categories`;
CREATE TABLE `goods_categories` (
	`id` bigint NOT NULL,
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`good_id` bigint(20) NOT NULL,
	`category_id` bigint(20) NOT NULL,
	PRIMARY KEY (`id`)
);

drop TABLE `uploads`;
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


ALTER TABLE `goods` ADD INDEX(`supply_id`);
ALTER TABLE `goods` ADD INDEX(`brand_id`);
ALTER TABLE `goods` ADD CONSTRAINT `goods_fk0` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);
ALTER TABLE `goods` ADD CONSTRAINT `goods_fk1` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`);


ALTER TABLE `goods` ADD INDEX(`supply_id`);
ALTER TABLE `goods` ADD INDEX(`parent_id`);
ALTER TABLE `goods` ADD INDEX(`external_id`,'supply_id');
ALTER TABLE `categories` ADD CONSTRAINT `categories_fk0` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`);
ALTER TABLE `categories` ADD CONSTRAINT `categories_fk1` FOREIGN KEY (`supply_id`) REFERENCES `suppliers`(`id`);

ALTER TABLE `goods_categories` ADD CONSTRAINT `goods_categories_fk0` FOREIGN KEY (`good_id`) REFERENCES `goods`(`id`);

ALTER TABLE `goods_categories` ADD CONSTRAINT `goods_categories_fk1` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`);

ALTER TABLE uploads
  ADD CONSTRAINT uploads_fk1 FOREIGN KEY (good_id) REFERENCES goods (id),
  ADD CONSTRAINT uploads_fk2 FOREIGN KEY (transaction_id) REFERENCES upload_transactions (id);

ALTER TABLE `upload_transactions`
    ADD CONSTRAINT `upload_transactions_fk01` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`),
    ADD CONSTRAINT `upload_transactions_fk02` FOREIGN KEY (`supply_id`) REFERENCES `suppliers` (`id`),
    ADD CONSTRAINT `upload_transactions_fk03` FOREIGN KEY (`status_id`) REFERENCES `upload_statuses` (`id`),
    ADD CONSTRAINT `upload_transactions_fk04` FOREIGN KEY (`error_id`) REFERENCES `errors` (`id`);
