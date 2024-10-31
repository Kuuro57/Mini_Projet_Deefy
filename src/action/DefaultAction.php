<?php

namespace iutnc\deefy\action;

use \iutnc\deefy\action\Action;

class DefaultAction extends Action {



    /**
     * Méthode qui execute l'action
     * @return string Un message par défaut
     */
    public function execute() : string {

        return "<b> Bienvenue ! </b>";

    }

}