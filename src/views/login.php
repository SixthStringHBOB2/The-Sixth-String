<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth = new Auth();

    if ($auth->login($email, $password)) {
        header('Location: /account');
        exit;
    } else {
        echo 'Ongeldig e-mailadres of wachtwoord.';
    }
}

?>
<link rel="stylesheet" href="/assets/css/auth.css">

<form method="POST" action="/login">
    <label for="email">E-mail</label>
    <input type="email" name="email" placeholder="Voer je e-mailadres in" required>

    <label for="password">Wachtwoord</label>
    <input type="password" name="password" placeholder="Voer je wachtwoord in" required>

    <button type="submit">Inloggen</button>
</form>
