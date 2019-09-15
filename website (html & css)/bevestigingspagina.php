<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | Bevestigen</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
require_once 'functions/functions_registreren.php';
if(isset($_SESSION['loggedin'])){
    header('Location: index.php');
}
?>
<div class="container">
    <div id="login-row" class="row justify-content-center align-items-center">
        <div id="login-column" class="col-md-6">
            <div id="login-box" class="col-md-12 p-3">
                <div class="card">
                    <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Bevestigingspagina</h4>
                    <?php
                    echo "<p>Uw registratie is gelukt! U heeft een mail ontvangen op het volgende e-mailadres:<p>
                    <br>$_SESSION[emailadres]<br>
                    <p>Volg de instructies in de gekregen mail om uw account te verifiëren.<br></p>
                    <p>Het Eenmaal Andermaal team wenst u nog een prettige dag.</p>";
                    ?>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'footer.php';
include 'scripts.html'
?>
</body>
</html>