<?php
require_once('view.php');
require_once('models.php');
require_once('security.php');

function router($conns, $request, $views) {  
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
          route_performer_lk($conns, $request, $views);
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
      if (security_is_customer($conns)) {    
        route_add_order_action($conns, $request, $views);
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
  view_render($views, 'index.php', array(
    'title' => 'Вход'
  ));
}

// GET: / (if authorized as customer)
function route_customer_lk($conns, $request, $views) {
  view_render($views, 'customerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_customer_orders($conns, $_SESSION['user']['id'])
  ));
}

// GET: / (if authorized as performer)
function route_performer_lk($conns, $request, $views) {
  view_render($views, 'performerlk.php', array(
    'title' => 'Личный кабинет',
    'orders' => model_get_all_orders_for_performer($conns, $_SESSION['user']['id'])
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

  echo json_encode($response);
}

// ALL: /auth/logout
function route_auth_logout_action($conns, $request, $views) {
  security_unauthorize();  
  header('Location: /');
}

// POST: /actions/orders/add
function route_add_order_action($conns, $request, $views) {
  $title = trim($request['post']['title']);
  $description = trim($request['post']['description']);
  $price = trim($request['post']['price']); 

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

  if (!model_add_order($conns, $title, $description, $price, $_SESSION['user']['id'])) {
    $response['type'] = 'error';
    $response['msgs']['server'][] = 'Что-то пошло не так, попробуйте позднее';         
  }

  echo json_encode($response);
}

function route_404($conns, $request, $views) {
  view_render($views, "404.php", array(
    'title' => 'Страница не найдена'
  ));
}