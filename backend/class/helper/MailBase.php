<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';

include "conf/config.php";


class MailBase
{
    protected PHPMailer $mail;

    public static function CheckSMPTServerStatus(): bool
    {
        $f = fsockopen('smtp host', 25);
        if ($f !== false) {
            $res = fread($f, 1024);
            if (strlen($res) > 0 && strpos($res, '220') === 0) {
                fclose($f);
                return true;
            } else {
                fclose($f);
                return false;
            }
        } else {
            return false;
        }
        
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->mail = new PHPMailer();
        $this->mail->SMTPDebug = 4;                      //Enable verbose debug output
        $this->mail->Debugoutput = function ($str, $level) {
           // file_put_contents('log/logfile.txt', gmdate('Y-m-d H:i:s') . "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
        };
        $this->mail->isSMTP();                                            //Send using SMTP
        $this->mail->Host       = SMTP_HOST;                     //Set the SMTP server to send through
        $this->mail->SMTPAutoTLS = true;
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->SMTPAuth   = true;                                   //Enable SMTP authentication//Enable SMTP authentication
        $this->mail->Username   = SMTP_USER;                     //SMTP username
        $this->mail->Password   = SMTP_PASSWORD;                               //SMTP passwor
        $this->mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $this->mail->setFrom(SMTP_USER, 'Sportify App Notification');


        $this->mail->isHTML(true);                                  //Set email format to HTML
    }

    /**
     * @throws Exception
     */
    public function SetRecipient(string $email): void
    {
        $this->mail->addAddress($email);               //Name is optional
    }

    public function SetSubject(string $subject): void
    {
        $this->mail->Subject = $subject;
    }

    public function SetBody(string $body): void
    {
        $this->mail->Body = $body;
    }

    /**
     * @throws Exception
     */
    public function Send(): void
    {

        $this->mail->send();

        /*$pid = pcntl_fork();

        if ($pid == -1) {
            die('Impossibile creare il processo figlio');
        } else if ($pid) {
            return;
        } else {
            
            exit();
        }*/
    }
}
