<?php
require_once('view.php');
require_once('models.php');

function router($conns, $request, $views) {  
  $url = $request['url']['path'];
  switch ($url) {
    case ($url === '/' && $request['method'] === 'GET'):
      route_index($conns, $request, $views);
      break;
    case ($url === '/lk' && $request['method'] === 'GET'):      
      route_lk($conns, $request, $views);
      break;
    case ($url === '/actions/orders/add' && $request['method'] === 'POST'):      
      route_add_order_action($conns, $request, $views);
      break;
    default:
      route_404($conns, $request, $views);
      break;
  }  
}

// GET: /
function route_index($conns, $request, $views) {
  view_render($views, 'index.php', array(
    'title' => 'Главная'
  ));
}

// GET: /lk
function route_lk($conns, $request, $views) {
  view_render($views, 'customerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_customer_orders($conns, 1)
  ));
}

// POST: /actions/orders/add
function route_add_order_action($conns, $request, $views) {
  $title = trim($request['post']['title']);
  $description = trim($request['post']['description']);
  $price = trim($request['post']['price']);
  $customer_id = 1;

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  );      

  if (strlen($title) === 0) {
    $response['type'] = 'error';
    $response['msgs'][] = 'title_field_error';
  }

  $price = str_replace(',', '.', $price);
  if (strlen($price) === 0 || !is_numeric($price)) {
    $response['type'] = 'error';
    $response['msgs'][] = 'price_field_error'; 
  } else {
    $price = doubleval($price);
    if ($price < 0 || $price > 999999999999.99) {
      $response['type'] = 'error';
      $response['msgs'][] = 'price_field_error'; 
    }
  }

  if ($response['type'] === 'error') {
    echo json_encode($response);
    return;
  }

  if (!model_add_order($conns, $title, $description, $price, $customer_id)) {
    $response['type'] = 'error';
    $response['msgs'][] = 'db_error';         
  }

  echo json_encode($response);
}

function route_404($conns, $request, $views) {
  view_render($views, "404.php", array(
    'title' => 'Страница не найдена'
  ));
}