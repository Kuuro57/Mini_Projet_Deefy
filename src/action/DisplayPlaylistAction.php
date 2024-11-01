<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;
use \iutnc\deefy\render\AudioListRenderer;
use \iutnc\deefy\repository\DeefyRepository;

/*
Classe qui affiche une playlist en session
*/

class DisplayPlaylistAction extends Action {

    public function execute() : string {

        $res = "<b> Affichage de la Playlist : </b>\n<br>";

        if (!isset($_SESSION['user'])) {
            return '<b> Veuillez vous connecter pour utiliser toutes les fonctionnalités ! </b>';
        }
        else if (!isset($_SESSION['playlist'])) {
            return '<b> Pas de playlist en session ! </b>';
        }

        else {

            if (isset($_GET['id'])) {
                $_SESSION['playlist'] = $_GET['id'];
                $_GET['id'] = null;
            }

            $pl_id = $_SESSION['playlist'];
            $res .= "<b> Playlist en session : </b>";
            $r = DeefyRepository::getInstance();
            $renderer = new AudioListRenderer($r->findPlaylist($pl_id));
            $res .= $renderer->render();
            $add_track = new AddPodcastTrackAction();
            $res .= $add_track->execute();

        }

        return $res;

    }

}