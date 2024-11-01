<?php

namespace iutnc\deefy\action;



use iutnc\deefy\auth\Auth;

/**
 * Classe qui représente l'action de créer un nouveau compte
 */
class AddUserAction extends Action {

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
    public function execute() : string {

        // Si la méthode utilisée est de type GET
        if ($this->http_method == "GET") {
            // On renvoie le formulaire
            $res = '<form method="post" action="?action=add-user">
                    <input type="email" name="email" placeholder="email" autofocus>
                    <input type="text" name="passwd1" placeholder="password 1">
                    <input type="text" name="passwd2" placeholder="password 2">
                    <input type="submit" name="connex" value="Connexion">
                    </form>';
        }
        // Sinon
        else {
            // On récupère l'email en le filtrant
            $e = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            // On récupère les mots de passe
            $p1= $_POST['passwd1'];
            $p2 = $_POST['passwd2'];
            // Si les deux mots de passe sont identiques
            if ($p1 === $p2) {
                // On enregistre le nouveau compte / utilisateur dans la BDD
                $res = "<p>" . Auth::register($e, $p1) . "</p>";
            }
            // Sinon
            else {
                // On réaffiche le formulaire
                $res = '<p>Mot de passe 1 et 2 différents</p>
                <form method="post" action="?action=add-user">
                <input type="email" name="email" placeholder="email" autofocus>
                <input type="text" name="passwd1" placeholder="password 1">
                <input type="text" name="passwd2" placeholder="password 2">
                <input type="submit" name="connex" value="Connexion">
                </form>';
            }
        }
        // On retourne le résultat
        return $res;

    }
}