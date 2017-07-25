<table class='table table-striped'>
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>Station Name</th>
            <th>Group Name</th>
            <th>Card</th>
        </tr>
    </thead>
    <tbody>
<?php
require "../sql_login.php";
$stmt = $db->prepare("SELECT foc_log_cards_given.timestamp, foc_stations.name, foc_groups.name, 
foc_card_types.filename, foc_card_types.id FROM foc_log_cards_given INNER JOIN 
foc_stations ON foc_log_cards_given.stn_id = foc_stations.id INNER JOIN foc_groups ON 
foc_log_cards_given.group_id = foc_groups.id INNER JOIN foc_card_types ON 
foc_log_cards_given.card_type_id = foc_card_types.id WHERE foc_groups.board = ? ORDER BY foc_log_cards_given.timestamp DESC");
$stmt->bind_param('i', $_GET["board"]);
$stmt->execute();
$stmt->bind_result($timestamp, $stn_name, $group_name, $filename, $card_type);
while ($stmt->fetch()) {
    $dt = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);
    $dt->add(new DateInterval("PT8H"));

    switch($card_type) {
        case 1:
            $type = "&nbsp;&nbsp;(Map)";
            break;
        case 2:
            $type = "&nbsp;&nbsp;(Block)";
            break;
        case 3:
            $type = "&nbsp;&nbsp;(Rockfall)";
            break;
        default:
            $type = "";            
    }

    echo "<tr>
        <td>".$dt->format("h:i:sa")."</td>
        <td>$stn_name</td>
        <td>$group_name</td>
        <td><img class='map-td' src='http://leeyenter.com/captogether/imgs/$filename'> $type</td>
    </tr>";
}
?>
    </tbody>
</table>