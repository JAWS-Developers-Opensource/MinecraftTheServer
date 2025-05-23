<?php


include "new_customer.php";

/**
 * This class allows a customer to be added.
 *
 * @author Timo Coupek | JAWS Developers
 * @version 21.03.2023
 */
class AssociationAdd
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $token;
    #endregion
    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
    }
    #endregion
    #region Init
    /**
     * @throws Exception
     */
    public function Init(): void
    {
        if (!API::GetUser()->IsAdmin())
            ProcessManager::EndProcessWithCode("3.3.1");

        if (($this->data->name ?? "") == "")
            ProcessManager::EndProcessWithCode("3.3.2.1");

        if (strlen($this->data->name ?? "") > 30)
            ProcessManager::EndProcessWithCode("3.3.2.2");

        if (($this->data->president_email ?? "") == "")
            ProcessManager::EndProcessWithCode("3.3.2.3");

        if (Association::ExistsByName($this->data->name))
            ProcessManager::EndProcessWithCode("3.3.2.4");

        if (User::ExistsByEmail($this->data->president_email)) {
            $this->CreateAssociation($this->data->name);

            $this->AddRoleToUser($this->data->customer_email, $this->data->name);

            $this->SendWelcomeEmailToPresident($this->data->president_email, $this->data->name);
        } else {
            $this->CreateAssociation($this->data->name);

            $this->InsertRegistrationKey($this->data->president_email, $this->data->name);

            $this->SendRegistrationEmailToPresident($this->data->president_email);
        }

        API::CloseConnection("2.2.3.0", $this->data->name);
    }
    #endregion

    #region Private
    /**
     * This function create the customer in the database
     *
     * @param string $customer_name
     * @return void
     */
    private function CreateAssociation(string $customer_name): void
    {
        $customer_name = strtolower($customer_name);
        $p = $this->conn->prepare("INSERT INTO `association` (`name`) VALUES (?)");
        $p->bind_param("s", $customer_name);
        $p->execute();
    }

    /**
     * This function allows you to enter the president's registration code
     * @param string $email
     * @param string $customer_name
     * @return void
     */
    private function InsertRegistrationKey(string $email, string $customer_name): void
    {
        $customer_name = strtolower($customer_name);
        $this->token = TokenManager::GenerateToken(30);
        $customer_id = Association::GetByName($customer_name)->GetId();
        $p = $this->conn->prepare("INSERT INTO `registration_key` (`token`, `email`, `association_id`, `role`) VALUES (?,?,?,'president')");
        $p->bind_param("ssd", $this->token, $email, $customer_id);
        $p->execute();
    }

    /**
     * This function add the president role to the created customer
     * @param string $email
     * @param string $customer_name
     * @return void
     */
    private function AddRoleToUser(string $email, string $customer_name): void
    {
        $customer_id = Association::GetByName($customer_name)->GetId();
        $user_id = Users::GetUserbyEmail($email)->GetId();
        $p = $this->conn->prepare("INSERT INTO `role` (`user_id`, `customer_id`, `role`) VALUES (?,?,'president') ");
        $p->bind_param("dd", $user_id, $customer_id);
        $p->execute();
    }

    /**
     * This function finalises customer registration by sending an email to the future president
     * @throws Exception
     */
    private function SendRegistrationEmailToPresident(string $president_email): void
    {
        $email = new MailBase();
        $email->SetBody(GetRegistrationEmail($this->token));

        $email->SetRecipient($president_email);

        $email->SetSubject("Run to create your own association");

        $email->Send();

        return;
    }

    /**
     * @throws Exception
     */
    private function SendWelcomeEmailToPresident(string $president_email): void
    {
        $email = new MailBase();
        $name = UserGet::ByEmail($president_email)->GetName();
        $email->SetRecipient($president_email);

        $email->SetBody(EmailGenerator::generateEmail('associationCreate', ['sign' => ucfirst(strtolower($name)), 'token' => $this->token, 'name' => API::GetUser()->GetName()]));

        $email->Send();

        return;
    }

    #endregion
}
