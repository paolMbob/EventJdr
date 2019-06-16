<?php

namespace App\Modele;
use App\Entity\Univers;

class UniversOption
{
    private $univers;

    public function getUnivers()
    {
        return $this->univers;
    }

    public function setUnivers($univers){
        $this->univers= $univers;
    }

}
