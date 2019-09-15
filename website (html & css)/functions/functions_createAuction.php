<?php
/*
Uses catagories global to make a categories dropdown with every category
*/
function getUserFromName($userName,$conn){
    $query = $conn->prepare("SELECT * FROM gebruiker WHERE gebruikersnaam = :user");
    $query->execute(array(
        ':user' => $userName
    ));
    $user = $query->fetch();
    return $user;
}

function getMaxItemInt($conn)
{
    $query = $conn->query("SELECT IDENT_CURRENT('voorwerp') as id");
    $int = $query->fetch();
    $int = $int['id'];
    return $int;
}


function createAuctionMessages($ID)
{
    switch ($ID) {
        case 0:
        makeModals('Succes!', 'Uw veiling is aan gemaakt');
        break;
        case 1:
        makeModals('Error', 'Upload error, Misschien is het bestand te groot. <br> De bestanden mogen samen niet meer dan 2MB zijn.');
        break;
        case 2:
        makeModals('Error', 'Het bestand dat u heeft geuploadet is geen afbeelding. <br> Zorg ervoor dat de file extensie klopt en geen hoofdletters bevat. <br> De volgende soorten afbeeldingen mogen worden geupload: <br> jpg, png, jpeg, gif');
        break;
        case 3:
        makeModals('Error', 'Veiling kan niet worden aangemaakt');
        break;
        case 4:
        makeModals('Error', 'Bestand kan niet worden geuploadet');
        break;
        case 5:
        makeModals('Error', "Je moet eerst verkoper worden. Ga <a href='profielpagina.php'>hier</a> naar de profiel pagina om verkoper te worden.'");
        break;
    }
}

function makeFullCategoryTree($conn)
{
    foreach ($conn->query("WITH    q AS 
    (
    SELECT  c.*, CAST('' AS VARCHAR(MAX)) AS bc
    FROM    Categorieen c
    WHERE   Parent IS NULL
    UNION ALL
    SELECT  c.*,  CAST(q.bc + ' / '   + c.Name  AS VARCHAR(MAX))
    FROM    Categorieen c
    JOIN    q
    ON      c.parent = q.ID
    )
SELECT  *
FROM    q
WHERE Parent is not null and not exists (select Parent from Categorieen d where q.ID = d.Parent)
ORDER BY
    bc") as $key=>$row) {
      $row['bc'] = substr($row['bc'],2);
      $string = explode(' / ',$row['bc']);
      $lastOfArray = sizeof($string) - 1;
      if($lastOfArray == 1){
      $row['bc'] = $string[0]." / ".$string[$lastOfArray];
      }else{
      $row['bc'] = $string[1]." / ".$string[$lastOfArray];
      }
        $_SESSION['fullTree'][$key] = "<option value='".$row['ID']."'>".$row['bc']."</option>";
    }
}

// Based on: https://stackoverflow.com/questions/7489742/php-read-exif-data-and-adjust-orientation
function correctImageOrientation($filename,$filetype) {
  ini_set ('gd.jpeg_ignore_warning', 1);
    if (function_exists('exif_read_data')) {
      $exif = exif_read_data($filename);
      if($exif && isset($exif['Orientation'])) {
        $orientation = $exif['Orientation'];
        if($orientation != 1){
        if($filetype == "jpg"){
          $img = imagecreatefromjpeg($filename);
        }elseif($filetype == "jpeg"){
            $img = imagecreatefromjpeg($filename);
        }elseif($filetype == "gif"){
            $img = imagecreatefromgif($filename);
        }elseif($filetype == "png"){
            $img = imagecreatefrompng($filename);
        }
          $degrees = 0;
          switch ($orientation) {
            case 3:
              $degrees = 180;
              break;
            case 6:
              $degrees = 270;
              break;
            case 8:
              $degrees = 90;
              break;
          }
          if ($degrees) {
            $img = imagerotate($img, $degrees, 0);        
          }
          imagejpeg($img, $filename);
        }
      }
    }      
  }

  function lowerImageQuality($filename,$filetype,$quality){
    if($filetype == "jpg"){
      $img = imagecreatefromjpeg($filename);
    }elseif($filetype == "jpeg"){
        $img = imagecreatefromjpeg($filename);
    }elseif($filetype == "gif"){
        $img = imagecreatefromgif($filename);
    }elseif($filetype == "png"){
        $img = imagecreatefrompng($filename);
    }
    imagejpeg($img, $filename,$quality);
  }
?>