<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | contact</title>
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
<div class="contactPage">
<div class="contactOutside">
    <div class="voorwaardenInside">
      <h1>Contact</h1>
        <ul class="contactPagina">
            <li>Telefoon: 0628934798</li>
            <li>Mail: ReversiveConcepts@gmail.com</li>
            <li>Adress: Ruitenberglaan 31, 6826 CC Arnhem</li>
        </ul>
          <div class="mapouter"><div class="gmap_canvas ml-5"><iframe width="280" height="200" id="gmap_canvas" src="https://maps.google.com/maps?q=han%20university%20of%20applied%20sciences&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>Google Maps Generator by <a href="https://www.embedgooglemap.net">embedgooglemap.net</a></div><style>.mapouter{position:relative;text-align:right;height:200px;width:280px;}.gmap_canvas {overflow:hidden;background:none!important;height:200px;width:280px;}</style>
        </div>
    </div>
  </div>
</div>
<?php
include 'footer.php';
include 'scripts.html';
?>
</body>
</html>