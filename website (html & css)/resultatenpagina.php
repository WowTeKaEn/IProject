<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="icon" type="image/png" href="img/favicon.png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<link rel="stylesheet" href="styles/bootstrap-slider.min.css" />
<title>Eenmaal Andermaal | Resultaten</title>
</head>
<body>
<?php
include 'nav.php';
require_once 'functions.php';
require_once 'functions/functions_products_page.php';
  if(empty($_GET['cat_id'])){
    $_GET['cat_id'] = -1;
  }
  if(empty($_GET['page'])){
    $_GET['page'] = 1;
  }
  echo createBreadCrumbs();
?>
<div>
<div class='resultatenpagina display-flex flex-row'>
<div class="w-25 filter-holder" style="min-width: 200px;">
<div class="specificeren mb-3 p-3 d-flex flex-column">
    <div>
      <form method="GET">
        <div class="filter active">
          <img src="/img/chevron-top.png" class="filter-hide-img" alt="pijltje naar boven">
          <img src="/img/chevron-bottom.png" class="filter-show-img" alt="pijltje naar beneden">
          <h2>Zoeken</h2>
          <input type="text" name="zoek_veld" id="zoek_veld_filter" class="form-control w-100" placeholder="Zoeken in subcategorie..." value="<?php if(isset($_GET['zoek_veld'])){echo $_GET['zoek_veld'];} ?>"/>
          <div class="d-flex flex-row">
          <input type="submit" class="btn mt-2 mb-4 w-50 mr-1" name="submit" value="Zoeken"/>
          <button onClick="document.getElementById('zoek_veld_filter').value = ''; return false;" class="btn w-50 mt-2 mb-4 ml-1">Zoekveld leegmaken</button></div>
        </div>
        <div id="price_slider_holder" class="filter active" style="display:none;">
          <img src="/img/chevron-top.png" class="filter-hide-img" alt="pijltje naar boven">
          <img src="/img/chevron-bottom.png" class="filter-show-img" alt="pijltje naar beneden">
          <h2>Prijs filter</h2>
          <b id="b_min_prijs"></b>
          <b id="b_max_prijs" class="float-right"></b>
          <input type="text" name="price_slider" class="span2" value="" data-slider-min="0" data-slider-max="10000" data-slider-step="1" data-slider-value="[250,750]" id="price_slider"/>
          <div class="d-flex flex-row">
            <input type="text" name="min_prijs_text" class="form-control w-50 float-left mt-2 mr-1" value="€ 0" disabled>
            <input type="text" name="max_prijs_text" class="form-control w-50 float-right mt-2 ml-1 text-right" value="€ 0" disabled>
          </div>
          <div class="d-flex flex-row">
            <input type="submit" class="btn mt-2 mb-4 w-50 mr-1" name="submit" value="Filteren op prijs"/>
            <button onClick="document.getElementById('price_slider').value = '';" class="btn w-50 mt-2 mb-4 ml-1">Filter resetten</button>
          </div>
        </div>
        <input type="hidden" name="cat_id" value="<?php echo $_GET['cat_id']; ?>"/>
      </form>
    </div>
    <div class="extraZoeken display-flex flex-column filter active">
          <img src="/img/chevron-top.png" class="filter-hide-img" alt="pijltje naar boven">
          <img src="/img/chevron-bottom.png" class="filter-show-img" alt="pijltje naar beneden">
        <?php printSubCategories(getCategoryFromID($_GET['cat_id'],$conn)); ?>
    </div>
</div>
</div>
  <div class='flex-grow flex-fill w-75 resize'>
    <div class="artikelen justify-content-between">
      <?php
      if($_GET['cat_id'] == -1 || $_GET['cat_id'] == ""){
        getAllProducts($_GET['page'], $conn);
      }else{
        getProducts($_GET['cat_id'], $_GET['page'], $conn);
      }
      ?>
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
