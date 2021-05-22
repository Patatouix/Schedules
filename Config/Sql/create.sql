
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `schedule`;

CREATE TABLE `schedule`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `day` TINYINT,
    `begin` TIME,
    `end` TIME,
    `closed` TINYINT(1),
    `period_begin` DATE,
    `period_end` DATE,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product_schedule`;

CREATE TABLE `product_schedule`
(
    `schedule_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `stock` INTEGER,
    PRIMARY KEY (`schedule_id`),
    INDEX `fi_product_id` (`product_id`),
    CONSTRAINT `fk_product_schedule_id`
        FOREIGN KEY (`schedule_id`)
        REFERENCES `schedule` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- content_schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `content_schedule`;

CREATE TABLE `content_schedule`
(
    `schedule_id` INTEGER NOT NULL,
    `content_id` INTEGER NOT NULL,
    PRIMARY KEY (`schedule_id`),
    INDEX `fi_content_id` (`content_id`),
    CONSTRAINT `fk_content_schedule_id`
        FOREIGN KEY (`schedule_id`)
        REFERENCES `schedule` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_content_id`
        FOREIGN KEY (`content_id`)
        REFERENCES `content` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- store_schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `store_schedule`;

CREATE TABLE `store_schedule`
(
    `schedule_id` INTEGER NOT NULL,
    PRIMARY KEY (`schedule_id`),
    CONSTRAINT `fk_store_schedule_id`
        FOREIGN KEY (`schedule_id`)
        REFERENCES `schedule` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- cart_item_schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `cart_item_schedule`;

CREATE TABLE `cart_item_schedule`
(
    `schedule_id` INTEGER NOT NULL,
    `cart_item_id` INTEGER NOT NULL,
    PRIMARY KEY (`schedule_id`),
    INDEX `fi_cart_item_id` (`cart_item_id`),
    CONSTRAINT `fk_cart_item_schedule_id`
        FOREIGN KEY (`schedule_id`)
        REFERENCES `schedule` (`id`),
    CONSTRAINT `fk_cart_item_id`
        FOREIGN KEY (`cart_item_id`)
        REFERENCES `cart_item` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_product_schedule
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_product_schedule`;

CREATE TABLE `order_product_schedule`
(
    `schedule_id` INTEGER NOT NULL,
    `order_product_id` INTEGER NOT NULL,
    PRIMARY KEY (`schedule_id`),
    INDEX `fi_order_product_id` (`order_product_id`),
    CONSTRAINT `fk_order_product_schedule_id`
        FOREIGN KEY (`schedule_id`)
        REFERENCES `schedule` (`id`),
    CONSTRAINT `fk_order_product_id`
        FOREIGN KEY (`order_product_id`)
        REFERENCES `order_product` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
