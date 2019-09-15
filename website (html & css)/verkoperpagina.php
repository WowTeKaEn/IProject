<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:500,100,300,700,400">
    <link rel="stylesheet" href="styles/stylesheet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
    <title>Eenmaal Andermaal</title>
</head>
<body>
<?php
if(!isset($_GET['verkoper'])){
    header("location: index.php");
}
include_once 'functions.php';
include_once 'functions/functions_product_page.php';
include 'nav.php';
if(isset($_GET['verkoper'])){
    if($_GET['verkoper'] != ""){
        echo createBreadcrumbs("Veilingen van verkoper : <i>". $_GET['verkoper']. "</i>");
    }
}else {
    echo createBreadcrumbs("Veilingen van verkoper");
}

?>

<div class="page-container flex-fill my-5">
    <div class="mt-3 mx-5">
        <div class="row">
        <div class="col-xl-4 mb-4 mt-5">
                <div class="card navbar-light mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-3">Informatie verkoper</h3>
                    <?php
                        printVerkoperInformatie($conn);
                    ?>
                    </div></div>
                    <?php
                        createFeedbackComments($conn);
                    ?>
                    </div>
                    <div class="col mb-4">
                        <h2 class='text-center'>Veilingen van deze verkoper</h2>
                        <div class="artikelen justify-content-between item-showcase">
                        <?php
                            printVeilingenVerkoper($conn);
                        ?>
                        </div>
              </div></div></div></div>


<?php
include 'footer.php';
include 'scripts.html';
?>
</body>
</html>