<?php

class User implements JsonSerializable
{


    public function __construct(stdClass|null $user)
    {
        if ($user == null)
            return;

        $this->SetId($user->id);
        $this->SetName($user->name);
        $this->SetSurname($user->surname);
        $this->SetEmail($user->email);
        $this->SetStatus($user->status);
        $this->SetRole($user->role);
        $this->SetProfilePicture($user->profile_picture);
        $this->SetAssociations($user->association);
    }

    #region PROPS
    protected int $id;

    /**
     * @return int
     */
    public function GetId(): int
    {
        return $this->id ?? 0;
    }

    /**
     * @param int $id
     */
    public function SetId(int $id): void
    {
        $this->id = $id;
    }


    protected string $name;

    /**
     * @return string
     */
    public function GetName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function SetName(string $name): void
    {
        $this->name = $name;
    }


    protected string $surname;


    /**
     * @return string
     */
    public function GetSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function SetSurname(string $surname): void
    {
        $this->surname = $surname;
    }


    protected string $email;

    /**
     * @return string
     */
    public function GetEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function SetEmail(string $email): void
    {
        $this->email = $email;
    }


    protected bool $status;

    /**
     * @return string
     */
    public function GetStatus(): string
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function SetStatus(bool $status): void
    {
        $this->status = $status;
    }


    protected array $role;

    /**
     * @return array
     */
    public function GetRole(): array
    {
        return $this->role;
    }

    /**
     * @param array $role
     */
    public function SetRole(array $role): void
    {
        $this->role = $role;
    }


    protected string $profile_picture;

    /**
     * @return string
     */
    public function GetProfilePicture(): string
    {
        return $this->profile_picture;
    }

    /**
     * @param string $profile_picture
     */
    public function SetProfilePicture(string $profile_picture): void
    {
        $this->profile_picture = $profile_picture;
    }



    protected array $association;

    /**
     * @return array
     */
    public function GetAssociations(): array
    {
        return $this->association;
    }

    /**
     * @param array $association
     */
    public function SetAssociations(array $association): void
    {
        $this->association = $association;
    }

    protected array $association_permission;

    /**
     * @return array
     */
    public function GetAssociationPermission(): array
    {
        return $this->association_permission;
    }

    /**
     * 
     */
    public function LoadPermissions(): void
    {
        if (empty($this->association)) {
            $this->association_permission = [];
        } else {
            foreach ($this->association as $association) {
                $this->association_permission[$association] = $this->GetPermissionOf($this->GetId(), $association);
            }
        }
    }

    #endregion

    #region PUBLIC

    /**
     * This function che if the current user is an admin
     *
     * @return bool return true if is an admin
     */
    public function IsAdmin(): bool
    {
        if (in_array("admin", $this->GetRole()))
            return true;

        return false;
    }
    
    public function HasPermissionTo(string $permission): bool
    {
        if(!array_key_exists(ProcessManager::GetAffectedAssociation(), $this->association_permission))
            return false;

        if (in_array($permission, $this->association_permission[ProcessManager::GetAffectedAssociation()]))
            return true;

        return false;
    }


    /**
     * 
     */
    public function IsPartOf(int $association_id)
    {
        return in_array($association_id, $this->GetAssociations());
    }

    public function UpdateLastLogin(): void
    {
        $procedure = API::GetDBConnection()->prepare("UPDATE `user` SET `last_login`= NOW() WHERE `id` = ?");
        $u_id = $this->GetId();
        $procedure->bind_param("s", $u_id);
        $procedure->execute();
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }

    #endregion

    #region STATIC
    /**
     * Returns the user IP address
     *
     * @return string IP address
     */
    public static function GetIP(): string
    {
        // Controlla se l'IP è passato attraverso un proxy
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // L'IP reale del client
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Rimuove eventuali porte dall'IP
        $ip = explode(',', $ip)[0]; // Prende solo il primo IP in caso ci siano più IP
        $ip = preg_replace('/:\d+$/', '', $ip); // Rimuove porte (se presenti)

        return $ip;
    }
    
    /**
     * Get user permission from id
     *
     * @param integer $user_id
     * @return array
     */
    private function GetPermissionOf(int $user_id, int $association_id): array
    {
        $p = API::GetDBConnection()->prepare("SELECT `permission`.`name` 
            FROM `user_association` 
            JOIN `roles` ON `user_association`.`role_id` = `roles`.`id` 
            JOIN `role_permission` ON `roles`.`id` = role_permission.role_id 
            JOIN `permission` ON `permission`.`id` = role_permission.permission_id 
            WHERE `user_association`.`user_id` = ? AND `user_association`.`association_id` = ?");
        $p->bind_param("ss", $user_id, $association_id);
        $p->execute();
        $result = $p->get_result();

        $per = array();
        while ($row = mysqli_fetch_array($result)) {
            $per[] = $row['name'];
        }

        return $per;
    }

    /**
     * This function checks if the user exists
     *
     * @param string $email
     * @return bool true if exists
     */
    public static function ExistsByEmail(string $email): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `user` WHERE `email` = ?");
        $p->bind_param("s", $email);
        $p->execute();
        $result = $p->get_result();

        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    /**
     * This function checks if the user exists
     *
     * @param int $id
     * @return bool true if exists
     */
    public static function ExistsById(int $id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `user` WHERE `id` = ?");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();

        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    /**
     * Get formatted user resutl from query
     *
     * @param mysqli_result $result
     * @return User User
     */
    public static function GetByResult(mysqli_result $result): User
    {

        if (!mysqli_num_rows($result))
            throw new Exception("The result doesn't contain any user");

        $row = $result->fetch_assoc();

        $user = new stdClass();

        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->name = $row['name'];
        $user->surname = $row['surname'];
        $user->status = $row['status'];
        $user->profile_picture = $row['profile_picture'];
        $user->role = json_decode($row['role']);
        $user->association = json_decode($row['association']);

        return new User($user);
    }

    /**
     * Get multiple formatted users result from query
     * 
     * @param mysqli_result Query reulst
     * @return Users[]
     */

    public static function GetAllByResult(mysqli_result $result): array
    {
        $users = array();
        while ($row = mysqli_fetch_array($result)) {
            $user = new stdClass();

            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->name = $row['name'];
            $user->surname = $row['surname'];
            $user->status = $row['status'];
            $user->profile_picture = $row['profile_picture'];
            $user->role = json_decode($row['role']);
            $user->association = json_decode($row['association']);

            $users[] = new User($user);
        }

        return $users;
    }
    #endregion
}
