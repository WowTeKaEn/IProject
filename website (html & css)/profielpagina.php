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
<title>Eenmaal Andermaal | Gebruiksersinstellingen</title>

</head>
<body>

<?php
include 'nav.php';
include_once "functions.php";
include_once 'functions/functions_profiel_pagina.php';
echo createBreadCrumbs("Profielpagina");

//Als een gebruiker niet ingelogd is kan hij niet op deze pagina zijn
if(!isset($_SESSION['loggedin'])){
    header('Location: index.php');
    exit;
}
if(isset($_SESSION['geblokkeerd'])){
    if(($_SESSION['geblokkeerd'] == true)){
        header('Location: index.php');
        exit;
    }
}
//Kijken of de gebruiker een verkoper is geworden.
//Zo ja dan een message tonen, zo nee dan niks doen
if(isset($_SESSION['briefVerkoper'])){
    if($_SESSION['briefVerkoper'] == true){
        makeModals("Success", "U bent nu een verkoper");
        $_SESSION['briefVerkoper'] = false;
    }
}
if(isset($_SESSION['brief'])){
    if($_SESSION['brief'] == true){
        $gebruikersnaam = $_SESSION['gebruikersnaam'];
        $sql = "SELECT gebruikersnaam, adresregel1, adresregel2, plaatsnaam, landnaam FROM dbo.Gebruiker
        WHERE gebruikersnaam = :gebruikersnaam";
        $query = $conn->prepare($sql);
        $query -> execute(array(
            ':gebruikersnaam' => $gebruikersnaam
        ));    
        $row = $query -> fetch();
        makeModals("Success", "Er is een brief verzonden naar het volgende adres:<br>
        $row[adresregel1]<br>
        $row[adresregel2]<br>
        $row[plaatsnaam]<br> 
        $row[landnaam]");
        $_SESSION['brief'] = false;
    }
}
//Gebruik de changePassword() functie als de 'newPass' form is ingevuld
if(isset($_POST['newPass'])){
    require_once 'SQLSrvConnect.php';
    changePassword($_SESSION["gebruikersnaam"], $_POST['oudwachtwoord'], $_POST['nieuwwachtwoord'], $_POST['nieuwwachtwoordopnieuw'], $conn);
}

//Gebruik de changeMailAddress functie als de 'newEmail' form is ingevuld
if(isset($_POST['newEmail'])){
    require_once 'SQLSrvConnect.php';
    changeMailAddress($_POST['nieuwemailadres'], $conn);
}

//Gebruik de deleteAccount functie als de 'deleteAccount' form is ingevuld
if(isset($_POST['deleteAccount'])){
    if(isset($_POST['confirm'])) {
        require_once 'SQLSrvConnect.php';
        deleteAccount($conn);
    }
    else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">Klik eerst op de checkbox als u uw account wil verwijderen!</div>";
    }
}

//Gebruik de changeLocation functie als de 'newLocation' form is ingevuld
if(isset($_POST['newLocation'])){
    require_once 'SQLSrvConnect.php';
    changeLocation($_POST['nieuweplaats'], $_POST['nieuwadres'], $_POST['nieuwadres2'], $_POST['postcode'], $_POST['land'], $conn);
}

//Gebruik de changeBank functie als de 'newBankDetails' form is ingevuld
if(isset($_POST['newBankDetails'])){
    require_once 'SQLSrvConnect.php';
    changeBank($_POST['banknaam'], $_POST['rekeningnummer'], $conn);
}
//Gebruik de changeCreditCard functie als de 'newCreditCard' form is ingevuld
if(isset($_POST['newCreditCard'])){
    require_once 'SQLSrvConnect.php';
    changeCreditCard($_POST['cardnummer'], $conn);
}
//Gebruik de changePhoneNr functie als de 'newPhoneNr' form is ingevuld
if(isset($_POST['newPhoneNr'])){
    require_once 'SQLSrvConnect.php';
    changePhoneNr($_POST['telefoonnummer'], $conn);
}
?>

