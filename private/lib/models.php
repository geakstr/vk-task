<?php
function model_get_user_by_email($conns, $email) {
  return model_get_user_by_field($conns, 'email', 's', $email);
}

function model_get_user_by_id($conns, $id) {
  return model_get_user_by_field($conns, 'id', 'i', $id);
}

function model_get_user_by_field($conns, $field, $type, $val) {
  $sql = "SELECT id, email, fio, balance, role, creation_time FROM users WHERE $field = ?";

  if ($stmt = mysqli_prepare($conns['users'], $sql)) {    
    mysqli_stmt_bind_param($stmt, $type, $val);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id,
                                   $email,
                                   $fio,
                                   $balance,
                                   $role,
                                   $creation_time);

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

function model_get_order_by_id($conns, $id) {
  $sql = "SELECT id, title, description, customer, performer, price, completed, payment_time, creation_time ";
  $sql .= " FROM orders WHERE id = ?";

  if ($stmt = mysqli_prepare($conns['orders'], $sql)) {    
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id,
                                   $title,
                                   $description,
                                   $customer,
                                   $performer,
                                   $price,
                                   $completed,
                                   $payment_time,
                                   $creation_time);

    $order = null;
    while (mysqli_stmt_fetch($stmt)) {
      $order = array(
        'id' => $id,
        'title' => $title,
        'description' => $description,
        'customer' => $customer,
        'performer' => $performer,        
        'price' => $price,
        'completed' => $completed,
        'creation_time' => date("d.m.Y H:i", strtotime($creation_time)),
        'payment_time' => date("d.m.Y H:i", strtotime($payment_time)),
      );      
    };
    mysqli_stmt_close($stmt);

    return $order;
  }

  return null;
}

function model_get_customer_total_orders_price($conns, $customer_id) {
  $sql = 'SELECT SUM(price) FROM orders WHERE completed = 0 AND customer = ?';

  if ($stmt = mysqli_prepare($conns['orders'], $sql)) {    
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total_price);

    while (mysqli_stmt_fetch($stmt)) {};
    mysqli_stmt_close($stmt);

    return $total_price;
  }

  return 0;
}

// Make new order from customer
function model_add_order($conns, $title, $description, $price, $customer_id) {
  // Make new order
  mysqli_autocommit($conns['orders'], false);
  $sql = 'INSERT INTO orders(title, description, price, customer) VALUES(?, ?, ?, ?)';
  $stmt = mysqli_stmt_init($conns['orders']);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
  }
  mysqli_stmt_bind_param($stmt, 'ssdi', $title, $description, $price, $customer_id);    
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

function model_perform_order($conns, $order_id, $performer_id) {
  $order = model_get_order_by_id($conns, $order_id);
  if ($order === null) {
    return false;
  }

  mysqli_autocommit($conns['orders'], false);
  mysqli_autocommit($conns['users'], false);  

  // Update order information
  $sql = 'UPDATE orders SET completed = 1, performer = ?, payment_time = ? WHERE id = ?';
  $stmt_order = mysqli_stmt_init($conns['orders']);
  if (!mysqli_stmt_prepare($stmt_order, $sql)) {
    return false;
  }
  mysqli_stmt_bind_param($stmt_order, 'isi', $performer_id, date("Y-m-d H:i:s"), $order_id);
  
  // Update customer balance
  $sql = 'UPDATE users SET balance = (balance - ?) WHERE id = ?';
  $stmt_cust = mysqli_stmt_init($conns['users']);  
  if (!mysqli_stmt_prepare($stmt_cust, $sql)) {
    return false;
  }
  mysqli_stmt_bind_param($stmt_cust, 'ii', $order['price'], $order['customer']);
  
  // Update performer balance
  $sql = 'UPDATE users SET balance = (balance + ?) WHERE id = ?';
  $stmt_perf = mysqli_stmt_init($conns['users']);
  if (!mysqli_stmt_prepare($stmt_perf, $sql)) {
    return false;
  }
  mysqli_stmt_bind_param($stmt_perf, 'ii', $order['price'], $performer_id);
  
  // Try executing queries. If something wrong — rollback all
  if (!mysqli_stmt_execute($stmt_order) || !mysqli_stmt_execute($stmt_cust) || !mysqli_stmt_execute($stmt_perf)) {
    mysqli_rollback($conns['orders']);
    mysqli_rollback($conns['users']);
    return false;
  }  

  if (!mysqli_commit($conns['orders']) || !mysqli_commit($conns['users'])) {
    return false;
  }  


  mysqli_stmt_close($stmt_order);
  mysqli_stmt_close($stmt_cust);
  mysqli_stmt_close($stmt_perf);


  mysqli_autocommit($conns['orders'], true);
  mysqli_autocommit($conns['users'], true);

  return true;  
}

// Get all customer orders and separte them to pending and completed for him
function model_get_all_customer_orders($conns, $customer) {
  $sql = 'SELECT id, title, description, price, completed, creation_time, payment_time ';
  $sql .= ' FROM orders WHERE customer = ?';

  return model_get_all_orders_width_sql($conns, $sql, $customer);
}

// Get all available orders for performer and separte them to pending and completed by him
function model_get_all_orders_for_performer($conns, $performer) {
  $sql = 'SELECT id, title, description, price, completed, creation_time, payment_time ';
  $sql .= ' FROM orders WHERE completed = 0 OR performer = ?';

  return model_get_all_orders_width_sql($conns, $sql, $performer);
}

function model_get_all_orders_width_sql($conns, $sql, $user_id) {
  if ($stmt = mysqli_prepare($conns['orders'], $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id,
                                   $title,
                                   $description,
                                   $price,
                                   $completed,
                                   $creation_time,
                                   $payment_time);

    $orders = array(
      'pending' => array(),
      'completed' => array()
    );
    while (mysqli_stmt_fetch($stmt)) {
      $orders[$completed == 1 ? 'completed' : 'pending'][] = array(
        'id' => $id,
        'title' => $title,
        'description' => $description,
        'price' => $price,
        'creation_time' => date("d.m.Y H:i", strtotime($creation_time)),
        'payment_time' => date("d.m.Y H:i", strtotime($payment_time)),
      );      
    }

    function pending_orders_cmp($a, $b) {
      $t1 = strtotime($a['creation_time']);
      $t2 = strtotime($b['creation_time']);
      return $t1 - $t2;
    }

    function completed_orders_cmp($a, $b) {
      $t1 = strtotime($a['payment_time']);
      $t2 = strtotime($b['payment_time']);
      return $t2 - $t1;
    }

    usort($orders['pending'], 'pending_orders_cmp');
    usort($orders['completed'], 'completed_orders_cmp');

    mysqli_stmt_close($stmt);

    return $orders;
  }

  return false;
}

function model_balance_refill($conns, $customer, $fee) {
  $sql = 'UPDATE users SET balance = (balance + ?) WHERE id = ?';
  $stmt = mysqli_stmt_init($conns['users']);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
  }
  mysqli_stmt_bind_param($stmt, 'ii', $fee, $customer);
  if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($conns['users']);
    return false;
  }
  if (!mysqli_commit($conns['users'])) {
    return false;
  }
  mysqli_stmt_close($stmt);
  mysqli_autocommit($conns['users'], true);

  return true;
}