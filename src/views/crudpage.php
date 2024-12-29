<?php
include 'views/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbconnection = getDbConnection();
$sqlGetAllItems = "SELECT * FROM item i WHERE i.isActive = 1 OR i.isActive IS NULL";
$result = mysqli_query($dbconnection, $sqlGetAllItems);
mysqli_close($dbconnection);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['id_item'])){
        $id_item = $_POST['id_item'];
        if(isset($_POST['delete'])) {
            deleteItem($id_item);
        }
        if(isset($_POST['edit'])) {
            updateDatabase($id_item);
        }
    }
    if(isset($_POST['Create'])) {
        createItem();
    }
}

function createItem(){
    $dbconnection = getDbConnection();

    $name = $_POST['name'];
    $colour = $_POST['colour'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $amount_frets = $_POST['amount_frets'];
    $amount_strings = $_POST['amount_strings'];
    $consumption = $_POST['consumption'];
    $built_in_effects = $_POST['built_in_effects'];
    $description = $_POST['description'];
    $discount = $_POST['discount'];
    $is_used = $_POST['is_used'];
    $used_damage = $_POST['used_damage'];
    $used_age = $_POST['used_age'];
    $id_category = $_POST['id_category'];
    $id_brand = $_POST['id_brand'];
    $isActive = 1; // default is always visible

    // force correct format
    if (strtotime($used_age)) {
        $used_age = date('Y-m-d', strtotime($used_age));
    } else {
        die("Invalid date format for used_age!");
    }

    $sqlCreateItem = "INSERT INTO item
        (`name`, `colour`, `price`, `weight`, `size`, `amount_frets`, `amount_strings`, `consumption`, 
        `built_in_effects`, `description`, `discount`, `is_used`, `used_damage`, `used_age`, `id_category`, `id_brand`, `isActive`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($dbconnection, $sqlCreateItem);

    mysqli_stmt_bind_param($stmt, 'ssddiiddiissisiii',
        $name, $colour, $price, $weight, $size, $amount_frets, $amount_strings, $consumption,
        $built_in_effects, $description, $discount, $is_used, $used_damage, $used_age, $id_category, $id_brand, $isActive);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($dbconnection);
}

function deleteItem($id_item) {
    $dbconnection = getDbConnection();
    $sqlDeleteItem = "UPDATE item i SET i.isActive = 0 WHERE i.id_item = $id_item";
    mysqli_query($dbconnection, $sqlDeleteItem);
    mysqli_close($dbconnection);
}

function updateDatabase($id_item) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $dbconnection = getDbConnection();

    // Retrieve user input from the form
    $name = $_POST['name'];
    $colour = $_POST['colour'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $amount_frets = $_POST['amount_frets'];
    $amount_strings = $_POST['amount_strings'];
    $consumption = $_POST['consumption'];
    $built_in_effects = $_POST['built_in_effects'];
    $description = $_POST['description'];
    $discount = $_POST['discount'];
    $is_used = $_POST['is_used'];
    $used_damage = $_POST['used_damage'];
    $used_age = $_POST['used_age'];
    $id_category = $_POST['id_category'];
    $id_brand = $_POST['id_brand'];

    // force correct format
    if (strtotime($used_age)) {
        $used_age = date('Y-m-d', strtotime($used_age));
    } else {
        die("Invalid date format for used_age!");
    }

    $sqlUpdateItem = "UPDATE item SET
        `name` = ?,
        colour = ?,
        price = ?,
        weight = ?,
        size = ?,
        amount_frets = ?,
        amount_strings = ?,
        consumption = ?,
        built_in_effects = ?,
        description = ?,
        discount = ?,
        is_used = ?,
        used_damage = ?,
        used_age = ?,
        id_category = ?,
        id_brand = ?
        WHERE id_item = ?";

    $stmt = mysqli_prepare($dbconnection, $sqlUpdateItem);

    mysqli_stmt_bind_param($stmt, 'ssddiiddiissisiii',
        $name, $colour, $price, $weight, $size, $amount_frets, $amount_strings, $consumption,
        $built_in_effects, $description, $discount, $is_used, $used_damage, $used_age, $id_category, $id_brand, $id_item
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($dbconnection);
}

?>

<html>
<head>
    <title>Item Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            font-family: Arial, sans-serif;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
            color: #333;
        }
        table td {
            border-bottom: 1px solid #ddd;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        table tbody tr:hover {
            background-color: #f9f9f9;
        }
        h2 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }
        .button-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }
        .bewerkenButton {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            background-color: #2196F3;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
        }
        .bewerkenButton:hover {
            background-color: #1976D2;
            transform: scale(1.05);
        }
        .verwijderenButton {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            background-color: #F44336;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
        }
        .verwijderenButton:hover {
            background-color: #D32F2F;
            transform: scale(1.05);
        }
        .aanmakenButton {
            padding: 6px 15px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            background-color: #2196F3;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
        }
        .aanmakenButton:hover {
            background-color: #1976D2;
            transform: scale(1.05);
        }
    </style>
