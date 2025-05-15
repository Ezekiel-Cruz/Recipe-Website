-- Update categories in an existing database
USE `recipe_website`;

-- First, make sure any recipes using categories that will be removed are assigned to NULL
UPDATE `recipes` SET `category_id` = NULL WHERE `category_id` NOT IN (
    SELECT `id` FROM `categories` WHERE `name` IN (
        'Breakfast', 'Lunch', 'Dinner', 'Snacks', 'Dessert', 
        'Appetizer', 'Side Dish', 'Beverage / Drinks', 'Soup', 'Salad'
    )
);

-- Delete all existing categories
DELETE FROM `categories`;

-- Reset auto-increment
ALTER TABLE `categories` AUTO_INCREMENT = 1;

-- Insert the new categories
INSERT INTO `categories` (`name`) VALUES
('Breakfast'),
('Lunch'),
('Dinner'),
('Snacks'),
('Dessert'),
('Appetizer'),
('Side Dish'),
('Beverage / Drinks'),
('Soup'),
('Salad');
