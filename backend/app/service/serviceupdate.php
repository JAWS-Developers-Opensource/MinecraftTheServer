<?php

/**
 * This class is used to update services
 *
 * @version 31.01.2024
 * @author Timo Coupek | JAWS Developers
 */
class ServiceUpdate
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
        switch ($this->service) {

            case "api":
                $branch = 'master'; // Usa 'dev' se vuoi per lo sviluppo
                $repoDir = '/mount/sportifyappco/api.sportifyapp.co';

                exec("cd $repoDir && git pull origin $branch", $output, $result);

                if ($result !== 0) {
                    ProcessManager::AddLogData("output", implode("\n", $output));
                    ProcessManager::EndProcessWithCode("4.6.2", $result);
                }

                ProcessManager::EndProcessWithCode("4.6.0");
                break;

            case "dashboard":
                $this->DashboardUpdate('master');
                break;
            case "dashboard-dev":
                $this->DashboardUpdate('dev');
                break;

            default:
                ProcessManager::EndProcessWithCode("4.6.1");
        }
    }
    #endregion
    #region Private

    private function DashboardUpdate(string $branch): void
    {
        $targetDir = $branch === "dev" ? "/mount/sportifyappco/dashboard-dev.sportifyapp.co" : "/mount/sportifyappco/dashboard.sportifyapp.co";
        $tmpDir = "/var/sportifyapp/dash/" . $branch;
        $buildDir = $tmpDir . "/dist/{.,}*";

        ProcessManager::AddLogData("branch", $branch);
        
        exec("cd $tmpDir && git fetch origin && git reset --hard FETCH_HEAD && git clean -fd $branch 2>&1", $output, $result);
        if ($result !== 0) {
            ProcessManager::AddLogData("output", implode("\n", $output));
            ProcessManager::EndProcessWithCode("4.6.3");
        }

        exec("cd $tmpDir && npm install 2>&1", $npmOutput, $npmResult);
        if ($npmResult !== 0) {
            ProcessManager::AddLogData("output", implode("\n", $output));
            ProcessManager::EndProcessWithCode("4.6.4");
        }

        exec("cd $tmpDir && npm run build 2>&1", $buildOutput, $buildResult);
        if ($buildResult !== 0) {
            http_response_code(500);
            echo "Errore durante la build: " . implode("\n", $buildOutput);
            ProcessManager::AddLogData("output", implode("\n", $output));
            ProcessManager::EndProcessWithCode("4.6.5");
        }

        // Copia i file della build nella directory di destinazione
        $command = "bash -c 'cp -r $buildDir $targetDir' 2>&1";
        exec($command, $copyOutput, $copyResult);
        if ($copyResult !== 0) {
            ProcessManager::AddLogData("output", implode("\n", $output));
            ProcessManager::EndProcessWithCode("4.6.6");
        } else {
            ProcessManager::EndProcessWithCode("4.6.0");
        }
    }


    #endregion
}
