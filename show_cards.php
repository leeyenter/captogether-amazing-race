<?php
session_start();
require "sql_login.php";
$group_id = $_SESSION["group_id"];

$get_group = $db->prepare("SELECT last_stn FROM foc_groups WHERE id = ? LIMIT 1");
$get_group->bind_param('i', $_SESSION["group_id"]);
$get_group->execute();
$get_group->bind_result($last_stn);
$get_group->store_result();
$get_group->fetch();

# Check if group is blocked
$stmt = $db->prepare("SELECT blocked FROM foc_groups WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($blocked);
$mode = $_GET["mode"];

while ($stmt->fetch()) {
    if ($blocked > 0) {
        $mode = "discard"; // overwrite mode
        # Display a banner
        ?>
        <br />
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;
            <strong>You are currently blocked!</strong> You will need to discard <?php if ($blocked == 1) { echo "1 card"; } else { echo "$blocked cards"; } ?>.
        </div>
        <?php
    } else {
        # Not blocked
        # Show discard button if mode is not discard, else show cancel
        if ($mode == "discard") {
            echo "<br /><button class='btn btn-danger' onclick=\"change_mode('none')\">Cancel</button><br />";
        } else {
            echo "<br /><button class='btn btn-danger' onclick=\"change_mode('discard')\">Discard</button><br />";
        }
    }
}

# Count the number of cards that the group already has
$stmt = $db->prepare("SELECT foc_cards_on_hand.id, foc_cards_on_hand.card_type_id, foc_card_types.filename FROM foc_cards_on_hand INNER JOIN foc_card_types ON foc_cards_on_hand.card_type_id = foc_card_types.id WHERE foc_cards_on_hand.group_id = ?");
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($card_id, $card_type, $card_filename);
$stmt->store_result();

while ($stmt->fetch()) {
    # Print the card picture and make it a button
    echo "<button class='card-btn'";
    //if ($blocked == 0) {
    if ($mode != "discard") {
        # Not blocked
        if ($stmt->num_rows <= 3 && !$last_stn) {
            echo " disabled";
        }
        switch ($card_type) {
            case 1:
                $mode = "goals";
                break;
            case 2:
                $mode = "block";
                break;
            case 3:
                $mode = "rockfall";
                break;
            default:
                $mode = "paths";
        }
        echo " onclick='change_card($card_type); change_mode(\"$mode\");'";
    } else {
        # Blocked
        # They will need to discard cards
        echo " onclick='discard_card($card_type);'";
    }
    
    echo ">";
    
    # Show overlays
    # Show grey overlay if buttons are disabled
    if ($mode == "discard") {
        echo "<div class='blocked-overlay'></div>";
    } else if ($stmt->num_rows <= 3 && !$last_stn) {
        echo "<div class='disabled-overlay'></div>";
    }

    echo "<img src='imgs/$card_filename' /></button>";
}
?>