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
            ProcessManager::EndProcessWithCode("2.2.2");
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
                        ProcessManager::EndProcessWithCode("2.2.2.1.2");

                if (!User::ExistsById($this->path))
                    ProcessManager::EndProcessWithCode("sd");

                ProcessManager::AddLogData("user_id", $this->path);
                ProcessManager::EndProcessWithData($this->ById($this->path), "2.2.2.0.1", $this->path);
                break;
            case "me":
                //Return users data of the asker user
                ProcessManager::EndProcessWithData($this->ById(API::GetUser()->GetId()), "2.2.0.3");
                break;

            default:
                ProcessManager::AddLogData("path", $this->path);
                ProcessManager::EndProcessWithCode("2.2.2");
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
        `user`.*, 
        IF(COUNT(`user_association`.association_id) = 0, 
           JSON_ARRAY(), 
           JSON_ARRAYAGG(
                   `user_association`.association_id
           )
        ) as association
        FROM `user`
        LEFT JOIN `user_association` 
            ON `user_association`.`user_id` = `user`.`id` 
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
        `user`.*, 
        IF(COUNT(`user_association`.association_id) = 0, 
           JSON_ARRAY(), 
           JSON_ARRAYAGG(
                   `user_association`.association_id
           )
        ) as association
        FROM `user`
        LEFT JOIN `user_association` 
        ON `user_association`.`user_id` = `user`.`id` 
        WHERE `id` = ?
        GROUP BY `user`.`id`");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();

        $user = User::GetByResult($result);
        $user->LoadPermissions();

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
        `user`.*, 
        IF(COUNT(`user_association`.association_id) = 0, 
           JSON_ARRAY(), 
           JSON_ARRAYAGG(
                   `user_association`.association_id
           )
        ) as association
        FROM `user`
        LEFT JOIN `user_association` 
        ON `user_association`.`user_id` = `user`.`id` 
        WHERE `user`.`email` = ?
        GROUP BY `user`.`id`");
        $p->bind_param("s", $email);
        $p->execute();
        $result = $p->get_result();

        $user = User::GetByResult($result);
        $user->LoadPermissions();

        return $user;
    }

    /**
     * This function returns all users that is part of the passed customer
     *
     * @param int $id
     * @return stdClass
     */
    private function GetUsersOfCustomer(int $id): stdClass
    {
        $p = $this->conn->prepare("SELECT `user`.`id`,`user`.`name`, `user`.`surname`, `user`.`email`, `user`.`status`, `user`.`role`, `user`.`profile_picture`, COALESCE(JSON_ARRAYAGG(`association`.`name`), '[]') as customers_names, COALESCE(JSON_ARRAYAGG(`role`.`customer_id`), '[]') as association FROM `user` LEFT JOIN `role` ON `user`.`id` = `role`.`user_id` LEFT JOIN `association` ON `role`.`customer_id` = `association`.`id` WHERE `association`.`id` = ? GROUP BY `user`.`id`;");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();

        return (object) User::GetAllByResult($result);
    }
}
