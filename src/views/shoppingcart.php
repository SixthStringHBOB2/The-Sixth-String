<HTML>

<?php
//session_start();
include  'database/db.php';
$userLoggedIn = 1;
$_SESSION['LoggedInUser'] = 393;
$current_datetime = date('Y-m-d H:i:s');
$shoppingCartId = ""; // is set later in the first function, might seem abit weird but idk it works.

// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
// this is only there so people have an example of how we expect it to be stored, if you uncomment below it makes the site buggy af lol
$_SESSION['shoppingCart'] = [];

$_SESSION['shoppingCart'] = [
    2 => [2, "product 1", "15,50", 1],
    3 => [3, "product 2", "15,50", 1],
    4 => [4, "product 3", "15,50", 1],
];

if(isset($_SESSION['LoggedInUser'])){
    $dbConnection = getDbConnection();
    $userId = $_SESSION['LoggedInUser'];
    $query = "SELECT sci.amount, sci.id_item, i.`name`, i.price, sci.id_shopping_cart
                FROM shopping_cart sc
                LEFT JOIN shopping_cart_item sci ON sc.id_shopping_cart = sci.id_shopping_cart
                LEFT JOIN item i ON sci.id_item = i.id_item
                WHERE sc.id_user = $userId";

    $queryResult = mysqli_query($dbConnection, $query);


    if (!$queryResult) {
        die("Query failed: " . mysqli_error($dbConnection));
    }
    if($queryResult->num_rows > 1){
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $itemId = $row['id_item'];
            $itemName = $row['name'];
            $itemPrice = number_format($row['price']);
            $itemAmount = $row['amount'];

            $shoppingCartId = $row['id_shopping_cart'];

            // Add the item to the shopping cart session
            if(!isset($_SESSION['shoppingCart'][$itemId])){
                $_SESSION['shoppingCart'][$itemId] = [
                    $itemId,
                    $itemName,
                    $itemPrice,
                    $itemAmount
                ];

            }else{
                $_SESSION['shoppingCart'][$itemId][3] + $itemAmount;
            }
        }
    } else{
        $sql = "SELECT id_shopping_cart FROM shopping_cart WHERE id_user = $userId";
        $result = mysqli_query($dbConnection, $sql);
        $shoppingCartId = ($row = mysqli_fetch_assoc($result)) ? $row['id_shopping_cart'] : null;
        mysqli_close($dbConnection);

    }
}

