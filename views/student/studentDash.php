<?php
use App\Connection;
use App\Model\Student;
use App\PasswordHelper;

Connection::sessionStarted();

$id = $_SESSION['student'] ?? null;
if($id === null){
  header('Location: /');
  exit;
}
$pdo = Connection::getPDO();

$query = $pdo->prepare("SELECT stdt.id, stdt.firstname, stdt.lastname, stdt.dateOfBirth, stdt.placeOfBirth,
                        stdt.mail, stdt.password, stdt.profile, cls.name as classe_id ,
                        fds.name as field_id, srs.name as serie_id, scl.name as school_id, lvl.name as level_id
                        FROM  students stdt  
                        JOIN classe cls ON stdt.classe_id = cls.id
                        JOIN fields fds ON stdt.field_id = fds.id
                        JOIN series srs ON stdt.serie_id = srs.id
                        JOIN school scl ON stdt.school_id = scl.id
                        JOIN levels lvl ON stdt.level_id = lvl.id
                        WHERE stdt.id =:id");
$query->execute(['id'=>$id]);
$query->setFetchMode(PDO::FETCH_CLASS, Student::class);
/** @var Student|null */
$student = $query->fetch();



$queryBull = $pdo->query("SELECT * FROM bulletin WHERE student_id ={$student->getID()}");
$queryBull->setFetchMode(PDO::FETCH_ASSOC);
$bulletin = $queryBull->fetch();


$layout = '../layout/';
$title = 'Mon espace';


if(isset($_POST['PasswordModify'])){
  $userId = htmlentities($_POST['studentID']);
  $newNoHashPassword = htmlentities($_POST['newPassword']);
  $newPassword = PasswordHelper::passwordHash(htmlentities($_POST['newPassword']));
  if(!empty($newPassword)){

    $querNewpass = $pdo->prepare("UPDATE students SET password = ?, motDePasse=?  WHERE id = ?");
    $modify = $querNewpass->execute([$newPassword,$newNoHashPassword,$userId]);
    if($modify){
      $newPasswordSuccess = "Mot de passe modifier avec succes !";
    }else{
      $errorNewPassword = "Le mot de passe n'a pas été modifier réessayez !";
    }

  }else{
    $errorNewPassword = "Vous n'avez pas rentrer le nouveau mot de passe !";
  }
}

if(isset($_POST['ProfileModify'])){
    $nameProfile = "profil-".$_POST['studentID'];
    $studentId = (int)$_POST['studentID'];

    if(isset($_FILES['newProfile']) && $_FILES['newProfile']['error'] == 0)

        $pathProfile =  dirname(__DIR__,2).'/public/layout/images/profiles/';
       
        $tempName = $_FILES['newProfile']['tmp_name'];
        $_FILES['newProfile']['size'];
        if(is_uploaded_file($tempName)){
            
            if($_FILES['newProfile']['size']  < 1000000){

                $profileName =  $_FILES['newProfile']['name'];
                $profileInfo = pathinfo($profileName);
                $extensionFile = $profileInfo['extension'];
                $extensionFile = strtolower($extensionFile);
                
                if(in_array($extensionFile, ['png','jpg','jpeg'])){
                  $nameProfile = "profile-".$studentId.'.'.$extensionFile;
                  if(move_uploaded_file($tempName, $pathProfile.$nameProfile) and $pdo->query("UPDATE students SET profile = '{$nameProfile}' WHERE id ={$studentId}")){
                        header('Location:/student/'.$student->getID());
                        $SuccessUpload = 'profile enreigistrer avec succes ';
                        exit;
                    }else{
                        $errorUpload = 'Eurreur lors de l\'enreigistrement';
                    }

                }else{
                  $errorProfileUpload = 'Fichier non conforme ! choisissez une image <br> (png ,jpg,jpeg';
                }
               
            }else{
              $errorProfileUpload = 'Image trop lourde max 1Mo';
            }
  }else{
    $errorProfileUpload = 'Erreur lors de l\'upload <br> Le fichier est probablement trop lourd';
    }
  
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?=$layout?>style.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?=$layout?>images/favicon/favicon.ico" type="image/x-icon">

    <title><?=$title?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

</head>
<body class="body-1">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          
          <img src="<?=$layout?>images/ISI.png" alt="logo ISI">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="#" style="background: linear-gradient(#d0ac34, #dc8922);
                  border-radius: 35px;">Home</a>
                
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Licence 1
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?=$bulletin['bulletin1']?>">Bulletin Semestre 1</a></li>
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 2</a></li>
                  </ul>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Licence 2
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 1</a></li>
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 2</a></li>
                  </ul>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Licence 3
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 1</a></li>
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 2</a></li>
                  </ul>
                  </li>
                  <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Master 1
                    </a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 1</a></li>
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 2</a></li>
                  </ul>
                  </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Master 2
                    </a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 1</a></li>
                    <li><a class="dropdown-item" href="#">Bulletin Semestre 2</a></li>
                  </ul>
                  </li>
                
                <button class="déconnexion">
                  <a href="/student/logout" >Déconnexion</a>
              </button>
            </div>
          </div>
        </nav>
        <?php if(!empty($newPasswordSuccess)):?>
        <div style="display: flex; justify-content:flex-end; align-items:center;">
              <div class="alert alert-success alert-dismissible fade show z-3 position-absolute p-5 rounded-3 text-center" role="alert" >
                  <strong><?=$newPasswordSuccess?></strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        <?php endif ?>
        <?php if(!empty($errorNewPassword)):?>
          <div style="display: flex; justify-content:flex-end; align-items:center;">
              <div class="alert alert-danger alert-dismissible fade show z-3 position-absolute p-5 rounded-3 text-center" role="alert" >
                  <strong><?=$errorNewPassword?></strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        <?php endif ?>

        <?php if(!empty($SuccessUpload)):?>
        <div style="display: flex; justify-content:flex-end; align-items:center;">
              <div class="alert alert-success alert-dismissible fade show z-3 position-absolute p-5 rounded-3 text-center" role="alert" >
                  <strong><?=$SuccessUpload?></strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        <?php endif ?>
        <?php if(!empty($errorProfileUpload)):?>
          <div style="display: flex; justify-content:flex-end; align-items:center;">
              <div class="alert alert-danger alert-dismissible fade show z-3 position-absolute p-5 rounded-3 text-center" role="alert" >
                  <strong><?=$errorProfileUpload?></strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>
        <?php endif ?>
      <main class="main-profil">
       
      <div class="countainer-profil">
          
          <div class="profile_div">
            <img src="<?=$layout.'images/profiles'.DIRECTORY_SEPARATOR.$student->profile() ?? '../data/profile/inconnue.png'?>" style="border-radius: 100%;" alt="Photo de profil" >
            <button type="button" class="btn border-0 btn-profile"  data-bs-toggle="modal" data-bs-target="#changeProfile<?=$student->getID()?>"> <label for="upload_image"><span class="material-symbols-outlined" style="cursor:pointer">
          add_a_photo
          </span>
            </button>
          </div>
          <h4 style="text-align: center;"><?=$student->getUsername()?></h4>
          <ul class="texte">
            <li>E-mail: <?=$student->getIdentifiant()?></li>
            <hr>
              <li><?=$student->getSchool()?></li> 
              <li><?=$student->getLevel()?></li>
              <li><?= $student->getSerie()?></li>
              <li><?=$student->getfield()?></li>
              <hr>
              <li><?=date('Y').'-'.(date('Y')+1)?></li>
              <hr>
              <li>
                <button type="button" class="btn" style="font-size:1.1em;" data-bs-toggle="modal" data-bs-target="#changePasse<?=$student->getID()?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                  </svg> Modifier mot de passe
                </button>
              </li>
          </ul>
      </div>



      <div class="modal fade" id="changePasse<?=$student->getID()?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Modifier votre mot de passe !</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="post">
                    <div class="alert alert-success">
                      <h5> Voulez vous modifer votre mot de passe ? </h5>
                    </div>
                      <input type="text" name="newPassword" class="form-control" placeholder="Entrer votre nouveau mot de passe">
                      <input type="text" name="studentID" value="<?=$student->getID()?>" style="display: none;">
  
                    <div class="modal-footer">
                      <button type="submit" name="PasswordModify" class="btn btn-success">Modifier</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="changeProfile<?=$student->getID()?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Modifier votre profile !</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="post" enctype="multipart/form-data">
                    <div class="alert alert-success">
                      <h5> Sélectionner la photo de profile </h5>
                    </div>
                      <input type="file" name="newProfile" class="form-control">
                      <input type="text" name="studentID" value="<?=$student->getID()?>" style="display: none;">
  
                    <div class="modal-footer">
                      <button type="submit" name="ProfileModify" class="btn btn-success">Modifier</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </main>
      

        <script>
            const loader = document.querySelector('.loader');

            window.addEventListener('load', () => {

                loader.classList.add('fondu-out');

            })
        </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>