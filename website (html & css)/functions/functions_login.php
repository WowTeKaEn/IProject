<?php
include_once 'functions/functions_mail.php';
if(isset($_POST['inloggen'])){
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $password = $_POST['wachtwoord'];
    
    $sql = "SELECT gebruikersnaam, wachtwoord, verificatie, emailadres, geblokkeerd FROM dbo.Gebruiker
    WHERE gebruikersnaam = :gebruikersnaam";
    $query = $conn->prepare($sql);
    $query -> execute(array(
        ':gebruikersnaam' => $gebruikersnaam
    ));    
    $row = $query -> fetch();
    if(password_verify($password, $row['wachtwoord'])){
        if($row['geblokkeerd'] == 1){
            $_SESSION['geblokkeerd'] = true;
            makeModals("Error", "Uw account is geblokkeerd! Kijk in de naar u gestuurde mail voor meer informatie.");
            ini_set( 'display_errors', 1 );
            error_reporting( E_ALL );
            $from = "ReversiveConcepts@gmail.com";
            $to = $row['emailadres'];
            $subject = "Account geblokkeerd";
            $message = "
            <html>
            <body>
            <h1>$subject</h1>
            <br>
            <p>Beste $gebruikersnaam,</p>
            <p>Het team van Eenmaal Andermaal heeft besloten om uw account te verwijderen.</p>
            <p>Dit hebben wij gedaan omdat u zich niet aan de regels heeft gehouden.</p>
            <p>Als u gebruik wilt blijven maken van onze site dan kunt u zich opnieuw aanmelden door te klikken op
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
        else{
            $_SESSION['geblokkeerd'] = false;
            if($row['verificatie'] == 0){
                makeModals("Verificatie", "Er is een mail gestuurd naar het voor u geregistreede
                e-mailadres. Volg de instructies in de mail om uw account te verifiëren.");
                $getal = mt_rand(111111, 999999);
                $_SESSION['code'] = $getal;
                $subject = "Account verificatiecode";
                $site = "href='https://iproject26.icasites.nl/verificatieGebruiker.php";
                $_SESSION['emailadres'] = $row['emailadres'];
                sendMail($getal, $subject, $site, $gebruikersnaam);
                $_SESSION['gebruikersnaam'] = $gebruikersnaam;
                $_SESSION['loggedin'] = true;
            }
            else{
                $_SESSION['gebruikersnaam'] = $gebruikersnaam;
                $_SESSION['loggedin'] = true;
                header('Location: index.php');
            }
        }
    }
    else{
        makeModals("Error", "De gebruikersnaam of het wachtwoord is onjuist!");
    }
}
?>