<?php
session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'restaurant_app';
const DB_USER = 'root';
const DB_PASS = '';

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'admin123';

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (Exception $e) {
  exit("DB Connection failed");
}

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
