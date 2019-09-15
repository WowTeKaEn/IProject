<?php
include_once 'functions/functions_mail.php';
if(isset($_POST['registreren'])) {
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $adres1 = $_POST['adres1'];
    $adres2 = $_POST['adres2'];
    $postcode = $_POST['postcode'];
    $plaats = $_POST['plaats'];
    $land = $_POST['land'];
    $geboortedatum = $_POST['geboortedatum'];
    $emailadres = $_POST['emailadres'];
    $password = $_POST['wachtwoord'];
    $password_confirm = $_POST['wachtwoord_herhalen'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $_SESSION['naam'] = $gebruikersnaam;
    
    $sql = "SELECT gebruikersnaam, emailadres FROM dbo.Gebruiker
    WHERE gebruikersnaam = :gebruikersnaam
    OR emailadres = :emailadres";
    $query = $conn->prepare($sql);
    $query -> execute(array(
        ':gebruikersnaam' => $gebruikersnaam,
        ':emailadres' => $emailadres
    ));
    $row = $query -> fetch();
    if($gebruikersnaam == $row['gebruikersnaam']){
        makeModals("Error", "Deze gebruikersnaam is al in gebruik");	
    }
    else if($emailadres == $row['emailadres']){
        makeModals("Error", "Dit e-mailadres is al in gebruik"); 	
    }
    else{
        if ($password == $password_confirm) {
            $nu = date("Y-m-d", time());
            $date1 = strtotime("$nu");  
            $date2 = strtotime("$geboortedatum");
            $verschil = $date1 - $date2;
            $min_leeftijd = 18 * 31556926;
            if ($verschil <= 0){
                makeModals("Error", "U komt uit de toekomst... Dit is niet mogelijk!");
            }
            else if ($verschil >= $min_leeftijd){
                $sql = "INSERT INTO dbo.Gebruiker (gebruikersnaam, voornaam, achternaam, adresregel1, 
                adresregel2, postcode, plaatsnaam, landnaam, geboortedatum, emailadres, wachtwoord,
                verkoper, source, verificatie, geblokkeerd)
                VALUES(:gebruikersnaam, :voornaam, :achternaam, :adres1, :adres2,:postcode, :plaats, :land, 
                :geboortedatum, :emailadres, :wachtwoord, :verkoper, :source, :verificatie, :geblokkeerd)";
                $query = $conn->prepare($sql);
                $query->execute(array(
                    ':gebruikersnaam' => htmlspecialchars($gebruikersnaam),
                    ':voornaam' => htmlspecialchars($voornaam),
                    ':achternaam' => htmlspecialchars($achternaam),
                    ':adres1' => htmlspecialchars($adres1),
                    ':adres2' => htmlspecialchars($adres2),
                    ':postcode' => htmlspecialchars($postcode),
                    ':plaats' => htmlspecialchars($plaats),
                    ':land' => $land,
                    ':geboortedatum' => $geboortedatum,
                    ':emailadres' => htmlspecialchars($emailadres),
                    ':wachtwoord' => password_hash($password, PASSWORD_DEFAULT),
                    ':verkoper' => 0,
                    ':source' => 0,
                    ':verificatie' => 0,
                    ':geblokkeerd' => 0
                    )
                );
                
                $sql2 = "INSERT INTO dbo.Gebruikerstelefoon (gebruikersnaam, telefoonnummer)
                VALUES(:gebruikersnaam, :telefoonnummer)";
                $query2 = $conn->prepare($sql2);
                $query2->execute(array(
                    ':gebruikersnaam' => htmlspecialchars($gebruikersnaam),
                    ':telefoonnummer' => $telefoonnummer
                    )
                );
                $getal = mt_rand(111111, 999999);
                $_SESSION['code'] = $getal;
                $_SESSION['emailadres'] = $emailadres;
                $subject = "Account verificatiecode";
                $site = "href='https://iproject26.icasites.nl/verificatieGebruiker.php?code=$getal";
                sendMail($getal, $subject, $site, $gebruikersnaam);
                header('Location: bevestigingspagina.php');
            }
            else {
                makeModals("Error", "U bent niet oud genoeg, u moet minimaal 18 jaar zijn!");
            }       
        } else {
            makeModals("Error", "De wachtwoorden zijn niet gelijk!");
        }
    }
}
?>