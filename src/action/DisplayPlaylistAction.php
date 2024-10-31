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

        if (!isset($_SESSION['playlist'])) {
            return '<b> Pas de playlist en session ! </b>';
        }
        else {
            $pl_id = $_SESSION['playlist'];
            $res .= "<b> Playlist en session : </b>";
            $r = DeefyRepository::getInstance();
            $renderer = new AudioListRenderer($r->findPlaylist($pl_id));
            $res .= $renderer->render();
        }

        return $res;

    }

}