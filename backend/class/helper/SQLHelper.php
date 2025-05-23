<?php

/**
 * Fully static class that allows interaction with the db for the simplest functions
 *
 * @author Timo Coupek | JAWS Developers
 * @author Luca Comolli | JAWS Developers
 * @version 09.11.2022
 */
class SQLHelper
{
    /**
     * This function contains the sql error data, but only contains it if the check function has been executed
     *
     * @var string
     */
    public static string $sql_error;

    /**
     * This function verifies the connection to the db. It returns true if the connection is stable false if there
     * is an error. The error is saved in the sql_err variable
     *
     * @return bool
     */
    public static function CheckStatus(): bool
    {
        try {
            // Creare una nuova istanza di mysqli senza tentare di connettersi
            $conn = new mysqli();
    
            // Impostare il timeout di connessione a 15 secondi
            $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
            
            // Tentare di connettersi con i parametri
            $conn->real_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
            // Controllare se ci sono errori di connessione
            if ($conn->connect_error) {
                self::$sql_error = $conn->connect_error . " (" . $conn->connect_errno . ")";
                return false;
            }
    
            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * This function returns the connection to the db
     *
     * @return mysqli
     */
    public static function GetConnection(): mysqli
    {
        return $conn = @(new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE));
    }
}