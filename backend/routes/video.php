<?php
include "app/video/videoupload.php";
include "app/video/videoview.php";
/**
 * This class handles videos
 *
 * @author Timo Coupek | JAWS Developers
 * @version 24.11.2024
 */
class Video
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

    #region Public
    public function Init()
    {
        switch ($this->path[0]) {
            case "get":
                // /public/association
                //(new ($this->path[1] ?? null))->Init();
                break;
            case "upload":
                // /public/association
                (new VideoAdd($this->path[1] ?? null))->Init();
                break;
            case "view":
                // /public/association
                (new VideoView($this->path[1] ?? null))->Init();
                break;
            default:
                ProcessManager::EndProcessWithCode("6.1.1");
        }
    }
    #rendregion



}
