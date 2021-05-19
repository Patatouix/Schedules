# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `product_schedule`;
DROP TABLE IF EXISTS `content_schedule`;
DROP TABLE IF EXISTS `store_schedule`;
DROP TABLE IF EXISTS `cart_item_schedule`;
DROP TABLE IF EXISTS `order_product_schedule`;
DROP TABLE IF EXISTS `schedule`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;