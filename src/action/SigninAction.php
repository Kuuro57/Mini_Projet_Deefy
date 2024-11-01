<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;
use PDO;
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
     */
    public function execute() : string{

        $res="";
        if($this->http_method == "GET") {

            $res='<form method="post" action="?action=sign-in">
                <input type="email" name="email" placeholder="email" autofocus>
                <input type="text" name="password" placeholder="mot de passe">
                <input type="submit" name="connex" value="Connexion">
                </form>';

        }
        else{

            $e = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $p = $_POST['password'];
            $bool = false;

            // On vérifie que l'utilisateur à bien rempli les champs
            try{
                $bool = \iutnc\deefy\auth\Auth::authenticate($e, $p);
            }catch(\iutnc\deefy\exception\AuthException $e){
                $res = "<p>Identifiant ou mot de passe invalide</p>";
            }

            if($bool){

                // On recupère les playlists de l'utilisateur
                $u = new User($e);
                $list_playlists =  $u->getPlaylists();

                // On met la première playlist en session
                $_SESSION['playlist'] = $list_playlists[0]->getId();

                foreach($list_playlists as $playlist){
                    $render = new AudioListRenderer($playlist);
                    $t .= $render->render();
                }

                $res= <<<END
                    <h3>Connexion réussie pour $e</h3>
                    <h3>Playlists de l'utilisateur : </h3>
                    <br>
                    <br>
                    $t
                END;

            }
            else {
                return 'Email inexistant !';
            }

        }

        return $res;
    }
}