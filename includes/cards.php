<?php
function output_logins($max_height = 30)
{
  $db_connection = db_connect();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./members.php">Logins</a></p>
      </h3>
      <div class="ms-auto">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#add-new-login">
          Add new
        </a>
      </div>
    </div>

    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      $logins = $db_connection->query("SELECT `email`, `password`, `members`.`first_name`, `members`.`last_name`, `members`.`image` FROM `logins` LEFT JOIN `members` ON `logins`.`ID` = `members`.`ID` WHERE `members`.`ID` >= 1 ORDER BY `members`.`first_name` ASC");

      if ($logins->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No members to display.</div>
            </div>
          </div>
        </div>
        <?php
      } else {
        $sort_initial = '';

        while ($login = $logins->fetch_assoc()) {
        ?>
          <div class="list-group-item">
            <div class="row">
              <div class="col-auto">
                <a href="#">
                  <?php
                  if ($login["image"] != "") {
                  ?>
                    <span class="avatar" style="background-image: url(<?= $login["image"]; ?>)"></span>
                  <?php
                  } else {
                  ?>
                    <span class="avatar"><?= substr($login["first_name"], 0, 1) . substr($login["last_name"], 0, 1); ?></span>
                  <?php
                  }
                  ?>
                </a>
              </div>
              <div class="col text-truncate">
                <a href="#" class="text-body d-block"><?= $login["first_name"] . " " . $login["last_name"]; ?></a>
                <div class="text-muted text-truncate mt-n1"><?= $login["email"]; ?></div>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>
    </div>
  </div>

  <div class="modal modal-blur fade" id="add-new-login" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="add-login-result" class="row" style="visibility: hidden; height: 0px;">
            <div id="add-login-result-status" class="modal-status bg-success"></div>
            <div class="text-center py-4">
              <div id="add-login-result-icon">
                Result icon
              </div>
              <h3 id="add-login-result-title">Result title</h3>
              <div class="text-muted" id="add-login-result-text">Result text</div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Member</label>
                <select id="add-login-member-ID" class="form-select">
                  <option value="" disabled selected></option>
                  <?php
                  $members = $db_connection->query("SELECT `first_name`, `last_name`, `ID` FROM `members` WHERE `ID`>=1 ORDER BY `first_name` ASC");

                  while ($member = $members->fetch_assoc()) {
                    $selected = "";

                  ?>
                    <option value="<?= $member["ID"]; ?>" <?= $selected; ?>><?= $member["first_name"]; ?> <?= $member["last_name"]; ?> (ID=<?= $member["ID"]; ?>)</option>
                  <?php
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">First name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="Adam" autocomplete="off" disabled>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Last name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="Blakey" autocomplete="off" disabled>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group input-group-flat">
                  <input id="add-login-email" type="email" class="form-control" value="" placeholder="john@example.com" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group input-group-flat">
                  <input id="add-login-password" type="password" class="form-control" value="" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <button id="add-login-button" class="btn btn-primary ms-auto" onclick="addLogin()">
            <span id="add-login-button-icon">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
              </svg>
            </span>
            <span id="add-login-button-text">
              Add login
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_members($max_height = 30)
{
  $db_connection = db_connect();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./members.php">Members</a></p>
      </h3>
      <div class="ms-auto">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#add-new-member">
          Add new
        </a>
      </div>
    </div>

    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      //$members = $db_connection->query("SELECT `members`.`ID`, `first_name`, `last_name`, `instrument`, `members`.`image`, `ensembles`.`name` AS `ensemble_name` FROM `members` LEFT JOIN `members-ensembles` ON `members-ensembles`.`member_ID` = `members`.`ID` LEFT JOIN `ensembles` ON `members-ensembles`.`ensemble_ID` = `ensembles`.`ID` WHERE `members`.`ID` >= 1 ORDER BY `first_name` ASC");
      $members = $db_connection->query("SELECT `members`.`ID`, `first_name`, `last_name`, `instrument`, `members`.`image` FROM `members` WHERE `members`.`ID` >= 1 ORDER BY `first_name` ASC");

      if ($members->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No members to display.</div>
            </div>
          </div>
        </div>
        <?php
      } else {
        $sort_initial = '';

        while ($member = $members->fetch_assoc()) {
          $ensembles = $db_connection->query("SELECT `ensembles`.`name` AS `name` FROM `ensembles` LEFT JOIN `members-ensembles` ON `members-ensembles`.`ensemble_ID`=`ensembles`.`ID` WHERE `member_ID`='" . $member["ID"] . "'");

          if ($ensembles->num_rows == 0) {
            $ensemble_list = "no ensembles";
          } else {
            $first_loop = true;
            $ensemble_list = "";
            while ($ensemble = $ensembles->fetch_assoc()) {
              if ($first_loop) {
                $ensemble_list .= "";
                $first_loop = false;
              } else {
                $ensemble_list .= ", ";
              }

              $ensemble_list .= $ensemble["name"];
            }
          }


          if ($sort_initial != substr($member["first_name"], 0, 1)) {
            $sort_initial = substr($member["first_name"], 0, 1);
        ?>
            <div class="list-group-header sticky-top"><?= $sort_initial; ?></div>
          <?php
          }
          ?>
          <div class="list-group-item">
            <div class="row">
              <div class="col-auto">
                <a href="#">
                  <?php
                  if ($member["image"] != "") {
                  ?>
                    <span class="avatar" style="background-image: url(<?= $member["image"]; ?>)"></span>
                  <?php
                  } else {
                  ?>
                    <span class="avatar"><?= substr($member["first_name"], 0, 1) . substr($member["last_name"], 0, 1); ?></span>
                  <?php
                  }
                  ?>
                </a>
              </div>
              <div class="col text-truncate">
                <a href="#" class="text-body d-block"><?= $member["first_name"] . " " . $member["last_name"]; ?></a>
                <div class="text-muted text-truncate mt-n1"><?= $member["instrument"]; ?>; <?= $ensemble_list; ?></div>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>
    </div>
  </div>

  <div class="modal modal-blur fade" id="add-new-member" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">First name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="John" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Last name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="Smith" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Instrument</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="Clarinet" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Ensemble</label>
                <select class="form-select" multiple="">
                  <option value="1">NSWO</option>
                  <option value="2">NWE</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add member
          </a>
        </div>
      </div>
    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_polls($max_height = 30)
{
  $db_connection = db_connect();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./polls.php">Active polls</a></p>
      </h3>
    </div>
    <div class="table-responsive" style="max-height: <?= $max_height; ?>rem">
      <table class="table table-vcenter card-table">
        <thead>
          <tr>
            <th>Ensemble</th>
            <th>Term</th>
            <th>Dates</th>
            <th>Link</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $polls = $db_connection->query("SELECT DISTINCT `terms`.`ID` AS `term_ID`, `terms`.`name` AS `term_name`, `ensembles`.`ID` AS `ensemble_ID`, `ensembles`.`name` AS `ensemble_name` FROM `terms` CROSS JOIN `ensembles`");

          if ($polls->num_rows == 0) {
          ?>
            <tr>
              <td>No polls to display.</td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <?php
          } else {
            while ($poll = $polls->fetch_assoc()) {
              $first_date_query = $db_connection->query("SELECT `datetime` FROM `term_dates` WHERE `term_ID`='" . $poll["term_ID"] . "' ORDER BY `datetime` ASC LIMIT 1")->fetch_array()[0];
              $last_date_query  = $db_connection->query("SELECT `datetime` FROM `term_dates` WHERE `term_ID`='" . $poll["term_ID"] . "' ORDER BY `datetime` DESC LIMIT 1")->fetch_array()[0];

              $first_date = new DateTime();
              $first_date->setTimestamp($first_date_query);
              $first_date->setTimeZone(new DateTimeZone('Europe/London'));
              $last_date  = new DateTime();
              $last_date->setTimestamp($last_date_query);
              $last_date->setTimeZone(new DateTimeZone('Europe/London'));

            ?>
              <tr>
                <td><?= $poll["term_name"]; ?></td>
                <td><?= $poll["ensemble_name"]; ?></td>
                <td class="text-muted"><?= $first_date->format("jS M Y"); ?> — <?= $last_date->format("jS M Y"); ?></td>
                <td class="text-muted">
                  <a target="_blank" href="./poll.php?<?= http_build_query(array("ensemble_ID" => $poll["ensemble_ID"], "term_ID" => $poll["term_ID"])); ?>" class="text-reset">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-link" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                      <path d="M10 14a3.5 3.5 0 0 0 5 0l4 -4a3.5 3.5 0 0 0 -5 -5l-.5 .5" />
                      <path d="M14 10a3.5 3.5 0 0 0 -5 0l-4 4a3.5 3.5 0 0 0 5 5l.5 -.5" />
                    </svg>
                  </a>
                </td>
              </tr>
          <?php
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_notifications($max_height = 30)
{
  $db_connection = db_connect();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./notifications.php">Notifications</a></p>
      </h3>
    </div>
    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      $notifications = $db_connection->query("SELECT `members`.`first_name`, `members`.`last_name`, `edit_datetime`, `members`.`instrument`, `status`, `members`.`image`, `term_dates`.`datetime` AS `rehearsal_date` FROM `attendance` LEFT JOIN `members` ON `attendance`.`member_ID` = `members`.`ID` LEFT JOIN `term_dates` ON `attendance`.`term_dates_ID` = `term_dates`.`ID` ORDER BY `edit_datetime` DESC");

      if ($notifications->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No notifications to display.</div>
            </div>
          </div>
        </div>
        <?php
      } else {
        while ($notification = $notifications->fetch_assoc()) {
          $date = new DateTime();
          $date->setTimestamp($notification["rehearsal_date"]);
          $date->setTimeZone(new DateTimeZone('Europe/London'));

          if ($notification["status"] == 1) {
            $notification_text = " is coming to " . $date->format("jS M Y @ H:i:s");
            $notification_colour = "bg-green";
          } else {
            $notification_text = " is <strong>not</strong> coming to " . $date->format("jS M Y @ H:i:s");
            $notification_colour = "bg-red";
          }
        ?>
          <div class="list-group-item">
            <div class="row align-items-center">
              <div class="col-auto"><span class="badge <?= $notification_colour; ?>"></span></div>
              <div class="col-auto">
                <a href="#">
                  <?php
                  if ($notification["image"] != "") {
                  ?>
                    <span class="avatar" style="background-image: url(<?= $notification["image"]; ?>)"></span>
                  <?php
                  } else {
                  ?>
                    <span class="avatar"><?= substr($notification["first_name"], 0, 1) . substr($notification["last_name"], 0, 1); ?></span>
                  <?php
                  }
                  ?>
                </a>
              </div>
              <div class="col text-truncate">
                <a href="#" class="text-body"><?= $notification["first_name"] . " " . $notification["last_name"]; ?></a><?= $notification_text; ?>
                <small class="d-block text-muted text-truncate mt-n1"><?= findTimeAgo($notification["edit_datetime"]); ?></small>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>

    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_ensembles($max_height = 30)
{
  $db_connection = db_connect();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./ensembles.php">Ensembles</a></p>
      </h3>
      <div class="ms-auto">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#add-new-ensemble">
          Add new
        </a>
      </div>
    </div>
    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      $ensembles = $db_connection->query("SELECT `ID`, `name`, `image` FROM `ensembles` ORDER BY `name` ASC");

      if ($ensembles->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No ensembles to display.</div>
            </div>
          </div>
        </div>
        <?php
      } else {
        while ($ensemble = $ensembles->fetch_assoc()) {
        ?>
          <div class="list-group-item">
            <div class="row">
              <div class="col-auto">
                <a href="#">
                  <?php
                  if ($ensemble["image"] != "") {
                  ?>
                    <span class="avatar" style="background-image: url('<?= $ensemble["image"]; ?>')"></span>
                  <?php
                  } else {
                  ?>
                    <span class="avatar"><?= $ensemble["name"]; ?></span>
                  <?php
                  }
                  ?>
                </a>
              </div>
              <div class="col text-truncate">
                <a href="#" class="text-body d-block"><?= $ensemble["name"]; ?></a>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>

    </div>
  </div>

  <div class="modal modal-blur fade" id="add-new-ensemble" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New ensemble</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Ensemble name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="The Clarinet Ensemble" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Safe name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="the-clarinet-ensemble" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="mb-3">
                <label class="form-label">Admin emails</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="admin@example.com,another@example.com" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label">Image</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="https://ensemble.com/image.jpg" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add member
          </a>
        </div>
      </div>
    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_terms($max_height = 30)
{
  $db_connection = db_connect();

  require("./config.php");

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./terms.php">Terms</a></p>
      </h3>
      <div class="ms-auto">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#add-new-term">
          Add new
        </a>
      </div>
    </div>
    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      $terms = $db_connection->query("SELECT `ID`, `name`, `image` FROM `terms` ORDER BY `name` ASC");

      if ($terms->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No terms to display.</div>
            </div>
          </div>
        </div>
        <?php
      } else {
        while ($term = $terms->fetch_assoc()) {
        ?>
          <div class="list-group-item">
            <div class="row">
              <div class="col-auto">
                <a href="<?=$config["base_url"]."/term-dates.php?term_id=".$term["ID"];?>">
                  <?php
                  if ($term["image"] != "") {
                  ?>
                    <span class="avatar" style="background-image: url(<?= $term["image"]; ?>)"></span>
                  <?php
                  } else {
                  ?>
                    <span class="avatar"><?= $term["name"]; ?></span>
                  <?php
                  }
                  ?>
                </a>
              </div>
              <div class="col text-truncate">
                <a href="<?=$config["base_url"]."/term-dates.php?term_id=".$term["ID"];?>" class="text-body d-block"><?= $term["name"]; ?></a>
              </div>
            </div>
          </div>
      <?php
        }
      }
      ?>

    </div>
  </div>

  <div class="modal modal-blur fade" id="add-new-term" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New term</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Term name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="Summer 2022" autocomplete="off">
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Safe name</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="summer-2022" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Image</label>
                <div class="input-group input-group-flat">
                  <input type="text" class="form-control" value="" placeholder="https://ensemble.com/summer-logo.jpg" autocomplete="off">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
            Cancel
          </a>
          <a href="#" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <line x1="12" y1="5" x2="12" y2="19" />
              <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Add member
          </a>
        </div>
      </div>
    </div>
  </div>
<?php

  db_disconnect($db_connection);
}

function output_term_dates($term_id, $max_height = 30)
{
  $db_connection = db_connect();

  require("./config.php");

  $term_name_query = $db_connection->prepare("SELECT `name` FROM `terms` WHERE `ID` = ?");
  $term_name_query->bind_param("s", $term_id);
  $term_name_query->execute();
  $term_name_query->bind_result($term_name);
  $term_name_query->fetch();
  $term_name_query->close();

  $ensembles_query = $db_connection->prepare("SELECT `ID`, `name` FROM `ensembles`");
  $ensembles_query->execute();
  $ensembles_result = $ensembles_query->get_result();
  $ensembles_query->fetch();
  $ensembles_query->close();

  $ensemble_ids   = array();
  $ensemble_names = array();
  while($ensemble = $ensembles_result->fetch_assoc()) {
    array_push($ensemble_ids, $ensemble["ID"]);
    array_push($ensemble_names, $ensemble["name"]);
  }

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./term-dates.php">Term dates for <?= $term_name; ?></a></p>
      </h3>
      <div class="ms-auto">
        <a class="btn btn-outline-primary w-100" onclick="addNewDate()">
          Add new
        </a>
      </div>
    </div>
    <div class="list-group list-group-flush overflow-auto" style="max-height: <?= $max_height; ?>rem">
      <?php
      $term_dates_query = $db_connection->prepare("SELECT `ID`, `datetime`, `datetime_end`, `is_featured`, `deleted` FROM `term_dates` WHERE `term_ID`=? ORDER BY `datetime` ASC");
      $term_dates_query->bind_param("s", $term_id);
      $term_dates_query->execute();

      $term_dates_result = $term_dates_query->get_result();
      $number_of_dates = $term_dates_result->num_rows;

      if ($number_of_dates == 0) {
      ?>
        <div id="table-default" class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><button class="table-sort" data-sort="sort-modified"></button></th>
                <th><button class="table-sort" data-sort="sort-date">Date</button></th>
                <th><button class="table-sort" data-sort="sort-start-time">Start time</button></th>
                <th><button class="table-sort" data-sort="sort-end-time">End time</button></th>
                <th><button class="table-sort" data-sort="sort-featured"><?=$config["taxonomy_concert"];?></button></th>
                <th><button class="table-sort" data-sort="sort-hidden">Hide</button></th>
                <th>Duplicate</th>
                <th>Permanently delete</th>
              </tr>
            </thead>
            <tbody class="table-tbody">
              <tr>
                <td></td>
                <td colspan="7">
                  No dates to display.
                </td>
              </tr>
            </tbody>
          </table>
          <div class="p-2 my-0 d-flex">
            <p class="ms-auto m-0 text-muted">
              <span style="padding-right: 10px;">You've changed or added <span class="fieldsCounter fw-bold">0</span> fields over <span class="termDatesCounter fw-bold">0</span> term dates.</span>
              <a class="updateTermDates btn btn-primary ms-auto disabled my-2" onclick="updateTermDates()" data-bs-toggle="modal" data-bs-target="#update-term-dates-result">Update</a>
              <a class="btn my-2" onclick="location.reload()">Reset</a>
            </p>
          </div>
        </div>
      <?php
      } else {
      ?>
        <div id="table-default" class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th><button class="table-sort" data-sort="sort-modified"></button></th>
                <th><button class="table-sort" data-sort="sort-date">Date</button></th>
                <th><button class="table-sort" data-sort="sort-start-time">Start time</button></th>
                <th><button class="table-sort" data-sort="sort-end-time">End time</button></th>
                <th><button class="table-sort" data-sort="sort-featured"><?=$config["taxonomy_concert"];?></button></th>
                <th><button class="table-sort" data-sort="sort-hidden">Hide</button></th>
                <th>Duplicate</th>
                <th>Permanently delete</th>
              </tr>
            </thead>
            <tbody class="table-tbody">

              <?php

              $id_array = array();

              while ($term_date = $term_dates_result->fetch_assoc()) {

                $id         = $term_date['ID'];
                $date       = date("Y-m-d", $term_date['datetime']);
                $start_time = date("H:i", $term_date['datetime']);
                $end_time   = date("H:i", $term_date['datetime_end']);
                $featured   = $term_date['is_featured'];
                $hidden     = $term_date['deleted'];

                $data_date       = strtotime(date("Y-m-d", $term_date['datetime']));
                $data_start_time = strtotime(date("1970-01-01 H:i", $term_date['datetime']));
                $data_end_time   = strtotime(date("1970-01-01 H:i", $term_date['datetime_end']));
                $data_featured   = $term_date['is_featured'];
                $data_hidden    = $term_date['deleted'];

                $id_array[] = $id;

                $hidden_indeterminate = "";
                $hidden_checked       = ($hidden == 1)?"checked":"";
                $hidden_disabled      = "";

              ?>
                <tr id="row-<?=$id;?>">
                  <td id="modified-<?=$id;?>" class="col-auto align-self-center sort-modified" data-modified="0" style="text-align: center; vertical-align: middle;">
                    
                  </td>
                  <td class="col-auto sort-date" data-date="<?= $data_date; ?>">
                    <div class="input-icon">
                      <input type="text" name="date" id="date-<?=$id;?>" class="form-control" placeholder="Select a date" value="<?=$date;?>" style="min-width: 150px;">
                      <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="4" y="5" width="16" height="16" rx="2"></rect><line x1="16" y1="3" x2="16" y2="7"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="4" y1="11" x2="20" y2="11"></line><line x1="11" y1="15" x2="12" y2="15"></line><line x1="12" y1="15" x2="12" y2="18"></line></svg>
                      </span>
                    </div>
                  </td>
                  <td class="col-auto sort-start-time" data-start-time="<?= $data_start_time; ?>">
                    <input type="time" name="start-time" id="start-time-<?=$id;?>" class="form-control" autocomplete="off" value="<?=$start_time;?>" onchange="changedField(this, '<?=$id;?>', 'start-time')">
                  </td>
                  <td class="col-auto sort-end-time" data-end-time="<?= $data_end_time; ?>">
                    <input type="time" name="end-time" id="end-time-<?=$id;?>" class="form-control" autocomplete="off" value="<?=$end_time;?>" onchange="changedField(this, '<?=$id;?>', 'end-time')">
                  </td>
                  <td class="col-auto sort-featured" data-featured="<?=$featured;?>">
                    <select name="featured" id="featured-<?=$id;?>" class="form-select" onchange="changedField(this, '<?=$id;?>', 'featured')" style="width: 100px;">
                      <option value="0" <?=($featured==0)?"selected":"";?>>None</option>
                      <option value="1" <?=($featured==1)?"selected":"";?>>All</option>
                      <optgroup label="<?=ucfirst($config["taxonomy_ensembles"]);?>">
                        <?php                          
                          for ($i = 0; $i < count($ensemble_ids); $i++)
                          {
                            $options[strval(-$ensemble_ids[$i])] = $ensemble_names[$i];
                            ?>
                              <option value="-<?=$ensemble_ids[$i];?>" <?=(-$ensemble_ids[$i]==$featured)?"selected":"";?>><?=$ensemble_names[$i];?></option>
                            <?php
                          }
                          
                        ?>
                      </optgroup>
                    </select>
                  </td>
                  <td class="sort-hidden" data-hidden="<?= $data_hidden; ?>">
                    <label class="form-colorcheckbox bigger" style="margin: 0px;">
                      <input name="hidden" id="hidden-<?=$id;?>" type="checkbox" value="<?=$hidden;?>" class="form-colorcheckbox-input <?=$hidden_indeterminate;?>" <?=$hidden_checked;?> <?=$hidden_disabled;?> onchange="changedField(this, '<?=$id;?>', 'hidden')" />
                      <span class="form-colorcheckbox-color "></span>
                    </label>
                  </td>
                  <td class="col-auto align-self-center" style="text-align: center; vertical-align: middle;">
                    <button type="button" name="duplicate" id="duplicate-<?=$id;?>" class="btn btn-sm btn-outline-secondary" onclick="duplicateDate('<?=$id;?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="20" height="20" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="8" y="8" width="12" height="12" rx="2" />
                        <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
                      </svg>
                      Duplicate
                    </button>
                  </td>
                  <?php
                    $attendance_uses = $db_connection->query("SELECT COUNT(*) FROM `attendance` WHERE `term_dates_ID` = ".$id)->fetch_row()[0];
                  ?>
                  <td class="col-auto" style="text-align: left; vertical-align: middle;">
                    <button type="button" name="permanently-delete" id="permanently-delete-<?=$id;?>" class="btn btn-sm btn-danger <?=($attendance_uses==0)?"":"disabled";?>" data-bs-toggle="modal" data-bs-target="#delete-term-date-result" onclick="loadDeleteDateModal('<?=$id;?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />';</svg>
                      Delete (<?=$attendance_uses;?> uses)
                    </button>
                  </td>
                </tr>

                <!-- <div class="list-group-item">
                    <div class="row">
                      <div class="col text-truncate">
                        <span class="text-body d-block"><?= timestamp_range($term_date["datetime"], $term_date["datetime_end"]); ?></span>
                      </div>
                      <div class="col-auto">
                      <label class="form-colorinput">
                        <input id="featured" type="checkbox" class="form-colorinput-input" <?= ($term_date["is_featured"]) ? "checked" : ""; ?>>
                        <span class="form-colorinput-color bg-blue"></span>
                      </label>
                      <label class="form-colorinput">
                        <input id="hidden" type="checkbox" class="form-colorinput-input" <?= ($term_date["hidden"]) ? "checked" : ""; ?>>
                        <span class="form-colorinput-color bg-red"></span>
                      </label>
                      </div>
                    </div>
                  </div> -->
              <?php
              }
              ?>
            </tbody>
          </table>
          <div class="p-2 my-0 d-flex">
            <p class="ms-auto m-0 text-muted">
              <span style="padding-right: 10px;">You've changed or added <span class="fieldsCounter fw-bold">0</span> fields over <span class="termDatesCounter fw-bold">0</span> term dates.</span>
              <a class="updateTermDates btn btn-primary ms-auto disabled my-2" onclick="updateTermDates()" data-bs-toggle="modal" data-bs-target="#update-term-dates-result">Update</a>
              <a class="btn my-2" onclick="location.reload()">Reset</a>
            </p>
          </div>
        </div>
      <?php
      }

      $term_dates_query->close();
      ?>

    </div>
  </div>

  <div class="modal modal-blur fade" id="update-term-dates-result" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <button type="button" id="update-term-dates-result-close-button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div id="update-term-dates-result-status" class="modal-status bg-success"></div>
        <div class="modal-body text-center py-4">
          <div id="update-term-dates-result-icon">
            Result icon
          </div>
          <h3 id="update-term-dates-result-title">Result title</h3>
          <div class="text-muted" id="update-term-dates-result-text">Result text</div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col"><a id="update-term-dates-result-button" href="#" class="btn btn-success disabled w-100" data-bs-dismiss="modal">
                  Result button text
                </a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-blur fade" id="delete-term-date-result" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-status bg-danger"></div>
        <div class="modal-body text-center py-4">
          <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>
          <h3>Are you sure?</h3>
          <div class="text-muted">Do you really want to permanently delete this term date? What you've done cannot be undone.</div>
        </div>
        <div class="modal-body py-4" id="delete-term-date-result-info">
          <strong>ID</strong>: <br />
          <strong>Date</strong>: <br />
          <strong>Start time</strong>: <br />
          <strong>End time</strong>: <br />
          <strong>Concert</strong>: <br />
          <strong>Hidden</strong>: <br />
          <strong>Term ID</strong>: <br />
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col"><a class="btn w-100" data-bs-dismiss="modal">
                  Cancel
                </a></div>
              <div class="col"><a id="delete-term-date-result-button" name="0" class="btn btn-danger w-100" onclick="">
                  Delete term
                </a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="./dist/libs/list.js/dist/list.min.js"></script>
  <script src="./dist/libs/litepicker/dist/litepicker.js"></script>
  <?php
    foreach ($id_array as $id) {
      ?>
        <script>
          // @formatter:off
          document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
              element: document.getElementById('date-<?=$id;?>'),
              buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
              },
              setup: (picker) => {
                picker.on('selected', (date1) => {
                  var field = document.getElementById('date-<?=$id;?>');
                  changedField(field, '<?=$id;?>', 'date');
                });
              },
            }));
          });
          // @formatter:on
        </script>
      <?php
    }
  ?>
  <script type="text/javascript">
    list = new List('table-default', {
      sortClass: 'table-sort',
      listClass: 'table-tbody',
      valueNames: [
        {
          attr: 'data-modified',
          name: 'sort-modified'
        },
        {
          attr: 'data-date',
          name: 'sort-date'
        },
        {
          attr: 'data-start-time',
          name: 'sort-start-time'
        },
        {
          attr: 'data-end-time',
          name: 'sort-end-time'
        },
        {
          attr: 'data-featured',
          name: 'sort-featured'
        },
        {
          attr: 'data-hidden',
          name: 'sort-hidden'
        }
      ]
    });

    document.addEventListener("DOMContentLoaded", function() { list.reIndex(); });

    fieldsCounter    = 0;
    termDatesCounter = 0;

    function changedField(element, id, name)
    {
      // Mark rows as modified.
      if (name == "date")
      {
        if (!element.parentElement.parentElement.parentElement.classList.contains('row-modified'))
        {
          element.parentElement.parentElement.parentElement.classList.add('row-modified');
        }
      }
      else if (name == "start-time")
      {
        if (!element.parentElement.parentElement.classList.contains('row-modified'))
        {
          element.parentElement.parentElement.classList.add('row-modified');
        }
      }
      else if (name == "end-time")
      {
        if (!element.parentElement.parentElement.classList.contains('row-modified'))
        {
          element.parentElement.parentElement.classList.add('row-modified');
        }
      }
      else if (name == "hidden")
      {
        if (!element.parentElement.parentElement.parentElement.classList.contains('row-modified'))
        {
          element.parentElement.parentElement.parentElement.classList.add('row-modified');
        }
      }
      else if (name == "featured")
      {
        if (!element.parentElement.parentElement.classList.contains('row-modified'))
        {
          element.parentElement.parentElement.classList.add('row-modified');
        }
      }

      // Update counters.
      if (!element.classList.contains('value-changed'))
      {
        element.classList.add('value-changed');
        fieldsCounter += 1
      }

      modifiedField = document.getElementById("modified-" + id);

      if (id.substring(0, 3) != "new" && modifiedField.getAttribute("data-modified") != "1")
      {
        modifiedField.innerHTML = '<div class="badge bg-primary"></div>';
        modifiedField.setAttribute("data-modified", "1");
        termDatesCounter += 1;
      }

      fieldsCounterElement = document.getElementsByClassName("fieldsCounter")[0];
      fieldsCounterElement.innerHTML = fieldsCounter;

      termDatesCounterElement = document.getElementsByClassName("termDatesCounter")[0];
      termDatesCounterElement.innerHTML = termDatesCounter;

      updateTermDatesElement = document.getElementsByClassName("updateTermDates")[0];
      updateTermDatesElement.classList.remove("disabled");

      // Changes value of checkbox.
      if (name == "hidden")
      {
        element.value = (element.checked) ? "1" : "0";
      }

      // Updates table sorting values.
      if (name == "date")
      {
        var timestamp = Date.parse(element.value + " 00:00:00")/1000;
        element.parentElement.parentElement.setAttribute("data-" + name, timestamp);
      }
      else if (name == "start-time")
      {
        var timestamp = Date.parse("1970-01-01 " + element.value)/1000;
        element.parentElement.setAttribute("data-" + name, timestamp);
      }
      else if (name == "end-time")
      {
        var timestamp = Date.parse("1970-01-01 " + element.value)/1000;
        element.parentElement.setAttribute("data-" + name, timestamp);
      }
      else if (name == "hidden")
      {
        element.parentElement.parentElement.setAttribute("data-" + name, (element.checked) ? "1" : "0");
      }
      else if (name == "featured")
      {
        element.parentElement.setAttribute("data-" + name, element.value);
      }

      list.reIndex();
    }

    var newCounter = 0;
    var oldCounter = <?=$number_of_dates;?>;

    function addNewDate()
    {
      if (oldCounter == 0 && newCounter == 0)
      {
        newCounter += 1;

        rowInsert  = '<tr id="row-new'+newCounter+'" class="row-modified">';
        rowInsert += '  <td id="modified-new'+newCounter+'" class="col-auto align-self-center sort-modified" data-modified="0" style="text-align: center; vertical-align: middle;">';
        rowInsert += '    <div class="badge bg-green"></div>';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto sort-date" data-date="0">';
        rowInsert += '    <div class="input-icon">';
        rowInsert += '      <input type="text" name="date" id="date-new'+newCounter+'" class="form-control" placeholder="Select a date" value="" style="min-width: 150px;">';
        rowInsert += '      <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->';
        rowInsert += '        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><rect x="4" y="5" width="16" height="16" rx="2"></rect><line x1="16" y1="3" x2="16" y2="7"></line><line x1="8" y1="3" x2="8" y2="7"></line><line x1="4" y1="11" x2="20" y2="11"></line><line x1="11" y1="15" x2="12" y2="15"></line><line x1="12" y1="15" x2="12" y2="18"></line></svg>';
        rowInsert += '      </span>';
        rowInsert += '    </div>';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto sort-start-time" data-start-time="0">';
        rowInsert += '    <input type="time" name="start-time" id="start-time-new'+newCounter+'" class="form-control" autocomplete="off" value="" onchange="changedField(this, \'new'+newCounter+'\', \'start-time\')">';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto sort-end-time" data-end-time="0">';
        rowInsert += '    <input type="time" name="end-time" id="end-time-new'+newCounter+'" class="form-control" autocomplete="off" value="" onchange="changedField(this, \'new'+newCounter+'\', \'end-time\')">';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto sort-featured" data-featured="0">';
        rowInsert += '    <select name="featured" id="featured-new'+newCounter+'" class="form-select" onchange="changedField(this, new'+newCounter+', featured)" style="width: 100px;">';
        rowInsert += '      <option value="0" selected>None</option>';
        rowInsert += '      <option value="1">All</option>';
        rowInsert += '      <optgroup label="<?=ucfirst($config["taxonomy_ensembles"]);?>">';
                          <?php
                            for ($i = 0; $i < count($ensemble_ids); $i++)
                            {
                              $options[strval(-$ensemble_ids[$i])] = $ensemble_names[$i];
                              ?>
        rowInsert += '           <option value="-<?=$ensemble_ids[$i];?>" ><?=$ensemble_names[$i];?></option>';
                              <?php
                            }
                          ?>
        rowInsert += '      </optgroup>';
        rowInsert += '    </select>';
        rowInsert += '  </td>';
        rowInsert += '  <td class="sort-hidden" data-hidden="-1">';
        rowInsert += '    <label class="form-colorcheckbox bigger" style="margin: 0px;">';
        rowInsert += '      <input name="hidden" id="hidden-new'+newCounter+'" type="checkbox" value="-1" class="form-colorcheckbox-input" onchange="changedField(this, \'new'+newCounter+'\', \'hidden\')" />';
        rowInsert += '      <span class="form-colorcheckbox-color "></span>';
        rowInsert += '    </label>';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto align-self-center" style="text-align: center; vertical-align: middle;">';
        rowInsert += '    <button type="button" name="duplicate" id="duplicate-new'+newCounter+'" class="btn btn-sm btn-outline-secondary" onclick="duplicateDate(\'new'+newCounter+'\')">';
        rowInsert += '      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-copy" width="20" height="20" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">';
        rowInsert += '        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>';
        rowInsert += '        <rect x="8" y="8" width="12" height="12" rx="2" />';
        rowInsert += '        <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />';
        rowInsert += '      </svg>';
        rowInsert += '      Duplicate';
        rowInsert += '    </button>';
        rowInsert += '  </td>';
        rowInsert += '  <td class="col-auto" style="text-align: left; vertical-align: middle;">';
        rowInsert += '    <button type="button" name="permanently-delete" id="permanently-delete-new'+newCounter+'" class="btn btn-sm btn-danger " onclick="deleteLocalDate(\'new'+newCounter+'\')">';
        rowInsert += '      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>';
        rowInsert += '      Delete';
        rowInsert += '    </button>';
        rowInsert += '  </td>';
        rowInsert += '</tr>';

        var table          = document.getElementById("table-default");
        var tbody          = table.getElementsByTagName("tbody")[0];
        var placeholderRow = tbody.rows[0];
        
        placeholderRow.remove();

        tbody.innerHTML = rowInsert;

        document.getElementById("hidden-new"+newCounter).indeterminate = true;

        // Add event listener for the date cell.
        window.Litepicker && (new Litepicker({
          element: document.getElementById('date-new'+newCounter),
          buttonText: {
            previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
            nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
          },
          setup: (picker) => {
            picker.on('selected', (date1) => {
              var field = document.getElementById('date-new'+newCounter);
              changedField(field, 'new'+newCounter, 'date');
            });
          },
        }));

        list = new List('table-default', {
          sortClass: 'table-sort',
          listClass: 'table-tbody',
          valueNames: [
            {
              attr: 'data-modified',
              name: 'sort-modified'
            },
            {
              attr: 'data-date',
              name: 'sort-date'
            },
            {
              attr: 'data-start-time',
              name: 'sort-start-time'
            },
            {
              attr: 'data-end-time',
              name: 'sort-end-time'
            },
            {
              attr: 'data-featured',
              name: 'sort-featured'
            },
            {
              attr: 'data-hidden',
              name: 'sort-hidden'
            }
          ]
        });
      }
      else
      {
        list.add({sort_modified: 0, sort_date: 0, sort_start_time: 0, sort_end_time: 0, sort_featured: 0, sort_hidden: 0});

        newCounter += 1;
        termDatesCounter += 1;

        fieldsCounterElement = document.getElementsByClassName("fieldsCounter")[0];
        fieldsCounterElement.innerHTML = fieldsCounter;

        termDatesCounterElement = document.getElementsByClassName("termDatesCounter")[0];
        termDatesCounterElement.innerHTML = termDatesCounter;

        var table   = document.getElementById("table-default");
        var tbody   = table.getElementsByTagName("tbody")[0];
        var lastRow = tbody.rows[tbody.rows.length - 1];

        lastRow.id = "row-new" + newCounter;

        var modifiedCell  = lastRow.cells[0];
        var dateCell      = lastRow.cells[1];
        var startTimeCell = lastRow.cells[2];
        var endTimeCell   = lastRow.cells[3];
        var featuredCell  = lastRow.cells[4];
        var hiddenCell    = lastRow.cells[5];
        var duplicateCell = lastRow.cells[6];
        var deleteCell    = lastRow.cells[7];

        // // Update IDs.
        // modifiedCell.id  = "modified-new"   + newCounter;
        // dateCell.id      = "date-new"       + newCounter;
        // startTimeCell.id = "start-time-new" + newCounter;
        // endTimeCell.id   = "end-time-new"   + newCounter;
        // featuredCell.id  = "featured-new"   + newCounter;
        // hiddenCell.id   = "hidden-new"    + newCounter;
        // duplicateCell.id = "duplicate-new"  + newCounter;
        
        // // Update names.
        // modifiedCell.name  = "modified-new"   + newCounter;
        // dateCell.name      = "date-new"       + newCounter;
        // startTimeCell.name = "start-time-new" + newCounter;
        // endTimeCell.name   = "end-time-new"   + newCounter;
        // featuredCell.name  = "featured-new"   + newCounter;
        // hiddenCell.name   = "hidden-new"    + newCounter;
        // duplicateCell.name = "duplicate-new"  + newCounter;

        // Mark as new field.
        lastRow.classList.add('row-modified');

        // Update modified cell.
        modifiedCell.innerHTML = '<div class="badge bg-green"></div>';

        // Update date cell.
        dateCell.setAttribute("data-date", "0");
        dateCell.getElementsByTagName('input')[0].id       = "date-new" + newCounter;
        dateCell.getElementsByTagName('input')[0].name     = "date";
        dateCell.getElementsByTagName('input')[0].value    = "";
        dateCell.getElementsByTagName('input')[0].onchange = function() { changedField(dateCell.getElementsByTagName('input')[0], "new" + newCounter, "date"); };

        // Update start time cell.
        startTimeCell.setAttribute("data-start-time", "0");
        startTimeCell.getElementsByTagName('input')[0].id       = "start-time-new" + newCounter;
        startTimeCell.getElementsByTagName('input')[0].name     = "start-time";
        startTimeCell.getElementsByTagName('input')[0].value    = "";
        startTimeCell.getElementsByTagName('input')[0].onchange = function() { changedField(startTimeCell.getElementsByTagName('input')[0], "new" + newCounter, "start-time"); };

        // Update end time cell.
        endTimeCell.setAttribute("data-end-time", "0");
        endTimeCell.getElementsByTagName('input')[0].id       = "end-time-new" + newCounter;
        endTimeCell.getElementsByTagName('input')[0].name     = "end-time";
        endTimeCell.getElementsByTagName('input')[0].value    = "";
        endTimeCell.getElementsByTagName('input')[0].onchange = function() { changedField(endTimeCell.getElementsByTagName('input')[0], "new" + newCounter, "end-time"); };

        // Update featured cell.
        featuredCell.setAttribute("data-featured", "0");
        featuredCell.getElementsByTagName('select')[0].id       = "featured-new" + newCounter;
        featuredCell.getElementsByTagName('select')[0].name     = "featured";
        featuredCell.getElementsByTagName('select')[0].value    = "0";
        featuredCell.getElementsByTagName('select')[0].onchange = function () { changedField(featuredCell.getElementsByTagName('selected')[0], "new" + newCounter, "featured"); };

        // Update hidden cell.
        hiddenCell.setAttribute("data-hidden", "-1");
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].id            = "hidden-new" + newCounter;
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].name          = "hidden";
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].indeterminate = true;
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].checked       = false;
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].value         = -1;
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].onchange      = function () { changedField(hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0], "new" + newCounter, "hidden"); };

        // Update duplicate button cell.
        duplicateCell.getElementsByTagName('button')[0].id      = "duplicate-new" + newCounter;
        duplicateCell.getElementsByTagName('button')[0].name    = "duplicate";
        duplicateCell.getElementsByTagName('button')[0].onclick = function() { duplicateDate('new' + duplicateCell.getElementsByTagName('button')[0].id.substr(13)); };

        // Update delete button cell.
        deleteCell.getElementsByTagName('button')[0].id         = "delete-new" + newCounter;
        deleteCell.getElementsByTagName('button')[0].name       = "delete";
        deleteCell.getElementsByTagName('button')[0].onclick    = function() { deleteLocalDate('new' + deleteCell.getElementsByTagName('button')[0].id.substr(10)); };
        deleteCell.getElementsByTagName('button')[0].innerHTML  = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>';
        deleteCell.getElementsByTagName('button')[0].innerHTML += 'Delete';
        deleteCell.getElementsByTagName('button')[0].classList.remove('disabled');
        deleteCell.getElementsByTagName('button')[0].removeAttribute('data-bs-target');
        deleteCell.getElementsByTagName('button')[0].removeAttribute('data-bs-toggle');

        // Add event listener for the date cell.
        window.Litepicker && (new Litepicker({
          element: document.getElementById('date-new'+newCounter),
          buttonText: {
            previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
            nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
          },
          setup: (picker) => {
            picker.on('selected', (date1) => {
              var field = document.getElementById('date-new'+newCounter);
              changedField(field, 'new'+newCounter, 'date');
            });
          },
        }));

        list.reIndex();
      }
    }

    function duplicateDate(duplicateId)
    {
      list.add({sort_modified: 0, sort_date: 0, sort_start_time: 0, sort_end_time: 0, sort_featured: 0, sort_hidden: 0});

      newCounter += 1;
      termDatesCounter += 1;

      fieldsCounterElement = document.getElementsByClassName("fieldsCounter")[0];
      fieldsCounterElement.innerHTML = fieldsCounter;

      termDatesCounterElement = document.getElementsByClassName("termDatesCounter")[0];
      termDatesCounterElement.innerHTML = termDatesCounter;

      var table   = document.getElementById("table-default");
      var tbody   = table.getElementsByTagName("tbody")[0];
      var lastRow = tbody.rows[tbody.rows.length - 1];

      lastRow.id = "row-new" + newCounter;

      var modifiedCell  = lastRow.cells[0];
      var dateCell      = lastRow.cells[1];
      var startTimeCell = lastRow.cells[2];
      var endTimeCell   = lastRow.cells[3];
      var featuredCell  = lastRow.cells[4];
      var hiddenCell    = lastRow.cells[5];
      var duplicateCell = lastRow.cells[6];
      var deleteCell    = lastRow.cells[7];

      // // Update IDs.
      // modifiedCell.id  = "modified-new"   + newCounter;
      // dateCell.id      = "date-new"       + newCounter;
      // startTimeCell.id = "start-time-new" + newCounter;
      // endTimeCell.id   = "end-time-new"   + newCounter;
      // featuredCell.id  = "featured-new"   + newCounter;
      // hiddenCell.id   = "hidden-new"    + newCounter;
      
      // // Update names.
      // modifiedCell.name  = "modified-new"   + newCounter;
      // dateCell.name      = "date-new"       + newCounter;
      // startTimeCell.name = "start-time-new" + newCounter;
      // endTimeCell.name   = "end-time-new"   + newCounter;
      // featuredCell.name  = "featured-new"   + newCounter;
      // hiddenCell.name   = "hidden-new"    + newCounter;

      // Mark as new field.
      lastRow.classList.add('row-modified');

      // Update modified cell.
      modifiedCell.innerHTML = '<div class="badge bg-green"></div>';

      // Update date cell.
      dateCell.setAttribute("data-date", document.getElementById('date-' + duplicateId).parentElement.parentElement.getAttribute("data-date"));
      dateCell.getElementsByTagName('input')[0].id       = "date-new" + newCounter;
      dateCell.getElementsByTagName('input')[0].name     = "date";
      dateCell.getElementsByTagName('input')[0].value    = document.getElementById('date-' + duplicateId).value;
      dateCell.getElementsByTagName('input')[0].onchange = function () { changedField(dateCell.getElementsByTagName('input')[0], "new" + newCounter, "date"); };

      // Update start time cell.
      startTimeCell.setAttribute("data-start-time", document.getElementById('start-time-' + duplicateId).parentElement.getAttribute("data-start-time"));
      startTimeCell.getElementsByTagName('input')[0].id       = "start-time-new" + newCounter;
      startTimeCell.getElementsByTagName('input')[0].name     = "start-time";
      startTimeCell.getElementsByTagName('input')[0].value    = document.getElementById('start-time-' + duplicateId).value;
      startTimeCell.getElementsByTagName('input')[0].onchange = function () { changedField(startTimeCell.getElementsByTagName('input')[0], "new" + newCounter, "start-time"); };

      // Update end time cell.
      endTimeCell.setAttribute("data-end-time", document.getElementById('end-time-' + duplicateId).parentElement.getAttribute("data-end-time"));
      endTimeCell.getElementsByTagName('input')[0].id       = "end-time-new" + newCounter;
      endTimeCell.getElementsByTagName('input')[0].name     = "end-time";
      endTimeCell.getElementsByTagName('input')[0].value    = document.getElementById('end-time-' + duplicateId).value;
      endTimeCell.getElementsByTagName('input')[0].onchange = function () { changedField(endTimeCell.getElementsByTagName('input')[0], "new" + newCounter, "end-time"); };

      // Update featured cell.
      featuredCell.setAttribute("data-featured", document.getElementById('featured-' + duplicateId).parentElement.getAttribute("data-featured"));
      featuredCell.getElementsByTagName('select')[0].id       = "featured-new" + newCounter;
      featuredCell.getElementsByTagName('select')[0].name     = "featured";
      featuredCell.getElementsByTagName('select')[0].value    = document.getElementById('featured-' + duplicateId).value;
      featuredCell.getElementsByTagName('select')[0].onchange = function () { changedField(featuredCell.getElementsByTagName('select')[0], "new" + newCounter, "featured"); };

      // Update hidden cell.
      if (document.getElementById('hidden-' + duplicateId).parentElement.parentElement.getAttribute("data-hidden") == -1)
      {
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].indeterminate = true;
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].checked       = false;
      }
      else
      {
        hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].checked  = document.getElementById('hidden-' + duplicateId).checked;
      }
      hiddenCell.setAttribute("data-hidden", document.getElementById('hidden-' + duplicateId).parentElement.parentElement.getAttribute("data-hidden"));
      hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].id            = "hidden-new" + newCounter;
      hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].name          = "hidden";
      hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].value         = document.getElementById('hidden-' + duplicateId).value;
      hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0].onchange      = function () { changedField(hiddenCell.getElementsByTagName('label')[0].getElementsByTagName('input')[0], "new" + newCounter, "hidden"); };

      // Update duplicate button cell.
      duplicateCell.getElementsByTagName('button')[0].id      = "duplicate-new" + newCounter;
      duplicateCell.getElementsByTagName('button')[0].name    = "duplicate";
      duplicateCell.getElementsByTagName('button')[0].onclick = function() { duplicateDate('new' + duplicateCell.getElementsByTagName('button')[0].id.substr(13)); };

      // Update delete button cell.
      deleteCell.getElementsByTagName('button')[0].id         = "delete-new" + newCounter;
      deleteCell.getElementsByTagName('button')[0].name       = "delete";
      deleteCell.getElementsByTagName('button')[0].onclick    = function() { deleteLocalDate('new' + deleteCell.getElementsByTagName('button')[0].id.substr(10)); };
      deleteCell.getElementsByTagName('button')[0].innerHTML  = '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="4" y1="7" x2="20" y2="7" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>';
      deleteCell.getElementsByTagName('button')[0].innerHTML += 'Delete';
      deleteCell.getElementsByTagName('button')[0].classList.remove('disabled');
      deleteCell.getElementsByTagName('button')[0].removeAttribute('data-bs-target');
      deleteCell.getElementsByTagName('button')[0].removeAttribute('data-bs-toggle');

      // Add event listener for the date cell.
      window.Litepicker && (new Litepicker({
        element: document.getElementById('date-new'+newCounter),
        buttonText: {
          previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
          nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
        },
        setup: (picker) => {
          picker.on('selected', (date1) => {
            var field = document.getElementById('date-new'+newCounter);
            changedField(field, 'new'+newCounter, 'date');
          });
        },
      }));

      list.reIndex();
    }

    function updateTermDates()
    {
      document.getElementById("update-term-dates-result-title").innerHTML = "Updating...";
      document.getElementById("update-term-dates-result-text").innerHTML = "Please wait.";
      document.getElementById("update-term-dates-result-icon").innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
      document.getElementById("update-term-dates-result-button").innerHTML = "Loading...";
      document.getElementById("update-term-dates-result-button").classList.add("disabled");
      document.getElementById("update-term-dates-result-button").classList.remove("btn-danger");
      document.getElementById("update-term-dates-result-button").classList.remove("btn-success");
      document.getElementById("update-term-dates-result-button").classList.add("btn-primary");
      document.getElementById("update-term-dates-result-status").classList.remove("bg-danger");
      document.getElementById("update-term-dates-result-status").classList.remove("bg-success");
      document.getElementById("update-term-dates-result-status").classList.add("bg-primary");

      var xhttp = new XMLHttpRequest();

      xhttp.open("POST", "<?=$config['base_url'];?>/api/v1/update_term-dates.php", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      var modified_term_dates_rows = document.getElementsByClassName("row-modified");
      var extracted_term_dates_data = [];
      for (let i = 0; i < modified_term_dates_rows.length; i++) {
        const row = modified_term_dates_rows[i];

        var id = row.id.substr(4);

        var extracted_row_data = {};
        extracted_row_data["id"] = id;

        var modified_term_dates_data = row.getElementsByTagName("input");
        for (let j = 0; j < modified_term_dates_data.length; j++) {
          const inputElement = modified_term_dates_data[j];

          var name  = inputElement.name;
          var value = inputElement.value;

          extracted_row_data[name] = value;
        }
        console.log(extracted_row_data);
        extracted_row_data["featured"] = document.getElementById("featured-" + id).value;

        extracted_term_dates_data.push(extracted_row_data);
      }

      xhttp.send("term_dates_data=" + JSON.stringify(extracted_term_dates_data) + "&term_ID=<?=$term_id;?>" + "&session_ID=<?=$_COOKIE["session_ID"];?>");

      xhttp.onload = function()
      {
        const JSON_response = JSON.parse(this.responseText);

        if (JSON_response.status == "success") {
          document.getElementById("update-term-dates-result-title").innerHTML = "Success!";
          document.getElementById("update-term-dates-result-text").innerHTML = "You changed or added " + fieldsCounter + " fields over " + termDatesCounter + " term dates.";
          document.getElementById("update-term-dates-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>';
          document.getElementById("update-term-dates-result-button").innerHTML = "Great!";
          document.getElementById("update-term-dates-result-button").classList.remove("disabled");
          document.getElementById("update-term-dates-result-button").classList.remove("btn-danger");
          document.getElementById("update-term-dates-result-button").classList.remove("btn-primary");
          document.getElementById("update-term-dates-result-button").classList.add("btn-success");
          document.getElementById("update-term-dates-result-status").classList.remove("bg-danger");
          document.getElementById("update-term-dates-result-status").classList.remove("bg-primary");
          document.getElementById("update-term-dates-result-status").classList.add("bg-success");

          document.getElementById("update-term-dates-result-close-button").onclick = function() { window.location.reload(); };
          document.getElementById("update-term-dates-result-button").onclick = function() { window.location.reload(); };
        }
        else {
          document.getElementById("update-term-dates-result-title").innerHTML = "Oops! An error occured.";
          document.getElementById("update-term-dates-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-red icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
          document.getElementById("update-term-dates-result-text").innerHTML = "Are you sure all of the fields have valid values?<br/><br/>Error message: " + JSON_response.error_message;
          document.getElementById("update-term-dates-result-button").innerHTML = "Understood.";
          document.getElementById("update-term-dates-result-button").classList.remove("disabled");
          document.getElementById("update-term-dates-result-button").classList.remove("btn-success");
          document.getElementById("update-term-dates-result-button").classList.remove("btn-primary");
          document.getElementById("update-term-dates-result-button").classList.add("btn-danger");
          document.getElementById("update-term-dates-result-status").classList.remove("bg-success");
          document.getElementById("update-term-dates-result-status").classList.remove("bg-primary");
          document.getElementById("update-term-dates-result-status").classList.add("bg-danger");
        }
      }
    }

    function loadDeleteDateModal(id)
    {
      var xhttp = new XMLHttpRequest();

      xhttp.open("POST", "<?=$config['base_url'];?>/api/v1/get_term-date-details.php", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      xhttp.send("term_date_ID=" + id + "&session_ID=<?=$_COOKIE["session_ID"];?>");

      xhttp.onload = function() {
        const JSON_response = JSON.parse(this.responseText);

        if (JSON_response.status == "success")
        {
          var start_datetime = new Date(JSON_response.datetime     * 1000);
          var end_datetime   = new Date(JSON_response.datetime_end * 1000);

          var start_datetime_formatted = start_datetime.getFullYear() + "-" + ('0' + start_datetime.getMonth() + 1).substr(-2) + "-" + ('0' + start_datetime.getDate()).substr(-2) + " " + ('0' + start_datetime.getHours()).substr(-2) + ":" + ('0' + start_datetime.getMinutes()).substr(-2)
          var end_datetime_formatted   = end_datetime.getFullYear()   + "-" + ('0' + end_datetime.getMonth()   + 1).substr(-2) + "-" + ('0' + end_datetime.getDate())  .substr(-2)   + " " + ('0' + end_datetime.getHours()).substr(-2)   + ":" + ('0' + end_datetime.getMinutes()).substr(-2)

          document.getElementById("delete-term-date-result-info").innerHTML  = "";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong>ID</strong>: "                                         + JSON_response.id           + "<br />";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong>Start datetime</strong>: "                             + start_datetime_formatted   + "<br />";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong>End datetime</strong>: "                               + end_datetime_formatted     + "<br />";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong><?=ucfirst($config["taxonomy_concert"]);?></strong>: " + JSON_response.is_featured  + "<br />";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong>Hidden</strong>: "                                     + JSON_response.deleted      + "<br />";
          document.getElementById("delete-term-date-result-info").innerHTML += "<strong>Term ID</strong>: "                                    + JSON_response.term_id      + "<br />";
        }
        else
        {
          document.getElementById("delete-term-date-result-info").innerHTML = "Error loading term date data: " + JSON_response.error_message;
        }
      }

      document.getElementById("delete-term-date-result-button").name    = id;
      document.getElementById("delete-term-date-result-button").onclick = function() { deleteDate(document.getElementById("delete-term-date-result-button").name); };
    }

    function deleteDate(id)
    {
      var xhttp = new XMLHttpRequest();

      xhttp.open("POST", "<?=$config['base_url'];?>/api/v1/remove_term-date.php", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      xhttp.send("term_date_ID=" + id + "&session_ID=<?=$_COOKIE["session_ID"];?>");

      xhttp.onload = function() {
        const JSON_response = JSON.parse(this.responseText);

        if (JSON_response.status == "success")
        {
          location.reload();
        }
        else
        {
          document.getElementById("delete-term-date-result-info").innerHTML = "Oops! An error occured: " + JSON_response.error_message;
        }
      }
    }

    function deleteLocalDate(id)
    {
      document.getElementById("row-" + id).remove();
      list.reIndex();

      var rows   = document.getElementById("table-default").getElementsByTagName("tr");
      var noRows = rows.length - 1; // Minus headings.

      console.log(noRows);

      if (noRows == 0)
      {
        document.getElementById("table-default").getElementsByTagName("tbody")[0].innerHTML = "<tr><td></td><td colspan=\"7\">No term dates found.</td></tr>";
      }
    }
  </script>
<?php

  db_disconnect($db_connection);
}
?>