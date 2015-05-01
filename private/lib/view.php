<?php
function view_render($views, $page, $data) {
  extract($data);
  require_once("$views/layout.php");
}