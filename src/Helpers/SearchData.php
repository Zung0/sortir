<?php

namespace App\Helpers;

use App\Entity\Site;
use phpDocumentor\Reflection\Types\Boolean;

class SearchData
{
    public $q ='';
    public $site;

    public $dateMin;
    public $dateMax;

   public $isOrganisateur =false;
    public $isInscrit = false;
    public $isNotInscrit = false;
    public $sortiesPassees;

}