<?php

namespace app\models\enums;

use app\models\enums\Enum;

class AreaPub extends Enum
{
    const Course = 'c';
    const Team = 't';
    const Choaces = 'e';
    const Managers = 'm';
    const Generic = 'n';
    const dummy_array = array('Course' => 'c', 'Team' => 't',  'Generic' => 'n','Choaces' => 'e','Managers' => 'm');
}
