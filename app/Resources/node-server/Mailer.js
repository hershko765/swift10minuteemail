/**
 * Mail Mailer Components
 *
 * @type {exports.MailParser|*}
 */
var MailParser = require("mailparser").MailParser,
    fs = require('fs'),
    request = require('request');

/**
 *
 * @constructor
 */
var Mailer = function() {
    var _this = this;
    this._checking = false;
    this.options = {
        mailPath: '/home/swiftmail/mail/swift10minutemail.com/swiftmail/new/',
        apiURL: 'http://swift10minutemail.com/api/v1/emails/log'
    };

    setInterval(function(){
        if ( ! _this._checking) _this.check();
    }, 500);

};


Mailer.prototype.check = function() {
    var _this = this;
    this._checking = true;
    fs.readdir(this.options.mailPath, function(err, files){
        if (files.length == 0) {
            console.log('No files found, aborting');
            _this._checking = false;
            return;
        }
        console.log('Found ' + files.length + ' Files, Syncing');

        var filesLeft = files.length;
        if ( ! err) {
            files.forEach(function(val, key, arr){
                fs.readFile(_this.options.mailPath + val, 'utf8', function(err, file){
                    fs.unlink(_this.options.mailPath + val, function(err){
                        filesLeft -= 1;
                        _this.parse(file);
                        if (filesLeft == 0) _this._checking = false;
                    });
                })
            });
        }
    })
};

Mailer.prototype.parse = function(email) {
    var _this = this,
        mailParser = new MailParser();

    mailParser.write(email);
    mailParser.on("end", function(mail_object){
        request.post({
            headers: {'content-type' : 'application/x-www-form-urlencoded'},
            url:     _this.options.apiURL,
            form:    mail_object
        }, function(error, response, body){
            var bodyObj = JSON.parse(body);
            _this.callback(bodyObj.visitorId, bodyObj);
        });
    });

    mailParser.end();
};

Mailer.prototype.onEmailReady = function(callback) {
    this.callback = callback;
};

exports.Mailer = Mailer;