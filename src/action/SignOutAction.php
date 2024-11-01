<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use PDO;
use iutnc\deefy\render\AudioListRenderer as AudioListRenderer;



/**
 * Classe qui représente l'action de déconnexion
 */
class SignOutAction extends Action {

    /**
     * Constructeur de la classe
     */
    public function __construct(){
        parent::__construct();
    }


    /**
     * Méthode qui execute l'action
     * @return string
     */
    public function execute() : string{

        // On enlève de la session l'email et le rôle
        $_SESSION['user'] = null;

        // On retourne un message
        return 'Deconnection bien effectuée !';

    }
}