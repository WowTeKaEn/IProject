<?php

//Voert de nodige informatie om een verkoper te worden in de database
if (isset($_POST['verkoper'])) {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $banknaam = $_POST['banknaam'];
    $rekeningnummer = $_POST['rekeningnummer'];
    $controleOptie = $_POST['controle_optie_naam'];
    if(isset($_POST['creditcardnummer'])){
      $creditcardnummer = $_POST['creditcardnummer'];
    }
    else{
      $creditcardnummer = 0;
    }

    $sql2 = "UPDATE dbo.Gebruiker
    SET verkoper = 1
    WHERE gebruikersnaam = :gebruikersnaam";
    $query2 = $conn->prepare($sql2);
    $query2->execute(
      array(
        ':gebruikersnaam' => $gebruikersnaam
      )
    );
    
    $sql = "INSERT INTO dbo.Verkoper (gebruikersnaam, banknaam, rekeningnummer, 
    controle_optie_naam, creditcardnummer, source)
    VALUES(:gebruikersnaam, :banknaam, :rekeningnummer, :controle_optie_naam, :creditcardnummer, :source)";
    $query = $conn->prepare($sql);
    $query->execute(
      array(
        ':gebruikersnaam' => htmlspecialchars($gebruikersnaam),
        ':banknaam' => $banknaam,
        ':rekeningnummer' => $rekeningnummer,
        ':controle_optie_naam' => $controleOptie,
        ':creditcardnummer' => $creditcardnummer,
        ':source' => 0
      )
    );
    $_SESSION['verkoper'] = true;
    header('Location: index.php');
}
if (isset($_POST['post'])) {
  $sql = "INSERT INTO dbo.Verkoper (gebruikersnaam, banknaam, rekeningnummer, 
    controle_optie_naam, creditcardnummer, source)
    VALUES(:gebruikersnaam, :banknaam, :rekeningnummer, :controle_optie_naam, :source)";
    $query = $conn->prepare($sql);
    $query->execute(
      array(
        ':gebruikersnaam' => htmlspecialchars($gebruikersnaam),
        ':banknaam' => $banknaam,
        ':rekeningnummer' => $rekeningnummer,
        ':controle_optie_naam' => $controleOptie,
        ':source' => 0
      )
    );
  //Hier moet er een brief gestuurd worden met $getal als code
  $getal = mt_rand(111111,999999);
  $_SESSION['briefCode'] = $getal;
  $_SESSION['brief'] = true;
  header('Location: profielpagina.php');
}
if (isset($_POST['codes'])) {
  if (isset($_SESSION['briefCode'])) {
    $gebruikersnaam = $_SESSION['gebruikersnaam'];
    $code = $_POST['code'];
    $briefCode = $_SESSION['briefCode'];
    if($code == $briefCode){  
      $sql2 = "UPDATE dbo.Gebruiker
      SET verkoper = 1
      WHERE gebruikersnaam = :gebruikersnaam";
      $query2 = $conn->prepare($sql2);
      $query2->execute(array(
        ':gebruikersnaam' => $gebruikersnaam
        )
      );
      $sql = "INSERT INTO dbo.Verkoper (gebruikersnaam, banknaam, rekeningnummer, 
      controle_optie_naam, source)
      VALUES(:gebruikersnaam, :banknaam, :rekeningnummer, :controle_optie_naam, :source)";
      $query = $conn->prepare($sql);
      $query->execute(
        array(
          ':gebruikersnaam' => $_SESSION['gebruiker'],
          ':banknaam' => $_SESSION['bank'],
          ':rekeningnummer' => $_SESSION['rekening'],
          ':controle_optie_naam' => $_SESSION['controle'],
          ':source' => 0
        )
      );
      $_SESSION['briefVerkoper'] = true;
    }
    else{
      makeModals("Error", "De codes komen niet overeen!");
    }
  }
  else{
    makeModals("Error", "De codes komen niet overeen!");
  }
}

function isVerkoper($conn){
    $naam = $_SESSION['gebruikersnaam'];
    $query = "SELECT verkoper FROM Gebruiker WHERE gebruikersnaam = :naam";
    $sql = $conn->prepare($query);
    $sql->execute(array(":naam" => $naam));
    $verkoper = $sql->fetchAll();
    if($verkoper[0]['verkoper'] == 1) {
        return true;
    }else{
        return false;
    }
}

