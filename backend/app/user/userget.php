<?php

/**
 * This class allows you to recognise all users and their information
 * @author Timo Coupek | JAWS Developers
 * @version 21.03.2023
 */
class UserGet
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $path;
    #endregion
    #region Boot
    public function __construct(string $path)
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();

        if ($path == "") {
            ProcessManager::EndProcessWithCode("2.2.5");
        }

        $this->path = $path;
    }
    #endregion
    #region Init
    public function Init(): void
    {
        switch ($this->path) {
                //Return all users of sportify
            case "*":
                //Check if user has super admin permission
                if (!API::GetUser()->IsAdmin())
                    ProcessManager::EndProcessWithCode("2.2.1.1");

                //Get all users
                ProcessManager::EndProcessWithData($this->GetAllUsers(), "2.2.0.1");
                break;
                //Return a single user specified by the passed id
            case is_numeric($this->path):
                if ($this->path != API::GetUser()->GetId())
                    if (!API::GetUser()->IsAdmin())
                        ProcessManager::EndProcessWithCode("2.2.1.2");

                if (!User::ExistsById($this->path))
                    ProcessManager::EndProcessWithCode("2.2.2.1");

                ProcessManager::AddLogData("user_id", $this->path);
                ProcessManager::EndProcessWithData($this->ById($this->path), "2.2.0.2", $this->path);
                break;
            case "me":
                //Return users data of the asker user
                ProcessManager::EndProcessWithData($this->ById(API::GetUser()->GetId()), "2.2.0.3");
                break;

            default:
                ProcessManager::AddLogData("path", $this->path);
                ProcessManager::EndProcessWithCode("2.2.5");
        }
    }
    #endregion
    #region Private
    /**
     * This function returns all users of the system (sportify)
     *
     * @return stdClass
     */
    private function GetAllUsers(): stdClass
    {
        $result = $this->conn->query("SELECT 
        *
        FROM `user`
        GROUP BY `user`.`id`;
        ");
        return (object) User::GetAllByResult($result);
    }

    /**
     * This function returns a single user that have the id passed as parameter
     *
     * @param int $id
     * @return User
     */
    public static function ById(int $id): User
    {
        $p = API::GetDBConnection()->prepare("SELECT 
        `user`.*
        FROM `user`
        WHERE `id` = ?
        GROUP BY `user`.`id`");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();

        $user = User::GetByResult($result);

        return $user;
    }

    /**
     * This function returns a single user that have the id passed as parameter
     *
     * @param int $id
     * @return User
     */
    public static function ByEmail(string $email): User
    {
        $p = API::GetDBConnection()->prepare("SELECT 
        `user`
        FROM `user`
        WHERE `user`.`email` = ?
        GROUP BY `user`.`id`");
        $p->bind_param("s", $email);
        $p->execute();
        $result = $p->get_result();

        $user = User::GetByResult($result);

        return $user;
    }
}
