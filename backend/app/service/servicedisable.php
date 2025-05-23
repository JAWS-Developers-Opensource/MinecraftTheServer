<?php

/**
 * This class is used to enable the services of mealpass
 *
 * @author Timo Coupek | JAWS Developers
 * @version 222051
 */
class ServiceDisable
{
    #region Prop
    protected mysqli $conn;
    protected string $service;
    #endregion
    #region Boot
    public function __construct(string $service)
    {
        $this->conn = API::GetDBConnection();

        $this->service = $service;
    }
    #endregion
    #region Init
    /**
     * Init function is used ti init the specific query.
     *
     * @return void
     */
    public function Init(): void
    {
        switch ($this->service)
        {
            case "api":
                self::DisableAPIService($this->conn);
                ProcessManager::EndProcessWithCode("4.5.0.1");
                break;

            case "dashboard":
                self::DisableDashboardService($this->conn);
                ProcessManager::EndProcessWithCode("4.5.0.2");
                break;

            /*case "mobile":
                 self::DIsableMobileService($this->conn);
                ProcessManager::EndProcessWithCode("2.4.4.0.3");
                break;*/

            default:
                ProcessManager::EndProcessWithCode("4.5.1");

        }
    }
    #endregion
    #region Private

    #endregion

    #region Static
    /**
     * This query is used to enable the api of mealpass
     *
     * @param mysqli $conn
     * @return void
     */
    public static function DisableAPIService(mysqli $conn): void
    {
        $conn->query("UPDATE `service` SET `status` = 0 WHERE `id_name` = 'api'");
    }

    /**
     * This query is used to enable the dashboard of mealpass
     *
     * @param mysqli $conn
     * @return void
     */
    public static function DisableDashboardService(mysqli $conn): void
    {
        $conn->query("UPDATE `service` SET `status` = 0 WHERE `id_name` = 'dashboard'");
    }


    /**
     * This query is used to enable the mobile application
     *
     * @param mysqli $conn
     * @return void
     */
    public static function DisableMobileService(mysqli $conn): void
    {
        $conn->query("UPDATE `service` SET `status` = 0 WHERE `id_name` = 'mobile'");
    }
    #endregion
}