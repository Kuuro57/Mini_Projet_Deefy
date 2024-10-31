<?php declare(strict_types=1);

namespace iutnc\deefy\repository;

use Exception;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use PDO;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;

class DeefyRepository {



    // Attributs
    private PDO $pdo;
    private static ?array $config = [];
    private static ?DeefyRepository $instance = null;



    /**
     * Constructeur de la classe
     */
    private function __construct(array $config) {

        $this->pdo = new PDO(
            $config['dns'], 
            $config['username'], 
            $config['password'], 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

    }


    /**
     * Méthode setConfig qui prend un nom de fichier qui contient les paramètres de connexion, charge le fichier et stocke
     * le tableau dans une variable static
     * @param string $file Nom de fichier
     * @throws Exception Erreur lors de la lecture du fichier de configuration
     */
    public static function setConfig(string $file) : void {

        $conf = parse_ini_file($file);

        if ($conf === false) {
            throw new Exception("Error reading configuration file");
        }

        self::$config = [
            'dns' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'username' => $conf['username'],
            'password' => $conf['password']
        ];

    }



    /**
     * Méthode getInstance qui retourne une instance de DeefyRepository
     * @return DeefyRepository Une instance de la classe
     */
    public static function getInstance() : DeefyRepository {
        
        if (self::$instance === null) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;

    }



    /**
     * Méthode qui récupère sous forme d'une liste toutes les playlists
     * @return Playlist[] une liste de d'objets Playlist
     */
    public function findAllPlaylist() : array {

        $playlists = [];

        $querySQL = 'SELECT * FROM playlist';

        $statement = $this->pdo->prepare($querySQL);
        $statement->execute();

        foreach ($statement->fetchAll() as $row) {
            $pl = new Playlist($row['nom'], []);
            $playlists[] = $pl;
        }

        return $playlists;

    }


    /**
     * Méthode qui récupère une playlist
     * @param int $id L'id de la Playlist
     * @return Playlist Objet de type Playlist
     * @throws InvalidPropertyValueException
     */
    public function findPlaylist(int $id) : Playlist {

        $querySQL1 = 'SELECT id, nom FROM playlist WHERE id = :id';

        $statement1 = $this->pdo->prepare($querySQL1);
        $statement1->execute(['id' => $id]);

        $row = $statement1->fetch();
        $pl = new Playlist($row['nom'], []);
        $pl->setId((int)$row['id']);



        $querySQL2 = 'SELECT * FROM playlist2track INNER JOIN track ON playlist2track.id_track = track.id
                      WHERE id_pl = :id';

        $statement2 = $this->pdo->prepare($querySQL2);
        $statement2->execute(['id' => $id]);


        foreach ($statement2->fetchAll() as $row) {
            if ($row['type'] === 'P') {
                $podcastTrack = new PodcastTrack($row['titre'], $row['filename']);
                $podcastTrack->setId((int)$row['id']);
                $podcastTrack->setGenre($row['genre']);
                $podcastTrack->setDuree((int)$row['duree']);
                $podcastTrack->setAuteur($row['auteur_podcast']);
                $podcastTrack->setDate($row['date_posdcast']);

                $pl->addTrack($podcastTrack);
            }
            else if ($row['type'] === 'A') {
                $albumTrack = new AlbumTrack($row['titre'], $row['filename'], $row['titre_album'], (int)$row['id']);
                $albumTrack->setArtiste($row['artiste_album']);
                $albumTrack->setAnnee((int)$row['annee_album']);
                $albumTrack->setGenre($row['genre']);
                $albumTrack->setDuree((int)$row['duree']);

                $pl->addTrack($albumTrack);
            }

        }




        return $pl;

    }



    /**
     * Méthode qui ajoute une playlist dans la BDD
     * @param Playlist $p Objet de type Playlist
     * @return Playlist Le nouvel objet Playlist (avec le nouvel id)
     */
    public function savePlaylist(Playlist $p) : Playlist {

        $querySQL = 'INSERT INTO playlist (nom) VALUES (:nom)';

        $statement = $this->pdo->prepare($querySQL);
        $statement->execute(['nom' => $p->__get('nom')]);

        $p->setId((int)$this->pdo->lastInsertId());

        // On retourne la playlist car on lui a changé son id
        return $p;

    }


    /**
     * Méthode qui ajoute une track à la BDD
     * @param AudioTrack $t Objet de type AudioTrack
     * @return AudioTrack Le nouvel objet AudioTrack (avec le nouvel id)
     * @throws InvalidPropertyNameException
     */
    public function saveAudioTrack(AudioTrack $t) : AudioTrack {
        
        if ($t instanceof PodcastTrack) {

            $querySQL = 'INSERT INTO track (titre, 
                                            genre, 
                                            duree, 
                                            filename, 
                                            type,
                                            auteur_podcast,
                                            date_posdcast) 
                        VALUES 
                                            (:titre,
                                            :genre, 
                                            :duree, 
                                            :filename, 
                                            "P",
                                            :auteur_podcast,
                                            :date_posdcast)';

            $statement = $this->pdo->prepare($querySQL);
            $statement->execute([
                'titre' => $t->__get('titre'),
                'genre' => $t->__get('genre'),
                'duree' => $t->__get('duree'),
                'filename' => $t->__get('nomFichierAudio'),
                'auteur_podcast' => $t->__get('auteur'),
                'date_posdcast' => $t->__get('date')
            ]);
        }

        else if ($t instanceof AlbumTrack) {

            $querySQL = 'INSERT INTO track (titre, 
                                            genre, 
                                            duree, 
                                            filename, 
                                            type, 
                                            artiste_album, 
                                            titre_album, 
                                            annee_album,
                                            numero_album) 
                        VALUES 
                                            (:titre,
                                            :genre, 
                                            :duree, 
                                            :filename, 
                                            "A", 
                                            :artiste_album, 
                                            :titre_album, 
                                            :annee_album,
                                            :numero_album)';

            $statement = $this->pdo->prepare($querySQL);
            $statement->execute([
                'titre' => $t->__get('titre'),
                'genre' => $t->__get('genre'),
                'duree' => $t->__get('duree'),
                'filename' => $t->__get('nomFichierAudio'),
                'artiste_album' => $t->__get('artiste'),
                'titre_album' => $t->__get('album'),
                'annee_album' => $t->__get('annee'),
                'numero_album' => $t->__get('numero')
            ]);
        }

        
        $t->setId((int)$this->pdo->lastInsertId());

        // On retourne l'AudioTrack car on lui a changé son id
        return $t;

    }


    /**
     * Méthode qui lie (dans la table playlist2track) une track avec une playlist
     * @param int $id_track Objet de type AudioTrack
     * @param int $id_playlist Objet de type playlist
     */
    public function addTrackToPlaylist(int $id_track, int $id_playlist) : void {

        $querySQL = 'INSERT INTO playlist2track (id_pl, id_track) VALUES (:id_pl, :id_track)';

        $statement = $this->pdo->prepare($querySQL);
        $statement->execute([
            'id_pl' => $id_playlist,
            'id_track' => $id_track
        ]);

    }

}