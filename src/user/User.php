<?php

namespace iutnc\deefy\user;

use Exception;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\repository\DeefyRepository;



/**
 * Classe qui permet d'afficher d'afficher les playlists en fonction du role (standard ou admin)
 */
class User{

    // Attributs
    private string $email;
    private int $role;


    /**
     * Constructeur de la classe
     * @param string $e Une adresse email
     * @param int $r L'identifiant du role
     */
    public function __construct(string $e, int $r){
        $this->email = $e;
        $this->role = $r;
    }


    /**
     * Méthode qui récupère la liste des playlists de l'utilisateur
     * @return array La liste des playlists de l'utilisateur
     * @throws InvalidPropertyValueException
     * @throws Exception
     */
    public function getPlaylists(): array {

        if ($this->role === 1) {
            $bd = DeefyRepository::getInstance();
            return $bd->getPlaylistsUser($this->email);
        }
        else if ($this->role === 100) {
            $bd = DeefyRepository::getInstance();
            return $bd->findAllPlaylist();
        }
        else {
            throw new Exception('Role innatendu');
        }

    }

}