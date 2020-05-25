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

use HalimonAlexander\MailTemplater\{
    Template
};

use PHPMailer\PHPMailer\{
  PHPMailer,
  Exception as MailSendException
};

use Exception;
use RuntimeException;
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

        $this->setup();
    }
  
    private function setup()
    {
        $this->phpMailer = new PHPMailer(true);
        $this->phpMailer->Debugoutput = 'error_log';

        $this->setupSMTP();
        $this->clearPhpMailer();
        $this->setCredentials();

        $this->phpMailer->CharSet = 'UTF-8';
        $this->phpMailer->WordWrap = 80;
    }

    /**
     * @return void
     *
     * @throws RuntimeException
     */
    private function setupSMTP(): void
    {
        if (
            !array_key_exists('secure', $this->config) ||
            !array_key_exists('host', $this->config) ||
            !array_key_exists('port', $this->config)
        ) {
            throw new RuntimeException('SMTP config is not set');
        }

        $this->phpMailer->isSMTP();
        $this->phpMailer->SMTPDebug = 0;

        $this->phpMailer->SMTPSecure = $this->config['secure'];
        $this->phpMailer->Host       = $this->config['host'];
        $this->phpMailer->Port       = $this->config['port'];
    }

    private function clearPhpMailer(): void
    {
        $this->phpMailer->clearAllRecipients();
        $this->phpMailer->clearAddresses();
        $this->phpMailer->clearCCs();
        $this->phpMailer->clearBCCs();
        $this->phpMailer->clearReplyTos();
        $this->phpMailer->clearAttachments();
        $this->phpMailer->clearCustomHeaders();
    }

    /**
     * @return void
     *
     * @throws RuntimeException
     */
    private function setCredentials(): void
    {
        $this->phpMailer->SMTPAuth = true;

        if (
            !array_key_exists('address', $this->config) ||
            !array_key_exists('password', $this->config)
        ) {
            throw new RuntimeException('SMTP credentials are not provided');
        }

        $this->phpMailer->Username   = $this->config['address'];
        $this->phpMailer->Password   = $this->config['password'];

        if (array_key_exists('address', $this->config)) {
            try {
                $this->phpMailer->setFrom(
                    $this->config['address'],
                    $this->config['username']
                );
            } catch (MailSendException $exception) {
                throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
            }
        }

        if (array_key_exists('replyto_address', $this->config)) {
            try {
                $this->phpMailer->addReplyTo($this->config['replyto_address'], $this->config['replyto_name']);
            } catch (MailSendException $exception) {
                throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
            }
        }
    }
  
    /**
     * @param Recipient|Recipient[] $recipients
     * @param Template $template
     *
     * @return bool
     */
    public function doSend($recipients, Template $template)
    {
        $this->setRecipients($recipients);
        $this->setSubject($template);
        $this->setBody($template);
        $this->setAttachments($template);

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

    private function setRecipients($recipients): void
    {
        if (!is_array($recipients)) {
            if (!($recipients instanceof Recipient)) {
                throw new InvalidArgumentException('Recipient is invalid');
            }

            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            $this->phpMailer->addAddress(
                $recipient->getEmail(),
                $recipient->getName()
            );
        }
    }

    private function setSubject(Template $template): void
    {
        $this->phpMailer->Subject = $template->getSubject();
    }

    private function setBody(Template $template): void
    {
        $htmlBody = $template->getHtmlBody();
        if (!empty($htmlBody)) {
            $this->phpMailer->isHTML(true);
            $this->phpMailer->Body = $htmlBody;
        } else {
            $this->phpMailer->isHTML(false);
        }

        $this->phpMailer->AltBody = $template->getPlaintextBody();
    }

    /**
     * @param Template $template
     *
     * @return void
     *
     * @throws RuntimeException
     */
    private function setAttachments(Template $template): void
    {
        foreach ($template->getAttachments() as $attachment) {
            try {
                $this->phpMailer->addAttachment(
                    $attachment->getPath(),
                    $attachment->getName()
                );
            } catch (MailSendException $exception) {
                throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
            }
        }
    }
}
