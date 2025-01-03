<?php
session_start();
//ini_set('display_errors', 1);

//$id_item = $_SESSION['id_product'] ?? 9;
$product_id = $_GET['product_id']; //Get product id from URL param
$id_item = $product_id  ?? 67; //LAATSTE CIJFER VOOR TESTEN SPECIFIEKE PRODUCTEN
include './database/dp.php' //OPHALEN DATA VERBINDING
;
//function getDbConnection() {
//    $host = "localhost";
//    $dbname = "thesixthstring";
//    $username = "thesixthstring";
//    $password = "HFIU67135dhaf";
//
//    // Check for missing environment variables
//    if (!$host || !$dbname || !$username || !$password) {
//        die('Missing environment variables for database connection');
//    }
//
//    // Create and return the MySQLi connection
//    $mysqli = new mysqli($host, $username, $password, $dbname);
//
//    if ($mysqli->connect_error) {
//        die("Connection failed: " . $mysqli->connect_error);
//    }
//
//    return $mysqli;
//}

$dbConnection = getDbConnection();
$query = "SELECT * 
          FROM thesixthstring.item 
          WHERE `id_item` = $id_item;";
$queryResultProduct = mysqli_fetch_all(mysqli_query($dbConnection, $query));


//PRODUCT INFORMATIE OPHALEN
$name = $queryResultProduct[0][1];
$colour = $queryResultProduct[0][2];
$price = $queryResultProduct[0][3];
$weight = $queryResultProduct[0][4];
$size = $queryResultProduct[0][5];
$amount_frets = $queryResultProduct[0][6];
$consumption = $queryResultProduct[0][7];
$build_in_effect = $queryResultProduct[0][9];
$description = $queryResultProduct[0][10];
$discount = $queryResultProduct[0][11];
$is_used = $queryResultProduct[0][12];
$used_damage = $queryResultProduct[0][13];
$used_age = $queryResultProduct[0][14];



// AANTAL RATINGS VAN DE REVIEWS (1-5)
function getReviewCountsByRating($id_item) {
    $query = "
        SELECT rating, COUNT(id_review) AS count_reviews
        FROM thesixthstring.review
        WHERE id_item = ?
        GROUP BY rating
    ";

    $dbConnection = getDbConnection();

    // QUERY MAKEN
    if ($statement = $dbConnection->prepare($query)) {

        $statement->bind_param("i", $id_item);

        // QUERY UITVOEREN
        $statement->execute();

        $result = $statement->get_result();

        // OPZET ARRAY
        $ratings = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        // ARRAY MET DE RESULTATEN
        while ($row = $result->fetch_assoc()) {
            $ratings[(int)$row['rating']] = (int)$row['count_reviews'];
        }

        // CLOSE DB
        $statement->close();
        $dbConnection->close();

        return $ratings;
    } else {
        die("Fout bij het voorbereiden van de query: " . $dbConnection->error);
    }
}

$reviewCounts = getReviewCountsByRating($id_item);

// VARIABELEN VAN DE RESULTATEN
$TotaalRating1 = $reviewCounts[1];
$TotaalRating2 = $reviewCounts[2];
$TotaalRating3 = $reviewCounts[3];
$TotaalRating4 = $reviewCounts[4];
$TotaalRating5 = $reviewCounts[5];
$TotaalRatings = $TotaalRating1 + $TotaalRating2 + $TotaalRating3 + $TotaalRating4 + $TotaalRating5;

//PERCENTAGE VOOR DE SLIDERS
if ($TotaalRatings > 0) {
    $Percentage1 = 100 / $TotaalRatings * $TotaalRating1;
    $Percentage2 = 100 / $TotaalRatings * $TotaalRating2;
    $Percentage3 = 100 / $TotaalRatings * $TotaalRating3;
    $Percentage4 = 100 / $TotaalRatings * $TotaalRating4;
    $Percentage5 = 100 / $TotaalRatings * $TotaalRating5;
}

