<?php

class ReportTemplateAdd
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    #endregion
    #region Boot

    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();

    }
    #endregion
    #region Init
    #[NoReturn] public function Init(): void
    {
        if(($this->data->customer_id ?? "") == "")
            API::EndProcessWithCode("2.5.2.3");

        if(!key_exists($this->data->customer_id, API::GetUser()->GetAssociationPermission()))
            API::EndProcessWithCode("2.5.2.1");

        if(API::GetUser()->GetAssociationPermission()[$this->data->customer_id] != "president")
            API::EndProcessWithCode("2.5.2.1");

        if(($this->data->name ?? "") == "")
            API::EndProcessWithCode("2.5.2.2");

        if(($this->data->form ?? "") == "")
            API::EndProcessWithCode("2.5.2.4");

        if(!is_array($this->data->form))
            API::EndProcessWithCode("2.5.2.5");

        if(!$this->CheckJsonValidity($this->data->form))
            API::EndProcessWithCode("2.5.2.6");

        $this->InsertReportTemplate($this->data->name, $this->data->customer_id, $this->data->form);

        API::EndProcessWithCode("2.5.2.0");
    }
    #endregion

    #region Private
    /**
     * Check if the json for the report template is valid
     *
     * @param array $json
     * @return bool true if valid
     */
    private function CheckJsonValidity(array $json): bool
    {
        foreach ($json as $item) {
            if(($item->type ?? "") == "")
                return false;

            if(($item->question ?? "") == "")
                return false;

        }

        return true;
    }

    private function InsertReportTemplate(string $name, int $customer_id, array $form)
    {
        $p = $this->conn->prepare("INSERT INTO `report_template` (`customer_id`, `name`, `report_template`) VALUES (?,?,?)");
        $p->bind_param("sds", $customer_id, $name, json_encode($form));
        $p->execute();
    }
    #endregion



}