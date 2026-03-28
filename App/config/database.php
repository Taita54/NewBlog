<?php

$secrets = require 'secrets.php';

try {
     $pdo = new PDO('mysql:host=localhost;dbname='.$secrets['DB_NAME'].';charset=utf8', $secrets['DB_USER'], $secrets['DB_PASSWORD'],);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     // echo "Connessione al database riuscita!"; // Messaggio di successo
    } catch (PDOException $e) {
        error_log("Errore di connessione al database: " . $e->getMessage());
        error_log("Codice di errore: " . $e->getCode());
        error_log("File: " . __FILE__);
        error_log("Line: " . __LINE__);

        die("Errore di connessione al database. Controlla il log di errori per dettagli.");
    }