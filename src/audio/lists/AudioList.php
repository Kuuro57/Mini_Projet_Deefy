<?php declare (strict_types=1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\exception\InvalidPropertyNameException;

require_once('src/exception/InvalidPropertyNameException.php');

class AudioList {

    // Attributs
    protected ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
    protected string $nom;
    protected int $nbPistes;
    protected int $dureeTotale;
    protected array $listAudioTrack;



    public function __construct(string $nom, array $list = []) {
        $this->id = null;
        $this->nom = $nom;
        $this->listAudioTrack = $list;
        $this->nbPistes = 0;
        $this->dureeTotale = 0;
        if ($list !== []) {
            foreach ($list as $key => $val) {
                $this->dureeTotale += $val->duree;
                $this->nbPistes++;
            }
        }
    }



    public function __get(string $attr) : mixed {
        if (property_exists($this, $attr)) {
            return $this->$attr;
        }
        throw new InvalidPropertyNameException("Invalid property name : $attr");
    }

    public function getNom(): string
    {
        if ($this->nom === null) {
            throw new InvalidPropertyNameException("Invalid property name : nom");
        }
        return $this->nom;

    }




    public function setId(int $i) : void {
        $this->id = $i;
    }

}