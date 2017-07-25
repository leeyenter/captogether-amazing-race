<?php
require "../sql_login.php";
# Show groups
$stmt = $db->prepare("SELECT id, name, board, affiliation, hash, blocked, last_stn FROM foc_groups");
$stmt->execute();
$stmt->bind_result($id, $name, $board, $affiliation, $hash, $blocked, $last_stn);
echo "<table class='table table-hover table-bordered'>
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Board</th>
        <th>Affiliation</th>
        <th>Hash</th>
        <th>Blocked</th>
        <th>Last Stn</th>
    </tr>
</thead>
<tbody>";
while ($stmt->fetch()) {
    echo "<tr>
        <td style='text-align:right;'>$id</td>
        <td>$name</td>
        <td style='text-align:center;'>$board</td>
        <td>".($affiliation == 0 ? "Not set": 
            ($affiliation == 1 ? "<span class='green'><span class='glyphicon glyphicon-wrench'></span> &nbsp; Miner</span>" : 
            "<span class='red'><span class='glyphicon glyphicon-fire'></span> &nbsp; Saboteur</span>") )."</td>
        <td><code>$hash</code></td>
        <td style='text-align:right;'>$blocked</td>
        <td>".($last_stn ? "<span class='green'><span class='glyphicon glyphicon-ok'></span> &nbsp; Yes</span>" : "No")."</td>
    </tr>";
    echo "<script>groups[$id] = $board;</script>";
}
echo "</tbody></table>";
?>