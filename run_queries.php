<?php
require "sql_login.php";

# To generate a unique hash for each group
$stmt = $db->prepare("UPDATE foc_groups SET hash = ? WHERE id = ? LIMIT 1");
for ($i = 1; $i <= 16; $i++) {
  $hash = substr(hash("sha1", rand()), 0, 6);
  echo $hash."<br />";
  $stmt->bind_param('si', $hash, $i);
  $stmt->execute();
}

# Generate dummy stations
// $stmt = $db->prepare("INSERT INTO foc_stations (name, hash) VALUES (?, ?)");
// for ($i = 1; $i <= 4; $i++) {
//   $hash = substr(hash("sha1", rand()), 0, 6);
//   $station = "Station ".$i;
//   $stmt->bind_param('ss', $station, $hash);
//   $stmt->execute();
// }

# Generate affiliations
// $stmt = $db->prepare("INSERT INTO foc_affiliation_cards (affiliation, hash) VALUES (?, ?)");
// for ($i = 1; $i <= 18; $i++) {
//   $hash = substr(hash("sha1", rand()), 1, 7);
//   if ($i <= 12) {
//     $affiliation = "Miner";
//   } else {
//     $affiliation = "Saboteur";
//   }
//   $stmt->bind_param('ss', $affiliation, $hash);
//   $stmt->execute();
// }

# View the cards
// $stmt = $db->prepare("SELECT type, connect_top, connect_bottom, connect_left, connect_right, filename FROM foc_card_types");
// $stmt->execute();
// $stmt->bind_result($type, $top, $bottom, $left, $right, $file);
// while ($stmt->fetch()) {
//   switch($type) {
//     case 1:
//       echo "<b>Path</b>";
//       break;
//     case 2:
//       echo "<b>Map</b>";
//       break;
//     case 3:
//       echo "<b>Block</b>";
//       break;
//     case 4:
//       echo "<b>Rockfall</b>";
//       break;
//   }
//   echo "  ";
//   if ($top) {
//     echo "top; ";
//   }
//   if ($left) {
//     echo "left; ";
//   }
//   if ($right) {
//     echo "right; ";
//   }
//   if ($bottom) {
//     echo "bottom; ";
//   }
//   echo "<br /><img src='imgs/$file'><br />";
// }

# Generate the full cards
// $add_card = $db->prepare("INSERT INTO foc_cards (card_type_id, hash) VALUES (?,?)");
//
// $stmt = $db->prepare("SELECT id, count FROM foc_card_types");
// $stmt->execute();
// $stmt->bind_result($card_type_id, $count);
// $stmt->store_result();
//
// while ($stmt->fetch()) {
//   for ($i = 0; $i < $count; $i++) {
//     $hash = substr(hash("sha1", rand()), 1, 8);
//     $add_card->bind_param('is', $card_type_id, $hash);
//     $add_card->execute();
//   }
// }

echo password_hash("dragon4", PASSWORD_DEFAULT);
?>
