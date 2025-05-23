<?php
include "app/service/servicestatus.php";
include "app/service/serviceenable.php";
include "app/service/servicedisable.php";
include "app/service/serviceupdate.php";
include "app/service/servicestatistics.php";
/**
 * This class is used to manage all services of meal pass. You can manage the status and the access leve like for
 * developing
 *
 * @author Timo Coupek | JAWS Developers
 * @version 16.01.2024
 */
class Service
{
    #region Prop
    /**
     * desc Path that defines the login or the register
     *
     * @var array
     */
    protected array $path;

    /**
     * Bool that contains if the user is authenticated
     *
     * @var bool
     */
    protected bool $isAuthenticated;
    #endregion

    #region Boot
    public function __construct(array $path, bool $isAuthenticated)
    {
        $this->path = $path;
        $this->isAuthenticated = $isAuthenticated;
    }
    #endregion

    #region Public
    /**
     * Init function is used ti init the specific query.
     *
     * @return void
     * @throws Exception
     */
    public function Init(): void
    {
        array_shift($this->path);

        if (!$this->isAuthenticated) {
            switch ($this->path[0]) {
                case "status":
                    /**
                     * /service/status/api
                     * /service/status/mobile
                     * /service/status/dashboard
                     */
                    (new ServiceStatus($this->path[1] ?? ""))->Init();
                    break;

                case "statistics":
                    /**
                     * /service/statistics
                     */
                    //(new ServiceStatistics())->Init();
                    break;
                default:
                    break;
            }
        } else {
            if(!API::GetUser()->IsAdmin())
                ProcessManager::EndProcessWithCode("4.1.1");

            switch ($this->path[0]) {
                case "enable":
                    /**
                     * /service/enable/api
                     * /service/enable/mobile
                     * /service/enable/dashboard
                     */
                    (new ServiceEnable($this->path[1] ?? ""))->Init();
                    break;
                case "disable":
                    /**
                     * /service/disable/api
                     * /service/disable/mobile
                     * /service/disable/dashboard
                     */
                    (new ServiceDisable($this->path[1] ?? ""))->Init();
                    break;
                case "update":
                    /**
                     * /service/update/dashboard
                     */
                    (new ServiceUpdate($this->path[1] ?? ""))->Init();
                    break;
                default:
                    ProcessManager::EndProcessWithCode("4.1.2");
            }
        }
    }
}
#endregion