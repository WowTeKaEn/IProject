<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<script src="https://www.google.com/recaptcha/api.js?render=6LffGKYUAAAAAEm_QQCxyNcUX2UfpouGrpkjhHDG"></script>

<title>Eenmaal Andermaal | registreren</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
require_once 'functions/functions_registreren.php';
if(isset($_SESSION['loggedin'])){
    header('Location: index.php');
}
echo createBreadCrumbs("Registreren");
?> 
<div class="registerPage">
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12 p-3">
                    <div class="card">
                        <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Registreren</h4>

                        <form method="POST">
                            <div class="form-group">
                                <label class="control-label">E-mailadres</label>
                                <input
                                name="emailadres" 
                                class="form-control" 
                                placeholder="e-mailadres" 
                                type="email" 
                                id="emailadres"
                                value="<?= isset($_POST['emailadres']) ? $_POST['emailadres'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Gebruikersnaam</label>
                                <input
                                name="gebruikersnaam" 
                                class="form-control" 
                                placeholder="gebruikersnaam" 
                                type="text" 
                                id="gebruikersnaam"
                                value="<?= isset($_POST['gebruikersnaam']) ? $_POST['gebruikersnaam'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Wachtwoord</label>
                                <input
                                name="wachtwoord"
                                class="form-control" 
                                placeholder="******" 
                                type="password"
                                id="wachtwoord"
                                value="<?= isset($_POST['wachtwoord']) ? $_POST['wachtwoord'] : ''; ?>"
                                pattern="(?=.*[a-z])(?=.*[A-Z]).{7,}"
                                required
                                title="Het wachtwoord moet minimaal bestaan uit:
                                7 karakters, 1 kleine letter en 1 hoofdletter.">
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                Het wachtwoord moet minimaal bestaan uit:
                                7 karakters, 1 kleine letter en 1 hoofdletter.
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Wachtwoord herhalen</label>
                                <input
                                name="wachtwoord_herhalen"
                                class="form-control" 
                                placeholder="******" 
                                type="password"
                                id="wachtwoord_herhalen"
                                value="<?= isset($_POST['wachtwoord_herhalen']) ? $_POST['wachtwoord_herhalen'] : ''; ?>"
                                pattern="(?=.*[a-z])(?=.*[A-Z]).{7,}"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Voornaam</label>
                                <input
                                name="voornaam" 
                                class="form-control" 
                                placeholder="voornaam" 
                                type="text" 
                                id="voornaam"
                                value="<?= isset($_POST['voornaam']) ? $_POST['voornaam'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Achternaam</label>
                                <input
                                name="achternaam" 
                                class="form-control" 
                                placeholder="achternaam" 
                                type="text" 
                                id="achternaam"
                                value="<?= isset($_POST['achternaam']) ? $_POST['achternaam'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Adres</label>
                                <input
                                name="adres1"
                                class="form-control" 
                                placeholder="5748 Guacamole Boulevard" 
                                type="text"
                                id="adres1"
                                value="<?= isset($_POST['adres1']) ? $_POST['adres1'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label>Adres 2</label>
                                <input
                                name="adres2"
                                class="form-control" 
                                placeholder="Rio Grande District" 
                                type="text"
                                id="adres2"
                                value="<?= isset($_POST['adres2']) ? $_POST['adres2'] : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label class="control-label">Postcode</label>
                                <input
                                name="postcode"
                                class="form-control" 
                                placeholder="1234AB" 
                                type="text"
                                id="postcode"
                                value="<?= isset($_POST['postcode']) ? $_POST['postcode'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Plaats</label>
                                <input
                                name="plaats"
                                class="form-control" 
                                placeholder="Ede" 
                                type="text"
                                id="plaats"
                                value="<?= isset($_POST['plaats']) ? $_POST['plaats'] : ''; ?>"
                                required>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Land</label>
                                <select name="land" class="form-control">
                                <?php
                                require_once 'SQLsrvConnect.php';
                                foreach(getLanden($conn) as $land){
                                    if($land[0] == "Nederland"){
                                        echo "<option value=\"$land[0]\" selected>$land[0]</option>";
                                    }
                                    else{
                                        echo "<option value=\"$land[0]\">$land[0]</option>"; 
                                    }
                                }
                                ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Telefoonnummer</label>
                                <input
                                name="telefoonnummer"
                                class="form-control" 
                                placeholder="06-12345678" 
                                type="tel"
                                id="telefoonnummer"
                                value="<?= isset($_POST['telefoonnummer']) ? $_POST['telefoonnummer'] : ''; ?>"
                                pattern="(?=.*\d).{10}"
                                required>
                                <small id="telefoonnummerHelpBlock" class="form-text text-muted">
                                Het telefoonnummer moet bestaan uit: 10 cijfers.
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Geboortedatum</label>
                                <input
                                name="geboortedatum"
                                class="form-control"
                                type="date" MIN="1890-01-01"
                                id="geboortedatum"
                                value="<?= isset($_POST['geboortedatum']) ? $_POST['geboortedatum'] : ''; ?>"
                                required>
                                </div>

                                <div class="form-group">
                                <input type="submit" class="btn btn-block" value="Registreren" name="registreren">
                            </div>   

                        </form>
                        </article>
                    </div>
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