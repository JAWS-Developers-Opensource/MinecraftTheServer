<?php

/**
 * This class is used to create a registration token NOT TO ADD A USER TO THE USER TABLE
 *
 * @author Timo Coupek
 * @version 09.10.2024
 */
class UserAdd
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $association_id;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
        $this->association_id = Client::GetAssociationId();
    }

    #endregion
    #region Init
    public function Init(): void
    {
        if (($this->data->email ?? "") == "")
            ProcessManager::EndProcessWithCode("2.3.2.1");

        if (($this->association_id ?? "") == "")
            ProcessManager::EndProcessWithCode("2.3.3.1");

        if (!is_numeric($this->association_id))
            ProcessManager::EndProcessWithCode("2.3.3.2");  

        ProcessManager::SetAffectedAssociation($this->association_id);

        $user = API::GetUser();
        if (!$user->HasPermissionTo(Permission::USER_ADD))
            if (!$user->IsAdmin())
                ProcessManager::EndProcessWithCode("2.3.1");

        ProcessManager::AddLogData("email", $this->data->email);

        if (User::ExistsByEmail($this->data->email)) {
            $invited_user = UserGet::ByEmail($this->data->email);

            if (TokenManager::InvitationKeyExistByAssocaition($invited_user->GetId(), $this->association_id))
                ProcessManager::EndProcessWithCode("2.3.2.2");


            if ($invited_user->IsPartOf($this->association_id))
                ProcessManager::EndProcessWithCode("2.3.2.3");

            $this->SendEmailToExistingUser(
                $this->data->email,
                $this->association_id,
                $this->InviteUser($invited_user->GetId(), $this->association_id)
            );

            ProcessManager::EndProcessWithCode("2.3.0.1");
        } else {

            if (TokenManager::RegistrationKeyExistByAssocaition($this->data->email, $this->association_id))
                ProcessManager::EndProcessWithCode("2.3.2.2");

            $this->SendEmailToNewUser(
                $this->data->email,
                $this->association_id,
                $this->CreateToken($this->data->email, $this->association_id)
            );

            ProcessManager::AddLogData("new_user_email", $this->data->email);
            ProcessManager::EndProcessWithCode("2.3.0.2");
        }
    }
    #endregion

    #region PRIVATE
    /**
     * This function create the token for the registration
     *
     * @param string $email
     * @param string $association_id
     * @return string
     */
    private function CreateToken(string $email, string $association_id): string
    {
        $token = TokenManager::GenerateToken(128, "registration");
        while (TokenManager::RegistrationKeyExist($token)) {
            $token = TokenManager::GenerateToken(128, "registration");
        }
        $p = $this->conn->prepare("INSERT INTO `registration_key` (`token`, `email`, `association_id`, `role_id`) VALUES (?,?,?,'2')");
        $p->bind_param("ssd", $token, $email, $association_id);
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
        $association_name = Association::GetById($association_id)->GetName();
        $user = API::GetUser()->GetName();
        $mail = new MailBase();

        $mail->SetBody(EmailGenerator::generateEmail('registration', ['token' => $token, 'association_name' => $association_name, 'user' => $user]));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been invited to " . $association_name);

        $mail->Send();
    }
    #endregion

    /**
     * @param string $email
     * @param int $association_id
     * @return string
     *
     */
    private function InviteUser(int $user_id, int $association_id): string
    {
        $token = TokenManager::GenerateToken(128, "invitation");
        while (TokenManager::InvitationKeyExist($token)) {
            $token = TokenManager::GenerateToken(128, "invitation");
        }
        $p = API::GetDBConnection()->prepare("INSERT INTO `invitation_key` (`user_id`, `association_id`, `role_id`, `token`) VALUE (?, ?, '2', ?)");
        $p->bind_param("sds", $user_id, $association_id, $token);
        $p->execute();
        return $token;
    }

    private function SendEmailToExistingUser(string $email, int $association_id, string $token): void
    {
        $association_name = Association::GetById($association_id)->GetName();
        $user = UserGet::ByEmail($email);
        $invitierUser = API::GetUser()->GetName();
        $mail = new MailBase();

        $mail->SetBody(EmailGenerator::generateEmail('invitation', ['sign' => ucfirst(strtolower($user->GetName())), 'token' => $token, 'association_name' => $association_name, 'user' => $invitierUser]));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been invited to" . $association_name);

        $mail->Send();
    }
}
