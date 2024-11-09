<?php
  require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php")
?>

<footer class="footer footer-transparent d-print-none">
  <div class="container-xl">
    <div class="row text-center align-items-center flex-row-reverse">
      <div class="col-lg-auto ms-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item"><a href="https://gitlab.com/adam.blakey/pollererer" target="_blank" class="link-secondary" rel="noopener">Source code</a></li>
          <li class="list-inline-item">v<?=get_pollererer_version();?></li>
        </ul>
      </div>
      <div class="col-12 col-lg-auto mt-3 mt-lg-0">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item">
            Copyright &copy; 2022–<?=date("Y");?>
            <a href="https://adam.blakey.family/" target="_blank" class="link-secondary">Adam Blakey</a> for <a href="<?=$config["home_url"];?>" target="_blank" class="link-secondary"><?=$config["group_name"];?></a>
          </li>
          <li class="list-inline-item">
            Design by <a href="https://tabler.io/" target="_blank" class="link-secondary">Tabler</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>