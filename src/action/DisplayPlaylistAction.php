<?php

namespace iutnc\deefy\action;

use iutnc\deefy\exception\InvalidPropertyNameException;
use \iutnc\deefy\render\AudioListRenderer;
use \iutnc\deefy\repository\DeefyRepository;



/**
 * Classe qui affiche une playlist en session
 */
class DisplayPlaylistAction extends Action {

    /**
     * Méthode qui execute l'action
     * @return string
     * @throws InvalidPropertyNameException
     */
    public function execute() : string {

        $res = "<b> Affichage de la Playlist : </b>\n<br>";

        // Si on a l'id d'une playlist dans la liste $_GET
        if (isset($_GET['id'])) {
            // On change l'id en session par l'id dans la liste $_GET
            $_SESSION['playlist'] = $_GET['id'];
            // On supprime l'id dans la liste $_GET
            $_GET['id'] = null;
        }

        // Si l'utilisateur n'est pas connecté
        if (!isset($_SESSION['user'])) {
            return '<b> Veuillez vous connecter pour utiliser toutes les fonctionnalités ! </b>';
        }
        // Sinon si l'utilisateur n'a aucune playlist en session
        else if (!isset($_SESSION['playlist'])) {
            return '<b> Pas de playlist en session ! </b>';
        }

        // Sinon
        else {

            // On récupère l'id de la playlist en session
            $pl_id = $_SESSION['playlist'];

            //$res .= "<b> Playlist en session : </b>";
            $r = DeefyRepository::getInstance();
            // On affiche la playlist
            $renderer = new AudioListRenderer($r->findPlaylist($pl_id));
            $res .= $renderer->render();
            // On affiche le formulaire d'ajout d'une track en bas de la playlist
            $add_track = new AddPodcastTrackAction();
            $res .= $add_track->execute();

        }

        // On retourne le résultat
        return $res;

    }

}