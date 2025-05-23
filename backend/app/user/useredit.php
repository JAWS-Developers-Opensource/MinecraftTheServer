<?php

/**
 * This class allows you to edit the information of a user
 * @author Timo Coupek | JAWS Developers
 * @version 21.03.2023
 */
class UserEdit
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected int $user_id;
    protected string $path;
    #endregion
    #region Boot
    public function __construct(string $user_id, string $path)
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();

        if (!is_numeric($user_id))
            API::EndProcessWithCode("2.3.4.1");

        $this->user_id = $user_id;
        $this->path = $path;
    }
    #endregion
    #region Init
    public function Init(): void
    {
        switch ($this->path) {
            case "name":
                if (($this->data->name ?? "") == "")
                    API::EndProcessWithCode("2.3.4.4");

                if ($this->user_id != API::GetUserData()->id)
                    if (!API::IsSuperAdmin())
                        API::EndProcessWithCode("2.3.4.2");

                $this->ChangeName($this->data->name, $this->user_id);

                API::EndProcessWithCode("2.3.4.0");
            # Break
            case "surname":
                if (($this->data->surname ?? "") == "")
                    API::EndProcessWithCode("2.3.4.5");

                if ($this->user_id != API::GetUserData()->id)
                    if (!API::IsSuperAdmin())
                        API::EndProcessWithCode("2.3.4.2");

                $this->ChangeSurname($this->data->surname, $this->user_id);

                API::EndProcessWithCode("2.3.4.0");
            # Break
            case "email":
                if (($this->data->email ?? "") == "")
                    API::EndProcessWithCode("2.3.4.6");

                if ($this->user_id != API::GetUserData()->id)
                    if (!API::IsSuperAdmin())
                        API::EndProcessWithCode("2.3.4.2");

                $this->ChangeEmail($this->data->email, $this->user_id);

                API::EndProcessWithCode("2.3.4.0");
            # Break
            default:
                API::EndProcessWithCode("2.3.4.3");
        }
    }
    #endregion
    #region Private
    /**
     * This function change the name of a user
     *
     * @param string $name
     * @param int $user_id
     * @return void
     */
    private function ChangeName(string $name, int $user_id): void
    {
        $p = $this->conn->prepare("UPDATE `user` SET `name` = ? WHERE `id` = ?");
        $p->bind_param("sd", $name, $user_id);
        $p->execute();
    }

    /**
     * This function change the surname of a user
     *
     * @param string $surname
     * @param int $user_id
     * @return void
     */
    private function ChangeSurname(string $surname, int $user_id): void
    {
        $p = $this->conn->prepare("UPDATE `user` SET `surname` = ? WHERE `id` = ?");
        $p->bind_param("sd", $surname, $user_id);
        $p->execute();
    }

    /**
     * This function change the email of a user
     *
     * @param string $email
     * @param int $user_id
     * @return void
     */
    private function ChangeEmail(string $email, int $user_id): void
    {
        $p = $this->conn->prepare("UPDATE `user` SET `email` = ? WHERE `id` = ?");
        $p->bind_param("sd", $email, $user_id);
        $p->execute();
    }

    /**
     * This function change the password of a user
     *
     * @param string $password
     * @param int $user_id
     * @return void
     */
    private function ChangePassword(string $password, int $user_id): void
    {
        $p = $this->conn->prepare("UPDATE `user` SET `password` = ? WHERE `id` = ?");
        $p->bind_param("sd", $password, $user_id);
        $p->execute();
    }
    #endregion
}