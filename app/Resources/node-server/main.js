var fs = require('fs'),
    http = require('http'),
    socketio = require('socket.io'),
    Mailer = require('./Mailer.js').Mailer,
    mysql = require("mysql");

var server = http.createServer(function(req, res) {
    res.end();

}).listen(8080, function() {
    console.log('Listening at: http://localhost:8080');
});
var users = {};

var mailer = new Mailer();
mailer.onEmailReady(function(id, data){
    if (users[id]) {
        users[id].forEach(function(userSocket){
            userSocket.emit('email:received', id, data);
        });
    }
});

socketio.listen(server).on('connection', function (socket) {
    socket.on('add:visitor', function(id){
        users[id] = users[id] || [];
        users[id].push(socket);


        socket.on('disconnect', function() {
            // Disconnect event
        });

    });
});
