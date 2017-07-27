<?php
session_start();
# Also used to reveal the goal card
require "sql_login.php";
$card_type = $_POST["card_type"];
$lower_block = $_POST["lower_block"];

$stmt = $db->prepare("DELETE FROM foc_cards_on_hand WHERE group_id = ? AND card_type_id = ? LIMIT 1");
$stmt->bind_param('ii', $_SESSION["group_id"], $card_type);
$stmt->execute();

# Decrease the block by one
if ($lower_block == 1 || $lower_block == "1") {
    $stmt = $db->prepare("UPDATE foc_groups SET blocked = blocked - 1 WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $_SESSION["group_id"]);
    $stmt->execute();
} 

if ($_POST["mode"] == "reveal") {
    # Reveal the card
    $col = $_POST["col"];
    $stmt = $db->prepare("SELECT board FROM foc_groups WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $_SESSION["group_id"]);
    $stmt->execute();
    $stmt->bind_result($board);
    while($stmt->fetch()) {
        if ($board == 1) {
            switch ($col) {
                case 1:
                case 4:
                    echo "coal";
                    break;
                case 7:
                    echo "gold";
                    break;
            }
        } else if ($board == 2) {
            switch ($col) {
                case 4:
                case 7:
                    echo "coal";
                    break;
                case 1:
                    echo "gold";
                    break;
            }
        }
    }
}

# Add a log
# If discard, don't show the card
$update_log = $db->prepare("INSERT INTO foc_log_cards_placed (group_id, card_type_id, row) VALUES (?, ?, ?)");

if ($_POST["mode"] == "discard") {
    # Add anonymous
    $card_type_id = 0;
    $row = 0;
} else {
    # Note that it's the map card
    $card_type_id = 1;
    $row = $col;
}

$update_log->bind_param('iii', $_SESSION["group_id"], $card_type_id, $row);
$update_log->execute();

?>