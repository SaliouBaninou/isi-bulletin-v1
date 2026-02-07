<?php
use App\Connection;
use App\Model\Admin;

Connection::sessionStarted();
$id = $_SESSION['admin'] ?? null;
if($id !== null){
  header('Location: /admin-'.$id);
  exit;
}

$title = 'Connexion Admin';

$error =[];

$vide = 'Complétez tout les champs';

if(!empty($_POST)){
    $identifiant = $_POST['mail'];
    $password = $_POST['password'];

    if(!empty($identifiant) & !empty($password)){
        if(filter_var($identifiant, FILTER_VALIDATE_EMAIL)){
            $pdo = Connection::getPDO();
            $query = $pdo->prepare("SELECT * FROM admin WHERE identifiant=:identifiant");
            $query->execute(['identifiant'=>$identifiant]);
            $query->setFetchMode(PDO::FETCH_CLASS, Admin::class);
            $admin = $query->fetch();
            if(!empty($admin)){
                    if($admin->getIdentifiant() === $identifiant){

                        if($admin->getPassword() === $password ){
                            session_start();
                            $_SESSION['admin'] = $admin->getID();
                            header('Location: /admin-'.$admin->getID());
                            exit;
                        }else{
                            $error = 'Identifiants Incorrects';
                        }
                }else{
                    $error = 'Identifiants Incorrects';
                }
            }else{
                $error = 'Identifiants Incorrects';
            }
    
        }else{
            $error = 'Identifiants Incorrects';
        }
    }else{
        $error = 'Complétez tout les champs';
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="layout/images/favicon/favicon.ico" type="image/x-icon">
    <title><?=$title ?? "Mon site"?></title>
    <link rel="stylesheet" href="layout/Style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="fond">

    <main class="main-connexion">
        <div class="formFound">
        <img class="logo" src="layout/images/ISI.png">
        <h1 class="titre">Admin</h1>
        <?php if(!empty($error)):?>
          <p class="alert alert-danger" style="width: 100%; z-index:3; transition:0.5s;"><?=$error?></p>
        <?php endif?>
        <form action="" method="post">
            <label for="mail">Email Adress</label><br>
                <input type="email" name="mail" id="mail" placeholder="Identifiant" class="form-control <?php if($error === $vide){echo 'is-invalid';}?>" value="<?php if(!empty($identifiant)){echo  htmlentities($identifiant);}?>">
                <?php if(!empty($error) & $error === $vide):?>
                    <div class="invalid-feedback">
                    Veuillez entrer votre Identifiant
                    </div>
                <?php endif?>
                <i class="bi bi-envelope"></i>
                </p> 
            <p><label for="password">Password</label><br>
            <input type="password" name="password" id="password" placeholder="Password" class="form-control <?php if($error === $vide){echo 'is-invalid';}?>" value="<?php if(!empty($password)){echo htmlentities($password);}?>">
            <?php if(!empty($error) & $error ===$vide):?>
                    <div class="invalid-feedback">
                    Veuillez entrer votre Mot de passe
                    </div>
                <?php endif?>
            <i class="bi bi-lock"></i>
            </p>
            <input type="submit" value="login" class="btn btn-primary">
        </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>