<?php
# Include

require_once 'email/EmailGenerator.php';

require_once 'class/autoload.php';
require_once "routes/autoload.php";

/**
 * This class is used to control the entire mealpass api system
 *
 * @author Timo Coupek | JAWS Developers
 * @version 10.01.2024
 * @link https://api.mealpass.ch
 */
class API
{
    #region Data


    # Existing variables in any case
    /**
     * This variable defines have been entered json data
     *
     * @var stdClass
     */
    protected static stdClass $json_data;

    /**
     * @return stdClass
     */
    public static function GetJsonData(): stdClass
    {
        return self::$json_data;
    }

    /**
     * Db connection
     *
     * @var mysqli
     */
    protected static mysqli $db_conn;


    /**
     * Get the DB connection
     *
     * @return mysqli
     */
    public static function GetDBConnection(): mysqli
    {
        return self::$db_conn ?? new mysqli();
    }


    /**
     * User data var
     *
     * @var User
     */
    protected static User $user;

    /**
     * Get all user data
     *
     * @return User|null
     */
    public static function GetUser(): null|User
    {
        return self::$user ?? new User(null);
    }

    /**
     * @var string
     */
    protected static string $speed;

    /**
     * Get all user data
     *
     * @return string
     */
    public static function GetSpeed(): string
    {
        return self::$speed;
    }

    /**
     * API action path
     *
     * @var array
     */
    protected array $path;

    /**
     * @var string
     */
    protected string $last_update = "28.11.2024";


    #endregion

    #region Boot Functions

    /**
     * This function initializes and saves all the data in the api
     *
     * @return void
     */
    public function Boot(): void
    {
        self::$speed = microtime(true);

        $this->CheckDatabaseStatus();
        //$this->CheckMailServerStatus();
        $this->SaveJsonData();
        $this->SaveAPIPath();
        //$this->CheckOrigin();
        ProcessManager::CreateProcessId();
    }

    /**
     * This function checks whether the connection to the db is existing or not
     *
     * @return void
     */
    private function CheckDatabaseStatus(): void
    {
        if (!SQLHelper::CheckStatus())
            ProcessManager::EndProcessWithCode("0.1.1", log: false);
        self::$db_conn = SQLHelper::GetConnection();
    }

    /**
     * This function checks whether the connection to the mail server is existing or not
     *
     * @return void
     */
    private function CheckMailServerStatus(): void
    {
        if (!MailBase::CheckSMPTServerStatus())
            ProcessManager::EndProcessWithCode("0.1.2", log: false);
    }

    /**
     * This function takes the data that the user has entered and saves it.
     * In case there is no data it saves nothing and the variable remains null
     *
     * @return void
     */
    private function SaveJsonData(): void
    {
        $json_data = file_get_contents('php://input');
        $json_data = json_decode(stripslashes($json_data));

        if (!is_object($json_data) && !is_array($json_data)) {
            $json_data = new stdClass();
        }
        self::$json_data =  $json_data;
    }


    /**
     * This functions save the api path
     *
     * @return void
     */
    private function SaveAPIPath(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = filter_var($uri, FILTER_SANITIZE_URL);
        $uri = explode('/', $uri);
        array_shift($uri); //Remove domain
        $this->path = $uri;
    }

    private function CheckOrigin(): void
    {
        Client::ValidateRequestOrigin();
    }

    #endregion

    #region Init
    /**
     * API initialization
     *
     * @return void
     * @throws Exception
     */
    public function InitPath(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "OPTIONS")
            ProcessManager::EndProcessWithCode("OPTION_REQUEST", log: false);
        //NO AUTHENTICATION
        switch ($this->path[0]) {
            case "":
                $this->PrintAPIDebug();
                break;
            case "get-session-id-for-auth":
                ProcessManager::EndProcessWithData(SessionManager::GenerateSessionIdForAuth(), "session-id");
                break;
            case "info":
                header('Content-Type: none');
                ProcessManager::EndProcessWithData(phpinfo(), "phpInfo");
                break;
            case "auth":
                if (!SessionManager::ValidateSessionId(Client::GetSessionId())) {
                    ProcessManager::AddLogData("x-session-id", Client::GetSessionId());
                    ProcessManager::EndProcessWithCode("1.1.3");
                }

                if (!isset($this->path[1]))
                    ProcessManager::EndProcessWithCode("1.1.1");

                (new Auth($this->path))->Init();
                break;

            case "service":
                if (!isset($this->path[1]))
                    ProcessManager::EndProcessWithCode("1.4.1.1");

                (new Service($this->path, false))->Init();
                break;
            default:
                self::$user = Auth::CheckUserCredential(true);
        }

        //WITH AUTHENTICATION
        switch ($this->path[0]) {
            case "user":
                array_shift($this->path);
                if (!isset($this->path[0]))
                    ProcessManager::EndProcessWithCode("2.1.1");
                (new Users($this->path))->Init();
                break;

            case "association":
                array_shift($this->path);
                if (!isset($this->path[0]))
                    ProcessManager::EndProcessWithCode("3.1.1");
                (new Associations($this->path))->Init();
                break;

            case "service":
                self::$user = Auth::CheckUserCredential(true);
                (new Service($this->path, true))->Init();
                break;

            case "event":
                array_shift($this->path);
                if (!isset($this->path[0]))
                    ProcessManager::EndProcessWithCode("5.1.1");
                (new Event($this->path))->Init();
                break;

            case "video":
                array_shift($this->path);
                if (!isset($this->path[0]))
                    ProcessManager::EndProcessWithCode("6.1.1");
                (new Video($this->path))->Init();
                break;

            case "attendance":
                array_shift($this->path);
                if (!isset($this->path[0]))
                    ProcessManager::EndProcessWithCode("5.1.1");
                (new Event($this->path))->Init();
                break;

            default:
                ProcessManager::EndProcessWithData("Bro, what are u looking for?", "?.?.?");
        }
    }

    /**
     * This function is called only when the query does not have a defined path.
     * In this case it prints ale general information.
     *
     * @param string $status
     * @return void
     */
    private function PrintAPIDebug(string $status = "up"): void
    {
        $data = new stdClass();
        $data->name = "Sportify API";
        $data->authors = array("Timo Coupek | JAWS Developers");
        $data->status = $status;
        $data->sql_conn = self::$db_conn->stat();
        $data->last_update = $this->last_update;
        $data->speed = number_format((microtime(true) - self::$speed), 4);
        print_r(json_encode($data));
        die();
    }
    #endregion

}
