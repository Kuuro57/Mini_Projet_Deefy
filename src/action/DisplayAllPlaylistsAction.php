<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use \iutnc\deefy\render\AudioListRenderer;
use \iutnc\deefy\repository\DeefyRepository;

/*
Classe qui affiche une playlist en session
*/

/**
 *
 */
class DisplayAllPlaylistsAction extends Action {

    /**
     * @return string
     */
    public function execute() : string {

        // récupération de la liste des playlists
        $r = DeefyRepository::getInstance();
        $playlists = $r->findAllPlaylist();

        if ($playlists === null) {
            // Retourner un message d'erreur si la récupération échoue
            return '<b>Erreur lors de la récupération des playlists !</b>';
        }

        $res = "<b> Affichage des Playlists : </b>\n<br>";

        if (empty($playlists)) {
            return '<b> Pas de playlist en base de données ! </b>';
        } else {
            $res .= "<b> Playlists en base de données : </b>";

            $ids = []; // Initialisation du tableau des ids
            $noms = []; // Initialisation du tableau des noms
            $liens = []; // Initialisation du tableau des liens

            for ($i = 0; $i < count($playlists); $i++) {
                $pl = $playlists[$i];

                // Récupération des informations de la playlist
                $id = $pl->getId();       // ID de la playlist
                $nom = $pl->__get("nom");      // Nom de la playlist
                // Remplissage des tableaux
                $ids[$i] = $id;
                $noms[$i] = $nom;
                // Création du lien avec la syntaxe correcte pour intégrer les variables
                $liens[$i] = "<a href='?action=add-playlist-to-session&id={$ids[$i]}'>{$noms[$i]}</a><br>";
            }
            // Affichage des liens
            foreach ($liens as $lien) {
                $res .= $lien;
            }
        }

        return $res;
    }


}