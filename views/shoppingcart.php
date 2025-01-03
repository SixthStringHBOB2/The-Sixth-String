<?php

$sessionCart = $shoppingCartService->getSessionCartItems();

$shoppingCart = null;
$userData = null;
if ($auth->isLoggedIn()) {
    $userData = $auth->getLoggedInUserData();
    $userId = $userData['id_user'];
    $shoppingCart = $shoppingCartService->getCartItems($userId);
} else {
    $shoppingCart = $shoppingCartService->getSessionCartItems();
}

if ($shoppingCart === null) {
    $shoppingCart = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update item in cart
    if (isset($_POST['id_item']) && isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
        $itemId = (int)$_POST['id_item'];
        $quantity = (int)$_POST['amount'];

        if ($auth->isLoggedIn()) {
            $userData = $auth->getLoggedInUserData();
            $userId = $userData['id_user'];
            $isUpdate = isset($_POST['is_update']);
            $shoppingCartService->addItemToCart($userId, $itemId, $quantity, $isUpdate);
            $shoppingCart = $shoppingCartService->getCartItems($userId);
        } else {
            $isUpdate = isset($_POST['is_update']);
            if ($isUpdate) {
                $shoppingCartService->updateSessionCartItem($itemId, $quantity);
            } else {
                $shoppingCartService->addToSessionCart($itemId, $quantity);
            }
            $shoppingCart = $shoppingCartService->getSessionCartItems();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['id_item'])) {
        $itemIdToRemove = (int)$_POST['id_item'];

        if ($auth->isLoggedIn()) {
            $userData = $auth->getLoggedInUserData();
            $userId = $userData['id_user'];
            $shoppingCartService->removeItemFromCart($userId, $itemIdToRemove);
            $shoppingCart = $shoppingCartService->getCartItems($userId);
        } else {
            $shoppingCartService->removeSessionCartItem($itemIdToRemove);
            $shoppingCart = $shoppingCartService->getSessionCartItems();
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
            background-color: #D9D9D9;
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
            background: linear-gradient(to right, #C7914E, #61584D);
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
        }

        .cart-item button:hover {
            background: linear-gradient(to right, #61584D, #C7914E);
        }

        .cart-item .remove-btn {
            background-color: red;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
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
            background: linear-gradient(to right, #C7914E, #61584D);
            border: none;
            color: white;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .sidebar .coupon-btn:hover {
            background: linear-gradient(to right, #61584D, #C7914E);
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
            margin-bottom: 4px;
        }

        input, select {
            background: white;
            width: 90%;
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
            background: linear-gradient(to right, #C7914E, #61584D);
            border: none;
            color: white;
            padding: 15px;
            width: 100%;
            border-radius: 15px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .order-btn:hover {
            background: linear-gradient(to right, #61584D, #C7914E);
        }

        select option {
            padding-left: 25px;
            background-size: 20px;
            background-repeat: no-repeat;
            background-position: left center;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + label {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="radio"] + label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #61584D;
            background-color: white;
            transition: background-color 0.3s, border-color 0.3s;
        }

        input[type="radio"]:checked + label:before {
            background-color: #61584D;
            border-color: #C7914E;
        }

        input[type="radio"]:checked + label {
            color: #61584D;
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
                    <p class="price">€<?= number_format($item['price'] * $item['amount'], 2) ?></p>
                    <p class="price-per-piece">Per stuk: €<?= number_format($item['price'], 2) ?></p>

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
        <form action="/order-confirmation" method="POST">
            <div class="form-group">
                <label for="address">Straat</label>
                <input
                        type="text"
                        id="address"
                        name="address"
                        value="<?= $userData ? htmlspecialchars($userData['address']) : '' ?>"
                        placeholder="Vul uw straat in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="shipping address-line1">
            </div>

            <div class="form-group">
                <label for="house_number">Huisnummer</label>
                <input
                        type="number"
                        id="house_number"
                        name="house_number"
                        value="<?= $userData ? htmlspecialchars($userData['house_number']) : '' ?>"
                        placeholder="Vul uw huisnummer in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="shipping address-line2">
            </div>

            <div class="form-group">
                <label for="zip_code">Postcode</label>
                <input
                        type="text"
                        id="zip_code"
                        name="zip_code"
                        value="<?= $userData ? htmlspecialchars($userData['zip_code']) : '' ?>"
                        placeholder="Vul uw postcode in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="shipping postal-code">
            </div>

            <div class="form-group">
                <label for="city">Stad</label>
                <input
                        type="text"
                        id="city"
                        name="city"
                        value="<?= $userData ? htmlspecialchars($userData['city']) : '' ?>"
                        placeholder="Vul uw stad in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="shipping address-level2">
            </div>

            <div class="form-group">
                <label for="country">Land</label>
                <input
                        type="text"
                        id="country"
                        name="country"
                        value="<?= $userData ? htmlspecialchars($userData['country']) : '' ?>"
                        placeholder="Vul uw land in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="shipping country">
            </div>

            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= $userData ? htmlspecialchars($userData['email_address']) : '' ?>"
                        placeholder="Vul uw e-mailadres in"
                    <?= $userData ? 'readonly' : 'required' ?>
                        autocomplete="email">
            </div>

            <div class="payment-method">
                <label for="payment">Betaling Methode</label>
                <select id="payment" name="payment" autocomplete="off">
                    <option value="creditcard" data-icon="creditcard">Creditcard</option>
                    <option value="paypal" data-icon="paypal">PayPal</option>
                    <option value="ideal" data-icon="ideal">iDEAL</option>
                </select>
                <img src="/assets/images/creditcard.png" class="payment-icon" alt="Payment Icon">
            </div>

            <div class="delivery-method">
                <label for="delivery">Bezorg Methode</label>
                <select id="delivery" name="delivery" autocomplete="off">
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

            <div class="summary">
                <?php
                $subtotalWithoutVAT = $subtotal / 1.21;
                $vatAmount = $subtotal - $subtotalWithoutVAT;
                ?>

                <p>Subtotaal (excl. BTW): €<?= number_format($subtotalWithoutVAT, 2) ?></p>
                <p>BTW (21%): €<?= number_format($vatAmount, 2) ?></p>
                <p class="total-price">Totaal (incl. BTW): €<?= number_format($subtotal, 2) ?></p>
            </div>

            <button type="submit" class="order-btn">Bestelling Plaatsen</button>
        </form>
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
