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



/**
 * Classe qui représente l'accès à la BDD
 */
class DeefyRepository {

    // Attributs
    private PDO $pdo; // Objet permettant d'accéder à la BDD et d'executer les requêtes SQL
    private static ?array $config = []; // Liste qui contient les configurations pour accéder à la BDD
    private static ?DeefyRepository $instance = null; // Instance unique de la classe DeefyRepository



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
     * @throws InvalidPropertyValueException
     */
    public function findAllPlaylist() : array {

        $playlists = [];

        $querySQL = 'SELECT id FROM playlist'; // Inclure les colonnes nécessaires uniquement
        $statement = $this->pdo->prepare($querySQL);

        // Exécuter la requête et vérifier son succès
        if ($statement->execute()) {
            foreach ($statement->fetchAll() as $row) {
                if (!empty($row['id'])) {  // Vérifier que les données sont valides
                    $pl = $this->findPlaylist((int) $row['id']);
                    $playlists[] = $pl;
                }
            }
        } else {
            // Gérer une exécution échouée (en option : log ou exception)
            echo "Erreur lors de l'exécution de la requête pour récupérer les playlists.";
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
        $pl->setId((int)$row['id']); // associe l'id à la playlist récupérée



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
     * @throws InvalidPropertyNameException
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



    /**
     * Méthode qui check si l'email donné en paramètre est présent dans la BDD et renvoie son mot de passe hashé
     * @param string $email Une adresse email
     * @return array Liste contenant le mot de passe hashé correspondant à l'email et son role
     */
    public function checkExistingEmail(string $email) : array {

        // Requête SQL
        $querySQL = "SELECT passwd, role FROM User WHERE email = ? ";
        // Préparation de la requête
        $statement = $this->pdo->prepare($querySQL);
        $statement->bindParam(1,$email);
        // Execution de la requête
        $statement->execute();
        // On récupère les données sorties par la requête
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        // Si les données sont vides
        if (empty($data)) {
            // Le résultat est une liste vide
            $res = [];
        }
        // Sinon
        else {
            // Le résultat est une liste contenant le mot de passe hashé correspondant à l'email et son role
            $res = [
                'passwd' => $data['passwd'],
                'role' => $data['role']
            ];
        }
        // On retourne le résultat
        return $res;

    }



    /**
     * Méthode qui ajoute un nouvel utilisateur à la BDD
     * @param string $email Une adresse email
     * @param string $hashpasswd Un mot de passe hashé
     * @param int $role Un identifiant de rôle
     */
    public function addUser(string $email, string $hashpasswd, int $role) : void {

        // Requête SQL
        $querySQL = "INSERT INTO User (email, passwd, role) VALUES (?, ?, ?)";
        // Préparation de la requête
        $statement = $this->pdo->prepare($querySQL);
        $statement->bindParam(1,$email);
        $statement->bindParam(2,$hashpasswd);
        $statement->bindParam(3,$role);
        // Execution de la requête
        $data = $statement->execute();

    }



    /**
     * Méthode qui récupère les playlists d'un utilisateur
     * @param string $email L'email de l'utilisateur
     * @return array La liste des playlists de l'utilisateur
     * @throws InvalidPropertyValueException
     */
    public function getPlaylistsUser(string $email) : array {

        // Requête SQL qui récupère l'id des playlists appartenant à l'utilisateur
        $querySQL = "SELECT Playlist.id AS idPlaylist FROM Playlist
                     INNER JOIN User2Playlist ON User2Playlist.id_pl = Playlist.id
                     INNER JOIN User On User.id = User2Playlist.id_user
                     WHERE User.email = ?";
        // Préparation de la requête
        $statement = $this->pdo->prepare($querySQL);
        $statement->bindParam(1, $email);
        // Execution de la requête
        $statement->execute();

        $res = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            $playlist = $this->findPlaylist((int) $data['idPlaylist']);
            $res[] = $playlist;
        }

        return $res;

    }



    /**
     * Méthode qui lie une playlist à un utilisateur
     * @param string $email L'email de l'utilisateur
     * @param int $id_playlist L'id de la playlist à ajouter
     */
    public function addPlaylistToUser(string $email, int $id_playlist) : void {

        // Requête SQL qui récupère l'id associé à l'email
        $querySQL = "SELECT User.id as id FROM User WHERE email = ?";

        // Préparation de la requête
        $statement = $this->pdo->prepare($querySQL);
        $statement->bindParam(1,$email);

        // Execution de la requête
        $statement->execute();

        // On récupère l'id
        $id_email = $statement->fetch()['id'];




        // Requête SQL qui ajoute l'id d'une playlist à l'id d'un utilisateur
        $querySQL2 = "INSERT INTO User2Playlist (id_user, id_pl) VALUES (?, ?)";

        // Préparation de la requête
        $statement2 = $this->pdo->prepare($querySQL2);
        $statement2->bindParam(1,$id_email);
        $statement2->bindParam(2,$id_playlist);

        // Execution de la requête
        $statement2->execute();

    }

}