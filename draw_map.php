<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "sql_login.php";

$checking = array();

$rows = 10;
$cols = 7;
$goal1 = 1;
$goal2 = 4;
$goal3 = 7;

for ($row = 1; $row <= $rows; $row++) {
  for ($col = 0; $col <= $cols; $col++) {
    $checking[$row][$col] = ["card" => "none", "can_place" => false, "top" => -1, "left" => -1, "right" => -1, "bottom" => -1];
  }
}

$checking[$rows][$goal2-1]["can_place"] = true;
$checking[$rows][$goal2-1]["right"] = 1;
$checking[$rows][$goal2+1]["can_place"] = true;
$checking[$rows][$goal2+1]["left"] = 1;
$checking[$rows-1][$goal2]["can_place"] = true;
$checking[$rows-1][$goal2]["bottom"] = 1;

$stmt = $db->prepare("SELECT foc_cards_on_board.id, foc_cards_on_board.rotated, foc_cards_on_board.row, foc_cards_on_board.col,
  foc_card_types.connect_top, foc_card_types.connect_left, foc_card_types.connect_bottom,
  foc_card_types.connect_right, foc_card_types.path, foc_card_types.filename FROM foc_cards_on_board INNER JOIN foc_card_types ON
  foc_cards_on_board.card_type_id = foc_card_types.id WHERE foc_cards_on_board.board = ?");
$stmt->bind_param('i', $_GET["board"]);
$stmt->execute();
$stmt->bind_result($card_id, $rotated, $card_row, $card_col, $connect_top, $connect_left, $connect_bottom, 
$connect_right, $path, $card_file);
while($stmt->fetch()) {
  $checking[$card_row][$card_col]["card"] = $card_file;
  $checking[$card_row][$card_col]["rotated"] = $rotated;
  $checking[$card_row][$card_col]["path"] = $path;

  if ($_GET["curr_mode"] == "paths") {
    if ($rotated == 0) {
      $top = $connect_top;
      $bottom = $connect_bottom;
      $left = $connect_left;
      $right = $connect_right;
    } else {
      # Flipped
      $top = $connect_bottom;
      $bottom = $connect_top;
      $left = $connect_right;
      $right = $connect_left;
    }
    # Update the surrounding cards
    if ($card_row > 1) {
      # Can place in the space above
      $checking[$card_row-1][$card_col]["bottom"] = $top;

      if ($top) {
        $checking[$card_row-1][$card_col]["can_place"] = true;
      }
    }
    if ($card_row < $rows) {
      # Can place in the space below
      $checking[$card_row+1][$card_col]["top"] = $bottom;

      if ($bottom) {
        $checking[$card_row+1][$card_col]["can_place"] = true;
      }
    }
    if ($card_col > 1) {
      # Can place in the space on the left
      $checking[$card_row][$card_col-1]["right"] = $left;

      if ($left) {
        $checking[$card_row][$card_col-1]["can_place"] = true;
      }
    }
    if ($card_col < $cols) {
      # Can place in the space on the right
      $checking[$card_row][$card_col+1]["left"] = $right;

      if ($right) {
        $checking[$card_row][$card_col+1]["can_place"] = true;
      }
    }
  }
}

if ($_GET["curr_mode"] != "goals") {
  echo "<form method='post' action='use_map_card.php'>";
}

// Check whether goal card is reached

$goalLeft = false;
$goalMiddle = false;
$goalRight = false;

$cardsToCheck = [];
$checked = []; # so that we don't add the same location multiple times
// Populate for goal 1
array_push($cardsToCheck, ["row"=> $rows, "col"=> $goal2-1]);
array_push($cardsToCheck, ["row"=> $rows, "col"=> $goal2+1]);
array_push($cardsToCheck, ["row"=> $rows-1, "col"=> $goal2]);

$checked = [["row"=> $rows, "col"=> $goal2-1], ["row"=> $rows, "col"=> $goal2+1], ["row"=> $rows-1, "col"=> $goal2]];

while (count($cardsToCheck) > 0) {
  $coords = array_shift($cardsToCheck);

  if (isset($checking[$coords["row"]][$coords["col"]]["path"]) && $checking[$coords["row"]][$coords["col"]]["path"]) {
    
    # I want to add top, left, bottom, right
    # Add top if not top row
    if ($coords["row"] > 1 && !in_array(($coords["row"]-1)."#".$coords["col"], $checked)) {
      array_push($cardsToCheck, ["row"=> $coords["row"]-1, "col"=> $coords["col"]]);
      array_push($checked, ($coords["row"]-1)."#".$coords["col"]);
    }
    # Add bottom if not bottom row
    if ($coords["row"] < $rows && !in_array(($coords["row"]+1)."#".$coords["col"], $checked)) {
      array_push($cardsToCheck, ["row"=> $coords["row"]+1, "col"=> $coords["col"]]);
      array_push($checked, ($coords["row"]+1)."#".$coords["col"]);
    }
    # Add left if not leftmost
    if ($coords["col"] > 1 && !in_array($coords["row"]."#".($coords["col"]-1), $checked)) {
      array_push($cardsToCheck, ["row"=> $coords["row"], "col"=> $coords["col"]-1]);
      array_push($checked, $coords["row"]."#".($coords["col"]-1));
    }
    # Add right if not rightmost
    if ($coords["col"] < $cols && !in_array($coords["row"]."#".($coords["col"]+1), $checked)) {
      array_push($cardsToCheck, ["row"=> $coords["row"], "col"=> $coords["col"]+1]);
      array_push($checked, $coords["row"]."#".($coords["col"]+1));
    }
  }
}

if (in_array("1#$goal1", $checked)) {
  $goalLeft = true;
}
if (in_array("1#$goal2", $checked)) {
  $goalMiddle = true;
}
if (in_array("1#$goal3", $checked)) {
  $goalRight = true;
}

?>

  <input type='hidden' name='mode' value='<?php echo $_GET["curr_mode"]; ?>' />
  <input type='hidden' name='rotated' value=<?php echo ($_GET["rotated"] == "0" ? "false" : $_GET["rotated"]); ?> />

  <div id="map">
    <?php

    if ($_GET["rotated"] == "true") { // Because the encoding is string
      $curr_rotated = true;
    } else {
      $curr_rotated = false;
    }

    if ($_GET["curr_mode"] == "paths") {
      # Show the current card selected, and give the option to rotate the card
      $stmt = $db->prepare("SELECT filename, connect_top, connect_left, connect_right, connect_bottom FROM foc_card_types WHERE id = ? LIMIT 1");
      $stmt->bind_param('i', $_GET["card"]);
      $stmt->execute();
      $stmt->bind_result($card_pic, $connect_top, $connect_left, $connect_right, $connect_bottom);

      while ($stmt->fetch()) {
        echo "
        <button type='button' class='btn btn-default' onclick='rotate_card()'>
          <span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>
          &nbsp;Rotate card
        </button>
        <br /><br />
        Select where to place &nbsp;&nbsp;
        <img src='http://leeyenter.com/captogether/imgs/$card_pic' class='map-td ";
        if ($curr_rotated) {
          echo "rotated";
          // Swap the available connections
          $tmp = $connect_bottom;
          $connect_bottom = $connect_top;
          $connect_top = $tmp;
          
          $tmp = $connect_left; 
          $connect_left = $connect_right;
          $connect_right = $tmp;
          
        }
        echo "'>&nbsp; :";
      }
    }
    ?>
    <table>
      <?php
      $alphabets = array("", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L"); // used as column names

      for ($row = 0; $row <= $rows; $row++) {
        echo "<tr>";
        for ($col = 0; $col <= $cols; $col++) {
          if ($row == 0 && $col == 0) {
            echo "<td>&nbsp;</td>";
          } else if ($row == 0) {
            # First row, print alphabets
            echo "<td style='text-align:center;'>{$alphabets[$col]}</td>";
          } else if ($col == 0) {
            # First column, print numbers
            echo "<td class='map-row-label'>$row</td>";
          } else {
            echo "<td class='map-td'>";
            if ($row == 1 && ($col == $goal1 || $col == $goal2 || $col == $goal3)) {
              if ($_GET["curr_mode"] == "goals") { # If map card selected
                echo "<button class='map-card' data-toggle='modal' data-target='#goal-card-modal' onclick='reveal_goal($col)'>";
              }
              echo "<img class='map-card";
              if ($_GET["curr_mode"] == "goals") { # If map card selected
                echo " card-glow";
              }
              if ($_GET["board"] == 1) {
                if ($goalLeft && $col == $goal1) {
                  echo "' src='http://leeyenter.com/captogether/imgs/coal.jpg'>";
                } elseif ($goalMiddle && $col == $goal2) {
                  echo "' src='http://leeyenter.com/captogether/imgs/coal.jpg'>";
                } elseif ($goalRight && $col == $goal3) {
                  echo "' src='http://leeyenter.com/captogether/imgs/gold.jpg'>";
                } else {
                  echo "' src='http://leeyenter.com/captogether/imgs/goal_backing.jpg'>";
                }
              } else {
                if ($goalLeft && $col == $goal1) {
                  echo "' src='http://leeyenter.com/captogether/imgs/gold.jpg'>";
                } elseif ($goalMiddle && $col == $goal2) {
                  echo "' src='http://leeyenter.com/captogether/imgs/coal.jpg'>";
                } elseif ($goalRight && $col == $goal3) {
                  echo "' src='http://leeyenter.com/captogether/imgs/coal.jpg'>";
                } else {
                  echo "' src='http://leeyenter.com/captogether/imgs/goal_backing.jpg'>";
                }
              }
              if ($_GET["curr_mode"] == "goals") { # If map card selected
                echo "</button>";
              }
            } else if ($row == $rows && $col == $goal2) {
              echo "<img class='map-card' src='http://leeyenter.com/captogether/imgs/starting.jpg'>";
            } else {
              if ($checking[$row][$col]["card"] == "none") {
                # Empty
                if ($_GET["curr_mode"] == "paths" && $checking[$row][$col]["can_place"]) {
                  if (($checking[$row][$col]["top"] == -1 || $checking[$row][$col]["top"] == $connect_top) &&
                      ($checking[$row][$col]["bottom"] == -1 || $checking[$row][$col]["bottom"] == $connect_bottom) &&
                      ($checking[$row][$col]["left"] == -1 || $checking[$row][$col]["left"] == $connect_left) &&
                      ($checking[$row][$col]["right"] == -1 || $checking[$row][$col]["right"] == $connect_right)) {
                    echo "<button class='btn map-card card-glow' name='add_values' value=".$_GET["card"]."#".$row."#".$col.">&nbsp;</button>";
                  }
                }
              } else {
                # Draw the card
                if ($_GET["curr_mode"] == "rockfall") {
                  echo "<button class='card-glow map-card' name='add_values' value='".$_GET["card"]."#$row#$col'>";
                }
                echo "<img class='map-card";
                if ($checking[$row][$col]["rotated"]) {
                  echo " rotated";
                }
                echo "' src='http://leeyenter.com/captogether/imgs/".$checking[$row][$col]["card"]."'>";
                if ($_GET["curr_mode"] == "rockfall") {
                  echo "</button>";
                }
              }

            }
            echo "</td>";
          }
        }
        echo "</tr>";
      }
      ?>
    </table>
  </div>
  <?php 
if ($_GET["curr_mode"] != "goals") {
  echo "</form>";
}
?>