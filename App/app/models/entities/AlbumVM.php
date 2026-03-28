<?php

namespace app\models\entities;

/**
 * Description of AlbumVM
 *
 * @author giovi
 */
class AlbumVM extends MediaVM
{

    public function __construct(
        protected string $CourseName = '',
        protected string $nome = '',
        protected int $IDTipoEvento = 0,
        protected string $randImage = '',
        protected string $dataevento = '',
        protected string $foto = '',
    ) {
        parent::class;
    }

    public function getCourseName()
    {
        return $this->CourseName;
    }
    public function getTeamName()
    {
        return $this->nome;
    }
    public function getDataEvento()
    {
        return itaDate($this->dataevento);
    }
    public function getTeamAvatar()
    {
        return $this->foto;
    }
    public function getRandImage(): string
    {
        return $this->randImage;
    }
    public function getIDTipoEvento()
    {
        return $this->IDTipoEvento;
    }

    public function setCourseName(string $val)
    {
        $this->CourseName = $val;
    }
    public function setTeamName(string $val)
    {
        $this->nome = $val;
    }
    public function setDataEvento(string $val)
    {
        $this->dataevento = $val;
    }
    public function setTeamAvatar(string $val)
    {
        $this->foto = $val;
    }
    public function setRandImage(string $val): void
    {
        $this->randImage = $val;
    }
    public function setIDTipoEvento(int $val): void
    {
        $this->IDTipoEvento = $val;
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