function isAdmin($conn){
  $naam = $_SESSION['gebruikersnaam'];
  $query = "SELECT admin FROM Gebruiker WHERE gebruikersnaam = :naam";
  $sql = $conn->prepare($query);
  $sql->execute(array(":naam" => $naam));
  $admin = $sql->fetchAll();
  if($admin[0]['admin'] == 1) {
      return true;
  }else{
      return false;
  }
}

//Verifieert en verandert het wachtwoord van de gebruiker in de database
function changePassword($username, $oldPassword, $newPassword, $newPasswordOpnieuw, $conn)
{
  if(!empty($newPassword) && !empty($newPasswordOpnieuw)) {
    if ($newPassword == $newPasswordOpnieuw) {
      $query = "SELECT wachtwoord FROM Gebruiker WHERE gebruikersnaam = ?";
      $sql = $conn->prepare($query);
      $sql->execute(array($username));
      $password = $sql->fetchAll();
      if (password_verify($oldPassword, $password[0][0])) {
        $query = "UPDATE Gebruiker SET wachtwoord = ? WHERE gebruikersnaam = ?";
        $sql = $conn->prepare($query);
        $sql->execute(array(password_hash($newPassword, PASSWORD_DEFAULT), $username));
        echo "<div class=\"alert alert-success\" role=\"alert\"> Uw wachtwoord is succesvol veranderd </div>";
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\"> U heeft een verkeerd wachtwoord ingevuld! </div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\">De nieuwe wachtwoordvelden moeten hetzelfde wachtwoord bevatten!</div>";
    }
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul de wachtwoordvelden in! </div>";
  }
}

//Verandert het emailadres van de gebruiker in de database
function changeMailAddress($email, $conn)
{
  if(!empty($email)) {
    $naam = $_SESSION['gebruikersnaam'];
    $query = "UPDATE Gebruiker SET emailadres = ? WHERE gebruikersnaam = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($email, $naam));
    echo "<div class=\"alert alert-success\" role=\"alert\"> Uw e-mailadres is succesvol veranderd </div>";
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\">Vul een e-mailadres in!</div>";
  }
}

//Verwijdert het account van de gebruiker
function deleteAccount($conn)
{
    $naam = $_SESSION['gebruikersnaam'];
    $query1 = "DELETE FROM Verkoper WHERE gebruikersnaam like :gebruikersnaam";
    $query2 = "DELETE FROM Gebruikerstelefoon WHERE gebruikersnaam like :gebruikersnaam";
    $query3 = "DELETE FROM Gebruiker WHERE gebruikersnaam like :gebruikersnaam";
    $sql1 = $conn->prepare($query1);
    $sql1 -> execute(array(
        ':gebruikersnaam' => $naam
    ));
    $sql2 = $conn->prepare($query2);
    $sql2 -> execute(array(
        ':gebruikersnaam' => $naam
    ));
    $sql3 = $conn->prepare($query3);
    $sql3 -> execute(array(
        ':gebruikersnaam' => $naam
    ));
    header("Location: index.php");
    echo "<div class=\"alert alert-success\" role=\"alert\"> Uw account is verwijderd. </div>";
    session_destroy();
    exit;
}

//Verandert de locatie van de gebruiker
function changeLocation($plaats, $adres, $adres2, $postcode, $land, $conn)
{
  if(!empty($plaats) && !empty($adres) && !empty($postcode)) {
    $naam = $_SESSION['gebruikersnaam'];
    $query = "UPDATE Gebruiker SET adresregel1 = ?, postcode = ?, plaatsnaam = ?, landnaam = ? WHERE gebruikersnaam = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($adres, $postcode, $plaats, $land, $naam));
    if (!empty($adres2)) {
      $query = "IF EXISTS(
                SELECT adresregel2 from Gebruiker where gebruikersnaam = ?
                )   BEGIN
                    UPDATE Gebruiker SET adresregel2 = ? WHERE gebruikersnaam = ?
                END
                ELSE
                IF NOT EXISTS(
                SELECT adresregel2 FROM Gebruiker where gebruikersnaam = ?
                )   BEGIN
                    INSERT INTO Gebruiker(adresregel2) VALUES(?) 
                END";
      $sql = $conn->prepare($query);
      $sql->execute(array(htmlspecialchars($naam), htmlspecialchars($adres2), htmlspecialchars($naam), htmlspecialchars($naam), htmlspecialchars($adres2)));
    }
    echo "<div class=\"alert alert-success\" role=\"alert\"> Uw locatiegegevens zijn succesvol veranderd </div>";
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul aub de verplichte velden in! </div>";
  }
}

