<?php
require_once('models.php');

function security_is_authorized($conns) {
  // User not authorized  
  if (!isset($_SESSION['user']) || $_SESSION['user'] == null) {
    return false;
  } else {
    // Check user exist again
    $user = model_get_user_by_email($conns, $_SESSION['user']['email']);

    // Hmm, session information not valid 
    if ($user === null) {
      return false;
    }

    return true;
  }
}

function security_update_session($conns) {
  if (isset($_SESSION['user']) && isset($_SESSION['user']['email'])) {
    $_SESSION['user'] = model_get_user_by_email($conns, $_SESSION['user']['email']);
  }
}

function security_is_customer($conns) {
  return security_session_has_role($conns, 1);
}

function security_is_performer($conns) {
  return security_session_has_role($conns, 2);
}

function security_session_has_role($conns, $role) {
  if (!security_is_authorized($conns)) {
    return false;
  }

  return $_SESSION['user']['role'] === $role;
}

function security_unauthorize() {
  unset($_SESSION['user']);
  session_unset();
  session_destroy();
}

function security_check_referer($url) {
  $port = isset($url['port']) ? ':' . $url['port'] : '';
  $port = strlen($port) > 0 ? $port : '';
  return "$url[scheme]://$url[host]$port/" === $_SERVER['HTTP_REFERER'];
}