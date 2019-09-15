<?php
function printBiedingen($pro_id, $conn){
  $query = "SELECT veiling_gesloten FROM Voorwerp WHERE voorwerpnummer = ?";
  $sql = $conn->prepare($query);
  $sql->execute(array($pro_id));
  $result = $sql->fetch();
  $closed = $result['veiling_gesloten'];

  $query = "SELECT bod_bedrag, voornaam, achternaam FROM Gebruiker LEFT JOIN Bod on Gebruiker.gebruikersnaam = Bod.gebruikersnaam WHERE Bod.voorwerpnummer = ?";
  $sql = $conn->prepare($query);
  $sql->execute(array($pro_id));
  $biedingen = $sql->fetchAll();


  if($closed){
    echo "<div class='card navbar-light'>
        <div class='card-body'>
      <div class='card bg-mindaro'>
          <div class='card-body'>
              <h4 class='card-title'>Biedingen:</h4>";
  }else{
    echo "<div class='card navbar-light'>
        <div class='card-body'>
      <h3 class='card-title'>Bieden</h3>
      <form action='' method='post'>
          <div class='form-group'>
              <input type='text' name='bod' title='Maximaal 8 getallen voor de komma en twee achter de komma' pattern='[0-9]{1,8}(,[0-9]{2})?' class='form-control' placeholder='Uw bod' required>
          </div>
          <input type='submit' value='Bieden' class='btn mb-3' name='bieden'>
      </form>

      <div class='card bg-mindaro'>
          <div class='card-body'>
              <h4 class='card-title'>Biedingen:</h4>";
  }
  

  if(!empty($biedingen)) {
    echo "<table class=\"mb-3\"style=\"width: 100%\";>";
    foreach ($biedingen as $bod) {
      echo "<tr>
        <td>$bod[voornaam]&nbsp;$bod[achternaam]</td>
        <td>&euro;$bod[bod_bedrag]</td>
        </tr>";
    }
    echo "</table>";
  } else {
    echo "Er is nog niet op deze advertentie geboden. Wees de eerste!";
  }
  echo "</div></div></div>";
}

