<?php

/**
 * This class is used to get some statistics of the mealpass services
 */
class ServiceStatistics
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
    public function Init(): void
    {
        $sql_users = "SELECT COUNT(*) AS total_users FROM `user`";
        $result_users = $this->conn->query($sql_users);
        $row_users = $result_users->fetch_assoc();
        $total_users = $row_users["total_users"];

        $sql_meals = "SELECT COUNT(*) AS total_meals FROM `canteen`";
        $result_meals = $this->conn->query($sql_meals);
        $row_meals = $result_meals->fetch_assoc();
        $total_meals = $row_meals["total_meals"];

        $sql_credits = "SELECT COUNT(*) AS total_active_credits FROM `active_credit`";
        $result_credits = $this->conn->query($sql_credits);
        $row_credits = $result_credits->fetch_assoc();
        $total_active_credits = $row_credits["total_active_credits"];

        $data = new stdClass();
        $data->total_users = $total_users;
        $data->total_canteens = $total_meals;
        $data->total_active_credits = $total_active_credits;

        ProcessManager::EndProcessWithData($data, "2.4.6.0", log: false);
    }
    #endregion
    #region Private
    #endregion
}