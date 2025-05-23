<?php

/**
 * This class makes it possible to recognise all necessary clients
 * @author Timo Coupek | JAWS Developers
 * @version 21.03.2023
 */
class AssociationGet
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
        $this->path = $path;
    }
    #endregion
    #region Init
   public function Init(): void
    {
        switch ($this->path)
        {
            case "*":
                if(!API::GetUser()->IsAdmin())
                    ProcessManager::EndProcessWithCode("3.2.1.1");

                ProcessManager::EndProcessWithData($this->GetAllAssociations(), "3.2.0.1");
            case is_numeric($this->path):
                if(!API::GetUser()->IsPartOf($this->path))
                    if(!API::GetUser()->IsAdmin())
                        ProcessManager::EndProcessWithCode("3.2.1.2");

                $association = Association::GetById($this->path);

                if(!$association)
                    ProcessManager::EndProcessWithCode("3.2.3");

                ProcessManager::EndProcessWithData($association, "3.2.0.2");
                break;
            default:
                ProcessManager::EndProcessWithCode("3.2.6");
        }
    }
    #endregion
    #region Private

    /**
     * This function returns all association
     *
     * @return array
     */
    private function GetAllAssociations(): array
    {
        $result = $this->conn->query("SELECT association.*, user.id as president_id FROM association LEFT JOIN role ON association.id = role.customer_id AND `role`.`role` = 'president' LEFT JOIN user ON role.user_id = user.id;");

        return Association::GetAllByResult($result);
    }

    #endregion


}