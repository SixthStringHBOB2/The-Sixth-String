<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Bestellingen</title>
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
        <h1>Mijn Bestellingen</h1>

        <?php
        $mysqli = getDbConnection();
        $userId = $_SESSION['user']['id'];

        $query = "
            SELECT o.id_order, o.order_date, 
                   (SELECT COUNT(*) FROM order_items WHERE id_order = o.id_order) AS item_count,
                   (SELECT SUM(price * quantity) FROM order_items WHERE id_order = o.id_order) AS total_amount,
                   s.name AS status
            FROM `order` o
            JOIN status s ON o.id_status = s.id_status
            WHERE o.id_user = ?
        ";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        $orders = [];
        while ($order = $result->fetch_assoc()) {
            $orders[] = $order;
        }
        ?>

        <?php if (!empty($orders)): ?>
            <table class="orders-table">
                <thead>
                <tr>
                    <th>Ordernummer</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Aantal Artikelen</th>
                    <th>Totaalbedrag</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id_order']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($order['order_date']))); ?></td>
                        <td><?php echo htmlspecialchars(date('H:i', strtotime($order['order_date']))); ?></td>
                        <td><?php echo htmlspecialchars($order['item_count']); ?></td>
                        <td>€<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><a href="/order/<?php echo htmlspecialchars($order['id_order']); ?>">Details</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>U heeft nog geen bestellingen geplaatst.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
