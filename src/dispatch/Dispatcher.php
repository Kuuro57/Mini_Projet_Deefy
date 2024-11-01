<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddUserAction;
use \iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayAllPlaylistsAction;
use \iutnc\deefy\action\DisplayPlaylistAction;
use \iutnc\deefy\action\AddPlaylistAction;
use \iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\SignOutAction;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\repository\DeefyRepository;


/**
 * Classe qui représente le dispatcher
 */
class Dispatcher {

    /**
     * Méthode qui lance le dispatcher
     * @throws InvalidPropertyNameException | InvalidPropertyValueException
     */
    public function run() : void {

        // On regarde (avec un switch) quelle action faire en récupérant l'action
        switch ($_GET['action']) {

            case "default" :
                $class = new DefaultAction();
                break;

            case 'add-user':
                $class = (new AddUserAction());
                break;

            case 'sign-in':
                $class = (new SigninAction());
                break;

            case 'sign-out':
                $class = (new SignOutAction());
                break;

            case "add-playlist" :
                $class = new AddPlaylistAction();
                break;

            case "add-track" :
                $class = new AddPodcastTrackAction();
                break;

            case "display-all-playlists" :
                $class = new DisplayAllPlaylistsAction();
                break;

            default :
                $class = new DisplayPlaylistAction();
                break;

        }

        // On affiche la page en executant la méthode execute d'une classe Action
        $this->renderPage($class->execute());

    }



    /**
     * Méthode qui ajoute le morceau de page à la page complète
     * @throws InvalidPropertyNameException | InvalidPropertyValueException
     */
    private function renderPage(string $html) : void {

        // Si l'utilisateur est connecté
        if (isset($_SESSION['user'])) {
            // On affiche l'email du compte auquel il est connecté
            $compte = 'Connecté au compte : ' . $_SESSION['user']['email'];
            // On affiche le bouton de déconnexion
            $btn_deco = "<button name='action' value='sign-out' class ='boutton'> signout </button>";
            // On affiche pas le bouton de connexion
            $btn_co = "";
            // On affiche pas le bouton de création de compte
            $btn_crea_compte = "";
        }
        // Sinon
        else {
            // On affiche un message
            $compte = 'Connectez-vous / Inscrivez-vous pour profiter du site !';
            // On n'affiche pas le bouton de déconnexion
            $btn_deco = "";
            // On affiche le bouton de connexion
            $btn_co = "<button name='action' value='sign-in' class='boutton'> signin </button>";
            // On affiche le bouton de création de compte
            $btn_crea_compte = "<button name='action' value='add-user' class ='boutton'> register </button>";
        }

        // Si il y a une playlist en session
        if (isset($_SESSION['playlist'])) {
            // On affiche le nom de la playlist en session
            $bd = DeefyRepository::getInstance();
            $pl = $bd->findPlaylist($_SESSION['playlist'])->getNom();
            $playlist = 'Playlist en session : ' . $pl;
        }
        // Sinon
        else {
            // On n'affiche rien
            $playlist = '';
        }


        // On affiche la page HTML complète
        echo <<<END

            <html lang="fr">
            
                <form method="get">
                    $btn_crea_compte
                    <tr>
                    $btn_co
                    <tr>
                    $btn_deco
                </form>
                
                $compte
                <br>
                $playlist
            
                <center><h1> Deefy Music ! </h1></center>
            
            
                <center><form method="get">
                
                    <button name='action' value='display-playlist'> Afficher la playlist </button>
                    <button name='action' value='add-playlist'> Ajouter une playlist </button>
                    <button name='action' value='add-track'> Ajouter une track à la playlist </button>
                    <button name='action' value='display-all-playlists'> Mes playlists </button>
                    
                </form></center>

                <br>
                <br>

                $html

            </html>

        END;

    }


}