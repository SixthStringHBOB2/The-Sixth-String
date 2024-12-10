<HTML>

<?php
session_start();
//require ('database/db.php');
$amount = 501;
$userLoggedIn = true;
$_SESSION['LoggedInUser'] = ["user" => ["1"]];
// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
$_SESSION['shoppingCart'] = [
    "id1" => ["1", "product 1", "15,50", $amount],
    "id2" => ["2", "product 2", "15,50", $amount],
    "id3" => ["3", "product 3", "15,50", $amount],
];

if(isset($_SESSION['LoggedInUser'])){
    $dbConnection = getDbConnection();
    $query = "SELECT sci.amount, sci.id_item, i.`name`, i.`price`
              FROM shopping_cart sc
              LEFT JOIN shopping_cart_item sci ON sc.id_shopping_cart_item = sci.id_shopping_cart_item
              LEFT JOIN item i ON sci.id_item = i.id_item
              WHERE sc.id_user = 1"; //TODO make the userId variable

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
    }else {
        //TODO make one shopping_cart
    }



    echo "<pre>";
    print_r($_SESSION['shoppingCart']);
    echo "</pre>";
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
        $amount = $product[3];

        $dbConnection = getDBConnection();
        $sql = "INSERT INTO shopping_cart_item (id_item, amount) VALUES ($productId, $amount)";

        mysqli_query($dbConnection, $sql);
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



//$dbConnection = getDbConnection();
//$query = "SELECT * FROM user";
//$queryResult = mysqli_fetch_all(mysqli_query($dbConnection, $query));

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
                $productPrice = $product[2];
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
    </form>
    <form method="POST" action="/shoppingcart">
        <input type="hidden" name="formType" value="purchaseCart"> <!-- Identify this as the purchase form -->
        <input type="submit" name="PurchaseButton" value="Koop winkelwagen">
    </form>
<?php
// leave the echo here for testing purpose so people can see what happens before we add styling and such
    $productIdToCheck = "id2";
    echo "Updated Quantity for Product $productIdToCheck: " . $_SESSION['shoppingCart'][$productIdToCheck][3];
//    print_r($queryResult);
?>
</body>
<footer>

</footer>
</html>