<?php

class Permission
{
    public const USER_GET = "user_get_all";
    public const USER_ADD = "user_add";
    public const USER_DEL = "user_del";
    public const USER_INVITATION_GET = "user_invitation_get";
    public const USER_INVITATION_RESEND = "user_invitation_resend";
    public const USER_INVITATION_DELETE = "user_invitation_delete";
    public const ASOCIATION_MEMBERS = "association_members";
    public const EVENT_ADD = "event_add";

    public static function CheckPermission(mysqli $conn): bool
    {

        $result = $conn->query("SELECT * FROM `permission`");
        $dbPerm = [];
        while ($row = mysqli_fetch_array($result)) {
            $dbPerm[] = $row['name'];
        }

        $classConstants = (new \ReflectionClass(self::class))->getConstants();

        $diffLocal = array_diff($dbPerm, $classConstants);
        $diffOnline = array_diff($classConstants, $dbPerm);

        $totalDiff = array_merge($diffLocal, $diffOnline);


        if (!empty($totalDiff))
            ProcessManager::EndProcessWithData(["missing_on_local" => $diffLocal, "missing_on_cloud" => $diffOnline], "0.1.3");

        return true; // Tutti i valori corrispondono
    }
}
