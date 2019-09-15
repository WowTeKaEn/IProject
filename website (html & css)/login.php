<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal | login</title>
</head>
<body>
<?php
include 'functions.php';
if(isset($_SESSION['loggedin'])){
  header('Location: index.php');
}
include 'nav.php';
include 'functions/functions_login.php';

if(isset($_GET['HasToLogin']) && $_GET['HasToLogin']){
  makeModals("Login","U moet eerst inloggen als u een veiling aan wilt maken");
}

echo createBreadCrumbs("Inloggen");
?>
<div class="loginPage flex-fill flex-grow m-0">
  <div class="container display-flex">
    <div id="login-row" class="display-flex row justify-content-center align-items-center">
      <div id="login-column" class="col-md-6">
        <div id="login-box" class="col-md-12 p-3">
          <div class="card">
            <article class="card-body">
              <h4 class="card-title mb-4 mt-1">Inloggen</h4>
              <form method="POST">
                <div class="form-group">
                  <label>Gebruikersnaam</label>
                  <input 
                  name="gebruikersnaam" 
                  class="form-control" 
                  placeholder="gebruikersnaam" 
                  type="text" 
                  id="gebruikersnaam"
                  required>
                </div>

                <div class="form-group">
                  <label>Wachtwoord</label>
                  <input
                  name="wachtwoord"
                  class="form-control" 
                  placeholder="******" 
                  type="password"
                  id="wachtwoord"
                  required>
                </div>
                <a class="float-left p-2" href="wachtwoordVergeten.php" name="vergeten">Wachtwoord vergeten?</a>
                <div class="form-group">
                  <input type="submit" class="btn btn-block" value="Inloggen" name="inloggen">
                  <p class="mt-3 ml-1">Nog geen account? Registreer je dan nu!</p>
                  <a href="registreren.php" class="float-left btn btn-block">Registreren</a>
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
include 'scripts.html';
?>
</body>