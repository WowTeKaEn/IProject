<?php
if(isset($_POST['verwijderen'])){
    $gebruikersnaam = $_SESSION['gebruikersnaam'];
    echo "$naam";
    
    $sql = "DELETE
    FROM dbo.Gebruiker
    WHERE gebruikersnaam like :gebruikersnaam";
    $query = $conn->prepare($sql);
    $query -> execute(array(
        ':gebruikersnaam' => $gebruikersnaam
    ));
}