<?php

class ShoppingCartService
{
    private $db;
    private $auth;

    public function __construct($auth, $db)
    {
        $this->auth = $auth;
        $this->db = $db;
    }

    public function getShoppingCartId($userId)
    {
        $sql = "SELECT id_shopping_cart FROM shopping_cart WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            $sql = "INSERT INTO shopping_cart (id_user) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            return $stmt->insert_id;
        }

        return $result['id_shopping_cart'];
    }

    public function getCartItems($userId)
    {
        // First, merge session cart into the database if there are session items and user is logged in
        $this->mergeSessionCartToDatabase($userId);

        // Get the cart items from the database
        $cartId = $this->getShoppingCartId($userId);
        $sql = "SELECT item.id_item, item.name, item.price, item.description, shopping_cart_item.amount
            FROM shopping_cart_item
            JOIN item ON shopping_cart_item.id_item = item.id_item
            WHERE shopping_cart_item.id_shopping_cart = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function mergeSessionCartToDatabase($userId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['shopping_cart']) && !empty($_SESSION['shopping_cart'])) {
            $cartId = $this->getShoppingCartId($userId);

            foreach ($_SESSION['shopping_cart'] as $sessionItem) {
                $itemId = $sessionItem['id_item'];
                $amount = $sessionItem['amount'];

                $sql = "SELECT amount FROM shopping_cart_item WHERE id_shopping_cart = ? AND id_item = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("ii", $cartId, $itemId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();

                if ($result) {
                    $newAmount = $result['amount'] + $amount;
                    $sql = "UPDATE shopping_cart_item SET amount = ? WHERE id_shopping_cart = ? AND id_item = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("iii", $newAmount, $cartId, $itemId);
                    $stmt->execute();
                } else {
                    $sql = "INSERT INTO shopping_cart_item (id_shopping_cart, id_item, amount) VALUES (?, ?, ?)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("iii", $cartId, $itemId, $amount);
                    $stmt->execute();
                }
            }

            unset($_SESSION['shopping_cart']);
        }
    }

    public function getCartCount()
    {
        if ($this->auth->isLoggedIn()) {
            $userId = $this->auth->getUserId();
            $cartId = $this->getShoppingCartId($userId);
            $sql = "SELECT SUM(amount) AS total_items FROM shopping_cart_item WHERE id_shopping_cart = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $cartId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return isset($result['total_items']) ? $result['total_items'] : 0;
        } else {
            $cartItems = $this->getSessionCartItems();
            $totalItems = 0;
            foreach ($cartItems as $item) {
                $totalItems += $item['amount'];
            }
            return $totalItems;
        }
    }

    public function addItemToCart($userId, $itemId, $amount, $isUpdate = false)
    {
        $cartId = $this->getShoppingCartId($userId);
        $sql = "SELECT amount FROM shopping_cart_item WHERE id_shopping_cart = ? AND id_item = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cartId, $itemId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $newAmount = $isUpdate ? $amount : $result['amount'] + $amount;
            $sql = "UPDATE shopping_cart_item SET amount = ? WHERE id_shopping_cart = ? AND id_item = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iii", $newAmount, $cartId, $itemId);
        } else {
            $sql = "INSERT INTO shopping_cart_item (id_shopping_cart, id_item, amount) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iii", $cartId, $itemId, $amount);
        }

        $stmt->execute();
    }

    public function removeItemFromCart($userId, $itemId)
    {
        $cartId = $this->getShoppingCartId($userId);
        $sql = "DELETE FROM shopping_cart_item WHERE id_shopping_cart = ? AND id_item = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cartId, $itemId);
        $stmt->execute();
    }

    public function getSessionCartItems()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cartItems = $_SESSION['shopping_cart'] ?? [];

        foreach ($cartItems as &$item) {
            $productDetails = $this->getProductDetails($item['id_item']);

            if ($productDetails) {
                $item['name'] = $productDetails['name'] ?? 'Unknown Product';
                $item['description'] = $productDetails['description'] ?? 'No description available';
                $item['price'] = $productDetails['price'] ?? 0;
            }
        }

        return $cartItems;
    }

    public function getProductDetails($itemId)
    {
        $query = "SELECT name, description, price FROM item WHERE id_item = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function addToSessionCart($itemId, $amount)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['shopping_cart'])) {
            $_SESSION['shopping_cart'] = [];
        }

        if (isset($_SESSION['shopping_cart'][$itemId])) {
            $_SESSION['shopping_cart'][$itemId]['amount'] += $amount;
        } else {
            $_SESSION['shopping_cart'][$itemId] = [
                'id_item' => $itemId,
                'amount' => $amount,
            ];
        }
    }

    public function updateSessionCartItem($itemId, $amount)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['shopping_cart'][$itemId])) {
            $_SESSION['shopping_cart'][$itemId]['amount'] = $amount;
        }
    }

    public function removeSessionCartItem($itemId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['shopping_cart'][$itemId]);
    }

    public function createOrder($userId)
    {
        $mysqli = $this->db;
        $cartItems = $userId ? $this->getCartItems($userId) : $this->getSessionCartItems();

        if (empty($cartItems)) {
            throw new Exception("Cannot create an order with an empty cart.");
        }

        $mysqli->begin_transaction();

        try {
            $statusId = 1;
            $stmt = $mysqli->prepare("INSERT INTO `order` (order_date, id_status, id_user) VALUES (NOW(), ?, ?)");
            $stmt->bind_param("ii", $statusId, $userId);
            $stmt->execute();
            $orderId = $stmt->insert_id;

            $stmt = $mysqli->prepare("INSERT INTO order_items (id_order, id_item, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt->bind_param("iiid", $orderId, $item['id_item'], $item['amount'], $item['price']);
                $stmt->execute();
            }

            if ($userId) {
                $this->clearCart($userId);
            } else {
                $this->clearSessionCart();
            }

            $mysqli->commit();

            return $orderId;

        } catch (Exception $e) {
            $mysqli->rollback();
            throw $e;
        }
    }

    public function clearCart($userId)
    {
        $cartId = $this->getShoppingCartId($userId);
        $sql = "DELETE FROM shopping_cart_item WHERE id_shopping_cart = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
    }

    public function clearSessionCart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['shopping_cart']);
    }

    public function getOrCreateUserIdByEmail(
        $email,
        $firstName = null,
        $lastName = null,
        $address = null,
        $city = null,
        $houseNumber = null,
        $zipCode = null,
        $country = null
    ) {
        $stmt = $this->db->prepare('SELECT id_user FROM user WHERE email_address = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            return $user['id_user'];
        }

        $firstName = $firstName ?? 'Guest';
        $lastName = $lastName ?? 'User';
        $address = $address ?? 'N/A';
        $city = $city ?? 'N/A';
        $houseNumber = is_numeric($houseNumber) ? (int)$houseNumber : 0;
        $zipCode = $zipCode ?? 'N/A';
        $country = $country ?? 'N/A';

        $randomPassword = bin2hex(random_bytes(4));
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'INSERT INTO user (first_name, last_name, email_address, password, address, city, house_number, zip_code, country, is_admin) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $isAdmin = 0;
        $stmt->bind_param(
            'sssssssisi',
            $firstName,
            $lastName,
            $email,
            $hashedPassword,
            $address,
            $city,
            $houseNumber,
            $zipCode,
            $country,
            $isAdmin
        );
        $stmt->execute();

        return $stmt->insert_id;
    }
}
