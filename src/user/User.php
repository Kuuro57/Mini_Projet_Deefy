<?php

namespace iutnc\deefy\user;

use iutnc\deefy\repository\DeefyRepository;


/**
 * Classe
 */
class User{

    // Attributs
    private string $email;



    /**
     * Constructeur de la classe
     * @param string $e Une adresse email
     */
    public function __construct(string $e){
        $this->email = $e;
    }



    /**
     * Méthode qui récupère la liste des playlists de l'utilisateur
     * @return array La liste des playlists de l'utilisateur
     */
    public function getPlaylists(): array {

        $bd = DeefyRepository::getInstance();
        return $bd->getPlaylistsUser($this->email);

    }

}