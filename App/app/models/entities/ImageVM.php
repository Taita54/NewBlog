<?php

namespace App\Models\Entities;

class ImageVM
{
    protected int $Id = 0;
    protected string $file = '';
    protected string $type = '';
    protected int $height = 0;
    protected int $width = 0;
    protected int $bytes = 0;
    protected string $title = '';
    protected string $descr = '';
    protected string $alt = '';
    protected string $creationDate = '';
    protected bool $hasValidExif = false;

    public function __construct(
        ?int $Id = 0,
        ?string $file = '',
        ?string $type = '',
        ?int $height = 0,
        ?int $width = 0,
        ?int $bytes = 0,
        ?string $title = '',
        ?string $descr = '',
        ?string $alt = '',
        ?string $creationDate = '',
        ?bool $hasValidExif = false
    ) {
        $this->Id = $Id;
        $this->file = $file;
        $this->type = $type;
        $this->height = $height;
        $this->width = $width;
        $this->bytes = $bytes;
        $this->$title = $title;
        $this->descr = $descr;
        $this->alt = $alt;
        $this->creationDate = $creationDate;
        $this->hasValidExif = $hasValidExif;
    }

    public function getId()
    {
        return $this->Id;
    }
    public function getFile()
    {
        return $this->file;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getHeight()
    {
        return $this->height;
    }
    public function getWidth()
    {
        return $this->width;
    }
    public function getBytes()
    {
        return $this->bytes;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescr()
    {
        return $this->descr;
    }
    public function getAlt()
    {
        return $this->alt;
    }
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    public function getHasValidExif()
    {
        return $this->hasValidExif;
    }

    public function setType($val)
    {
        $this->type = $val;
    }
    public function setFile($val)
    {
        $this->file = $val;
    }
    public function setTitle($val)
    {
        $this->title = $val;
    }
    public function setBytes($val)
    {
        $this->bytes = $val;
    }
    public function setDescr($val)
    {
        $this->descr = $val;
    }
    public function setAlt($val)
    {
        $this->alt = $val;
    }
    public function setCreationDate($val)
    {
        $this->creationDate = $val;
    }
    public function setHeight($val)
    {
        $this->height = $val;
    }
    public function setWidth($val)
    {
        $this->width = $val;
    }
    public function setValidExif($val)
    {
        $this->hasValidExif = $val;
    }
}
