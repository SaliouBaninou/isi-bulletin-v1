<?php
use App\Connection;
use App\FormHelper;

Connection::sessionStarted();

$id = $_SESSION['admin'] ?? null;
if($id === null){
  header('Location: /admin');
  exit;
}
$layout = '../layout/';

$pdo = Connection::getPDO();

if(isset($_POST['addSerie'])){
  
  if(!empty($_POST['nameSerie'])){
    $SerieName = htmlentities($_POST['nameSerie']);
    $count = $pdo->query("SELECT COUNT(id) FROM series WHERE name = '{$SerieName}'");
    $verify = $count->fetch();
    $number = (int)$verify['COUNT(id)'];
    if($number===0){
        $query = $pdo->prepare('INSERT INTO series(name) VALUES(?)');
        $statement = $query->execute([$SerieName]);
        if($statement == true){
          $addSerieSucces = "La nouvelle série a été enreigistrer avec succes !";
        }else{
          $addSerieError = "La nouvelle série  n'a pas été enreigistrer réessayer !";
        };
    }else{
      $addSerieError = "Cette série existe déjà x {$number} !";
    }
    
  }else{
    $addSerieError = "Veuillez nommer cette série";
  }
}

if(isset($_POST['modifySerie'])){
  if(!empty($_POST['nameModifySerie'])){
    $SerieName = htmlentities($_POST['nameModifySerie']);
    $classeId =(int) (htmlentities($_POST['idSerie']));
        $query = $pdo->prepare("UPDATE series SET name = ? WHERE id = $classeId");
        $statement = $query->execute([$SerieName]);
        if($statement == true){
          $modifyLevelSucces = "Modification de la série effectuer avec succes !";
        }else{
          $modifyLevelError = "Erreur de modification de la série Réessayer !";
        };
  }else{
    $errorModify = "Erreur Vous n'avez pas nommer la série réessayer";
  }
  
}


if(isset($_POST['deleteSerie'])){
  $nameDelete = htmlentities($_POST['nameDeleteSerie']);
  $idDelete = (int)(htmlentities($_POST['idDeleteSerie']));
  $count = $pdo->query("SELECT COUNT(id) FROM series WHERE name = '{$nameDelete}' ");
  $verify = $count->fetch();
  $number = (int)$verify['COUNT(id)'];
  if($number !==0){
    $queryDelete = $pdo->prepare("DELETE FROM series WHERE id = ? ");
    $statement = $queryDelete->execute([$idDelete]);
    if($statement === true){
      $deleteSuccess = "Le niveau {$nameDelete} à été supprimer";
    }else{
      $errorDelete = "Impossible de supprimer la niveau {$nameDelete} réessayer";
    }

  }else{
    $errorDelete = "Impossible de supprimer le ni+veau {$nameDelete}  car elle n'exites pas";
  }

}

$querySerie = $pdo->query("SELECT * FROM series");
$querySerie->setFetchMode(PDO::FETCH_ASSOC);
$allSerie = $querySerie->fetchAll();


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
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Etablissement</a>
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
        
      
    <?php if(!empty($addSerieSucces)):?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong><?=$addSerieSucces?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>
      <?php if(!empty($addSerieError)):?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong><?=$addSerieError?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>


      <?php if(!empty($deleteSuccess)):?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong><?=$deleteSuccess?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>
      <?php if(!empty($errorDelete)):?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong><?=$errorDelete?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>


      <?php if(!empty($modifySerieSucces)):?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong><?=$modifySerieSucces?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>
      <?php if(!empty($errorModify) || !empty($modifySerieError)):?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong><?=$errorModify ?? $modifySerieError?></strong>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif ?>
        <div>
          <h1>Ajouter une nouvelle série</h1>
          <p class="alert alert-primary" style="text-align: center;">Ajouter une série --<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSerie">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                                      <path d="m.5 3 .04.87a1.99 1.99 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2Zm5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19c-.24 0-.47.042-.683.12L1.5 2.98a1 1 0 0 1 1-.98h3.672Z"/>
                                      <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5Z"/>
                                      </svg>
                                   </button>
                                  
          </p>
        </div>
    <div class="table-responsive">
            <table class="table table-striped">
            <thead>
                    <tr>
                        <th style="text-align: center;">ID</th>
                        <th style="text-align: center;">Nom du niveau</th>
                        <th style="text-align: center;">Modifier</th>
                        <th style="text-align: center;">Supprimer</th>
                    </tr>
            </thead>
            <tbody>
              <?php if(!empty($allSerie)):?>
                  <?php foreach($allSerie as $serie):?>
                    <tr>
                        <td style="text-align: center;"><?=$serie['id']?></td>
                        <td style="text-align: center;"><?=$serie['name']?></td>
                        <td style="text-align: center;">
                             <button type="button" class="btn " style="background-color: #35F035;" data-bs-toggle="modal" data-bs-target="#modifySerie-<?=$serie['id']?>">
                             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                              </svg>
                              </button>                           
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn" style="background-color: #F03549;" data-bs-toggle="modal" data-bs-target="#deleteSerie-<?=$serie['id']?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                  <?php endforeach?>
                <?php endif ?>
            </table>
            </div>
      
          <!-- Modal AddClasse -->
          <div class="modal fade" id="addSerie" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Ajouter série</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="post">
                    <input type="text" name="nameSerie" id="" placeholder="Nom de la nouvelle classe" class="form-control">
                    <div class="modal-footer">
                      <button type="submit" name="addSerie" class="btn btn-primary">Ajouter</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php if(!empty($allSerie)):?>
          <?php foreach($allSerie as $serie):?>
          <!-- Modal ModiyClasse -->
          <div class="modal fade" id="modifySerie-<?=$serie['id']?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Modifier série</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="alert alert-success">
                    <h5>Voulez vous modifier la série<?=$serie['name']?> ?</h5>
                  </div>
                  <form action="" method="post">
                    <input type="text" name="nameModifySerie" value="<?=$serie['name']?>" placeholder="Nom de la nouvelle classe" class="form-control">
                    <input type="text" name="idLevel" value="<?=$serie['id']?>" style="display: none;">
                    <div class="modal-footer">
                      <button type="submit" name="modifySerie" class="btn btn-success">Modifier</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal DeleteClasse -->
          <div class="modal fade" id="deleteSerie-<?=$serie['id']?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="staticBackdropLabel">Supprimer série</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="post">
                    <div class="alert alert-danger">
                      <h5>Voulez vous suppimer la série <?=$serie['name']?> ?</h5>
                      <input type="text" name="idDeleteSerie" value="<?=$serie['id']?>" style="display: none;">
                      <input type="text" name="nameDeleteSerie" value="<?=$serie['name']?>" style="display: none;">
                    </div>
                    <div class="modal-footer">
                      <button type="submit" name="deleteSerie" class="btn btn-danger">Supprimer</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach?>
        <?php endif?>


    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>