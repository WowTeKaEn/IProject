<?php
function createNotification($koper, $verkoopprijs, $printBod, $naam, $conn){
    $sql = "SELECT gebruikersnaam, titel, datum, tekst FROM dbo.Meldingen
    WHERE gebruikersnaam = :user";
    $query = $conn->prepare($sql);
    $query->execute(array(":user" => $_SESSION['gebruikersnaam']));
    $row = $query->fetchAll();
    if(isset($row)){
        $advertentie = $row[0];
        if(!empty($advertentie['gebruikersnaam'])) {
            foreach ($row as $meldingen) {
                $title = $meldingen['titel'];
                $tijd = $meldingen['datum'];
                $description = $meldingen['tekst'];
                echo "<article class='alert alert-success alert-dismissible' role='alert'>
                <h2 class='alert-heading'>$title</h2>
                <h4>$tijd</h4>
                <p>$description</p>
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                </article>";
            }
        }
    }
}
?>