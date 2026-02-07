<?php
namespace App;

class PasswordHelper{

    public static function StrGenerate($longueur = 10)
    {
     $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $longueurMax = strlen($caracteres);
     $chaineAleatoire = '';
     for ($i = 0; $i < $longueur; $i++)
     {
     $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
     }
     return $chaineAleatoire;
    }

    public static function passwordHash(string $password):string{
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $passwordHash;
    }

    

}