//Verandert de bank en het rekeningnummer van de gebruiker
function changeBank($banknaam, $rekeningnummer, $conn)
{
  if(!empty($rekeningnummer)) {
    $naam = $_SESSION['gebruikersnaam'];
    $query = "UPDATE verkoper SET banknaam = ?, rekeningnummer = ? WHERE gebruikersnaam = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($banknaam, $rekeningnummer, $naam));
    echo "<div class=\"alert alert-success\" role=\"alert\"> Uw bankgegevens zijn succesvol veranderd </div>";
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul een rekeningnummer in! </div>";
  }
}

//Verandert het creditcardnummer van de gebruiker
function changeCreditCard($creditcardnummer, $conn)
{
  if(!empty($creditcardnummer)) {
    $naam = $_SESSION['gebruikersnaam'];
    $query = "UPDATE Verkoper SET creditcardnummer = ? WHERE gebruikersnaam = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($creditcardnummer, $naam));
    echo "<div class=\"alert alert-success\" role=\"alert\"> Uw Creditcardgegevens zijn succesvol veranderd </div>";
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul een creditcardnummer in! </div>";
  }
}

//Verandert het telefoonnummer van de gebruiker
function changePhoneNr($telefoonnummer, $conn)
{
  if(!empty($telefoonnummer)) {
    $naam = $_SESSION['gebruikersnaam'];
    $query = "UPDATE Gebruikerstelefoon SET telefoonnummer = ? WHERE gebruikersnaam = ?";
    $sql = $conn->prepare($query);
    $sql->execute(array($telefoonnummer, $naam));
    echo "<div class=\"alert alert-success\" role=\"alert\">Uw telefoonnummer is succesvol veranderd</div>";
  } else {
    echo "<div class=\"alert alert-danger\" role=\"alert\"> Vul een telefoonnummer in! </div>";
  }
}

//Print alle gegevens van de gebruiker in een tabel
function displayMijnGegevens($conn)
{
    $query = "SELECT emailadres, banknaam, rekeningnummer, telefoonnummer, plaatsnaam, adresregel1, adresregel2, postcode, landnaam, creditcardnummer, verkoper FROM gebruiker left join verkoper on gebruiker.gebruikersnaam = verkoper.gebruikersnaam 
                                left join Gebruikerstelefoon on gebruiker.gebruikersnaam = Gebruikerstelefoon.gebruikersnaam WHERE gebruiker.gebruikersnaam = ?";
    $naam = $_SESSION['gebruikersnaam'];
    $sql = $conn->prepare($query);
    $sql->execute(array($naam));
    $gegevens = $sql->fetchAll();
    echo "
        <table class=\"mb-3\" style=\"width: 100%;\">
            <tr>
                <td>Naam:</td>
                <td>$naam</td>
            </tr>
            <tr>
                <td>Plaats:</td>
                <td>".$gegevens[0]['plaatsnaam']."</td>
            </tr>
            <tr>
                <td>Adres:</td>
                <td>".$gegevens[0]['adresregel1']."</td>";
    if (!empty($gegevens[0]['adresregel2'])) {
        echo "
            </tr>
                <td>Adres 2:</td>
                <td>".$gegevens[0]['adresregel2']."</td>";
    }
    echo "
            </tr>
            <tr>
                <td>Postcode:</td>
                <td>".$gegevens[0]['postcode']."</td>
            </tr>
            <tr>
                <td>Land:</td>
                <td>".$gegevens[0]['landnaam']."</td>
</tr>
            <tr>
                <td>e-mail:</td>
                <td>".$gegevens[0]['emailadres']."</td>
            </tr>
            <tr>
                <td>Telefoonnummer:</td>
                <td>".$gegevens[0]['telefoonnummer']."</td>
            </tr>
            ";
  if($gegevens[0]['verkoper'] == 1) {
      echo "
            <tr>
                <td>Bank:</td>
                <td>" . $gegevens[0]['banknaam'] . "</td>
            </tr>
            <tr>
                <td>Rekeningnummer:</td>
                <td>" . $gegevens[0]['rekeningnummer'] . "</td>
            </tr>
            <tr>
                <td>Creditcardnummer:</td>
                <td>" . $gegevens[0]['creditcardnummer'] . "</td>
            </tr>
        ";
    }
  echo "</table>";
}



