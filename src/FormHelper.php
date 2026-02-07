<?php 
namespace App;

class FormHelper{

    private $fname;
    private $lname;
    private $date;
    private $place;
    private $serie;
    private $field;
    private $level_id;
    private $classe;
    private $mail;
    private $password;
    private $admin_id;
    private $school;
    private $errorAdd;

    public function __construct(string $firstName, string $lastName, string $date, string $place , string $serie, string $field,
                                int $level_id, string $classe, string $mail, string $password, int $admin_id, string $school
    )
    {
        $this->fname = $firstName;
        $this->lname = $lastName;
        $this->date = $date;
        $this->place = $place;
        $this->serie = $serie;
        $this->field = $field;
        $this->level_id = $level_id;
        $this->classe = $classe;
        $this->mail = $mail;
        $this->password = $password;
        $this->admin_id = $admin_id;
        $this->school = $school;
    }
    
    public function StudentValid(): ?array{

        if(empty( $this->fname)){
            $this->errorAdd['fname'] = "Veuillez entrer le nom";
         }
         if(empty( $this->lname)){
             $this->errorAdd['lname'] = "Veuillez entrer le prénom";
         }
        
         if(empty( $this->date)){
            $this->errorAdd['date'] = "Veuillez entrer la date de naissance";
         }
         if(empty( $this->place)){
             $this->errorAdd['place'] = "Veuillez entrer le lieu de naissance";
         }
       
         if(empty( $this->serie)){
            $this->errorAdd['serie'] = "Veuillez entrer la série BAC";
         }
         if(empty( $this->field )){
             $this->errorAdd['field'] = "Veuillez entrer la filière";
         }
         if(empty( $this->level_id )){
              $this->errorAdd['level'] = "Veuillez entrer le nievau";
         }
         if(empty( $this->classe)){
            $this->errorAdd['classe'] = "Veuillez entrer la classe";
         }
         if(empty( $this->mail)){
             $this->errorAdd['mail'] = "Veuillez entrer l'addresse E-mail";
         }
         if(empty( $this->password)){
             $this->errorAdd['password'] = "Erreur de génération de mot de passe";
         }
         if(empty($this->admin_id)){
             $this->errorAdd['admin_id'] = "id admin non trouvé";
         }
         if(empty($this->school)){
             $this->errorAdd['etablissement'] = 'Sélectionnez l\'établissement';
         }
         
        return $this->errorAdd;

    }

}