<div class="page-container my-5 pt-3">
<div class="container">
<div class="row">
<div class="col mb-3">
<h1>Mijn gegevens</h1>
</div>
</div>
<div class="row">
<div class="col mb-3">
<div class="d-flex border p-3 mb-3">
<?php
require_once("SQLSrvConnect.php");
displayMijnGegevens($conn);
?>
</div>
</div>
</div>
<?php
if(isVerkoper($conn)){
    echo " <div class='row mb-3'>
    <a class='btn' href='eigenVeilingen.php'>Bekijk hier uw Veilingen</a>
    </div>";  
}
echo "</div>
</div>";
if(isAdmin($conn)){
    echo "<div class='page-container my-5 pt-3'>
    <div class='container'>
    <div class='row'>
    <div class='col mb-3 p-3 border'>
    <h3>Blokkeren gebruiker</h3>";
    
    // Blokkeer een gebruiker knop.
    // Vul een gebruikersnaam in om die gebruiker te blokkeren.
    echo "<form method='POST'>
    <div class='form-group'>
    <label>Gebruikersnaam</label>
    <input 
    name='gebruikersnaam' 
    class='form-control' 
    placeholder='gebruikersnaam' 
    type='text' 
    id='gebruikersnaam'
    required>
    </div>
    <input type='submit' class='btn btn-block mb-3' value='Blokkeren gebruiker' name='blokkerenG'>
    </form>
    </div>
    </div>
    </div>
    </div>";
    
    echo "<div class='page-container my-5 pt-3'>
    <div class='container'>
    <div class='row'>
    <div class='col mb-3 p-3 border'>
    <h3>Blokkeren veiling</h3>";
    
    // Blokkeer een veiling knop.
    // Vul een titel van een veiling in om die veiling te blokkeren.
    echo "<form method='POST'>
    <div class='form-group'>
    <label>Titel</label>
    <input 
    name='titel' 
    class='form-control' 
    placeholder='titel' 
    type='text' 
    id='titel'
    required>
    </div>
    <input type='submit' class='btn btn-block mb-3' value='Blokkeren veiling' name='blokkerenV'>
    </form>
    </div>
    </div>
    </div>
    </div>";
}
?>
<?php
require_once "SQLsrvConnect.php";
if(isVerkoper($conn))
printMijnVeilingen();
?>

