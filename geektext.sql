
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
	subtitle VARCHAR(60), author VARCHAR(60) NOT NULL, publisher VARCHAR(60),
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

	
	
	
	
	
	
	
						