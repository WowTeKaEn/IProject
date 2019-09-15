<?php
require_once("SQLSrvConnect.php");
/*if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}*/

session_start();
function createItem($img, $name, $discription){
    echo "
    <article>
    <a href='detailpagina.php' class='shadow-lg d-flex flex-column align-items-center mx-3 my-4 item background-item-color'>
    <div class=' d-flex justify-content-center align-items-center img-container'>
    <img  src='img/".$img."' alt='".$name."'  class='img'>
    </div>
    <div class='d-flex flex-fill flex-column item-inner'>
    <div class='w-100 highest-bid'>
    <p class='text-wrap text-center py-1 mb-0'>Hoogste bod: <strong>$10</strong></p>
    <p>Sluit over:<strong> 00:00:12</strong></p>
    </div>
    <div class='h-100 w-100 p-3'>
    <h6>".$name."</h6>
    <p class='d-flex flex-fill text-wrap item-inner'>".$discription."</p>
    </div>
    </div>
    </a>
    <div class='mt-auto btn bid-btn text-nowrap'>Bied nu!</div>
    </article>
    ";
}

function checkProducts(){
    global $conn;
    $query = "SELECT * FROM Voorwerp
    WHERE veiling_gesloten = 0 AND
    CONVERT(datetime,CAST(looptijd_einde_datum AS datetime) + CAST(looptijd_einde_tijd AS datetime)) < CONVERT(datetime,CURRENT_TIMESTAMP)";

    $number_array = [];
    foreach($conn->query($query) as $row){
        array_push($number_array,$row['voorwerpnummer']);
    }
}
// checkProducts();

/*
Good product function
*/
function createItemProper($pro_id, $img, $name, $price, $sec_diff, $geblokkeerd){
    if($geblokkeerd == 0){
    echo "
    <article>
    <a href='detailpagina.php?pro_id=$pro_id' class='shadow-lg d-flex flex-column align-items-center mx-3 my-4 item background-item-color'>
    <div class=' d-flex justify-content-center align-items-center img-container'>
    <img src='".$img."' alt='Foto ".$name."'  class='img-fluid h-100'>
    </div>
    <div class='d-flex flex-fill flex-column item-inner'>
    <div class='w-100 highest-bid'>
    <p class='text-wrap text-center py-1 mb-0'>Hoogste bod: <strong>&euro; ". number_format($price, 2, ',', '.') ."</strong></p>
    <p>Sluit over: <strong><span id='timer' data-time='". $sec_diff ."'></span></strong></p>
    </div>
    <div class='h-100 w-100 p-3'>
    <h6>".$name."</h6>
    </div>
    </div>
    <div class='mt-auto btn bid-btn text-nowrap'>Bied nu!</div>
    </a>
    </article>
    ";
    }
    else{
        echo "
        <article>
        <a class='shadow-lg d-flex flex-column align-items-center mx-3 my-4 item background-item-color'>
        <div class=' d-flex justify-content-center align-items-center img-container'>
        <p>Product niet gevonden!</p>
        </div>
        <div class='d-flex flex-fill flex-column item-inner'>
        <div class='w-100 highest-bid'>
        <p class='text-wrap text-center py-1 mb-0'>Product niet gevonden!</p>
        <p></p>
        </div>
        <div class='h-100 w-100 p-3'>
        <h6>Product niet gevonden!</h6>
        </div>
        </div>
        <button class='mt-auto btn bid-btn text-nowrap'>Product niet gevonden!</button>
        </a>
        </article>
        ";
    }
}

