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
        }
        .product img {
            max-width: 100%;
            height: auto;
        }
        .stars {
            color: #f39c12;
        }
        @media (max-width: 1200px) {
            .product {
                flex: 1 1 calc(25% - 10px);
            }
        }
        @media (max-width: 768px) {
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
        <!-- Add more filter options -->
    </div>
    <div class="products">
        <div class="product-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product">
                    <a href="/products/<?= $product['id_item'] ?>">
                        <img src="/assets/images/product.png" alt="Default Product Image">
                    </a>
                    <h4>
                        <a href="/products/<?= $product['id_item'] ?>">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h4>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p style="font-weight: bolder">€<?= number_format($product['price'], 2) ?></p>
                    <p>
                        <span class="stars">
                            <?= str_repeat('★', (int)$product['avg_rating']) ?>
                            <?= str_repeat('☆', 5 - (int)$product['avg_rating']) ?>
                        </span>
                        (<?= number_format($product['avg_rating'], 1) ?>)
                    </p>
                    <p>
                        <a href="/products/<?= $product['id_item'] ?>#reviews">View Reviews</a>
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

