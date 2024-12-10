<HTML>

<?php
session_start();
//require ('database/db.php');
$amount = 500;
$userLoggedIn = true;

// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
$_SESSION['shoppingCart'] = [
    "id1" => ["1", "product 1", $amount],
    "id2" => ["2", "product 2", $amount],
    "id3" => ["3", "product 3", $amount],
    "id4" => ["4", "product 4", $amount],
    "id5" => ["5", "product 5", $amount],
    "id6" => ["6", "product 6", $amount],
    "id7" => ["7", "product 7", $amount]
];

function checkIfShoppingAlreadyExist($userLoggedIn){
    //check if user is logged in, if user is NOT logged in skip this function
    //the variable i am using to check if user is logged in is placeholder for the real one

//    if($userLoggedIn){
//        $dbConnection = getDbConnection();
//        $query = "SELECT count(*) FROM shoppingcart";
//        $queryResult = mysqli_fetch_all(mysqli_query($dbConnection, $query));
//
//        //TODO map $queryResult to $_SESSION['productId'] and fill it or update it? idk need to check it
//    }
}


//Code below updates the amount the customer wants to buy to the php session variable
//it gets the value from the hidden input field which is being updated with js
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['formType'])) {
        // Determine which form was submitted
        if ($_POST['formType'] === 'updateCart' && isset($_POST['amounts'])) {
            // Handle cart updates
            foreach ($_POST['amounts'] as $productId => $amount) {
                if (isset($_SESSION['shoppingCart'][$productId])) {
                    // Update the amount or remove the item if amount is less than 1
                    if ($amount > 0) {
                        $_SESSION['shoppingCart'][$productId][2] = $amount;
                    } else {
                        unset($_SESSION['shoppingCart'][$productId]);
                    }
                }
            }
        }
        if ($_POST['formType'] === 'purchaseCart' && isset($_POST['PurchaseButton'])) {
            updateToDatabase();
        }
    }
}
function updateToDatabase(){
    foreach ($_SESSION['shoppingCart'] as $product) {
        $productId = $product[0];
        $amount = $product[2];

        $dbConnection = getDBConnection();
        $sql = "INSERT INTO shopping_cart_item (id_item, amount) VALUES ($productId, $amount)";
        echo"godverdomme2";

        mysqli_query($dbConnection, $sql);
        echo"dadada";
        mysqli_close($dbConnection);
    }
}



function getDbConnection() {
    $host = "192.168.1.11";
    $dbname = "thesixthstring";
    $username = "default";
    $password = "rEN28Sd8?W|L6FquVky>";

    // Check for missing environment variables
    if (!$host || !$dbname || !$username || !$password) {
        die('Missing environment variables for database connection');
    }

    // Create and return the MySQLi connection
    $mysqli = new mysqli($host, $username, $password, $dbname);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}



$dbConnection = getDbConnection();
$query = "SELECT * FROM user";
$queryResult = mysqli_fetch_all(mysqli_query($dbConnection, $query));

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
                <th>Product naam</th>
                <th>aantal</th>
            </tr>
            <?php
            foreach ($_SESSION['shoppingCart'] as $product) {
                $productId = $product[0];  // The product ID
                $productName = $product[1];  // The product name
                $amount = $product[2];  // The amount

                echo "
                <tr>
                    <td>plaatje</td>
                    <td>$productId</td> 
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
<!--        <input type="submit" name="PurchaseButton" value="Koop winkelwagen">-->
    </form>
    <form method="POST" action="/shoppingcart">
        <input type="hidden" name="formType" value="purchaseCart"> <!-- Identify this as the purchase form -->
        <input type="submit" name="PurchaseButton" value="Koop winkelwagen">
    </form>
<?php
// leave the echo here for testing purpose so people can see what happens before we add styling and such
    $productIdToCheck = "id5";
//    echo "Updated Quantity for Product $productIdToCheck: " . $_SESSION['shoppingCart'][$productIdToCheck][2];
    print_r($queryResult);


?>
</body>
<footer>

</footer>
</html>