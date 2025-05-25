<?php

class SessionManager
{
    // Chiave segreta per la cifratura
    private const DASHBOARD_KEY = SECRET_KEY;
    // Lunghezza totale del token desiderata
    private const TOKEN_LENGTH = 128;
    // Prefisso fisso per l'ID di sessione
    private const PREFIX = 'MTS-client--';
    // Genera un Session ID completamente casuale e lo firma
    public static function GenerateSessionIdForAuth(): string
    {
        // Genera una stringa casuale con la lunghezza rimanente
        $remainingLength = self::TOKEN_LENGTH - strlen(self::PREFIX) - 64; // 64 caratteri per la firma
        $randomString = self::generateRandomString($remainingLength);

        // Firma la stringa casuale con HMAC usando la DASHBOARD_KEY
        $signature = hash_hmac('sha256', self::PREFIX . $randomString, self::DASHBOARD_KEY);

        // Concatenazione del prefisso, stringa casuale e firma
        return self::PREFIX . $randomString . $signature;
    }


    // Funzione per validare il token generato
    public static function ValidateSessionId(string $sessionId): bool
    {
        // Controlla se il token ha il prefisso corretto
        if (substr($sessionId, 0, strlen(self::PREFIX)) !== self::PREFIX) {
            return false; // Prefisso non valido
        }

        // Estrai la stringa casuale e la firma
        $randomStringStart = strlen(self::PREFIX);
        $randomString = substr($sessionId, $randomStringStart, self::TOKEN_LENGTH - strlen(self::PREFIX) - 64);
        $signature = substr($sessionId, -64);

        // Ricomputa la firma
        $expectedSignature = hash_hmac('sha256', self::PREFIX . $randomString, self::DASHBOARD_KEY);

        // Verifica se la firma corrisponde
        return hash_equals($expectedSignature, $signature);
    }

    // Funzione per generare una stringa casuale alfanumerica
    private static function generateRandomString(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
