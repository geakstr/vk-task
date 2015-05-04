<?php
require_once('view.php');
require_once('models.php');
require_once('security.php');

function router($conns, $request, $views) {    
  security_update_session($conns);

  $url = $request['url']['path'];

  switch ($url) {
    case ($url === '/' && $request['method'] === 'GET'):  
      if (!security_is_authorized($conns)) {
        security_unauthorize();
        route_welcome($conns, $request, $views);  
      } else {
        if (security_is_customer($conns)) {
          route_customer_lk($conns, $request, $views);
        } else {
          route_worker_lk($conns, $request, $views);
        }  
      }
      break;

    case ($url === '/actions/auth/login' && $request['method'] === 'POST'):
      route_auth_login_action($conns, $request, $views);
      break;

    case ($url === '/actions/auth/logout'):
      route_auth_logout_action($conns, $request, $views);
      break;

    case ($url === '/actions/orders/add' && $request['method'] === 'POST'):  
      if (security_check_referer($request['url']) && security_is_customer($conns)) {    
        route_add_order_action($conns, $request, $views);
      } else {
        route_auth_logout_action($conns, $request, $views);
      }
      break;

    case ($url === '/actions/orders/work' && $request['method'] === 'POST'):    
      if (security_check_referer($request['url']) && security_is_worker($conns)) {
        route_work_order_action($conns, $request, $views); 
      } else {
        route_auth_logout_action($conns, $request, $views); 
      }
      break;

    case ($url === '/actions/orders/delete' && $request['method'] === 'POST'):
      if (security_check_referer($request['url']) && security_is_customer($conns)) {    
        route_delete_order_action($conns, $request, $views);
      } else {
        route_auth_logout_action($conns, $request, $views);
      }
      break;

    case ($url === '/actions/balance/refill' && $request['method'] === 'POST'):
      if (security_check_referer($request['url']) && security_is_customer($conns)) {
        route_balance_refill($conns, $request, $views);
      } else {
        route_auth_logout_action($conns, $request, $views); 
      }
      break;

    default:
      route_404($conns, $request, $views);
      break;
  }  
}

// GET: / (if not authorized)
function route_welcome($conns, $request, $views) {
  $profit = number_format(model_get_service_profit($conns), '2', '.', '');
  view_render($views, 'index.php', array(
    'title' => 'Вход',
    'profit' => $profit
  ));
}

// GET: / (if authorized as customer)
function route_customer_lk($conns, $request, $views) {
  $total_price = model_get_customer_total_orders_price($conns, $_SESSION['user']['id']);
  $sub_balance = $_SESSION['user']['balance'] - $total_price; 
  $sub_balance = number_format($sub_balance, '2', '.', '');
  view_render($views, 'customerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_customer_orders($conns, $_SESSION['user']['id']),
    'sub_balance' => $sub_balance
  ));
}

// GET: / (if authorized as worker)
function route_worker_lk($conns, $request, $views) {
  view_render($views, 'workerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_orders_for_worker($conns, $_SESSION['user']['id'])
  ));
}

// POST: /auth/login
function route_auth_login_action($conns, $request, $views) {
  $email = trim($request['post']['email']);

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  ); 

  $user = model_get_user_by_email($conns, $email);

  if ($user === null) {
    $response['type'] = 'error';
    $response['msgs']['email'][] = 'Такой пользователь не найден';
  } else {
    security_unauthorize();
    session_start();
    $_SESSION['user'] = $user;
  }

  header("content-type: application/json; charset=utf8");
  echo json_encode($response);
}

// ALL: /auth/logout
function route_auth_logout_action($conns, $request, $views) {
  security_unauthorize();  
  header('Location: /');
}

// POST: /actions/orders/add
function route_add_order_action($conns, $request, $views) {
  $title = htmlentities(trim($request['post']['title']), ENT_QUOTES, 'UTF-8');
  $description = htmlentities(trim($request['post']['description']), ENT_QUOTES, 'UTF-8');
  $price = trim($request['post']['price']); 

  $response = array(
    'type' => 'ok',
    'msgs' => array(),
    'params' => array()
  );      

  if (strlen($title) === 0) {
    $response['type'] = 'error';
    $response['msgs']['title'][] = 'Не может быть пустым';
  }

  $price = str_replace(',', '.', $price);
  if (strlen($price) === 0 || !is_numeric($price)) {
    $response['type'] = 'error';
    $response['msgs']['price'][] = 'Сумма должна быть числом'; 
  } else {
    $price = doubleval($price);
    if ($price < 0 || $price > 999999999999.99) {
      $response['type'] = 'error';
      $response['msgs']['price'][] = 'Может быть от 0 до 999999999999.99'; 
    }
  }

  // Check customer balance
  $total_price = model_get_customer_total_orders_price($conns, $_SESSION['user']['id']);
  $total_price += $price;
  $diff = $_SESSION['user']['balance'] - $total_price;
  if ($diff < 0) {
    $def = $total_price - $_SESSION['user']['balance'];
    $response['type'] = 'error';
    $response['msgs']['server'][] = "На балансе не хватает $def руб., чтобы оплатить все заказы"; 
  }

  if ($response['type'] === 'error') {
    header("content-type: application/json; charset=utf8");
    echo json_encode($response);
    return;
  }

  $order_id = model_add_order($conns, $title, $description, $price, $_SESSION['user']['id']);
  if ($order_id === false) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  } else {
    $response['params']['order_id'] = $order_id;
  }

  header("content-type: application/json; charset=utf8");
  echo json_encode($response);
}

// POST: /actions/order/delete
function route_delete_order_action($conns, $request, $views) {
  $id = trim($request['post']['id']);

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  );     

  if (!model_delete_order($conns, $id)) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  }

  header("content-type: application/json; charset=utf8");
  echo json_encode($response);
}

// POST: /actions/orders/work
function route_work_order_action($conns, $request, $views) {
  $order_id = trim($request['post']['id']);

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  );

  if (!ctype_digit($order_id)) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  }

  if ($response['type'] === 'error') {
    header("content-type: application/json; charset=utf8");
    echo json_encode($response);
    return;
  }

  if (!model_work_order($conns, $order_id, $_SESSION['user']['id'])) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  }

  header("content-type: application/json; charset=utf8");
  echo json_encode($response);
}

// POST: /actions/balance/refill
function route_balance_refill($conns, $request, $views) {
  $fee = str_replace(',', '.', $request['post']['fee']);

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  );  

  if (strlen($fee) === 0 || !is_numeric($fee)) {
    $response['type'] = 'error';
    $response['msgs']['fee'][] = 'Сумма должна быть числом'; 
  } else {
    $fee = doubleval($fee);
    if ($fee < 0) {
      $response['type'] = 'error';
      $response['msgs']['fee'][] = 'Сумма от 0 до 999999999999.99 руб'; 
    } else if ($fee + doubleval($_SESSION['user']['balance']) > 999999999999.99) {
      $response['type'] = 'error';
      $response['msgs']['fee'][] = 'Баланс может быть от 0 до 999999999999.99 руб'; 
    }  
  }

  if ($response['type'] === 'error') {
    header("content-type: application/json; charset=utf8");
    echo json_encode($response);
    return;
  }  
  
  if (!model_balance_refill($conns, $_SESSION['user']['id'], $fee)) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  }

  header("content-type: application/json; charset=utf8");
  echo json_encode($response);
}

function route_404($conns, $request, $views) {
  view_render($views, "404.php", array(
    'title' => 'Страница не найдена'
  ));
}