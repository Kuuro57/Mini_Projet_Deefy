<?php

namespace iutnc\deefy\action;



/**
 * Classe qui représente l'action par défaut
 */
class DefaultAction extends Action {

    /**
     * Méthode qui execute l'action
     * @return string
     */
    public function execute() : string {

        return "<b> Bienvenue ! </b>";

    }

}