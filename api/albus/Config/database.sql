# SQL table that is microblog example runs on

CREATE TABLE posts (
	`id` INT(11) AUTO_INCREMENT,
	`author` VARCHAR(30) NOT NULL,
	`content` VARCHAR(255) NOT NULL,
	`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
);