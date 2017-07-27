<?php 
session_start();
require "sql_login.php";
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#2b3e50">
    <title>CAPTogether</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script> 
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>
  <body>
    <script>
      var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
    </script>
    <div class="container">
      <h1><strong>CAPT</strong>ogether
      <?php 
      if (isset($_SESSION["group_id"])) {
        $stmt = $db->prepare("SELECT name FROM foc_groups WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $_SESSION["group_id"]);
        $stmt->execute();
        $stmt->bind_result($group_name);
        while ($stmt->fetch()) {
          echo " - $group_name";
        }
      }
      ?>
      </h1>
      <?php 

      if (!isset($_SESSION["group_id"])) {
        # Not logged in
          if (isset($_GET["group_hash"])) {
          # Proceed to find which group this belongs to
          $hash = $_GET["group_hash"];
          $stmt = $db->prepare("SELECT id, name, board, affiliation, blocked FROM foc_groups WHERE hash = ? LIMIT 1");
          $stmt->bind_param('s', $hash);
          $stmt->execute();
          $stmt->bind_result($group_id, $group_name, $board, $affiliation, $blocked);
          $stmt->store_result();
          if ($stmt->num_rows == 0) {
            # No results found
            # Ask them to enter again
            ?>
            <p>A group with that hash could not be found. Please try again!</p>
            <form class="form" action="index.php" method="get">
              <label for='group_hash'>Group Hash:</label>
              <input class='form-control' type="text" name="group_hash" value="" id='group_hash'><br />
              <button type="submit" class='btn btn-primary form-control'>Submit</button>
            </form>
            <?php
          } else {
            $stmt->fetch();
            $_SESSION["group_id"] = $group_id;
            header("Location: index.php");
          }
        } else {
          # Ask for hash
          ?>
          <p>Welcome! Start by keying in the hash for your group:</p>
          <form class="form" action="index.php" method="get">
            <label for='group_hash'>Group Hash:</label>
            <input class='form-control' type="text" name="group_hash" value="" id='group_hash'><br />
            <button type="submit" class='btn btn-primary form-control'>Submit</button>
          </form>
          <?php
        }
      } else {
        # Logged in
        if (isset($_SESSION["group_id"])) {
          $stmt = $db->prepare("SELECT name, board, affiliation FROM foc_groups WHERE id = ? LIMIT 1");
          $stmt->bind_param('i', $_SESSION["group_id"]);
          $stmt->execute();
          $stmt->bind_result($group_name, $board, $affiliation);
          $stmt->store_result();
          $stmt->fetch();

          if ($affiliation == 0) {
            if (!isset($_GET["aff_hash"])) {
              # Affiliation not set
              # Ask them to input it
              ?>
              <p>Welcome <strong><?php echo $group_name; ?></strong>! Key in your affiliation code:</p>
              <form class="form" action="index.php" method="get">
                <label for='aff_hash'>Affiliation Code:</label>
                <input class='form-control' type="text" name="aff_hash" value="" id='aff_hash'><br />
                <button type="submit" class='btn btn-primary form-control'>Submit</button>
              </form>
              <?php
            } else {
              # They input their affiliation
              # Check it
              $hash = $_GET["aff_hash"];
              $stmt = $db->prepare("SELECT affiliation FROM foc_affiliation_cards WHERE hash = ? LIMIT 1");
              $stmt->bind_param('s', $hash);
              $stmt->execute();
              $stmt->bind_result($affiliation_txt);
              $stmt->store_result();
              if ($stmt->num_rows == 0) {
                # Affiliation code not found
                ?>
                <p>Welcome <strong><?php echo $group_name; ?></strong>! We couldn't find that affiliation code. Please try again:</p>
                <form class="form" action="index.php" method="get">
                  <label for='aff_hash'>Affiliation Code:</label>
                  <input type="hidden" name="group_hash" value="<?php echo $_GET["group_hash"]; ?>">
                  <input class='form-control' type="text" name="aff_hash" value="" id='aff_hash'><br />
                  <button type="submit" class='btn btn-primary form-control'>Submit</button>
                </form>

                <?php
              } else {
                $stmt->fetch();
                # Update the affiliation code
                $stmt = $db->prepare("UPDATE foc_groups SET affiliation = ? WHERE id = ? LIMIT 1");
                if ($affiliation_txt == "Miner") {
                  $affiliation_num = 1;
                } else {
                  $affiliation_num = 2;
                }
                $stmt->bind_param('ii', $affiliation_num, $_SESSION["group_id"]);
                $stmt->execute();
                //header("Location: index.php");
?>
<script>
socket.emit("update-group-list", 0, function(msg) {
    window.location.href = "index.php";
})
</script>
                <?php
              }
            }
          } else {
            # Affiliation set
            # Now to input cards and show board

            echo "<div id='display'></div>
            <div id='cards-panel'></div>
            <div id='logs'></div>";

          }
        }
      }
      ?>

    </div>

    <div class="modal fade" id="goal-card-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id='modal-content' style='text-align:center;'>
        
        </div>
      </div>
    </div>

    <script type="text/javascript">
    <?php 
    if (isset($_SESSION["group_id"])) {
    ?>
      
      //var socket = io.connect("ws://yenter.io:2000");
      //var socket = io.connect("http://localhost:2000");
      
      var mode = "none";
      var curr_card = 0;
      var card_rotated = false;

      socket.on("update-board", function(board) {
				if (board == <?php echo $board; ?>) {
					// Refresh the board
          console.log("Refresh board");
          updateMap();
          getLogs();
				}
			});
			
			socket.on("update-cards", function(user) {
        console.log("Received socket emit");
				if (user == <?php echo $_SESSION["group_id"]; ?>) {
          console.log("Fetch card");
          // Called when a card is awarded, or when a card is used
          mode = "none";
          updateCards();
				}
        getLogs();
      });

      function updateMap() {
        console.log("Update map. Mode: "+mode);
        if (mode != "block") {
          // Draw a map
          $.ajax({
            method: "GET",
            url: "draw_map.php",
            data: {
              rotated: card_rotated, 
              curr_mode: mode, 
              board: <?php echo $board; ?>, 
              card: curr_card
            }
          })
            .done(function(map) {
              $("#display").html(map);
            })
        } else {
          // Show who can be blocked
          $.ajax({
            method: "GET",
            url: "show_board_groups.php",
            data: {board: <?php echo $board; ?>}
          })
            .done(function(data) {
              $("#display").html(data);
            })
        }
        getLogs();
      }

      function updateCards() {
        $.ajax({
          method: "GET",
          url: "show_cards.php", 
          data: {mode: mode}
        }) 
        .done(function(map) {
          $("#cards-panel").html(map);
        })
        getLogs();
      }

      function change_card(card) {
        card_rotated = 0;
        curr_card = card;
      }

      function change_mode(new_mode) {
        //$(".goal-backing").addClass("card-glow");
        /*if (mode != new_mode) {
          mode = new_mode;
        } else {
          mode = "none";
        }*/
        mode = new_mode;
        updateMap();
        updateCards();
        console.log(mode);
      }

      function rotate_card() {
        card_rotated = !card_rotated;
        updateMap();
      }

      function discard_card(card_type) {
        if (confirm("Are you sure you want to discard this card?")) {
          // Remove the card
          $.ajax({
            method: "POST",
            url: "discard_card.php",
            data: {card_type: card_type, lower_block: 1, mode: "discard"}
          }) 
          .done(function(map) {
            mode = "none";
            curr_card = 0;
            updateCards();
            socket.emit("update-cards", <?php echo $_SESSION["group_id"]; ?>);
          })
        }
      }

      function reveal_goal(column) {
        if (confirm("Are you sure you want to reveal this card?")) {
          $.ajax({
            method: "POST",
            url: "discard_card.php",
            data: {card_type: curr_card, lower_block: 0, col: column, mode: "reveal"}
          }) 
          .done(function(result) {
            // Reveal the result here
            mode = "none";
            curr_card = 0;
            $("#modal-content").html("<img src='imgs/"+result+".jpg'>");
            updateCards();
            updateMap();
            socket.emit("update-cards", <?php echo $_SESSION["group_id"]; ?>);
          })
        }
      }

      function getLogs() {
        $.ajax({
          method: "GET", 
          url: "show_logs_placed.php", 
          data: {board: <?php echo $board; ?>}
        }).done(function(result) {
          $("#logs").html(result);
        })
      }

      updateMap();
      updateCards();
      getLogs();

    <?php
    }
    ?>
    </script>
  </body>
</html>