function printVerkoperSettings($conn){
  if(isVerkoper($conn)) {
    echo "<div class=\"row\">
            <div class=\"col mb-3 p-3 border\">
                <h3>Betaalgegevens veranderen</h3>
                <form action=\"\" method=\"post\">
                    <div class=\"form-group\">
                        <label class=\"control-label\" for=\"banknaam\">Naam van uw bank:</label>
                        <select name=\"banknaam\" class=\"form-control\">
                            <option value=\"rabobank\">Rabobank</option>
                            <option value=\"abnamro\">ABN AMRO</option>
                            <option value=\"ingbank\">ING Bank</option>
                        </select>
                        <label class=\"control-label\" for=\"rekeningnummer\">Rekennummer:</label>
                        <input type=\"text\" name=\"rekeningnummer\" class=\"form-control\" placeholder=\"Rekeningnummer\">
                    </div>
                    <input type=\"submit\" value=\"Bankgegevens veranderen\" class=\"btn\" name=\"newBankDetails\">
                </form>
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col mb-3 p-3 border\">
                <h3>Creditcardgegevens veranderen</h3>
                <form action=\"\" method=\"post\">
                    <div class=\"form-group\">
                        <label class=\"control-label\" for=\"cardnummer\">Uw credit card nummer:</label>
                        <input type=\"text\" name=\"cardnummer\" class=\"form-control\" placeholder=\"Credit card nummer\">
                    </div>
                    <input type=\"submit\" value=\"Creditcardgegevens veranderen\" class=\"btn\" name=\"newCreditCard\">
                </form>
            </div>
        </div>";
  }
}

function printMijnVeilingen(){
  $query = "";
}

// printen van gewonnen veilingen

function gewonnenVeilingen($conn){
  $query = $conn->prepare("SELECT V.voorwerpnummer, V.titel, dbo.FN_GeefPrijs(V.voorwerpnummer) AS 'prijs', V.thumbnail, V.source, V.looptijd_einde_datum , V.looptijd_einde_tijd
  FROM dbo.Voorwerp V WHERE koper_gebruikersnaam = :username");
  $query->execute(array(":username" => $_SESSION['gebruikersnaam']));
  if($result = $query->fetchAll()){
    foreach($result as $product){
    $date = date_create($product['looptijd_einde_datum'] ."  ". $product['looptijd_einde_tijd']);
    $sec_diff = date_diff(date_create(),$date);
    $secs = 0;
    $secs += $sec_diff->format('%a') * 24 * 60 * 60;
    $secs += $sec_diff->format('%h') * 60 * 60;
    $secs += $sec_diff->format('%i') * 60;
    $secs += $sec_diff->format('%s');
    $secs = $secs * 1000;
    $img_string = "". $product['thumbnail'];
    if($product['source'] == 1){
        $img_string = 'http://iproject26.icasites.nl/thumbnails/'. $img_string;
    }else{
        $img_string = 'http://iproject26.icasites.nl/uploadedImages/'. $img_string;
    }
    createItemProperClosed($product['voorwerpnummer'],$img_string, $product['titel'], $product['prijs'], $secs);
  }
}
else{
  echo "<h3 class='text-center'> U heeft nog geen veilingen gewonnen</h3>";
}
}

function createItemProperClosed($pro_id, $img, $name, $price, $sec_diff){
  echo "
  <article>
  <a href='detailpagina.php?pro_id=$pro_id' class='shadow-lg d-flex flex-column align-items-center mx-3 my-4 item background-item-color'>
  <container class=' d-flex justify-content-center align-items-center img-container'>
  <img  src='".$img."' alt='Foto ".$name."'  class='img-fluid h-100'>
  </container>
  <div class='d-flex flex-fill flex-column item-inner'>
  <div class='w-100 highest-bid'>
  <p class='text-wrap text-center py-1 mb-0'>Hoogste bod: <strong>&euro; ". number_format($price, 2, ',', '.') ."</strong></p>
  <p><strong> Gesloten </strong></p>
  </div>
  <div class='h-100 w-100 p-3'>
  <h6>".$name."</h6>
  </div>
  </div>
  <button class='mt-auto btn bid-btn text-nowrap' type='submit'>U heeft gewonnen!</button>
  </a>
  </article>
  ";
}
?>