/* 
Pulls all catagories from database and loads them into a global variable 
*/
function makeCategories($conn){
    if(empty($GLOBALS['Categories'])){
        $categories = [[]];
        $count = 0;
        try {
            foreach($conn->query('SELECT * from Rubriek') as $row) {
                $categories[$count] = $row;
                $count ++;
            }
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        $GLOBALS['Categories'] = $categories;
    }
}



/*
Uses catagories global to make catagory tree with only the top categories
*/
function makeCategoryTree($conn){
    makeCategories($conn);
    $subCategories = getSubCategory($GLOBALS['Categories'][0],$GLOBALS['Categories']);
    makeSingleTree($subCategories,$GLOBALS['Categories'],$GLOBALS['Categories'][0]);
}


/*
Creates tree with only the top parent categories
*/
function makeSingleTree($subCategories,$categories,$parent){
    foreach($subCategories as $subCategory){           
    // $subsubCategories = getSubCategory($subCategory ,$categories);
    // if(!empty($subsubCategories)){
    //     echo "<option data-tokens='".$parent['rubrieknaam']."'>".$subCategory['rubrieknaam']."</option>";   
    //     makeSingleTree($subsubCategories,$categories,$subCategory);
    // }else{
        if(isset($_GET['cat_id'])){
            if($subCategory['rubrieknummer'] == $_GET['cat_id']){
                echo "<option selected value='".$subCategory['rubrieknummer']."' data-tokens='".$parent['rubrieknaam']."'>".$subCategory['rubrieknaam']."</option>";
            }else{
                echo "<option value='".$subCategory['rubrieknummer']."' data-tokens='".$parent['rubrieknaam']."'>".$subCategory['rubrieknaam']."</option>";
            }
        }else{
            echo "<option value='".$subCategory['rubrieknummer']."' data-tokens='".$parent['rubrieknaam']."'>".$subCategory['rubrieknaam']."</option>";
        }
        // }     
    } 
}

function printSubCategories($category){
    if ($category['rubrieknummer'] != -1) {
        echo "<h2>".$category['rubrieknaam']."</h2>";
    }else{
        echo "<h2>Alle categorieën</h2>";
    }
    $subCategories = getSubCategory($category,$GLOBALS['Categories']);
    if(empty($subCategories)){
        echo "Deze categorie bevat geen subcategorieën";
    }
    foreach($subCategories as $subCategory){
        echo "<a class='w-100 float-left' href='resultatenpagina.php?cat_id=".$subCategory['rubrieknummer']."'><span>".$subCategory['rubrieknaam']."</span></a><br>";
    }
}
function getSubCategory($category,$categories){
    $returnCategories = [];
    $count = 0;
    foreach($categories as $value){
        if($value['parent_rubriek'] == $category['rubrieknummer']){
            $returnCategories[$count] = $value;
            $count++;
        }
    }
    return $returnCategories;
}

function getCategoryFromID($ID,$conn){
    $query = $conn->prepare("SELECT * FROM Rubriek WHERE rubrieknummer = :id");
    $query->execute(array(
        ':id' => $ID
    ));
    $category = $query->fetch();
    return $category;
}

/* 
Function:
createBreadcrumbs
---
Returns:
A string containing the breadcrumbs
---
Parameter(s):
@page_title - Takes the title of a page if theres no category or product selected.
--
Made by:
Danny Teunissen (623499) 
09-05-2019
*/
function createBreadcrumbs($page_title = ""){
    if($page_title != "index"){
        $html_string = "";
        global $conn;
        if(isset($_GET['cat_id'])){$cat_id = $_GET['cat_id'];}
        if(isset($_GET['pro_id'])){$product_id = $_GET['pro_id'];}
        if(isset($product_id)){
            $sql     = "SELECT V.titel,VIR.rubrieknummer FROM Voorwerp V, VoorwerpInRubriek VIR WHERE V.voorwerpnummer = ? AND V.voorwerpnummer = VIR.voorwerpnummer";
            $query   = $conn->prepare($sql);
            $query->execute(array($_GET['pro_id']));
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            if (sizeof($result) > 0) {
                $cat_id = $result[0]['rubrieknummer'];
                $product_titel = $result[0]['titel'];
                $id_to_look_for = $cat_id;
                $main_cat_found = false;
                $sql     = "SELECT rubrieknummer,rubrieknaam,parent_rubriek FROM [iproject26].[dbo].[Rubriek]";
                $query   = $conn->query($sql);
                $result  = $query->fetchAll(PDO::FETCH_ASSOC);
                $i = 0;
                while ($i < sizeof($result)) {
                    if ($result[$i]['rubrieknummer'] == $id_to_look_for) {
                        $html_string = "<li class=\"breadcrumb-item\"><a href=\"resultatenpagina.php?cat_id=".$result[$i]['rubrieknummer']."\">".$result[$i]['rubrieknaam']."</a></li>". $html_string;
                        if ($result[$i]['parent_rubriek'] == -1) {
                            $html_string = "<li class=\"breadcrumb-item\"><a href=\"resultatenpagina.php?cat_id=-1\">Alle producten</a></li>". $html_string;
                            break;
                        } else {
                            $id_to_look_for = $result[$i]['parent_rubriek'];
                            array_splice($result, $i, 1);
                            $i = -1;
                        }
                    }
                    $i++;
                }
                $html_string .= "<li class=\"breadcrumb-item active\" aria-current=\"page\">".$product_titel."</li>";
            }
        } else if(isset($cat_id)) {
            $id_to_look_for = 0;
            $main_cat_found = false;
            $sql     = "SELECT rubrieknummer,rubrieknaam,parent_rubriek FROM [iproject26].[dbo].[Rubriek]";
            $query   = $conn->query($sql);
            $result  = $query->fetchAll(PDO::FETCH_ASSOC);
            $i = 0;
            while($i < sizeof($result)){
                if($result[$i]['rubrieknummer'] == $id_to_look_for){
                    $html_string = "<li class=\"breadcrumb-item\"><a href=\"?cat_id=".$result[$i]['rubrieknummer']."\">".$result[$i]['rubrieknaam']."</a></li>". $html_string;
                    if($result[$i]['parent_rubriek'] == -1){
                        $html_string = "<li class=\"breadcrumb-item\"><a href=\"?cat_id=-1\">Alle producten</a></li>". $html_string;
                        break;
                    }else{
                        $id_to_look_for = $result[$i]['parent_rubriek'];
                        array_splice($result,$i,1);
                        $i = -1;
                    }
                }else if($main_cat_found == false && $result[$i]['rubrieknummer'] == $cat_id){
                    $main_cat_found = true;
                    if ($cat_id != -1) {
                        $html_string .= "<li class=\"breadcrumb-item active\" aria-current=\"page\">".$result[$i]['rubrieknaam']."</li>";
                    }else{
                        $html_string .= "<li class=\"breadcrumb-item active\" aria-current=\"page\">Alle producten</li>";
                    }
                    if($result[$i]['parent_rubriek'] == -1){
                        $html_string = "<li class=\"breadcrumb-item\"><a href=\"?cat_id=-1\">Alle producten</a></li>". $html_string;
                        break;
                    }else{
                        $id_to_look_for = $result[$i]['parent_rubriek'];
                        array_splice($result,$i,1);
                        $i = -1;
                    }
                }
                $i++;
            }
        }else if(isset($page_title)){
            $html_string .= "<li class=\"breadcrumb-item active\" aria-current=\"page\">".$page_title."</li>";
        }
        if(isset($_GET['zoek_veld'])){
            if ($_GET['zoek_veld'] != "") {
                $html_string .= "<li class=\"breadcrumb-item active\" aria-current=\"page\">Zoekopdracht : <i>".$_GET['zoek_veld']."</i></li>";
            }
        }
        $html_string = "<nav aria-label=\"breadcrumb\"><ol class=\"breadcrumb\"><li class=\"breadcrumb-item\"><a href=\"index.php\">Home</a></li>". $html_string ."</ol></nav>";
        return $html_string;
    }
}

function printBackButton($category, $conn){
    $parent = getParent($category, $conn);
    if($parent['rubrieknummer'] === 0 || empty($parent['rubrieknummer'])){
        echo "<div class='mt-3'><a class='mx-3' href='index.php'>&laquo; Terug</a></div>";
    }else{
        echo "<div class='mt-3'><a class='mx-3' href='resultatenpagina.php?cat_id=".$parent['rubrieknummer']."'>&laquo; Terug</a></div>";
    }
}

function getParent($category, $conn){
    $query = $conn->prepare("SELECT * FROM Rubriek WHERE rubrieknummer = :parent");
    $query->execute(array(
        ':parent' => $category['parent_rubriek']
    ));
    $category = $query->fetch();
    return $category;
}

function resultsFor(){
    if (!empty($_GET['search'])) {
        echo "<p class='text-center display-4 mt-3' style='font-size: 2rem;'>Resultaten weergeven voor: <strong>".$_GET['search']."</strong></p>";
    } else {
        echo "<p class='text-center display-4 mt-3' style='font-size: 2rem;'>Resultaten</p>";
    }
}



function getLanden($conn){
    $query = "SELECT DISTINCT NAAM_LAND FROM tblIMAOLand WHERE EINDDATUM >= GETDATE() OR EINDDATUM IS NULL";
    $sql = $conn->query($query);
    $landen = $sql->fetchAll();
    return $landen;
}
//Voor als je een nieuw wachtwoord ingevult hebt
if(isset($_POST['aanpassen'])){
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $password = $_POST['wachtwoord'];
    $password_confirm = $_POST['wachtwoord_herhalen'];
    $newPass = password_hash($password,PASSWORD_DEFAULT);
    
    if($password == $password_confirm){
        $sql     = "UPDATE dbo.Gebruiker
        SET wachtwoord = '$newPass'
        WHERE gebruikersnaam = :gebruikersnaam";
        $query = $conn->prepare($sql);
        $query->execute(array(
            ':gebruikersnaam' => $gebruikersnaam
            )
        );
        $_SESSION['aangepast'] = true;
        header('Location: index.php');
    } else {
        makeModals("Error", "De wachtwoorden zijn niet gelijk!");
    }
}
//Voor het valideren van een nieuw wachtwoord voor een gebruiker 
if (isset($_POST['Nwachtwoord'])) {
    $code = $_POST['code'];
    $codeMail = $_SESSION['code'];
    if ($code == $codeMail) {
        header('Location: nieuwWachtwoord.php');
    }
    else{
        makeModals("Error", "De codes komen niet overeen!");
    }
}
//Voor het valideren van een gebruiker als deze zich net geregistreerd heeft
if(isset($_POST['valideren'])){
    $naam = $_SESSION['gebruikersnaam'];
    $code = $_POST['code'];
    $codeMail = $_SESSION['code'];
    if($code == $codeMail){
        $sql     = "UPDATE dbo.Gebruiker
        SET verificatie = 1
        WHERE gebruikersnaam = :gebruikersnaam";
        $query   = $conn->prepare($sql);
        $query   -> execute(array(
            ':gebruikersnaam' => $naam
            )
        );
        $_SESSION['gevalideerd'] = true;
        header('Location: index.php');
    } else {
        makeModals("Error", "De codes komen niet overeen!");
    }
}



function makeModals($title, $message){
    echo "<div class='modal fade' id='message' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
    <div class='modal-content'>
    <div class='modal-header'>
    <h5 class='modal-title' id='exampleModalLabel'>$title</h5>
    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
    </button>
    </div>
    <div class='modal-body'>
    $message
    </div>
    <div class='modal-footer'>
    <button type='button' class='btn btn-block' data-dismiss='modal'>Sluiten</button>
    </div>
    </div>
    </div>
    </div>";
}
//Voor het blokkeren van een account
if(isset($_POST['blokkerenG'])){
    $gebruikersnaam = $_POST['gebruikersnaam'];    
    $sql = "UPDATE dbo.Gebruiker
    SET geblokkeerd = 1
    WHERE gebruikersnaam = :gebruikersnaam";
    $query = $conn->prepare($sql);
    $query->execute(array(
        ':gebruikersnaam' => $gebruikersnaam
        )
    );
    makeModals("Success", "De gebruiker is geblokkeerd!");
}
//Voor het blokkeren van een veiling
if(isset($_POST['blokkerenV'])){
    $titel = $_POST['titel'];    
    $sql = "UPDATE dbo.Voorwerp
    SET geblokkeerd = 1
    WHERE titel = :titel";
    $query = $conn->prepare($sql);
    $query->execute(array(
        ':titel' => $titel
        )
    );
    makeModals("Success", "De veiling is geblokkeerd!");
}

function createThreeOldestItems($conn){
    $query = "SELECT TOP 3 voorwerpnummer, titel, dbo.FN_GeefPrijs(voorwerpnummer) AS 'startprijs', thumbnail, looptijd_einde_datum, looptijd_einde_tijd, source, geblokkeerd FROM Voorwerp 
              WHERE looptijd_einde_datum >= GETDATE() ORDER BY looptijd_einde_datum, looptijd_einde_tijd ASC";
    $sql = $conn->query($query);
    $fetch = $sql->fetchAll();
    foreach($fetch as $item){
        if($item['source'] == 1) {
            $item['thumbnail'] = 'https://iproject26.icasites.nl/thumbnails/' . $item['thumbnail'];
        } else {
            $item['thumbnail'] = 'https://iproject26.icasites.nl/UploadedImages/' . $item['thumbnail'];
        }

        $date = date_create($item['looptijd_einde_datum'] . "  " . $item['looptijd_einde_tijd']);
        $sec_diff = date_diff(date_create(), $date);
        $secs = 0;
        $secs += $sec_diff->format('%a') * 24 * 60 * 60;
        $secs += $sec_diff->format('%h') * 60 * 60;
        $secs += $sec_diff->format('%i') * 60;
        $secs += $sec_diff->format('%s');
        $secs = $secs * 1000;
        createItemProper($item['voorwerpnummer'], $item['thumbnail'], $item['titel'], $item['startprijs'], $secs, $item['geblokkeerd']);
    }
}

function createRandomItems($conn){
    $query = "SELECT TOP 20 voorwerpnummer, titel, dbo.FN_GeefPrijs(voorwerpnummer) AS 'startprijs', thumbnail, looptijd_einde_datum, looptijd_einde_tijd, source, geblokkeerd 
              FROM Voorwerp WHERE looptijd_einde_datum > GETDATE() ORDER BY NEWID()";
    $sql = $conn->query($query);
    $fetch = $sql->fetchAll();
    foreach($fetch as $item) {
        if($item['source'] == 1) {
            $item['thumbnail'] = 'https://iproject26.icasites.nl/thumbnails/' . $item['thumbnail'];
        } else {
            $item['thumbnail'] = 'https://iproject26.icasites.nl/UploadedImages/' . $item['thumbnail'];
        }

        $date = date_create($item['looptijd_einde_datum'] . "  " . $item['looptijd_einde_tijd']);
        $sec_diff = date_diff(date_create(), $date);
        $secs = 0;
        $secs += $sec_diff->format('%a') * 24 * 60 * 60;
        $secs += $sec_diff->format('%h') * 60 * 60;
        $secs += $sec_diff->format('%i') * 60;
        $secs += $sec_diff->format('%s');
        $secs = $secs * 1000;
        createItemProper($item['voorwerpnummer'], $item['thumbnail'], $item['titel'], $item['startprijs'], $secs, $item['geblokkeerd']);
    }
}
?>  
