const express = require('express');
const app = express();
var request = require('request');
const mysql = require('mysql');


// to remove the need to type .ejs all the time
app.set("view engine", "ejs");



/* command to refresh the server automatically
 * nodemon
 */


const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',               // change
    password: '123456',         // change password to your database
    database: 'geektext_db'     // modify as needed
});


// To connect to the SQL
db.connect((err) => {
    if (err) {
        throw err;
    }
    console.log('MySql Connected...');
})


/////------- routes -------------//////////

app.get("/", function (req, res) {
    // to link/send a a page in html, should be inside the views folder
    res.render("home");
});


app.get("/search", function (req, res) {
    res.render("search");
});

app.get("/cart", function (req, res) {
    res.render("cart");
});


var data;
app.get("/results", function (req, res) {
    var query = req.query.search;
    var url = "https://www.googleapis.com/books/v1/volumes?q=intitle=" + query;         // using google's book api

    //var url = "http://www.omdbapi.com/?s=" + query + "&apikey=thewdb";
    request(url, function (error, response, body) {
        if (!error && response.statusCode == 200) {
            data = JSON.parse(body)
            //res.send(data["Search"][0]["Title"]);
            res.render("results", { data: data });
        }
    });
});


// to create the table for the book, go to /createbookstable directory
app.get('/createbookstable', (req, res) => {
    let sql = 'CREATE TABLE books(id int AUTO_INCREMENT, title VARCHAR(255), ' +
        'subtitle VARCHAR(255), author VARCHAR(255), publisher VARCHAR(255), ' +
        'published_date VARCHAR(255), ISBN_13 VARCHAR(255), ISBN_10 VARCHAR(255), ' +
        'page_count VARCHAR(255), print_type VARCHAR(255), category VARCHAR(255), ' +
        'avg_rating FLOAT, rating_count VARCHAR(255), language VARCHAR(255), ' +
        'thumnail VARCHAR(255), description VARCHAR(1500), PRIMARY KEY (id))';
    db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Books table created...');
        }
    });
});


app.get("/add", function (req, res) {
    var test = req.query.id;
    var record = {};
    var counter = 0;
    data["items"].forEach(function (book) {
        counter++;
        if (counter == test) {
            let isbn13 = '';
            let isbn10 = '';
            let j = 0;

            if (book["volumeInfo"]["industryIdentifiers"]) {
                book["volumeInfo"]["industryIdentifiers"].forEach(function (book2) {
                    if (j == 0) {
                        isbn13 = book2["identifier"];
                        j++;
                    } else {
                        isbn10 = book2["identifier"];
                    }
                    isbn13 = book2["identifier"];
                })
            }


            record = {
                title: book["volumeInfo"]["title"],
                subtitle: book["volumeInfo"]["subtitle"],
                author: book["volumeInfo"]["authors"][0] + ', ' + book["volumeInfo"]["authors"][1],
                publisher: book["volumeInfo"]["publisher"],
                published_date: book["volumeInfo"]["publishedDate"],
                ISBN_13: isbn13,
                ISBN_10: isbn10,
                page_count: book["volumeInfo"]["pageCount"],
                print_type: book["volumeInfo"]["printType"],
                category: book["volumeInfo"]["categories"][0],
                avg_rating: book["volumeInfo"]["averageRating"],
                rating_count: book["volumeInfo"]["ratingsCount"],
                language: book["volumeInfo"]["language"],
                thumnail: book["volumeInfo"]["imageLinks"]["thumbnail"],
                description: book["volumeInfo"]["description"]
            }
        }
    })
    let sql = 'INSERT INTO books SET ?';
    let query = db.query(sql, record, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.render('add', { data: record });
        }
    });

    console.log(record);
    //res.render('add', {data: record});
});




// SQL queriees (notes)
/*
Create database
app.get('/createdb', (req, res) => {
    let sql = 'CREATE DATABASE geektext_db';
    db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            cosole.log(result);
            res.send('Database created...');
        }
    })
})



// create a table
app.get('/createpoststable', (req, res) => {
    let sql = 'CREATE TABLE posts(id int AUTO_INCREMENT, title VARCHAR(255), body VARCHAR(255), PRIMARY KEY (id))';
    db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Post table created...');
        }
    });
});


// insert post 1
app.get('/addpost1', (req, res) => {
    let post = {title:'Post one', body:'This is post number 1'};
    let sql = 'INSERT INTO posts SET ?';
    let query = db.query(sql, post, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Post 1 added...');
        }
    });
});


// insert post 2
app.get('/addpost2', (req, res) => {
    let post = { title: 'Post two', body: 'This is post number 2' };
    let sql = 'INSERT INTO posts SET ?';
    let query = db.query(sql, post, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Post 2 added...');
        }
    });
});


// select posts
app.get('/getposts', (req, res) => {
    let sql = 'SELECT * FROM posts';
    let query = db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Posts fetched');
        }
    });
});


// select single post
app.get('/getpost/:id', (req, res) => {
    let sql = `SELECT * FROM posts WHERE id = ${req.params.id}`;        // need to use ` (left to #1, instead of ')
    let query = db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Posts fetched...');
        }
    });
});


// update post
app.get('/updatepost/:id', (req, res) => {
    let newTitle = 'Updated Title';
    let sql = `UPDATE posts SET title = '${newTitle}' WHERE id = ${req.params.id}`;        // need to use ` (left to #1, instead of ')
    let query = db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Posts updated...');
        }
    });
});


// delete post
app.get('/deletepost/:id', (req, res) => {
    let newTitle = 'Updated Title';
    let sql = `DELETE FROM posts WHERE id = ${req.params.id}`;        // need to use ` (left to #1, instead of ')
    let query = db.query(sql, (err, result) => {
        if (err) {
            throw err;
        } else {
            console.log(result);
            res.send('Posts deleted...');
        }
    });
});

*/


app.listen('3000', () => {
    console.log('Server started on port 3000');
});
