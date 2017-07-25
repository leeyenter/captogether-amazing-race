<?php
session_start();
date_default_timezone_set('Asia/Singapore');
require "sql_login.php";
ini_set('display_errors', 1);
?>
<style>
html {
    background-color: "#2b3e50";
}
</style>
<?php
$add_values = $_POST["add_values"];
$group_id = $_SESSION["group_id"];
$mode = $_POST["mode"];

// Get the group's id from the hash
$get_group_id = $db->prepare("SELECT board FROM foc_groups WHERE id = ? LIMIT 1");
$get_group_id->bind_param('i', $group_id);
$get_group_id->execute();
$get_group_id->bind_result($board);
$get_group_id->store_result();
while ($get_group_id->fetch()) {
    $vals = explode("#", $add_values);

    switch ($mode) {
        case "paths":
        case "rockfall":
            $card_type = intval($vals[0]);
            $row = $vals[1];
            $col = $vals[2];
            break;
    } 

    // Check if group has the card in the first place
    $stmt = $db->prepare("SELECT id FROM foc_cards_on_hand WHERE group_id = ? AND card_type_id = ? LIMIT 1");
    $stmt->bind_param('ii', $group_id, $card_type);
    $stmt->execute();
    $stmt->bind_result($card_id);
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Card not found
        // Throw error
        $msg = "card_not_found";
    } else {
        // Let's continue
        $stmt->fetch();
        switch ($mode) {
            case "paths":
                $rotated = $_POST["rotated"];
                $rotated = $rotated == "true" ? true : false;
                // Check if the map still has space
                $stmt = $db->prepare("SELECT id FROM foc_cards_on_board WHERE board = ? AND row = ? AND col = ? LIMIT 1");
                $stmt->bind_param('iii', $board, $row, $col);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    // Map no space
                    // Throw error
                    $msg = "map_no_space";
                } else {
                    $msg = "ok";
                    // Let's add the card in
                    $stmt = $db->prepare("INSERT INTO foc_cards_on_board (board, row, col, card_type_id, rotated) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param('iiiii', $board, $row, $col, $card_type, $rotated);
                    $stmt->execute();

                    // Add into log
                    $stmt = $db->prepare("INSERT INTO foc_log_cards_placed (group_id, card_type_id, row, col, rotated) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param('iiiii', $group_id, $card_type, $row, $col, $rotated);
                    $stmt->execute();
                }
                break;
            case "rockfall":
                $stmt = $db->prepare("SELECT id FROM foc_cards_on_board WHERE board = ? AND row = ? AND col = ? LIMIT 1");
                $stmt->bind_param('iii', $board, $row, $col);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 0) {
                    // Map no space
                    // Throw error
                    $msg = "cannot_use_card";
                } else {
                    $msg = "ok";
                    // Delete from board
                    $stmt = $db->prepare("DELETE FROM foc_cards_on_board WHERE board = ? AND row = ? AND col = ? LIMIT 1");
                    $stmt->bind_param('iii', $board, $row, $col);
                    $stmt->execute();

                    // Add into log
                    $stmt = $db->prepare("INSERT INTO foc_log_cards_placed (group_id, card_type_id, row, col, rotated) VALUES (?, ?, ?, ?, 0)");
                    $stmt->bind_param('iiii', $group_id, $card_type, $row, $col);
                    $stmt->execute();
                }
                break;
        }

        if ($msg == "ok") {
            // Remove the card from the user's stack
            $stmt = $db->prepare("DELETE FROM foc_cards_on_hand WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $card_id);
            $stmt->execute();
        }
    }
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
<script>
var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
let data = {
    "board": <?php echo $board; ?>, 
    "group": <?php echo $group_id; ?>
}
socket.emit("update-both-callback", JSON.stringify(data) , function(msg) {
    window.location.href = "<?php echo "index.php?msg=$msg"; ?>"
})
</script>