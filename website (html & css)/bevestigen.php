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
        include_once 'functions.php';
        include 'nav.php';
    ?>
<div class="container">
  <div id="login-row" class="row justify-content-center align-items-center">
    <div id="login-column" class="col-md-6">
      <div id="login-box" class="col-md-12 p-3">
        <div class="card">
          <article class="card-body">
            <h4 class="card-title mb-4 mt-1">Registreren</h4>
            <?php
            if(isset($_POST['sturen'])){
              $email = $_POST['email'];
              echo "<h3>Er is een mail gestuurd naar: <br>" . ($_POST["email"]). "</h3>";
            }
            ?>
            <form method="POST" action="bevestigen.php">
            <div class="form-group">
              <label>E-mailadres</label>
              <input
              name="email" 
              class="form-control" 
              placeholder="email" 
              type="email" 
              id="email"
              required>
            </div>
            <div class="form-group">
            <input type="submit" class="btn btn-primary btn-block" value="Sturen" name="sturen">
            </div>
            </form>
            <div class="form-group">
              <form method="POST" action="registreren.php">
              <?php
              if(isset($_POST['sturen'])){
                $email = $_POST['email'];
                echo "
                    <div class='form-group'>
                    <label>E-mailadres</label>
                    <input
                    name='email' 
                    class='form-control' 
                    value='$email' 
                    type='email' 
                    id='email'
                    required
                    readonly>
                    </div>";
              }
              ?>
              <label>Code</label>
              <input
                name="code" 
                class="form-control" 
                placeholder="code" 
                type="text" 
                id="code"
                required>
            </div>
              <div class="form-group">
                <input type="submit" class="btn btn-primary btn-block" value="Bevestigen" name="bevestigen">
              </div>
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