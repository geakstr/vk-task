<?php
function db_create_conns($dbs_conf) {
  $dbs = array();

  foreach ($dbs_conf as $db => $db_conf) {
    $dbs[$db] = mysqli_connect($db_conf['host'], $db_conf['user'], $db_conf['password'], $db);

    if (mysqli_connect_errno()) {
      printf("MySQL DB not connected: %s\n", mysqli_connect_error());
      exit();
    }
  }

  return $dbs;
}

function db_close_conns($dbs) {
  foreach ($dbs as $key => $val) {
    mysqli_close($val);
  }  
}