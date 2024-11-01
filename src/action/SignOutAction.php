<?php

namespace iutnc\deefy\action;



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
     * @return string Message indiquant que la déconnexion s'est bien déroulé
     */
    public function execute() : string{

        // On enlève de la session l'email et le rôle
        $_SESSION['user'] = null;
        $_SESSION['role'] = null;

        // On enlève la playlist en session
        $_SESSION['playlist'] = null;

        // On retourne un message
        return 'Deconnection bien effectuée !';

    }
}