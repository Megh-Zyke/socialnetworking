<?php

session_start();
session_unset();
session_destroy();
unset($_SESSION['email']);
  header('location:admin_login.php');