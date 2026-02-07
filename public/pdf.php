<?php
use App\Connection;
require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
Connection::sessionStarted();

$id = $_SESSION['student'] ?? null;
if($id == null){
  header('Location: /login');
  exit;
}
header('Content-type: application/pdf');
readfile('../views/data/bulletin/bull-1-1.pdf');