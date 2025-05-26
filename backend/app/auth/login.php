<?php

/**
 * This class allows the authentication to be performed and the authentication token to be received.
 *
 * @author Timo Coupek | JAWS Developers
 * @version 09.10.2024
 */
class Login
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected User $user_data;
    protected string $token;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
    }
    #endregion
    #region Init
    #endregion
    /**
     * @return void
     */
    function Init(): void
    {
        if (($this->data->username ?? "") == "")
            ProcessManager::EndProcessWithCode("1.3.2.1");

        if (($this->data->password ?? "") == "")
            ProcessManager::EndProcessWithCode("1.3.2.2");

        $session_id = Client::GetSessionId();

        if ($session_id == "")
            ProcessManager::EndProcessWithCode("1.3.3");

        //if (($this->data->login_type ?? "") == "")
        //ProcessManager::EndProcessWithCode("1.3.4");

        if (!$this->CheckCredential($this->data->username, $this->data->password, $session_id, "web")) {
            ProcessManager::AddLogData("username", $this->data->username);
            ProcessManager::AddLogData("x-session-id", $session_id);
            ProcessManager::EndProcessWithCode("1.3.2.3");
        }

        if (!$this->user_data->GetStatus())
            ProcessManager::EndProcessWithCode("1.3.2.4");

        ProcessManager::AddLogData("user_id", $this->user_data->GetId());
        ProcessManager::AddLogData("token", $this->token);
        ProcessManager::AddLogData("session_id", $session_id);

        ProcessManager::LoginSuccess($this->token);
    }

    #endregion
    #region Private
    /**
     * Method that verifies the user's credentials and enters the date and time the user logged in the log
     *
     * @param string $email User email
     * @param string $password User password
     * @return true if exist
     */
    public function CheckCredential(string $email, string $password, string $session_id, string $login_type): bool
    {
        //Lowercase and crypt
        $email = strtolower($email);

        $password = $password . PASSWORD_PAPER;

        // Recupera l’hash salvato del database basato sull’email dell’utente
        $procedure = $this->conn->prepare("SELECT `user`.*
            FROM `user`
            WHERE `user`.`username` = ?");
        $procedure->bind_param("s", $email);
        $procedure->execute();
        $result = $procedure->get_result();
        if ($row = $result->fetch_assoc()) {
            $storedPasswordHash = $row['password'];
            $passwordHashWithoutPrefix = str_replace("MTS-pass--", "", $storedPasswordHash);
            // Verifica la password usando l'hash senza prefisso
            if (password_verify($password, $passwordHashWithoutPrefix)) {
                $user_data = new User(null);

                $user_data->SetId($row['id']);
                $user_data->SetUsername($row['username']);
                $user_data->SetName($row['name']);
                $user_data->SetSurname($row['surname']);
                $user_data->SetStatus($row['status']);
                $user_data->SetRole($row['role']);
                $this->user_data = $user_data;

                $this->GenerateToken(($login_type == "mobile" ? "mobile" : "web"), $session_id);
                return true;
            }
        }
        return false;
    }

    /**
     * This class allows you to generate the token or return the active one if it has not yet expired
     *
     */
    public function GenerateToken(string $type, string $session_id): void
    {
        // Controlla se l'utente ha già un token valido
        $token = TokenManager::GetTokenByUId($this->user_data->GetId(), $this->conn, $type);

        // Verifica la validità del token e ottieni il token valido
        while (!$validToken = TokenManager::CheckTokenValidityByType($type, $token, $session_id, $this->user_data->GetId())) {
            // Se il token non è valido, creane uno nuovo
            $token = TokenManager::CreateToken($this->user_data->GetId(), $session_id, $type);
        }

        // Imposta il token attivo
        $this->token = $validToken;
    }

    /**
     * This function takes the query passed by parameter and saves all the necessary values by returning them
     * @param mysqli_result $result
     * @return stdClass
     */
    public static function SaveUserData(mysqli_result $result): User
    {
        $user_data = new User(null);
        while ($row = mysqli_fetch_array($result)) {
            $user_data->SetId($row['id']);
            $user_data->SetUsername($row['username']);
            $user_data->SetName($row['name']);
            $user_data->SetSurname($row['surname']);
            $user_data->SetStatus($row['status']);
            $user_data->SetRole($row['role']);
        }
        return $user_data;
    }
    #endregion
}
