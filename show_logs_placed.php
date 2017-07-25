<?php
require "sql_login.php";
date_default_timezone_set('Asia/Singapore');

$stmt = $db->prepare("SELECT foc_groups.name, foc_card_types.id, foc_card_types.filename, foc_log_cards_placed.row, foc_log_cards_placed.col, foc_log_cards_placed.timestamp 
FROM foc_log_cards_placed INNER JOIN foc_groups ON foc_log_cards_placed.group_id = foc_groups.id INNER JOIN foc_card_types 
ON foc_log_cards_placed.card_type_id = foc_card_types.id WHERE foc_groups.board = ? ORDER BY foc_log_cards_placed.timestamp DESC");
$stmt->bind_param('i', $_GET["board"]);
$stmt->execute();
$stmt->bind_result($group_name, $card_type, $filename, $row, $col, $timestamp);
$stmt->store_result();

?>

<table class='table table-striped'>
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>Group Name</th>
            <th>Card</th>
            <th>Used On</th>
        </tr>
    </thead>
    <tbody>
        <?php

        $cols = ["", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K"];

        $find_group = $db->prepare("SELECT name FROM foc_groups WHERE id = ? LIMIT 1");

        while ($stmt->fetch()) {
            $dt = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);
            $dt->add(new DateInterval("PT8H"));

            switch($card_type) {
                case 0:
                    $type = "";
                    $target = "Discarded";
                    break;
                case 1:
                    $type = "&nbsp;&nbsp;(Map)";
                    $target = "Card at column $row";
                    break;
                case 2:
                    $type = "&nbsp;&nbsp;(Block)";
                    $find_group->bind_param('i', $row);
                    $find_group->execute();
                    $find_group->bind_result($found_group);
                    while($find_group->fetch()) {
                        $target = "<span class='glyphicon glyphicon-user'></span> &nbsp; ".$found_group;
                    }
                    break;
                case 3:
                    $type = "&nbsp;&nbsp;(Rockfall)";
                    $target = "Cell ".$cols[$col].$row;  
                    break;
                default:
                    $type = "";
                    $target = "Cell ".$cols[$col].$row;                
            }

            echo "<tr>
                <td>".$dt->format("h:i:sa")."</td>
                <td>$group_name</td>
                <td><img class='map-td' src='http://leeyenter.com/captogether/imgs/$filename'> $type</td>
                <td>$target</td>
            </tr>";
        }
        ?>
    </tbody>
</table>