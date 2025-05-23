<?php

/**
 * This class allows to add a video with a title and description
 *
 * @author Timo Coupek | JAWS Developers
 * @version 222051
 * @link /video/add
 */
class VideoAdd
{
    #region Prop
    protected stdClass $data;
    protected mysqli $conn;
    protected string $association_id;
    protected string $event_id;
    protected string $uploadDirectory = '/mount/sportifyappco/data.sportifyapp.co/';
    protected string $record_date;
    protected stdClass $video_info;
    #endregion

    #region Boot
    public function __construct()
    {
        $this->data = API::GetJsonData();
        $this->conn = API::GetDBConnection();
        $this->association_id = Client::GetAssociationId();
        $this->event_id = Client::GetEventId();
        $this->record_date = $_POST['record_date'];
        $this->video_info = json_decode($_POST['video_info']);
    }
    #endregion

    #region Init
    /**
     * @throws Exception
     */
    public function Init(): void
    {

        //if (($this->data->video->title ?? "") == "")
        //ProcessManager::EndProcessWithCode("6.3.2.1");

        //if (strlen($this->data->video->title) > 255)
        //ProcessManager::EndProcessWithCode("6.3.2.2");

        //if (($this->data->video->description ?? "") == "")
        //ProcessManager::EndProcessWithCode("6.3.2.3");

        if (($this->event_id ?? "") == "")
            ProcessManager::EndProcessWithCode("6.3.3.1");

        if (!is_numeric($this->event_id))
            ProcessManager::EndProcessWithCode("6.3.3.2");

        if (!Event::CheckEvent($this->event_id))
            ProcessManager::EndProcessWithCode("6.3.3.3");

        $association_id = (Event::GetEvent($this->event_id))->association_id;

        $per = API::GetUser()->GetAssociationPermission();

        if (!API::GetUser()->IsAdmin()) {
            if (!key_exists($association_id, $per))
                ProcessManager::EndProcessWithCode("6.3.1");

            if (($per[$association_id] != "president") && ($per[$association_id] != "coach"))
                ProcessManager::EndProcessWithCode("6.3.1");
        }

        $videoFiles = $_FILES['videos'];

        // Cicla attraverso ogni file caricato
        $videoFileTmpName = $videoFiles['tmp_name'];
        $ext = explode(".", $videoFiles['name']);
        $ext = $ext[count($ext) - 1];
        $video_id = $this->InsertVideo(json_decode($_POST['video_info'])->topic, $this->record_date);
        $eventDirectory = $this->uploadDirectory . $this->event_id; // Directory dell'evento

        // Verifica se la directory dell'evento esiste, altrimenti la crea
        if (!is_dir($eventDirectory)) {
            mkdir($eventDirectory);
        }

        // Recupera il file temporaneo e definisce il percorso completo del file
        $videoFileTmpName = $videoFiles['tmp_name'];
        $videoFilePath = $eventDirectory . "/" . $video_id . ".mp4";

        // Sposta il file caricato nella posizione desiderata
        if (!move_uploaded_file($videoFileTmpName, $videoFilePath)) {
            ProcessManager::EndProcessWithCode("6.3.7"); // Errore durante l'upload
        }

        // Se tutto è andato a buon fine
        ProcessManager::EndProcessWithCode("6.3.0");
    }
    #endregion

    #region Private

    /**
     * This function inserts a new video into the database and updates the video URL with the newly created ID.
     *
     * @param string $videoFilePath
     * @return void
     */
    private function InsertVideo(string $topic, string $record_date): int
    {
        // Step 1: Inserimento iniziale con un placeholder per il `video_url`
        $process = $this->conn->prepare("
            INSERT INTO `videos` 
            (`event_id`, `video_url`, `topic`, `created_at`) 
            VALUES (?, '', ?, ?)
        ");
        $process->bind_param("sss", $this->event_id, $topic, $record_date);
        $process->execute();

        // Step 2: Ottieni l'ID della riga appena creata
        $lastInsertId = $this->conn->insert_id;

        // Step 3: Costruisci il valore per `video_url`
        $videoUrl = "https://api.sportifyapp.co/video/view/" . $lastInsertId;

        // Step 4: Aggiorna il campo `video_url` con il valore generato
        $update = $this->conn->prepare("
            UPDATE `videos`
            SET `video_url` = ?
            WHERE `id` = ?
        ");
        $update->bind_param("si", $videoUrl, $lastInsertId);
        $update->execute();

        return $lastInsertId;
    }


    #endregion

    #region Static
    // You can add any static methods here if needed
    #endregion
}
