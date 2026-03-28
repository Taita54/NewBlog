<?php

namespace app\db;
use app\db\DbPdo;
/**
 * Description of DBFactory
 *
 * @author Giovanni
 */

class DbFactory {

    public static function create(array $options) {

        if(!array_key_exists('charset', $options)){
            $options['charset'] ='utf8';
        } 
        if (!array_key_exists('dsn', $options)) {
            if (!array_key_exists('driver', $options)) {
                throw new \InvalidArgumentException('nessun driver predefinito');
            }
            $dsn = '';
            switch ($options['driver']) {
                case 'mysql':
                case 'oracle':
                case 'mssql':
                    $dsn = $options['driver'] . ':host=' . $options['host'] .
                            ';dbname=' . $options['database'] . ';charset=' . $options['charset'];
                    break;
                case 'sqlite':
                    $dsn = 'sqllite:' . $options['database'];
                    break;
                default :
                    throw new \InvalidArgumentException('driver non impostato o sconosciuto');
            }
        $options['dsn'] = $dsn;
        }
        // a questo punto richiamiamo la classe singleton in DBPDO
        // PASSANDOGLI IL NUOVO DSN mentre user e password sono già lì
        return  DbPdo::getInstance($options);
    }

}