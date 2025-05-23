<?php

/**
 * This class is used to enable the services of mealpass
 *
 * @author Timo Coupek | JAWS Developers
 * @version 222051
 */
class UserInvitation
{
    #region Prop
    protected mysqli $conn;
    protected string $path;
    protected string $association_id;
    protected stdClass $data;
    #endregion
    #region Boot
    public function __construct(string $path)
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
        $this->association_id = Client::GetAssociationId();
        $this->path = $path;
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
        $token = $_GET['token'] ?? "";

        switch ($this->path) {
            case "accept":
                if ($token == "")
                    ProcessManager::EndProcessWithCode("2.6.5.1");

                $invitation = $this->GetInvitation($token);

                if (!$invitation)
                    ProcessManager::EndProcessWithCode("2.6.5.2");

                if ($invitation->user_id != API::GetUser()->GetId())
                    ProcessManager::EndProcessWithCode("2.6.5.3");

                if ($this->path == "get")
                    ProcessManager::EndProcessWithData($invitation, "2.6.0.5");
                $this->InvitationAccept($token, $invitation->association_id, $invitation->role_id);
                $this->SendMailForInvitationAccept(API::GetUser()->GetEmail(), $invitation->association_id);

                ProcessManager::EndProcessWithCode("2.6.0.4");
                break;

            default:

                if (($this->association_id ?? "") == "")
                    ProcessManager::EndProcessWithCode("2.6.3.1");

                if (!is_numeric($this->association_id))
                    ProcessManager::EndProcessWithCode("2.6.3.2");

                ProcessManager::SetAffectedAssociation($this->association_id);
        }

