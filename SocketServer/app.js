
/**
 * Module dependencies.
 */

var express = require('express');
var routes = require('./routes');
var http = require('http');
var path = require('path');
var webSocketServer = require('websocket').server;

var app = express();

// all environments
app.set('port', process.env.PORT || 9090);
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.json());
app.use(express.urlencoded());
app.use(express.methodOverride());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

var server = http.createServer(app);

/* WebSocket server */
var wsServer = new webSocketServer({
  // WebSocket server is tied to a HTTP server. WebSocket request is just
  // an enhanced HTTP request. For more info http://tools.ietf.org/html/rfc6455#page-6
  httpServer: server
});
var clients = [];

wsServer.on('request', function(request) {
  console.log((new Date()) + ' Connection from origin ' + request.origin + '.');
  var connection = request.accept(null, request.origin); 
  var index = clients.push(connection) - 1;

  // user disconnected
  connection.on('close', function(connection) {
    console.log((new Date()) + " Peer " + connection + " disconnected.");
    clients.splice(index, 1);
  });
});

app.get('/', routes.index);
app.post('/', function(req, res) {
  var data = req.body['data'];
  if (!!data) {
    for (var i in clients) {
      clients[i].sendUTF(data);
    }
  }
  res.send("");
});

server.listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});
