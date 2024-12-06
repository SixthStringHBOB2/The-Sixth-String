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
            @media(max-width:929px){display: flex;
            flex-direction: column;}

        .flexitem-footer{}

        .flexcontainer-footerB{
            display: flex;
            width: 50%;}

        .flexcontainer-footerC{
            display: flex;
            width: 50%;}


        .imgfooter{
            max-width: 300px;
            min-width: 100px;
            height: 200px;
            border-radius: 25px;
            margin: 0px 30px 0px 0px;
            align-self: flex-end;}

        .link2{
            text-decoration-line: underline;
            font-weight: light;
            color: white;
            Padding: 5px 0px;
            margin:5px 0px;}

    </style>
<body>
<div class="background">
    <div class="flexcontainer-footerA">
            <div style="margin:0px 30px 0px 0px;" class="flexitem-footer">
                <h4>Sitemap</h4>
                <p><a class="link2">Home</a><br>
                <a class="link2">Catalogus</a><br>
                <a class="link2">Over Ons</a><br>
                <a class="link2">Winkelwagen</a></p>
            </div>
            <div style="margin:0px 30px 0px 0px;" class="flexitem-footer">
                <h4>Mijn account</h4>
                <a class="link2">Overzicht</a><br>
                <a class="link2">Bestellingen</a><br>
                <a class="link2">Retouren en reperaties</a><br>
                <a class="link2">Gegevens en voorkeuren</a><br>
                <a class="link2"Verlanglijstje</a>
            </div>
            <div style="flex: 1 1 auto" class="flexitem-footer">
                <img src="/assets/images/GitaarWinkel.jpg" class='imgfooter' alt="Gitaar winkel">
            </div>
            <div class="flexitem-footer">
                <h4>Contact</h4>
                <p class="link2"><a class="link2">0612345678</a><br>
                    <br>
                <a class="link2">info@TheSixthString.nl</a><br>
                <a class="link2">Whatsapp</a><br>
                <a class="link2">Instagram</a></p>
            </div>

    </div>

</div>
</body>