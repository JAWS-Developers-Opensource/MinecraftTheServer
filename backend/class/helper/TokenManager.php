<?php

/**
 * Class to manage user token
 *
 * @author Timo Coupek | JAWS Developers
 *
 * @version 22.11.2022
 */
class TokenManager
{
    /**
     * 
     */
    public static function GetSessionId(): string
    {
        return $_SERVER['HTTP_X_SESSION_ID'] ?? "";
    }
    #endregion

    /**
     * Function to create a cryptographically secure token (random string of characters)
     *
     * @param int $length Length of the token
     * @return string Token
     */
    public static function GenerateToken(string $prefix, int $length = 128, array $salts = []): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = 'MTS-';  // Prefisso fisso
        $baseLength = strlen($randomString); // Lunghezza del prefisso "SportifyApp-"

        // Spazio rimanente dopo il prefisso per rispettare la lunghezza totale
        $remainingLength = $length - $baseLength;

        // Generazione della prima parte del token (5 caratteri completamente casuali)
        $randomString .= $prefix;


        $randomString .= "--";
        $baseLength += 2 + strlen($prefix); // Aggiungi i 5 caratteri e il "--" al calcolo della lunghezza

        // Ricalcola lo spazio rimanente per i sali
        $remainingLength = $length - $baseLength;

        // Concatenazione dei sali hashati
        if (!empty($salts)) {
            $saltString = '';

            foreach ($salts as $salt) {
                $saltString .= hash('sha256', $salt); // Hash dei sali
            }

            // Troncamento del salt hash se eccede lo spazio rimanente
            if (strlen($saltString) > $remainingLength) {
                $saltString = substr($saltString, 0, $remainingLength);
            }

            $randomString .= $saltString; // Aggiungi i sali al token
        } else {
            for ($i = $baseLength; $i < $length; $i++) {
                // Use random_int for a cryptographically secure random number
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
        }

        // Se lo spazio per i sali è sufficiente, vengono aggiunti interamente, altrimenti tronchiamo la stringa risultante.    
        return $randomString;
    }

    /**
     * @param string $type
     * @param string $token
     * @param string $session_id
     * @return string|null Ritorna il token valido o null se il token non è valido e deve essere generato un nuovo token
     */
    public static function CheckTokenValidityByType(string $type, string $token, string $session_id, int $user_id): ?string
    {
        // Prepara la query per verificare il token esistente
        $p = API::GetDBConnection()->prepare("SELECT `token`, `session_id`, `expire`
        FROM `session`
        WHERE `token` = ?
        AND `type` = ?");
        $p->bind_param("ss", $token, $type);
        $p->execute();
        $result = $p->get_result();

        if ($row = $result->fetch_assoc()) {
            $expireDate = new DateTime($row['expire']);
            $currentDate = new DateTime();

            // Controlla se il token è scaduto
            if ($currentDate > $expireDate) {
                // Elimina il token scaduto
                $dp = API::GetDBConnection()->prepare("DELETE FROM `session` WHERE `token` = ?");
                $dp->bind_param("s", $token);
                $dp->execute();
                return null; // Token scaduto
            }

            // Controlla se la sessione corrisponde
            if ($row['session_id'] === $session_id) {
                return $row['token']; // Ritorna il token valido
            }
        }

        // Genera un nuovo token se il token non esiste o se la sessione è diversa
        $newToken = self::CreateToken($user_id, $session_id, $type);
        return $newToken;
    }

    /**
     * @param string $token
     * @param mysqli $conn
     * @return bool return true if is valid
     */
    public static function CheckTokenValidity(string $token, string $session_id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT *
            FROM `session`
            WHERE `token` = ?
            AND `session_id` = ?
            AND `expire` >= NOW()");
        $p->bind_param("ss", $token, $session_id);
        $p->execute();
        $result = $p->get_result();

        if (!mysqli_num_rows($result)) {
            $dp = API::GetDBConnection()->prepare("DELETE FROM `session` WHERE `token` = ?");
            $dp->bind_param("s", $token);
            $dp->execute();
            return false;
        }

        return true;
    }


    /**
     * Check if there is any valid token for the user.
     */
    public static function GetTokenByUId(int $user_id, mysqli $conn, string $type): string
    {
        $p = $conn->prepare("SELECT * FROM `session` WHERE `user_id` = ? AND `type` = ?");

        $p->bind_param("ss", $user_id, $type);
        $p->execute();
        $result = $p->get_result();
        if (mysqli_num_rows($result)) {
            while ($row = mysqli_fetch_array($result))
                return $row['token'];
            return "";
        } else {
            return "";
        }
    }

    public static function CreateToken(int $user_id, string $session_id, string $type): string
    {
        $expire_time = $type == "mobile" ? "INTERVAL 14 DAY" : "INTERVAL 10 MINUTE";
        $p = API::GetDBConnection()->prepare("INSERT INTO `session` (`token`, `session_id`, `user_id`, `expire`, `type`, `ip`) VALUES (?,?,?,
        (SELECT DATE_ADD(NOW(), $expire_time)), ?, ?)");
        $token = self::GenerateToken("auth");
        $ip = User::GetIP();
        $p->bind_param("sssss", $token, $session_id, $user_id, $type, $ip);

        $p->execute();

        return $token;
    }

    public static function RevokeToken(string $token): void
    {
        $dp = API::GetDBConnection()->prepare("DELETE FROM `session` WHERE `token` = ?");
        $dp->bind_param("s", $token);
        $dp->execute();
    }


    /**
     * Check if invitation key exists
     */
    public static function CheckifRegistrationKeyExists(string $token): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `registration_key` WHERE `token` = ? AND DATE_ADD(`issued_date`, INTERVAL 7 DAY) >= NOW()");
        $p->bind_param("s", $token);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    public static function UpdateTokenValidity(string $token): void
    {
        //TO BE CHANGED CREATE ANOTHER TOKEN
        $p = API::GetDBConnection()->prepare("UPDATE `session`
        SET `expire` = CASE
            WHEN `type` = 'web' THEN DATE_ADD(NOW(), INTERVAL 10 MINUTE)
            ELSE DATE_ADD(NOW(), INTERVAL 14 DAY)
        END
        WHERE `token` = ?;");
        $p->bind_param("s", $token);
        $p->execute();

        API::GetDBConnection()->query("DELETE FROM `session` WHERE `expire` <= NOW()");
    }

    /**
     * Check if token is valid with the passed email
     *
     * @param string $email
     * @param int $association_id
     * @return boolean true if valid
     */
    public static function InvitationKeyExistByAssocaition(int $id, int $association_id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `invitation_key` WHERE `user_id` = ? AND `association_id` = ?");
        $p->bind_param("ss", $id, $association_id);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result)) {
            return true;
        }

        return false;
    }

    #region INIVTATION & REGISTRATION

    /**
     * Check if token is valid with the passed email
     *
     * @param string $email
     * @param int $customer_id
     * @return boolean true if valid
     */
    public static function RegistrationKeyExistByAssocaition(string $email, int $association_id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `registration_key` WHERE `email` = ? AND `association_id` = ?");
        $p->bind_param("sd", $email, $association_id);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result)) {
            return true;
        }

        return false;
    }

    /**
     * Check if invitation key exists
     */
    public static function InvitationKeyExist(string $token): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `invitation_key` WHERE `token` = ?");
        $p->bind_param("s", $token);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    /**
     * Check if invitation key exists
     */
    public static function RegistrationKeyExist(string $token): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `registration_key` WHERE `token` = ?");
        $p->bind_param("s", $token);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    #endregion


}
