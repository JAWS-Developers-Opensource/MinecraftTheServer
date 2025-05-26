<?php

/**
 * desc
 * This class handles server
 *
 * @author Timo Coupek | JAWS Developers
 * @version 26.05.2025
 */
class Servers
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
    #endregion

    #region Public
    public function Init()
    {
        switch ($this->path[0]) {
            case "get":
                /**
                 * /users/get/*
                 * /users/get/[id]
                 * /users/get/me
                 * /users/get/my
                 */
                (new UserGet($this->path[1] ?? 0))->Init();
                break;
            default:
        }
    }
    #rendregion



}
