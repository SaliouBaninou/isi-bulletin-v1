<?php
use App\Connection;
use App\Model\Student;

Connection::sessionStarted();

$id = $_SESSION['admin'] ?? null;
if($id === null){
  header('Location: /admin');
  exit;
}

$pdo = Connection::getPDO();



$layout = '../layout/';

if(isset($_POST['ajouter'])){
    
    $nameSem = $_POST['semestre'];
    $numberSemestre =  explode('-',$nameSem)[0];
    $studentId = explode('-',$nameSem)[1];
    if(isset($_FILES['bulletin']) && $_FILES['bulletin']['error'] == 0){

        $pathBull =  dirname(__DIR__).'/data/bulletin/';
        $tempName = $_FILES['bulletin']['tmp_name'];
        $_FILES['bulletin']['size'];
        if(is_uploaded_file($tempName)){
            
            if($_FILES['bulletin']['size']  < 2000000){

                $fileName =  $_FILES['bulletin']['name'];
                $fileInfo = pathinfo($fileName);
                $extensionFile = $fileInfo['extension'];
                $extensionFile = strtolower($extensionFile);
                $autorizeExtension = 'pdf';
               
                if($extensionFile == $autorizeExtension){
                    
                    $bullName = "bull-".$nameSem;
                    $bullExits = "SELECT COUNT(id) FROM bulletin WHERE student_id = {$studentId}";
                    $queryVerify = $pdo->query($bullExits)->fetch();
                     //On vérifie si l'étudiant posède  bulletin 
                    if( $queryVerify['COUNT(id)'] !==0){
                        if(move_uploaded_file($tempName, $pathBull.$bullName.'.pdf') and $pdo->query("UPDATE bulletin SET bulletin{$numberSemestre} = '{$bullName}' WHERE student_id = $studentId;")){
                            $SuccessUpload = 'bulletin a été enreigistrer avec succes ';
                        }else{
                            $errorUpload = 'Eurreur lors de l\'enreigistrement';
                        }
                    }else{
                        if(move_uploaded_file($tempName, $pathBull.$bullName.'.pdf') and $pdo->query("INSERT INTO bulletin(bulletin{$numberSemestre},student_id) VALUES('{$bullName}',{$studentId})")){
                            $SuccessUpload = 'bulletin a été enreigistrer avec succes ';
                        }else{
                            $errorUpload = 'Eurreur lors de l\'enreigistrement';
                        }
                    }
                    
                }else{
                    $errorUpload = 'Le format utilisé est PDF';
                }

            }else{
                $errorUpload = 'Fichier trop volumineux';
            }
           
        }else{
            $errorUpload = 'Le fichier est introuvable ressayé :)';
        }

    }else{
        $errorUpload = 'Erreur lors de l\'upload <br> Le fichier est probablement trop lourd';
    }
    
}


if(isset($_POST['delete'])){
    $nameSem = $_POST['semestre'];
    $numberSemestre =  explode('-',$nameSem)[0];
    $studentId = explode('-',$nameSem)[1];
    $bullName = "bull-".$nameSem;
    $tableBulle = "bulletin{$numberSemestre}";
    $bullExits = "SELECT COUNT(id) FROM bulletin WHERE  bulletin{$numberSemestre} = '{$bullName}'";
    $queryVerify = $pdo->query($bullExits)->fetch();

    if($queryVerify['COUNT(id)']!==0){
        $fileName = dirname(__DIR__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'bulletin'.DIRECTORY_SEPARATOR.'bull-'.$numberSemestre.'-'.$studentId.'.pdf';
        $queryDelete = $pdo->query("UPDATE bulletin SET bulletin{$numberSemestre} = NULL WHERE student_id = $studentId");
        if($queryDelete and unlink($fileName)){
            $DeleteSuccess = "Le Bulletin du semtres {$numberSemestre} Supprimer avec succes";
        }else{
            $errorDelete = "Une érreur c'est produit lors de la suppression du Bulletin";
        }
    }else{
        $errorDelete = "Il n'y a pas de Bulletin a supprimer";
    }
    


}

$Queryclasses = $pdo->query("SELECT * FROM  classe");
$Queryclasses->setFetchMode(PDO::FETCH_ASSOC);
$Classes = $Queryclasses->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="<?=$layout?>Adminstyle.css">
    <link rel="shortcut icon" href="<?=$layout?>images/favicon/favicon.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>


    <nav class="navbar navbar-expand-lg ">
            <div class="container-fluid ">
                <img src="../layout/images/ISI.png" class="logoAdmin">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse containtAdmin" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle etablissment" href="#" data-bs-toggle="dropdown" aria-expanded="false">Classe</a>
                        <ul class="dropdown-menu">
                        <?php if(!empty($Classes)):?>
                            <?php foreach($Classes as $classe):?>
                                <li><a class="dropdown-item" href="/admin/classe?classeID=<?=$classe['id']?>"><?=$classe['name']?></a></li>
                            <?php endforeach?>
                        <?php endif?>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="Adminlink"href="/admin-<?=$_SESSION['admin']?>">Acceuil</a></li>
                    <li class="nav-item"><a class="Adminlink" href="/admin/addUser">Etudiant</a></li>
                </ul>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Etablissement</a>
                        <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/addClasse">Ajouter classe</a></li>
                        <li><a class="dropdown-item" href="/admin/addLevel">Ajouter Niveau</a></li>
                        <li><a class="dropdown-item" href="/admin/addSerie">Ajouter Série Bac</a></li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex" role="search" method="post" action="admin-<?=$id?>/search">
                <input class="form-control me-2" type="text" placeholder="Search" aria-label="Search" name="qname">
                <button class="btn btn-success" type="submit" name="q">Rechercher</button>
                </form>
                    <a class="logoutAdmin" href="/admin/logout">Déconnexion</a>
            </div>
            </div>
        </nav>
        
    <main class="container mt-5">
        <?php $nmrstudent = $pdo->query("SELECT COUNT(id) FROM students")->fetch(); $nmbrsClasse = $pdo->query("SELECT COUNT(id) FROM classe")->fetch(); $adminName = $pdo->query("SELECT name FROM admin WHERE id = $id")->fetch();?>
        <section class="back-g-admin " >
            <h1>Bienvenue <?= $adminName['name']?>  </h1>
            <section class="home-admin">
                <div class="nmbr_etudiants">
                    <h3>Nombre d'étudiants</h3>
                    <p>L'école possède: </p>
                    <h4><?php if(((int)$nmrstudent['COUNT(id)']) <=1){echo $nmrstudent['COUNT(id)']." étudiant sur le site";}else{echo $nmrstudent['COUNT(id)']." étudiants sur le site";}?> </h4>
                </div>
                <div class="nmbr_classes">
                    <h3>Nombre de classes</h3>
                    <p>L'école possède: </p>
                    <h4><?php if(((int)$nmbrsClasse['COUNT(id)']) <=1){echo $nmbrsClasse['COUNT(id)']." classe sur le site";}else{echo $nmbrsClasse['COUNT(id)']." classes sur le site";}?></h4>
                </div>
            </section>
        </section>
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>