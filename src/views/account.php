<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userName); ?></title>
    <link rel="stylesheet" href="/assets/css/account.css">
    <style>
        .admin-links a {
            color: red;
            text-decoration: none;
        }
        .admin-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h3>Account Menu</h3>
        <ul>
            <li><a href="../index.php">Profiel</a></li>
            <li><a href="../index.php">Bestellingen</a></li>
            <li><a href="../index.php">Uitloggen</a></li>
        </ul>

        <?php if ($auth->isAdminLogin()): ?>
            <h3>Admin</h3>
            <ul class="admin-links">
                <li><a href="../index.php">CRUD Pagina</a></li>
                <li><a href="../index.php">Dashboard</a></li>
            </ul>
        <?php endif; ?>
    </div>

    <div class="account-content">
        <h1>
            <?php if ($auth->isAdminLogin()): ?>
                <span class="admin-badge">Admin</span>
            <?php endif; ?>
            Welkom, <?php echo htmlspecialchars($userName); ?>!
        </h1>
        <div class="user-info">
            <h2>Uw Persoonlijke Gegevens</h2>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($userData['email_address']); ?></p>
            <p><strong>Voornaam:</strong> <?php echo htmlspecialchars($userData['first_name']); ?></p>
            <p><strong>Achternaam:</strong> <?php echo htmlspecialchars($userData['last_name']); ?></p>

            <h2>Uw Adres</h2>
            <p><strong>Adres:</strong> <?php echo htmlspecialchars($userData['address']); ?></p>
            <p><strong>Stad:</strong> <?php echo htmlspecialchars($userData['city']); ?></p>
            <p><strong>Land:</strong> <?php echo htmlspecialchars($userData['country']); ?></p>
            <p><strong>Huisnummer:</strong> <?php echo htmlspecialchars($userData['house_number']); ?></p>
            <p><strong>Postcode:</strong> <?php echo htmlspecialchars($userData['zip_code']); ?></p>
        </div>
        <div class="logout-section">
            <form action="../index.php" method="POST">
                <button type="submit" class="logout-button">Uitloggen</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>