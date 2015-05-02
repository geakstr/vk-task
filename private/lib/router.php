<?php
require_once('view.php');
require_once('models.php');

function router($conns, $request, $views) {  
  $url = $request['url']['path'];
  switch ($url) {
    case ($url === '/' && $request['method'] === 'GET'):  
      // User not authorized  
      if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null) {
        route_welcome($conns, $request, $views);  
      } else {
        $user = model_get_used_by_id($conns, $_SESSION['user_id']);

        // Hmm, session information not valid 
        if ($user === null || ($user['role'] !== 1 && $user['role'] !== 2)) {
          route_auth_logout($conns, $request, $views);  
        } else {
          // Ok, we have valid session
          if ($user['role'] === 1) {
            route_customer_lk($conns, $request, $views);
          } else {
            route_performer_lk($conns, $request, $views);
          }
        }
      }
      break;

    case ($url === '/auth/login' && $request['method'] === 'POST'):
      route_auth_login($conns, $request, $views);
      break;

    case ($url === '/auth/logout'):
      route_auth_logout($conns, $request, $views);
      break;

    case ($url === '/actions/orders/add' && $request['method'] === 'POST'):      
      route_add_order_action($conns, $request, $views);
      break;

    default:
      route_404($conns, $request, $views);
      break;
  }  
}

// GET: / (if not authorized)
function route_welcome($conns, $request, $views) {
  view_render($views, 'index.php', array(
    'title' => 'Вход'
  ));
}

// GET: / (if authorized as customer)
function route_customer_lk($conns, $request, $views) {
  view_render($views, 'customerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_customer_orders($conns, 1)
  ));
}

// GET: / (if authorized as performer)
function route_performer_lk($conns, $request, $views) {
  view_render($views, 'customerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_customer_orders($conns, 1)
  ));
}

// POST: /auth/login
function route_auth_login($conns, $request, $views) {
  $email = trim($request['post']['email']);

  $response = array(
    'type' => 'ok',
    'msgs' => array()
  ); 

  $user_id = model_auth_login($conns, $email);

  if ($user_id === null) {
    $response['type'] = 'error';
    $response['msgs']['email'][] = 'Такой пользователь не найден';
  } else {
    $_SESSION['user_id'] = $user_id;
  }

  echo json_encode($response);
}

// ALL: /auth/logout
function route_auth_logout($conns, $request, $views) {
  unset($_SESSION['user_id']);
  session_unset();
  session_destroy();
  header('Location: /');
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
    $response['msgs']['title'][] = 'Не может быть пустым';
  }

  $price = str_replace(',', '.', $price);
  if (strlen($price) === 0 || !is_numeric($price)) {
    $response['type'] = 'error';
    $response['msgs']['price'][] = 'Должна быть числом'; 
  } else {
    $price = doubleval($price);
    if ($price < 0 || $price > 999999999999.99) {
      $response['type'] = 'error';
      $response['msgs']['price'][] = 'Может быть от 0 до 999999999999.99'; 
    }
  }

  if ($response['type'] === 'error') {
    echo json_encode($response);
    return;
  }

  if (!model_add_order($conns, $title, $description, $price, $customer_id)) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'something';         
  }

  echo json_encode($response);
}

function route_404($conns, $request, $views) {
  view_render($views, "404.php", array(
    'title' => 'Страница не найдена'
  ));
}