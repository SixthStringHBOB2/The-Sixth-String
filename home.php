
<html>
<head>
    <meta charset="utf-8"> </meta>
    <title>Home</title>
    <link rel='stylesheet' type='text/css' href='./Stylesheet.css'> </link>
    <style>
        .headerbackground{width:100%;
            height: 528px;
            object-fit: cover;}


        .onze producten{background-color:white;
        }

    </style>
</head>
<body style="background-color:white;">
<img src="./afbeeldingen/banner 3.jpg/" class="Banner-Img">
<div>
    <?php include './Assets/Header.php';?>
</div>

    <div class="Content-standaard" style="height: 345px ">
        <h1>The Sixth String</h1>
        <p class="H1subtitle">Jouw gitaar specialist</p>

    </div>

<div class="white-seperation-bar"></div>
<div class="Contenblok1">
    <h2 style="padding: 0px 50px">Onze producten.</h2>
    <div class="flex-itemframes-container">
        <div class="item-frame1 , flex-column-standaard">
            <img src="Afbeeldingen/gitaar.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Gitaren</h3>
            <ul>
                <li class ="link3" class="Arrow">Basgitaren</li>
                <li class = link3>Elektrische gitaren</li>
                <li class = link3>Akoestische gitaren</li>
                <li class = link3>Gebruikte gitaren</li>
            </ul>
            <button class="button1">Bekijk alles</button>
        </div>
        <div class="item-frame1 , flex-column-standaard"">
            <img src="Afbeeldingen/Versterker 1.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Versterkers</h3>
            <ul>
                <li class ="link3" class="Arrow">Buizen versterkers</li>
                <li class ="link3" class="Arrow">Transistor versterkers</li>
                <li class ="link3" class="Arrow">Hybride versterkers</li>
                <li class ="link3" class="Arrow">Gebruikte versterkers</li>
            </ul>
            <button class="button1">Bekijk alles</button>
        </div>
        <div class="item-frame1  , flex-column-standaard"">
            <img src="Afbeeldingen/Gitaar accessoires 2.jpg" class="img-item-frame1"></img>
            <h3 class="h3-item-frame1">Accessoires</h3>
            <ul>
                <li class ="link3" class="Arrow">Houders en standaarden</li>
                <li class ="link3" class="Arrow">Straps</li>
                <li class ="link3" class="Arrow">Microfoons</li>
                <li class ="link3" class="Arrow">Audio apperatuur</li>
            </ul>
            <button class="button1">Bekijk alles</button>
        </div>
    </div>

</div>
<div style="background-color: white">
    <div style="padding: 40px 20px 0px 10%; height: 50%">
        <div class="flex-column-standaard , beschrijving-naast-img">
            <h2 style="text-decoration: none"><span class="highlight">Persoonlijk</span> advies en aandacht is onze <span class="highlight">missie</span>.</h2>
            <p>Bij ons draait alles om persoonlijk advies en aandacht.Of je nu net begint of een
                ervaren gitarist bent, wij helpen je graag bij het vinden van jouw ideale gitaar
                en de daarbij behorende accessoires. In onze winkel kun je verschillende modellen
                uitproberen met begeleiding van onze experts, en ook in onze webshop krijg je
                hetzelfde persoonlijke advies. Plan gerust een persoonlijk adviesgesprek in
                <span class="extrabold">– we staan voor je klaar om je te helpen bij elke muzikale stap!</span><br>
                <br>
                Email: info@TheSixthString.nl<br>
                Tel: 06 1234 5678</p>
                <button class="button3">Plan een adviesgesprek</button>
        </div>
        <img src="Afbeeldingen/Medewerkers.jpg" class="img-naast-beschrijving , img-rechts">
    </div>


</div>

<div>
    <?php include './Assets/Footer.php';?>
</div>
</body>

</html>