//OUTPUT TEST
//echo "Totaal reviews met rating 1: $TotaalRating1\n";
//echo "Totaal reviews met rating 2: $TotaalRating2\n";
//echo "Totaal reviews met rating 3: $TotaalRating3\n";
//echo "Totaal reviews met rating 4: $TotaalRating4\n";
//echo "Totaal reviews met rating 5: $TotaalRating5\n";
//echo "Totaal aantal reviews: $TotaalRatings\n";


//REVIEWS OPHALEN
function getReviewsByItemId($id_item) {
    $db = getDbConnection();

    $query = "
        SELECT 
            Name, 
            rating, 
            Description, 
            id_user 
        FROM thesixthstring.review
        WHERE id_item = ?
        ORDER BY rating DESC";

    // STATEMENT
    if ($stmt = $db->prepare($query)) {
        // BIND PARAMETERS EN QUERY UITVOEREN
        $stmt->bind_param("i", $id_item);
        $stmt->execute();

        // RESULTATEN OPHALEN
        $result = $stmt->get_result();
        $reviews = [];

        // RESULTATEN NAAR ARRAY
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;}

        $stmt->close();
        $db->close();

        return $reviews;
    } else {
        die("Fout bij het voorbereiden van de query: " . $db->error);
    }
}

// REVIEWS IN HTML
function displayReviews($id_item) {
    $reviews = getReviewsByItemId($id_item);

    if (empty($reviews)) {
        echo "<p>Geen reviews gevonden voor dit item.</p>";
        return;
    }

    foreach ($reviews as $review) {
        echo "<div class='item-frame2'>";
        echo "<div class='review'>";
        echo "<h4>" . ($review['Name']) . "</h4>";
        echo "<p class='extrabold'>Rating: " . str_repeat("⭐ ", (int)$review['rating']) . "</p>";
        echo "<p>" . ($review['Description']) . "</p>";
        //echo "<p class='extrabold'>- " . ($review['id_user']) . "</p>"; DOORONTWIKKELING: USER KOPPELEN AAN REVIEW
        echo "</div>";
        echo "</div>";
    }
}

?>

<html>
<head>
    <meta charset="utf-8"> </meta>
    <title>Product <?php echo"$name" ?></title>
    <link rel='stylesheet' type='text/css' href='./public/css/Stylesheet.css'> </link>
</head>

<body style="background-color:white;">


<img src="./public/images/Banner4.jpg" class="Banner-Img">
<div>
    <?php include './views/Header.php';?>
</div>

<div class="Content-standaard" style="height: 345px ">
    <h1></h1>
    <p class="H1subtitle"></p>
</div>

<div class="white-seperation-bar"></div>
<div style="background-color: white; padding: 0px 20px 20px 5%;"><a href="" class="link3">Terug</a></div>

<div style="background-color: white;" class="beschrijving-flex">
    <img class="beschrijving-product-img" src="./public/images/Gitaartje.jpg""> <!--TEST AFBEELDING!-->
    <div class="beschrijving-product" style="min-width: 30%; min-height: 60%;">
        <div class="flex-row-standaard">
            <h2><?php echo($name) ?></h2>
            <p class="prijs">Є<?php echo($price) ?></p>
        </div>
        <p>
            <?php echo($description)?>
        </p>
        <div class="flex-dubbel-button">
            <button class="button3"><a href="mailto:info@TheSixthString.nl?subject=Inruilen gitaar&body=Graag ontvangen we de volgende informatie
            om een waardeschatting van uw product te kunnen maken:%0A %0A 1. Wat u wilt inruilen:%0A 2. Welke schade
            heeft het product:%0A 3. Wat is de leeftijd van het product:%0A %0A Voeg tot slot afbeeldingen toe, zodat
            we een betere inschatting van de waarde kunnen maken.">Gitaar inruilen</a></button>
            <form action="./shoppingcart" method="POST">
                <input type="hidden" name="id_item" value="<?php echo ($id_item); ?>">
                <input type="hidden" name="amount" value="1">
                <button type="submit" name="action" value="add_to_cart" class="button4">
                    In winkelwagen 🛒
                </button>
            </form>
        </div>
    </div>
</div>


