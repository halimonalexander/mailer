<?php
/*
 * This file is part of Mailer.
 *
 * (c) Halimon Alexander <vvthanatos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace HalimonAlexander\Mailer;

use HalimonAlexander\Mailer\Persons\Recipient;
use HalimonAlexander\Mailer\Persons\Sender;

use HalimonAlexander\MailTemplater\{
    Exceptions\InvalidMarkup,
    Template
};

use PHPMailer\PHPMailer\{
  PHPMailer,
  Exception as MailSendException
};

use Exception;
use InvalidArgumentException;

class Mailer
{
    /**
     * @var PHPMailer
     */
    private $phpMailer;

    /**
     * @var array
     */
    private $config;
  
    public function __construct($config)
    {
        $this->config = $config;

        $this->phpMailer = new PHPMailer(true);

        $this->setup();
    }
  
    private function setup()
    {
        $this->phpMailer->isSMTP();
        $this->phpMailer->SMTPDebug = 0;
        $this->phpMailer->Debugoutput = 'error_log';
        $this->phpMailer->Mailer = "smtp";

        $this->phpMailer->SMTPAuth = true;

        $this->phpMailer->clearAllRecipients();
        $this->phpMailer->clearAddresses();
        $this->phpMailer->clearCCs();
        $this->phpMailer->clearBCCs();
        $this->phpMailer->clearReplyTos();
        $this->phpMailer->clearAttachments();
        $this->phpMailer->clearCustomHeaders();

        $this->applyConfig();

        $this->phpMailer->CharSet = 'UTF-8';
        $this->phpMailer->WordWrap = 80;
        $this->phpMailer->isHTML(true);
    }
  
    private function applyConfig()
    {
        $this->phpMailer->SMTPSecure = isset($this->config['secure']) ? $this->config['secure'] : 'ssl';
        $this->phpMailer->Host       = isset($this->config['host']) ? $this->config['host'] : 'smtp.zoho.com';
        $this->phpMailer->Port       = isset($this->config['port']) ? $this->config['port'] : 465;

        $this->phpMailer->Username = $this->config['address'];
        $this->phpMailer->Password = $this->config['password'];

        $sender = new Sender($this->config['address'], $this->config['username']);

        $this->phpMailer->setFrom(
            $sender->getEmail(),
            $sender->getName()
        );

        if ( !empty($this->config['replyto_address']) )
            $this->phpMailer->addReplyTo($this->config['replyto_address'], $this->config['replyto_name']);
    }
  
    /**
     * @param Recipient|Recipient[] $recipients
     * @param Template $template
     *
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws InvalidArgumentException
     * @throws InvalidMarkup
     */
    public function doSend($recipients, Template $template)
    {
        if (!is_array($recipients)) {
            if ($recipients instanceof Recipient)
                $recipients = [$recipients];
            else
                throw new InvalidArgumentException('Recipient is invalid');
        }

        foreach ($recipients as $recipient)
            $this->phpMailer->addAddress(
                $recipient->getEmail(),
                $recipient->getName()
            );

        $htmlBody = $template->getHtmlBody();
        if (!empty($htmlBody)) {
            $this->phpMailer->isHTML(true);
            $this->phpMailer->Body = $htmlBody;
        }

        $this->phpMailer->AltBody = $template->getPlaintextBody();
        $this->phpMailer->Subject = $template->getSubject();

        foreach ($template->getAttachments() as $attachment)
            $this->phpMailer->addAttachment($attachment->getPath(), $attachment->getName());

        try{
            $status = $this->phpMailer->send();
        } catch (MailSendException $e) {
            echo $e->errorMessage();
            return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }

        return $status;
    }
}
