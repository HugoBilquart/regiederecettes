<?php
  include('functions.php');
  session_start();
  if($_SESSION) {
    $_SESSION = array();
    session_destroy();
    unset($_SESSION);
    redirectToIndex();
  }
  else {
    redirectToIndex();
  }
?>