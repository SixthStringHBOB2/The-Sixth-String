<HTML>

<?php
session_start();
include  'database/db.php'; //TODO fix this
$userLoggedIn = true;
$_SESSION['LoggedInUser'] = 0;
// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
// this is only there so people have an example of how we expect it to be stored, if you uncomment below it makes the site buggy af lol
$_SESSION['shoppingCart'] = [
    1 => [1, "product 1", "15,50", 1],
    2 => [2, "product 2", "15,50", 1],
    3 => [3, "product 3", "15,50", 1],
];

if(isset($_SESSION['LoggedInUser'])){
    $dbConnection = getDbConnection();
    $userId = $_SESSION['LoggedInUser'];
    $query = "SELECT sci.amount, sci.id_item, i.`name`, i.`price`
                FROM shopping_cart sc
                LEFT JOIN shopping_cart_item sci ON sc.id_shopping_cart = sci.id_shopping_cart
                LEFT JOIN item i ON sci.id_item = i.id_item
                WHERE sc.id_user = $userId"; //TODO make the userId variable

    $queryResult = mysqli_query($dbConnection, $query);

    if (!$queryResult) {
        die("Query failed: " . mysqli_error($dbConnection));
    }
    if($queryResult->num_rows >= 1){
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $itemId = $row['id_item'];
            $itemName = $row['name'];
            $itemPrice = number_format($row['price']);
            $itemAmount = $row['amount'];

            // Add the item to the shopping cart session
            $_SESSION['shoppingCart'][$itemId] = [
                $itemId,
                $itemName,
                $itemPrice,
                $itemAmount
            ];
        }
    }
}

//Code below updates the amount the customer wants to buy to the php session variable
//it gets the value from the hidden input field which is being updated with js
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formType'])) {
        // Update the shopping cart amount
        if ($_POST['formType'] === 'purchaseCart' && isset($_POST['amounts'])) {
            $dbConnection = getDbConnection();
            foreach ($_POST['amounts'] as $productId => $amount) {
                if (isset($_SESSION['shoppingCart'][$productId])) {
                    if ($amount > 0) {
                        $_SESSION['shoppingCart'][$productId][3] = $amount;  // Update the amount in session
                    } else {
                        if(isset($_SESSION['LoggedInUser'])){
                            $productId = $_SESSION['shoppingCart'][$productId][0];
                            $userId = $_SESSION['LoggedInUser'];
                            $sqlQueryToDeleteFromShoppingCart = "DELETE sci
                                    FROM shopping_cart_item sci
                                    LEFT JOIN shopping_cart sc ON sci.id_shopping_cart = sc.id_shopping_cart
                                    WHERE sci.id_item = $productId  AND sc.id_user = $userId;
                                    ";
                            mysqli_query($dbConnection, $sqlQueryToDeleteFromShoppingCart);
                        }
                        unset($_SESSION['shoppingCart'][$productId]);  // Remove product from session if amount is 0
                    }
                }
            }
            // update the database
            if (isset($_POST['PurchaseButton2'])) {
                if (isset($_SESSION['LoggedInUser'])) {
                    $userId = $_SESSION['LoggedInUser'];
                    // check if there is a shopping_cart_item for the user and get the ids
                    $sqlCheckIfThereIsShopping_cart_item = "
                        SELECT sci.id_shopping_cart, sci.id_item
                        FROM shopping_cart_item sci
                        LEFT JOIN shopping_cart sc ON sci.id_shopping_cart = sc.id_shopping_cart
                        WHERE sc.id_user = $userId"; // Use $userId dynamically

                    $queryResult = mysqli_query($dbConnection, $sqlCheckIfThereIsShopping_cart_item);

                    if ($queryResult) {
                        $columns = mysqli_fetch_all($queryResult, MYSQLI_ASSOC);
                        // loop through each shopping cart item for the user
                        foreach ($columns as $column) {
                            $shoppingCartId = $column['id_shopping_cart'];
                            $itemIdFromShoppingCart = $column['id_item'];

                            // Loop through each product in the shopping cart
                            foreach ($_SESSION['shoppingCart'] as $product) {
                                $productId = $product[0];
                                $amount = $product[3];

                                // Check if the item already exists in the users cart
                                $sqlCheckItemExistenceInDatabase = "
                                    SELECT id_shopping_cart
                                    FROM shopping_cart_item
                                    WHERE id_shopping_cart = $shoppingCartId AND id_item = $productId";

                                $itemExistResult = mysqli_query($dbConnection, $sqlCheckItemExistenceInDatabase);

                                if (mysqli_num_rows($itemExistResult) > 0) {
                                    // update the amount
                                    $sqlUpdate = "
                                        UPDATE shopping_cart_item
                                        SET amount = $amount
                                        WHERE id_shopping_cart = $shoppingCartId AND id_item = $productId";

                                    $updateResult = mysqli_query($dbConnection, $sqlUpdate);
                                } else {
                                    // insert a new record
                                    $sqlInsert = "
                                        INSERT INTO shopping_cart_item (id_shopping_cart, id_item, amount)
                                        VALUES ($shoppingCartId, $productId, $amount)";

                                    mysqli_query($dbConnection, $sqlInsert);
                                }
                            }
                        }
                    }
                    //todo make the order table for when the user is logged in
                    mysqli_close($dbConnection);
                }
                //TODO make the order table for when the user is not logged in
            }
        }
    }
}

