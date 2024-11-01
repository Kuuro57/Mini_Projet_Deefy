<?php

namespace iutnc\deefy\action;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\user\User;



/**
 * Classe qui affiche toutes les playlists d'un utilisateur
 */
class DisplayAllPlaylistsAction extends Action {

    /**
     * Méthode qui execute l'action
     * @return string
     * @throws InvalidPropertyNameException
     * @throws InvalidPropertyValueException
     */
    public function execute() : string {

        // Si l'utilisateur n'est pas connecté
        if (!isset($_SESSION['user'])) {
            return '<b> Veuillez vous connecter pour utiliser toutes les fonctionnalités ! </b>';
        }

        // On récupère l'email et le role
        $e = $_SESSION['user']['email'];
        $role = (int) $_SESSION['user']['role'];

        // On créé un utilisateur
        $u = new User($e, $role);
        $playlists = $u->getPlaylists();

        // Si l'utilisateur n'a pas de playlist
        if ($playlists === []) {
            // On renvoie un message disant qu'il n'y a pas de playlist
            return 'Vous n\'avez aucune playlist !';
        }

        // On boucle sur les playlists
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

        // On retourne les playlists sous forme de formulaire
        return $res . '</form>';
    }


}