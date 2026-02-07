<?php

namespace App\Model;


class Admin{

    private $id;

    private $firstname;

    private $lastname;


    private $identifiant;

    private $password;


 

    public function getID():int{
        return $this->id;
    }

    public function getIdentifiant():?string{
        return $this->identifiant;
    }

    public function getPassword(): ?string{
        return $this->password;
    }


    public function getUsername(): ?string{
        return $this->firstname.' '.$this->lastname;
    }


}