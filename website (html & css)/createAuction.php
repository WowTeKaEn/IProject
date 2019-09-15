<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v4.7.0/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
  <script src="https://use.fontawesome.com/2f1f646602.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | creeer veiling</title>
</head>
<body >
<?php
 include 'nav.php';
  include 'functions/functions_createAuction.php';
  include_once 'functions/functions_profiel_pagina.php';
  include_once 'functions.php';
  echo createBreadCrumbs("Veiling aanmaken");

  if(!isVerkoper($conn)){
    makeModals('Error', "Je moet eerst verkoper worden. Ga <a href='profielpagina.php'>hier</a> naar de profiel pagina om verkoper te worden.'");
  }
 
  if(isset($_POST['message'])){
    createAuctionMessages($_POST['message']);
  }
   if(!isset($_SESSION['fullTree'])){
    makeFullCategoryTree($conn);
    }
?>
<h1 class="mt-3 display-4 text-center">Maak hier uw eigen veiling aan!</h1>
<div class="w-100 h-100 m-auto d-flex" >
<article class="card-body   p-sm-3 p-0 mt-3 d-flex justify-content-center align-content-center">
<form method="post" action="upload.php" enctype="multipart/form-data" class="filling m-0 mw-100 card p-sm-4 px-3 py-4">
    <div class="row">
    <div class="col w-50">
      <input type="text" name="title" class="form-control" required placeholder="Titel">
      </div>
      <div  style='line-height: 1.15;' class="col w-50">
      
      <select style="width:auto; " data-width="100%" id="selectpicker" class='selectpicker fullCategory nav-select'name="cat_id" title="Rubriek..." required data-live-search-placeholder="Zoeken naar rubriek...." data-live-search='true'>
      <?php
      foreach($_SESSION['fullTree'] as $row){
      echo $row;
      }
      ?>
      </select>
      </div></div>
      <textarea class="form-control mt-3" name="discription" required placeholder="Omschrijving"></textArea>
      <div class="row">
        <div class="col">
        <input type="text" name="transportInstructions" class="form-control mt-3" placeholder="Verzend instructies">
        </div>
      <div class="col">
      <input type="text" pattern="[0-9]{1,8}(,[0-9]{2})?" maxlength="11" title="Maximaal 8 getallen voor de komma en twee achter de komma"  name="transportCosts" class="form-control mt-3" placeholder="Verzend kosten">
      </div>

      </div>
      <input type="text" name="paymentInstructions" class="form-control mt-3" placeholder="Betalings omschrijving">
      <input type="text" name="price" pattern="[0-9]{1,8}(,[0-9]{2})?" maxlength="11" title="Maximaal 8 getallen voor de komma en twee achter de komma" class="form-control mt-3" placeholder="Start prijs">
      <div class="row">
        <div class="col">
          <select class="w-100 selectpicker mt-3" title="Betalingswijze" name="paymentMethod">
            <option value="Bank">Bank</option>
            <option value="Contant">Contant</option>
          </select>
        </div>
      <div class="col">
      <select class="w-100 selectpicker mt-3" title="Conditie" name="condition">
        <option value="Nieuw">Nieuw</option>
        <option value="In nieuwstaat">In nieuwstaat</option>
        <option value="Tweede Hands">Tweede Hands</option>
        <option value="Beschadigd">Beschadigd</option>
        <option value="Kapot">Kapot</option>
      </select>
      </div></div>
      <input type="file" multiple class='my-3 mt-3' required name="files[]" >
      <div class="container">
        <div class='col-md-5'>
          <label for="datetimepicker7">Start datum en tijd</label>
            <div class="form-group">
              <div class="input-group date" style="min-width:200px !important;"  id="datetimepicker7" data-target-input="nearest">
              <input type="text" title="Datum" required  name="dateTimeStart" pattern="[0-3][0-9]\-[0-1][0-9]\-[0-2][0-9]{3}\ [0-5][0-9]\:[0-5][0-9]" maxlength="16" class="form-control datetimepicker-input" data-target="#datetimepicker7"/>
                    <div class="input-group-append" data-target="#datetimepicker7" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
        </div>
      <div class='col-md-5'>
      <label for="datetimepicker8">Eind datum en tijd</label>
          <div class="form-group">
            <div class="input-group date" style="min-width:200px !important;" id="datetimepicker8" data-target-input="nearest">
                  <input type="text" title="Datum"  required name="dateTimeEnd" pattern="[0-3][0-9]\-[0-1][0-9]\-[0-2][0-9]{3}\ [0-5][0-9]\:[0-5][0-9]" maxlength="16" class="form-control datetimepicker-input" data-target="#datetimepicker8"/>
                  <div class="input-group-append" data-target="#datetimepicker8" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
      <input type="submit" onclick="setloading()" class='btn' id="btnLoading" value="Upload Image" name="submit">
  </form>
</article>
</div>
<?php
  include 'footer.php';
  include 'scripts.html';
?>
</body>