<?php

/**
 * This class is used to enable the services of mealpass
 *
 * @author Timo Coupek | JAWS Developers
 * @version 222051
 */
class VideoView
{
    #region Prop
    protected mysqli $conn;
    protected string $video;
    #endregion
    #region Boot
    public function __construct(string $video)
    {
        $this->conn = API::GetDBConnection();

        $this->video = $video;
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
        
    }
    #endregion
    #region Private

    #endregion

    #region Static
    
    #endregion
}