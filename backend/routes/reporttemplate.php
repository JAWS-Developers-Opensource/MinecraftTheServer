<?php
include "app/reporttemplate/reporttemplateadd.php";
class ReportTemplate
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

    #endregion

    #region Public
    public function Init():void
    {
        switch ($this->path[0])
        {
            case "add":
                (new ReportTemplateAdd())->Init();
                break;
            default:
                API::EndProcessWithCode("2.5.1.1");
        }
    }
    #endregion
}