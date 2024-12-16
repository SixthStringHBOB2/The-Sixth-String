<?php
include '../database/db.php';

function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

// URL Params
$selectedBrands = isset($_GET['brands']) ? explode(',', $_GET['brands']) : [];
$selectedBrands = array_map(function ($brands) {
    return (int)ltrim($brands, 'b');
}, $selectedBrands);

$selectedCategories = isset($_GET['categories']) ? explode(',', $_GET['categories']) : [];
$selectedCategories = array_map(function ($category) {
    return (int)ltrim($category, 'c');
}, $selectedCategories);

$selectedStates = isset($_GET['states']) ? explode(',', $_GET['states']) : [];
$selectedStates = array_map(function ($states) {
    return (int)ltrim($states, 's');
}, $selectedStates);
// If there are multiple states, set $state to null, otherwise create a list of states
if (count($selectedStates) > 1) {
    $state = null;
} else {
    $state = implode(',', array_map('intval', $selectedStates));
}

$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 10000;
// TODO: reviews

// Pagination
$items_per_page = 25;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

try {
    // Establish connection
    $mysqli = getDbConnection();

// Get products
    $productsSQL = "SELECT item.id_item, item.name, item.price, item.description, 
                COALESCE(AVG(review.rating), 0) AS avg_rating 
                FROM item 
                LEFT JOIN review ON item.id_item = review.id_item
                WHERE 1=1";

    if (!empty($selectedCategories)) {
        $categoriesList = implode(',', array_map('intval', $selectedCategories));
        $productsSQL .= " AND item.id_category IN ($categoriesList)";
    }

    if (!empty($selectedBrands)) {
        $brandsList = implode(',', array_map('intval', $selectedBrands));
        $productsSQL .= " AND item.id_brand IN ($brandsList)";
    }

    if (!empty($state)) {
        $productsSQL .= " AND item.is_used = $state";
    }

    if (!empty($minPrice)) {
        $productsSQL .= " AND item.price > ($minPrice)";
    }

    if (!empty($maxPrice)) {
        $productsSQL .= " AND item.price < ($maxPrice)";
    }

    $productsSQL .= " GROUP BY item.id_item 
                  LIMIT $items_per_page OFFSET $offset";


    $products = $mysqli->query($productsSQL);

    $total_result = $mysqli->query("SELECT COUNT(*) AS total FROM item");
    $total_products = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_products / $items_per_page);

    // Get filter options
    $brandsSQL = "SELECT id_brand, name FROM brand ORDER BY brand.name";
    $brands = $mysqli->query($brandsSQL);

    $categoriesSQL = "SELECT id_category, name FROM category ORDER BY category.name";
    $categories = $mysqli->query($categoriesSQL);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Filter selection -->
    <div class="bg-white p-6 rounded-lg shadow-md w-64" id="filter-bar">
        <h2 class="text-xl font-semibold mb-4 text-[#546E7A]">Welke kies jij?</h2>

        <!-- Brand Filter -->
        <div class="mb-4" id="brandFilter">
            <label class="block text-sm font-medium text-[#546E7A]">Merk</label>
            <div class="space-y-2 mt-2" id="brands">
                <!-- Brands are added here -->
            </div>
        </div>

        <!-- Category Filter -->
        <div class="mb-4" id="categoryFilter">
            <label class="block text-sm font-medium text-[#546E7A]">Categorie</label>
            <div class="space-y-2 mt-2" id="categories">
                <!-- Categories are added here -->
            </div>
        </div>

        <!-- Price Filter -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#546E7A]">Prijs</label>
            <div class="flex space-x-4 mt-2">
                <div class="w-1/2">
                    <label for="minPrice" class="block text-xs text-gray-700">Minimale prijs</label>
                    <input type="number" id="minPrice"
                           class="w-full mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#546E7A]"
                           placeholder="€0" min="0" value="0">
                </div>
                <div class="w-1/2">
                    <label for="maxPrice" class="block text-xs text-gray-700">Maximale prijs</label>
                    <input type="number" id="maxPrice"
                           class="w-full mt-1 p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#546E7A]"
                           placeholder="€10.000" max="10000" value="10000">
                </div>
            </div>
        </div>

        <!-- Egyptische oudheid filter -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#546E7A]">Staat</label>
            <div class="space-y-2 mt-2">
                <div class="flex items-center">
                    <input type="checkbox" id="new"
                           class="h-4 w-4 text-[#546E7A] border-gray-300 rounded focus:ring-[#546E7A]"
                           data-filter="states" data-id="s0">
                    <label for="new" class="ml-2 text-sm text-gray-700">Nieuw</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="secondhand"
                           class="h-4 w-4 text-[#546E7A] border-gray-300 rounded focus:ring-[#546E7A]"
                           data-filter="states" data-id="s1">
                    <label for="secondhand" class="ml-2 text-sm text-gray-700">Tweedehands</label>
                </div>
            </div>
        </div>

        <!-- Review Filter -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#546E7A]">Beoordeling</label>
            <div class="flex items-center">
                <input type="radio" id="review5" name="review"
                       class="h-4 w-4 text-[#546E7A] border-gray-300 focus:ring-[#546E7A]" data-filter="review"
                       data-id="5">
                <label for="review5" class="ml-2 text-sm text-gray-700">5 sterren</label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="review4" name="review"
                       class="h-4 w-4 text-[#546E7A] border-gray-300 focus:ring-[#546E7A]" data-filter="review"
                       data-id="4">
                <label for="review4" class="ml-2 text-sm text-gray-700">4+ sterren</label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="review3" name="review"
                       class="h-4 w-4 text-[#546E7A] border-gray-300 focus:ring-[#546E7A]" data-filter="review"
                       data-id="3">
                <label for="review3" class="ml-2 text-sm text-gray-700">3+ sterren</label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="review2" name="review"
                       class="h-4 w-4 text-[#546E7A] border-gray-300 focus:ring-[#546E7A]" data-filter="review"
                       data-id="2">
                <label for="review2" class="ml-2 text-sm text-gray-700">2+ sterren</label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="review1" name="review"
                       class="h-4 w-4 text-[#546E7A] border-gray-300 focus:ring-[#546E7A]" data-filter="review"
                       data-id="1">
                <label for="review1" class="ml-2 text-sm text-gray-700">1+ sterren</label>
            </div>
        </div>
        <button id="clearFilters">Clear Filters</button>

        <button class="mt-4 w-full py-2 px-4 bg-[#546E7A] text-white rounded-lg hover:bg-[#4A5E66] focus:outline-none focus:ring-2 focus:ring-[#546E7A] focus:ring-opacity-50"
                id="applyFilters">
            Filters toepassen
        </button>
    </div>

    <script>
        const brands = <?= json_encode($brands->fetch_all(MYSQLI_ASSOC)); ?>;
        const categories = <?= json_encode($categories->fetch_all(MYSQLI_ASSOC)); ?>;

        // Load brand filters
        function loadBrandFilters() {
            const brandContainer = document.getElementById('brands');
            brands.forEach(brand => {
                const brandDiv = document.createElement('div');
                brandDiv.classList.add('flex', 'items-center');
                brandDiv.innerHTML = `
                    <input type="checkbox" id="brand${brand.id_brand}" class="h-4 w-4 text-[#546E7A] border-gray-300 rounded focus:ring-[#546E7A]" data-filter="brand" data-id="b${brand.id_brand}">
                    <label for="brand${brand.id_brand}" class="ml-2 text-sm text-gray-700">${brand.name}</label>
                `;
                brandContainer.appendChild(brandDiv);
            });
        }

        // Load category filters
        function loadCategoryFilters() {
            const categoryContainer = document.getElementById('categories');
            categories.forEach(category => {
                const categoryDiv = document.createElement('div');
                categoryDiv.classList.add('flex', 'items-center');
                categoryDiv.innerHTML = `
                    <input type="checkbox" id="category${category.id_category}" class="h-4 w-4 text-[#546E7A] border-gray-300 rounded focus:ring-[#546E7A]" data-filter="category" data-id="c${category.id_category}">
                    <label for="category${category.id_category}" class="ml-2 text-sm text-gray-700">${category.name}</label>
                `;
                categoryContainer.appendChild(categoryDiv);
            });
        }

        // Load filter options on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadBrandFilters();
            loadCategoryFilters();
        });

        function getSelectedFilters() {
            const selectedFilters = {
                brands: [],
                categories: [],
                states: [],
                review: null,
                minPrice: document.getElementById('minPrice').value,
                maxPrice: document.getElementById('maxPrice').value
            };

            // Get selected brands
            const brandCheckboxes = document.querySelectorAll('[data-filter="brand"]:checked');
            brandCheckboxes.forEach(checkbox => {
                selectedFilters.brands.push(checkbox.getAttribute('data-id'));
            });

            // Get selected categories
            const categoryCheckboxes = document.querySelectorAll('[data-filter="category"]:checked');
            categoryCheckboxes.forEach(checkbox => {
                selectedFilters.categories.push(checkbox.getAttribute('data-id'));
            });

            // Get selected states
            const statesCheckboxes = document.querySelectorAll('[data-filter="states"]:checked');
            statesCheckboxes.forEach(checkbox => {
                selectedFilters.states.push(checkbox.getAttribute('data-id'));
            });

            // // Get selected reviews
            // const reviewRadios = document.querySelectorAll('[data-filter="review"]:checked');
            // reviewRadios.forEach(radio => {
            //     selectedFilters.review.push(radio.getAttribute('data-id'));
            // });

            return selectedFilters;
        }

        function updateURLWithFilters() {
            const selectedFilters = getSelectedFilters();

            const params = new URLSearchParams();

            if (selectedFilters.brands.length > 0) {
                params.set('brands', selectedFilters.brands.join(','));
            }

            if (selectedFilters.categories.length > 0) {
                params.set('categories', selectedFilters.categories.join(','));
            }

            if (selectedFilters.states.length > 0) {
                params.set('states', selectedFilters.states.join(','));
            }

            params.set('minPrice', selectedFilters.minPrice);
            params.set('maxPrice', selectedFilters.maxPrice);

            // if (selectedFilters.review.length > 0) {
            //     params.set('reviews', selectedFilters.review.join(','));
            // }

            window.history.pushState({}, '', '?' + params.toString());
        }

        document.getElementById('applyFilters').addEventListener('click', function () {
            updateURLWithFilters();
            location.reload();
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Get the query parameters
            const urlParams = new URLSearchParams(window.location.search);

            // Apply brand filters
            const brands = urlParams.get('brands');
            if (brands) {
                brands.split(',').forEach(brandId => {
                    const brandCheckbox = document.querySelector(`[data-id="${brandId}"]`);
                    brandCheckbox.checked = true;
                });
            }

            // Apply category filters
            const categories = urlParams.get('categories');
            if (categories) {
                categories.split(',').forEach(categoryId => {
                    const categoriesCheckbox = document.querySelector(`[data-id="${categoryId}"]`);
                    categoriesCheckbox.checked = true;
                });
            }

            // Apply price filters
            const minPrice = urlParams.get('minPrice');
            const maxPrice = urlParams.get('maxPrice');
            if (minPrice) document.getElementById('minPrice').value = minPrice;
            if (maxPrice) document.getElementById('maxPrice').value = maxPrice;

            // Apply oudheid filters
            const states = urlParams.get('states');
            if (states) {
                states.split(',').forEach(stateId => {
                    const stateCheckbox = document.querySelector(`[data-id="${stateId}"]`);
                    stateCheckbox.checked = true;
                });
            }

            // // Apply review filters
            // const reviews = urlParams.get('reviews');
            // if (reviews) {
            //     reviews.split(',').forEach(rating => {
            //         const radio = document.querySelector(`[data-value="${rating}"]`);
            //         if (radio) radio.checked = true;
            //     });
            // }

            // Clear filters
            function clearFilters() {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.delete('brands');
                urlParams.delete('categories');
                urlParams.delete('minPrice');
                urlParams.delete('maxPrice');
                urlParams.delete('states');
                urlParams.delete('reviews');

                window.history.pushState({}, '', '?' + urlParams.toString());

                // Reset the filter UI
                document.querySelectorAll('[data-filter]').forEach(element => {
                    element.checked = false;
                });
                document.getElementById('minPrice').value = '';
                document.getElementById('maxPrice').value = '';
                location.reload();
            }

            document.getElementById('clearFilters').addEventListener('click', clearFilters);

        });

    </script>

    <div class="products">
        <div class="product-grid">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product">
                    <a href="/products/<?= $product['id_item'] ?>">
                        <img src="../public/images/product.png" alt="Default Product Image">
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
                            <img src="../public/images/shoppingbasket.png" alt="Add to Basket">
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
