const mysql = require('mysql');

const express = require('express');
const app = express();


const session = require('express-session');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

require("dotenv").config();
const secretKey = process.env.SECRET_KEY;
const host = process.env.HOST;
const user = process.env.USER;
const passWord = process.env.PASSWORD;
const dataBase = process.env.DATABASE;

const path = require('path')
const cors = require('cors')

// use {} cause call it to be module not object
const { isPasswordSimilar } = require(path.join(__dirname, 'modules/checkPasswordSimilar'));

app.use(session({
    secret: secretKey,
    resave: false,
    saveUninitialized: true,
    cookie: { maxAge: 3600000 } // 1 hour in milliseconds
}));

app.listen(3000, () => console.log("Start service on port 3000"));
app.use(express.json())
app.use(express.urlencoded({ extended: true }))
app.set('views', path.join(__dirname, 'views'))
app.set('view engine', 'ejs')

var con = mysql.createConnection({
    host: host,
    user: user,
    password: passWord,
    database: dataBase,
});
con.connect(function (err) {
    if (err) throw err;
    console.log("Connected!");
});

app.use(cors())
var text = ""

app.get('/test', async (req, res) => {
    con.query('SELECT * FROM customer_account', async function (error, results, fields) {
        if (error) throw error;
        
        res.send(results) 
        return
    })

});

app.post('/_test', (req, res) => {
    const { username, password } = req.body;
    con.query("insert into users(username, password) values(?, ?)", [username, password], (error, result, fields) => {
        if (error) throw error;
        console.log('doneeeeee');
      });

  });

app.get("/register", async (req, res) => {
    res.render('register', { text: text })
})

app.post("/_register", async (req, res) => {
    const { username, password } = req.body;

    await con.query("select username from users where username = ?", [username], async function (err, result, fields) {
        if (err) {
            return res.status(401).json({ message: "can't connect db" });
        }
        if (result.length !== 0) {
            text = "Username already used"
            return res.redirect('register')
        } else {
            const salt = await bcrypt.genSalt(10);
            const hashedPassword = await bcrypt.hash(password, salt);

            con.query("insert into users(username, password) values(?, ?)", [username, hashedPassword], (error, result, fields) => {
                if (error) {
                    return res.status(401).json({ message: "Unable to complete registration" });
                }

                // return res.status(201).json({ message: "Register successfully" });
                return res.redirect('login')
            })
        }
    });
})

app.get("/login", async (req, res) => {
    res.render('login', { text: text })
    text = ""
})

app.post("/_login", async (req, res) => {
    const { username, password } = req.body;
    con.query("select username, password from customer_account where username = ?", [username], async function (err, result, fields) {
        if (err) {
            return res.status(401).json({ message: "can't connect db" });
        }
        if (result.length === 0) {
            text = "Username or Password incorrect"
            return res.redirect('login')
        }
        const passwordMatch = await bcrypt.compare(password, result[0].password);
        if (!passwordMatch) {

            // return res.status(401).json({ message: "Username or Password incorrect" });
            text = "Username or Password incorrect"
            return res.redirect('login')
        }
        else {
            const token = jwt.sign({ username: username }, secretKey, {
                expiresIn: '1h',
            });
            req.session.username = username;
            // return res.status(200).json({ token });
            // return res.status(201).json({ message: "Login successfully" });
            return res.status(202).json({ message: "login success" });
            // return res.redirect('changePassword')
        }

    });

})

app.get("/changePassword", async (req, res) => {
    // if(!req.session.username){//check session undefined
    //     res.redirect('/login')
    // }else{
        res.render('changePassword', { text: text })
        text = ""
    // }
    
})

app.post("/_changePassword", async (req, res) => {
    // const username = req.session.username;
    const username = "123456Wa";
    const { oldPassword, newPassword } = req.body;
    
    console.log("old"+oldPassword)
    console.log("new change"+newPassword)
    con.query("select username, password from users where username = ?", [username], async function (err, result, fields) {
        if (err) {
            return res.status(401).json({ message: "can't connect db" });
        }
        if (result.length === 0) { // check username is incorrect
            text = "Username or Password incorrect"
            return res.redirect('changePassword')
        }
        const passwordMatch = await bcrypt.compare(oldPassword, result[0].password);
        if (!passwordMatch) {   //check password incorrect
            text = "Password incorrect"
            return res.redirect('changePassword')
        }else if (isPasswordSimilar(newPassword, oldPassword, 5)) { // check similarity
            
            text = "New password is similar to old password"
            return res.redirect('changePassword')
        }else {//change password here
            const salt = await bcrypt.genSalt(10);
            const hashedPassword = await bcrypt.hash(newPassword, salt);
            con.query("UPDATE users SET password = ? WHERE username = ?", [hashedPassword, username], (error, result, fields) => {
                if (error) {
                    return res.status(401).json({ message: "Unable to complete registration" });
                }
                return res.status(201).json({ message: "changePassword successfully" });
            })
        }

    });

})







app.get('/protected', (req, res) => {
    const token = req.headers.authorization.split(' ')[1]

    try {
        const decoded = jwt.verify(token, secretKey)

        res.json({
            message: 'Hello! You are authorized',
            decoded,
        })
    } catch (error) {
        res.status(401).json({
            message: 'Unauthorized',
            error: error.message,
        })
    }
})
