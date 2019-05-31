const express = require('express');
const mysql = require('mysql');


const app = express();



// to remove the need to type .ejs all the time
app.set("view engine", "ejs");

app.get("/", function (req, res) {
    // to link/send a a page in html, should be inside the views folder
    res.render("home");
});

app.get("/cart", function (req, res) {
    res.render("cart");
});


/* command to refresh the server automatically
 * nodemon
 */

/*

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '123456',
    database: 'geektext_db'
});


// To connect to the SQL
db.connect((err) => {
    if (err) {
        throw err;
    }
    console.log('MySql Connected...');
})


const app = express();


// Create database
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
