<?php

function getShoppingCartId($userId)
{
    $db = getDbConnection();
    $sql = "SELECT id_shopping_cart FROM shopping_cart WHERE id_user = $userId";
    $result = $db->query($sql);
    $cart = $result->fetch_assoc();

    if (!$cart) {
        $sql = "INSERT INTO shopping_cart (id_user) VALUES ($userId)";
        $db->query($sql);
        return $db->insert_id;
    }

    return $cart['id_shopping_cart'];
}

function getCartItemsWithDetails($cartId)
{
    $db = getDbConnection();
    $sql = "SELECT 
                shopping_cart_item.id_item AS id_item, 
                shopping_cart_item.amount AS amount, 
                shopping_cart_item.id_shopping_cart, 
                item.name, 
                item.price, 
                item.description 
            FROM shopping_cart_item
            JOIN item ON shopping_cart_item.id_item = item.id_item
            WHERE shopping_cart_item.id_shopping_cart = $cartId";
    $result = $db->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addOrUpdateCartItem($cartId, $itemId, $quantity, $is_update = false)
{
    $db = getDbConnection();
    $sql = "SELECT amount FROM shopping_cart_item WHERE id_shopping_cart = $cartId AND id_item = $itemId";
    $result = $db->query($sql);
    $item = $result->fetch_assoc();

    if ($item) {
        if ($is_update) {
            $sql = "UPDATE shopping_cart_item SET amount = $quantity WHERE id_shopping_cart = $cartId AND id_item = $itemId";
        } else {
            $sql = "UPDATE shopping_cart_item SET amount = amount + $quantity WHERE id_shopping_cart = $cartId AND id_item = $itemId";
        }
        $db->query($sql);
    } else {
        $sql = "INSERT INTO shopping_cart_item (id_shopping_cart, id_item, amount) VALUES ($cartId, $itemId, $quantity)";
        $db->query($sql);
    }
}

function mergeSessionCart($userId, $sessionCart)
{
    $cartId = getShoppingCartId($userId);
    foreach ($sessionCart as $item) {
        addOrUpdateCartItem($cartId, $item['id_item'], $item['amount']);
    }
}

function getSessionCartItems()
{
    return $_SESSION['shoppingCart'] ?? [];
}

function setSessionCartItems($items)
{
    $_SESSION['shoppingCart'] = $items;
}

$sessionCart = getSessionCartItems();

if ($auth->isLoggedIn()) {
    $userData = $auth->getLoggedInUserData();
    $userId = $userData['id_user'];
    mergeSessionCart($userId, $sessionCart);
    setSessionCartItems([]);
    $cartId = getShoppingCartId($userId);
    $shoppingCart = getCartItemsWithDetails($cartId);
} else {
    $shoppingCart = [];
    foreach ($sessionCart as $item) {
        $db = getDbConnection();
        $sql = "SELECT id_item, name, price, description FROM item WHERE id_item = " . (int)$item['id_item'];
        $result = $db->query($sql);
        $product = $result->fetch_assoc();

        if ($product) {
            $shoppingCart[] = [
                'id_item' => $product['id_item'],
                'name' => $product['name'],
                'price' => $product['price'],
                'description' => $product['description'],
                'amount' => $item['amount']
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_item']) && isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
        $itemId = (int)$_POST['id_item'];
        $quantity = (int)$_POST['amount'];

        if ($auth->isLoggedIn()) {
            $userData = $auth->getLoggedInUserData();
            $userId = $userData['id_user'];
            $cartId = getShoppingCartId($userId);
            addOrUpdateCartItem($cartId, $itemId, $quantity, isset($_POST['is_update']));
            $shoppingCart = getCartItemsWithDetails($cartId);
        } else {
            $sessionCart = getSessionCartItems();
            $exists = false;
            foreach ($sessionCart as &$item) {
                if ($item['id_item'] == $itemId) {
                    if (isset($_POST['is_update'])) {
                        $item['amount'] = $quantity;
                    } else {
                        $item['amount'] += $quantity;
                    }
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $sessionCart[] = ['id_item' => $itemId, 'amount' => $quantity];
            }
            setSessionCartItems($sessionCart);
            $shoppingCart = $sessionCart;
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['id_item'])) {
        $itemIdToRemove = (int)$_POST['id_item'];
        if ($auth->isLoggedIn()) {
            $userData = $auth->getLoggedInUserData();
            $userId = $userData['id_user'];
            $cartId = getShoppingCartId($userId);

            $sql = "DELETE FROM shopping_cart_item WHERE id_shopping_cart = $cartId AND id_item = $itemIdToRemove";
            getDbConnection()->query($sql);

            $shoppingCart = getCartItemsWithDetails($cartId);
        } else {
            foreach ($sessionCart as $key => $item) {
                if ($item['id_item'] == $itemIdToRemove) {
                    unset($sessionCart[$key]);
                    break;
                }
            }
            setSessionCartItems($sessionCart);
            $shoppingCart = $sessionCart;
        }
    }

    $referer = $_SERVER['HTTP_REFERER'] ?? '/products';
    header('Location: ' . $referer);
    exit;
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelwagentje</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            background-color: #f4f4f4;
        }

        .cart-container {
            width: 70%;
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .cart-items, .sidebar {
            width: 48%;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-items h1, .sidebar h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .cart-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item img {
            width: 25%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item .item-details {
            flex-grow: 1;
            margin-left: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cart-item .price {
            font-size: 18px;
            font-weight: bold;
            color: #000000;
        }

        .cart-item .price-per-piece {
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .cart-item .description {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .cart-item .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-item .quantity-controls input {
            width: 50px;
            text-align: center;
            padding: 5px;
        }

        .cart-item button {
            background: #30B6FA;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cart-item button:hover {
            background: #2289bc;
        }

        .cart-item .remove-btn {
            background-color: red;
            color: white;
        }

        .sidebar {
            background-color: #ecf0f1;
        }

        .sidebar .coupon-input {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .sidebar .coupon-btn {
            background: #30B6FA;
            border: none;
            color: white;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .sidebar .coupon-btn:hover {
            background: #30B6FA;
        }

        .sidebar .summary {
            margin-top: 20px;
        }

        .sidebar .summary p {
            font-size: 16px;
            margin: 5px 0;
        }

        .sidebar .summary .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }

        .sidebar .form-group {
            margin-bottom: 20px;
        }

        input, select {
            background: white;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .payment-method, .delivery-method {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .payment-method select, .delivery-method select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .payment-method .payment-icon, .delivery-method .delivery-icon {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .order-btn {
            background: #2ecc71;
            border: none;
            color: white;
            padding: 15px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 20px;
        }

        .order-btn:hover {
            background: #27ae60;
        }

        /* Custom select option with icons */
        select option {
            padding-left: 25px;
            background-size: 20px;
            background-repeat: no-repeat;
            background-position: left center;
        }

        .delivery-method select option[data-icon="postnl"] {
            background-image: url('path_to_postnl_icon.png');
        }

        .delivery-method select option[data-icon="dhl"] {
            background-image: url('path_to_dhl_icon.png');
        }

    </style>
</head>
<body>

<div class="cart-container">
    <div class="cart-items">
        <h1>Winkelwagentje</h1>

        <?php
        $subtotal = 0;
        foreach ($shoppingCart as $item):
            $subtotal += $item['price'] * $item['amount'];
            ?>
            <div class="cart-item">
                <img src="/assets/images/product.png" alt="Product Image">
                <div class="item-details">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="description"><?= htmlspecialchars($item['description']) ?></p>
                    <p class="price">€<?= number_format($item['price'], 2) ?></p>
                    <p class="price-per-piece">Per stuk: €<?= number_format($item['price'] / $item['amount'], 2) ?></p>

                    <form action="" method="POST" class="quantity-controls">
                        <input type="number" name="amount" value="<?= $item['amount'] ?>" min="1">
                        <input type="hidden" name="id_item" value="<?= $item['id_item'] ?>">
                        <input type="hidden" name="is_update" value="true">
                        <button type="submit" name="action" value="update">Update</button>
                        <button type="submit" name="action" value="remove" class="remove-btn">Remove</button>
                    </form>


                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="sidebar">
        <h1>Betaling en Bezorging</h1>
        <div class="form-group">
            <label for="address">Adres</label>
            <input type="text" id="address" name="address" placeholder="Vul uw adres in">
        </div>

        <div class="payment-method">
            <label for="payment">Betaling Methode</label>
            <select id="payment" name="payment">
                <option value="creditcard" data-icon="creditcard">Creditcard</option>
                <option value="paypal" data-icon="paypal">PayPal</option>
                <option value="ideal" data-icon="ideal">iDEAL</option>
            </select>
            <img src="/assets/images/creditcard.png" class="payment-icon" alt="Payment Icon">
        </div>

        <div class="delivery-method">
            <label for="delivery">Bezorg Methode</label>
            <select id="delivery" name="delivery">
                <option value="pickup" data-icon="pickup">Afhalen</option>
                <option value="postnl" data-icon="postnl">PostNL</option>
                <option value="dhl" data-icon="dhl">DHL</option>
            </select>
            <img src="/assets/images/winkel.png" class="delivery-icon" alt="Delivery Icon">
        </div>

        <div class="form-group">
            <label for="coupon">Kortingscode</label>
            <input type="text" id="coupon" name="coupon" placeholder="Voer kortingscode in">
        </div>

        <button class="coupon-btn">Kortingscode toepassen</button>

        <div class="summary">
            <p>Subtotaal: €<?= number_format($subtotal, 2) ?></p>
            <p>BTW (21%): €<?= number_format($subtotal * 0.21, 2) ?></p>
            <p class="total-price">Totaal: €<?= number_format($subtotal * 1.21, 2) ?></p>

            <button class="order-btn">Bestelling Plaatsen</button>
        </div>
    </div>
</div>

<script>
    document.querySelector('#delivery').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const icon = selectedOption.getAttribute('data-icon');
        const deliveryMethod = document.querySelector('.delivery-method .delivery-icon');
        if (icon === 'postnl') {
            deliveryMethod.src = '/assets/images/postnl.png';
        } else if (icon === 'dhl') {
            deliveryMethod.src = '/assets/images/dhl.png';
        } else {
            deliveryMethod.src = '/assets/images/winkel.png';
        }
    });

    document.querySelector('#payment').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const icon = selectedOption.getAttribute('data-icon');
        const deliveryMethod = document.querySelector('.payment-method .payment-icon');
        if (icon === 'creditcard') {
            deliveryMethod.src = '/assets/images/creditcard.png';
        } else if (icon === 'ideal') {
            deliveryMethod.src = '/assets/images/ideal.png';
        } else if (icon === 'paypal') {
            deliveryMethod.src = '/assets/images/paypal.png';
        }
    });
</script>

</body>
</html>