<?php

namespace app\db;

/*
 * questa classe è di tipo SINGLETON essa mantiene attiva una unica istanza
 * di accesso al Database durante utta la esecuzione della sessione
 * tale tipo di classe ha un metodo pubblico che si chiama getinstance
 * che normalmente riceve un array di opzioni ( in questo caso per il collegamento
 */

class DbPdo {

    protected $conn;
    protected static $instance; //viene definita static perchè in tal modo

    //esisterà per qualunque tipo di oggetto

    public static function getInstance(array $options) {
        if (!static::$instance) {
            static::$instance = new static($options);
        }
        return static::$instance;
    }

    // il costruttore è protetto per fare in modo da non instanziare la classe
    protected function __construct(array $options) {
        $this->conn = new \PDO($options['dsn'], $options['user'], $options['password']);
        if (array_key_exists('options', $options)) {
            foreach ($options['options'] as $opt) {
                $this->conn->setAttribute(key($opt), current($opt));
            }
        }
    }

    public function getConn() {
        return $this->conn;
    }

}
