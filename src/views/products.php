<?php
include 'database/db.php';

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
        }

        .product .shopping-basket img {
            width: 24px;
            height: 24px;
            align-self: center;
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
<!-- TODO: add header -->

<div class="container">
    <div class="filter-bar">
        <h3>Filters</h3>
        <p>Category</p>
        <p>Brand</p>
        <p>Price Range</p>
        <!-- TODO: Add filtering options here -->
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
                                <a style="width: max-content" href="/products/<?= $product['id_item'] ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h4>
                            <p class="price">€<?= number_format($product['price'], 2) ?></p>
                        </div>
                        <button class="shopping-basket">
                            <img src="/assets/images/shoppingbasket.png" alt="Add to Basket">
                        </button>
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

<!-- TODO: add footer -->
</body>
</html>
