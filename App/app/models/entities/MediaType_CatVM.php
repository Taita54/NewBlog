<?php

namespace app\models\entities;

class MediaType_CatVM extends MediaVM
{
    protected string $Type = '';
    protected string $Cat = '';


    public function getType()
    {
        return $this->Type;
    }
    public function setType($val)
    {
        $this->Type = $val;
    }
    public function getCat()
    {
        return $this->Cat;
    }

    public function setCat($val)
    {
        if ($val === null) {
            $val = '';
        }
        $this->Cat = $val;
    }
}
