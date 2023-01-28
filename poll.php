<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta5
* @link https://tabler.io
* Copyright 2018-2022 The Tabler Authors
* Copyright 2018-2022 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/kernel.php"); ?>
<?php $db_connection = db_connect(); ?>

<?php
  $attendance_select_sortby    = isset($_GET["sortby"])?$_GET["sortby"]:"";
  $attendance_select_direction = isset($_GET["sortdir"])?$_GET["sortdir"]:"";
  $theme                       = isset($_GET["theme"])?$_GET["theme"]:"";

  if (!in_array($attendance_select_sortby, array("first_name", "last_name", "datetime", "instrument")))
  {
    $attendance_select_sortby = "last_name";
  }

  if (!in_array($attendance_select_direction, array("ASC", "DESC")))
  {
    $attendance_select_direction = "ASC";
  }

  if (isset($_GET["ensemble_name"]) and isset($_GET["term_name"]))
  {
    $ensemble_ID_query = $db_connection->prepare("SELECT `ID` FROM `ensembles` WHERE `safe_name`=? LIMIT 1");
    $ensemble_ID_query ->bind_param("s", $_GET["ensemble_name"]);
    $ensemble_ID_query ->execute();
    $ensemble_ID_query ->bind_result($ensemble_ID);
    $ensemble_ID_query ->fetch();
    $ensemble_ID_query ->close();

    $term_ID_query = $db_connection->prepare("SELECT `ID` FROM `terms` WHERE `safe_name`=? LIMIT 1");
    $term_ID_query ->bind_param("s", $_GET["term_name"]);
    $term_ID_query ->execute();
    $term_ID_query ->bind_result($term_ID);
    $term_ID_query ->fetch();
    $term_ID_query ->close();
  }
  else
  {
    $ensemble_ID = (isset($_GET["ensemble_ID"]))?intval($_GET["ensemble_ID"]):0;
    $term_ID     = (isset($_GET["term_ID"]))?intval($_GET["term_ID"]):0;
  }
?>

<?php
  $term_name = $db_connection->query("SELECT `name` FROM `terms` WHERE `ID`=".$term_ID." LIMIT 1")->fetch_array()[0];

  $ensemble_name = $db_connection->query("SELECT `name` FROM `ensembles` WHERE `ID`=".$ensemble_ID." LIMIT 1")->fetch_array()[0];

  $ensemble_safe_name = $db_connection->query("SELECT `safe_name` FROM `ensembles` WHERE `ID`=".$ensemble_ID." LIMIT 1")->fetch_array()[0];

  if ($term_name == NULL)
  {
    $title = "Unknown term";
  }
  else if ($ensemble_name == NULL)
  {
    $title = "Unknown ".$config["taxonomy_ensemble"];
  }
  else
  {
    //$title = $ensemble_name." Rehearsals: ".$term_name;
    $title = $ensemble_name." ".ucfirst($config["taxonomy_rehearsals"]).": ".$term_name;
  }

  $term_date_counter = [];
  $term_date_counter_intederminate = [];

  $headings_printed = false;
?>

