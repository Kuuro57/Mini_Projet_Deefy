<?php

namespace iutnc\deefy\user;

use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\repository\DeefyRepository;



/**
 * Classe qui permet d'afficher d'afficher les playlists en fonction du role (standard ou admin)
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
     * @throws InvalidPropertyValueException
     */
    public function getPlaylists(): array {

        $bd = DeefyRepository::getInstance();
        return $bd->getPlaylistsUser($this->email);

    }



    /**
     * Méthode qui récupère toutes les playlists de la BDD
     * @return void
     */
    public function getPlaylistsADMIN() : array {

        $bd = DeefyRepository::getInstance();
        return $bd->findAllPlaylist();
    }

}