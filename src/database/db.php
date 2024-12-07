<?php
function getDbConnection() {
//    $host = getenv('MYSQL_HOST');
//    $dbname = getenv('MYSQL_DATABASE');
//    $username = getenv('MYSQL_USER');
//    $password = getenv('MYSQL_PASSWORD');

    $host = "192.168.1.11";
    $dbname = "thesixthstring";
    $username = "default";
    $password = "rEN28Sd8?W|L6FquVky>";

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
