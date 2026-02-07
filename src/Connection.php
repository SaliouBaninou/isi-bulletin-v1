<?php
namespace App;

use PDO;

class Connection{

    public static function getPDO(): PDO
    {
        return new PDO('mysql:dbname=bulletinisi; host=127.0.0.1:3306', 'root', 'saliou',[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function sessionStarted()
    {   
        if(session_status() !== PHP_SESSION_ACTIVE){
            return session_start();
        }
        
    }
}