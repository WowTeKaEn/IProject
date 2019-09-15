<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | veilingen</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
include_once 'functions/functions_eigenVeilingen.php';
include_once 'functions/functions_profiel_pagina.php';

if(!isVerkoper($conn)){
    header('location: index.php');
}

if(isset($_GET['pro_id']) && isset($_GET['delete']) && !$_GET['delete']){
  AuctionCheck("deleteVeiling.php?pro_id=".$_GET['pro_id'], "Weet u zeker dat u de veiling wilt verwijderen?");
}

if(isset($_POST['ended']) && isset($_POST['ended']) && $_POST['ended']) {
  makeModals("Veiling beëindigd","Uw veiling is beëindigd");
}

if(isset($_GET['pro_id']) && isset($_GET['end']) && $_GET['end']){
  endAuction($conn);
}

if(isset($_POST['product']) && isset($_POST['end'])){
  if(!$_POST['end']){
    AuctionCheck("eigenVeilingen.php?pro_id=".$_POST['product']."&end=1", "Weet u zeker dat u de veiling wilt beëindigen?");
  }
}
  echo createBreadCrumbs("Eigen veilingen");
  echo "<div class='artikelen justify-content-between flex-fill'>";
  createOwnItems($conn);
  echo "</div>"
?>



<script>
function endAuction(id) {
  document.getElementById(id).submit();
}
</script>

<?php
include 'footer.php';
include 'scripts.html';
?>
</body>
</html>