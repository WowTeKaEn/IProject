<?php
function getProducts($cat_id,$page,$conn){
    $filter_string = "";
    if(!is_numeric($cat_id)){
        $cat_id = -1;
    }
    if(!is_numeric($page) || $page < 1){
        $page = 1;
    }
    $filter_string = "&cat_id=". $cat_id;
    $search_string = "";
    $price_filter_string = "";
    $search_array;
    if(isset($_GET['zoek_veld'])){
        if ($_GET['zoek_veld'] != "") {
            $search_string = str_replace(","," ",$_GET['zoek_veld']);
            $search_array = explode(" ",$search_string);
            $search_string = "AND (";
            for($i = 0; $i < count($search_array); $i++){
                $search_string .='V.titel LIKE ? ';
                if($i+1 < count($search_array)){
                    $search_string .= ' OR ';
                }
            }
            $search_string .= ") ";
        }
    }
    $waardes;
    if(isset($_GET['price_slider'])){
        if($_GET['price_slider'] != ""){
            $waardes_string = str_replace("[","",$_GET['price_slider']);
            $waardes_string = str_replace("]","",$waardes_string);
            $waardes = explode(",",$waardes_string);
            if(is_numeric($waardes[0]) && is_numeric($waardes[1])){
                $price_filter_string .= " AND dbo.FN_GeefPrijs(V.voorwerpnummer) >= ? AND dbo.FN_GeefPrijs(V.voorwerpnummer) <= ? ";
            }
        }
    }
    $product_limit = 16;
    $offset = $product_limit * ($page -1);
    $categories = filterCategories($cat_id);
    $inQuery = implode(',', array_fill(0, count($categories), '?'));
    
    /* Producten ophalen die onder de rubrieknummers vallen */
    $sql = "SELECT V.voorwerpnummer, V.titel, dbo.FN_GeefPrijs(V.voorwerpnummer) AS 'prijs', V.thumbnail, V.source, V.looptijd_einde_datum, V.looptijd_einde_tijd, V.geblokkeerd
            FROM dbo.Voorwerp V
            INNER JOIN dbo.VoorwerpInRubriek VIR 
            ON VIR.voorwerpnummer = V.voorwerpnummer
            WHERE veiling_gesloten = 0 AND geblokkeerd = 0 AND VIR.rubrieknummer IN(". $inQuery .") ".$search_string." ". $price_filter_string ."
            ORDER BY V.voorwerpnummer 
            OFFSET ". $offset ." ROWS 
            FETCH NEXT ". $product_limit ." ROWS ONLY";
    $query = $conn->prepare($sql);
    for($i = 0; $i < count($categories); $i++){
        $query->bindValue($i+1, $categories[$i]);
    }
    if (isset($_GET['zoek_veld'])) {
        if ($_GET['zoek_veld'] != "") {
            for($i = 0; $i < count($search_array); $i++){
                $query->bindValue(count($categories) + $i + 1, "%".$search_array[$i]."%");
            }
        }
    }
    if(isset($waardes)){
        if ($waardes != "") {
            $count = count($categories);
            if(isset($search_array)){
                $count += count($search_array);
            }
            $query->bindValue($count + 1, $waardes[0]);
            $query->bindValue($count + 2, $waardes[1]);
        }
    }
    $query -> execute();
    $row = $query -> fetchAll(PDO::FETCH_ASSOC);
    if(count($row) < 1){
        echo"<h3 class='text-center w-100 mt-3'>Geen producten gevonden</h3>";
    }else{
        foreach ($row as $product) {
            $date = date_create($product['looptijd_einde_datum'] ."  ". $product['looptijd_einde_tijd']);
            $sec_diff = date_diff(date_create(),$date);
            $secs = 0;
            $secs += $sec_diff->format('%a') * 24 * 60 * 60;
            $secs += $sec_diff->format('%h') * 60 * 60;
            $secs += $sec_diff->format('%i') * 60;
            $secs += $sec_diff->format('%s');
            $secs = $secs * 1000;
            if(date_create() > $date){
                $secs -= $secs * 2;
            }
            $img_string = "". $product['thumbnail'];
            if($product['source'] == 1){
                $img_string = 'https://iproject26.icasites.nl/thumbnails/'. $img_string;
            }else{
                $img_string = 'https://iproject26.icasites.nl/uploadedImages/'. $img_string;
            }
            createItemProper($product['voorwerpnummer'],$img_string, $product['titel'], $product['prijs'],$secs, $product['geblokkeerd']);
        }
        $sql = "SELECT COUNT(*) AS 'aantal', MIN(dbo.FN_GeefPrijs(V.voorwerpnummer)) AS 'min_prijs', MAX(dbo.FN_GeefPrijs(V.voorwerpnummer)) AS 'max_prijs'
            FROM dbo.Voorwerp V 
            INNER JOIN dbo.VoorwerpInRubriek VIR 
            ON VIR.voorwerpnummer = V.voorwerpnummer
            WHERE veiling_gesloten = 0 AND geblokkeerd = 0 AND VIR.rubrieknummer IN(". $inQuery .") ". $search_string ." ".$price_filter_string;
        $query = $conn->prepare($sql);
        for ($i = 0; $i < count($categories); $i++) {
            $query->bindValue($i+1, $categories[$i]);
        }
        if (isset($_GET['zoek_veld'])) {
            if ($_GET['zoek_veld'] != "") {
                for($i = 0; $i < count($search_array); $i++){
                    $query->bindValue(count($categories) + $i + 1, "%".$search_array[$i]."%");
                }
            }
        }
        if(isset($waardes)){
            if ($waardes != "") {
                $count = count($categories);
                if(isset($search_array)){
                    $count += count($search_array);
                }
                $query->bindValue($count + 1, $waardes[0]);
                $query->bindValue($count + 2, $waardes[1]);
            }
        }
        $query -> execute();
        $row = $query -> fetchAll(PDO::FETCH_ASSOC);
        $max_aantal = $row[0]['aantal'];
        echo "<form><input type='hidden' value='".$row[0]['min_prijs']."' id='min_prijs' /><input type='hidden' value='".$row[0]['max_prijs']."' id='max_prijs' /></form>";
        if($product['geblokkeerd'] == 0){
            loadPagination($page, CEIL($max_aantal / $product_limit), $filter_string);
        }
    }
}

