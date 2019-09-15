<?php
function createOwnItems($conn){
    $query = $conn->prepare("SELECT V.voorwerpnummer, V.titel, dbo.FN_GeefPrijs(V.voorwerpnummer) AS 'prijs', V.thumbnail, V.source, V.looptijd_einde_datum , V.looptijd_einde_tijd, V.veiling_gesloten
  FROM dbo.Voorwerp V WHERE verkoper_gebruikersnaam = :username");
  $query->execute(array(":username" => $_SESSION['gebruikersnaam']));
  if($result = $query->fetchAll()){
    foreach($result as $product){
    $closed = $product['veiling_gesloten'];
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
      createOwnItemProper($product['voorwerpnummer'],"uploadedImages/".$product['thumbnail'],$product['titel'],$product['prijs'],$secs,$closed);
    }
  }else{
    echo "<h1 class='m-auto display-3'>U heeft geen eigen veilingen<h1>";
  }
}

function endAuction($conn){
  $query = $conn->prepare("SELECT verkoper_gebruikersnaam, veiling_gesloten FROM voorwerp WHERE voorwerpnummer = :pro_id");
  $query->execute(array(":pro_id" => $_GET['pro_id']));
  $result = $query->fetch();
  if($result['verkoper_gebruikersnaam'] == $_SESSION['gebruikersnaam']){
    if(!$result['veiling_gesloten']){
  $query = $conn->prepare("SELECT gebruikersnaam FROM bod WHERE bod_bedrag IN(SELECT max(bod_bedrag) FROM bod WHERE voorwerpnummer = :pro_id)");
  $query->execute(array(":pro_id" => $_GET['pro_id']));
  if($result = $query->fetch()){
  $maxBidder = $result['gebruikersnaam'];
  $timeNow = date('h:i:s');
  $dateNow = date('Y-m-d');
  $query = $conn->prepare("UPDATE Voorwerp SET veiling_gesloten = 1, koper_gebruikersnaam = :maxBidder, looptijd_einde_tijd = :timeNow, looptijd_einde_datum = :dateNow WHERE voorwerpnummer = :pro_id");
  $query->execute(array(":pro_id" => $_GET['pro_id'],":maxBidder" => $maxBidder,":timeNow" => $timeNow, ":dateNow" => $dateNow));
  echo "<form name='autoSubmit' style='display:none;' method='post' action='eigenVeilingen.php'>
  <input name='ended' value=1>
  </form>
  <script>
  document.autoSubmit.submit();
  </script>
  ";
  }else{
    makeModals("Error", "Nog niemand heeft op deze veiling geboden dus u kunt de veiling niet sluiten");
  }
}else{
  makeModals("Error", "Deze veiling is al gesloten");
}
}else{
  makeModals("Error", "Dit is niet uw veiling");
}
}



function AuctionCheck($href,$message){
  echo "<div class='modal fade' data-backdrop='true' id='message' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
  <div class='modal-content'>
  <div class='modal-header'>
  <h5 class='modal-title' id='exampleModalLabel'>Bevestiging</h5>
  <a href=eigenveilingen.php class='close' aria-label='Close'></a>
  <span aria-hidden='true'>&times;</span>
  </button>
  </div>
  <div class='modal-body'>
  $message
  </div>
  <div class='modal-footer'>
  <a href=$href class='btn btn-block mt-2' >Ja</a>
  <a href=eigenVeilingen.php class='btn btn-block mt-2' >Nee</a>
  </div>
  </div>
  </div>
  </div>";
}

function createOwnItemProper($pro_id, $img, $name, $price, $sec_diff,$closed){
  if($closed){
  $time = "<strong>Gesloten</strong>";
  $bid = 'Gesloten';
  }else{
  $time = "<p>Sluit over: <strong><span id='timer' data-time='". $sec_diff ."'></span></strong></p>";
  $bid = "Bied nu!";
  }
  echo "
  <article>
  <div class='mx-3 position-absolute inFront d-flex justify-content-center flex-column'>
  <a href='eigenveilingen.php?pro_id=$pro_id&delete=0' class='position-relative hoverBtn mt-auto btn text-nowrap'>Verwijder veiling</a><br>
  <a href='detailpagina.php?pro_id=$pro_id' class='position-relative hoverBtn mt-auto btn text-nowrap'>Bekijk veiling</a>";
  if(!$closed){
  echo "<button  onclick=endAuction($pro_id) class='position-relative hoverBtn mt-4 btn text-nowrap'>Beëindig veiling</button>
  <form name='autoSubmit' id=$pro_id style='display:none;' method='post' action='eigenveilingen.php'>
  <input name='product' value=$pro_id>
  <input name='end' value=0>
  </form>";
  }
  echo "</div>
  <div class='ownItemArticle mx-3 my-4 bid-btn'>
  <div class='bid-btn ownItem shadow-lg d-flex flex-column align-items-center  item background-item-color'>
  <container class=' d-flex justify-content-center align-items-center img-container'>
    <img  src='".$img."' alt='Foto ".$name."'  class='img-fluid h-100'>
    </container>
    <div class='d-flex flex-fill flex-column item-inner'>
    <div class='w-100 highest-bid'>
    <p class='text-wrap text-center py-1 mb-0'>Hoogste bod: <strong>&euro; ". number_format($price, 2, ',', '.') ."</strong></p>
    $time
    </div>
    <div class='h-100 w-100 p-3'>
    <h6>".$name."</h6>
    </div>
    </div>
    <button class='mt-auto btn bid-btn text-nowrap' type='submit'>$bid</button>
    </a>
    </article>";
}

?>