<?php
function model_get_user_by_email($conns, $email) {
  $sql = "SELECT id, email, fio, balance, role, creation_time FROM users WHERE email = ?";

  if ($stmt = mysqli_prepare($conns['users'], $sql)) {    
    mysqli_stmt_bind_param($stmt, 's', $email);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $user_id, $email, $fio, $balance, $role, $creation_time);

    $user = null;
    while (mysqli_stmt_fetch($stmt)) {
      $user = array(
        'id' => $user_id,
        'email' => $email,
        'fio' => $fio,
        'balance' => $balance,
        'role' => $role,
        'creation_time' => $creation_time
      );
    };
    mysqli_stmt_close($stmt);

    return $user;
  }

  return null;
}

// Make new order from customer
function model_add_order($conns, $title, $description, $price, $customer) {
  // Disable autocommit to have transaction rollback possibility
  mysqli_autocommit($conns['orders'], false);

  $sql = 'INSERT INTO orders(title, description, price, customer) VALUES(?, ?, ?, ?)';
  $stmt = mysqli_stmt_init($conns['orders']);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
  }

  mysqli_stmt_bind_param($stmt, 'ssdi', $title, $description, $price, $customer);    

  if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($conns['orders']);
    return false;
  }

  if (!mysqli_commit($conns['orders'])) {
    return false;
  }

  mysqli_stmt_close($stmt);
  mysqli_autocommit($conns['orders'], true);

  return true;
}

// Get all customer orders and separte them to pending and completed for him
function model_get_all_customer_orders($conns, $customer) {
  $sql = 'SELECT title, description, price, completed, creation_time, payment_time FROM orders ';
  $sql .= ' WHERE customer = ? ORDER BY creation_time ASC';

  if ($stmt = mysqli_prepare($conns['orders'], $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $customer);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title, $description, $price, $completed, $creation_time, $payment_time);

    $orders = array(
      'pending' => array(),
      'completed' => array()
    );
    while (mysqli_stmt_fetch($stmt)) {
      $orders[$completed == 1 ? 'completed' : 'pending'][] = array(
        'title' => $title,
        'description' => $description,
        'price' => $price,
        'creation_time' => date("d.m.Y H:i", strtotime($creation_time)),
        'payment_time' => date("d.m.Y H:i", strtotime($payment_time)),
      );      
    }

    mysqli_stmt_close($stmt);

    return $orders;
  }

  return false;
}

// Get all available orders for performer and separte them to pending and completed by him
function model_get_all_orders_for_performer($conns, $performer) {
  $sql = 'SELECT title, description, price, completed, creation_time, payment_time FROM orders ';
  $sql .= ' WHERE completed = 0 OR performer = ? ORDER BY creation_time ASC';

  if ($stmt = mysqli_prepare($conns['orders'], $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $performer);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title, $description, $price, $completed, $creation_time, $payment_time);

    $orders = array(
      'pending' => array(),
      'completed' => array()
    );
    while (mysqli_stmt_fetch($stmt)) {
      $orders[$completed == 1 ? 'completed' : 'pending'][] = array(
        'title' => $title,
        'description' => $description,
        'price' => $price,
        'creation_time' => date("d.m.Y H:i", strtotime($creation_time)),
        'payment_time' => date("d.m.Y H:i", strtotime($payment_time)),
      );      
    }

    mysqli_stmt_close($stmt);

    return $orders;
  }

  return false;
}