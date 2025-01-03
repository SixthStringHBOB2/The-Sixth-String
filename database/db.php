<?php
function getDbConnection() {
    $host = "localhost";
    $dbname = "thesixthstring";
    $username = "thesixthstring";
    $password = "HFIU67135dhaf";

    // Check for missing environment variables
    if (!$host || !$dbname || !$username || !$password) {
        die('Missing environment variables for database connection');
    }

    // Create and return the MySQLi connection
    $mysqli = new mysqli($host, $username, $password, $dbname);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
