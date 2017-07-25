<?php
session_start();
require "../sql_login.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>CAPTogether - Admin</title>
        <link rel="stylesheet" href="https://bootswatch.com/cosmo/bootstrap.css">
        <link rel="stylesheet" href="main.css?v=1.2">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1">
        <meta name="theme-color" content="#ffffff">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script> 
    </head>
    <body>
        <script>
            var groups = {};
        </script>
        <div class='container'>
            <div class='row'>
            <h1><strong>CAPT</strong>ogether &ndash; Admin Page</h1>
            <h2>Groups</h2>
            <div id='groups'></div>
            <?php 
            if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == "yes_logged_in") {
                # logged in
                
                echo "<h2>Station Masters</h2>";
                $stmt = $db->prepare("SELECT id, name, hash FROM foc_stations");
                $stmt->execute();
                $stmt->bind_result($id, $name, $hash);
                echo "<table class='table table-hover table-bordered'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Hash</th>
                    </tr>
                </thead>
                <tbody>";
                while ($stmt->fetch()) {
                    echo "<tr>
                        <td style='text-align:right;'>$id</td>
                        <td>$name</td>
                        <td><code>$hash</code></td>
                    </tr>";
                }
                echo "</tbody></table>
                <h2>Card Types</h2>";
                $stmt = $db->prepare("SELECT id, filename FROM foc_card_types");
                $stmt->execute();
                $stmt->bind_result($id, $filename);
                while ($stmt->fetch()) {
                    echo "<div class='panel panel-default'><div class='panel-body'>(#$id) <img src='../imgs/$filename'></div></div>";
                }
                echo "</div>
                <div class='row'>
                    <div class='col-md-6'>
                        <h2>Board 1</h2>
                        <div id='board1'></div>
                    </div>
                    <div class='col-md-6'>
                        <h2>Board 2</h2>
                        <div id='board2'></div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <h2>Cards Placed (Board 1)</h2>
                        <div id='placed_logs1'></div>
                    </div>
                    <div class='col-md-6'>
                        <h2>Cards Placed (Board 2)</h2>
                        <div id='placed_logs2'></div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <h2>Cards Given (Board 1)</h2>
                        <div id='given_logs1'></div>
                    </div>
                    <div class='col-md-6'>
                        <h2>Cards Given (Board 2)</h2>
                        <div id='given_logs2'></div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>
                        <h2>Groups Closed (Board 1)</h2>
                        <div id='closed_logs1'></div>
                    </div>
                    <div class='col-md-6'>
                        <h2>Groups Closed (Board 2)</h2>
                        <div id='closed_logs2'></div>
                    </div>
                </div>

                ";
            } else if (isset($_POST["pw"])) {
                # login attempt
                if (password_verify($_POST["pw"], '$2y$10$U7VCXdNE3PmsAYbxWbQG8.Mf4IlBIaPlY0HBLi2E6eZ5Xec9/g00G')) {
                    $_SESSION["logged_in"] = "yes_logged_in";
                    header("Location: index.php");
                }
            } else {
                # display log in
                ?>
                Please log in!
                <form method='post'>
                    <input type='password' name='pw' class='form-control'>
                    <input type='submit' class='form-control btn btn-primary'>
                </form>
                <?php
            }
            ?>
        </div>
        <?php if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == "yes_logged_in") {
            ?>
        <script>
            var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });

            socket.on("update-board", function(board) {
                updateMap(board);
                getPlacedLogs(board);
            });

            socket.on("update-cards", function(group) {
                updateMap(groups[group]);
                getPlacedLogs(groups[group]);
                getGivenLogs(groups[group]);
            })

            socket.on("update-group-list", function(data) {
                updateGroups();
                getClosedLogs(1);
                getClosedLogs(2);
            })
                    
            function updateMap(board_num) {
                // Draw a map
                $.ajax({
                    method: "GET",
                    url: "../draw_map.php",
                    data: {
                        rotated: "false", 
                        curr_mode: "none", 
                        board: board_num
                    }
                })
                .done(function(map) {
                    console.log("Done");
                $("#board"+board_num).html(map);
                })
            }
            
            function getPlacedLogs(board_num) {
                // Draw a map
                $.ajax({
                    method: "GET",
                    url: "../show_logs_placed.php",
                    data: {
                        rotated: "false", 
                        curr_mode: "none", 
                        board: board_num
                    }
                })
                .done(function(map) {
                    console.log("Done");
                $("#placed_logs"+board_num).html(map);
                })
            }

            function getGivenLogs(board_num) {
                $.ajax({
                    method: "GET",
                    url: "show_logs_given.php",
                    data: {
                        board: board_num
                    }
                })
                .done(function(map) {
                    console.log("Done");
                    $("#given_logs"+board_num).html(map);
                })
            }

            function getClosedLogs(board_num) {
                $.ajax({
                    method: "GET",
                    url: "show_logs_closed.php",
                    data: {
                        board: board_num
                    }
                })
                .done(function(map) {
                    console.log("Done");
                    $("#closed_logs"+board_num).html(map);
                })
            }

            function updateGroups() {
                $.ajax({
                    method: "GET",
                    url: "get_groups.php"
                })
                .done(function(data) {
                    $("#groups").html(data);
                })
            }
            
            updateMap(1);
            updateMap(2);
            getPlacedLogs(1);
            getPlacedLogs(2);
            getGivenLogs(1);
            getGivenLogs(2);
            getClosedLogs(1);
            getClosedLogs(2);

            updateGroups();
        <?php } ?>
        </script>
    </body>
</html>