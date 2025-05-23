<?php

/**
 * This class is used to get all events.
 *
 * @author Timo Coupek | JAWS Developers
 * @version 18.01.2025
 * @link /event/get/<id>
 */
class EventGet
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $path;
    protected string $association;
    #endregion
    #region Boot

    public function __construct(string $path)
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
        $this->path = $path;
        $this->association = Client::GetAssociationId();
    }
    #endregion
    #region Init
    /**
     * @throws Exception
     */
    public function Init(): void
    {
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $givenDate = $_GET['given_date'] ?? null;

        if (($this->path != "current") && ($this->path != "bydate") && (!is_numeric($this->path))) {
            if (!$startDate || !$endDate) {
                ProcessManager::EndProcessWithCode("5.2.5.1");
            }

            if (!Dates::ValidateDate($startDate) || !Dates::ValidateDate($endDate)) {
                ProcessManager::EndProcessWithCode("5.2.5.2");
            }
        } else if ($this->path == "bydate") {
            if (!$givenDate) {
                ProcessManager::EndProcessWithCode("5.2.5.3");
            }

            if (!Dates::ValidateDate($givenDate)) {
                ProcessManager::EndProcessWithCode("5.2.5.4");
            }
        }

        if ($this->association == "")
            ProcessManager::EndProcessWithCode("5.2.3.1");

        if (!is_numeric($this->association))
            ProcessManager::EndProcessWithCode("5.2.3.2");

        if (!Association::ExistsById($this->association))
            ProcessManager::EndProcessWithCode("5.2.3.3");

        if (!API::GetUser()->IsAdmin())
            if (!API::GetUser()->IsPartOf($this->association))
                ProcessManager::EndProcessWithCode("5.2.1");

        switch ($this->path) {
            case is_numeric($this->path):
                $events = $this->GetEventsById($this->path, $this->association);
                ProcessManager::EndProcessWithData($events, "5.2.0.6");
                break;
            case "past":
                $events = $this->GetPastEventsForRange($this->association, $startDate, $endDate);
                ProcessManager::EndProcessWithData($events, "5.2.0.1");
                break;

            case "future":
                $events = $this->GetFutureEventsForRange($this->association, $startDate, $endDate);
                ProcessManager::EndProcessWithData($events, "5.2.0.2");
                break;

            case "current":
                $events = $this->GetCurrentEventForAssociation($this->association);
                ProcessManager::EndProcessWithData($events, "5.2.0.3");
                break;

            case "range":
                $events = $this->GetEventsByRange($this->association, $startDate, $endDate);
                ProcessManager::EndProcessWithData($events, "5.2.0.4");

            case "bydate":
                $events = $this->GetEventsByDate($givenDate);
                ProcessManager::EndProcessWithData($events, "5.2.0.5");

            default:
                ProcessManager::EndProcessWithCode("5.2.6");
        }
    }
    #endregion
    #region Private

    /**
     * Get all events within a specific date range for an association
     *
     * @param integer $associationId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function GetEventsByRange(int $associationId, string $startDate, string $endDate): array
    {
        $p = $this->conn->prepare("
        SELECT * 
        FROM `event` 
        WHERE `association_id` = ? AND 
              `start_date` BETWEEN ? AND ? 
        ORDER BY `start_date` ASC
    ");
        $p->bind_param("iss", $associationId, $startDate, $endDate);
        $p->execute();
        $result = $p->get_result();
        return $this->GetEventsByResult($result);
    }


    private function GetEventsByDate(string $givenDate): array
    {
        $p = $this->conn->prepare("
            SELECT * 
            FROM event
            WHERE start_date <= ? AND end_date >= ?;
        ");
        $p->bind_param("ss", $givenDate, $givenDate);
        $p->execute();
        $result = $p->get_result();
        return $this->GetEventsByResult($result);
    }

    private function GetEventsById(int $event_id, int $association_id): array
    {
        $p = $this->conn->prepare("
            SELECT * 
            FROM event
            WHERE id = ? AND association_id = ?;
        ");
        $p->bind_param("dd", $event_id, $association_id);
        $p->execute();
        $result = $p->get_result();
        return $this->GetEventsByResult($result);
    }

    /**
     * Get past events within a specific date range
     */
    private function GetPastEventsForRange(int $associationId, string $startDate, string $endDate): array
    {
        return $this->GetEventsByRange($associationId, $startDate, $endDate);
    }

    /**
     * Get future events within a specific date range
     */
    private function GetFutureEventsForRange(int $associationId, string $startDate, string $endDate): array
    {
        return $this->GetEventsByRange($associationId, $startDate, $endDate);
    }

    /**
     * Get current events for the current month
     */
    private function GetCurrentMonthEvents(int $associationId): array
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        return $this->GetEventsByRange($associationId, $startDate, $endDate);
    }

    private function GetCurrentEventForAssociation(int $associationId): ?stdClass
    {
        $p = $this->conn->prepare("SELECT * FROM `event` WHERE `start_date` <= NOW() AND `end_date` >= NOW() AND `association_id` = ? LIMIT 1");
        $p->bind_param("i", $associationId);
        $p->execute();
        $result = $p->get_result();

        if ($row = $result->fetch_assoc()) {
            $event = new stdClass();
            $event->id = $row['id'];
            $event->name = $row['name'];
            $event->report_template_id = $row['report_template_id'];
            $event->start_date = $row['start_date'];
            $event->end_date = $row['end_date'];
            return $event;
        }

        return null;
    }

    /**
     * @param bool|mysqli_result $result
     * @return array
     */
    public function GetEventsByResult(bool|mysqli_result $result): array
    {
        $events = array();

        while ($row = mysqli_fetch_array($result)) {
            $event = new stdClass();
            $event->id = $row['id'];
            $event->name = $row['name'];
            $event->report_template_id = $row['report_template_id'];
            $event->start_date = $row['start_date'];
            $event->end_date = $row['end_date'];
            $events[] = $event;
        }

        return $events;
    }
    #endregion
}
