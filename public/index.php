<?php
require_once(__DIR__ . '/../private/lib/router.php');
require_once(__DIR__ . '/../private/lib/db.php');

$path = array(
  'views' => __DIR__ . '/../private/views',
  'config' => __DIR__ . '/../private/config.json'
);

$conns = db_create_conns(json_decode(file_get_contents($path['config']), true));

// Start serving routes
$request['url'] = parse_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$request['post'] = $_POST;
$request['method'] = $_SERVER['REQUEST_METHOD'];

router($conns, $request, $path['views']);

db_close_conns($conns);