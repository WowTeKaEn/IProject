<?php
function sendMail($getal, $subject, $site, $gebruikersnaam){
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "ReversiveConcepts@gmail.com";
    $to;
    if(isset($_POST['emailadres'])){
        global $to;
        $to = $_POST['emailadres'];
    }
    else{
        global $to;
        $to = $_SESSION['emailadres'];
    }
    $message = "
    <html>
    <body>
    <h1>$subject</h1>
    <h2>Welkom bij eenmaal andermaal $gebruikersnaam</h2>
    <br>
    <p>Wij hopen dat u al uw veilingen kan vinden die u zoekt bij ons op de website.</p>
    <p>Als u uw registratie wilt voltooien klik dan op
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
    <p>en vul dan deze code in: ($getal)</p>
    <p>Wij wensen u veel bied plezier.</p>
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
//Voor als iemand zijn wachtwoord vergeten is
if(isset($_POST['versturen'])){
    $emailadres = $_POST['emailadres'];
    
    $sql = "SELECT emailadres, gebruikersnaam FROM dbo.Gebruiker
    WHERE emailadres = :emailadres";
    $query = $conn->prepare($sql);
    $query -> execute(array(
        ':emailadres' => $emailadres
    ));
    $row = $query -> fetch();
    $gebruikersnaam = $row['gebruikersnaam'];
    $getal = mt_rand(111111,999999);
    $_SESSION['naam'] = $gebruikersnaam;
    $_SESSION['code'] = $getal;
    $subject = "Wachtwoord aanpassen";
    $site = "href='https://iproject26.icasites.nl/verificatieWachtwoord.php";
    sendMail($getal, $subject, $site, $gebruikersnaam);
    makeModals("Succes", "Er is een e-mail verstuurd naar het volgende e-mailaders: $emailadres");
}
?>