</head>
<body style="background-color:white;">
<h2 style="color: black">Producten</h2>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Naam</th>
        <th>Kleur</th>
        <th>Prijs</th>
        <th>Gewicht</th>
        <th>Grootte</th>
        <th>Aantal frets</th>
        <th>Aantal snaren</th>
        <th>Consumption</th>
        <th>Built-in Effects</th>
        <th>Description</th>
        <th>Discount</th>
        <th>Is Used</th>
        <th>Used Damage</th>
        <th>Used Age</th>
        <th>Category ID</th>
        <th>Brand ID</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <form method='post' action='/crudpage'>
            <td><input type='text' name='id_item' readonly style='width: 60px;' required></td>
            <td><input type='text' name='name' style='width: 100px;' required></td>
            <td><input type='text' name='colour' style='width: 80px;' required></td>
            <td><input type='number' name='price' style='width: 80px;' step="0.01" required></td>
            <td><input type='number' name='weight' style='width: 80px;' step="0.01" required></td>
            <td><input type='number' name='size' style='width: 80px;' step="0.01" required></td>
            <td><input type='number' name='amount_frets' style='width: 80px;' required></td>
            <td><input type='number' name='amount_strings' style='width: 80px;' required></td>
            <td><input type='number' name='consumption' style='width: 80px;' step="0.01" required></td>
            <td><input type='text' name='built_in_effects' style='width: 120px;' required></td>
            <td><input type='text' name='description' style='width: 120px;' required></td>
            <td><input type='number' name='discount' style='width: 80px;' step="0.01" required></td>
            <td><input type='number' name='is_used' style='width: 80px;' required></td>
            <td><input type='text' name='used_damage' style='width: 80px;' required></td>
            <td><input type='datetime-local' name='used_age' style='width: 80px;' required></td>
            <td><input type='number' name='id_category' style='width: 80px;' required></td>
            <td><input type='number' name='id_brand' style='width: 80px;' required></td>
            <td>
                <button class='aanmakenButton' type='submit' name='Create'>Aanmaken</button>
            </td>
        </form>
    </tr>
    </tbody>

    <tbody>
    <?php
    while ($item = mysqli_fetch_assoc($result)) {
        echo "
            <tr>
                <form method='post' action='/crudpage'>
                    <td><input type='text' name='id_item' value='" . $item['id_item'] . "' readonly style='width: 60px;' required></td>
                    <td><input type='text' name='name' value='" . $item['name'] . "' style='width: 100px;' required></td>
                    <td><input type='text' name='colour' value='" . $item['colour'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='price' value='" . $item['price'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='weight' value='" . $item['weight'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='size' value='" . $item['size'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='amount_frets' value='" . $item['amount_frets'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='amount_strings' value='" . $item['amount_strings'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='consumption' value='" . $item['consumption'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='built_in_effects' value='" . $item['built_in_effects'] . "' style='width: 120px;' required></td>
                    <td><input type='text' name='description' value='" . $item['description'] . "' style='width: 120px;' required></td>
                    <td><input type='text' name='discount' value='" . $item['discount'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='is_used' value='" . $item['is_used'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='used_damage' value='" . $item['used_damage'] . "' style='width: 80px;' required></td>
                    <td><input type='date' name='used_age' value='" . $item['used_age'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='id_category' value='" . $item['id_category'] . "' style='width: 80px;' required></td>
                    <td><input type='text' name='id_brand' value='" . $item['id_brand'] . "' style='width: 80px;' required></td>
                    <td>
                        <div class='button-container'>
                            <button class='bewerkenButton' type='submit' name='edit' value='" . $item['id_item'] . "'>Bewerken</button>
                            <button class='verwijderenButton' type='submit' name='delete' value='" . $item['id_item'] . "'>Verwijderen</button>
                        </div>
                    </td>
                </form>
            </tr>
            ";
    }
    ?>
    </tbody>
</table>
</body>
<footer style="margin-top: 50px;">
    <?php include 'views/footer.php'; ?>
</footer>
</html>

