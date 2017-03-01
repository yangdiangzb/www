<?php
  try {
    $hostname = "localhost";
    $dbname = "fyp";
    $username = "root";
    $pw = "";
    $dbh = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
  } catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
  }
?>