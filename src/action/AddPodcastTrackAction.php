<?php

namespace iutnc\deefy\action;


use \iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use \iutnc\deefy\repository\DeefyRepository;



/**
 * Classe qui ajoute 1 track dans la playlist
 */
class AddPodcastTrackAction extends Action {

    /**
     * Méthode qui execute l'action
     * @return string
     * @throws InvalidPropertyNameException | InvalidPropertyValueException
     */
    public function execute() : string {

        // Si l'utilisateur n'est pas connecté
        if (!isset($_SESSION['user'])) {
            return '<b> Veuillez vous connecter pour utiliser toutes les fonctionnalités ! </b>';
        }
        // Si il n'y a pas de session
        else if (!isset($_SESSION['playlist'])) {
            return '<b> Pas de playlist en session ! </b>';
        }
        // Sinon
        else {
            // Si la méthode HTTP est de type GET
            if ($this->http_method === 'GET') {
                // Afficher une formulaire d'ajout d'une track
                return '
                    <form method="post" name="nom_track" action="?action=add-track" enctype="multipart/form-data">
                        Nom de la piste : <input type="text" name="nom_track" placeholder="<nom>">
                        Nom de l\'artiste : <input type="text" name="artiste" placeholder="<artiste>">
                        Fichier Audio : <input type="file" name="fichier" placeholder="<fichier>">
                        Nom de l\'album : <input type="text" name="nom_album" placeholder="<album>">
                        Numero de la piste : <input type="text" name="numero" placeholder="<piste>">

                        <button type="submit" name="valider"> Valider </button>
                    </form>
                ';
            }
            // Sinon si la méthode HTTP est de type POST
            else if ($this->http_method === 'POST') {
                // On récupère et filtre les données du formulaire
                $nomTrack = filter_var($_POST['nom_track'], FILTER_SANITIZE_SPECIAL_CHARS);
                $artiste = filter_var($_POST['artiste'], FILTER_SANITIZE_SPECIAL_CHARS);
                $nomAlbum = filter_var($_POST['nom_album'], FILTER_SANITIZE_SPECIAL_CHARS);
                $numero = filter_var($_POST['numero'], FILTER_SANITIZE_SPECIAL_CHARS);
                // On génère une id unique
                $id = uniqid() . '.mp3';
                // On récupère l'id de la playlist en session
                $id_playlist = $_SESSION['playlist'];
                // On récupère la playlist
                $r = DeefyRepository::getInstance();
                $playlist = $r->findPlaylist($id_playlist);
                // Si le fichier audio envoyé par le client est bon
                if ($this->verifFichier($id)) {
                    // On créé la track avec les données donné par le formulaire
                    $track = new AlbumTrack($nomTrack, $id, $nomAlbum, $numero);
                    $track->setArtiste($artiste);
                    // On ajoute la track dans la playlist
                    $playlist->addTrack($track);
                    // On ajoute la track dans la BDD
                    $track = $r->saveAudioTrack($track);
                    // On lie la track à la playlist (dans la BDD) qui se trouve dans la session
                    $r->addTrackToPlaylist($track->__get("id"), $id_playlist);
                    // On informe que la track à bien été ajoutée et que la playlist a été mise en session
                    return "<b> Track ajouté à la playlist et sauvegardé en session ! </b>";   
                }
                // Sinon
                else {
                    // On informe que le fichier n'est pas correct
                    return "<b> Erreur : Track non ajoutée à la playlist ! (fichier audio incorrect) </b>";
                }

            }
            // Sinon
            else {
                // On informe d'une erreur
                return 'Erreur : Méthode inconnue';
            }
        }
    }



    /**
     * Méthode qui vérifie le fichier audio envoyer par le client
     */
    public function verifFichier(string $id) : bool {
        // Si aucun fichier n'a été envoyé
        if (count($_FILES) === 0) {
            // On renvoie false
            return false;
        }
        // Si le fichier a été envoyé par la méthode POST
        if (is_uploaded_file($_FILES['fichier']['tmp_name'])) {
            // Si l'extension du fichier n'est pas en .mp3
            if (!(substr($_FILES['fichier']['name'],-4) === '.mp3')) {
                // On renvoie false
                return false;
            }
            // On met comme type au fichier audio audio/mpeg
            $_FILES['fichier']['type'] = 'audio/mpeg';
            // On met ce fichier dans le répertoire /audio
            $dir = $_SERVER['DOCUMENT_ROOT'] . '/TDS3WEB/TD13/audio/' . $id;
            move_uploaded_file($_FILES['fichier']['tmp_name'], $dir);
            // On renvoie true (fichier correct)
            return true;
        }
        // Sinon
        else {
            // On renvoie false (fichier incorrect)
            return false;
        }
    }

}