//Code below updates the amount the customer wants to buy to the php session variable
//it gets the value from the hidden input field which is being updated with js
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formType'])) {
        if ($_POST['formType'] === 'purchaseCart' && isset($_POST['amounts'])) {
            $dbConnection = getDbConnection();
            foreach ($_POST['amounts'] as $productId => $amount) {
                // Update the shopping cart amount and check if db also need to be updated, if so then do so
                if (isset($_SESSION['shoppingCart'][$productId])) {
                    if ($amount > 0) {
                        $_SESSION['shoppingCart'][$productId][3] = $amount;
                        if (isset($_SESSION['LoggedInUser'])) {
                            $sqlcheckIfItemInDbShoppingCartExsist = "SELECT sci.id_item
                                                        FROM shopping_cart_item sci
                                                        LEFT JOIN shopping_cart sc ON sci.id_shopping_cart = sc.id_shopping_cart
                                                        WHERE sc.id_user = $userId";

                            $checkIfItemInDbExsist = mysqli_query($dbConnection, $sqlcheckIfItemInDbShoppingCartExsist);

                            if ($checkIfItemInDbExsist->num_rows <= 1) {
                                while ($product = current($_SESSION['shoppingCart'])) {
                                    $productId = $product[0];
                                    $amount = $product[3];
                                    echo $amount;
                                    echo "<br>";

                                    // Insert into shopping_cart_item
                                    $sqlInsert = "INSERT INTO shopping_cart_item (id_shopping_cart, id_item, amount)
                                    VALUES ($shoppingCartId, $productId, $amount)";
                                    echo $shoppingCartId;
                                    mysqli_query($dbConnection, $sqlInsert);

                                    // Move to the next item in the array
                                    next($_SESSION['shoppingCart']);
                                }
                            }
                            $sqlUpdateShoppingCartItemAmount = "UPDATE shopping_cart_item
                                                                SET amount = $amount
                                                                WHERE id_shopping_cart = $shoppingCartId 
                                                                AND id_item = $productId";

                            mysqli_query($dbConnection, $sqlUpdateShoppingCartItemAmount);
                        }
                        // Update the amount in session
                    } else {
                        if (isset($_SESSION['LoggedInUser'])) {
                            $productId = $_SESSION['shoppingCart'][$productId][0];
                            $userId = $_SESSION['LoggedInUser'];
                            $sqlQueryToDeleteFromShoppingCart = "
                                DELETE sci
                                FROM shopping_cart_item sci
                                JOIN shopping_cart sc ON sc.id_shopping_cart = sci.id_shopping_cart
                                WHERE sci.id_item = $productId AND sc.id_user = $userId";

                            mysqli_query($dbConnection, $sqlQueryToDeleteFromShoppingCart);
                        }
                        unset($_SESSION['shoppingCart'][$productId]);  // Remove product from session if amount is 0
                    }
                }
            }
        }
    }
}
// update the database
if (isset($_POST['PurchaseButton2'])) {
    $dbConnection = getDbConnection();
    if (isset($_SESSION['LoggedInUser'])) {
        //create order
        $sqlCreateOrder = "INSERT INTO `order` (order_date, id_status, id_user) VALUES ('$current_datetime', 1, $userId)"; // the status is set to 1 for now, this is the happy flow
        mysqli_query($dbConnection, $sqlCreateOrder);

        //get created order id
        $lastInsertedId = mysqli_insert_id($dbConnection);
        createOrderDetail($lastInsertedId);
    }else{
        $sqlCreateOrder = "INSERT INTO `order` (order_date, id_status, id_user) VALUES ('$current_datetime', 1, 1)"; // the status is set to 1 for now, this is the happy flow
        // the id_user is set to 1, de database always expect a user so id_user is now a geust user for people without an account
        mysqli_query($dbConnection, $sqlCreateOrder);

        //get created order id
        $lastInsertedId = mysqli_insert_id($dbConnection);
        createOrderDetail($lastInsertedId);
        // clear the shopping_cart_item table after purchase is done
    }
    clearShoppingCart();
    mysqli_close($dbConnection);
}


function clearShoppingCart(){
    if($_SESSION['LoggedInUser']){
        $userId = $_SESSION['LoggedInUser'];
        $dbconnection = getDbConnection();

        $sqlDeleteItems = "DELETE sci
                       FROM shopping_cart_item sci
                       LEFT JOIN shopping_cart sc ON sc.id_shopping_cart = sci.id_shopping_cart
                       WHERE sc.id_user = $userId";


        mysqli_query($dbconnection, $sqlDeleteItems);
        mysqli_close($dbconnection);
    } else{
        unset($_SESSION['shoppingCart']);
        echo "done";
    }
}

function createOrderDetail($lastInsertedId){
    $dbConnection = getDbConnection();
    foreach ($_SESSION['shoppingCart'] as $product) {
        $productId = $product[0];
        $amount = $product[3];

        $sqlCreateOrderDetail = "INSERT INTO order_detail (amount, id_item, id_order) VALUES ($amount, $productId, $lastInsertedId)";

        mysqli_query($dbConnection, $sqlCreateOrderDetail);
    }
    mysqli_close($dbConnection);
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
    </form>
    <form method="POST" action="/shoppingcart">
        <input type="submit" name="PurchaseButton2" value="Koop winkelwagen">
    </form>
</body>
<footer>

</footer>
</html>