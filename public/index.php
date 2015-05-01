<?php
require_once(__DIR__ . '/../private/lib/router.php');
require_once(__DIR__ . '/../private/lib/db.php');

$dbs_conf = array(
  'vk_db_1' => array(
    'host' => '127.0.0.1',    
    'port' => '3306',
    'user' => 'root',
    'password' => 'rootpass',
    'charset' => 'utf8'
  ),
  'vk_db_2' => array(
    'host' => '127.0.0.1',
    'port' => '3306',
    'user' => 'root',
    'password' => 'rootpass',
    'charset' => 'utf8'
  )
);

$dirs = array(
  'views' => __DIR__ . '/../private/views'
);

$dbs = db_create_conns($dbs_conf);

// Start serving routes
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$request['url'] = parse_url("$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$request['post'] = $_POST;
router($dbs, $request, $dirs['views']);

db_close_conns($dbs);