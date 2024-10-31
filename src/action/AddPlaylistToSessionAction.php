<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;
use \iutnc\deefy\audio\lists\Playlist;
use \iutnc\deefy\repository\DeefyRepository;

/*
Classe qui renvoie un code HTML contenant un formulaire pour donner le nom de la playlist que l'on veut créé
*/

class AddPlaylistToSessionAction extends Action
{
    // attribut Playlist
    // récupère la playlist passée en paramètre GET et désérialise
    private int $id;
    private Playlist $playlist;


    public function execute(): string
    {
        //initialisation de la playlist
        $this->setPlaylist();
        $res =" ";
        $playlist_display = new DisplayPlaylistAction();
        $add_track = new AddPodcastTrackAction();
        // Si il y a déjà une playlist de créée
        if (isset($_SESSION['playlist'])) {
            // si la playlist de session est n'est pas la meme que l'attribut playlist
            // on met l'attribut playlist dans la session
            // on retourne un message de confirmation
            if ($_SESSION['playlist'] != $this->playlist->__get("id")) {
                $_SESSION['playlist'] = $this->playlist->__get("id");
                $res = 'Playlist ajoutée à la session';
                $res .= $playlist_display->execute();

            } else {
                $res = 'Playlist déjà en session';
                $res .= $playlist_display->execute();
            }
        }
        // si il n'y a pas de playlist en session
        // on met l'attribut playlist dans la session
        // on retourne un message de confirmation
        else {
            $_SESSION['playlist'] = $this->playlist->__get("id");

            $res = 'Playlist ajoutée à la session s';
            $res .= $playlist_display->execute();
        }
        return $res;
    }

    // récupère la playlist passée en paramètre GET et désérialise
    public function setPlaylist(): AddPlaylistToSessionAction
    {
        // récupération de la playlist passée en paramètre GET
        // reconstruction de l'objet Playlist
        $r = DeefyRepository::getInstance();


        $id = $_GET['id'];

        $this->playlist = $r->findPlaylist((int)$id);
        return $this;

    }
}