<?php

/**
 * This class returns all members of a customer.
 *
 * @author Timo Coupek | JAWS Developers
 * @version 24.03.2023
 */
class AssociationMembers
{
    #region Prop
    protected mysqli $conn;
    protected string $association;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->conn = API::GetDBConnection();
        $this->association = Client::GetAssociationId();
        ProcessManager::AddLogData("association_id", $this->association);
    }
    #endregion
    #region Init
    public function Init()
    {
        $offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;

        // Validate the offset and limit
        if ($offset < 0 || $limit <= 0 || $limit > 100)
            ProcessManager::EndProcessWithCode("3.4.4");

        if ($this->association == "")
            ProcessManager::EndProcessWithCode("3.4.3.1");

        if (!is_numeric($this->association))
            ProcessManager::EndProcessWithCode("3.4.3.2");

        ProcessManager::SetAffectedAssociation($this->association);

        $user = API::GetUser();
        if (!$user->HasPermissionTo(Permission::ASOCIATION_MEMBERS))
            if (!$user->IsAdmin())
                ProcessManager::EndProcessWithCode("3.4.1");

        ProcessManager::AddLogData("offset", $offset);
        ProcessManager::AddLogData("limit", $limit);

        ProcessManager::EndProcessWithData($this->GetAllCustomerUsersOf($this->association, $offset, $limit), "3.4.0");
    }
    #endregion
    #region Private
    /**
     * This function returns all users of a specific customer that have as id the passed parameter
     *
     * @param int $customer_id
     * @param int $offset
     * @param int $limit
     * @return array
     */
    private function GetAllCustomerUsersOf(int $customer_id, int $offset, int $limit): array
    {
        $p = $this->conn->prepare("
        SELECT `user`.*, 
        IF(COUNT(`user_association`.association_id) = 0, 
           JSON_ARRAY(), 
           JSON_ARRAYAGG(
                   `user_association`.association_id
           )
        ) as association
        FROM `user`
        LEFT JOIN `user_association` 
            ON `user_association`.`user_id` = `user`.`id` 
            WHERE `user_association`.`association_id` = ? 
        GROUP BY `user`.`id`
        LIMIT ? OFFSET ?;
        ");
        $p->bind_param("iii", $customer_id, $limit, $offset);
        $p->execute();
        $result = $p->get_result();

        return User::GetAllByResult($result);
    }
    #endregion
}
