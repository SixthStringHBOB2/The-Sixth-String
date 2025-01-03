<?php
session_start();
include './database/dp.php';

ini_set('display_errors', 1);
function getDbConnectionn() {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de ingevulde gegevens op
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $rating = intval($_POST['rating']);
    $id_user = 1; // Standaard gebruiker
    $id_item = isset($_POST['id_item']) ? intval($_POST['id_item']) : null;

    // Controleer of id_item is meegegeven
    if ($id_item === null || $id_item <= 0) {
        die('id_item is niet geldig of ontbreekt.');
    }

    // Maak verbinding met de database
    $conn = getDbConnectionn();

    // Maak een nieuw id_review
    $result = $conn->query("SELECT MAX(id_review) AS max_id FROM thesixthstring.review");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

    // Voeg de review toe
    $stmt = $conn->prepare("
        INSERT INTO thesixthstring.review (id_review, name, description, rating, id_item, id_user) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiis", $next_id, $name, $description, $rating, $id_item, $id_user);

    if ($stmt->execute()) {
        echo '<p>Bedankt voor het plaatsen van een review!</p>';
    } else {
        echo '<p>Er is een fout opgetreden: ' . $conn->error . '</p>';
    }

    $stmt->close();
    $conn->close();

    // Redirect terug naar de originele pagina
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>