<HTML>
    <head>
        <title>Shopping Cart</title>
<!--        include header-->
    </head>

    <body>
        <h1>Shopping Cart</h1>
        <?php
            echo get_all_tables();
        ?>
    </body>

    <footer>
<!--    include footer -->
    </footer>
</html>


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

?>