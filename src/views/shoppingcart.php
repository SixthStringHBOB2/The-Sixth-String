<HTML>

<?php
session_start();
function db_connect()
{
    $host = 'localhost';
    $username = 'admin';
    $password = 'admin';
    $database = 'admin';

// Create connection
    return mysqli($host, $username, $password, $database);
}

function get_all_tables()
{
    $db_connection = db_connect();
    $query = "SHOW TABLES";
    $result = mysqli_fetch_all(mysqli_query($db_connection, $query));
    mysqli_close($db_connection);
    return $result;
}

$amount = 1;
// dummy products, this is how we expect it to be stored in the session. Everything should be a variable so id1 and product 1 are varibale. The number at the end is how much the customer wants
$_SESSION['productId'] = [
    "id1" => ["id1", "product 1", $amount],
    "id2" => ["id2", "product 2", $amount],
    "id3" => ["id3", "product 3", $amount],
    "id4" => ["id4", "product 4", $amount],
    "id5" => ["id5", "product 5", $amount],
    "id6" => ["id6", "product 6", $amount],
    "id7" => ["id7", "product 7", $amount]
];

?>
<script>
    function incrementAmount(productId) {
        const element = document.getElementById('incrementText_' + productId);
        if (element) {
            let value = parseInt(element.innerHTML);
            value++;
            element.innerHTML = value;
        }
    }

    function decrementAmount(productId) {
        const element = document.getElementById('incrementText_' + productId);
        if (element) {
            let value = parseInt(element.innerHTML);
            if (value > 0) {
                value--;
            }
            element.innerHTML = value;
        }
    }
</script>


<head>
    <title>Shopping Cart</title>
    <!--        include header-->
</head>

<body>
    <h1>Shopping Cart</h1>
<table>
    <tr>
        <th>Plaatje</th>
        <th>Product ID</th>
        <th>Product naam</th>
        <th>aantal</th>
    </tr>
    <?php
    foreach ($_SESSION['productId'] as $product) {
        $productId = $product[0];  // The product ID
        $productName = $product[1];  // The product name
        $amount = $product[2];  // The amount

        echo "
            <tr>
                <td>plaatje</td>
                <td>$productId</td> 
                <td>$productName</td> 
                <td><label id='incrementText_$productId'>$amount</label></td> 
                <td>
                    <button type='button' onclick='incrementAmount(\"$productId\")'>Increment</button>
                </td>
                <td>
                    <button type='button' onclick='decrementAmount(\"$productId\")'>Subtract</button>
                </td>
            </tr>
        ";
    }
    ?>
</table>

</body>
<footer>
    <!--    include footer -->
</footer>
</html>
