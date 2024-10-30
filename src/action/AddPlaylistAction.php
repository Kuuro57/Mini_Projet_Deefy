<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;
use \iutnc\deefy\audio\lists\Playlist;
use \iutnc\deefy\repository\DeefyRepository;

/*
Classe qui renvoie un code HTML contenant un formulaire pour donner le nom de la playlist que l'on veut créé
*/

class AddPlaylistAction extends Action {

    public function execute() : string {

        // Si il y a déjà une playlist de créée
        if (!isset($_SESSION['playlist'])) {
            
            // Si la méthode HTTP est de type GET (= que l'on veut créer une playlist)
            if ($this->http_method === 'GET') {

                // On retourne un code HTML avec le formulaire
                return '
                <form method="post" name="nom" action="?action=add-playlist">
                    <label> Nom de la playlist : 
                        <input type="text" name="nom" placeholder="<nom>">
                    </label>

                    <button type="submit" name="valider"> Valider </button>
                </form>
                ';

            }
            // Si la méthode HTTP est de type POST (= que l'on a répondu au formulaire)
            else if ($this->http_method === 'POST') {

                // Récupération de l'instance unique pour l'accès à la BDD
                $r = DeefyRepository::getInstance();

                // On récupère le nom de la playlist en filtrant les données présentes dans le POST
                $nomPlaylist = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
                // On créé la playlist avec le nom récupéré et une liste vide
                $playlist = new Playlist($nomPlaylist, []);
                // On ajoute la playlist dans la BDD
                $playlist = $r->savePlaylist($playlist);
                // On met dans la session "playlist" l'id de la playlist
                $_SESSION['playlist'] = $playlist->__get("id");
                // On informe que la playlist a bien été créée
                $res = 'Création et mise en session de l\'id de la playlist <b> réussie </b>';
                // On affiche un lien qui permet de directement ajouter une musique à la playlist
                $res .= '<a href="?action=add-track"> Ajouter une piste </a>';
                // On retourne tout ce que l'on veut afficher
                return $res;

            }
            // Sinon
            else {
                // On indique une erreur
                return 'Erreur : Méthode inconnue';
            }
        }
        // Sinon
        else {
            // On informe qu'une playlist existe déjà
            return "<b> Playlist déjà existante ! </b>";
        }

    }

}