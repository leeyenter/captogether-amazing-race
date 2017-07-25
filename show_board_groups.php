<?php
session_start();
require "sql_login.php";

$group_id = $_SESSION["group_id"];
$board_num = $_GET["board"];

$stmt = $db->prepare("SELECT id, name, blocked FROM foc_groups WHERE board = ?");
$stmt->bind_param('i', $board_num);
$stmt->execute();
$stmt->bind_result($id, $name, $blocked);

echo "
<form action='use_block_card.php' method='post'>
<input type='hidden' name='group_id' value=$group_id />";

while($stmt->fetch()) {
    if ($id != $group_id) {
        echo "<div class='radio'><label><input type='radio' name='block_group' value='$id' id='block_$id' /> $name ($blocked blocks)</label></div>";
    }
}
echo "<input type='submit' value='Block Group' class='btn btn-primary' />";
echo "</form>";

?>