
-- GeekText Project MySQL Database Schema


-- create user's table
CREATE TABLE user (user_id int AUTO_INCREMENT, f_name VARCHAR(255) NOT NULL, 
	l_name VARCHAR(255), nickname VARCHAR(255), email VARCHAR(255) NOT NULL, 
	password VARCHAR(255) NOT NULL, token VARCHAR(32),
	PRIMARY KEY (user_id), UNIQUE (email));


-- create address' table					
CREATE TABLE address (address_id int AUTO_INCREMENT, user_id int NOT NULL, 
	name VARCHAR(100) NOT NULL, street_address VARCHAR(100) NOT NULL,
	state VARCHAR(40) NOT NULL, city VARCHAR(40) NOT NULL, 
	zip_code VARCHAR(10) NOT NULL, country VARCHAR(40) NOT NULL, 
	primary_phone VARCHAR(20) NOT NULL, secondary_phone VARCHAR(20),
	PRIMARY KEY (address_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE);
	

-- create credit card's table
CREATE TABLE credit_card (card_id int AUTO_INCREMENT, user_id int NOT NULL,
	type VARCHAR(20) NOT NULL, number VARCHAR(20) NOT NULL, exp_month int NOT NULL,
	exp_year int NOT NULL, security_code int NOT NULL, nickname VARCHAR(20),
	cardholder VARCHAR(100) NOT NULL, 
	PRIMARY KEY (card_id), 
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE);
	
-- create book's table
CREATE TABLE book (book_id int AUTO_INCREMENT, title VARCHAR(60) NOT NULL,
	subtitle VARCHAR(60), authors VARCHAR(60) NOT NULL, publisher VARCHAR(60),
	published_date VARCHAR(20), isbn_13 VARCHAR(20), isbn_10 VARCHAR(20),
	page_count VARCHAR(10), print_type VARCHAR(20), category VARCHAR(20),
	average_rating VARCHAR(20), rating_count VARCHAR(20), language VARCHAR(20),
	image_url VARCHAR(255), description VARCHAR(1500), qty int, price float, sales int,
	PRIMARY KEY (book_id));
	

-- create shopping cart's table
CREATE TABLE cart (cart_id int AUTO_INCREMENT, user_id int NOT NULL,
	book_id int NOT NULL, qty int NOT NULL, saved_for_later tinyint(1),
	price float,
	PRIMARY KEY (cart_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
	FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE);

	

-- *****Note: You MUST modify these procedures with your database credentials (our username: cen_team8) before running these queries.

-- procedure to add an address
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `addAddress`(IN `token` VARCHAR(60), IN `name` VARCHAR(60), 
	IN `street` VARCHAR(60), IN `state` VARCHAR(60), IN `city` VARCHAR(60), IN `zip` VARCHAR(60), IN `country` VARCHAR(60))
	INSERT INTO address (name, street_address, state, city, zip_code, country, primary_phone, user_id) VALUES 
	(name, street, state, city, zip, country, '3055555555', 
	(SELECT user_id 
				FROM user 
				WHERE user.token = token))$$
DELIMITER ;


-- procedure to add book to the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `addToCart`(IN `token` VARCHAR(60), IN `book_id` INT, IN `qty` INT)
	BEGIN
		IF book_id IN (SELECT book_id
					FROM cart
					WHERE cart.book_id = book_id
					AND user_id IN (SELECT user_id
									FROM user
									WHERE user.token = token))
			THEN UPDATE cart SET cart.qty = cart.qty + qty, saved_for_later = 0
						WHERE cart.book_id = book_id 
						AND user_id IN (SELECT user_id 
										FROM user 
										WHERE user.token = token);
		ELSE INSERT INTO cart (book_id, qty, saved_for_later, user_id, price) 
			VALUES (book_id, qty, 0, (SELECT user.user_id 
										FROM user 
										WHERE user.token = token), 
												(SELECT book.price
												FROM book
												WHERE book.book_id = book_id));
		
		END IF;
	END$$
DELIMITER ;


-- to add a credit card to the user's account
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `addCard`(IN `token` VARCHAR(60), IN `holder` VARCHAR(100), 
	IN `type` VARCHAR(20), IN `number` VARCHAR(16), IN `exp` INT(11), IN `year` INT(11), IN `code` INT(11))
	INSERT INTO credit_card (cardholder, type, number, exp_month, exp_year, security_code, user_id) VALUES 
	(holder, type, number, exp, year, code, (SELECT user_id 
				FROM user 
				WHERE user.token = token))$$
DELIMITER ;


-- changes quanty of books in the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `changeQty`(IN `token` VARCHAR(60), IN `id` INT, IN `quantity` INT)
	UPDATE cart SET qty = quantity 
	WHERE book_id = id
	AND user_id IN (SELECT user_id 
					FROM user 
					WHERE user.token = token)$$
DELIMITER ;


-- deletes a book from the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `deleteFromCart`(IN `token` VARCHAR(60), IN `book_id` INT)
	DELETE FROM cart 
	WHERE cart.book_id = book_id
	AND user_id IN (SELECT user_id 
					FROM user 
					WHERE user.token = token)$$
DELIMITER ;


-- removes all books from the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `emptyCart`(IN `token` VARCHAR(60))
	DELETE FROM cart
	WHERE saved_for_later = 0
	AND user_id IN (SELECT user_id 
					FROM user 
					WHERE user.token = token)$$
DELIMITER ;


-- returns all records/books in the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `getCart`(IN `token` VARCHAR(60))
	SELECT title, authors, image_url, cart.qty, cart.price, cart.book_id, saved_for_later
	FROM cart, book 
	WHERE book.book_id = cart.book_id
	AND cart.user_id IN (SELECT user_id 
						FROM user
						WHERE user.token = token)$$
DELIMITER ;


-- returns all books/records in the saved for later list
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `getSavedForLater`(IN `token` VARCHAR(60))
	SELECT title, author, image_url, cart.qty, cart.price, cart.book_id, saved_for_later
	FROM cart, book 
	WHERE book.book_id = cart.book_id
	AND saved_for_later = 1
	AND cart.user_id IN (SELECT user_id 
						FROM user
						WHERE user.token = token)$$
DELIMITER ;


-- to get a subtotal of the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `getCartSubtotal`(IN `token` VARCHAR(60))
	SELECT SUM(qty) as NumItems, SUM(qty * price) as Subtotal
	FROM cart
	WHERE saved_for_later = 0
	AND cart.user_id IN (SELECT user_id 
						FROM user
						WHERE user.token = token)$$
DELIMITER ;


-- get number of books in the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `getCartQty`(IN `token` VARCHAR(60))
	SELECT SUM(qty) as number_of_items
	FROM cart
	WHERE saved_for_later = 0
	AND cart.user_id IN (SELECT user_id 
						FROM user
						WHERE user.token = token)$$
DELIMITER ;


-- moves book from saved fro later list to the shopping cart
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `moveToCart`(IN `token` VARCHAR(60), IN `book_id` INT)
	UPDATE cart 
	SET saved_for_later = 0, qty = 1
	WHERE cart.book_id = book_id
	AND user_id IN (SELECT user_id 
					FROM user 
					WHERE user.token = token)$$
DELIMITER ;


-- moves books from the shopping cart to the saved for later list
DELIMITER $$
	CREATE DEFINER=`cen_team8`@`localhost` PROCEDURE `saveForLater`(IN `token` VARCHAR(60), IN `book_id` INT)
	UPDATE cart 
	SET saved_for_later = 1, qty = 0
	WHERE cart.book_id = book_id
	AND user_id IN (SELECT user_id 
					FROM user 
					WHERE user.token = token)$$
DELIMITER ;
	
	
	
						