<div class="page-container my-5 pt-3">
    <div class="container">
        <div class="row">
            <div class="col mb-3">
                <h1>Gewonnen Veilingen</h1>
            </div>
        </div>
            <div class="row">
                <div class="col mb-3">
                    <div class="d-flex justify-content-center border p-3 mb-3">
                    <?php
                    gewonnenVeilingen($conn);
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="page-container my-5 pt-3">
<div class="container">
<div class="row">
<div class="col mb-3">
<h1>Instellingen</h1>
</div>
</div>
<div class="row">
<div class="col mb-3">
<h2>Accountinstellingen</h2>
</div>
</div>
<div  class="row">
<div class="col mb-3 p-3 border">
<h3>Wachtwoord veranderen</h3>
<form action="profielpagina.php" method="post">
<div class="form-group">
<label class="control-label" for="oudwachtwoord">Uw huidige wachtwoord</label>
<input type="password" name="oudwachtwoord" class="form-control" placeholder="Huidig wachtwoord" required>
</div>
<div class="form-group">
<label class="control-label" for="nieuwwachtwoord">Uw nieuwe wachtwoord</label>
<input type="password" name="nieuwwachtwoord" class="form-control" placeholder="Nieuw wachtwoord" pattern="(?=.*[a-z])(?=.*[A-Z]).{7,}" required>
<label class="control-label" for="nieuwwachtwoordopnieuw">Uw nieuwe wachtwoord opnieuw</label>
<input type="password" name="nieuwwachtwoordopnieuw" class="form-control" placeholder="Nieuw wachtwoord opnieuw" pattern="(?=.*[a-z])(?=.*[A-Z]).{7,}" required>
</div>
<input type="submit" value="Wachtwoord veranderen" class="btn" name="newPass">
</form>
</div>
</div>
<div class="row">
<div class="col mb-3 p-3 border">
<h3>E-mailadres veranderen</h3>
<form action="" method="post">
<div class="form-group">
<label class="control-label" for="nieuwemailadres">Uw nieuwe e-mailadres</label>
<input type="email" name="nieuwemailadres" class="form-control" placeholder="Nieuw e-mailadres" required>
</div>
<input type="submit" value="Nieuw e-mailadres opgeven" class="btn" name="newEmail">
</form>
</div>
</div>
<?php
if(!isVerkoper($conn)){
    echo "
    <div class='row'>
    <div class='col mb-3 p-3 border'>
    <h3>Verkoper worden</h3>
    <a class='float-left p-2 btn btn-block mb-3' href='verkoopAccount.php'>Vul het formulier in</a>
    <h5>Al een code?</h5>
    <form method='post'>
    <div class='form-group'>
    <label class='control-label' for='code'>Uw ontvangen code</label>
    <input type='text' name='code' class='form-control' placeholder='1A2bDc3' required>
    </div>
    <input type='submit' value='Verkoper worden' class='p-2 btn btn-block' name='codes'>
    </form>
    </div>
    </div>";
}
?>
<div class="row">
<div class="col mb-3 p-3 border">
<h3>Account verwijderen</h3>
<form action="" method="post">
<label for="deleteaccount">U kunt uw account verwijderen door op onderstaande knop te drukken:</label><br>
<input type="checkbox" name="confirm" required value="Confirm">
<label for="confirm">Ik weet zeker dat ik mijn account wil verwijderen.</label>
<input type="submit" value="Mijn account verwijderen" class="btn" name="deleteAccount">
</form>
</div>
</div>
<div class="row">
<div class="col mb-3">
<h2>Persoonsinformatie</h2>
</div>
</div>
<div class="row">
<div class="col mb-3 p-3 border">
<h3>Locatie veranderen</h3>
<form action="" method="post">
<div class="form-group">
<label class="control-label" for="plaats">Uw woonplaats</label>
<input type="text" name="nieuweplaats" class="form-control" placeholder="Plaats" required>
<label class="control-label" for="adres">Uw adres</label>
<input type="text" name="nieuwadres" class="form-control" placeholder="Adres" required>
<label for="adres2">Uw tweede adres:</label>
<input type="text" name="nieuwadres2" class="form-control" placeholder="Tweede adres">
<label class="control-label" for="postcode">Uw postcode</label>
<input type="text" name="postcode" class="form-control" placeholder="Postcode" required>
<label class="control-label" for="land">Uw land</label>
<select name="land" class="form-control">
<?php
require_once 'SQLsrvConnect.php';
foreach(getLanden($conn) as $land){
    echo "<option value=\"$land[0]\">$land[0]</option>";
}
?>
</select>
</div>
<input type="submit" value="Locatiegegevens veranderen" class="btn" name="newLocation">
</form>
</div>
</div>
<div class="row">
<div class="col mb-3 p-3 border">
<h3>Telefoonnummer veranderen</h3>
<form action="" method="post">
<div class="form-group">
<label class="control-label" for="telefoonnummer">Uw telefoonnummer</label>
<input type="text" name="telefoonnummer" class="form-control" placeholder="Telefoonnummer" pattern="(?=.*\d).{10}" required>
<small id="telefoonnummerHelpBlock" class="form-text text-muted">
Het telefoonnummer moet bestaan uit: 10 cijfers.
</small>
</div>
<input type="submit" value="Telefoonnummer veranderen" class="btn" name="newPhoneNr">
</form>
</div>
</div>
<?php
require_once 'SQLsrvConnect.php';
printVerkoperSettings($conn);
?>
</div>
</div>

<?php
include 'footer.php';
include 'scripts.html';
?>

</body>


</html>
