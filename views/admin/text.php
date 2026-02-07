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
if(!empty($_GET['classeID'])){
    $classeID = htmlentities((int)$_GET['classeID']);
    $queryC = $pdo->prepare("SELECT name FROM classe WHERE id= ?");
    $queryC->execute([$classeID]);
    $queryC->setFetchMode(PDO::FETCH_ASSOC);
    $classeC = $queryC->fetch();
}
$query = $pdo->prepare("SELECT * FROM students WHERE classe_id = ?");
$query->execute([$classeID]);
$query->setFetchMode(PDO::FETCH_CLASS,Student::class);
$students = $query->fetchAll();


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
                
                    <a class="logoutAdmin" href="/admin/logout">Déconnexion</a>
            </div>
            </div>
        </nav>
        
    <main class="container mt-5">
        <?php if(!empty($errorUpload)):?>
            <div class="alert alert-danger"><?=$errorUpload?></div>
        <?php endif ?>
        <?php if(!empty($errorDelete)):?>
            <div class="alert alert-danger"><?=$errorDelete?></div>
        <?php endif ?>
        <?php if(!empty($SuccessUpload)):?>
            <div class="alert alert-success"><?=$SuccessUpload?></div>
        <?php endif ?>
        <?php if(!empty($DeleteSuccess)):?>
            <div class="alert alert-success"><?=$DeleteSuccess?></div>
        <?php endif ?>
        <h1 style="text-align: center;">Liste des étudiant de la <?=$classeC['name']?></h1>
       
            </div>

            <div class="table-responsive">
            <table class="table table-striped">
            <thead>
                    <tr>
                        <th style="text-align: center;">ID</th>
                        <th style="text-align: center;">Nom</th>
                        <th style="text-align: center;">Prénom</th>
                        <th style="text-align: center;">Date de naissance</th>
                        <th style="text-align: center;">Lieu de naissance</th>
                        <th style="text-align: center;">Filière</th>
                        <th style="text-align: center;">Mot de passe</th>
                        <th style="text-align: center;">Ajouter ou Modifier</th>
                        <th style="text-align: center;">Supprimer</th>
                    </tr>
            </thead>
            <tbody>
                <?php foreach($students as $student):?>
                    <tr>
                        <td style="text-align: center;"><?=$student->getID()?></td>
                        <td style="text-align: center;"><?=$student->getFirstname()?></td>
                        <td style="text-align: center;"><?=$student->getLastname()?></td>
                        <td style="text-align: center;"><?=$student->getDateOfBirth()?></td>
                        <td style="text-align: center;"><?=$student->getplaceOfBirth()?></td>
                        <td style="text-align: center;"><?php $idfield = (int)$student->getField();
                                                                $field = $pdo->query("SELECT name FROM fields WHERE id ={$idfield}");
                                                                $field->setFetchMode(PDO::FETCH_ASSOC);
                                                                $fieldname = $field->fetch();
                                                                echo $fieldname['name'];?></td>
                        <td style="text-align: center;"><?=$student->getMotdepasse()?></td>
                        <td style="text-align: center;">
                            <button type="button" class="btn " style="background-color: #358DF0;" data-bs-toggle="modal" data-bs-target="#ajouter-<?=$student->getID()?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                                    <path d="m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2Zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672Z"/>
                                    <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5Z"/>
                                </svg>
                              </button>                            
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn" style="background-color: #F03549;" data-bs-toggle="modal" data-bs-target="#delete-<?=$student->getID()?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            </div>
        <?php foreach($students as $student):?>
        <div class="modal fade" id="ajouter-<?=$student->getID()?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un Bulletin</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="file" name="bulletin">
                        <select name="semestre" id="">
                            <option value="1-<?=$student->getID()?>">Semestre 1</option>
                            <option value="2-<?=$student->getID()?>">Semestre 2</option>
                            <option value="3-<?=$student->getID()?>">Semestre 3</option>
                            <option value="4-<?=$student->getID()?>">Semestre 4</option>
                            <option value="5-<?=$student->getID()?>">Semestre 5</option>
                            <option value="6-<?=$student->getID()?>">Semestre 6</option>
                        </select>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary" name="ajouter" >Enreigistrer</button>
                          </div>
                    </form>
                </div>
              </div>
            </div>
        </div>
        <?php endforeach?>
        
        <?php foreach($students as $student):?>
        <div class="modal fade" id="delete-<?=$student->getID()?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Bulletin de  <?=$student->getFirstname()?></h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="delete" onsubmit="return confirm('Voulez vous supprimer ce buletin ?')">
                        <p>Choisir un semestre à supprimer</p>
                        <select name="semestre" id="">
                            <option value="1-<?=$student->getID()?>">Semestre 1</option>
                            <option value="2-<?=$student->getID()?>">Semestre 2</option>
                            <option value="3-<?=$student->getID()?>">Semestre 3</option>
                            <option value="4-<?=$student->getID()?>">Semestre 4</option>
                            <option value="5-<?=$student->getID()?>">Semestre 5</option>
                            <option value="6-<?=$student->getID()?>">Semestre 6</option>
                        </select>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger" name="delete">Supprimer</button>
                          </div>
                    </form>
                </div>
              </div>
            </div>
        </div>
        <?php endforeach?>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>