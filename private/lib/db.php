<?php
function db_create_conns($dbs_conf) {
  $dbs = array();

  foreach ($dbs_conf as $table => $conn) {
    $dbs[$table] = mysqli_connect($conn['host'], $conn['user'], $conn['password'], $conn['db']);

    if (mysqli_connect_errno()) {
      printf("DB '$conn[db]' not connected: %s\n", mysqli_connect_error());
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