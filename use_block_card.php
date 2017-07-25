<?php
session_start();
date_default_timezone_set('Asia/Singapore');
require "sql_login.php";
$from_group_id = $_SESSION["group_id"];
$to_group_id = $_POST["block_group"];

// Check if the user has the card
$stmt = $db->prepare("SELECT id FROM foc_cards_on_hand WHERE group_id = ? AND card_type_id = 2 LIMIT 1");
$stmt->bind_param('i', $from_group_id);
$stmt->execute();
$stmt->bind_result($card_id);
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // No card found
    $msg = "card_not_found";
} else {
    // Card was found
    // Use it
    $stmt = $db->prepare("UPDATE foc_groups SET blocked = blocked + 1 WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $to_group_id);
    $stmt->execute();

    // Remove the card
    $stmt = $db->prepare("DELETE FROM foc_cards_on_hand WHERE group_id = ? AND card_type_id = 2 LIMIT 1");
    $stmt->bind_param('i', $from_group_id);
    $stmt->execute();

    // Add into the log
    $stmt = $db->prepare("INSERT INTO foc_log_cards_placed (group_id, card_type_id, row) VALUES (?, 2, ?)");
    $stmt->bind_param('ii', $from_group_id, $to_group_id);
    $stmt->execute();

    $msg = "ok";
}

// Get the hash, for redirect
$stmt = $db->prepare("SELECT hash FROM foc_groups WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $from_group_id);
$stmt->execute();
$stmt->bind_result($hash);
/*while ($stmt->fetch()) {
    header("Location: index.php?group_hash=$hash&msg=$msg");
}*/
?>
<html>
    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
        <title>Blocking Group...</title>
        <meta name="theme-color" content="#2b3e50">
    </head>
    <body style='background-color:#2b3e50;'>
        <script>
        var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
        socket.emit("add-cards", <?php echo $to_group_id; ?>, function(msg) {
            socket.emit("update-group-list", 0, function(msg) {
                window.location.href = "index.php";
            })
        })
        </script>
    </body>
</html>