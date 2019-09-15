    
<?php
include_once 'functions.php';
?>
<div class="d-flex flex-column">
  <nav class="navbar navbar-expand-lg navbar-light">
    <a href="index.php">
    <img class="img-fluid mb-1 mr-3" src="img/Logo.png" id="Logo" alt="EenmaalAndermaal">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <div class="navbar-nav mr-auto">
        <form action="resultatenpagina.php" method="get" class="d-flex flex-row ">
        <ul class="navbar-nav mr-auto d-flex flex-fill">
          <li class="nav-item active flex-fill mr-2 mt-1">
            <input class="form-control" type="text" name="zoek_veld" placeholder="Zoekopdracht..." aria-label="Zoeken">

          </li>
          <li class="d-flex flex-row">
            <div class="nav-item mr-3 mt-1 d-flex flex-row position-absolute">
              <select class='selectpicker mr-3 nav-select' name="cat_id" title='Hoofdrubriek...' data-live-search='true'>
              <?php
              makeCategoryTree($conn);
              ?>
              </select>
              <button type="submit" class="h-100 w-25 btn">Zoek</button>
            </div>
          </li>
        </ul>
      </form>
      </div>
      <div class="mt-5 mt-sm-0 ">
        <div class="m-0 mt-lg-0 d-flex flex-row justify-content-end align-items-start btn-group">
        <?php
        if(isset($_SESSION['gebruikersnaam'])) {
          if($_SESSION['loggedin'] == true) {
            echo "<span class='badge alert-danger' style='margin-left:-18px;'>New</span>
            <a class='mw-50 btn mr-1' href='meldingen.php'>Meldingen</a>
            <a style='height:38px !important;' class='mw-50 text-center btn mx-1 text-nowrap' href='profielpagina.php'>Mijn profiel</a>
            <a class='mw-50 btn ml-1' href='uitloggen.php'>Log uit</a>";
          }else {
            echo "<a class='btn' href='login.php'>Log in</a>";
          } 
        }
        else {
          echo "<a class='btn' href='login.php'>Log in</a>";
        }
        if(isset($_SESSION['loggedin'])){
          echo "</div><a href='createAuction.php' class='btn mt-1'>Start veiling</a>";
        }else{
          echo "</div><a href='login.php?HasToLogin=true' class='btn mt-1' >Start veiling</a>";
        }
        ?>
      </div>
    </div>
  </nav>
</div>