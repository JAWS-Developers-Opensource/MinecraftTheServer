<?php

/**
 * This customer allow to manage data of a user
 *
 * @author Timo Coupek | JAWS Developers
 * @version 29.03.2023
 */
class AssociationMember
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected array $path;
    #endregion
    #region Boot
    public function __construct(array $path)
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();

        if(($path[1] ?? "") == "")
            API::EndProcessWithCode("2.2.5.1");

        $this->path = $path;

    }
    #endregion
    #region Init
    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    #[NoReturn] public function Init(): void
    {

        switch ($this->path[1]) {
            case "promote":
                if(!API::IsSuperAdmin())
                    if(!Users::IsPresidentOf($this->path[2]))
                        API::EndProcessWithCode("2.2.5.2.1");

                $this->CheckData("2.2.5.2");
                $this->SetCoachUser($this->path[3], $this->path[2]);
                API::EndProcessWithCode("2.2.5.2.0");

                break;
            case "demote":
                if(!API::IsSuperAdmin())
                    if(!Users::IsPresidentOf($this->path[2]))
                        API::EndProcessWithCode("2.2.5.3.1");

                $this->CheckData("2.2.5.3");
                $this->SetAtleUser($this->path[3], $this->path[2]);
                API::EndProcessWithCode("2.2.5.3.0");
                break;

            case "addto":
                if(($this->data->email ?? "") == "")
                    API::EndProcessWithCode("2.2.5.4.2");

                if (($this->path[2] ?? "") == "")
                    API::EndProcessWithCode("2.2.5.4.3");

                if(!is_numeric($this->path[2]))
                    API::EndProcessWithCode("2.2.5.4.4");

                if(!Association::CheckIfCustomerExistsById($this->path[2]))
                    API::EndProcessWithCode("2.2.5.4.5");

                if(!API::IsSuperAdmin())
                    if(!Users::IsPresidentOf($this->path[2]))
                        if(!Users::IsCoachOf($this->path[2]))
                            API::EndProcessWithCode("2.2.5.4.1");

                if(User::ExistsByEmail($this->data->email)) {
                    $this->AddRoleToUser($this->data->email, $this->path[2]);

                    $this->SendEmailToExistingUser($this->data->email, $this->path[2]);

                } else {
                    $this->SendEmailToNewUser($this->data->email,
                        $this->path[2] ,
                        $this->CreateToken($this->data->email, $this->path[2]));
                }

                API::CloseConnection("2.2.5.4.0");

                    break;

            case "remove":
                break;

            default:
                API::EndProcessWithCode("2.2.5.1.1");
        }
    }
    #endregion
    #private region
    /**
     * This function set coach a user in a specific customer
     *
     * @param int $user_id
     * @param int $customer_id
     * @return void
     */
    private function SetCoachUser(int $user_id, int $customer_id): void
    {
        $p = $this->conn->prepare("UPDATE `role` SET `role` = 'coach' WHERE `user_id` = ? AND `customer_id` = ?");
        $p->bind_param("dd", $user_id, $customer_id);
        $p->execute();
    }

    /**
     * This function set atle a user in a specific customer
     *
     * @param int $user_id
     * @param int $customer_id
     * @return void
     */
    private function SetAtleUser(int $user_id, int $customer_id): void
    {
        $p = $this->conn->prepare("UPDATE `role` SET `role` = 'athlet' WHERE `user_id` = ? AND `customer_id` = ?");
        $p->bind_param("dd", $user_id, $customer_id);
        $p->execute();
    }

    /**
     * @param string $root
     * @return void
     */
    private function CheckData(string $root): void
    {
        if (($this->path[2] ?? "") == "")
            API::EndProcessWithCode($root . ".2");

        if (($this->path[3] ?? "") == "")
            API::EndProcessWithCode($root . ".3");

        if (!is_numeric($this->path[2]))
            API::EndProcessWithCode($root . ".4");

        if (!is_numeric($this->path[3]))
            API::EndProcessWithCode($root . ".5");

        if(!Association::CheckIfCustomerExistsById($this->path[2]))
            API::EndProcessWithCode($root . ".6");

        if(!User::ExistsById($this->path[3]))
            API::EndProcessWithCode($root . ".7");
    }

    /**
     * This function create the token for the registration
     *
     * @param string $email
     * @param string $customer_id
     * @return string
     */
    private function CreateToken(string $email, string $customer_id): string
    {
        $token = TokenManager::GenerateToken(30);
        while (TokenManager::CheckIfInvitationKeyExists($token))
        {
            $token = TokenManager::GenerateToken(30);
        }
        $p = $this->conn->prepare("INSERT INTO `registration_key` (`token`, `email`, `association_id`, `role`) VALUES (?,?,?,'atlet')");
        $p->bind_param("ssd", $token, $email, $customer_id);
        $p->execute();
        return $token;
    }

    /**
     * This function send the email to the new user
     *
     * @param string $email
     * @param int $association_id
     * @param string $token
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function SendEmailToNewUser(string $email, int $association_id, string $token): void
    {
        $association_name = Association::GetById($association_id)->name;
        $mail = new MailBase();

        $mail->SetBody(GetEmailForAtlet($association_name, $token));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been invited to" . $association_name);

        $mail->Send();

    }

    /**
     * This function send the email to the new user
     *
     * @param string $email
     * @param int $association_id
     * @return void
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function SendEmailToExistingUser(string $email, int $association_id): void
    {
        $association_name = Association::GetById($association_id)->name;
        $mail = new MailBase();

        $mail->SetBody(GetEmailForAtlet2($association_name));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been added to" . $association_name);

        $mail->Send();

    }

    /**
     * This function add the president role to the created customer
     * @param string $user_email
     * @param int $customer_id
     * @return void
     */
    private function AddRoleToUser(string $user_email, int $customer_id): void
    {
        $user_id = Users::GetUserbyEmail($user_email)->GetId();
        $p = $this->conn->prepare("INSERT INTO `role` (`user_id`, `customer_id`, `role`) VALUES (?,?,'atlet') ");
        $p->bind_param("dd", $user_id, $customer_id);
        $p->execute();
    }

    #endregion


}