<HTML>

<?php
session_start();
include ('db.php');
$amount = 1;
$userLoggedIn = true;
// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
$_SESSION['shoppingCart'] = [
    "id1" => ["id1", "product 1", $amount],
    "id2" => ["id2", "product 2", $amount],
    "id3" => ["id3", "product 3", $amount],
    "id4" => ["id4", "product 4", $amount],
    "id5" => ["id5", "product 5", $amount],
    "id6" => ["id6", "product 6", $amount],
    "id7" => ["id7", "product 7", $amount]
];

function checkIfShoppingAlreadyExist($userLoggedIn){
    //check if user is logged in, if user is NOT logged in skip this function
    //the variable i am using to check if user is logged in is placeholder for the real one

    if($userLoggedIn){
        $dbConnection = getDbConnection();
        $query = "SELECT count(*) FROM shoppingcart";
        $queryResult = mysqli_fetch_all(mysqli_query($dbConnection, $query));

        //TODO map $queryResult to $_SESSION['productId'] and fill it or update it? idk need to check it
    }
}


//Code below updates the amount the customer wants to buy to the php session variable
//it gets the value from the hidden input field which is being updated with js
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amounts'])) {
    foreach ($_POST['amounts'] as $productId => $amount) {
        if (isset($_SESSION['shoppingCart'][$productId])) {
            $_SESSION['shoppingCart'][$productId][2] = $amount;
            if($_SESSION['shoppingCart'][$productId][2] < 1){
                unset($_SESSION['shoppingCart'][$productId]);
            }
        }
    }
}

?>
<script>
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
    <!--        include header-->
</head>

<body>
    <h1>Shopping Cart</h1>

    <form method="POST" action="/shoppingcart">
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
        <input type="submit" name="PurchaseButton" value="Koop winkelwagen"> <!--TODO once this button is pressed the user buys the shit, make it so that the bestellingen table in db gets updated -->
    </form>
<?php
// leave the echo here for testing purpose so people can see what happens before we add styling and such
    $productIdToCheck = "id5";
    echo "Updated Quantity for Product $productIdToCheck: " . $_SESSION['shoppingCart'][$productIdToCheck][2];
?>
</body>
<footer>

</footer>
</html>