function getAllProducts($page,$conn){
    $filter_string = "";
    if(!is_numeric($page) || $page < 1){
        $page = 1;
    }
    $search_string = "";
    $price_filter_string = "";
    $search_array;
    if(isset($_GET['zoek_veld'])){
        if ($_GET['zoek_veld'] != "") {
            $search_string = str_replace(","," ",$_GET['zoek_veld']);
            $search_array = explode(" ",$search_string);
            $search_string = "AND (";
            for($i = 0; $i < count($search_array); $i++){
                $search_string .='V.titel LIKE ? ';
                if($i+1 < count($search_array)){
                    $search_string .= ' OR ';
                }
            }
            $search_string .= ") ";
        }
    }
    $waardes;
    if(isset($_GET['price_slider'])){
        if($_GET['price_slider'] != ""){
            $waardes_string = str_replace("[","",$_GET['price_slider']);
            $waardes_string = str_replace("]","",$waardes_string);
            $waardes = explode(",",$waardes_string);
            if(is_numeric($waardes[0]) && is_numeric($waardes[1])){
                $price_filter_string .= " AND dbo.FN_GeefPrijs(V.voorwerpnummer) >= ? AND dbo.FN_GeefPrijs(V.voorwerpnummer) <= ? ";
            }
        }
    }
    $product_limit = 16;
    $offset = $product_limit * ($page -1);
    
    /* Producten ophalen die onder de rubrieknummers vallen */
    $sql = "SELECT V.voorwerpnummer, V.titel, dbo.FN_GeefPrijs(V.voorwerpnummer) AS 'prijs', V.thumbnail, V.source, V.looptijd_einde_datum , V.looptijd_einde_tijd, V.geblokkeerd
            FROM dbo.Voorwerp V 
            WHERE veiling_gesloten = 0 AND geblokkeerd = 0 ". $search_string ." ".$price_filter_string." 
            ORDER BY V.voorwerpnummer 
            OFFSET ". $offset ." ROWS 
            FETCH NEXT ". $product_limit ." ROWS ONLY";
    $query = $conn->prepare($sql);
    if (isset($_GET['zoek_veld'])) {
        if ($_GET['zoek_veld'] != "") {
            for($i = 0; $i < count($search_array); $i++){
                $query->bindValue($i + 1, "%".$search_array[$i]."%");
            }
        }
    }
    if(isset($waardes)){
        if ($waardes != "") {
            $count = 0;
            if(isset($search_array)){
                $count += count($search_array);
            }
            $query->bindValue($count + 1, $waardes[0]);
            $query->bindValue($count + 2, $waardes[1]);
        }
    }
    $query -> execute();
    $row = $query -> fetchAll(PDO::FETCH_ASSOC);
    if(count($row) < 1){
        echo "<h3 class='text-center w-100 mt-3'>Geen producten gevonden</h3>";
    }else{
        foreach ($row as $product) {
            $date = date_create($product['looptijd_einde_datum'] ."  ". $product['looptijd_einde_tijd']);
            $sec_diff = date_diff(date_create(),$date);
            $secs = 0;
            $secs += $sec_diff->format('%a') * 24 * 60 * 60;
            $secs += $sec_diff->format('%h') * 60 * 60;
            $secs += $sec_diff->format('%i') * 60;
            $secs += $sec_diff->format('%s');
            $secs = $secs * 1000;
            if(date_create() > $date){
                $secs -= $secs * 2;
            }
            $img_string = "". $product['thumbnail'];
            if($product['source'] == 1){
                $img_string = 'https://iproject26.icasites.nl/thumbnails/'. $img_string;
            }else{
                $img_string = 'https://iproject26.icasites.nl/uploadedImages/'. $img_string;
            }
            createItemProper($product['voorwerpnummer'],$img_string, $product['titel'], $product['prijs'],$secs, $product['geblokkeerd']);
        }
        $sql = "SELECT COUNT(*) AS 'aantal',MIN(dbo.FN_GeefPrijs(V.voorwerpnummer)) AS 'min_prijs', MAX(dbo.FN_GeefPrijs(V.voorwerpnummer)) AS 'max_prijs'
            FROM dbo.Voorwerp V
            WHERE veiling_gesloten = 0 AND geblokkeerd = 0 ".$search_string." ". $price_filter_string;
        $query = $conn->prepare($sql);
        if (isset($_GET['zoek_veld'])) {
            if ($_GET['zoek_veld'] != "") {
                for($i = 0; $i < count($search_array); $i++){
                    $query->bindValue($i + 1, "%".$search_array[$i]."%");
                }
            }
        }
        if(isset($waardes)){
            if ($waardes != "") {
                $count = 0;
                if(isset($search_array)){
                    $count += count($search_array);
                }
                $query->bindValue($count + 1, $waardes[0]);
                $query->bindValue($count + 2, $waardes[1]);
            }
        }
        $query -> execute();
        $row = $query -> fetchAll(PDO::FETCH_ASSOC);
        $max_aantal = $row[0]['aantal'];
        echo "<form><input type='hidden' value='".$row[0]['min_prijs']."' id='min_prijs' /><input type='hidden' value='".$row[0]['max_prijs']."' id='max_prijs' /></form>";
        if($product['geblokkeerd'] == 0){
            loadPagination($page, CEIL($max_aantal / $product_limit), $filter_string);
        }
    }
}

