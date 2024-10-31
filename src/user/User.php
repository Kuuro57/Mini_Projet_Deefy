<?php

namespace iutnc\deefy\user;

use iutnc\deefy\repository\DeefyRepository;
use PDO;
use iutnc\deefy\audio\lists\Playlist as Playlist;


/**
 * Classe
 */
class User{

    // Attributs
    private string $email;
    private string $password;
    private int $role;



    /**
     * Constructeur de la classe
     * @param string $e Une adresse email
     * @param string $p Un mot de passe
     * @param int $r Un role
     */
    public function __construct(string $e, string $p, int $r){
        $this->email = $e;
        $this->password = $p;
        $this->role = $r;
    }



    /**
     * Méthode qui récupère la liste des playlists de l'utilisateur
     * @return string La liste des playlists de l'utilisateur
     */
    public function getPlaylists() {

        $bd = DeefyRepository::getInstance();
        return $bd->getPlaylistsUser($this->email, $this->password, $this->role);

    }

}