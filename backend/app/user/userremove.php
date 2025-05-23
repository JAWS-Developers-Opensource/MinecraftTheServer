<?php

/**
 * This class allows a user to be deleted.
 * The class must be initialised with the user ID and requires authentication by the administrator or
 * president of the association.
 * @author Timo Coupek | JAWS Developers
 * @version 21.12.2022
 */
class UserRemove
{

    #region Prop
    protected mysqli $conn;
    protected string $user_id;
    protected stdClass $data;

    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
    }
    #endregion
    #region Init
    /**
     * @throws Exception
     */
    public function Init(): void
    {
        if (($this->data->user_id ?? "") == "")
            ProcessManager::EndProcessWithCode("2.5.2.1");

        if (!is_numeric($this->data->user_id))
            ProcessManager::EndProcessWithCode("2.5.2.2");

        if (!(API::GetUser()->GetId() == $this->data->user_id))
            if (!API::GetUser()->IsAdmin())
                ProcessManager::EndProcessWithCode("2.5.1");

        $this->DeleteUser($this->user_id);

        ProcessManager::EndProcessWithCode("2.5.0");
    }

    /**
     * This function allows to delete a user
     *
     * @param int|string $user_id
     * @return void
     */
    private function DeleteUser(int|string $user_id)
    {
        $p = $this->conn->prepare("DELETE FROM `user` WHERE id = ?");
        $p->bind_param("d", $user_id);
        $p->execute();
    }
}
