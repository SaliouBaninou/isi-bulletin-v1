<?php
use App\Connection;
use App\FormHelper;
use App\PasswordHelper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
Connection::sessionStarted();

$id = $_SESSION['admin'] ?? null;
if($id === null){
  header('Location: /admin');
  exit;
}

$layout = '../layout/';

$pdo = Connection::getPDO();

function recupe(string $dbname, $pdo):array{
    $queryrecupe = $pdo->query("SELECT * FROM {$dbname}");
    $queryrecupe->setFetchMode(PDO::FETCH_ASSOC);
    $recupe = $queryrecupe->fetchAll();
    return $recupe;
}

$Series = recupe('series',$pdo);
$Levels = recupe('levels',$pdo);
$Classes = recupe('classe',$pdo);
$Filieres = recupe('fields',$pdo);
$etablissements = recupe('school',$pdo);




if(!empty($_POST)){
    
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $date = $_POST['date'];
        $place = $_POST['place'];
        $serie =(int) $_POST['serie'];
        $field =(int) $_POST['field'];
        $level_id = (int)$_POST['level'];
        $classe = (int)$_POST['classe'];
        $email = $_POST['mail'];
        $profile = 'inconnue.png';
        $admin_id = $id;
        $school =(int) $_POST['school'];
    
        $chaineAleatoire = PasswordHelper::StrGenerate(10);
        $motDePasse = htmlentities($chaineAleatoire);
        $password = PasswordHelper::passwordHash($chaineAleatoire);
        $formHelper = new FormHelper($fname,$lname,$date,$place,$serie,$field,$level_id,$classe,$email,$password,$admin_id,$school);
        $errorAdd =$formHelper->StudentValid();
        if(empty($errorAdd)){
            $emailIsNotdb = $pdo->query("SELECT COUNT(id) FROM students WHERE mail='{$email}'");
            $emailCount = $emailIsNotdb->fetch();
            if($emailCount['COUNT(id)'] === 0){

                


                $date = date_create($_POST['date']);
                $date = date_format($date,"Y-m-d H:i:s");
                    $query = $pdo->prepare("INSERT INTO students (firstname, lastname, dateOfBirth, placeOfBirth,field_id, mail, password, profile,classe_id, serie_id, admin_id, level_id,school_id,MotDePasse) 
                                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)");
                                    $satement = $query->execute([
                                        $fname,
                                        $lname,
                                        $date,
                                        $place,
                                        $field,
                                        $email,
                                        $password,
                                        $profile,
                                        $classe,
                                        $serie,
                                        $admin_id,
                                        $level_id,
                                        $school,
                                        $motDePasse
                                        ]);
                    if($satement === true){
                       
                        $AddSuccess = "L'étudiant à été ajouter dans la base de donné";
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->Host='smtp.gmail.com';
                        $mail->SMTPAuth=true;
                        $mail->Username='salioubaninou@gmail.com';
                        $mail->Password='rlfyfsqqjlsrzpui';
                        $mail->SMTPSecure='ssl';
                        $mail->Port=465;
                        $mail->setFrom('salioubaninou@gmail.com');
                        $mail->addAddress($email);
                        $mail->isHTML(true);
                        $mail->Subject = 'Mot de passe ISI Bulletin';
                        $mail->Body = <<<HTML
                        <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
             body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                            background-color: #f4f4f4;
                        }

                        .email-signature {
                            max-width: 500px;
                            margin: 0 auto;
                            background: rgb(195,168,11);
                            background: linear-gradient(90deg, rgba(195,168,11,1) 0%, rgba(211,192,49,1) 28%, rgba(183,169,30,0.8688725490196079) 51%, rgba(202,167,8,1) 100%);
                            border: 1px solid #e1e1e1;
                            padding: 20px;
                            border-radius: 8px;
                            height: 100%;
                        }

                        .name {
                            font-size: 18px;
                            color: #333333;
                            margin-bottom: 5px;
                        }

                        .title {
                            font-size: 14px;
                            color: #666666;
                            margin-bottom: 10px;
                        }

                        .contact-info {
                            font-size: 14px;
                            color: #666666;
                            margin-bottom: 10px;
                        }

                        .social-icons a {
                            display: inline-block;
                            margin-right: 10px;
                        }

                        .social-icons img {
                            width: 20px;
                            height: 20px;
                            border: 0;
                        }
        </style>
    </head>
    <body>



            <div class="email-signature">
                <div style="display: flex; justify-content: center; align-items: center;"><img src="https://gvl5649.webmo.fr/layout/images/accueil/ISI.png" style="height: 80px;width: auto;"></div>
                <span style="font-size: 1.4em; color: #fff;text-align: center;">Bienvenue sur ISI Bulletin</span>
                <p>Bonjour $fname, $lname</p>
                <p>Votre Identifiant est : <strong style="text-decoration:none;color:#000000;">$email</strong></p>
                <p>Votre Mot de passe est : <strong>$chaineAleatoire</strong></p>
                <a href="https://gvl5649.webmo.fr/" target="_blank">Lien vers ISI Bulletin:gvl5649.webmo.fr/</a>
                <p>Merci d'avoir choisis ISI pour vos études <br>
                    Nous vous souhaitons une bonne journée !</p>
                <p class="name">Cordialement <strong>NSM</strong></p>
                <p class="title">Pour lus d'information:</p>
                <p class="contact-info">BATTERY IV/Libreville/Gabon | <a class="numero" href="tel:+24174034444" style="text-decoration: none; color:#fff;">074 03 44 44</a><br><a class="numero" href="tel:+24162131452" style="text-decoration: none; color:#fff;">062 13 14 52</a> | <a href="mailto:salioubaninou@gmail.com" style="text-decoration: none; color:#fff;">cliquer pour envoyer un <i class="fa-solid fa-envelope"></i></a></p>

                <div class="social-icons">
                    <a href="https://www.linkedin.com/" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="https://twitter.com/" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                    <!-- Ajoutez d'autres icônes de médias sociaux selon vos besoins -->
                </div>
            </div>
            <script src="https://kit.fontawesome.com/d5568101bc.js" crossorigin="anonymous"></script>

    </body>
    </html>
HTML;
                        
                       
                        
                        if($mail->send()){
                           $SuccesMail = "Mot de passe envoyer à $email";
                        }
                    
                    }else{
                        $noAdd = "Etudiant non ajouter Veuillez réessayer";
                    }
            }else{
                $errorAdd['exitmail'] = "E-mail déjà utiliser Utilisez une autre address mail !";
            }
        }else{
            $errorAdd['vide'] = "Veuillez compléter tous les champs";
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
              <li class="nav-item"><a class="Adminlink" href="/admin/addUser">+ Etudiant</a></li>
            </ul>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle etablissment" href="#" data-bs-toggle="dropdown" aria-expanded="false">Etablissement</a>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="/admin/addClasse">Ajouter classe</a></li>
                    <li><a class="dropdown-item" href="/admin/addLevel">Ajouter Niveau</a></li>
                    <li><a class="dropdown-item" href="/admin/addSerie">Ajouter Série Bac</a></li>
                    <li><a class="dropdown-item" href="/admin/addField">Ajouter Filière</a></li>
                    </ul>
                </li>
            </ul>
            
                <a class="logoutAdmin" href="/admin/logout">Déconnexion</a>
          </div>
        </div>
    </nav>
    <main class="container">
        <?php if(!empty($errorAdd)):?>
            <div class="alert alert-danger">
                <?php foreach($errorAdd as $e){echo $e."<br>" ?? "Complétez tous les champs";} ?>
            </div>
        <?php endif ?>
        <?php if(!empty($AddSuccess) and !empty($SuccesMail)):?>
            <div class="alert alert-success">
                <?=$AddSuccess ."<br>".$SuccesMail?>
            </div>
        <?php endif?>
        <div class="containerFormAdmin">
            <h1 class="text-center">Créer utilisateur</h1>
            <form action="" method="post">
                <div class="form-group mb-3  nom-prenom">
                    <input type="text" name="fname" id="name" placeholder="Nom" class="form-control">
                    <input type="text" name="lname" id="prenom" placeholder="Prénom" class="form-control">
                </div>
                <div class="form-group mb-3 date-place">

                        <label for="date" class="datel">Date de naissance</label>
                        <input type="date" name="date" id="date" placeholder="Date de naissance" class="form-control date">
                   
                    <input type="text" name="place" id="lieu" placeholder="Lieu de naissance" class="form-control">
                </div>
                <div class="form-group mb-3 serie-field">
                    <select name="serie" id="niveau" class="form-select">
                        <option selected>Choisir la série BAC</option>
                        <?php if(!empty($Series)):?>
                            <?php foreach($Series as $serie):?>
                        <option value="<?=$serie['id']?>"><?=$serie['name']?></option>
                            <?php endforeach?>
                        <?php endif?>
                    </select>
                    <select name="field" id="" class="form-select">
                        <option selected>Choisir la filière</option>
                        <?php if(!empty($Filieres)):?>
                            <?php foreach($Filieres as $filiere):?>
                        <option value="<?=$filiere['id']?>"><?=$filiere['name']?></option>
                            <?php endforeach?>
                        <?php endif?>
                    </select>
                </div>
                <div class="form-group mb-3 level-classe">
                   <select name="level" id="niveau" class="form-select">
                        <option selected>Choisir le niveau</option>
                        <?php if(!empty($Levels)):?>
                            <?php foreach($Levels as $level):?>
                        <option value="<?=$level['id']?>"><?=$level['name']?></option>
                            <?php endforeach?>
                        <?php endif?>
                       
                    </select>
                    <select name="classe" id="" class="form-select">
                        <option selected>Choisir la classe</option>
                        <?php if(!empty($Classes)):?>
                            <?php foreach($Classes as $clas):?>
                        <option value="<?=$clas['id']?>"><?=$clas['name']?></option>
                            <?php endforeach?>
                        <?php endif?>
                    </select>
                </div>
                <div class="form-group mb-3 school-email">
                    <input type="email" name="mail" id="email" placeholder="Email" class="form-control">
                    <select name="school" id="" class="form-select">
                        <option selected>Choisir l'établissement</option>
                        <?php if(!empty($etablissements)):?>
                            <?php foreach($etablissements as $etablis):?>
                        <option value="<?=$etablis['id']?>"><?=$etablis['name']?></option>
                            <?php endforeach?>
                        <?php endif?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter étudiant
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                    <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                    </svg>
                </button>
            </form>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>