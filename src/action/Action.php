<?php

namespace iutnc\deefy\action;



/**
 * Classe abstraite qui représente une action
 */
abstract class Action {

    // Attributs
    protected ?string $http_method = null; // Méthode utilisée par une requête HTTP
    protected ?string $hostname = null; // Nom du host
    protected ?string $script_name = null; // Nom du script



    /**
     * Constructeur de la classe
     */
    public function __construct(){
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }



    /**
     * Méthode qui execute une action
     * @return string
     */
    abstract public function execute() : string;
    
}