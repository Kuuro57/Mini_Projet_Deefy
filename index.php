<?php declare (strict_types=1);

// ################################
//       AUTOLOADER (COMPOSER)
// ################################
/*
    Pour générer l'autoloader composer :
        1 -> Aller sur https://getcomposer.org/download/ et rentrer les lignes de commandes 
            (dans votre dossier où il y a vos classes) pour installer composer
        2 -> Aller sur le terminal et faire : 'php composer.phar install'
        3 -> Mettre la ligne de commandes ci-dessous dans l'index
*/
require_once 'vendor/autoload.php'; 




// ################################
//               USE
// ################################
use \iutnc\deefy\dispatch\Dispatcher;
use \iutnc\deefy\repository\DeefyRepository;



// ################################
//             MAIN
// ################################


session_start();
DeefyRepository::setConfig('conf.db.ini');


if (!isset($_GET['action'])) {
    $_GET['action'] = 'default';
}

$dispatcher = new Dispatcher();
$dispatcher->run();