<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.10/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="styles/stylesheet.css" />
<title>Eenmaal Andermaal</title>
</head>
<body>
<?php
include 'functions.php';
include 'nav.php';
include 'functions/functions_profiel_pagina.php';
echo createBreadCrumbs("Verkoper worden");
if(!isset($_SESSION['loggedin'])){
    header('Location: index.php');
}
?>
<div class="container flex-fill">
    <div id="login-row" class="row justify-content-center align-items-center">
        <div id="login-column" class="col-md-6">
            <div id="login-box" class="col-md-12 p-3">
                <div class="card">
                    <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Verkoper worden</h4>
                        <form method="POST">
                            <div class="form-group">
                                <?php
                                $gebruikersnaam = $_SESSION['gebruikersnaam'];
                                if(isset($_POST['tussenstap'])){
                                    if(isset($_POST['controle_optie_naam'])){       
                                        $controle = $_POST['controle_optie_naam'];
                                        $keuzeBank = $_POST['banknaam'];
                                        if(($controle == "Niks") || ($keuzeBank == "Niks")){
                                            makeModals("Error", "Je bent vergeten om bepaalde opties te selecteren");
                                            echo "<div class='form-group'>
                                            <label>Gebruikersnaam</label>
                                            <input 
                                            name='gebruikersnaam' 
                                            class='form-control' 
                                            value='$gebruikersnaam'
                                            type='text' 
                                            id='gebruikersnaam'
                                            required
                                            readonly>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Bank</label>
                                            <select id='banknaam' name='banknaam' class='form-control'>
                                            <option value='Niks'>...</option>
                                            <option>​ABN Amro</option>
                                            <option>ASN Bank</option>
                                            <option>ING</option>
                                            <option>Rabobank</option>
                                            <option>SNS Bank</option>
                                            <option>Triodos Bank</option>
                                            <option>Knab</option>
                                            </select>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Rekeningnummer</label>
                                            <input
                                            name='rekeningnummer'
                                            class='form-control' 
                                            placeholder='6473654' 
                                            type='number'
                                            id='rekeningnummer'
                                            required>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Controle via</label>
                                            <select id='controle_optie_naam' name='controle_optie_naam' class='form-control'>
                                            <option value='Niks'>...</option>
                                            <option value='Creditcard'>Creditcard</option>
                                            <option value='Post'>Post</option>
                                            </select>
                                            </div>
                                            <div class='form-group'>
                                            <input type='submit' class='btn btn-block' value='Verkoper worden' name='tussenstap'>
                                            </div>"; 
                                        }
                                        else {
                                            echo "<div class='form-group'>
                                            <label>Gebruikersnaam</label>
                                            <input
                                            class='form-control' 
                                            value='$gebruikersnaam'
                                            name='gebruikersnaam'
                                            required
                                            readonly>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Bank</label>
                                            <input
                                            class='form-control' 
                                            value=" . $_POST['banknaam'] . "
                                            name='banknaam'
                                            required>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Rekeningnummer</label>
                                            <input
                                            class='form-control' 
                                            value=" . $_POST['rekeningnummer'] . "
                                            name='rekeningnummer'
                                            required>
                                            </div>";
                                            
                                            echo "<div class='form-group'>
                                            <label>Controle via</label>
                                            <input
                                            class='form-control' 
                                            value=" . $_POST['controle_optie_naam'] . "
                                            name='controle_optie_naam'
                                            required>
                                            </div>";
                                            
                                            if($controle == "Creditcard"){
                                                echo"<div class='form-group'>
                                                <label>Creditcardnummer</label>
                                                <input
                                                name='creditcardnummer'
                                                class='form-control' 
                                                placeholder='6473654' 
                                                type='number'
                                                id='creditcardnummer'
                                                required>
                                                </div>";
                                                echo "<div class='form-group'>
                                                <input type='submit' class='btn btn-block' value='Verkoper worden' name='verkoper'>
                                                </div>";
                                            }
                                            else if($controle == "Post"){
                                                echo "<div class='form-group'>
                                                <input type='submit' class='btn btn-block' value='Verkoper worden' name='post'>
                                                </div>";
                                                makeModals("Succes", "Klik opnieuw op de knop om het verkoper worden te bevestigen.<br>
                                                U zal een brief ontvangen met daarop een code,<br>
                                                voer deze in op uw profielpagina en dan bent u een verkoper.");
                                            }
                                        }
                                    }
                                }
                                else {
                                    echo "<div class='form-group'>
                                    <label>Gebruikersnaam</label>
                                    <input 
                                    name='gebruikersnaam' 
                                    class='form-control' 
                                    value='$gebruikersnaam'
                                    type='text' 
                                    id='gebruikersnaam'
                                    required
                                    readonly>
                                    </div>";
                                    
                                    echo "<div class='form-group'>
                                    <label>Bank</label>
                                    <select id='banknaam' name='banknaam' class='form-control'>
                                    <option value='Niks'>...</option>
                                    <option>​ABN Amro</option>
                                    <option>ASN Bank</option>
                                    <option>ING</option>
                                    <option>Rabobank</option>
                                    <option>SNS Bank</option>
                                    <option>Triodos Bank</option>
                                    <option>Knab</option>
                                    </select>
                                    </div>";
                                    
                                    echo "<div class='form-group'>
                                    <label>Rekeningnummer</label>
                                    <input
                                    name='rekeningnummer'
                                    class='form-control' 
                                    placeholder='6473654' 
                                    type='number'
                                    id='rekeningnummer'
                                    required>
                                    </div>";
                                    
                                    echo "<div class='form-group'>
                                    <label>Controle via</label>
                                    <select id='controle_optie_naam' name='controle_optie_naam' class='form-control'>
                                    <option value='Niks'>...</option>
                                    <option value='Creditcard'>Creditcard</option>
                                    <option value='Post'>Post</option>
                                    </select>
                                    </div>
                                    <div class='form-group'>
                                    <input type='submit' class='btn btn-block' value='Verkoper worden' name='tussenstap'>
                                    </div>";
                                }
                                ?>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'footer.php';
include 'scripts.html'
?>
</body>