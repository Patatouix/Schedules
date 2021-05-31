# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `product_schedule`;
DROP TABLE IF EXISTS `content_schedule`;
DROP TABLE IF EXISTS `store_schedule`;
DROP TABLE IF EXISTS `schedule`;

DROP TABLE IF EXISTS `cart_item_schedule_date`;
DROP TABLE IF EXISTS `order_product_schedule_date`;
DROP TABLE IF EXISTS `schedule_date`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;