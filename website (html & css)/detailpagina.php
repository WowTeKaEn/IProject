<!DOCTYPE html>
<html lang="nl">
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
    <?php 
        include_once 'functions.php';
        include_once 'functions/functions_product_page.php';
        echo "<title>Eenmaal Andermaal | ".GiveProductName($conn)."</title>";
    ?>

</head>
<body>
<?php
include_once 'functions.php';
include_once 'functions/functions_product_page.php';
include 'nav.php';

if(isset($_POST['feedback']) && $_POST['feedback'] === 'Versturen'){
    processFeedback($conn); 
}

if(isset($_POST['bieden'])){
    require_once "SQLsrvConnect.php";
    plaatsBod($_GET['pro_id'], $_POST['bod'], $conn);
}
echo createBreadcrumbs();
?>


<div class="page-container my-5">
    <div class="container mt-3">
        <div class="row">
            <div class="col-xl-8 mb-4">
                <div class="card navbar-light mb-4 h-100">
                    <div class="card-body">
                        <?php
                        require_once "SQLsrvConnect.php";
                        printAdvertentie($_GET['pro_id'], $conn);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 mb-4">
                <div class="card navbar-light mb-4">
                    <div class="card-body">
                        <h3 class="card-title mb-3">Informatie verkoper</h3>
                        <?php
                        require_once "SQLsrvConnect.php";
                        printVerkoperInformatieProduct($_GET['pro_id'], $conn);
                        ?>
                    </div>
                </div>
                <?php
                printBiedingen($_GET['pro_id'],$conn)
                ?>
                </div>
            </div>
        </div>
        <?php
        createFeedback($conn);
        createFeedbackComments($conn);
        ?>
        <div class="row mt-3">
            <div class="col text-center">
                <h2>Bekijk ook:</h2>
            </div>
        </div>
        <div class="container item-showcase mb-4">
            <div class="row">
                  <?php
                  require_once "SQLsrvConnect.php";
                    geefGerelateerdeProducten($_GET['pro_id'], $conn);
                    ?>
                </div>
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