        switch ($this->path) {
            case '':
                if (!API::GetUser()->HasPermissionTo(Permission::USER_INVITATION_GET))
                    if (!API::GetUser()->IsAdmin())
                        ProcessManager::EndProcessWithCode("2.6.1");

                ProcessManager::EndProcessWithData($this->GetInvitations($this->association_id), "2.6.0.1");
                break;

            case "resend":
                if (!API::GetUser()->HasPermissionTo(Permission::USER_INVITATION_RESEND))
                    if (!API::GetUser()->IsAdmin())
                        ProcessManager::EndProcessWithCode("2.6.1");

                if (($this->data->email ?? "") == "")
                    ProcessManager::EndProcessWithCode("2.6.2.1");

                if (User::ExistsByEmail($this->data->email)) {
                    if (!$this->CheckIfInvitationExists($this->data->email, $this->association_id))
                        ProcessManager::EndProcessWithCode("2.6.2.2");

                    $this->SendEmailToNewUser($this->data->email, $this->association_id, $this->GenereteNewInvitation($this->data->email));
                    ProcessManager::EndProcessWithCode("2.6.0.2");
                } else {
                    if (!$this->CheckIfregistrationExists($this->data->email, $this->association_id))
                        ProcessManager::EndProcessWithCode("2.6.2.2");

                    $this->SendEmailToExistingUser($this->data->email, $this->association_id, $this->GenereteNewInvitation($this->data->email));
                    ProcessManager::EndProcessWithCode("2.6.0.2");
                }
                break;

            case "delete":
                if (!API::GetUser()->HasPermissionTo(Permission::USER_INVITATION_DELETE))
                    if (!API::GetUser()->IsAdmin())
                        ProcessManager::EndProcessWithCode("2.6.1");

                if (($this->data->email ?? "") == "")
                    ProcessManager::EndProcessWithCode("2.6.2.1");

                if (User::ExistsByEmail($this->data->email)) {
                    if (!$this->CheckIfInvitationExists($this->data->email, $this->association_id))
                        ProcessManager::EndProcessWithCode("2.6.2.2");

                    $this->InvitationDelete($this->data->email, $this->association_id);
                    ProcessManager::EndProcessWithCode("2.6.0.3");
                } else {
                    if (!$this->CheckIfregistrationExists($this->data->email, $this->association_id))
                        ProcessManager::EndProcessWithCode("2.6.2.2");

                    $this->RegistrationDelete($this->data->email, $this->association_id);
                    ProcessManager::EndProcessWithCode("2.6.0.3");
                }
                break;

            default:
                ProcessManager::EndProcessWithCode("2.6.6");
                break;
        }
    }
    #endregion
    #region Private
    private function GetInvitations(int $association_id): array
    {
        $p = $this->conn->prepare("SELECT 
            `registration_key`.`token`, `registration_key`.`email`, `roles`.`name` as 'role',
                'registration' AS type, `registration_key`.`issued_date`
            FROM 
                `registration_key`
            JOIN `roles` ON `roles`.`id` = registration_key.role_id

            WHERE 
                `registration_key`.`association_id` = ?

            UNION ALL

            SELECT 
                `invitation_key`.`token`, `user`.`email`, `roles`.`name` as 'role',
                'invitation' AS type, `invitation_key`.`issued_date`
            FROM 
                `invitation_key`
			JOIN `user`
			ON `invitation_key`.`user_id` = `user`.`id`
            JOIN `roles` ON `roles`.`id` = invitation_key.role_id
            WHERE 
               `invitation_key`.`association_id` = ?;
        ");

        $p->bind_param("ss", $association_id, $association_id);
        $p->execute();
        $result = $p->get_result();

        $invitations = array();
        while ($row = mysqli_fetch_array($result)) {
            $invitation = new stdClass();

            $invitation->token = $row['token'];
            $invitation->email = $row['email'];
            $invitation->role = $row['role'];
            $invitation->type = $row['type'];
            $invitation->issued_date = $row['issued_date'];

            $invitations[] = $invitation;
        }

        return $invitations;
    }

    /**
     * This function create the token for the registration
     *
     * @param string $email
     * @param string $association_id
     * @return string
     */
    private function GenereteNewInvitation(string $email): string
    {
        $token = TokenManager::GenerateToken(128, "registration");
        while (TokenManager::RegistrationKeyExist($token)) {
            $token = TokenManager::GenerateToken(128, "registration");
        }
        $p = $this->conn->prepare("UPDATE `registration_key` SET `token` = ?, `issued_date` = NOW() WHERE `email` = ?");
        $p->bind_param("ss", $token, $email);
        $p->execute();
        return $token;
    }

    /**
     * Check if invitation key exists
     */
    public static function CheckIfregistrationExists(string $email, string $association_id): bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `registration_key` WHERE `email` = ? AND `association_id` = ?");
        $p->bind_param("dd", $email, $association_id);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    /**
     * Check if invitation key exists
     */
    public static function CheckIfInvitationExists(string $email, string $association_id): bool
    {
        $user_id = UserGet::ByEmail($email)->GetId();
        $p = API::GetDBConnection()->prepare("SELECT * FROM `invitation_key` WHERE `user_id` = ? AND `association_id` = ?");
        $p->bind_param("dd", $user_id, $association_id);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        if (mysqli_num_rows($result))
            return true;

        return false;
    }

    /**
     * Check if invitation key exists
     */
    public static function GetInvitation(string $token): stdClass | bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `invitation_key` JOIN `association` ON `association`.`id` = `invitation_key`.`association_id` WHERE `token` = ? AND DATE_ADD(`issued_date`, INTERVAL 7 DAY) >= NOW()");
        $p->bind_param("s", $token);
        $p->execute();
        $result = $p->get_result();

        # Check if exist
        $row = mysqli_fetch_array($result);

        if (!$row)
            return false;

        $invitation = new stdClass();
        $invitation->user_id = $row['user_id'];
        $invitation->issued_date = $row['issued_date'];
        $invitation->association_name = $row['name'];
        $invitation->role_id = $row['role_id'];
        $invitation->association_id = $row['association_id'];

        return $invitation;
    }

    public function InvitationAccept(string $token, string $association_id, int $role_id): void
    {
        $p = $this->conn->prepare("DELETE FROM `invitation_key` WHERE `token` = ?");
        $p->bind_param("s", $token);
        $p->execute();
        $user_id = API::GetUser()->GetId();
        $p = $this->conn->prepare("INSERT INTO `user_association` (`user_id`, `association_id`, `role_id`) VALUES (?,?,?)");
        $p->bind_param("dds", $user_id, $association_id, $role_id);
        $p->execute();
    }

    
    public function InvitationDelete(string $email, string $association_id): void
    {
        $user_id = UserGet::ByEmail($email)->GetId();
        $p = $this->conn->prepare("DELETE FROM `invitation_key` WHERE `user_id` = ? AND `association_id` = ?");
        $p->bind_param("dd", $user_id, $association_id);
        $p->execute();
    }

    public function RegistrationDelete(string $email, string $association_id): void
    {
        $p = $this->conn->prepare("DELETE FROM `registration_key` WHERE `email` = ? AND `association_id` = ?");
        $p->bind_param("dd", $email, $association_id);
        $p->execute();
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

        $mail->SetBody(EmailGenerator::generateEmail('register', ['token' => $token, 'association_name' => $association_name, 'user' => $user]));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been reinvited to " . $association_name);

        $mail->Send();
    }

    private function SendEmailToExistingUser(string $email, int $association_id, string $token): void
    {
        $association_name = Association::GetById($association_id)->GetName();
        $user = UserGet::ByEmail($email);
        $mail = new MailBase();

        $mail->SetBody(EmailGenerator::generateEmail('invitation', ['sign' => ucfirst(strtolower($user->GetName())), 'token' => $token, 'association_name' => $association_name]));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been invited to" . $association_name);

        $mail->Send();
    }

    private function SendMailForInvitationAccept(string $email, int $association_id): void
    {
        $association_name = Association::GetById($association_id)->GetName();
        $user = API::GetUser()->GetName();
        $mail = new MailBase();

        $mail->SetBody(EmailGenerator::generateEmail('accept', ['sign' => $user, 'association_name' => $association_name]));

        $mail->SetRecipient($email);

        $mail->SetSubject("You have been reinvited to " . $association_name);

        $mail->Send();
    }
    #endregion

    #region Static

    #endregion
}
