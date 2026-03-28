<?php

namespace app\models\entities;

/**
 * Description of MediaVM
 *
 * @author giovi
 */
class MediaVM
{
    // protected string $AnnoSportivo = '';// (per articoli albums avvisi)
    // protected string $Description=''; //(articoli, albums, pubblicazioni, video moduli)
    // protected int $Size=0; //(albums, articoli, pubblicazioni)
    // public string $photoAlbum='';//solo per files riferiti ad un evento

    public function __construct(
        protected int $ID = 0,
        protected int $IdDestination = 0,
        protected int $IdMediaType = 0,
        protected ?string $Description = null,
        protected ?string $Capitolo = null,
        protected ?string $AnnoSportivo = null,
        protected ?string $Area = null, // Nota: null anziché ''
        protected ?string $Titolo = null, // Nota: null anziché ''
        protected ?string $Data_creazione = null, // Nota: null anziché ''
        protected ?string $Data_scadenza = null, // Nota: null anziché ''
        protected ?string $Alt_text = null, // Nota: null anziché ''
        protected ?string $FileName = null, // Nota: null anziché ''
        protected ?bool $Draft = false,
        protected ?string $Author = null, // Nota: null anziché ''
        protected ?string $Publisher = null, // Nota: null anziché ''
        protected int $Size = 0
    ) {
        $this->Area = $this->Area ?? '';
        $this->Titolo = $this->Titolo ?? '';
        $this->Data_creazione = $this->Data_creazione ?? '';
        $this->Data_scadenza = $this->Data_scadenza ?? '';
        $this->Alt_text = $this->Alt_text ?? '';
        $this->FileName = $this->FileName ?? '';
        $this->Author = $this->Author ?? '';
        $this->Publisher = $this->Publisher ?? '';
        $this->Capitolo = $this->Capitolo ?? '';
        $this->Description = $this->Description ?? '';
        $this->AnnoSportivo = $this->AnnoSportivo ?? '';
    }

    public function getID()
    {
        return $this->ID;
    }
    public function getIdDestination()
    {
        return $this->IdDestination;
    }
    public function getDescription(): string|null
    {
        return $this->Description;
    }
    public function getIdMediaType()
    {
        return $this->IdMediaType;
    }
    public function getArea()
    {
        return $this->Area;
    }
    public function getTitolo()
    {
        return $this->Titolo;
    }
    public function getDataCreazione()
    {
        return itaDate($this->Data_creazione);
    }
    public function getDataScadenza()
    {
        return itaDate($this->Data_scadenza);
    }
    public function getAlt_Text()
    {
        if ($this->Alt_text === null) {
            return ''; // Restituisce una stringa vuota invece di null
        }
        return $this->Alt_text;
    }
    public function getFileName()
    {
        return $this->FileName;
    }
    public function isDraft()
    {
        return $this->Draft;
    }
    public function getAuthor()
    {
        return $this->Author;
    }
    public function getPublisher()
    {
        return $this->Publisher;
    }

    public function getSummaryLine()
    {
        $base = "{$this->Titolo} <br/>";
        $base .= "(del {$this->Data_creazione} di {$this->Author})<br/>";
        $base .= "revisione di: {$this->Publisher}";
        return $base;
    }

    public function getAnnoSportivo(): string
    {
        return $this->AnnoSportivo;
    }

    public function getCapitolo(): string
    {
        if ($this->Capitolo === null) {
            return ''; // Restituisce una stringa vuota invece di null
        }
        return $this->Capitolo;
    }
    public function getSize(): int
    {
        return $this->Size;
    }
    public function setID($val)
    {
        $this->ID = $val;
    }
    public function setIdDestination($val)
    {
        $this->IdDestination = $val;
    }
    public function setIdMediaType($val)
    {
        $this->IdMediaType = $val;
    }
    public function setArea($val)
    {
        $this->Area = $val;
    }
    public function setTitolo($val)
    {
        $this->Titolo = $val;
    }
    public function setDescription($val)
    {
        $this->Description = $val;
    }
    public function setData_creazione($val)
    {
        $this->Data_creazione = $val;
    }
    public function setData_scadenza($val)
    {
        $this->Data_scadenza = $val;
    }
    public function setAlt_text($val)
    {
        $this->Alt_text = $val;
    }
    public function setFileName($val)
    {
        $this->FileName = $val;
    }
    public function setDraft($val)
    {
        $this->Draft = $val;
    }
    public function setAuthor($val)
    {
        $this->Author = $val;
    }
    public function setPublisher($val)
    {
        $this->Publisher = $val;
    }
    public function setAnnoSportivo($val)
    {
        $this->AnnoSportivo = $val;
    }
    public function setCapitolo($val): void
    {
        $this->Capitolo = $val;
    }
    public function setSize($val): void
    {
        $this->Size = $val;
    }
}
