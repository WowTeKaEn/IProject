<?php
include_once('functions.php');
include_once('functions/functions_profiel_pagina.php');
if(isVerkoper($conn) && isset($_GET['pro_id'])){
    $stmt = $conn->prepare("SELECT * FROM Voorwerp WHERE verkoper_gebruikersnaam = :username and voorwerpnummer = :product");
    $stmt->execute(array(':username' => $_SESSION['gebruikersnaam'], ':product' => $_GET['pro_id']));
    $isFromUserfetch = $stmt->fetch();
    if($isFromUserfetch){
        $stmt = $conn->prepare("SELECT filenaam FROM bestand WHERE voorwerpnummer = :product");
        $stmt->execute(array(':product' => $_GET['pro_id']));
        $result = $stmt->fetchAll();
        foreach($result as $key=>$val){
        umask(0);
        chmod("uploadedImages/".$result[$key]['filenaam'], 0777);
        try{
            unlink("uploadedImages/".$result[$key]['filenaam']);
        }
        catch (Exception $e){
        }
    
        }
        $conn->query("DELETE voorwerpInRubriek WHERE voorwerpnummer = ".$_GET['pro_id']);
        $conn->query("DELETE bestand WHERE voorwerpnummer = ".$_GET['pro_id']);
        $conn->query("DELETE bod WHERE voorwerpnummer = ".$_GET['pro_id']);
        $conn->query("DELETE voorwerp WHERE voorwerpnummer = ".$_GET['pro_id']);
    }
}
header('location: eigenVeilingen.php');
?>