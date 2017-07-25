<!doctype html>
<html>
    <head>
        <title>Adding Card..</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
        <meta name="theme-color" content="#2b503e">
    </head>
    <body style='background-color: #2b503e;'>
    <?php
    require '../sql_login.php';
    date_default_timezone_set('Asia/Singapore');

    $stn_id = $_POST["stn_id"];
    $group_id = $_POST["group_id"];
    $card_type_id = $_POST["card_type_id"];

    $stmt = $db->prepare("SELECT COUNT(id) FROM foc_cards_on_hand WHERE group_id = ?");
    $stmt->bind_param('i', $group_id);
    $stmt->execute();
    $stmt->bind_result($count_cards);
    $stmt->store_result();
    $stmt->fetch();

    if ($count_cards < 4) {
        $stmt = $db->prepare("INSERT INTO foc_cards_on_hand (group_id, card_type_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $group_id, $card_type_id);
        $stmt->execute();

        $stmt = $db->prepare("INSERT INTO foc_log_cards_given (stn_id, group_id, card_type_id) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $stn_id, $group_id, $card_type_id);
        $stmt->execute();
    }

    
    ?>

    <script>
    //var socket = io.connect("ws://yenter.io:2000");
    var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
    socket.emit("add-cards", <?php echo $group_id; ?>, function(message) {
        window.location.href = "<?php echo "index.php?gm_hash=".$_POST["gm_hash"]."&banner=add_card"; ?>";
    });
    //
    </script>
    </body>
</html>