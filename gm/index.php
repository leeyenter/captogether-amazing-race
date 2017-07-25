<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../sql_login.php";
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>CAPTogether - GM Page</title>
    <link rel="stylesheet" href="bootstrap.css">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="theme-color" content="#2b503e">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script> 
    <script src="../js/bootstrap.min.js" charset="utf-8"></script>
  </head>
  <body>
    <div class="container">

      <!-- Banner for notifications -->

      <?php
      if (isset($_GET["banner"])) {
        switch($_GET["banner"]) {
          case "add_card":
            ?>
            <div class="alert alert-info alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;
              <strong>Success!</strong> Card was awarded to group.
            </div>
            <?php
            break;
          case "closed_group": 
            ?>
            <div class="alert alert-info alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;
              <strong>Success!</strong> Group was closed. 
            </div>
            <?php
            break;
        }
      }
      ?>

      <h1><strong>CAPT</strong>ogether &ndash; GM Dashboard</h1>

      <?php
      if (!isset($_GET["gm_hash"])) {
        # Ask for the hash
        ?>
        <p>Welcome! Start by keying in the hash for your station:</p>
        <form class="form" action="index.php" method="get">
          <label for='gm_hash'>GM Hash:</label>
          <input class='form-control' type="text" name="gm_hash" value="" id='gm_hash'><br />
          <button type="submit" class='btn btn-primary form-control'>Submit</button>
        </form>
        <?php
      } else {
        # Hash was specified
        # Check the hash
        $stmt = $db->prepare("SELECT id, name FROM foc_stations WHERE hash = ? LIMIT 1");
        $stmt->bind_param('s', $_GET["gm_hash"]);
        $stmt->execute();
        $stmt->bind_result($stn_id, $stn_name);
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
          # GM hash not found, try again
          ?>
          <p>We couldn't find a station master with that hash. Please try again!</p>
          <form class="form" action="index.php" method="get">
            <label for='gm_hash'>GM Hash:</label>
            <input class='form-control' type="text" name="gm_hash" value="" id='gm_hash'><br />
            <button type="submit" class='btn btn-primary form-control'>Submit</button>
          </form>
          <?php
        } else {
          $stmt->fetch();
          # Logged in
          
          ?>
          <h2>Add Card for a Group</h2>
          <form action="add_card.php" method="post">
            <input type="hidden" name="stn_id" value="<?php echo $stn_id; ?>">
            <input type="hidden" name="gm_hash" value="<?php echo $_GET["gm_hash"]; ?>">
            <div class="form-group">
              <label for="group_id">Select group:</label>
              <div id='group_dropdown'></div>
            </div>
            <div class="form-group">
              <!-- <label for="card_hash">Card hash:</label>
              <input type="text" class='form-control' name="card_hash" value=""> -->
              <?php
              $stmt = $db->prepare("SELECT id, filename FROM foc_card_types WHERE id > 0");
              $stmt->execute();
              $stmt->bind_result($card_type_id, $card_filename);
              while($stmt->fetch()) {
                ?>
                <div class="radio-inline">
                  <label>
                    <input type="radio" name="card_type_id" value="<?php echo $card_type_id; ?>">
                    <?php echo "<img src='../imgs/$card_filename' style='width:100px;' />"; ?>
                  </label>
                </div>
                <?php
              }
              ?>
            </div>
            <div class="form-group">
              <button class='btn btn-primary form-control' type="submit">Add Card</button>
            </div>
          </form>

          <br /><br /><br />

          <h2>Group's Last Station</h2>
          <form action='close_group.php' method='post'>
            <input type="hidden" name="stn_id" value="<?php echo $stn_id; ?>">
            <input type="hidden" name="gm_hash" value="<?php echo $_GET["gm_hash"]; ?>">
            <div class="form-group">
              <label for="group_id_close">Select group:</label>
              <select class="form-control" onclick='block();' onchange='unblock();' name="group_id" id="group_id_close">
              <?php
              $stmt = $db->prepare("SELECT id, name FROM foc_groups WHERE last_stn = 0");
              $stmt->execute();
              $stmt->bind_result($group_id, $group_name);
              while ($stmt->fetch()) {
                echo "<option value=$group_id>$group_name</option>";
              }
              ?>
              </select>
            </div>
            <div class="form-group">
              <button class='btn btn-danger form-control' type="submit">Close Group</button>
            </div>
          </form>
          <?php
        }
      }
      ?>
    </div>
    <script>
      /*var socket = io.connect("http://leeyenter.com", { path: "/server/socket.io" });
      socket.on("update-cards", function() {
        getGroups();
      })*/

      var blocked = false;

      function block() {
        blocked = true;
      }

      function unblock() {
        blocked = false;
      }

      // When any group uses a card, update the dropdown
      function getGroups() {
        if (!blocked) {
          $.ajax({
            method: "GET",
            url: "get_groups.php"
          })
            .done(function(data) {
              $("#group_dropdown").html(data);
            })
        }
      }
      
      getGroups();
    </script>
  </body>
</html>
