<?php

/**
 * This class is used to enable the services of mealpass
 *
 * @author Timo Coupek | JAWS Developers
 * @version 222051
 */
class Logout
{
    #region Prop
    protected mysqli $conn;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->conn = API::GetDBConnection();
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
        Auth::CheckUserCredential();

        $token = Client::GetBearerToken();

        TokenManager::RevokeToken($token);

        ProcessManager::EndProcessWithCode("1.5.0");
    }
    #endregion
    #region Private

    #endregion

    #region Static

    #endregion
}
