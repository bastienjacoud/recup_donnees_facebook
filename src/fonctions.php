<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    //Fonction utilisée ici pour se connecter à la BDD
    function getDb()
    {
        return new PDO("mysql:host=localhost;dbname=recuperation_donnees;charset=utf8", "root", "",
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    }
    
?>
