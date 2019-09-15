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
<title>Eenmaal Andermaal | Wachtwoord Vergeten</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
include_once 'functions/functions_mail.php';
echo createBreadCrumbs("Wachtwoord vergeten");
?>   
<div class="container">
    <div id="login-row" class="row justify-content-center align-items-center">
        <div id="login-column" class="col-md-6">
            <div id="login-box" class="col-md-12 p-3">
                <div class="card">
                    <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Resetten wachtwoord</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label>E-mail</label>
                                <input
                                name="emailadres" 
                                class="form-control" 
                                placeholder="e-mailadres" 
                                type="email" 
                                id="emailadres"
                                required>
                            </div>
                            <input type="submit" class="btn btn-block" value="Versturen" name="versturen">
                        </form>
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