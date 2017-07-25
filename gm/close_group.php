<?php
require "../sql_login.php";

$stn_id = $_POST["stn_id"];
$group_id = $_POST["group_id"];

$stmt = $db->prepare("UPDATE foc_groups SET last_stn = 1 WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $group_id);
$stmt->execute();

$stmt = $db->prepare("INSERT INTO foc_log_groups_closed (stn_id, group_id) VALUES (?, ?)");
$stmt->bind_param('ii', $stn_id, $group_id);
$stmt->execute();

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
<script>
var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
socket.emit("add-cards", <?php echo $group_id; ?>, function(msg) {
    socket.emit("update-group-list", 0, function(msg) {
        window.location.href = "<?php echo "index.php?gm_hash=".$_POST["gm_hash"]."&banner=closed_group"; ?>"
    })
});

</script>