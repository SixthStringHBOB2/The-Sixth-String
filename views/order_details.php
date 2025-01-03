<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orderdetails</title>
    <link rel="stylesheet" href="/assets/css/account.css">
    <link rel="stylesheet" href="/assets/css/orders.css">
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h3>Account Menu</h3>
        <ul>
            <li><a href="/account">Profiel</a></li>
            <li><a href="/orders">Bestellingen</a></li>
            <li><a href="/logout">Uitloggen</a></li>
        </ul>
    </div>

    <div class="account-content">
        <?php
        $userData = $auth->getLoggedInUserData();

        if (!$userData || !isset($userData['id_user'])) {
            echo "<p>User not logged in.</p>";
            exit;
        }

        $mysqli = getDbConnection();
        $userId = $userData['id_user'];
        $orderId = $_GET['id'];

        $query = "
            SELECT o.*, s.name AS status 
            FROM `order` o
            JOIN status s ON o.id_status = s.id_status
            WHERE o.id_order = ? AND o.id_user = ?
        ";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            echo "<p>Order not found.</p>";
            exit;
        }

        $itemQuery = "
            SELECT oi.*, i.name AS item_name 
            FROM order_items oi
            JOIN item i ON oi.id_item = i.id_item
            WHERE oi.id_order = ?
        ";

        $itemStmt = $mysqli->prepare($itemQuery);
        $itemStmt->bind_param("i", $orderId);
        $itemStmt->execute();

        $itemResult = $itemStmt->get_result();

        $items = [];
        while ($item = $itemResult->fetch_assoc()) {
            $items[] = $item;
        }
        ?>

        <h1>Ordernummer: <?php echo htmlspecialchars($order['id_order']); ?></h1>
        <p><strong>Datum:</strong> <?php echo htmlspecialchars(date('d-m-Y H:i', strtotime($order['order_date']))); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

        <h2>Items</h2>
        <table class="order-items-table">
            <thead>
            <tr>
                <th>Product</th>
                <th>Hoeveelheid</th>
                <th>Prijs</th>
                <th>Totaal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><a href="/producten/<?php echo htmlspecialchars($item['id_item']); ?>"> <?php echo htmlspecialchars($item['item_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>€<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    <td>€<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
