<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Class MailerModel
 *
 * Represents the interface to the SMTP mail server
 */
class MailerModel extends Model
{
    private $fromAddress;
    private $fromName;

    /**
     * MailerModel constructor.
     * @param $fromAddress
     * @param $fromName
     */
    public function __construct($fromAddress, $fromName)
    {
        parent::__construct(array());

        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    /**
     * Send mail using PHPMailer
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    public function send($to, $subject, $message)
    {
        try {
            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Disable debug message output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = env('SMTP_HOST');                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = env('SMTP_USERNAME');                     // SMTP username
            $mail->Password   = env('SMTP_PASSWORD');                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = env('SMTP_PORT', 587);                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($this->fromAddress, $this->fromName);
            $mail->addAddress($to);     // Add a recipient
            $mail->addReplyTo($this->fromAddress, $this->fromName);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Pass to mailer model
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    public static function sendMail($to, $subject, $message)
    {
        try {
            $user = User::where('id', '=', auth()->id())->first();
            if ($user) {
                $ws = WorkSpaceModel::where('id', '=', $user->workspace)->first();
            } else {
                $ws = WorkSpaceModel::where('id', '=', env('TEMP_WORKSPACE', null))->first();
            }

            if (($ws) && ($ws->mailer_useown)) {
                putenv('SMTP_HOST=' . $ws->mailer_host_smtp);
                putenv('SMTP_PORT=' . $ws->mailer_port_smtp);
                putenv('MAILSERV_HOST=' . $ws->mailer_host_imap);
                putenv('MAILSERV_PORT=' . $ws->mailer_port_imap);
                putenv('MAILSERV_INBOXNAME=' . $ws->mailer_inbox);
                putenv('SMTP_FROMADDRESS=' . $ws->mailer_address);
                putenv('MAILSERV_EMAILADDR=' . $ws->mailer_address);
                putenv('SMTP_FROMNAME=' . $ws->mailer_fromname);
                putenv('SMTP_USERNAME=' . $ws->mailer_username);
                putenv('MAILSERV_USERNAME=' . $ws->mailer_username);
                putenv('SMTP_PASSWORD=' . $ws->mailer_password);
                putenv('MAILSERV_PASSWORD=' . $ws->mailer_password);
            }

            $mailer = new self(env('SMTP_FROMADDRESS'), env('SMTP_FROMNAME'));
            return $mailer->send($to, $subject, $message);
        } catch (\Exception $e) {
            return false;
        }
    }
}