function plaatsBod($pro_id, $bedrag, $conn){
  if(!empty($_SESSION['gebruikersnaam'])) {
    if (!empty($bedrag)) {
      $bedrag = str_replace(',','.', $bedrag);
      $naam = $_SESSION['gebruikersnaam'];
      $datum = date("Y-m-d");
      $tijd = date("H:i:s");
      $query = "INSERT INTO Bod VALUES(?, ?, ?, ?, ?)";
      $query2 = "SELECT bod_bedrag FROM Bod WHERE voorwerpnummer = ?";
      $sql2 = $conn->prepare($query2);
      $sql2->execute(array($pro_id));
      $biedingen = $sql2->fetchAll();
      $hoogsteBod = 0.0;
      $startprijs = 0.0;
      if(!empty($biedingen)){
        foreach($biedingen as $bod){
          if($bod[0] > $hoogsteBod){
            $hoogsteBod = $bod[0];
          }
        }
      } else {
        $query3 = "SELECT startprijs FROM Voorwerp WHERE voorwerpnummer = ?";
        $sql3 = $conn->prepare($query3);
        $sql3->execute(array($pro_id));
        $fetch = $sql3->fetchAll();
        $startprijs = $fetch[0];
      }
      if($bedrag >= $startprijs[0]) {
        if ($bedrag > ($hoogsteBod + ($hoogsteBod * 0.01))) {
          try {
            $sql = $conn->prepare($query);
            $sql->execute(array($pro_id, $bedrag, htmlspecialchars($naam), $datum, $tijd));
          } catch (PDOException $e) {
            echo $e->getMessage();
          }
        } else {
          echo "<div class=\"alert alert-danger\" role=\"alert\"> Uw bod moet minstens 1% hoger zijn dan een vorig bod! </div>";
        }
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\"> Uw bod moet hoger zijn dan de startprijs! </div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul een bedrag in! </div>";
    }
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Log in voordat u een bod maakt! </div>";
  }
}

//TODO href voor de buttons
function printVerkoperInformatieProduct($pro_id, $conn){
  $query = "SELECT voornaam, achternaam, Gebruiker.plaatsnaam, emailadres, telefoonnummer, Voorwerp.verkoper_gebruikersnaam, Gebruiker.gebruikersnaam FROM Gebruiker 
            LEFT JOIN Gebruikerstelefoon on Gebruiker.gebruikersnaam = Gebruikerstelefoon.gebruikersnaam 
            LEFT JOIN Voorwerp on Gebruiker.gebruikersnaam = Voorwerp.verkoper_gebruikersnaam 
            WHERE voorwerpnummer = ?";
  $sql = $conn->prepare($query);
  $sql->execute(array($pro_id));
  $fetch = $sql->fetchAll();
  $verkoper = $fetch[0];
  $queryRating = "SELECT AVG(rating) AS rating FROM Feedback WHERE verkoper = ? AND door_koper = 1";
  $sqlRating = $conn->prepare($queryRating);
  $sqlRating->execute(array($verkoper['verkoper_gebruikersnaam']));
  $fetchRating = $sqlRating->fetchAll();
  $ratingArray = $fetchRating[0];
  $rating = $ratingArray[0];

  if(empty($verkoper['plaatsnaam']))
    $verkoper['plaatsnaam'] = "[onbekend]";
  if(empty($verkoper['achternaam']))
    $verkoper['achternaam'] = "[onbekend]";
  if(empty($verkoper['emailadres']))
    $verkoper['emailadres'] = "[onbekend]";
  if(empty($verkoper['telefoonnummer']))
    $verkoper['telefoonnummer'] = "[onbekend]";

    echo"<table class=\"mb-3\"style='width: 100%;'>
    <tr>
    <td>Naam:</td>
    <td>$verkoper[voornaam] $verkoper[achternaam]</td>
    </tr>
    <tr>
    <td>Plaats:</td>
    <td>$verkoper[plaatsnaam]</td>
    </tr>
    <tr>
    <td>e-mail:</td>
    <td>$verkoper[emailadres]</td>
    </tr>
    <tr>
    <td>Telefoon:</td>
    <td>$verkoper[telefoonnummer]</td>
    </tr>
    <tr>
    <td>Rating:</td>
    <td>";

    if(!empty($rating)) {
      for ($i = 0; $i < $rating; $i++) {
        echo "<span class='fa fa-star checked' style='color:gold;'></span>";
      }
      if ($rating - (int)$rating != 0) {
        echo "<span class='fa fa-star-half-alt'></span>";
      }
      for ($i = 0; $i < 5 - $rating; $i++) {
        echo "<span class='fa fa-star'></span>";
      }
      echo "</td>
    </tr>
    </table>  ";
    } else {

      echo "(Geen rating)";
    }
    $verkoperNaam = $verkoper['gebruikersnaam'];
  echo "</td>
    </tr>
    </table>     
    <a href='verkoperpagina.php?verkoper=$verkoperNaam' class=\"btn mb-2\">Meer veilingen van deze verkoper</a><br>";
    }

function printFeedback($pro_id, $conn){
    $query = "SELECT feedback_soort_naam, datum, CONVERT(varchar(5), tijd) AS tijd, commentaar FROM Feedback WHERE voorwerpnummer = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($pro_id));
    $fetch = $sql->fetchAll();
    if(!empty($fetch)) {
        foreach ($fetch as $feedback) {
            echo "<p>Recensie gegeven op $feedback[datum] om $feedback[tijd]<br>
              Algemene indruk: $feedback[feedback_soort_naam]<br>
              Bericht:<br>$feedback[commentaar]</p>";
        }
    } else {
        echo "<p class='mb-3 mt-3'>Er zijn nog geen recensies voor deze verkoper</p>";
    }
}

function printAdvertentie($pro_id, $conn){
  $query = "SELECT titel, beschrijving, looptijd_start_datum, CONVERT(varchar(5), looptijd_start_tijd) AS looptijd_start_tijd, bod_bedrag, looptijd_einde_datum, looptijd_einde_tijd, startprijs, Voorwerp.source, conditie, verzendkosten, verzendinstructies, veiling_gesloten FROM Voorwerp 
            FULL JOIN Bod on Voorwerp.voorwerpnummer = Bod.voorwerpnummer
            WHERE Voorwerp.voorwerpnummer = ?";
  $sql = $conn->prepare($query);
  $sql->execute(array($pro_id));
  $fetch = $sql->fetchAll();
  $imgquery = "SELECT filenaam FROM Bestand WHERE Voorwerpnummer = ?";
  $imgsql = $conn->prepare($imgquery);
  $imgsql->execute(array($pro_id));
  $imageresults = $imgsql->fetchAll(PDO::FETCH_NUM);
  $images = $imageresults[0];

  

  $advertentie = $fetch[0];
  if($advertentie['veiling_gesloten']){
    $closed = true;
  }else{
    $closed = false;
  }
  $date = date_create($advertentie['looptijd_einde_datum'] ."  ". $advertentie['looptijd_einde_tijd']);
  $sec_diff = date_diff(date_create(),$date);
  $secs = 0;
  $secs += $sec_diff->format('%a') * 24 * 60 * 60;
  $secs += $sec_diff->format('%h') * 60 * 60;
  $secs += $sec_diff->format('%i') * 60;
  $secs += $sec_diff->format('%s');
  $secs = $secs * 1000;
  if(date_create() > $date){
      $secs -= $secs * 2;
  }
  if(!empty($advertentie['bod_bedrag'])) {
    $hoogsteBod = 0;
    foreach ($fetch as $bod) {
        if ($bod['bod_bedrag'] > $hoogsteBod) {
          $hoogsteBod = $bod['bod_bedrag'];
        }
    }
    $printBod = "Hoogste bod: &euro;$hoogsteBod";
  } else {
    $startprijs = $advertentie['startprijs'];
    if($startprijs == .00){
      $startprijs = 0.00;
    }
    $printBod = "Startprijs: &euro;$startprijs <br>Er is nog niet geboden!";
  }




    echo "
    <h1 class=\"card-title\">$advertentie[titel]</h1>
    <h3>$printBod</h3>
    Conditie: $advertentie[conditie]<br>";
    if(!empty($advertentie['verzendinstructies']))
      echo "Verzenden: $advertentie[verzendinstructies]<br>";
    if($advertentie['verzendkosten'] != 0)
      echo "Verzendkosten: &euro;$advertentie[verzendkosten]<br>";
    echo "Begonnen op $advertentie[looptijd_start_datum] om $advertentie[looptijd_start_tijd]<br>
    Eindigt op $advertentie[looptijd_einde_datum] om ". substr($advertentie['looptijd_einde_tijd'],0,5) ."</p>
    <p>Sluit over: <strong><span id='timer' data-time='". $secs ."'></span></strong></p>
    <div id=\"productImages\" class=\"carousel slide bg-taupe w-100 \" data-ride=\"carousel\">
    <div class=\"carousel-inner border p-carousel h-100\"'>
    <div class=\"d-flex\" style=\"height: 100%;\">
    <div class=\"carousel-item active\">
    <div class=\"d-flex justify-content-center align-items-center h-100\">";
    if($advertentie['source'] == 1) {
        echo "<img src=\"https://iproject26.icasites.nl/pics/$images[0]\" alt=\"$advertentie[titel]\" class=\"img-fluid \">";
    } else {
        echo "<img src=\"https://iproject26.icasites.nl/UploadedImages/$images[0]\" alt=\"$advertentie[titel]\" class=\"img-fluid\">";
    }
    echo "
    </div>
    </div>
    ";

    if(count($imageresults) > 1) {
      array_shift($imageresults);
      foreach ($imageresults as $img) {
        if($advertentie['source'] == 1) {
          echo "<div class=\"carousel-item\">
            <div class=\"d-flex justify-content-center align-items-center h-100\">
            <img src=\"https://iproject26.icasites.nl/pics/$img[0]\" alt=\"$advertentie[titel]\" class=\"img-fluid h-100\">
            </div>
            </div>";
        } else {
          echo "<div class=\"carousel-item\">
            <div class=\"d-flex justify-content-center h-100\">
            <img src=\"https://iproject26.icasites.nl/UploadedImages/$img[0]\" alt=\"$advertentie[titel]\" class=\"img-fluid mt-auto h-100\">
            </div>
            </div>";
        }
      }
    }
    echo"</div>
    </div>
    <a class=\"carousel-control-prev\" href=\"#productImages\" role=\"button\" data-slide=\"prev\">
    <span class=\"carousel-control-prev-icon\"></span>
    <span class=\"sr-only\">Vorige</span>
    </a>
    <a class=\"carousel-control-next\" href=\"#productImages\" role=\"button\" data-slide=\"next\">
    <span class=\"carousel-control-next-icon\"></span>
    <span class=\"sr-only\">Volgende</span>
    </a>
    </div>
    <h3 class=\"card-title\">Beschrijving</h3>
    <p>$advertentie[beschrijving]</p>";
}


function printVeilingenVerkoper($conn){
    $sql = "SELECT *, dbo.FN_GeefPrijs(voorwerpnummer) AS 'prijs'
              FROM Voorwerp 
              WHERE verkoper_gebruikersnaam = ? AND geblokkeerd = 0 AND veiling_gesloten = 0";
    $query = $conn->prepare($sql);
    $query -> execute(array($_GET['verkoper']));
    $row = $query -> fetchAll(PDO::FETCH_ASSOC);
    if($row) {
      foreach ($row as $product) {
        $pro_id = $product['voorwerpnummer'];
        $date = date_create($product['looptijd_einde_datum'] . "  " . $product['looptijd_einde_tijd']);
        $sec_diff = date_diff(date_create(), $date);
        $secs = 0;
        $secs += $sec_diff->format('%a') * 24 * 60 * 60;
        $secs += $sec_diff->format('%h') * 60 * 60;
        $secs += $sec_diff->format('%i') * 60;
        $secs += $sec_diff->format('%s');
        $secs = $secs * 1000;
        $item['pro_id'] = $pro_id;
        if($product['source'] == 1) {
          $item['img'] = 'https://iproject26.icasites.nl/thumbnails/' . $product['thumbnail'];
        } else {
          $item['img'] = 'https://iproject26.icasites.nl/UploadedImages/' . $product['thumbnail'];
        }
        $item['titel'] = $product['titel'];
        $item['startprijs'] = $product['startprijs'];
        $item['secs'] = $secs;
        createItemProper($product['voorwerpnummer'], $item['img'],$product['titel'],$product['prijs'],$secs,$product['veiling_gesloten']);
      }
}else{
  echo "<h1 class='text-center my-3 mx-auto'>Deze verkoper heeft geen actieve veilingen</h1>";
}
}


function printVerkoperInformatie($conn){

  $query = $conn->prepare("SELECT voornaam, achternaam, Gebruiker.plaatsnaam, emailadres, telefoonnummer,Gebruiker.gebruikersnaam FROM Gebruiker 
  LEFT JOIN Gebruikerstelefoon on Gebruiker.gebruikersnaam = Gebruikerstelefoon.gebruikersnaam WHERE Gebruiker.gebruikersnaam = :username");
  $query->execute(array(":username" => $_GET['verkoper']));
  
  $verkoper = $query->fetch();
  if(empty($verkoper['plaatsnaam']))
  $verkoper['plaatsnaam'] = "[onbekend]";
  if(empty($verkoper['achternaam']))
  $verkoper['achternaam'] = "[onbekend]";
  if(empty($verkoper['emailadres']))
  $verkoper['emailadres'] = "[onbekend]";
  if(empty($verkoper['telefoonnummer']))
  $verkoper['telefoonnummer'] = "[onbekend]";

  $queryRating = "SELECT AVG(rating) AS rating FROM Feedback WHERE verkoper = :verkoper_gebruikersnaam AND door_koper = 1";
  $sqlRating = $conn->prepare($queryRating);
  $sqlRating->execute(array(":verkoper_gebruikersnaam" => $_GET['verkoper']));
  $fetchRating = $sqlRating->fetch();
  $rating = $fetchRating['rating'];
  
  echo"<table class=\"mb-3\"style='width: 100%;'>
  <tr>
  <td>Naam:</td>
  <td>$verkoper[voornaam] $verkoper[achternaam]</td>
  </tr>
  <tr>
  <td>Plaats:</td>
  <td>$verkoper[plaatsnaam]</td>
  </tr>
  <tr>
  <td>e-mail:</td>
  <td>$verkoper[emailadres]</td>
  </tr>
  <tr>
  <td>Telefoon:</td>
  <td>$verkoper[telefoonnummer]</td>
  </tr>
  <tr>
  <td>Rating:</td><td>";

  if(!empty($rating)) {
    for ($i = 0; $i < $rating; $i++) {
      echo "<span class='fa fa-star checked' style='color:gold;'></span>";
    }
    if ($rating - (int)$rating != 0) {
      echo "<span class='fa fa-star-half-alt'></span>";
    }
    for ($i = 0; $i < 5 - $rating; $i++) {
      echo "<span class='fa fa-star'></span>";
    }
  }else{
    echo "Geen rating";
  }


  echo "</td></tr></table>";

  }

function geefGerelateerdeProducten($pro_id, $conn){
  $sql = "SELECT rubrieknummer FROM VoorwerpInRubriek WHERE voorwerpnummer = ?";
  $query = $conn->prepare($sql);
  $query->execute(array($pro_id));
  $rubriek = $query->fetchAll();
  $rubrieknummer = $rubriek[0]['rubrieknummer'];

  $sql2 = "SELECT TOP 6 V.voorwerpnummer, V.titel, dbo.FN_GeefPrijs(V.voorwerpnummer) AS 'startprijs', V.thumbnail, V.looptijd_einde_datum, V.looptijd_einde_tijd, V.source, V.geblokkeerd
            FROM dbo.Voorwerp V
            INNER JOIN dbo.VoorwerpInRubriek VIR 
            ON VIR.voorwerpnummer = V.voorwerpnummer
            WHERE VIR.rubrieknummer = ? AND V.voorwerpnummer != ? AND looptijd_einde_datum >= GETDATE() ORDER BY NEWID()";
  $query = $conn->prepare($sql2);
  $query -> execute(array($rubrieknummer, $pro_id));
  $row = $query -> fetchAll(PDO::FETCH_ASSOC);
  if(sizeof($row) < 6){
    $sql3 = "SELECT TOP 6 voorwerpnummer, titel, dbo.FN_GeefPrijs(voorwerpnummer) AS 'startprijs', thumbnail, looptijd_einde_datum, looptijd_einde_tijd, source, geblokkeerd FROM Voorwerp WHERE voorwerpnummer != ? AND looptijd_einde_datum > GETDATE() ORDER BY NEWID()";
    $query = $conn->prepare($sql3);
    $query->execute(array($pro_id));
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
  }
  $increment = 0;
  $gegevens = null;
  if(!empty($row)) {
    foreach ($row as $product) {
      $pro_id = $product['voorwerpnummer'];
      $date = date_create($product['looptijd_einde_datum'] . "  " . $product['looptijd_einde_tijd']);
      $sec_diff = date_diff(date_create(), $date);
      $secs = 0;
      $secs += $sec_diff->format('%a') * 24 * 60 * 60;
      $secs += $sec_diff->format('%h') * 60 * 60;
      $secs += $sec_diff->format('%i') * 60;
      $secs += $sec_diff->format('%s');
      $secs = $secs * 1000;
      $item['pro_id'] = $pro_id;
      if($product['source'] == 1) {
        $item['img'] = 'https://iproject26.icasites.nl/thumbnails/' . $product['thumbnail'];
      } else {
        $item['img'] = 'https://iproject26.icasites.nl/UploadedImages/' . $product['thumbnail'];
      }
      $item['titel'] = $product['titel'];
      $item['startprijs'] = $product['startprijs'];
      $item['secs'] = $secs;
      $item['geblokkeerd'] = $product['geblokkeerd'];
      $gegevens[$increment] = $item;
      $increment++;
    }
  }

  $increment = 0;
  if(!empty($gegevens)) {
    foreach ($gegevens as $item) {
      if ($increment % 3 == 0) {
        echo "</div> <div class=\"row\">";
      }
      echo "<div class=\"col\">";
      createItemProper($item['pro_id'], $item['img'], $item['titel'], $item['startprijs'], $item['secs'], $item['geblokkeerd']);
      echo "</div>";
      $increment++;
    }
  }
}

function createFeedbackComments($conn){
  if(isset($_GET['pro_id'])){
  $query = $conn->prepare("SELECT TOP (10) *
  FROM feedback  WHERE voorwerpnummer = :pro_id");
  $query->execute(array(":pro_id" => $_GET['pro_id']));
  $result = $query->fetchAll();
  }else if(isset($_GET['verkoper'])){
    $query = $conn->prepare("SELECT *
    FROM feedback  WHERE verkoper = :username AND door_koper = 1");
    $query->execute(array(":username" => $_GET['verkoper']));
    $result = $query->fetchAll();
    if(!$result){
      echo "
        <div class=\"card navbar-light mb-4\">
        <div class=\"card-body d-flex flex-column\">
        <h4>Deze verkoper heeft momenteel geen feedback</h4>
        </div></div>";
    }
  }
  if($result){
  foreach($result as $row){ 
    if(!$row['door_koper']){
      $name =$row['verkoper'];
    }else{
     $name = getNameFromProductID($conn,$row['voorwerpnummer']);
    }

      $tijd = $row['tijd'];
      $tijd = explode(":",$tijd);
      $tijd = $tijd[0].":".$tijd[1];


      echo "<div class=\"row\">
        <div class=\"col-xl-12 mb-4\">
        <div class=\"card navbar-light mb-4 h-100\">
        <div class=\"card-body d-flex flex-column\">";
        echo "<div class='row ml-1'><h3 class='mb-3'>Naam: $name</h3><p class='ml-auto mr-3 mb-0'>".$row['datum']."</p>";
        echo "<p class='mr-3'>$tijd</p></div>";
        echo "Feedback:";
        echo "<div class='card'><p class='card-body' style='background-color:white;'>".$row['commentaar']."</p></div>";
        echo "<div class='mt-3 d-flex flex-row'><span class='mb-0 mr-2'>Rating:</span>";
          for ($i = 0; $i < $row['rating']; $i++) {
            echo "<span class='d-flex align-items-center fa fa-star checked' style='color:gold;'></span>";
          }
          for ($i = 0; $i < 5 - $row['rating']; $i++) {
            echo "<span  class='d-flex align-items-center fa fa-star'></span>";
          }
          if(!$row['door_koper']){
          echo "<span class=' ml-auto'>Feedback van verkoper op koper</span>";
          }
      echo "</div></div></div></div></div>";
  }
 }
}

function getNameFromProductID($conn,$ID){
  $query = $conn->prepare("SELECT koper_gebruikersnaam
  FROM voorwerp  WHERE voorwerpnummer = :pro_id");
  $query->execute(array(":pro_id" => $ID));
  $result = $query->fetch();
  return $result['koper_gebruikersnaam'];
}

function createFeedback($conn){

  $query = $conn->prepare("SELECT veiling_gesloten, koper_gebruikersnaam, verkoper_gebruikersnaam
  FROM voorwerp  WHERE voorwerpnummer = :pro_id");
  $query->execute(array(":pro_id" => $_GET['pro_id']));
  $result = $query->fetch();
  $reviewer = -1;
  if($result['veiling_gesloten']){
    if(isset($_SESSION['gebruikersnaam']) && $_SESSION['gebruikersnaam'] == $result['koper_gebruikersnaam']){
    $reviewer = 1;
    }elseif (isset($_SESSION['gebruikersnaam']) && $_SESSION['gebruikersnaam'] == $result['verkoper_gebruikersnaam']){
    $reviewer = 0;
  }
}
    $checkQuery = $conn->prepare('SELECT voorwerpnummer FROM feedback WHERE voorwerpnummer = :pro_id AND door_koper = '.$reviewer);
    $checkQuery->execute(array("pro_id" => $_GET['pro_id']));
    $checkQueryResult = $checkQuery->fetch();
    if(!$checkQueryResult){
    if($reviewer == 0 || $reviewer == 1){
  echo "
  <div class=\"row\">
      <div class=\"col-xl-12 mb-4\">
          <div class=\"card navbar-light mb-4 h-100\">
              <div class=\"card-body d-flex flex-column\">";
                echo "<h3 class='text-center mb-3'>Geef hier uw feedback op de aankoop<h3>";
                echo "<form method=\"post\" class=\" m-0 mw-100\">
                  <textarea maxlength='500' minlength='20' name=\"feedbackText\" class=\"form-control p-3\" required placeholder=\"Feedback\" style='height:20vh; min-height:10vh;'></textArea>
                  <div class=\"row mt-3\">
                  <div class=\"col  w-50\">
                  </div>
                  <div class=\"col w-50\">
                  <div class='rate' style='float:right;'>
                    <input type='radio' id='star5' required name='rate' value='5'/>
                    <label for='star5' title='5 stars'>5 stars</label>
                    <input type='radio' id='star4' name='rate' value='4'/>
                    <label for='star4' title='4 stars'>4 stars</label>
                    <input type='radio' checked id='star3' name='rate' value='3'/>
                    <label for='star3'  title='3 stars'>3 stars</label>
                    <input type='radio' id='star2' name='rate' value='2'/>
                    <label for='star2' title='2 stars'>2 stars</label>
                    <input type='radio' id='star1' name='rate' value='1'/>
                    <label for='star1' title='1 stars'>1 star</label>
                  </div>
                  </div>
                  </div>
                  <input name='reviewer' type='hidden' value='$reviewer'>
                  <input type='submit' name='feedback' value='Versturen' class='btn'>
                  
                  </form>";
                echo "</div>
          </div>
      </div>
  </div>";
  }
 }else{
  
 }
}

function processFeedback($conn){
  $sideQuery = $conn->prepare('SELECT verkoper_gebruikersnaam FROM voorwerp WHERE voorwerpnummer = :pro_id');
  $sideQueryResult = $sideQuery->execute(array("pro_id" => $_GET['pro_id']));
  $sideQueryResult = $sideQuery->fetch();

  $query = $conn->prepare("INSERT INTO feedback(
   [voorwerpnummer]
  ,[door_koper]
  ,[datum]
  ,[tijd]
  ,[commentaar]
  ,[rating]
  ,[verkoper]) VALUES (:productid, :door_koper, :datum, :tijd, :commentaar, :rating, :verkoper_gebruikersnaam)");
  
  $rating = $_POST['rate'];
  if($rating > 5){
  $rating = 5;
  }elseif($rating < 1){
  $rating = 1;
  }

  try{
    $query->execute(array(
    ":productid" => $_GET['pro_id'],
    ':door_koper' => htmlspecialchars($_POST['reviewer']),
    ':datum' => date('Y-m-d'),
    ':tijd' => date('H:i:s'),
    ':commentaar' => htmlspecialchars($_POST['feedbackText']),
    ':rating' => $rating,
    ':verkoper_gebruikersnaam' => htmlspecialchars($sideQueryResult['verkoper_gebruikersnaam'])
  ));
  }catch (Exepction $e){
    makeModals("Error", "U heeft al feedback geuploaded");
  }
}

function veilingVoorbij($pro_id, $conn){
    $query = "SELECT * FROM Voorwerp WHERE looptijd_einde_datum >= GETDATE() AND voorwerpnummer = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($pro_id));
    $fetch = $sql->fetchAll();
    return empty($fetch);
}

function GiveProductName($conn){
  $query = "SELECT titel FROM Voorwerp Where voorwerpnummer = :pro_id";
  $query = $conn->Prepare($query);
  $query->execute(array(":pro_id" => $_GET["pro_id"]));
  $Result = $query->fetch();
  $Name = $Result["titel"];
  return $Name;
}
?>