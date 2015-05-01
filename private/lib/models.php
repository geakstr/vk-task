<?php
// Make new order from customer
function model_add_order($dbs, $title, $description, $price, $customer) {
  // Disable autocommit to have transaction rollback possibility
  mysqli_autocommit($dbs['vk_db_2'], false);

  $sql = "INSERT INTO orders(title, description, price, customer) VALUES(?, ?, ?, ?)";
  $stmt = mysqli_stmt_init($dbs['vk_db_2']);

  if (!mysqli_stmt_prepare($stmt, $sql)) {
    return false;
  }

  mysqli_stmt_bind_param($stmt, "ssdi", $title, $description, $price, $customer);    

  if (!mysqli_stmt_execute($stmt)) {
    mysqli_rollback($dbs['vk_db_2']);
    return false;
  }

  if (!mysqli_commit($dbs['vk_db_2'])) {
    return false;
  }

  mysqli_stmt_close($stmt);
  mysqli_autocommit($dbs['vk_db_2'], true);

  return true;
}

// Get all orders for customer and separte them to pending and completed
function model_get_all_customer_orders($dbs, $customer) {
  $sql = "SELECT title, description, price, completed, creation_time, payment_time FROM orders ";
  $sql .= " WHERE customer = ? ORDER BY creation_time ASC";

  if ($stmt = mysqli_prepare($dbs['vk_db_2'], $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title, $description, $price, $completed, $creation_time, $payment_time);

    $result = array(
      'pending' => array(),
      'completed' => array()
    );
    while (mysqli_stmt_fetch($stmt)) {
      $result[$completed == 1 ? 'completed' : 'pending'][] = array(
        'title' => $title,
        'description' => $description,
        'price' => $price,
        'creation_time' => date("d.m.Y H:i", strtotime($creation_time)),
        'payment_time' => date("d.m.Y H:i", strtotime($payment_time)),
      );      
    }

    $result['pending_cnt'] = count($result['pending']);
    $result['completed_cnt'] = count($result['completed']);

    mysqli_stmt_close($stmt);

    return $result;
  }

  return false;
}