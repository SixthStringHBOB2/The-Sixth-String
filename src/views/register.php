<?php

use services\Auth;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $houseNumber = $_POST['house_number'] ?? 0;
    $zipCode = $_POST['zip_code'] ?? '';

    require_once 'services/authService.php';
    $auth = new Auth();

    $auth->register($firstName, $lastName, $email, $password, $address, $city, $houseNumber, $zipCode, $country);
    if ($auth->login($email, $password)) {
        header('Location: /account');
        exit;
    } else {
        echo 'Registration was successful, but login failed. Please try again.';
    }
}

?>
<link rel="stylesheet" href="/assets/css/auth.css">

<form method="POST" action="../index.php">
    <h2>Account Informatie</h2>

    <label for="first_name">Voornaam</label>
    <input type="text" name="first_name" placeholder="Voer je voornaam in" required>

    <label for="last_name">Achternaam</label>
    <input type="text" name="last_name" placeholder="Voer je achternaam in" required>

    <label for="email">E-mail</label>
    <input type="email" name="email" placeholder="Voer je e-mailadres in" required>

    <label for="password">Wachtwoord</label>
    <input type="password" name="password" placeholder="Kies een wachtwoord" required>

    <h2>Adres Informatie</h2>

    <label for="address">Adres</label>
    <input type="text" name="address" placeholder="Voer je adres in" required>

    <label for="city">Stad</label>
    <input type="text" name="city" placeholder="Voer je stad in" required>

    <label for="house_number">Huisnummer</label>
    <input type="text" name="house_number" placeholder="Voer je huisnummer in" required>

    <label for="country">Land</label>
    <input type="text" name="country" placeholder="Voer je land in" required>

    <label for="zip_code">Postcode</label>
    <input type="text" name="zip_code" placeholder="Voer je postcode in" required>

    <button type="submit">Registreren</button>
</form>
