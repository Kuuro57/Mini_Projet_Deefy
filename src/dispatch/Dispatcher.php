<?php

namespace iutnc\deefy\dispatch;

use \iutnc\deefy\action\DefaultAction;
use \iutnc\deefy\action\DisplayPlaylistAction;
use \iutnc\deefy\action\AddPlaylistAction;
use \iutnc\deefy\action\AddPodcastTrackAction;


class Dispatcher {

    /**
     * Méthode qui lance le dispatcher
     */
    public function run() : void {

        // On regarde (avec un switch) quelle action faire en récupérant l'action
        switch ($_GET['action']) {

            case "default" :
                $class = new DefaultAction();
                break;

            case "add-playlist" :
                $class = new AddPlaylistAction();
                break;

            case "add-track" :
                $class = new AddPodcastTrackAction();
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
     */
    private function renderPage(string $html) : void {

        echo <<<END

            <html>

                <center><h1> Ma super page de musiques </h1></center>

                <div>
                    <form method="get">
                        <button name='action' value='default'> Méthode par défaut </button>
                        <button name='action' value='display-playlist'> Afficher la playlist </button>
                        <button name='action' value='add-playlist'> Ajouter une playlist à la session </button>
                        <button name='action' value='add-track'> Ajouter une track à la playlist </button>
                    </form>
                </div>

                <br>
                <br>

                $html

            </html>

        END;

    }


}