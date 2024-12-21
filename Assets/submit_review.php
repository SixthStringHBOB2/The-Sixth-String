<?php

function getDbConnectionn() {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de ingevulde gegevens op
    $name = (trim($_POST['name']));
    $description = (trim($_POST['description']));
    $rating = intval($_POST['rating']);
    $id_user = 1; //ALS DE REVIEWS AAN EEN GEBRUIKER GEKOPPELD GAAN WORDEN DEZE GEBRUIKEN

    // Maak verbinding met de database
    $conn = getDbConnectionn();

    // MAKEN NIEUW REVIEW_ID DOOR TE KIJKEN NAAR HET HOOGSTE ID AANWEZIG + 1
    $result = $conn->query("
        SELECT MAX(id_review) AS max_id 
        FROM thesixthstring.review");
    $row = $result->fetch_assoc();
    $next_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

    // REVIEW TOEVOEGEN
    $stmt = $conn->prepare("
        INSERT INTO thesixthstring.review (id_review, name, description, rating, id_item, id_user) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiis", $next_id, $name, $description, $rating, $id_item, $id_user);

    if ($stmt->execute()) {
        echo '<p>Bedankt voor het plaatsen van een review!</p>';
    } else {
        // FOUTMELDING
        echo '<p>Er is een fout opgetreden: ' . $conn->error . '</p>';
    }

    // SLUITEN VERBINDING
    $stmt->close();
    $conn->close();}

?>