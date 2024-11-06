<?php
namespace iutnc\deefy\action;

use iutnc\deefy\auth\Auth;
use iutnc\deefy\exception\AuthException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\user\User;
use iutnc\deefy\render\AudioListRenderer as AudioListRenderer;



/**
 * Classe qui représente l'action de connexion à un compte
 */
class SigninAction extends Action {

    /**
     * Constructeur de la classe
     */
    public function __construct(){
        parent::__construct();
    }


    /**
     * Méthode qui execute l'action
     * @return string Un message au format HTML contenant un formulaire (lorsque méthode GET utilisée)
     *                Un message indiquant que la connexion s'est bien déroulée et la liste des playlists
     *                de l'utilisateur (lorsque méthode POST utilisée)
     * @throws InvalidPropertyValueException
     */
    public function execute() : string{

        // Si la méthode utilisée est de type GET
        if ($this->http_method == "GET") {

            // On affiche le formulaire
            $res='
                <h1> Connexion </h1>
                <form method="post" action="?action=sign-in">
                <input type="email" name="email" placeholder="email" class="input-field" autofocus>
                <input type="password" name="password" placeholder="mot de passe" class="input-field">
                <input type="submit" name="connex" value="Connexion" class="button">
                </form>';

        }
        // Sinon
        else {
            // On récupère l'email et le mot de passe
            $e = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $p = $_POST['password'];
            $bool = false;

            // On vérifie que l'utilisateur à bien rempli les champs
            try{
                $bool = Auth::authenticate($e, $p);
            }catch(AuthException $e){
                $res = "<p>Identifiant ou mot de passe invalide</p>";
            }

            // Si l'authentification à réussie
            if($bool){

                // On recupère les playlists de l'utilisateur
                $u = new User($e, (int) $_SESSION['user']['role']);
                $list_playlists =  $u->getPlaylists();

                // Si l'utilisateur n'a pas de playlists
                if ($list_playlists === []) {
                    // On renvoie un message
                    return 'Vous n\'avez aucune playlist !';
                }

                // On met la première playlist en session
                $_SESSION['playlist'] = $list_playlists[0]->getId();

                // On boucle sur les playlist et on récupère leur rendu HTML
                $t = '';
                foreach($list_playlists as $playlist){
                    $render = new AudioListRenderer($playlist);
                    $t .= $render->render();
                }

                // On affiche les playlists de l'utilisateur
                $res= <<<END
                    <h3>Connexion réussie pour $e</h3>
                    <h3>Playlists de l'utilisateur : </h3>
                    <br>
                    <br>
                    $t
                END;

            }

        }

        // On retourne le résultat
        return $res;
    }
}