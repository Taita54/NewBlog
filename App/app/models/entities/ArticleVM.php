<?php

namespace app\models\entities;

class ArticleVM extends MediaVM
{

    public function __construct(
        // protected string $AnnoSportivo='',
        protected string $RowVersion = '',
        protected string $nome = '',
        protected string $artThumb = '',
        protected string $foto = ''
    ) {
        parent::class;
    }

    // public function getAnnoSportivo()   :string{
    //     return $this->AnnoSportivo;
    // }
    public function getRowVersion()
    {
        return itaDate($this->RowVersion);
    }
    public function getTeamName()
    {
        return $this->nome;
    }
    public function getArtThumb()
    {
        return $this->artThumb;
    }
    public function getTeamAvatar()
    {
        return $this->foto;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function getFoto()
    {
        return $this->foto;
    }
    // public function setAnnoSportivo($val){
    //     $this->AnnoSportivo=$val;
    // }  
    public function setRowVersion($val)
    {
        $this->RowVersion = $val;
    }
    public function setTeamName($val)
    {
        $this->nome = $val;
    }
    public function setArtThumb($val)
    {
        $this->artThumb = $val;
    }
    public function setTeamAvatar($val)
    {
        $this->foto = $val;
    }
    public function setNome($val)
    {
        $this->nome = $val;
    }
    public function setFoto($val)
    {
        if ($val) {
            $this->foto = $val;
        } else {
            $this->foto = '';
        }
    }
}
