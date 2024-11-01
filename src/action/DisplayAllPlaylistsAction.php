<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\exception\InvalidPropertyNameException;
use \iutnc\deefy\render\AudioListRenderer;
use \iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;

/*
Classe qui affiche une playlist en session
*/

/**
 *
 */
class DisplayAllPlaylistsAction extends Action {

    /**
     * Méthode qui execute l'action
     * @return string
     * @throws InvalidPropertyNameException
     */
    public function execute() : string {

        if (!isset($_SESSION['user'])) {
            return '<b> Veuillez vous connecter pour utiliser toutes les fonctionnalités ! </b>';
        }

        $e = $_SESSION['user']['email'];
        $role = (int) $_SESSION['user']['role'];

        $u = new User($e);

        // Si l'utilisateur a un rôle standard
        if ($role === 1) {
            // On recupère les playlists de l'utilisateur
            $playlists = $u->getPlaylists();
        }
        // Si l'utilisateur à un rôle admin
        else if ($role === 100) {
            $playlists = $u->getPlaylistsADMIN();
        }

        // Si l'utilisateur n'a pas de playlist
        if ($playlists === []) {
            // On renvoie un message disant qu'il n'y a pas de playlist
            return 'Vous n\'avez aucune playlist !';
        }

        for ($i = 0; $i < count($playlists); $i++) {
            $pl = $playlists[$i];

            // Récupération des informations de la playlist
            $id = $pl->getId();       // ID de la playlist
            $nom = $pl->__get("nom");      // Nom de la playlist
            // Remplissage des tableaux
            $ids[$i] = $id;
            $noms[$i] = $nom;
            // Création du lien avec la syntaxe correcte pour intégrer les variables
            $liens[$i] = "<a href='?action=display-playlist&id={$ids[$i]}'>{$noms[$i]}</a><br>";
        }
        // Affichage des liens
        $res = '<form method="get">';
        // Affichage des liens
        foreach ($liens as $lien) {
            $res .= $lien;
        }

        return $res . '</form>';
    }


}