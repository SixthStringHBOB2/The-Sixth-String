<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $houseNumber = $_POST['house_number'] ?? '';
    $zipCode = $_POST['zip_code'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $email = $_POST['email'] ?? '';
    $paymentMethod = $_POST['payment'] ?? '';
    $deliveryMethod = $_POST['delivery'] ?? '';
    $couponCode = $_POST['coupon'] ?? '';

    try {
        if ($auth->isLoggedIn()) {
            $userId = $auth->getLoggedInUserData()['id_user'];
        } else {
            $userId = $shoppingCartService->getOrCreateUserIdByEmail(
                $email,
                $_POST['first_name'] ?? 'Guest',
                $_POST['last_name'] ?? 'User',
                $address,
                $city,
                $houseNumber,
                $zipCode,
                $country
            );

        }

        $orderId = $shoppingCartService->createOrder($userId);
    } catch (Exception $e) {
        echo "<h1>Fout</h1>";
        echo "<p>Er is een probleem met je bestelling. Probeer het later opnieuw.</p>";
        echo "<p>Fout: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Bevestigd</title>
    <link rel="stylesheet" href="/assets/css/account.css">
    <link rel="stylesheet" href="/assets/css/orders.css">
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h3>Order Menu</h3>
        <ul>
            <li><a href="/">Startpagina</a></li>
            <li><a href="../index.php">Mijn Bestellingen</a></li>
            <li><a href="/order/<?= $orderId ?>">Bekijk Deze Bestelling</a></li>
        </ul>
    </div>

    <div class="account-content">
        <h1>Bestelling Bevestigd</h1>
        <p>Je bestelling is succesvol geplaatst!</p>

        <div class="order-details">
            <h3>Bestelgegevens</h3>
            <p><strong>Order ID:</strong> <?= $orderId ?></p>
            <p><strong>Verzendadres:</strong> <?= htmlspecialchars($address . ' ' . $houseNumber . ', ' . $zipCode . ', ' . $city . ', ' . $country) ?></p>
            <p><strong>E-mailadres:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Bezorgmethode:</strong> <?= htmlspecialchars($deliveryMethod) ?></p>
            <p><strong>Betalingsmethode:</strong> <?= htmlspecialchars($paymentMethod) ?></p>
        </div>
    </div>
</div>

</body>
</html>