?>
<script>
    //TODO make the two fucntions below one
    //code below updates the amount the customer wants to buy on the client side and the hidden input field so php can get the variable from there
    //could also write a mapping but that is too much work... :)
    function incrementAmount(productId) {
        const element = document.getElementById('incrementText_' + productId);
        const hiddenInput = document.getElementById('hiddenInput_' + productId);

        if (element && hiddenInput) {
            let value = parseInt(element.innerHTML);
            value++;
            element.innerHTML = value;
            hiddenInput.value = value;
        }
    }
    //code below updates the amount the customer wants to buy on the client side and the hidden input field so php can get the variable from there
    //could also write a mapping but that is too much work... :)
    function decrementAmount(productId) {
        const hiddenInput = document.getElementById('hiddenInput_' + productId);
        const element = document.getElementById('incrementText_' + productId);

        if (element && hiddenInput) {
            let value = parseInt(element.innerHTML);
            if (value > 0) {
                value--;
            }
            element.innerHTML = value;
            hiddenInput.value = value;
        }
    }
</script>

<head>
    <title>Shopping Cart</title>
</head>

<body>
    <h1>Shopping Cart</h1>
    <form method="POST" action="/shoppingcart">
        <input type="hidden" name="formType" value="updateCart">
        <table>
            <tr>
                <th>Plaatje</th>
                <th>Product ID</th>
                <th>Prijs</th>
                <th>Naam</th>
                <th>Aantal</th>
            </tr>
            <?php
            foreach ($_SESSION['shoppingCart'] as $product) {
                $productId = $product[0];  // The product ID
                $productName = $product[1];  // The product name
                $productPrice = $product[2]; // The product price
                $amount = $product[3];  // The amount

                echo "
                <tr>
                    <td>plaatje</td>
                    <td>$productId</td> 
                    <td>$productPrice</td> 
                    <td>$productName</td> 
                    <td>
                        <button type='submit' onclick='incrementAmount(\"$productId\")'>+</button>
                    </td>
                    <td>
                        <label id='incrementText_$productId'>$amount</label>
                        <input type='hidden' name='amounts[$productId]' id='hiddenInput_$productId' value='$amount'>
                    </td> 
                    <td>
                        <button type='submit' onclick='decrementAmount(\"$productId\")'>-</button>
                    </td>
                </tr>
            ";
            }
            ?>
        </table>
        <br>
        <input type="hidden" name="formType" value="purchaseCart">
        <input type="submit" name="PurchaseButton2" value="Koop winkelwagen">
    </form>
</body>
<footer>

</footer>
</html>