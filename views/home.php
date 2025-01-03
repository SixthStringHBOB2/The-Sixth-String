
<html>
<head>
    <meta charset="utf-8"> </meta>
    <title>Home</title>
    <link rel='stylesheet' type='text/css' href='./public/css/Stylesheet.css'> </link>
    <style>
        .onze producten{background-color:white;
        }

    </style>
</head>
<body style="background-color:white;">
<img src="./public/images/Banner3.jpg" class="Banner-Img">
<div>
    <?php include './views/Header.php';?> <!--//INLADEN HEADER-->
</div>

    <div class="Content-standaard" style="height: 345px ">
        <h1>The Sixth String</h1>
        <p class="H1subtitle">Jouw gitaar specialist</p>

    </div>

<div class="white-seperation-bar"></div>
<div class="Contenblok1">
    <a href="/productdetailpagina"><h2 style="padding: 0px 50px; margin-left: auto; margin-right: auto; max-width:1900px;">Onze producten.</h2></a>
    <div class="flex-itemframes-container" style="margin-left: auto; margin-right: auto; margin-bottom: 50px;">
        <div class="item-frame1 , flex-column-standaard">
            <img src="./public/images/gitaar.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Gitaren</h3>
            <ul>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c1" class="arrow">Basgitaren</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c2" class="arrow">Elektrische gitaren</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c3" class="arrow">Akoestische gitaren</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c4" class="arrow">Gebruikte gitaren</a></li>
            </ul>
            <a href="/products" class="button1">Bekijk alles</a>
        </div>
        <div class="item-frame1 , flex-column-standaard"">
            <img src="./public/images/Versterker1.jpg" class="img-item-frame1">
            <h3 class="h3-item-frame1">Versterkers</h3>
            <ul>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c5" class="arrow">Buizen versterkers</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c6" class="arrow">Transistor versterkers</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c7" class="arrow">Hybride versterkers</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c8" class="arrow">Gebruikte versterkers</a></li>
            </ul>
        <a href="/products" class="button1">Bekijk alles</a>
        </div>
        <div class="item-frame1  , flex-column-standaard"">
            <img src="./public/images/Gitaaraccessoires2.jpg" class="img-item-frame1"></img>
            <h3 class="h3-item-frame1">Accessoires</h3>
            <ul>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c1" class="arrow">Houders en standaarden</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c2" class="arrow">Straps</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c3" class="arrow">Microfoons</a></li>
                <li class ="link3" style="padding-bottom: 7px;"><a href="https://www.thesixthstring.com/products?categories=c4" class="arrow">Audio apperatuur</a></li>
            </ul>
            <a href="/products" class="button1">Bekijk alles</a>
        </div>
    </div>

</div>
<div style="background-color: white;">
    <div class="beschrijving-flex" style=" margin-left: auto; margin-right: auto; ">
        <div class="beschrijving-product" style="left: 20px;">
            <h2 style="text-decoration: none"><span class="highlight">Persoonlijk</span> advies en aandacht is onze <span class="highlight">missie</span>.</h2>
            <p>Bij ons draait alles om persoonlijk advies en aandacht.Of je nu net begint of een
                ervaren gitarist bent, wij helpen je graag bij het vinden van jouw ideale gitaar
                en de daarbij behorende accessoires. In onze winkel kun je verschillende modellen
                uitproberen met begeleiding van onze experts, en ook in onze webshop krijg je
                hetzelfde persoonlijke advies. Plan gerust een persoonlijk adviesgesprek in
                <span class="extrabold">– we staan voor je klaar om je te helpen bij elke muzikale stap!</span><br>
                <br>
                Email: <a href="mailto:info@TheSixthString.nl?subject=Contact" class="link3">info@TheSixthString.nl</a><br>
                Tel: <a href="tel:+31612345678" class="link3">06 1234 5678</a></p>
                <div class="flex-dubbel-button" style="justify-content: center;">
                    <a href="mailto:info@TheSixthString.nl?subject=Inplannen advies gesprek&body=Laat ons weten wanneer je graag langs zou willen komen.">
                        <button class="button3"  style="min-width: 100%">Plan een adviesgesprek</button></a>
                </div>

        </div>

            <img src="./public/images/Medewerkers.jpg" style="left: 20px; object-position: top; max-width: 1000px;" class="beschrijving-product-img">

    </div>


</div>

<div>
    <?php include './views/Footer.php';?> <!--//INLADEN FOOTER-->
</div>
</body>

</html>

