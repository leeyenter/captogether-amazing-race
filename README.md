# captogether-amazing-race
An online Saboteur web app that allows groups to play remotely and in real time.

This project utilises PHP, JavaScript, [Bootstrap](www.getboostrap.com) and [socket.io](https://socket.io/). 
* **PHP** is used to interact with the MySQL database, and stores what cards are in play, as well as
create a log of everything that's going on. 
* **socket.io** is used to update the other clients whenever a card is awarded or used.
* **JavaScript** (and [jQuery](https://jquery.com/)) is used to update the respective segments
of the UI whenever new data is coming in. 

*Image assets were retrieved from http://imgur.com/gallery/gio5M*. 

TO-DO:
* Add schema for MySQL database
* Clean up code