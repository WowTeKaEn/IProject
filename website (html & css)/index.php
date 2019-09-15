<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<link rel="icon" type="image/png" href="img/favicon.png">
<title>Eenmaal Andermaal | Home</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
include_once 'functions/functions_veilingMails.php';
?>

<div class="zoekenOutside w-100 py-5 d-flex align-items-center flex-column">
    <div class="welkom">
        <section class="d-flex flex-column align-items-center my-5">

        <?php
        //Kijken of de gebruiker zijn/haar wachtwoord aangepast heeft.
        //Zo ja dan een message tonen, zo nee dan niks doen
        if(isset($_SESSION['aangepast'])){
            if($_SESSION['aangepast'] == true){
                makeModals("Succes", "Uw wachtwoord is gewijzigd!");
                $_SESSION['aangepast'] = false;
            }
        }
        //Kijken of de gebruiker zijn/haar account geverifieerd heeft.
        //Zo ja dan een message tonen, zo nee dan niks doen
        if(isset($_SESSION['gevalideerd'])){
            if($_SESSION['gevalideerd'] == true){
                makeModals("Succes", "Uw account is geverifieerd!");
                $_SESSION['gevalideerd'] = false;
            }
        }
        //Kijken of de gebruiker een verkoper is geworden.
        //Zo ja dan een message tonen, zo nee dan niks doen
        if(isset($_SESSION['verkoper'])){
            if($_SESSION['verkoper'] == true){
                makeModals("Succes", "U bent nu een verkoper");
                $_SESSION['verkoper'] = false;
            }
        }
        if(isset($_SESSION['loggedin'])){
            echo "<h1 class='display-4 text-center'>Welkom bij Eenmaal Andermaal "
            . ($_SESSION["gebruikersnaam"]) . "</h1>";
        }else{
            echo "<h1 class='display-4 text-center'>Welkom bij Eenmaal Andermaal</h1>";
        }
        ?>
        <h2 class="display-5 text-center">De veilingsite voor iedereen!</h2>
        </section>
    </div>
    <div class="m-2 w-50">
        <form action="resultatenpagina.php" method="get">
            <div class= "d-flex justify-content-center h-75 w-100">
            <input class="zoektypen m-0 h-100 mr-5 border-dark border-bottom py-2 px-3" type="text" name="zoek_veld" placeholder="Naar product(en) zoeken...">
            <select data-live-search='true' title="Selecteer rubriek(en) ..." class="selectpicker zoektypen mt-auto border-dark border-bottom" name="cat_id">
            <?php
            makeCategoryTree($conn);
            ?>
            </select>
        </div>
    <input class="btn mt-3" type="submit" value="Naar product(en) zoeken">
    </form>
    </div>
</div>
<section class="align-items-center d-flex w-100 my-2 mt-4 flex-column reg-bied-win-img">
<img src="img/index_reg_bied_win.png" alt="registreer_bied_win" class="w-50 m-0"/>
</section>
<div class="page-container">

<section>
    <h5 class="p-2 mb-0 mt-2 text-center">Verloopt binnenkort</h5>
    <div class="d-flex flex-row mx-5 mb-5 justify-content-center item-showcase flex-wrap">
        <?php
        require_once "SQLsrvConnect.php";
        createThreeOldestItems($conn);
        ?>
    </div>
</section>

<section class="mb-5 ">
    <h5 class="ml-5 text-center">Andere producten</h5>
    <div class="d-flex flex-row dragscroll mx-5  item-showcase" >
        <?php
        require_once "SQLsrvConnect.php";
        createRandomItems($conn);
        ?>
    </div>
</section>
</div>
<?php
include 'footer.php';
include 'scripts.html'
?>
</body>
</html>