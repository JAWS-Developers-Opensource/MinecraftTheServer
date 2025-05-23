<?php
include "app/association/associationget.php";
include "app/association/associationadd.php";
include "app/association/associationmembers.php";
include "app/association/associationmember.php";

/**
 * This class handles requests to association.
 * @author Timo Coupek | JAWS Developers
 * @version 21.03.2023
 */
class Associations
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

    public function Init(): void
    {
        switch ($this->path[0]){
            case "get":
                // /association/get
                (new AssociationGet($this->path[1] ?? ""))->Init();
                break;
            case "add":
                // /association/add
                (new AssociationAdd())->Init();
                break;

            case "members":
                // /association/members
                (new AssociationMembers())->Init();
                break;
            case "member":
                (new AssociationMember($this->path))->Init();
                break;
            default:
                ProcessManager::EndProcessWithCode("2.3.1.1");
        }
    }
    #rendregion
}