<div   class="gradient-seperation-bar">
    <div  class="flex-row-standaard" style="padding: 10px 10% 50px 10%">
        <div>
            <h2>Specificaties</h2>
            <p>
            <?php
            function renderProductInfo($queryResultProduct)
            {
                // ALLE GEGEVENS UIT DATABASE $queryResult
                $name = $queryResultProduct[0][1];
                $colour = $queryResultProduct[0][2];
                $price = $queryResultProduct[0][3];
                $weight = $queryResultProduct[0][4];
                $size = $queryResultProduct[0][5];
                $amount_frets = $queryResultProduct[0][6];
                $consumption = $queryResultProduct[0][7];
                $build_in_effect = $queryResultProduct[0][9];
                $description = $queryResultProduct[0][10];
                $discount = $queryResultProduct[0][11];
                $is_used = $queryResultProduct[0][12];
                $used_damage = $queryResultProduct[0][13];
                $used_age = $queryResultProduct[0][14];

                //BASIS SPECS HTML
                $specs = "
                    <span class=\"extrabold\">Kleur:</span> {$colour} <br>
                    <span class=\"extrabold\">Prijs:</span> €{$price} <br>
                    <span class=\"extrabold\">Gewicht: </span> {$weight} kg<br>
                    <span class=\"extrabold\">Grootte: </span> {$size} <br>
                    <span class=\"extrabold\">Aantal frets: </span> {$amount_frets} <br>
                    <span class=\"extrabold\">Energie verbruik:</span> {$consumption} watt<br>
                    <span class=\"extrabold\">Ingebouwde effecten:</span> {$build_in_effect}<br>";

                // Controleer of het product gebruikt is
                if ($is_used == 1) {
                    // LEEFTIJD BEREKENEN
                    $currentDate = new DateTime(); // HUIDIGE DATUM
                    $originalDate = new DateTime($used_age); // DATUM UIT $used_age
                    $ageInterval = $currentDate->diff($originalDate); // VERSCHIL

                    if ($ageInterval->y >= 1) {
                        $productAge = $ageInterval->y . " jaar";
                    } else {
                        $productAge = $ageInterval->m . " maanden";
                    }

                    //EXTRA INFO GEBRUIKT
                    $specs .= "
                        <span class=\"extrabold\">Staat: </span> Gebruikt <br>
                        <span class=\"extrabold\">Schade: </span> {$used_damage} <br>
                        <span class=\"extrabold\">Leeftijd: </span> {$productAge} <br>";
                    }

                return $specs; //ALLE SPECS

            }
            $queryResult = [
                [null, $name, $colour, $price, $weight, $size, $amount_frets, $consumption, null, $build_in_effect, null , null , $is_used , $used_damage, $used_age]];
            echo renderProductInfo($queryResult); //PLAATSEN VAN DE HTML CODE VAN DE SPECS
            ?>
        </div>
        <div style="max-width: 400px">
            <h2>Waarom The Sixth String?</h2>
            <p>
                ✅ 30 dagen bedenktijd <br>
                ✅ In de winkel proberen <br>
                ✅ Persoonlijk advies <br>
                ✅ Vandaag besteld, morgen in huis <br>
                ✅ Heb je vragen? Tot 17:00 zijn we bereikbaar <br>
                ✅ 2 jaar fabrieks garantie
            </p>
        </div>
    </div>
    <div class="white-seperation-bar"></div>
</div>

