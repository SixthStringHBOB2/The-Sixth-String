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

    if (isset($_POST['remove_id_item'])) {
        $itemIdToRemove = (int)$_POST['remove_id_item'];
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
            color: #2c3e50; /* Matching product.php heading color */
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
            color: #27ae60; /* Price green color */
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

        .cart-items, .sidebar {
            border-radius: 10px;
        }

    </style>
</head>
<body>

<div class="cart-container">
    <!-- Cart Items -->
    <div class="cart-items">
        <h1>Winkelmandje</h1>

        <?php if (empty($shoppingCart)): ?>
            <p>Uw Winkelmandje is leeg.</p>
        <?php else: ?>
            <?php foreach ($shoppingCart as &$item): ?>
                <div class="cart-item">
                    <img src="/assets/images/product.png" alt="Product Image">
                    <div class="item-details">
                        <h2><?= htmlspecialchars($item['name']) ?></h2>
                        <p class="description"><?= htmlspecialchars($item['description'] ?? 'No description available') ?></p>

                        <div class="quantity-controls">
                            <form method="POST" style="display:inline;">
                                <input type="number" name="amount" value="<?= $item['amount'] ?>" min="1">
                                <input type="text" name="is_update" value="true" hidden>
                                <button type="submit" name="id_item" value="<?= $item['id_item'] ?>">Update</button>
                            </form>
                        </div>
                        <div class="price">€<?= number_format($item['amount'] * $item['price'], 2) ?></div>
                    </div>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="remove_id_item" value="<?= $item['id_item'] ?>">
                        <button type="submit">Verwijder</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="sidebar">
        <h1>Bestelling overzicht</h1>

        <input class="coupon-input" type="text" placeholder="Voer kortingscode in">
        <button class="coupon-btn">Pas Kortingscode Toe</button>

        <div class="summary">
            <?php
            $subtotal = 0;
            foreach ($shoppingCart as $item) {
                $subtotal += $item['amount'] * $item['price'];
            }

            $tax = $subtotal * 0.21;
            $total = $subtotal + $tax;
            ?>
            <p>Excl. BTW: €<?= number_format($subtotal, 2) ?></p>
            <p>BTW (21%): €<?= number_format($tax, 2) ?></p>
            <p class="total-price">Totaal (incl. BTW): €<?= number_format($total, 2) ?></p>
        </div>
    </div>
</div>

</body>
</html>
