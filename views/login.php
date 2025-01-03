<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirectPath = $_POST['redirect_path'] ?? '/account';

    $auth = new Auth();

    if ($auth->login($email, $password)) {
        header("Location: $redirectPath");
        exit;
    } else {
        echo 'Ongeldig e-mailadres of wachtwoord.';
    }
}

?>
<link rel="stylesheet" href="/assets/css/auth.css">

<form method="POST" action="/login">
    <input type="hidden" name="redirect_path" value="<?php echo htmlspecialchars($_GET['redirect_path'] ?? '/account'); ?>">
    <label for="email">E-mail</label>
    <input type="email" name="email" placeholder="Voer je e-mailadres in" required>

    <label for="password">Wachtwoord</label>
    <input type="password" name="password" placeholder="Voer je wachtwoord in" required>

    <button type="submit">Inloggen</button>
</form>
