<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


    //Connexion à la BDD
    require_once 'fonctions.php';
    $pdo = getDB();


    //Dans le cas où l'on reçoit des données defacebookData.
    if(isset($_POST['Posts']) && isset($_POST['Comm']) && isset($_POST['Donnee']))
    {
        
        $textes = $_POST['Posts'];
        $comms = $_POST['Comm'];
        $donnees = $_POST['Donnee'];

        $indiceMsg = 0;
        try
        {
            //on traite chaque message et on l'enregistre dans la BDD
            for($i=0;$i<sizeof($donnees[0]);$i++) 
            {   
                $type = $donnees[0][$i];
                $date = $donnees[1][$i];
                $lien = $donnees[2][$i];
                $texte = $textes[$i];
               
                
                //Ajoute les caractères d'échappement au texte
                $texteEnregistre = addslashes($texte);
                //On enregistre la donnée
                $req = "INSERT INTO message VALUES (null, '$type', '$date', '$lien', '$texteEnregistre')";
                $prep = $pdo->prepare($req);
                $prep->execute();
                $prep->closeCursor();
                $prep = null;

                //On récupère l'ID de la donnée enregistrée
                $req = "select max(Id) from message";
                $prep = $pdo->prepare($req);

                $prep->execute();

                $IdMessage = $prep->fetch();
                $prep->closeCursor();
                $prep = NULL;
                
                
                $bool = 1;
                //On enregistre les commentaires qui lui sont associés
                for($j=0;$j<sizeof($comms[0]) && $bool == 1;$j++)
                {
                    //On vérifie que le commentaire est bien en lien avec le post en question
                    if($comms[1][$j] == $indiceMsg)
                    {
                        $comm = $comms[0][$j];

                        //Ajout des caractères d'échappement
                        $commEnregistre = addslashes($comm);
                        //On enregistre le commentaire
                        $req = "INSERT INTO commentaire VALUES (null, '$commEnregistre', '$IdMessage[0]')";
                        $prep = $pdo->prepare($req);
                        $prep->execute();
                        $prep->closeCursor();
                        $prep = null;

                    }
                    //Si l'on a ajouté tous les commentaires par rapport à ce message, on sort de la boucle
                    else if($comms[1][$j] > $indiceMsg)
                    {
                        $bool = 0;
                    }
                }  
                $indiceMsg ++;
            }
            //Si toutes les insertions ont réussies.
            echo 'succes';
        }
        //S'il y a eu un échec quelque part, on rentre dans le bloc catch et affiche l'erreur.
        catch (Exception $ex) 
        {
                echo "Erreur : " . $ex->getMessage();
        }
        
    }
?>