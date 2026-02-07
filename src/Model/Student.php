<?php

namespace App\Model;

class Student{

    private $id;

    private $firstname;

    private $lastname;

    private $dateOfBirth;
    
    private $placeOfBirth;

    private $field_id;

    private $mail;

    private $password;

    private $profile;

    private $admin_id;

    private $level_id;

    private $serie_id;

    private $classe_id;

    private $school_id;

    private $motDePasse;

    public function getID():int{
        return $this->id;
    }

    public function getIdentifiant():?string{
        return $this->mail;
    }

    public function getAdmin_id():?string{
        return $this->admin_id;
    }


    public function getClasse():?string{
        return $this->classe_id;
    }

    public function getSchool():? string
    {
        return $this->school_id;
    }

    public function getSerie():?string{
        return $this->serie_id;
    }


    public function getPassword(): ?string{
        return $this->password;
    }

    public function getfield(): ?string{
        return $this->field_id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getUsername(): ?string{
        return $this->firstname.' '.$this->lastname;
    }

    public function getDateOfBirth(): ?string
    {   
        $birth = explode(' ',$this->dateOfBirth);
        return $birth[0];
    }

    public function getPlaceOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    public function profile(): ?string{
        return $this->profile;
    }

    public function getLevel(): ?string
    {
        return $this->level_id;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motDePasse;
    }

}
