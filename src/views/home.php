<html lang="">
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel='stylesheet' type='text/css' href='/assets/css/Stylesheet.css'>
    <style>
        .headerbackground {
            width: 100%;
            height: 528px;
            object-fit: cover;
        }

        .onze-producten {
            background-color: white;
        }
    </style>
</head>
<body style="background-color:white;">
<?php include 'views/header.php'; ?>
<img src="/assets/images/Banner3.jpg" class="Banner-Img">
<div class="Content-standaard">
    <h1>The Sixth String</h1>
    <p class="H1subtitle">Jouw gitaar specialist</p>
</div>
<div class="white-seperation-bar"></div>
<div class="Contenblok1">
    <h2 style="padding: 0px 50px">Onze producten.</h2>
    <div class="flex-itemframes-container">
        <!-- Guitars -->
        <div class="item-frame1">
            <img src="/assets/images/Gitaar.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Gitaren</h3>
            <ul>
                <li class="link3 Arrow">Basgitaren</li>
                <li class="link3">Elektrische gitaren</li>
                <li class="link3">Akoestische gitaren</li>
                <li class="link3">Gebruikte gitaren</li>
            </ul>
        </div>
        <div class="item-frame1">
            <img src="/assets/images/Versterker1.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Versterkers</h3>
            <ul>
                <li class="link3 Arrow">Buizen versterkers</li>
                <li class="link3 Arrow">Transistor versterkers</li>
                <li class="link3 Arrow">Hybride versterkers</li>
                <li class="link3 Arrow">Gebruikte versterkers</li>
            </ul>
        </div>
        <div class="item-frame1">
            <img src="/assets/images/GitaarAccessoires2.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Accessoires</h3>
            <ul>
                <li class="link3 Arrow">Houders en standaarden</li>
                <li class="link3 Arrow">Straps</li>
                <li class="link3 Arrow">Microfoons</li>
                <li class="link3 Arrow">Audio apperatuur</li>
            </ul>
        </div>
    </div>
</div>
<div style="background-color: white;">
    <h2 style="text-decoration: none">
        <span class="highlight">Persoonlijk</span> advies en aandacht is onze <span class="highlight">missie</span>.
    </h2>
    <p>
        Bij ons draait alles om persoonlijk advies en aandacht. Of je nu net begint of een ervaren gitarist bent, wij helpen je graag bij het vinden van jouw ideale gitaar en de daarbij behorende accessoires. In onze winkel kun je verschillende modellen uitproberen met begeleiding van onze experts, en ook in onze webshop krijg je hetzelfde persoonlijke advies. Plan gerust een persoonlijk adviesgesprek in
        <span class="extrabold">– we staan voor je klaar om je te helpen bij elke muzikale stap!</span><br><br>
        Email: info@TheSixthString.nl<br>
        Tel: 06 1234 5678
    </p>
</div>
<?php include 'views/footer.php'; ?>
</body>
</html>
