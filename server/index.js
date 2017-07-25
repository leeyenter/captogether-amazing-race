var app = require("express")();
var http = require( "http" ).createServer( app );
var io = require( "socket.io" )( http );
http.listen(2000, "127.0.0.1");

io.on('connection',function(socket){  
  console.log("A user is connected");

  socket.on("update-board", function(data) {
    io.emit("update-board", data);
    console.log("update board");
  })

  socket.on("update-cards", function(data) {
    io.emit("update-cards", data);
    console.log("update cards");
  })

  socket.on("add-cards", function(data, fn) {
    io.emit("update-cards", data);
    console.log("update cards");
    fn({msg: "success"});
  })

  socket.on("update-both-callback", function(data, fn) {
    let parsed = JSON.parse(data);
    io.emit("update-board", parsed.board);
    io.emit("update-cards", parsed.group);
    console.log("Updated both cards & board");
    fn({msg: "success"});
  })

  socket.on("update-group-list", function(data, fn) {
    io.emit("update-group-list", 0);
    fn({msg: "success"});
  })

});