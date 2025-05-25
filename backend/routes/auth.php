<?php

include "app/auth/login.php";
include "app/auth/register.php";
include "app/auth/logout.php";

/**
 * desc
 * This class handles the authentication.
 *
 * @author Timo Coupek | JAWS Developers
 * @version data 09.10.2024
 */
class Auth
{
    #region Prop
    /**
     * desc Path that defines the login or the register
     *
     * @var array
     */
    protected array $path;
    #endregion

    #region Boot
    public function __construct(array $path)
    {
        $this->path = $path;
    }

    #endregion

    /**
     * Undocumented function
     *
     * @param boolean $print_error
     * @return User | void DROPS CONNECTION IF NOT DISABLED
     */
    public static function CheckUserCredential(bool $print_error = false): User
    {
        $token = Client::GetBearerToken();
        $session_id = Client::GetSessionId();
        $db_conn = API::GetDBConnection();

        //Check if token exist
        if (($token == "") && $print_error)
            ProcessManager::EndProcessWithCode("1.4.1", "", false);

        //Check if API credential are valid
        if (!TokenManager::CheckTokenValidity($token, $session_id)) {
            ProcessManager::AddLogData("x-session-id", $session_id);
            ProcessManager::AddLogData("token", $token);
            ProcessManager::EndProcessWithCode("1.4.2");
        }

        //Get user id
        $result = $db_conn->query("SELECT `user_id` FROM `session` WHERE `token` = '" . $token . "'");

        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
        }

        //Save user dat
        $user = UserGet::ById($user_id);

        //Check account status
        if (!$user->GetStatus())
            ProcessManager::EndProcessWithCode("1.4.3");

        TokenManager::UpdateTokenValidity($token);
        return $user;
    }

    #region Public
    public function Init(): void
    {
        switch ($this->path[1] ?? "") {
            case "register":
                (new Register($this->path[2] ?? ""))->Init();
                break;

            case "login":
                // /auth/login
                (new Login())->Init();
                break;
            case "logout":
                // /auth/logout
                (new Logout())->Init();
                break;
            default:
                ProcessManager::EndProcessWithCode("1.1.1");
        }
    }
    #rendregion

    #endregion

}
