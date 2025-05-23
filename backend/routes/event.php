<?php
include "app/event/eventadd.php";
include "app/event/eventget.php";
/**
 * This class allows to manage events.
 *
 * @author Timo Coupek | JAWS Developers
 * @version 31.03.2023
 */
class Event
{
    #region Prop
    /**
     * desc Path that defines the query
     *
     * @var array
     */
    protected array $path;
    #endregion

    #region Boot
    public function __construct(array $path)
    {
        $this->path = $path;
    }
    #endregion

    #region Static
    /**
     *  Function to check if event exist nn b
     * @param int $event_id event id
     * @return bool true if event exist
     */
    public static function CheckEvent(int $event_id): bool
    {

        $process = API::GetDBConnection()->prepare("SELECT * FROM `event` WHERE `id` = ?");
        $process->bind_param("d", $event_id);
        $process->execute();
        $result = $process->get_result();
        if (mysqli_num_rows($result))
            return true;
        return false;
    }

    /**
     *  Function to check if event exist nn b
     * @param int $event_id event id
     * @return stdClass true if event exist
     */
    public static function GetEvent(int $event_id): stdClass
    {

        $process = API::GetDBConnection()->prepare("SELECT * FROM `event` WHERE `id` = ?");
        $process->bind_param("d", $event_id);
        $process->execute();
        $event = new stdClass();
        $result = $process->get_result();
        if (mysqli_num_rows($result)) {
            while ($row = mysqli_fetch_array($result)) {
                $event->id = $row['id'];
                $event->name = $row['name'];
                $event->association_id = $row['association_id'];
            }
        }

        return $event ?? new stdClass();
    }


    #endregion
    #region Public
    public function Init(): void
    {
        switch ($this->path[0]) {
            case "get":
                (new EventGet($this->path[1]))->Init();
                break;
            case "create":
                (new EventAdd())->Init();
                break;
            default:
                ProcessManager::EndProcessWithCode("5.1.1");
        }
    }
    #rendregion
}
