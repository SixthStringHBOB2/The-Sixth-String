

<!--TEST CODE VOOR DE HEADER-->
<html>
<head>
    <title>Header</title>
    <style>
        html{font-family:Inter,sans-serif,Arial,sans-serif}
        body {margin: 0px;
        background-color: black}

        .header{padding: 20px;}


        .zoek{
            /*border: 3px solid #FC914E;*/
            /*border-style: solid;*/
            background: linear-gradient(white, white) padding-box,
            linear-gradient(to right, #FC914E, #947957) border-box;
            border: 3px solid transparent;
            border-radius: 999px;
            margin: 0px;
            padding: 10px;
            text-align: left;
            width: 100%;
            align-self;
            @media (max-width: 1000px) {
                width: calc()
            }
           }

        .fleximg{height: 55px;
            width: 55px;

            border-radius: 12px}

        .flexcontainer-row-header{
            background: white;
            padding: 2px 3px;
            border-radius: 15px;
            align-items: center;
            @media(min-width: 1000px){
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                flex-wrap: wrap;
                align-items: center;}
            @media(max-width:1000px){
                display: flex;
                flex-direction: column;)
                justify-content: flex-start;
                align-items: baseline;
            }}

        .flexcontainer-row{display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin: 3px;}

        .flexitem1{
            align-self:left;
            padding: 5px 0px;
            min-width: 25%;
            @media {
                max-width: 1000px;
                align-self: flex-start}}
        .flexitem2{
            align-self: center;
            margin: 3px;
            min-width: 35%;
            height: 40.63px;
            @media (max-width: 1000px;){
            align-self: center;}
            }
        .flexitem3{@media(min-width: 1000px){
            display: flex;
            flex-direction: row-reverse;
            min-width: 25%;}
            @media (max-width: 1000px;){
            display: flex;
            flex-direction: row-reverse;
            height: 40.63px;
        }}
        .pHeader{margin: 20px 0px}



    </style>
</head>
<body>
<div>
    <div class="header">
        <div class="flexcontainer-row-header">
            <div class="flexcontainer-row , flexitem1">
                <div>
                    <a href="home.php"><img src="../Afbeeldingen/Logo%202.jpg" class="fleximg"></a>
                </div>
                <div class="flexcontainer-row" class="flexitem1">
                    <a href="https://www.thesixthstring.com/products.php?categories=c1"><p class="link1 , pHeader" >Gitaren</p></a>
                    <a href="https://www.thesixthstring.com/products.php?categories=c5"><p class="link1 , pHeader">Versterkers</p></a>
                    <a href="https://www.thesixthstring.com/products.php?categories=c10"><p class="link1 , pHeader">Accessoires</p></a>
                </div>
            </div >
            <div class="flexitem2">
                <input type="search" class="zoek" max="75" placeholder="Zoeken..."></input>
            </div>
            <div class="flexitem3" style="align-items: center;">
                <!-- <p class="link1 , pHeader">Over ons</p>-->
                <a href="www.thesixthstring.com/shoppingcart.php"><p class="link1 , pHeader" style="font-size: 30px; text-decoration-line: none;">🛒</p></a>
                <a href="www.thesixthstring.com/account.php"><p class="link1 , pHeader">Mijn account</p></a>
                <!-- INLOG ICON -->
                <!-- WINKELWAGEN ICON -->
            </div>
            </div>
    </div>
</div>



</body>
</html>