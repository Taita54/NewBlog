<?php


namespace App\Models\Enums;

/**
 * Description of PubTypes
 * elenca le tipologie di documenti
 * creabili con l'editor
 * @author giovi
 */

use app\models\enums\Enum;


class PubTypes extends Enum
{
    const Comunicato = 1;
    const Articolo = 2;
    const Avviso = 3;
    const Album = 4;
    const Video = 5;
    const Modulo = 6;
    const Link = 7;
    const Guida = 8;
    const Report=9;
    const dummy_array = array('Comunicato' => 1, 'Articolo' => 2, 'Avviso' => 3, 'Album' => 4, 'Video' => 5, 'Modulo' => 6, 'Link' => 7, 'Guida' => 8,'Report' => 9);
}