<!-- SIDE MENU -->
<div class="flex-sidemenu" style="margin-bottom: 50px;">
    <div class="sidemenu">
        <h2>Reviews</h2>
        <h3>Beoordeling</h3> <br>
        <div class="flex-column-standaard" style="max-width: 450px">
            <div class="review-flex">
                <p class="review-aantel-sterren">5 sterren</p>
                <div class="review-bar-bg" style="background: linear-gradient(to right, #FC914E <?php echo($Percentage5)?>% , white <?php echo($Percentage5)?>%)"></div>
                <p class="review-text"><?php echo($TotaalRating5); if ($TotaalRating5 == 1) {echo " review";} else {echo " reviews";} ?></p>
            </div>
            <div class="review-flex"">
                <p class="review-aantel-sterren">4 sterren</p>
                <div class="review-bar-bg" style="background: linear-gradient(to right, #FC914E <?php echo($Percentage4)?>% , white <?php echo($Percentage4)?>%)"></div>
                <p class="review-text"><?php echo($TotaalRating4); if ($TotaalRating4 == 1) {echo " review";} else {echo " reviews";} ?> </p>
            </div>
            <div class="review-flex">
                <p class="review-aantel-sterren">3 sterren</p>
                <div class="review-bar-bg" style="background: linear-gradient(to right, #FC914E <?php echo($Percentage3)?>% , white <?php echo($Percentage3)?>%)"></div>
                <p class="review-text"><?php echo($TotaalRating3); if ($TotaalRating3 == 1) {echo " review";} else {echo " reviews";} ?> </p>
            </div>
            <div class="review-flex">
                <p class="review-aantel-sterren">2 sterren</p>
                <div class="review-bar-bg" style="background: linear-gradient(to right, #FC914E <?php echo($Percentage2)?>% , white <?php echo($Percentage2)?>%)"></div>
                <p class="review-text"><?php echo($TotaalRating2); if ($TotaalRating2 == 1) {echo " review";} else {echo " reviews";} ?></p>
            </div>
            <div class="review-flex">
                <p class="review-aantel-sterren">1 ster</p>
                <div class="review-bar-bg" style="background: linear-gradient(to right, #FC914E <?php echo($Percentage1)?>% , white <?php echo($Percentage1)?>%)"></div>
                <p class="review-text"><?php echo($TotaalRating1);  if ($TotaalRating1 == 1) {echo " review";} else {echo " reviews";} ?></p>
            </div>

<?php
        // Toon de succes- of foutmelding als deze is ingesteld
        if (isset($_SESSION['success_message'])) {
        echo '<p class="success-message" style="color: green; font-weight: bold;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        unset($_SESSION['success_message']); // Wis het bericht na weergave
        }

        if (isset($_SESSION['error_message'])) {
        echo '<p class="error-message" style="color: red; font-weight: bold;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']); // Wis het bericht na weergave
        }
?>
        <!-- HET SCHRIJVEN VAN EEN REVIEW -->
        <label for="toggle-form" class="button4">Schrijf een review</label>
        <input type="checkbox" id="toggle-form" class="hidden-toggle" />

        <?php include './services/submitreview.php'; ?>

        <div class="review-form">
            <form method="POST" action="/submitreview"> <!-- EXTERN BESTAND REGELD HET PLAATSEN VAN DE REVIEW -->

                <!-- Verborgen veld voor id_item -->
                <input type="hidden" name="id_item" value="<?php echo htmlspecialchars($id_item, ENT_QUOTES, 'UTF-8'); ?>">

                <label for="review-title">Naam van de review</label>
                <input type="text" id="review-title" name="name" required />

                <!-- RATING -->
                <label for="rating">Rating (1-5 sterren)</label>
                <div class="star-rating">
                    <input type="radio" id="star1" name="rating" value="5" required />
                    <label for="star1">★</label>
                    <input type="radio" id="star2" name="rating" value="4" />
                    <label for="star2">★</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3">★</label>
                    <input type="radio" id="star4" name="rating" value="2" />
                    <label for="star4">★</label>
                    <input type="radio" id="star5" name="rating" value="1" />
                    <label for="star5">★</label>
                </div>

                <label for="review-description">Beschrijving van de review</label>
                <input id="review-description" name="description" required></input>

                <!-- VERZENDEN -->
                <button type="submit" class="button3" style="display: block; margin-left: auto; margin-right: auto;">Review indienen</button>
            </form>
        </div>
        </div>
    </div>
    <div style="width: 100%;
    @media(max-width 900px){width: calc(100vw - 410px)}">
        <div >
            <?php displayReviews($id_item); ?>
        </div>
    </div>
</div>

<div>
    <?php include './views/Footer.php';?>
</div>