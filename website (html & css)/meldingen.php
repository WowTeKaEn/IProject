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
<title>Eenmaal Andermaal | meldingen</title>
</head>
<body>
<?php
include 'nav.php';
include_once 'functions.php';
echo createBreadCrumbs("Meldingen");
if(!isset($_SESSION['loggedin'])){
    header('Location: login.php');
    exit;
}
if(isset($_SESSION['geblokkeerd'])){
    if(($_SESSION['geblokkeerd'] == true)){
        header('Location: index.php');
        exit;
    }
}
echo "<main class='page-container flex-fill my-5'>
<h1>Meldingen</h1>";
$title = "Welkom!";
$description = "<h4>Goedendag $_SESSION[gebruikersnaam]</h4>
<p>Bij deze heten wij u welkom op onze site.</p>
<p>Mocht u vragen of opmerkingen hebben, laat het ons dan zeker weten.</p>
<br>
<p>Met vriendelijke groet,</p>
<br>
<p>het Eenmaal Andermaal team</p>";

echo "<article class='alert alert-success alert-dismissible' role='alert'>
<h2 class='alert-heading'>$title</h2>
<p>$description</p>
<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
<span aria-hidden='true'>&times;</span>
</article>";
include_once 'functions/functions_veilingMails.php';
echo "</main>";
include 'footer.php';
include 'scripts.html';
?>
</body>
</html>