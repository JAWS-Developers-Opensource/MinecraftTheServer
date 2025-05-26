<?php
include "app/user/userget.php";
include "app/user/useredit.php";
include "app/user/userremove.php";
include "app/user/useradd.php";
include "app/user/userinvitation.php";
/**
 * Manage user
 * 
 * @author Timo Coupek | JAWS Developers
 * @version 09.10.2024
 */
class Users
{
    #region Prop
    /**
     * desc Path that defines the query
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

    #region Static

    /**
     * This function checks if the user exists
     *
     * @param int $id
     * @return bool true if exists
     */
    public static function CheckIfUserExistsById(int $id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `user` WHERE `id` = ?");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();

        if (mysqli_num_rows($result))
            return true;

        return false;
    }
    #endregion

    #region Public
    public function Init(): void
    {
        switch ($this->path[0]) {
            case "get":
                /**
                 * /users/get/*
                 * /users/get/[id]
                 * /users/get/me
                 * /users/get/my
                 */
                (new UserGet($this->path[1] ?? 0))->Init();
                break;
            case "add":
                /**
                 * /users/add
                 */
                (new UserAdd())->Init();
                break;
            case "edit":
                /**
                 * /users/edit/1/name
                 * /users/edit/1/surname
                 * /users/edit/1/email
                 * /users/edit/1/password
                 */
                (new UserEdit($this->path[1] ?? "", $this->path[2] ?? ""))->Init();
                break;
            case "remove":
                /**
                 * /users/delete/[1]
                 */
                (new UserRemove($this->path[1] ?? ""))->Init();
                break;
            case "invitation":
                /**
                 * /users/delete/[1]
                 */
                (new UserInvitation($this->path[1] ?? ""))->Init();
                break;
            default:
                ProcessManager::AddLogData("path", $this->path[0]);
                ProcessManager::EndProcessWithCode("2.1.5");
        }
    }
    #rendregion

}
