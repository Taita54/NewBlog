<?php
// ... other functions ...
// function s_open()
// {

//     $_SESSION['PHPSESSID'] = session_id();
//     // Aggiungi un log di debug per l'apertura della sessione
//     error_log("- Apertura sessione {$_SESSION['PHPSESSID']}");
// }

// function s_close()
// {
//     // Aggiungi un log di debug per la chiusura della sessione
//     error_log("- Chiusura sessione");
// }

// function s_read($PHPSESSID)
// {
//     // Aggiungi un log di debug per la lettura dei dati di sessione
//     error_log("- Lettura dati di sessione (session_id=$PHPSESSID)");
//     // echo "- Lettura dati di sessione (session_id=$PHPSESSID)<br>";
// }

// function s_write($PHPSESSID, $data)
// {

//     session_save_path(__DIR__ . DS . '..' . DS . 'resources' . DS . 'LOGSadvices' . DS);
//     session_id($PHPSESSID);
//     print($data);
//     // Aggiungi un log di debug per la scrittura dei dati di sessione
//     error_log("- Scrittura dei dati di sessione:\nsession_id=$PHPSESSID\ndati: $data");
// }

// function s_destroy($PHPSESSID)
// {
//     // Aggiungi un log di debug per la distruzione dei dati di sessione
//     error_log("- Distruzione dei dati di sessione");
// }

// function s_gc($life)
// {
//     // Aggiungi un log di debug per la pulizia delle sessioni scadute
//     error_log("- Pulizia delle sessioni scadute (max:$life secondi)");
// }

// ob_start();

// adesso posso mostrare l output dello script
// ob_end_flush();


function s_open($save_path)
{
    // This function is called when a session is opened.  We can use it to create the log file.
    $session_id = session_id();
    $_SESSION['PHPSESSID'] = session_id();
    $logFile = $save_path . $session_id . '.txt';
    if (!file_exists($logFile)) {
        // Create the log file if it doesn't exist.  You might want to add error handling here.
        file_put_contents($logFile, "Session " . $session_id . " started.\n");
    }
    return true; // Indicate success
}

function s_read($session_id)
{
    //This function is called when a session is read. We'll log this action.
    $save_path = session_save_path();
    $logFile = $save_path . $session_id . '.txt';
    $logEntry = date("Y-m-d H:i:s") . ": Session read.\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    return file_get_contents($save_path . $session_id); //Return the session data.
}


function s_write($session_id, $session_data)
{
    // This function is called when session data is written.
    $save_path = session_save_path();
    $logFile = $save_path . $session_id . '.txt';
    $logEntry = date("Y-m-d H:i:s") . ": Session data written.\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    return file_put_contents($save_path . $session_id, $session_data);
}

function s_close()
{
    //This function is called when a session is closed.
    return true;
}

function s_destroy($session_id)
{
    // This function is called when a session is destroyed.
    $save_path = session_save_path();
    $logFile = $save_path . $session_id . '.txt';
    $logEntry = date("Y-m-d H:i:s") . ": Session destroyed.\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    return unlink($save_path . $session_id); // Delete the session file
}

function s_gc($maxlifetime)
{
    // This function is called by garbage collection.
    //It's already handled by PHP's built-in garbage collection if you use session_save_path()
    error_log("- Pulizia delle sessioni scadute (max:$maxlifetime secondi)");
    return true;
}

ob_start();

// adesso posso mostrare l output dello script
ob_end_flush();

