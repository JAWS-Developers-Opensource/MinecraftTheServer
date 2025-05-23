<?php
class Client
{
    // Array di domini consentiti
    private const ALLOWED_DOMAINS = ['dashboard.sportifyapp.co', 'dashboard-dev.sportifyapp.co'];

    // Funzione per validare che la richiesta provenga da uno dei domini consentiti
    public static function ValidateRequestOrigin(): bool
    {
        // Controlla l'header 'Origin' o 'Referer'
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
        $referer = $_SERVER['HTTP_REFERER'] ?? null;
        // Se è presente l'header 'Origin', confrontalo con i domini consentiti
        if ($origin) {
            $originHost = parse_url($origin, PHP_URL_HOST);
            if (in_array($originHost, self::ALLOWED_DOMAINS)) {
                return true;
            }
        }

        // Se non c'è 'Origin', controlla il 'Referer'
        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            if (in_array($refererHost, self::ALLOWED_DOMAINS)) {
                return true;
            }
        }

        return false; // Origine o referer non valido
    }

        /**
     * Function that extract autorization token from header
     *
     * @return string|null
     */
    private static function GetAuthorizationHeader(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    #region AUTH
    /**
     * Function that return the token
     *
     * @return string|null
     */
    public static function GetBearerToken(): ?string
    {
        $headers = Client::GetAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

        /**
     * 
     */
    public static function GetSessionId(): string
    {
        return $_SERVER['HTTP_X_SESSION_ID'] ?? "";
    }

    public static function GetAssociationId(): string
    {
        return $_SERVER['HTTP_X_ASSOCIATION_ID'] ?? "";
    }

    public static function GetEventId(): string
    {
        return $_SERVER['HTTP_X_EVENT_ID'] ?? "";
    }
}