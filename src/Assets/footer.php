<!DOCTYPE html>
<html lang="nl"
    <head>
          <meta charset="UTF-8">
          <title>Footer</title>
    </head>
    <style>
        html{font-family:Inter,sans-serif,Arial,sans-serif;
        color:white;}
        body{margin:0;padding:0;}

        .background {
            background-color: black;
            border-radius: 50px 50px 0px 0px;
            padding: 50px }

        .flexcontainer-footerA{
            display: flex;
            justify-content:space-between;
            flex-wrap:wrap;
            min-width: 100%;
            @media(max-width:975px){display: flex;
            flex-direction: column;}

        .flexitem-footer{
           display: flex;
            flex-direction: column;
            justify-content: flex-start;
            @media(max-width: 975px){padding-bottom: 50px;}}
        }



        .imgfooter {
            width: 380px;
            height: 100%;
            border-radius: 25px;
            object-fit: cover;
            @media (max-width: 975px) {
                width: 100%;
                max-height: 300px;
            }
        }

        p{margin: 0px 0px 15px 0px;}

    </style>
<body>
<div class="background" >
    <div class="flexcontainer-footerA">
            <div class="flexitem-footer">
                <h5>Sitemap</h5>
                <p><a href="https://www.thesixtstring.com" class="link2">Home</a></p>
                <p><a href="https://www.thesixthstring.com/products.php" class="link2">Catalogus</a></p>
                <!-- <p><a class="link2">Over Ons</a></p> -->
                <p><a href="https://www.thesixtstring.com/shoppingcart.php" class="link2">Winkelwagen</a></p>
            </div>
            <div style="margin:0px 30px 0px 0px;" class="flexitem-footer">
                <h5>Mijn account</h5>
                <p><a class="link2">Overzicht</a></p>
                <p><a class="link2">Bestellingen</a><br></p>
                <p><a class="link2">Retouren en reperaties</a><br></p>
                <p><a class="link2">Gegevens en voorkeuren</a><br></p>
                <p><a class="link2">Verlanglijstje</a></p>
            </div>
            <div class="flexitem-footer">
                <img src="../Afbeeldingen/Gitaar%20winkel.jpg" class='imgfooter' alt="Gitaar winkel">
            </div>
            <div class="flexitem-footer">
                <h5>Contact</h5>
                <p><a href="tel:+31612345678" class="link2">0612345678</a><br></p>
                <p><a href="mailto:info@TheSixthString.nl?subject=Contact" class="link2">info@TheSixthString.nl</a><br></p>
                <p><a href="https://wa.me/message/IDtheSIXTHstring" class="link2">Whatsapp</a><br></p>
                <p><a href="https://www.instagram.com/user/TheSixthString" class="link2">Instagram</a></p>
            </div>
    </div>

</div>
</body>