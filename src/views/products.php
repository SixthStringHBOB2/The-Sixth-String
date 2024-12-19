<?php
$mysqli = getDbConnection();

$items_per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$sql = "SELECT item.id_item, item.name, item.price, item.description, 
        COALESCE(AVG(review.rating), 0) AS avg_rating 
        FROM item 
        LEFT JOIN review ON item.id_item = review.id_item 
        GROUP BY item.id_item 
        LIMIT $items_per_page OFFSET $offset";
$result = $mysqli->query($sql);

$total_result = $mysqli->query("SELECT COUNT(*) AS total FROM item");
$total_products = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $items_per_page);

function getShoppingCartItems($userId = null)
{
    if ($userId) {
        $db = getDbConnection();
        $sql = "
        SELECT shopping_cart_item.id_item AS id_item, 
               shopping_cart_item.amount AS amount
        FROM shopping_cart_item
        INNER JOIN shopping_cart ON shopping_cart_item.id_shopping_cart = shopping_cart.id_shopping_cart
        WHERE shopping_cart.id_user = $userId
    ";

        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return $_SESSION['shoppingCart'] ?? [];
    }
}

$isLoggedIn = isset($auth) && $auth->isLoggedIn();
$sessionCart = [];
if ($isLoggedIn) {
    $userData = $auth->getLoggedInUserData();
    $userId = $userData['id_user'];
    $sessionCart = getShoppingCartItems($userId);
} else {
    $sessionCart = getShoppingCartItems();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-direction: row;
        }

        .filter-bar {
            width: 20%;
            padding: 10px;
            border-right: 1px solid #ccc;
        }

        .products {
            width: 80%;
            padding: 10px;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .product {
            flex: 1 1 calc(25% - 10px);
            box-sizing: border-box;
            padding: 10px;
            border: 3px solid #444C50;
            border-radius: 25px;
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .product h4 {
            font-size: 25px;
            font-weight: 700;
            margin: 10px 0 5px;
            text-align: left;
        }

        .product p {
            margin: 5px 0;
        }

        .product .price {
            font-size: 18px;
            width: max-content;
            font-weight: bold;
            text-align: left;
        }

        .product .shopping-basket {
            background: #30B6FA;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-self: center;
            cursor: pointer;
            justify-content: center;
            position: relative;
        }

        .product .shopping-basket img {
            width: 24px;
            height: 24px;
            align-self: center;
        }

        .product .item-count {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #FF5733;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .stars {
            position: relative;
            display: inline-block;
            font-size: 1.5em;
            line-height: 1;
        }

        .stars::before {
            content: "★★★★★";
            letter-spacing: 3px;
            background: linear-gradient(90deg, #f39c12 calc(var(--rating, 0) / 5 * 100%), #ccc calc(var(--rating, 0) / 5 * 100%));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 1200px) {
            .product {
                flex: 1 1 calc(25% - 10px);
            }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .filter-bar {
                width: 100%;
                order: -1;
                border-right: none;
                border-bottom: 1px solid #ccc;
            }

            .products {
                width: 100%;
            }

            .product {
                flex: 1 1 calc(33.33% - 10px);
            }
        }

        @media (max-width: 480px) {
            .product {
                flex: 1 1 calc(50% - 10px);
            }
        }
    </style>
</head>
<body>


<div class="container">
    <div class="filter-bar">
        <h3>Filters</h3>
        <p>Category</p>
        <p>Brand</p>
        <p>Price Range</p>
    </div>

    <div class="products">
        <div class="product-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product">
                    <a href="/products/<?= $product['id_item'] ?>">
                        <img src="/assets/images/product.png" alt="Default Product Image">
                    </a>
                    <div class="container" style="justify-content: space-between">
                        <div style="flex-direction: column">
                            <h4>
                                <a href="/products/<?= $product['id_item'] ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h4>
                            <p class="price">€<?= number_format($product['price'], 2) ?></p>
                        </div>

                        <?php
                        // Find item count in session or from the database if logged in
                        $item_count = 0;
                        foreach ($sessionCart as $cartItem) {
                            if ($cartItem['id_item'] == $product['id_item']) {
                                $item_count = $cartItem['amount'];
                                break;
                            }
                        }
                        ?>

                        <form method="POST" action="/shoppingcart" style="display:inline;">
                            <input type="text" name="id_item" value="<?= $product['id_item'] ?>" hidden>
                            <input type="number" name="amount" value="1" style="width: 40px;" hidden>
                            <input type="hidden" name="price" value="<?= $product['price'] ?>">
                            <button type="submit" name="add_to_cart" class="shopping-basket">
                                <img src="/assets/images/shoppingbasket.png" alt="Add to Basket">
                                <?php if ($item_count > 0): ?>
                                    <div class="item-count"><?= $item_count ?></div>
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                    <p>
                        <span class="stars" style="--rating: <?= number_format($product['avg_rating'], 1) ?>;"></span>
                        (<?= number_format($product['avg_rating'], 1) ?>)
                    </p>
                    <p>
                        <a href="/products/<?= $product['id_item'] ?>#reviews">Bekijk</a>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $page ? 'style="font-weight: bold;"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>

</body>
</html>
