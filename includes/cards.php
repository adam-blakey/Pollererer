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
                <a href="#">
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
                <a href="#" class="text-body d-block"><?= $term["name"]; ?></a>
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

  $term_name_query = $db_connection->prepare("SELECT `name` FROM `terms` WHERE `ID` = ?");
  $term_name_query->bind_param("s", $term_id);
  $term_name_query->execute();
  $term_name_query->bind_result($term_name);
  $term_name_query->fetch();
  $term_name_query->close();

?>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <p><a href="./term-dates.php">Term dates for <?= $term_name; ?></a></p>
      </h3>
      <div class="ms-auto">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#add-new-term">
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

      if ($term_dates_result->num_rows == 0) {
      ?>
        <div class="list-group-item">
          <div class="row">
            <div class="col">
              <div class="text-body">No term dates to display.</div>
            </div>
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
                <th><button class="table-sort" data-sort="sort-featured">Featured?</button></th>
                <th><button class="table-sort" data-sort="sort-deleted">Deleted?</button></th>
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
                $deleted    = $term_date['deleted'];

                $data_date       = strtotime(date("Y-m-d", $term_date['datetime']));
                $data_start_time = strtotime(date("1970-01-01 H:i", $term_date['datetime']));
                $data_end_time   = strtotime(date("1970-01-01 H:i", $term_date['datetime_end']));
                $data_featured   = $term_date['is_featured'];
                $data_deleted    = $term_date['deleted'];

                $id_array[] = $id;

                $featured_indeterminate = "";
                $featured_checked       = ($featured == 1)?"checked":"";
                $featured_disabled      = "";

                $deleted_indeterminate = "";
                $deleted_checked       = ($deleted == 1)?"checked":"";
                $deleted_disabled      = "";

              ?>
                <tr>
                  <td id="modified-<?=$id;?>" class="col-auto align-self-center sort-modified" data-modified="0" style="text-align: center; vertical-align: middle;">
                    
                  </td>
                  <td class="col-auto sort-date" data-date="<?= $data_date; ?>">
                    <div class="input-icon">
                      <input type="text" name="date-<?=$id;?>" class="form-control" placeholder="Select a date" id="datepicker-<?=$id;?>" value="<?=$date;?>">
                    </div>
                  </td>
                  <td class="col-auto sort-start-time" data-start-time="<?= $data_start_time; ?>">
                    <input type="time" name="start-time-<?=$id;?>" class="form-control" autocomplete="off" value="<?=$start_time;?>" onchange="changedField(this, <?=$id;?>, 'start-time')">
                  </td>
                  <td class="col-auto sort-end-time" data-end-time="<?= $data_end_time; ?>">
                    <input type="time" name="end-time-<?=$id;?>" class="form-control" autocomplete="off" value="<?=$end_time;?>" onchange="changedField(this, <?=$id;?>, 'end-time')">
                  </td>
                  <td class="sort-featured" data-featured="<?= $data_featured; ?>">
                    <label class="form-colorcheckbox bigger" style="margin: 0px;">
                      <input name="featured-<?=$id;?>" type="checkbox" value="lime" class="form-colorcheckbox-input <?=$featured_indeterminate;?>" <?=$featured_checked;?> <?=$featured_disabled;?> onchange="changedField(this, <?=$id;?>, 'featured')" />
                      <span class="form-colorcheckbox-color "></span>
                    </label>
                  </td>
                  <td class="sort-deleted" data-deleted="<?= $data_deleted; ?>">
                    <label class="form-colorcheckbox bigger" style="margin: 0px;">
                      <input name="deleted-<?=$id;?>" type="checkbox" value="lime" class="form-colorcheckbox-input <?=$deleted_indeterminate;?>" <?=$deleted_checked;?> <?=$deleted_disabled;?> onchange="changedField(this, <?=$id;?>, 'deleted')" />
                      <span class="form-colorcheckbox-color "></span>
                    </label>
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
                        <input id="deleted" type="checkbox" class="form-colorinput-input" <?= ($term_date["deleted"]) ? "checked" : ""; ?>>
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
        </div>
      <?php
      }

      $term_dates_query->close();
      ?>

    </div>
  </div>

  <div class="modal modal-blur fade" id="add-new-term" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">New term date</h5>
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
  <script src="./dist/libs/list.js/dist/list.min.js"></script>
  <script src="./dist/libs/litepicker/dist/litepicker.js"></script>
  <?php
    foreach ($id_array as $id) {
      ?>
        <script>
          // @formatter:off
          document.addEventListener("DOMContentLoaded", function() {
            window.Litepicker && (new Litepicker({
              element: document.getElementById('datepicker-<?=$id;?>'),
              buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
              },
              setup: (picker) => {
                picker.on('selected', (date1) => {
                  var field = document.getElementById('datepicker-<?=$id;?>');
                  changedField(field, <?=$id;?>, 'date');
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
    const list = new List('table-default', {
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
          attr: 'data-deleted',
          name: 'sort-deleted'
        }
      ]
    });

    document.addEventListener("DOMContentLoaded", function() { list.reIndex(); });

    function changedField(element, id, name)
    {
      if (!element.classList.contains('value-changed'))
      {
        element.classList.add('value-changed');
      }

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
      else if (name == "deleted" || name == "featured")
      {
        element.parentElement.parentElement.setAttribute("data-" + name, (element.checked) ? "1" : "0");
      }

      modifiedField = document.getElementById("modified-" + id);

      modifiedField.innerHTML = '<div class="badge bg-primary"></div>';
      modifiedField.setAttribute("data-modified", "1");

      list.reIndex();
    }      
  </script>
<?php

  db_disconnect($db_connection);
}
?>