function filterCategories($id = -1){
    $categories = array($id);
    $i = 0;
    while($i < count($GLOBALS['Categories'])){
        if($GLOBALS['Categories'][$i]['parent_rubriek'] === $id){
            $new_categories = filterCategories($GLOBALS['Categories'][$i]['rubrieknummer']);
            for($j =0; $j < count($new_categories); $j++){
                array_push($categories,$new_categories[$j]);
            }
        }
        $i++;
    }
    return $categories;
}

function loadPagination($page,$max_page,$filter = ""){
    echo '<nav aria-label="navigation" class="w-100">
    <ul class="pagination justify-content-center">';
    if(isset($_GET['zoek_veld'])){
        $filter .= "&zoek_veld=". $_GET['zoek_veld'];
    }
    if(isset($_GET['price_slider'])){
        $filter .= "&price_slider=". $_GET['price_slider'];
    }
    if ($max_page > 1) {
        if ($page == 1) {
            echo '<li class="page-item disabled"><a class="page-link" href="#">Vorige</a></li>';
            echo '<li class="page-item active"><a class="page-link" href="?page=1'. $filter .'">1</a></li>';
        } else {
            echo '<li class="page-item"><a class="page-link" href="?page='. ($page -1) . $filter .'">Vorige</a></li>';
            echo '<li class="page-item"><a class="page-link" href="?page=1'. $filter .'">1</a></li>';
        }
        
        if ($page > 4) {
            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        for ($i = 0; $i < 5; $i++) {
            $page_to_load = $page + $i - 2;
            if ($page_to_load > 1 && $page_to_load < $max_page) {
                if ($page_to_load == $page) {
                    echo '<li class="page-item active"><a class="page-link" href="?page='. $page_to_load . $filter .'">'. $page_to_load .'</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?page='. $page_to_load . $filter .'">'. $page_to_load .'</a></li>';
                }
            }
        }
        if ($page < ($max_page - 4)) {
            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        
        if ($page == $max_page) {
            echo '<li class="page-item active"><a class="page-link" href="?page='. $page . $filter .'">'. $page .'</a></li>';
            echo '<li class="page-item disabled"><a class="page-link" href="#">Volgende</a></li>';
        } else {
            echo '<li class="page-item"><a class="page-link" href="?page='. $max_page . $filter .'">'. $max_page .'</a></li>';
            echo '<li class="page-item"><a class="page-link" href="?page='. ($page + 1) . $filter .'">Volgende</a></li>';
        }
    }else{
        echo '<li class="page-item disabled"><a class="page-link" href="#">Vorige</a></li>';
        echo '<li class="page-item active"><a class="page-link" href="?page=1'. $filter .'">1</a></li>';
        echo '<li class="page-item disabled"><a class="page-link" href="#">Volgende</a></li>';
    }
    echo '</ul></nav>';
}
?>