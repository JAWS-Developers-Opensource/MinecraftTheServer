<?php

/**
 * This class allow to add an event for a specific customer
 *
 * @author Timo Coupek | JAWS Developers
 * @version 31.03.2023
 * @link /event/add
 */
class EventAdd
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $association_id;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
        $this->association_id = Client::GetAssociationId();
    }
    #endregion
    #region Init
    /**
     * @throws Exception
     */
    public function Init(): void
    {

        //Add coach or president permission
        if (($this->data->event->name ?? "") == "")
            ProcessManager::EndProcessWithCode("5.3.2.1");

        if (($this->association_id ?? "") == "")
            ProcessManager::EndProcessWithCode("5.3.2.2");

        if (!is_numeric($this->association_id))
            ProcessManager::EndProcessWithCode("5.3.2.3");

        ProcessManager::SetAffectedAssociation($this->association_id);
        
        if (!API::GetUser()->HasPermissionTo(Permission::EVENT_ADD))
            if (!API::GetUser()->IsAdmin())
            ProcessManager::EndProcessWithCode("5.3.1");

        ProcessManager::SetAffectedAssociation($this->association_id);


        //if (($this->data->event->report_template_id ?? "") == "")
            //ProcessManager::EndProcessWithCode("5.3.1");

        //if ((!is_numeric($this->data->event->report_template_id)))
            //ProcessManager::EndProcessWithCode("5.3.1");

        //Need to be changed
        //if (false)
            //ProcessManager::EndProcessWithCode("2.4.2.7");

        if (($this->data->event->start_date ?? "") == "")
            ProcessManager::EndProcessWithCode("5.3.2.8");

        if (!Dates::ValidateDate($this->data->event->start_date))
            ProcessManager::EndProcessWithCode("5.3.2.9");

        if (($this->data->event->end_date ?? "") == "")
            ProcessManager::EndProcessWithCode("5.3.2.10");

        if (!Dates::ValidateDate($this->data->event->end_date))
            ProcessManager::EndProcessWithCode("5.3.2.11");

        if($this->data->event->end_date < $this->data->event->start_date)
            ProcessManager::EndProcessWithCode("5.3.2.12");

        $this->InsertEvent();

        ProcessManager::EndProcessWithCode("5.3.0");
    }

    /**
     * This function can insert a new report
     *
     * @return void
     */
    private function InsertEvent(): void
    {
        $process = $this->conn->prepare("
            INSERT INTO `event` (`name`, `association_id`, `report_template_id`, `start_date`, `end_date`) 
            VALUES (?, ?, ?, ?, ?)");
        $r_id = ($this->data->event->report_template_id ?? 1);
        $process->bind_param("sddss", $this->data->event->name, $this->association_id, $r_id, $this->data->event->start_date, $this->data->event->end_date);
        $process->execute();
    }
}
