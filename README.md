# CAPTogether &ndash; Amazing Race
*An online Saboteur web app that allows groups to play remotely and in real time. 
I did this web-app for my hostel's orientation game, which involved groups playing
an Amazing Race style game as they collected cards to play Saboteur.*

## Software Used

This project utilises PHP, JavaScript, [Bootstrap](www.getboostrap.com) and [socket.io](https://socket.io/). 
* **PHP** is used to interact with the MySQL database, and stores what cards are in play, as well as
create a log of everything that's going on. 
* **socket.io** is used to update the other clients whenever a card is awarded or used.
* **JavaScript** (and [jQuery](https://jquery.com/)) is used to update the respective segments
of the UI whenever new data is coming in. 

*Image assets were retrieved from http://imgur.com/gallery/gio5M*. 

## Roles Included

There are 3 different UIs available, that fulfill different roles. The game was structured
such that there were 2 concurrent boards, each board having several groups playing, and 
also dedicated Game Masters to control the giving out of cards. 

* **Normal user**: UI found in the root folder
    * Able to see their respective boards
    * Able to play cards on the board or on other players on that board
    * Able to see a log of cards played for that board
* **Game Master**: UI found in the `/gm` folder
    * Able to see how many cards each group has
    * Able to give out cards to the groups
* **Admin**: UI found in the `/admin` folder
    * Able to see status of all groups
    * Able to see both boards
    * Able to see logs, including which groups played which cards,
    as well as what cards each group was given during the course
    of the game. 

## Features

* Users and game masters log in using unique hashes (designed not to be incredibly secure but increase ease of short-term use)
* Admin log in using password "`dragon4`"
* Live updates on all 3 UIs whenever cards are given, used or discarded
* Log of all actions done, viewable by users (for cards played) and the admin
* Automatic highlighting of possible places to put cards, and automatic flipping of goal cards when a path is built

## Gameplay

* Users must keep the number of cards in their hand between 3 to 4 cards, unless they have requested for the GM to 'close' their group
* After their group is closed, they are also unable to receive any more cards
* Blocking another group requires that group to discard a card instead of playing it
* They can also opt to discard cards without playing them