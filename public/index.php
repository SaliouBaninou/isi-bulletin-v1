<?php
use App\Router;
require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$path = dirname(__DIR__).'/views';

$router = new Router($path);


$router
    /** Les URLS La partie Etudiant  */
    ->get('/','index','Accueil')
    ->get('/login','login','login')
    ->post('/login','login','loginPOST')
    ->get('/student/[i:id]','student/studentDash','DashStudent')
    ->post('/student/[i:id]','student/studentDash','PostDashStudent')
    ->get('/student/logout','logout','logoutStudent')
    ->get('/student/[*:slug]-[i:bull]-[i:id]','student/download','downloadBulletin')

    /** Les URLS La partie Admin */
    ->post('/admin-[i:id]/search','admin/recherche','psearsh')
    ->get('/admin/classe','admin/text','text')
    ->get('/admin','loginAdmin','loginAdmin')
    ->post('/admin','loginAdmin','loginAdminPost')
    ->get('/admin-[i:id]','admin/index','adminDash')
    ->post('/admin-[i:id]','admin/index','addBull')
    ->get('/admin/logout','logoutAdmin','logoutAdmin')
    ->get('/admin/addUser','admin/addUsers','adminAdd')
    ->post('/admin/addUser','admin/addUsers','AddUser')
    ->get('/admin/addLevel','admin/addLevel','adminAddLevel')
    ->post('/admin/addLevel','admin/addLevel','adminAddLevelpost')
    ->get('/admin/addSerie','admin/addSerie','adminAddSerie')
    ->post('/admin/addSerie','admin/addSerie','adminAddSeriepost')
    ->get('/admin/addClasse','admin/addClasse','adminAddClasse')
    ->post('/admin/addClasse','admin/addClasse','adminAddClassepost')
    ->get('/admin/addField','admin/addField','adminAddField')
    ->post('/admin/addField','admin/addField','adminAddFieldpost')
    
    ->run();