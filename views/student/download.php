<?php
use App\Connection;
Connection::sessionStarted();

$id = $_SESSION['student'] ?? null;
if($id === null){
  header('Location: /');
  exit;
}

$bulletin = explode('/',$_SERVER['REQUEST_URI']);
$bullName = $bulletin[2];
$explodeBull = explode('-',$bullName);
$idBull = $explodeBull[2];

if($id == $idBull){
    $pathBull =  dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'bulletin'.DIRECTORY_SEPARATOR.$bulletin[2].'.pdf';
    header('Content-description: File transfer');
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$bullName.'.pdf');
    header('Content-length:'.filesize($pathBull));
    ob_clean();
    readfile($pathBull);
    exit;
}else{
    header('Location: /');
    exit;
}


