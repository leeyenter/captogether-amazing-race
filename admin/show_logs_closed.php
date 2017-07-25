<table class='table table-striped'>
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>Station Name</th>
            <th>Group Name</th>
        </tr>
    </thead>
    <tbody>
<?php
require "../sql_login.php";
$stmt = $db->prepare("SELECT foc_log_groups_closed.timestamp, foc_stations.name, foc_groups.name
FROM foc_log_groups_closed INNER JOIN foc_stations ON foc_log_groups_closed.stn_id = foc_stations.id 
INNER JOIN foc_groups ON foc_log_groups_closed.group_id = foc_groups.id WHERE foc_groups.board = ? 
ORDER BY foc_log_groups_closed.timestamp DESC");
$stmt->bind_param('i', $_GET["board"]);
$stmt->execute();
$stmt->bind_result($timestamp, $stn_name, $group_name);
while ($stmt->fetch()) {
    $dt = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);
    $dt->add(new DateInterval("PT8H"));

    echo "<tr>
        <td>".$dt->format("h:i:sa")."</td>
        <td>$stn_name</td>
        <td>$group_name</td>
    </tr>";
}
?>
    </tbody>
</table>