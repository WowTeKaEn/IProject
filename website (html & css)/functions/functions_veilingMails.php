<?php
include_once 'functions_messages.php';
include_once 'functions_product_page.php';
include_once 'functions_login.php';
function sendAuctionInfo($text, $subject, $emailadres, $tijd){
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "ReversiveConcepts@gmail.com";
    $to = $emailadres;
    $site = "href='https://iproject26.icasites.nl/index.php";
    $message = "
    <html>
    <body>
    <h1>$subject</h1>
    <h4>$tijd</h4>
    <br>
    $text
    <br>
    <p>Als u uw veilingen wilt bekijken klik dan op
    <table cellspacing='0' cellpadding='0'>
    <tr>
    <td style='border-radius: 2px;' bgcolor='#88B04B'>
    <a class='btn' target='_blank' 
    $site'style='padding: 8 12px;
    font-size: 14px; color: #000000; font-weight:bold; display: inline-block;'>    
    deze link!           
    </a>
    </td>
    </tr>
    </table>
    </p>
    <p>Wij wensen u veel success met uw veilingen.</p>
    <br>
    <p>Met vriendelijke groet,</p>
    <p>het Eenmaal Andermaal team</p>
    <img src='https://iproject26.icasites.nl/img/Logo.png' alt='logo' height=100px width=100px>
    </body>
    </html>";
    
    $headers = "From: ". strip_tags($from) . "\r\n";
    $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
    $headers .= "Return-Path: ". strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers.='X-Mailer: PHP/' . phpversion().'\r\n';
    mail($to, $subject, $message, $headers);
}
if(isset($_SESSION['loggedin'])){
    $gebruikersnaam = $_SESSION['gebruikersnaam'];
    $sql1 = "SELECT emailadres, gebruikersnaam FROM dbo.Gebruiker
    WHERE gebruikersnaam = :gebruikersnaam";
    $query1 = $conn->prepare($sql1);
    $query1 -> execute(array(
        ':gebruikersnaam' => $gebruikersnaam
    ));
    $row1 = $query1 -> fetch();
    $emailadres = $row1['emailadres'];
    
    $query = $conn->prepare("SELECT veiling_gesloten, looptijd, verkoper_gebruikersnaam, 
    looptijd_einde_tijd, looptijd_einde_datum, V.voorwerpnummer, titel, verkoopprijs, 
    koper_gebruikersnaam, geblokkeerd, source
    FROM dbo.Voorwerp V FULL JOIN Bod B on V.voorwerpnummer = B.voorwerpnummer
    WHERE source = 0");
    $query->execute(array($gebruikersnaam));
    if($result = $query->fetchAll()){
        foreach($result as $product){
            $sql3 = "SELECT titel, startprijs, V.source, gebruikersnaam, source, bod_bedrag 
            FROM Voorwerp V
            FULL JOIN Bod B on V.voorwerpnummer = B.voorwerpnummer
            WHERE V.voorwerpnummer = :itemNumber
            AND source = 0";
            $query3 = $conn->prepare($sql3);
            $query3->execute(array(":itemNumber" => $product["voorwerpnummer"]));
            $fetch = $query3->fetchAll();
            $advertentie = $fetch[0];
            if(!empty($advertentie['bod_bedrag'])) {
                $hoogsteBod = 0;
                foreach ($fetch as $bod) {
                    if ($bod['bod_bedrag'] > $hoogsteBod) {
                        $hoogsteBod = $bod['bod_bedrag'];
                    }
                }
                $printBod = $hoogsteBod;
            } else {
                $startprijs = $advertentie['startprijs'];
                if($startprijs == .00){
                    $startprijs = 0.00;
                }
                $printBod = $startprijs;
            }           
            $sql4 = "SELECT top(1) gebruikersnaam FROM bod WHERE bod_bedrag IN(SELECT max(bod_bedrag) FROM bod WHERE voorwerpnummer = :pro_id) AND voorwerpnummer = :pro_id2";
            $query4 = $conn->prepare($sql4);
            $query4->execute(array(":pro_id" => $product['voorwerpnummer'],":pro_id2" => $product['voorwerpnummer']));
            $row4 = $query4->fetch();
            $naam = $row4['gebruikersnaam'];
            
            
            $looptijd_einde_datum = $product['looptijd_einde_datum'];
            $looptijd_einde_tijd = $product['looptijd_einde_tijd'];
            $voorwerpnummer = $product['voorwerpnummer'];
            $verkoopprijs = $product['verkoopprijs'];
            $titel = $product['titel'];
            $koper = $product['koper_gebruikersnaam'];
            $verkoper = $product['verkoper_gebruikersnaam'];
            $huidigeTijd = date("Y-m-d H:i:s");
            $eindeVeiling = date('Y-m-d H:i:s', strtotime("$looptijd_einde_datum $looptijd_einde_tijd"));
            
            $date1 = strtotime("$huidigeTijd");  
            $date2 = strtotime("$eindeVeiling");  
            
            // Formulate the Difference between two dates 
            $diff = abs($date2 - $date1);
            $years = floor($diff / (365*60*60*24));  
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));  
            $days = floor(($diff - $years * 365*60*60*24 -  $months*30*60*60*24)/ (60*60*24)); 
            $hours = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24) 
            / (60*60));  
            $minutes = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24  
            - $hours*60*60)/ 60);
            $seconds = floor(($diff - $years * 365*60*60*24  - $months*30*60*60*24 - $days*60*60*24 
            - $hours*60*60 - $minutes*60));
            
            if ($date2 - $date1 <= 0){
                $sql = "UPDATE dbo.Voorwerp
                SET veiling_gesloten = 1, koper_gebruikersnaam = :user, verkoopprijs = :bid
                WHERE source = 0
                AND voorwerpnummer = $product[voorwerpnummer]";
                $query = $conn->prepare($sql);
                $query -> execute(array(
                  ":user" =>  $naam,  
                  ":bid" => $printBod 
                ));
            }
            
            if ($product['geblokkeerd'] == 1){
                if ($verkoper == $gebruikersnaam){
                    $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                    WHERE titel = 'Uw veiling geblokkeerd!'";
                    $query = $conn->query($sql);
                    $row = $query->fetch();
                    if(isset($row)){
                        $subject = "Uw veiling geblokkeerd!";
                        $text = "<h4>Goedendag $gebruikersnaam</h4>
                        <p>Het team van Eenmaal Andermaal heeft besloten om uw veiling $titel te blokkeren.</p>
                        <p>Dit hebben wij gedaan omdat de veiling niet aan de regels voldoet.</p>
                        <p>Mocht u in de toekomst meer veilingen openen die niet aan onze regels voldoen,
                        dan wordt uw account geblokkeerd.</p>";
                        if($row == false){
                            $sql2 = "INSERT INTO dbo.Meldingen
                            VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                            $query2 = $conn->prepare($sql2);
                            $query2->execute(array(
                                $_SESSION['gebruikersnaam']
                                )
                            );
                            sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                        }
                        else{
                            if($row['tekst'] != $text){
                                $sql2 = "INSERT INTO dbo.Meldingen
                                VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                $query2 = $conn->prepare($sql2);
                                $query2->execute(array(
                                    $_SESSION['gebruikersnaam']
                                    )
                                );
                            }
                        }
                    }
                }
                else if ($naam == $gebruikersnaam){
                    $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                    WHERE titel = 'Een veiling geblokkeerd!'";
                    $query = $conn->query($sql);
                    $row = $query->fetch();
                    if(isset($row)){
                        $subject = "Een veiling geblokkeerd!";
                        $text = "<h4>Goedendag $gebruikersnaam</h4>
                        <p>Het team van Eenmaal Andermaal heeft besloten om de veiling $titel te blokkeren.</p>
                        <p>Dit hebben wij gedaan omdat de veiling niet aan de regels voldoet.</p>
                        <p>Wij hopen dat u hier geen hinder van ondervind.</p>";
                        if($row == false){
                            $sql2 = "INSERT INTO dbo.Meldingen
                            VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                            $query2 = $conn->prepare($sql2);
                            $query2->execute(array(
                                $_SESSION['gebruikersnaam']
                                )
                            );
                            sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                        }
                        else{
                            if($row['tekst'] != $text){
                                $sql2 = "INSERT INTO dbo.Meldingen
                                VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                $query2 = $conn->prepare($sql2);
                                $query2->execute(array(
                                    $_SESSION['gebruikersnaam']
                                    )
                                );
                            }
                        }
                    }
                }
            }
            else{
                if ($product['veiling_gesloten'] == 1){
                    if ($koper == $gebruikersnaam){
                        $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                        WHERE titel = 'U heeft gewonnen!'";
                        $query = $conn->query($sql);
                        $row = $query->fetch();
                        if(isset($row)){
                            $subject = "U heeft gewonnen!";
                            $text = "<h4>Goedendag $gebruikersnaam</h4>
                            <p>U heeft de veiling gewonnen!</p>
                            <p>Met uw bod van $verkoopprijs euro heeft u de veiling $titel gewonnen.</p>
                            <p>Wij feliciteren u en wensen u veel plezier met uw product!</p>";
                            if($row == false){
                                $sql2 = "INSERT INTO dbo.Meldingen
                                VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                $query2 = $conn->prepare($sql2);
                                $query2->execute(array(
                                    $_SESSION['gebruikersnaam']
                                    )
                                );
                                sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                            }
                            else{
                                if($row['tekst'] != $text){
                                    $sql2 = "INSERT INTO dbo.Meldingen
                                    VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                    $query2 = $conn->prepare($sql2);
                                    $query2->execute(array(
                                        $_SESSION['gebruikersnaam']
                                        )
                                    );
                                }
                            }
                        }
                    }
                    else if ($verkoper == $gebruikersnaam){
                        $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                        WHERE titel = 'Uw veiling is beëindigd!'";
                        $query = $conn->query($sql);
                        $row = $query->fetch();
                        if(isset($row)){
                            $subject = "Uw veiling is beëindigd!";
                            $text = "<h4>Goedendag $gebruikersnaam</h4>
                            <p>De veiling $titel is beëindigd!</p>
                            <p>Het hoogste bod is afkomstig van $koper met $verkoopprijs euro.</p>
                            <p>Mocht u meer voorwerpen willen verkopen/veilen dan helpen wij u graag verder.</p>";
                            if($row == false){
                                $sql2 = "INSERT INTO dbo.Meldingen
                                VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                $query2 = $conn->prepare($sql2);
                                $query2->execute(array(
                                    $_SESSION['gebruikersnaam']
                                    )
                                );
                                sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                            }
                            else{
                                if($row['tekst'] != $text){
                                    $sql2 = "INSERT INTO dbo.Meldingen
                                    VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                    $query2 = $conn->prepare($sql2);
                                    $query2->execute(array(
                                        $_SESSION['gebruikersnaam']
                                        )
                                    );
                                }
                            }
                        }
                    }
                    else if ($naam == $gebruikersnaam){
                        $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                        WHERE titel = 'De veiling is beëindigd!'";
                        $query = $conn->query($sql);
                        $row = $query->fetch();
                        if(isset($row)){
                            $subject = "De veiling is beëindigd!";
                            $text = "<h4>Goedendag $gebruikersnaam</h4>
                            <p>De veiling $titel, waar u op heeft geboden is beëindigd!</p>
                            <p>Helaas, u heeft niet hoog genoeg geboden om te winnen.</p>
                            <p> $koper heeft met een bod van $verkoopprijs euro de veiling gewonnen.</p>";
                            if($row == false){   
                                $sql2 = "INSERT INTO dbo.Meldingen
                                VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                $query2 = $conn->prepare($sql2);
                                $query2->execute(array(
                                    $_SESSION['gebruikersnaam']
                                    )
                                );
                                sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                            }
                            else{
                                if($row['tekst'] != $text){
                                    $sql2 = "INSERT INTO dbo.Meldingen
                                    VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                    $query2 = $conn->prepare($sql2);
                                    $query2->execute(array(
                                        $_SESSION['gebruikersnaam']
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
                else{
                    if($naam == $gebruikersnaam){
                        if ($years >= 0 && $months >= 0 && $days >= 0 && $hours >= 0 && $minutes >= 0 && $seconds >= 0){
                            $sql = "SELECT titel, datum, tekst FROM dbo.Meldingen
                            WHERE titel = 'Nog 1 uur!'";
                            $query = $conn->query($sql);
                            $row = $query->fetch();
                            if(isset($row)){
                                $subject = "Nog 1 uur!";
                                $text = "<h4>Goedendag $gebruikersnaam</h4>
                                <p>Uw veiling $titel, verloopt over een uur.</p>
                                <p>Het hoogste bod op dit moment is $printBod euro.</p>
                                <p>Dit bod is uitgebracht door $naam</p>
                                <p>Mocht u meer voorwerpen willen verkopen/veilen dan helpen wij u graag verder.</p>";
                                if($row == false){
                                    $sql2 = "INSERT INTO dbo.Meldingen
                                    VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                    $query2 = $conn->prepare($sql2);
                                    $query2->execute(array(
                                        $_SESSION['gebruikersnaam']
                                        )
                                    );
                                    sendAuctionInfo($text, $subject, $emailadres, $huidigeTijd);
                                }
                                else{
                                    if($row['tekst'] != $text){
                                        $sql2 = "INSERT INTO dbo.Meldingen
                                        VALUES ('$_SESSION[gebruikersnaam]', '$subject', CURRENT_TIMESTAMP, '$text')";
                                        $query2 = $conn->prepare($sql2);
                                        $query2->execute(array(
                                            $_SESSION['gebruikersnaam']
                                            )
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($_SERVER['PHP_SELF'] == "/meldingen.php"){
            createNotification($koper, $verkoopprijs, $printBod, $naam, $conn);
        }
    }
}
?>