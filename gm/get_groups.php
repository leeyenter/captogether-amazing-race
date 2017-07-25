<?php
require "../sql_login.php";
$stmt = $db->prepare("SELECT id, name FROM foc_groups WHERE last_stn = 0");
$count_cards = $db->prepare("SELECT COUNT(id) FROM foc_cards_on_hand WHERE group_id = ?");

$stmt->execute();
$stmt->bind_result($group_id, $group_name);
$stmt->store_result();

echo '<select class="form-control" name="group_id" id="group_id">';

while ($stmt->fetch()) {
    $count_cards->bind_param('i', $group_id);
    $count_cards->execute();
    $count_cards->bind_result($num_cards);

    while ($count_cards->fetch()) {
    echo "<option value=$group_id ";
    if ($num_cards >= 4) {
        echo "disabled";
    }
    echo ">$group_name ($num_cards cards)</option>";
    }
}
echo '</select>';
?>