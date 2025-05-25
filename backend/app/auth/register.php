<?php

/**
 * Class to manage registration user
 *
 * @author Timo Coupek | JAWS Developers
 * @version 09.10.2024
 */
class Register
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $role;
    protected int $customer_id;
    protected int $user_id;
    protected string $device;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
    }
    #endregion
    #region Init
    public function Init(): void
    {
        if (($this->data->username ?? "") == "")
            ProcessManager::EndProcessWithCode("1.2.2.1");

        if (($this->data->password ?? "") == "")
            ProcessManager::EndProcessWithCode("1.2.2.2");

        if (($this->data->name ?? "") == "")
            $this->data->name = "";

        if (($this->data->surname ?? "") == "")
            $this->data->surname = "";

        if ($this->CheckUsernameValidity($this->data->username))
        {
            ProcessManager::AddLogData("username", $this->data->username);
            ProcessManager::EndProcessWithCode("1.2.2.3");
        }

        if (!$this->CheckPasswordSafety($this->data->password))
        {
            ProcessManager::EndProcessWithCode("1.2.2.4");
        }
              
        if (!$this->CreateUser($this->data->username, $this->data->password, $this->data->name, $this->data->surname))
            ProcessManager::EndProcessWithCode("1.2.10");

        //$this->DeleteRegisterCode($this->data->token);

        //$this->AssignRoleInCustomer($this->role, $this->customer_id);

        //$this->SendWelcomeEmail($this->data->username);
        ProcessManager::EndProcessWithCode("1.2.0");
    }
    #endregion

    #region Private
    /**
     * Function to check id the username is valid
     *
     * @param string $username Email
     * @return true if exists
     */
    private function CheckUsernameValidity(string $username, bool $save_id = false): bool
    {
        # Lower Case
        $username = strtolower($username);

        $p = $this->conn->prepare("SELECT * FROM `user` WHERE `username` = ?");
        $p->bind_param("s", $username);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result)) {
            if (!$save_id)
                return true;

            while ($row = mysqli_fetch_array($result)) {
                $this->user_id = $row['id'];
            }
            return true;
        }

        return false;
    }


    /**
     * Function to check the password safety
     *
     * @param string $password User password
     * @return boolean true for valid
     */
    private function CheckPasswordSafety(string $password): bool
    {

        # Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8)
            return false;
        return true;
    }

    /**
     * Function to create the user
     *
     * @param string $email
     * @param string $password
     * @param string $name
     * @param string $surname
     * @return bool
     */
    public function CreateUser(string $username, string $password, string $name, string $surname): bool
    {

        # Lower Case all and crypt
        $username = strtolower($username);
        $name = strtolower($name);
        $surname = strtolower($surname);

        $password = $password . PASSWORD_PAPER;

        $password = "MTS-pass--" . password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 20, // 64 MB di memoria
            'time_cost' => 4,         // 4 passaggi di hashing
            'threads' => 2            // Due thread (supporto multi-threading)
        ]);

        $p = $this->conn->prepare("INSERT INTO `user` (`email`, `name`, `surname`, `password`) VALUES (?,?,?,?)");
        $p->bind_param("ssss", $username, $name, $surname, $password);
        $p->execute();

        # Check if user are inserted
        if ($this->CheckUsernameValidity($username, true))
            return true;
            
        return false;
    }

    /**
     * This function deletes the registration code passed by parameter
     * @param $code
     * @return void
     */
    private function DeleteRegisterCode($code): void
    {
        $p = $this->conn->prepare("DELETE  FROM `registration_key` WHERE `token` = ?");
        $p->bind_param("s", $code);
        $p->execute();
    }

    /**
     * This function assign a passed role to the passed customer
     *
     * @param string $role
     * @param int $association_id
     * @return void
     */
    private function AssignRoleInCustomer(int $role_id, int $association_id): void
    {
        $p = $this->conn->prepare("INSERT INTO `user_association` (`user_id`, `association_id`, `role_id`) VALUES (?,?,?)");
        $p->bind_param("dds", $this->user_id, $association_id, $role_id);
        $p->execute();
    }

    private function SendWelcomeEmail(string $email)
    {
        $mail = new MailBase();

        $mail->SetRecipient($email);

        $mail->SetSubject("Welcome to Sportify");

        $mail->SetBody(EmailGenerator::generateEmail('registered', ['name' => ucfirst(strtolower($this->data->name))]));
    
        $mail->Send();
    }
    #endregion

}
