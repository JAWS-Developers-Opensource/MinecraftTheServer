<?php
include "events.php";
/**
 * desc
 * This class handles event info
 *
 * @author Timo Coupek | JAWS Developers
 * @version 21.11.2022
 */
class Pub
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
    public static function GetCurrentEvent(): stdClass
    {
        return (new Events(""))->GetCurrent();
    }
    #endregion

    #region Public
    public function Init()
    {
        switch ($this->path[0]){
            case "association":
                // /public/association
                (new Events($this->path[1] ?? null))->Init();
                break;
            default:
                API::EndProcessWithCode("1.3.1.1", "Invalid path");
        }
    }
    #rendregion



}

?>