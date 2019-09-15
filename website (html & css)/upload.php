<div style="display:none;">
<?php
include_once 'functions.php';
include_once 'functions/functions_createAuction.php';
include_once 'functions/functions_profiel_pagina.php';

if(isset($_POST['submit'])){
    if(isVerkoper($conn)){
    $error = 0;
    $imageQuality = 20;
    $user = getUserFromName($_SESSION['gebruikersnaam'],$conn);
    $username = $user['gebruikersnaam'];
    $title = $_POST['title'];
    $discription =  $_POST['discription'];
    $entryPrice = str_replace(',','.',$_POST['price']);
    $place = $user['plaatsnaam'];
    $country = $user['landnaam'];

    $start = explode(' ',$_POST['dateTimeStart']);
    $end = explode(' ',$_POST['dateTimeEnd']);
    $startDateParts = explode('-',$start[0]);
    $startDate = $startDateParts[2]."-".$startDateParts[1]."-".$startDateParts[0];
    $startTime = $start[1];
    $endDateParts = explode('-',$end[0]);
    $endDate = $endDateParts[2]."-".$endDateParts[1]."-".$endDateParts[0];
    $endTime = $end[1];
    $interval  = date_diff(date_create($startDate),date_create($endDate));
    $runningTime = $interval->format('%a');
    
    if(isset($_POST['transportCosts'])){
    $transportCosts = str_replace(',','.',$_POST['transportCosts']);
    }else{
    $transportCosts = null; 
    }

    if(isset($_POST['transportInstructions'])){
    $transportInstructions = $_POST['transportInstructions'];
    }else{
    $transportInstructions = null; 
    }

    if(isset($_POST['paymentInstructions'])){
    $paymentinstructions = $_POST['paymentInstructions'];
    }else{
    $paymentinstructions = null; 
    }

    if(isset($_POST['condition'])){
    $condition = $_POST['condition'];
    }else{
    $condition = null;
    }

    if(isset($_POST['paymentMethod'])){
    $paymentMethod = $_POST['paymentMethod'];
    }else{
    $paymentMethod = null; 
    }

    $targetDir = "uploadedImages/";
    $allowTypes = array('jpg','png','jpeg','gif');

    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = '';
    if(!empty(array_filter($_FILES['files']['name']))){
        foreach($_FILES['files']['name'] as $key=>$val){
            $fileName = basename($_FILES['files']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
          
            if(in_array($fileType, $allowTypes)){
            }else{
                $error = 2;
            }
        }
    }else{
        $error = 2;
    }   

    $int = getMaxItemInt($conn);
    $int ++;

    if($error === 0){
        $imageCount = 0;
        foreach($_FILES['files']['name'] as $key=>$val){
            $imageCount ++;
            $fileName = basename($_FILES['files']['name'][$key]);
            $inipath = php_ini_loaded_file();        
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
            $newfileName = "dt_".$imageCount."_".$int.".".$fileType;
            $targetFilePath = $targetDir . $newfileName;
            correctImageOrientation($_FILES["files"]["tmp_name"][$key],$fileType);
            lowerImageQuality($_FILES["files"]["tmp_name"][$key],$fileType,$imageQuality);
            if(in_array($fileType, $allowTypes)){
                if($error === 0){
                if(rename($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                    $insertValuesSQL .= "(".$int.",'".$newfileName."',0),";
                    if($imageCount === 1){
                        $firstfileName = $newfileName;
                    }
                }else{
                    $error = 1;
                }
            }
        }else{
            $error = 2;
        }
        
    }
    if($error === 0){
        $insertItemPart1 = "INSERT INTO Voorwerp (titel, beschrijving,startprijs,betalingswijze_naam,plaatsnaam,landnaam,looptijd,looptijd_start_datum,looptijd_start_tijd,verkoper_gebruikersnaam, looptijd_einde_datum, looptijd_einde_tijd, veiling_gesloten,source,verzendkosten,verzendinstructies,betalingsinstructie,conditie, thumbnail)
        VALUES (:title, :discription,:entryPrice,
         :paymentMethod,:place, :country,:runningTime,
         :startDate,:startTime,:username,
         :endDate, :endTime,
         :closed, :source, :transportCosts, :transportInstructions, :paymentinstructions, :condition, :thumbnail)";
        $insertItem = $conn->prepare($insertItemPart1);
        try {
        $insertItem->execute(array(
           ':title' => htmlspecialchars($title),
           ':discription' => htmlspecialchars($discription),
           ':entryPrice' => floatval($entryPrice),
           ':paymentMethod' => htmlspecialchars($paymentMethod),
           ':place' => htmlspecialchars($place),
           ':country' => htmlspecialchars($country),
           ':runningTime' => $runningTime,
           ':startDate' => $startDate,
           ':startTime' => $startTime,
           ':username' => htmlspecialchars($username),
           ':endDate' => $endDate,
           ':endTime' => $endTime, 
           ':closed' => 0, 
           ':source' => 0, 
           ':transportCosts' => floatval($transportCosts),
           ':transportInstructions' => htmlspecialchars($transportInstructions),
           ':paymentinstructions' => htmlspecialchars($paymentinstructions),
           ':condition' => $condition,
           'thumbnail' => $firstfileName
        ));
    } catch (Exception $e){
        $error = 3;
    } 
}
        if($error === 0){
            $category = $_POST['cat_id'];
            $insertCategory = $conn->prepare("INSERT INTO voorwerpInRubriek (voorwerpnummer, rubrieknummer,source) VALUES ($int,:category,0)");
            $insertCategory->execute(array(":category" => $category));
        }
    if($error === 0){
        if(!empty($insertValuesSQL)){
            $insertValuesSQL = htmlspecialchars(trim($insertValuesSQL,','));
            try {
                $insert = $conn->query("INSERT INTO bestand (voorwerpnummer, filenaam, source) VALUES $insertValuesSQL");
            } catch (Exception $e) {
                $error = 4;
            }
        }
    }
}
}else{
    $error = 5;
}
}else {
    header("Location: index.php");
}
if($error == 0){
$action = 'detailpagina.php?pro_id='.$int;
}else{
    $action = 'createAuction.php';
}
echo "<form name='autoSubmit' style='display:none;' method='post' action=$action>
<input name='message' value=$error>
</form>";
?>
</div>
<script>
document.autoSubmit.submit();
</script>
