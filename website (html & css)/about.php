<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | about</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
  if(empty($_GET['cat_id'])){
    $_GET['cat_id'] = -1;
  }
  echo createBreadCrumbs();
?>
<div class="voorwaardenPage mt-5 pt-5">
<div class="voorwaardenOutside mt-5">
    <div class="voorwaardenInside">
        <h1>Over EenmaalAndermaal</h1>
        <p>EenmaalAndermaal, opgericht in 2019, was in Nederland één van de eerste websites die zich volledig richtte op rubrieksadvertenties. Sinds de oprichting is de onderneming uitgegroeid tot de populairste website op dit gebied in Nederland met een nog altijd groeiende klantenbasis van particulieren en kleine en grote bedrijven. Elke dag bezoeken ruim 5 mensen EenmaalAndermaal. Samen kopen en verkopen zij een zeer divers aanbod aan nieuwe en gebruikte producten en diensten. </p>
        <p>Per dag worden gemiddeld 2 nieuwe advertenties op de website geplaatst, van kleding en verzamelobjecten tot auto’s en huisraad. Op elk willekeurig moment bevat EenmaalAndermaal ruim 15 advertenties. Een paar voorbeelden van opmerkelijke advertenties die op EenmaalAndermaal hebben gestaan zijn: een tafeltje uit het Paleis Soestdijk, het tijdschrift Nieuwe Revu, een draagvleugelboot van Connexxion, de Bassie en Adriaan-auto met caravan en gratis Robin, een replica van de trouwjurk van Maxima en een privévliegtuig uit 1980.</p>
    </div>
  </div>
</div>
<?php
include 'footer.php';
include 'scripts.html';
?>
</body>
</html>