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
<title>Eenmaal Andermaal | Nieuw Wachtwoord</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
?>   
<div class="container">
    <div id="login-row" class="row justify-content-center align-items-center">
        <div id="login-column" class="col-md-6">
            <div id="login-box" class="col-md-12 p-3">
                <div class="card">
                    <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Nieuw wachtwoord</h4>
                        <form method="POST" action="nieuwWachtwoord.php">
                            <div class="form-group">
                                <?php
                                echo "<label>Gebruikersnaam</label>
                                <input 
                                name='gebruikersnaam' 
                                class='form-control' 
                                value=$_SESSION[naam]
                                type='text' 
                                id='gebruikersnaam'
                                required
                                readonly>
                                </div>";
                            ?>

                            <div class="form-group">
                                <label>Nieuw wachtwoord</label>
                                <input
                                name="wachtwoord"
                                class="form-control" 
                                placeholder="******" 
                                type="password"
                                id="wachtwoord"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,}"
                                required>
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                Het wachtwoord moet minimaal bestaan uit:
                                7 karakters, 1 cijfer, 1 kleine letter en 1 hoofdletter.
                                </small>
                            </div> 

                            <div class="form-group">
                                <label>Wachtwoord herhalen</label>
                                <input
                                name="wachtwoord_herhalen"
                                class="form-control" 
                                placeholder="******" 
                                type="password"
                                id="wachtwoord_herhalen"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,}"
                                required>
                            </div>
                            <input type="submit" class="btn btn-block" value="Aanpassen" name="aanpassen">
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