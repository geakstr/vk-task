<?php
require_once('view.php');
require_once('models.php');

function router($dbs, $request, $views) {
  switch ($request['url']['path']) {
    case '/':
      view_render($views, 'index.php', array(
        'title' => 'Главная'
      ));
      break;

    case '/lk':
      view_render($views, 'customerlk.php', array(
        'title' => 'Личный кабинет',
        'orders' => model_get_all_customer_orders($dbs, 1)
      ));

      break;

    case '/actions/orders/add':      
      $title = trim($request['post']['title']);
      $description = trim($request['post']['description']);
      $price = trim($request['post']['price']);
      $customer_id = 1;

      $json_response = array(
        'type' => 'ok',
        'msgs' => array()
      );      

      if (strlen($title) === 0) {
        $json_response['type'] = 'error';
        $json_response['msgs'][] = 'title_field_error';
      }

      $price = str_replace(',', '.', $price);
      if (strlen($price) === 0 || !is_numeric($price)) {
        $json_response['type'] = 'error';
        $json_response['msgs'][] = 'price_field_error'; 
      } else {
        $price = doubleval($price);
        if ($price < 0 || $price > 999999999999.99) {
          $json_response['type'] = 'error';
          $json_response['msgs'][] = 'price_field_error'; 
        }
      }

      if ($json_response['type'] === 'error') {
        echo json_encode($json_response);
        return;
      }

      if (!model_add_order($dbs, $title, $description, $price, $customer_id)) {
        $json_response['type'] = 'error';
        $json_response['msgs'][] = 'db_error';         
      }

      echo json_encode($json_response);

      break;

    default:
      view_render($views, "404.php", array(
        'title' => 'Страница не найдена'
      ));
      break;
  }  
}