<?php
  if (login_valid() and login_restricted(-$ensemble_ID))
  {
    ?>
    <html lang="en">
      <head>
        <?php include($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
        <meta name="robots" content="noindex,nofollow">
        <title><?=$title;?></title>
        <script type="text/javascript">
          var attendanceCounter = 0;
          var memberCounter     = 0;

          function updateTotalChanged(element)
          {
            if (!element.classList.contains('attendance-changed'))
            {
              attendanceCounter++;
              element.classList.add('attendance-changed');
            }

            if (!element.parentElement.parentElement.parentElement.parentElement.classList.contains('attendance-changed-member'))
            {
              memberCounter++;
              element.parentElement.parentElement.parentElement.parentElement.classList.add('attendance-changed-member');
            }

            var attendanceCounters = document.getElementsByClassName('attendanceCounter');
            for (var i=0; i<attendanceCounters.length; i++)
            {
              attendanceCounters[i].innerHTML = attendanceCounter;
            }
            var memberCounters = document.getElementsByClassName('memberCounter');
            for (var i=0; i<memberCounters.length; i++)
            {
              memberCounters[i].innerHTML = memberCounter;
            }
            var updateAttendances = document.getElementsByClassName('updateAttendance');
            for (var i=0; i<updateAttendances.length; i++)
            {
              updateAttendances[i].classList.remove("disabled");
            }        
          }

          function viewEditHistory(member_ID, ensemble_ID, term_ID)
          {
            document.getElementById("edit-history-contents").innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Loading...';

            var xhttp = new XMLHttpRequest();

            xhttp.open("POST", "<?=$config['base_url'];?>/api/v1/get_attendance-edit-history.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhttp.send(
              "member_ID="    + member_ID +
              "&ensemble_ID=" + ensemble_ID +
              "&term_ID="     + term_ID
            );

            xhttp.onload = function() {
              const JSON_response = JSON.parse(this.responseText);

              var edit_history_contents = '';
              var edit_history_title    = '';
              if (JSON_response.status == "no_results") {
                edit_history_contents = JSON_response.edit_history;
                edit_history_title    = JSON_response.member_name;
              }
              else if (JSON_response.status == "success") {
                edit_history_contents  = '<table class="table table-vcenter card-table table-striped">';
                edit_history_contents += '  <thead>';
                edit_history_contents += '    <tr>';
                edit_history_contents += '      <th>When</th>';
                edit_history_contents += '      <th>Rehearsal</th>';
                edit_history_contents += '      <th>Changed to</th>';
                edit_history_contents += '      <th>By</th>';
                edit_history_contents += '    </tr>';
                edit_history_contents += '  <tbody>';
                for (let i=0; i<JSON_response.edit_history.length; i++) {
                  edit_history_contents += '    <tr>';
                  edit_history_contents += '      <td>'+JSON_response.edit_history[i][0]+'</td>';
                  edit_history_contents += '      <td>'+JSON_response.edit_history[i][1]+'</td>';
                  edit_history_contents += '      <td>'+JSON_response.edit_history[i][2]+'</td>';
                  edit_history_contents += '      <td>'+JSON_response.edit_history[i][3]+'</td>';
                  edit_history_contents += '    </tr>';
                }
                edit_history_contents += '  </tbody>';
                edit_history_contents += '</table>';

                edit_history_title = JSON_response.member_name;
              }
              else {
                edit_history_contents = 'An error occured: '+JSON_response.error_message;
                edit_history_title    = 'An error occured';
              }

              document.getElementById("edit-history-contents").innerHTML = edit_history_contents;
              document.getElementById("edit-history-title").innerHTML    = edit_history_title;
            }
          }

          function updateAttendance()
          {
            document.getElementById("update-attendance-result-title").innerHTML = "Updating...";
            document.getElementById("update-attendance-result-text").innerHTML = "Please wait.";
            document.getElementById("update-attendance-result-icon").innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>';
            document.getElementById("update-attendance-result-button").innerHTML = "Loading...";
            document.getElementById("update-attendance-result-button").classList.add("disabled");
            document.getElementById("update-attendance-result-button").classList.remove("btn-danger");
            document.getElementById("update-attendance-result-button").classList.remove("btn-success");
            document.getElementById("update-attendance-result-button").classList.add("btn-primary");
            document.getElementById("update-attendance-result-status").classList.remove("bg-danger");
            document.getElementById("update-attendance-result-status").classList.remove("bg-success");
            document.getElementById("update-attendance-result-status").classList.add("bg-primary");

            var xhttp = new XMLHttpRequest();

            xhttp.open("POST", "<?=$config['base_url'];?>/api/v1/update_attendance.php", true);
            xhttp.timeout = 10000;
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            var attendance_data = document.getElementsByClassName("attendance-changed");
            var extracted_attendance_data = [];
            for (let i=0; i<attendance_data.length; i++)
            {
              var data = attendance_data[i].name.split("-");
              var currentEntry = {};
              currentEntry["member_ID"] = data[2].substring(4);
              currentEntry["term_dates_ID"] = data[3].substring(8);
              currentEntry["ensemble_ID"] = data[1].substring(8);
              currentEntry["status"] = attendance_data[i].checked;
              extracted_attendance_data.push(currentEntry);
            }

            xhttp.send("attendance_data="+JSON.stringify(extracted_attendance_data)+"&session_ID=<?=$_COOKIE["session_ID"];?>");
            // xhttp.send(
            //   "session_id="    + document.getElementById("session_id").value +
            //   "&user_id="      + document.getElementById("user_id").value + 
            //   "&shortname="    + document.getElementById("shortname").value +
            //   "&status="       + document.getElementById("status").value +
            //   "&redirect_url=" + document.getElementById("redirect_url").value
            // );
            xhttp.onload = function() {
              const JSON_response = JSON.parse(this.responseText);

              if (JSON_response.status == "success") {
                document.getElementById("update-attendance-result-title").innerHTML = "Success!";
                document.getElementById("update-attendance-result-text").innerHTML = "You updated the attendance of " + memberCounter + " people over " + attendanceCounter + " dates.";
                document.getElementById("update-attendance-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M9 12l2 2l4 -4" /></svg>';
                document.getElementById("update-attendance-result-button").innerHTML = "Great!";
                document.getElementById("update-attendance-result-button").classList.remove("disabled");
                document.getElementById("update-attendance-result-button").classList.remove("btn-danger");
                document.getElementById("update-attendance-result-button").classList.remove("btn-primary");
                document.getElementById("update-attendance-result-button").classList.add("btn-success");
                document.getElementById("update-attendance-result-status").classList.remove("bg-danger");
                document.getElementById("update-attendance-result-status").classList.remove("bg-primary");
                document.getElementById("update-attendance-result-status").classList.add("bg-success");

                memberCounter = 0;
                attendanceCounter = 0;
              }
              else {
                document.getElementById("update-attendance-result-title").innerHTML = "Oops! An error occured.";
                document.getElementById("update-attendance-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-red icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
                document.getElementById("update-attendance-result-text").innerHTML = "Error message: " + JSON_response.error_message;
                document.getElementById("update-attendance-result-button").innerHTML = "Understood.";
                document.getElementById("update-attendance-result-button").classList.remove("disabled");
                document.getElementById("update-attendance-result-button").classList.remove("btn-success");
                document.getElementById("update-attendance-result-button").classList.remove("btn-primary");
                document.getElementById("update-attendance-result-button").classList.add("btn-danger");
                document.getElementById("update-attendance-result-status").classList.remove("bg-success");
                document.getElementById("update-attendance-result-status").classList.remove("bg-primary");
                document.getElementById("update-attendance-result-status").classList.add("bg-danger");
              }
            }

            xhttp.ontimeout = function() {
              document.getElementById("update-attendance-result-title").innerHTML = "Oops! An error occured.";
              document.getElementById("update-attendance-result-icon").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-red icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
              document.getElementById("update-attendance-result-text").innerHTML = "Error message: " + "Request to API timed out. <strong>Your attendance has not been saved.</strong>";
              document.getElementById("update-attendance-result-button").innerHTML = "Understood.";
              document.getElementById("update-attendance-result-button").classList.remove("disabled");
              document.getElementById("update-attendance-result-button").classList.remove("btn-success");
              document.getElementById("update-attendance-result-button").classList.remove("btn-primary");
              document.getElementById("update-attendance-result-button").classList.add("btn-danger");
              document.getElementById("update-attendance-result-status").classList.remove("bg-success");
              document.getElementById("update-attendance-result-status").classList.remove("bg-primary");
              document.getElementById("update-attendance-result-status").classList.add("bg-danger");
            }
          }

          function setIndeterminate()
          {
            var indeterminates = document.getElementsByClassName("indeterminate");

            for (let i=0; i<indeterminates.length; i++)
            {
              indeterminates[i].indeterminate = true;
            }
          }

          function moveToTop()
          {
            var rowToMove   = document.getElementById("move-to-top");
            var rowToMoveTo = document.getElementById("move-to-top-location");
            var table       = document.getElementById("attendance-table");

            table.insertBefore(rowToMove, rowToMoveTo);
          }

          function checkPollEnded()
          {
            <?php
              $poll_ended = $db_connection->query("SELECT `datetime` FROM term_dates WHERE `term_ID`='".$term_ID."' AND (`is_featured` >= 0 OR `is_featured`='-".$ensemble_ID."') AND `datetime` >= UNIX_TIMESTAMP()");

              if ($poll_ended->num_rows == 0)
              {
                $new_poll_info = $db_connection->query("SELECT `name`, `safe_name` FROM terms WHERE `ID`>='".$term_ID."' ORDER BY `ID` DESC LIMIT 1");

                if ($new_poll_info->num_rows == 1)
                {
                  $poll_info = $new_poll_info->fetch_assoc();
                  ?>
                  var poll_ended    = true;
                  var link          = "<?=$config['base_url']."/".$ensemble_safe_name."/".$poll_info["safe_name"]."/";?>";
                  var ensemble      = "<?=$ensemble_name;?>";
                  var new_term_name = "<?=$poll_info["name"];?>";
                  <?php
                }
                else
                {
                  ?>
                  var poll_ended = false;
                  <?php
                }
              }
              else
              {
                ?>
                var poll_ended = false;
                <?php
              }
            ?>
            
            if (poll_ended)
            {
              $('#poll-ended-box')       .modal('show');
              $('#poll-ended-box-icon')  .html('<svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-yellow icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" /><circle cx="18" cy="18" r="4" /><path d="M15 3v4" /><path d="M7 3v4" /><path d="M3 11h16" /><path d="M18 16.496v1.504l1 1" /></svg>');
              $('#poll-ended-box-title') .html("This poll has ended");
              $('#poll-ended-box-text')  .html("Did you mean to go to " + ensemble + " " + new_term_name + "?");
              $('#poll-ended-box-button').attr("href", link);
            }
          }

          function warnLeaving()
          {
            window.onbeforeunload = function (e) {
              if (attendanceCounter > 0 || memberCounter > 0) {
                return 1;
              } else {
                
              }
            };
          }

          function hidePlaceholder()
          {
            document.getElementById("placeholder-loading").style.display = "none";
            document.getElementById("main-content")       .style.display = "block";
          }

          function pageLoaded()
          {
            setIndeterminate();
            moveToTop();
            checkPollEnded();
            warnLeaving();
            hidePlaceholder();
          }
          //window.onload = pageLoaded();
        </script>
      </head>
      <body onload="pageLoaded();" >
        <div class="wrapper">
          <?php include($_SERVER['DOCUMENT_ROOT']."/includes/header.php"); ?>
          <?php include($_SERVER['DOCUMENT_ROOT']."/includes/navigation.php"); ?>
          <div class="page-wrapper">
            <div class="page-body">
              <div class="container-xl">
                <div class="row row-cards mb-3">              
                  <!--<div class="col-12">-->
                  <div class="col-md-3 col-lg-3">
                    <div class="card mb-3 bg-blue text-white">
                      <div class="card-stamp">
                        <div class="card-stamp-icon bg-white text-primary">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-mobile" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="7" y="4" width="10" height="16" rx="1" /><line x1="11" y1="5" x2="13" y2="5" /><line x1="12" y1="17" x2="12" y2="17.01" /></svg>
                        </div>
                      </div>
                      <div class="card-header">
                        <h3 class="card-title">On mobile?</h3>
                      </div>
                      <div class="card-body border-bottom py-3">
                        <p>Try scrolling left or right on the names below to see the checkboxes.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3 col-lg-3">
                    <div class="card mb-3 bg-blue text-white">
                      <div class="card-stamp">
                        <div class="card-stamp-icon bg-white text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-desktop" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="4" width="18" height="12" rx="1" /><line x1="7" y1="20" x2="17" y2="20" /><line x1="9" y1="16" x2="9" y2="20" /><line x1="15" y1="16" x2="15" y2="20" /></svg>
                        </div>
                      </div>
                      <div class="card-header">
                        <h3 class="card-title">On desktop?</h3>
                      </div>
                      <div class="card-body border-bottom py-3">
                        <p>Scroll to bottom of page to reveal horizontal scrollbar, or use shift+scroll.</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-lg-6">
                    <div class="card mb-6">
                      <div class="ribbon ribbon-top bg-red">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01" /><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" /></svg>
                      </div>
                      <div class="card-header">
                        <h3 class="card-title">GDPR warning</h3>
                      </div>
                      <div class="card-body border-bottom py-3">
                        <p>Please be aware that anyone else in the group can see what you select in the options below. If you want either your name to be changed or your attendance hidden from others, then please <a href="mailto:<?=$config["admin_email"];?>" target="_blank">contact <?=$config["admin_name"];?></a>.</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title"><?=$title;?></h3>
                      </div>
                      <?php 
                      if ($ensemble_name == NULL)
                      {
                        ?>
                          <div class="card-body">
                            <p>The ensemble given with ID <?=$ensemble_ID;?> is unknown. Please double-check the URL!</p>
                          </div>
                        <?php
                      }
                      else if ($term_name == NULL)
                      {
                        ?>
                          <div class="card-body">
                            <p>The term given with ID <?=$term_ID;?> is unknown. Please double-check the URL!</p>
                          </div>
                        <?php
                      }
                      else
                      {
                      ?>
                        <div class="card-body border-bottom py-3 col-form-label">
                          <div class="ms-auto text-muted">
                            <form method="get" action="" id="form-sort">
                              <input type="hidden" name="theme" value="<?=$theme;?>" form="form-sort" />
                              <input type="hidden" name="ensemble_ID" value="<?=$_GET['ensemble_ID'];?>" form="form-sort" />
                              <input type="hidden" name="term_ID" value="<?=$_GET['term_ID'];?>" form="form-sort" />
                              <div class="ms-2 d-inline-block">
                                <select class="form-select" name="sortby" form="form-sort">
                                  <?php
                                    if ($config["hide_instrument"])
                                    {
                                      $options = array("first_name" => "First name", "last_name" => "Last name");
                                    }
                                    else
                                    {
                                      $options = array("first_name" => "First name", "last_name" => "Last name", "instrument" => "Instrument");
                                    }

                                    foreach ($options as $value => $option)
                                    {
                                      $selected = ($attendance_select_sortby==$value)?"selected":"";
                                      ?>
                                        <option value="<?=$value;?>" <?=$selected;?>><?=$option;?></option>
                                      <?php
                                    }
                                  ?>
                                </select>
                              </div>
                              <div class="ms-2 d-inline-block">
                                <select class="form-select" name="sortdir" form="form-sort">
                                  <?php
                                    $options = array("ASC" => "Asc.", "DESC" => "Desc.");

                                    foreach ($options as $value => $option)
                                    {
                                      $selected = ($attendance_select_direction==$value)?"selected":"";
                                      ?>
                                        <option value="<?=$value;?>" <?=$selected;?>><?=$option;?></option>
                                      <?php
                                    }
                                  ?>
                                </select>
                              </div>
                              <div class="ms-2 d-inline-block">
                                <button type="submit" class="btn btn-warning ms-auto my-2">Change sort</button>
                              </div>
                            </form>
                          </div>
                          <!-- <div class="ms-auto text-muted">
                            Search:
                            <div class="ms-2 d-inline-block">
                              <input type="text" class="form-control form-control-sm" aria-label="Search invoice">
                            </div>
                          </div> -->
                        </div>

                        <?php
                          if ($config["hide_past_dates"])
                          {
                            $term_dates_query = $db_connection->query("SELECT `datetime`, `datetime_end`, `ID`, `is_featured` FROM term_dates WHERE `term_ID`='".$term_ID."' AND (`is_featured` >= 0 OR `is_featured`='-".$ensemble_ID."') AND `deleted`=0 AND `datetime_end` >= UNIX_TIMESTAMP() ORDER BY `datetime` ASC");
                          }
                          else
                          {
                            $term_dates_query = $db_connection->query("SELECT `datetime`, `datetime_end`, `ID`, `is_featured` FROM term_dates WHERE `term_ID`='".$term_ID."' AND (`is_featured` >= 0 OR `is_featured`='-".$ensemble_ID."') AND `deleted`=0 ORDER BY `datetime` ASC");
                          }

                          $term_dates = array();
                          while ($result = $term_dates_query->fetch_array())
                          {
                            $term_dates[] = $result;
                          }

                          $no_term_dates = count($term_dates);
                        ?>

                        <div class="table-responsive" id="main-content" style="display:none">
                          <form id="update_attendance">
                            <table id="attendance-table" class="table card-table table-vcenter text-nowrap datatable">
                              <div class="p-2 my-0 d-flex">
                                <p class="ms-auto m-0 text-muted">
                                  <span style="padding-right: 10px;">You've changed <span class="memberCounter fw-bold">0</span> people's attendance over <span class="attendanceCounter fw-bold">0</span> dates.</span>
                                  <a class="updateAttendance btn btn-primary ms-auto disabled my-2" onclick="updateAttendance()" data-bs-toggle="modal" data-bs-target="#update-attendance-result">Update</a>
                                  <a class="btn my-2" onclick="location.reload()">Reset</a>
                                </p>
                              </div>
                              <tbody id="move-to-top-location">
                              <?php
                              $members = $db_connection->query("SELECT `first_name`, `last_name`, `instrument`, `members`.`ID` AS `ID` FROM `members` LEFT JOIN `members-ensembles` ON `members-ensembles`.`member_ID`=`members`.`ID` WHERE `members-ensembles`.`ensemble_ID`=".$ensemble_ID." AND `members`.`deleted`='0' ORDER BY `".$attendance_select_sortby."` ".$attendance_select_direction);

                              if ($members->num_rows == 0)
                              {
                                ?> 
                                  <tr>
                                    <td width="99%">
                                      No members to display.
                                    </td>
                                    <?php
                                      foreach($term_dates as $term_date)
                                      {
                                        ?>
                                        <td></td>
                                        <?php
                                      }
                                    ?>
                                  </tr>
                                <?php
                              }
                              else
                              {
                                $sort_initial = '';

                                while($member = $members->fetch_assoc())
                                {
                                  switch ($attendance_select_sortby)
                                  {
                                    case 'first_name':
                                      $current_sort_initial = substr($member["first_name"], 0, 1);
                                      break;
                                    case 'last_name':
                                      $current_sort_initial = substr($member["last_name"], 0, 1);
                                      break;
                                    case 'datetime':
                                      $current_sort_initial = '';
                                      break;
                                    case 'instrument':
                                      $current_sort_initial = $member["instrument"];
                                      break;
                                    
                                    default:
                                      $current_sort_initial = '';
                                      break;
                                  }

                                  if (($current_sort_initial != $sort_initial and $config["repeat_headings"]) or !$headings_printed)
                                  {
                                    $headings_printed = true;
                                    $sort_initial     = $current_sort_initial;
                                    ?>
                                      <thead>
                                        <tr>
                                          <?php 
                                            $options = array("first_name" => "First name", "last_name" => "Last name", "instrument" => "Instrument");
                                          ?>
                                          <th class="sticky-top w-1">Members (by <?=$options[$attendance_select_sortby];?>)
                                            <?php
                                              if ($attendance_select_direction == "DESC")
                                              {
                                                ?>
                                                <a href="?<?=http_build_query(array_merge($_GET, array('sortdir'=>'ASC')));?>">
                                                  <!-- Download SVG icon from http://tabler-icons.io/i/chevron-up -->
                                                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-dark icon-thick" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 15 12 9 18 15" /></svg>
                                                </a>
                                                <?php
                                              }
                                              else
                                              {
                                                ?>
                                                <a href="?<?=http_build_query(array_merge($_GET, array('sortdir'=>'DESC')));?>">
                                                  <!-- Download SVG icon from http://tabler-icons.io/i/chevron-down -->
                                                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-dark icon-thick" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                </a>
                                                <?php
                                              }
                                            ?>
                                          </th>
                                          <?php
                                            foreach($term_dates as $term_date)
                                            {
                                              $start = new DateTime();
                                              $start ->setTimestamp($term_date[0]);
                                              $start ->setTimeZone(new DateTimeZone('Europe/London'));
                                              $end   = new DateTime();
                                              $end   ->setTimestamp($term_date[1]);
                                              $end   ->setTimeZone(new DateTimeZone('Europe/London'));

                                              ($term_date[1]<time())?$strike_through_start="<s>":$strike_through_start="";
                                              ($term_date[1]<time())?$strike_through_end="</s>":$strike_through_end="";
                                              ($term_date[3])?$is_featured="bg-primary text-white":$is_featured="";
                                              ?>
                                              <th class="sticky-top text-center align-text-top <?=$is_featured;?>">
                                                <?=$strike_through_start;?>
                                                <?=$start->format('M');?><br /><span style="line-height: 30px; font-size: 32px; margin:none;"><?=$start->format('j');?></span><br /><?=$start->format('D');?><br /><?=$start->format('H:i');?><br /><?=$end->format('H:i');?><!--<br /><span style="word-wrap: normal; white-space: pre-wrap">(<?=$term_date[4];?>)</span>-->
                                                <?=$strike_through_end;?>
                                              </th>
                                              <?php
                                            }
                                          ?>
                                        </tr>
                                      </thead>
                                      <?php
                                        if ($config["repeat_headings"])
                                        {
                                          ?>
                                            <tr>
                                              <td colspan="100%">
                                                <div class="" style="font-size: .75rem; padding: 0rem 0rem;"><?=$sort_initial;?></div>
                                              </td>
                                            </tr>
                                          <?php
                                        }
                                      ?>
                                    <?php
                                  }
                                  ?>
                                    <tr>
                                      <td width="99%">
                                        <div class="d-flex py-1 align-items-center">
                                          <span class="avatar me-2"><?=substr($member["first_name"], 0, 1).substr($member["last_name"], 0, 1);?></span>
                                          <div class="flex-fill">
                                            <?php if ($config["hide_instrument"]) { ?>
                                              <div class="font-weight-medium"><?=$member["first_name"]." ".$member["last_name"];?></div>
                                            <?php } else { ?>
                                              <div class="font-weight-medium"><?=$member["first_name"]." ".$member["last_name"];?> <span class="">(<?=$member["instrument"];?>)</span></div>
                                            <?php } ?>
                                            <a class="text-muted" style="cursor: pointer;" onclick="viewEditHistory(<?=$member["ID"];?>, <?=$ensemble_ID;?>, <?=$term_ID;?>)" data-bs-toggle="modal" data-bs-target="#edit-history">
                                              <!-- Download SVG icon from http://tabler-icons.io/i/pencil -->
                                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" /><line x1="13.5" y1="6.5" x2="17.5" y2="10.5" /></svg>
                                              <?php
                                                $last_edited_query = $db_connection->query("SELECT `edit_datetime` FROM `attendance` LEFT JOIN `term_dates` ON `term_dates`.`ID`=`attendance`.`term_dates_ID` WHERE `attendance`.`member_ID`='".$member['ID']."' AND `attendance`.`ensemble_ID`=".$ensemble_ID." AND `term_dates`.`term_ID`='".$term_ID."' ORDER BY `edit_datetime` DESC LIMIT 1");

                                                if ($last_edited_query->num_rows>0)
                                                {
                                                  $last_edited = $last_edited_query->fetch_array()[0];
                                                }
                                                else
                                                {
                                                  $last_edited = 0;
                                                }
                                              ?>
                                              <?=findTimeAgo($last_edited)?>
                                            </a>
                                          </div>
                                        </div>
                                      </td>
                                      <?php
                                        foreach($term_dates as $term_date)
                                        {
                                          $attendance_query = $db_connection->query("SELECT `status` FROM `attendance` WHERE `member_ID`='".$member['ID']."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_dates_ID`='".$term_date[2]."' ORDER BY `edit_datetime` DESC LIMIT 1");

                                          if ($attendance_query->num_rows>0)
                                          {
                                            $attendance = $attendance_query->fetch_array()[0];
                                          }
                                          else
                                          {
                                            $attendance = NULL;
                                          }

                                          // if ($member["first_name"] == "Samantha")
                                          // {
                                          //   echo "SELECT `status` FROM `attendance` WHERE `member_ID`='".$member['ID']."' AND `ensemble_ID`='".$ensemble_ID."' AND `term_dates_ID`='".$term_date[1]."' ORDER BY `edit_datetime` DESC LIMIT 12";
                                          // }

                                          //($attendance==NULL)?$attendance="1":$attendance=$attendance;
                                          ($attendance==NULL)?$indeterminate="indeterminate":$indeterminate="";
                                          ($attendance=="1")?$checked="checked":$checked="";
                                          ($term_date[1]<time())?$disabled="disabled":$disabled="";
                                          ($term_date[3])?$is_featured="bg-blue-25":$is_featured="";

                                          if ($attendance=="1")
                                          {
                                            $term_date_counter[strval($term_date[2])]++;
                                          }
                                          elseif ($attendance == NULL)
                                          {
                                            $term_date_counter_intederminate[strval($term_date[2])]++; 
                                          }

                                          ?>
                                            <td class="text-center <?=$is_featured;?>">
                                              <div class="col-auto">
                                                <label class="form-colorcheckbox bigger" style="margin: 0px;">
                                                  <input name="attendance-ensemble<?=$ensemble_ID;?>-user<?=$member["ID"];?>-termdate<?=$term_date[2];?>" form="update_attendance" type="checkbox" value="lime" class="form-colorcheckbox-input <?=$indeterminate;?>" <?=$checked;?> <?=$disabled;?> onchange="updateTotalChanged(this)" />
                                                  <span class="form-colorcheckbox-color "></span>
                                                </label>
                                              </div>
                                            </td>
                                          <?php
                                        }
                                      ?>
                                    </tr>
                                  <?php
                                }
                              }
                              // $attendance = $db_connection->query("SELECT `attendance`.`status`, `attendance`.`ID` FROM `attendance` INNER JOIN `term_dates` ON `attendance`.`term_dates_ID`=`term_dates`.`term_ID` WHERE `term_dates`.`term_ID`=1 GROUP BY `attendance`.`ID`");
                                //$attendance = $db_connection->query("SELECT * FROM `attendance`");

                              ?>
                              <tr>
                                <td><!--<div class="avatar bg-primary" style="height: 1rem; width: 1rem;"></div> = concert--></td>
                                <?php
                                  foreach($term_dates as $term_date)
                                  {
                                    ?>
                                      <td class="text-center"><strong><?=isset($term_date_counter[$term_date[2]])?$term_date_counter[$term_date[2]]:"0";?></strong> (<?=isset($term_date_counter_intederminate[$term_date[2]])?$term_date_counter_intederminate[$term_date[2]]:"0";?>)</td>
                                    <?php
                                  }
                                ?>
                              </tr>
                              <tr id="move-to-top">
                                <td><!--<div class="avatar bg-primary" style="height: 1rem; width: 1rem;"></div> = concert--></td>
                                <?php
                                  foreach($term_dates as $term_date)
                                  {
                                    ?>
                                      <td class="text-center"><strong><?=isset($term_date_counter[$term_date[2]])?$term_date_counter[$term_date[2]]:"0";?></strong> (<?=isset($term_date_counter_intederminate[$term_date[2]])?$term_date_counter_intederminate[$term_date[2]]:"0";?>)</td>
                                    <?php
                                  }
                                ?>
                              </tr>
                              </tbody>
                            </table>
                          </form>
                        </div>

                        <div class="card" id="placeholder-loading">
                          <ul class="list-group list-group-flush placeholder-glow">
                            <li class="list-group-item opacity-100">
                              <div class="row align-items-center">
                                <div class="col-auto">
                                  <div class="avatar avatar-rounded placeholder"></div>
                                </div>
                                <div class="col-7">
                                  <div class="placeholder placeholder-xs col-9"></div>
                                  <div class="placeholder placeholder-xs col-7"></div>
                                </div>
                                <div class="col-2 ms-auto text-end">
                                  <div class="placeholder placeholder-xs col-8"></div>
                                  <div class="placeholder placeholder-xs col-10"></div>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item opacity-80">
                              <div class="row align-items-center">
                                <div class="col-auto">
                                  <div class="avatar avatar-rounded placeholder"></div>
                                </div>
                                <div class="col-7">
                                  <div class="placeholder placeholder-xs col-9"></div>
                                  <div class="placeholder placeholder-xs col-7"></div>
                                </div>
                                <div class="col-2 ms-auto text-end">
                                  <div class="placeholder placeholder-xs col-8"></div>
                                  <div class="placeholder placeholder-xs col-10"></div>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item opacity-60">
                              <div class="row align-items-center">
                                <div class="col-auto">
                                  <div class="avatar avatar-rounded placeholder"></div>
                                </div>
                                <div class="col-7">
                                  <div class="placeholder placeholder-xs col-9"></div>
                                  <div class="placeholder placeholder-xs col-7"></div>
                                </div>
                                <div class="col-2 ms-auto text-end">
                                  <div class="placeholder placeholder-xs col-8"></div>
                                  <div class="placeholder placeholder-xs col-10"></div>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item opacity-40">
                              <div class="row align-items-center">
                                <div class="col-auto">
                                  <div class="avatar avatar-rounded placeholder"></div>
                                </div>
                                <div class="col-7">
                                  <div class="placeholder placeholder-xs col-9"></div>
                                  <div class="placeholder placeholder-xs col-7"></div>
                                </div>
                                <div class="col-2 ms-auto text-end">
                                  <div class="placeholder placeholder-xs col-8"></div>
                                  <div class="placeholder placeholder-xs col-10"></div>
                                </div>
                              </div>
                            </li>
                            <li class="list-group-item opacity-20">
                              <div class="row align-items-center">
                                <div class="col-auto">
                                  <div class="avatar avatar-rounded placeholder"></div>
                                </div>
                                <div class="col-7">
                                  <div class="placeholder placeholder-xs col-9"></div>
                                  <div class="placeholder placeholder-xs col-7"></div>
                                </div>
                                <div class="col-2 ms-auto text-end">
                                  <div class="placeholder placeholder-xs col-8"></div>
                                  <div class="placeholder placeholder-xs col-10"></div>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </div>

                        <div class="card-footer d-flex">
                          <p class="ms-auto m-0 text-muted">
                            <span style="padding-right: 10px;">You've changed <span class="memberCounter fw-bold">0</span> people's attendance over <span class="attendanceCounter fw-bold">0</span> dates.</span>
                            <a class="updateAttendance btn btn-primary ms-auto disabled my-2" onclick="updateAttendance()" data-bs-toggle="modal" data-bs-target="#update-attendance-result">Update</a>
                            <a class="btn my-2" onclick="location.reload()">Reset</a>
                          </p>
                        </div>
                      <?php
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php include($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
          </div>
        </div>

        <div class="modal modal-blur fade" id="update-attendance-result" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="javascript:window.location.reload()"></button>
              <div id="update-attendance-result-status" class="modal-status bg-success"></div>
              <div class="modal-body text-center py-4">
                <div id="update-attendance-result-icon">
                  Result icon
                </div>
                <h3 id="update-attendance-result-title">Result title</h3>
                <div class="text-muted" id="update-attendance-result-text">Result text</div>
              </div>
              <div class="modal-footer">
                <div class="w-100">
                  <div class="row">
                    <div class="col"><a id="update-attendance-result-button" href="#" class="btn btn-success disabled w-100" data-bs-dismiss="modal" onclick="javascript:window.location.reload()">
                        Result button text
                      </a></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal modal-blur fade" id="edit-history" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="edit-history-title">Adam Blakey</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="edit-history-contents">
                Content
              </div>
              <div class="modal-footer">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal modal-blur fade" id="poll-ended-box" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div id="poll-ended-box-status" class="modal-status bg-yellow"></div>
              <div class="modal-body text-center py-4">
                <div id="poll-ended-box-icon">
                  Result icon
                </div>
                <h3 id="poll-ended-box-title">Result title</h3>
                <div class="text-muted" id="poll-ended-box-text">Result text</div>
              </div>
              <div class="modal-footer">
                <div class="w-100">
                  <div class="row">
                    <div class="col">
                      <a href="#" class="btn w-100" data-bs-dismiss="modal">
                        No, stay here.
                      </a>
                    </div>
                    <div class="col">
                      <a id="poll-ended-box-button" href="#" class="btn btn-yellow w-100">
                        Yes, take me there!
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script src="<?=$config['base_url'];?>/dist/js/tabler.min.js"></script>
        <script src="<?=$config['base_url'];?>/dist/js/demo.min.js"></script>
      </body>
    </html>
    <?php
  }
  else if(login_valid())
  {
    output_restricted_page();
  }
  else
  {
    header("Location: ".$config['base_url']."/login.php?redirect_page=".urlencode($_SERVER['REQUEST_URI'])); 
  }
  ?>

<?php db_disconnect($db_connection); ?>