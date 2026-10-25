<?php require 'includes/auth.php';
header('Location: ' . BASE_URL . (isset($_SESSION['user']) ? '/' . $_SESSION['user']['role'] . '/dashboard.php' : '/login.php'));
