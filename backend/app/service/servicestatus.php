<?php

/**
 * This class is used to get the status of a specific service of mealpass
 *
 * @author Timo Coupek | JAWS Developers
 * @version 21.10.2024
 */
class ServiceStatus
{
    #region Prop
    protected mysqli $conn;
    protected string $service;
    #endregion
    #region Boot
    public function __construct(string $service)
    {
        $this->conn = API::GetDBConnection();

        if($service == "")
            ProcessManager::EndProcessWithCode("4.2.1");

        $this->service = $service;
    }
    #endregion
    #region Init
    /**
     * Init function is used ti init the specific query.
     *
     * @return void
     * @throws Exception
     */
    public function Init(): void
    {
        switch ($this->service)
        {
            case "dashboard":
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/JAWS-Developers/SportifyappDashboard/contents/package.json");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, "SportifyApp");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . GITHUB_TOKEN));

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    throw new Exception(curl_error($ch));
                }

                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpcode != 200) {
                    $version = "x.x.x";

                } else {
                    curl_close($ch);

                    $data = json_decode($response);
                    $content = base64_decode($data->content);
                    $packageJson = json_decode($content);
                    $version = $packageJson->version;
                }


                ProcessManager::EndProcessWithData($this->GetDashboardStatus($version), "4.2.0.2", log: false);
                break;
            /*case "mobile":
                ProcessManager::EndProcessWithData($this->GetMobileStatus(), "2.4.2.2.0.3", log: false);
                break;*/
            default:
                ProcessManager::EndProcessWithCode("2.4.2.1");

        }
    }
    #endregion
    #region Private
    #endregion
    #region Public

    /**
     * This query returns the status of the mealpass api
     *
     * @return stdClass
     */
    public function GetAPIStatus(): stdClass
    {
        $result = $this->conn->query("SELECT * FROM `service` WHERE `id_name` = 'api'");

        $api_status = new stdClass();

        while ($row = mysqli_fetch_array($result))
        {
            $api_status->name = $row['id_name'];
            $api_status->status = $row['status'];
            $api_status->last_update = $row['last_update'];
        }

        return $api_status;
    }

    /**
     * This query returns the mealpass dashboard status
     *
     * @param string $version
     * @return stdClass
     */
    public function GetDashboardStatus(string $version): stdClass
    {
        $result = $this->conn->query("SELECT * FROM `service` WHERE `id_name` = 'dashboard'");

        $api_status = new stdClass();

        while ($row = mysqli_fetch_array($result))
        {
            $api_status->name = $row['id_name'];
            $api_status->status = filter_var($row['status'], FILTER_VALIDATE_BOOLEAN);
            $api_status->last_update = $row['last_update'];
            $api_status->version = $version;
        }

        return $api_status;
    }

    /**
     * This query returns the mealpass mobile status
     *
     * @return stdClass
     */
    public function GetMobileStatus(): stdClass
    {
        $result = $this->conn->query("SELECT * FROM `service` WHERE `id_name` = 'mobile'");

        $api_status = new stdClass();

        while ($row = mysqli_fetch_array($result))
        {
            $api_status->name = $row['id_name'];
            $api_status->status = $row['status'];
            $api_status->last_update = $row['last_update'];
        }

        return $api_status;
    }
    #endregion
}