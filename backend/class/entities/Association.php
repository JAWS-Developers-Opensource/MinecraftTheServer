<?php

class Association implements JsonSerializable
{

    public function __construct(stdClass $association)
    {
        $this->SetId($association->id);
        $this->SetName($association->name);
        $this->SetCreationDate($association->creation_date);
        $this->SetPresidentId($association->president_id ?? 0);
    }

    protected int $id;

    /**
     * @param int $id
     */
    public function SetId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function GetId(): int
    {
        return $this->id;
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

    protected string $creation_date;

    /**
     * @return string
     */
    public function GetCreationDate(): string
    {
        return $this->creation_date;
    }

    /**
     * @param string $creation_date
     */
    public function SetCreationDate(string $creation_date): void
    {
        $this->creation_date = $creation_date;
    }

    protected int $president_id;

    /**
     * @return int
     */
    public function GetPresidentId(): int
    {
        return $this->president_id;
    }

    /**
     * @param int $president_id
     */
    public function SetPresidentId(int $president_id): void
    {
        $this->president_id = $president_id;
    }

    public function SetAssociation(stdClass $association)
    {
        $this->SetId($association->id);
        $this->SetName($association->name);
        $this->SetCreationDate($association->creation_date);
        $this->SetPresidentId($association->president);
    }



    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }    
    
    
    /**
     * This function returns one customer that on that as the passed id. If there are no results the function returns an
     * empty object
     *
     * @param int $id
     * @return Association
     */
    public static function GetById(int $id): Association | bool
    {
        $p = API::GetDBConnection()->prepare("SELECT * FROM `association` WHERE `association`.`id` = ?;");
        $p->bind_param("d", $id);
        $p->execute();
        $result = $p->get_result();
        return self::GetByResult($result);
    }

        /**
     * This function check if the passed id customer exists
     *
     * @param int $customer_name
     * @return bool true if exists
     */
    public static function ExistsById(int $association_id): bool
    {
        if(self::GetById($association_id))
            return true;

        return false;
    }

    /**
     * This function returns one customer that have the same passed name
     *
     * @param string $name
     * @return Association
     */
    public static function GetByName(string $name): Association
    {
        $name = strtolower($name);
        $p = API::GetDBConnection()->prepare("SELECT * WHERE `association`.`id` = ?");
        $p->bind_param("s", $name);
        $p->execute();
        $result = $p->get_result();
        return Association::GetByResult($result);
    }   
    
    /**
     * This function check if the passed name customer exists
     *
     * @param string $customer_name
     * @return bool true if exists
     */
    public static function ExistsByName(string $association_name): bool
    {
        if(self::GetByName($association_name))
            return true;

        return false;
    }


    /**
     * This function is used to return the object
     *
     * @param mysqli_result|bool $result
     * @return array
     */
    public static function GetAllByResult(mysqli_result|bool $result): array
    {
        while ($row = mysqli_fetch_array($result)) {
            $customer = new stdClass();

            $customer->id = $row['id'];
            $customer->name = $row['name'];
            $customer->creation_date = $row['creation_date'];
            $customer->president_id = $row['president_id'];

            $customers[] = new Association($customer);;
        }

        return $customers ?? array();
    }

        /**
     * This function is used to return the object
     *
     * @param mysqli_result|bool $result
     * @return Association
     */
    public static function GetByResult(mysqli_result|bool $result): Association | bool
    {
        $row = $result->fetch_assoc();

        if($row < 1)
            return false;

        $customer = new stdClass();

        $customer->id = $row['id'];
        $customer->name = $row['name'];
        $customer->creation_date = $row['creation_date'];


        return new Association($customer);
    }
}