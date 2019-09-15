<?php
require_once "SQLsrvConnect.php";
session_start();
session_destroy();
header('Location: index.php'); 
?>