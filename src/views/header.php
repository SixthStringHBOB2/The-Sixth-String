<!--TEST CODE VOOR DE HEADER-->
<html>
<head>
    <title>Header</title>
    <style>
        html {
            font-family: Inter, sans-serif, Arial, sans-serif
        }

        body {
            margin: 0px;
            background-color: black
        }

        .header {
            padding: 10px 20px;
            margin-bottom: 20px;
        }

        .link1 {
            text-decoration-line: underline;
            font-weight: bold;
            color: #444C50;
            Padding: 0px 10px;
            margin: 0px;
        }

        .zoek {
            border: 3px #FC914E;
            border-style: solid;
            border-radius: 999px;
            margin: 0px;
            padding: 10px;
            text-align: left;
            min-width: 150px;
        }

        .fleximg {
            height: 55px;
            width: 55px;

            border-radius: 12px
        }

        .flexcontainer-row-header {
            display: flex;
            flexdirection: row;
            justify-content: space-between;
            background: white;
            padding: 2px 3px;
            border-radius: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .flexcontainer-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin: 3px;
        }

        .flexitem1 {
            width: 30%;
            allign-self: left;
            padding: 5px 0px;
        }

        .flexitem2 {
            width: 40%;
            allign-self: center;
            margin: 3px;
        }

        .flexitem3 {
            width: 30%
            allign-self: right;
        }

        .bgimg {
            background-position: top;
        }

        /* STANDAARD OPMAAK */


    </style>
</head>
<body>
<div>
    <div class="header">
        <div class="flexcontainer-row-header">
            <div class="flexcontainer-row" class="flexitem1">
                <div>
                    <img src="/assets/images/Logo2.jpg" class="fleximg">
                </div>
                <div class="flexcontainer-row" class="flexitem1">
                    <p class="link1">Gitaren</p>
                    <p class="link1">Versterkers</p>
                    <p class="link1">Accessoires</p>
                </div>
            </div>
            <div class="flexitem2">
                <p class="zoek">Zoeken...</p>
            </div>
            <div class="flexitem3">
                <p class="link1">Over ons</p>

            </div>
        </div>
    </div>
</div>


</body>
</html>