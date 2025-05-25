<?php

/**
 *
 *
 * @author Timo Coupek | JAWS Developers
 * @version 16.01.2024
 */
class ProcessManager
{

    static private string $process_id;
    static private int $affected_association_id;

    static private stdClass $log_data;

    static public function AddLogData(string $var_name, string $var_value): void
    {
        self::$log_data->$var_name = $var_value;
    }

    /**
     * @param int $affected_association
     */
    public static function SetAffectedAssociation(int $affected_association): void
    {
        self::$affected_association_id = $affected_association;
    }

    /**
     * @param int $affected_association
     */
    public static function GetAffectedAssociation(): int
    {
        if(!isset(self::$affected_association_id))
            throw new Exception("Affected association not set");
        
        return self::$affected_association_id;
    }

    public static function CreateProcessId(): void
    {
        // Genera un token unico di 50 caratteri
        $token = TokenManager::GenerateToken("process");

        // Verifica che il token non sia già presente nel database
        $checkToken = API::GetDBConnection()->prepare("SELECT * FROM `log` WHERE `action_id` = ?");
        $checkToken->bind_param("s", $token);
        $checkToken->execute();
        $result = $checkToken->get_result();

        // Se il token esiste già, genera un nuovo token
        while ($result->num_rows != 0) {
            $token = TokenManager::GenerateToken("process");
            $checkToken->bind_param("s", $token);
            $checkToken->execute();
            $result = $checkToken->get_result();
        }

        //Creation data var
        self::$log_data = new stdClass();

        self::$process_id = $token;
    }


    private static function EndProcessBase(string $error_code, bool $log = true, string $url = ""): void
    {
        self::$process_id = self::$process_id ?? TokenManager::GenerateToken("boot-err");
        $data = new stdClass();
        $data->request_id = self::$process_id;
        $data->error_code = $error_code;
        $data->speed = number_format((microtime(true) - API::GetSpeed()), 4);
        print_r(json_encode($data));
        $uid = API::GetUser()->GetId();

        if ($log) self::Log($error_code, $uid, json_encode(self::$log_data));

        if ($log) API::GetDBConnection()->close();

        if ($url !== "") {
            header("location: " . $url);
        }

        die;
    }

    /**
     * This function allows you to close the api by giving an exit code
     *
     * @param string $error_code
     * @param string|null $some_data
     * @param bool $log
     * @return void
     */
    public static function EndProcessWithCode(string $error_code,  bool $log = true): void
    {
        self::EndProcessBase($error_code,  $log);
    }

    /**
     * This function allows you to close the api by giving an exit code
     *
     * @param string|null $some_data
     */
    public static function EndProcessWithRedirect(
        string $error_code,
        string $url,
        string $some_data = null
    ): void {
        self::EndProcessBase($error_code, $some_data, url: $url);
    }

    /**
     * This function allows you to close the api by giving data as response
     *
     * @param stdClass|array|User|Association $data
     * @param string $error_code
     * @param string|null $additional_data
     * @param bool $log
     * @return void
     */
    public static function EndProcessWithData(
        mixed $data_to_print,
        string $error_code,
        bool $log = true
    ): void {
        self::$process_id = self::$process_id ?? TokenManager::GenerateToken("boot-err");
        $data = new stdClass();
        $data->request_id = self::$process_id;
        $data->error_code = $error_code;
        $data->speed = number_format((microtime(true) - API::GetSpeed()), 4);
        $data->data = $data_to_print;

        print_r(json_encode($data));
        if ($log)
            self::Log($error_code, API::GetUser()->GetId(), json_encode(self::$log_data ?? []));

        die;
    }

    /**
     * Log
     *
     * @param string $error_code
     * @param int|string $user_id
     * @param string|null $some_data
     * @return void
     */
    public static function Log(string $error_code, int|string $user_id, ?string $some_data = null): void
    {

        if ($user_id == null)
            $user_id = 0;

        $conn = API::GetDBConnection();

        $association = self::$affected_association_id ?? 0;
        $ip = User::GetIP();
        $p_id = self::$process_id ?? TokenManager::GenerateToken("boot-err");
        // Inserisce i dati con il token nella colonna id
        $process = $conn->prepare("INSERT INTO `log` (`action_id`, `user_id`, `action`, `other_data`, `ip`, `affected_association`) VALUES (?,?,?,?,?,?)");
        $process->bind_param("sdsssd", $p_id , $user_id, $error_code, $some_data, $ip, $association);

        $process->execute();
    }

    /**
     * Kill process printing login data
     *
     * @param array $user_data User data
     * @param string $token User token
     * @param string $redirect
     * @param string $password
     */
    public static function LoginSuccess(string $token): void
    {
        $json = new stdClass();
        $json->token = $token;

        ProcessManager::EndProcessWithData($json, "1.3.0");
    }


    /**
     * This function allows you to close the api by giving an exit code
     *
     * @param string $error_code
     * @param string $error_desc
     * @param string|null $some_data
     * @return void
     */
    /*#[\JetBrains\PhpStorm\NoReturn] public static function CloseConnection(string $error_code, string $error_desc, string $some_data = null): void
    {
        $data = new stdClass();
        $data->error_code = $error_code;
        $data->error_desc = $error_desc;
        $data->speed = number_format((microtime(true) - API::GetSpeed()), 4);
        print_r(json_encode($data));

        # Log
        self::Log($error_code, self::$json_data->user_id ?? "0", $some_data);
        fastcgi_finish_request();
    }*/
}
