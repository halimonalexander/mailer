# Mailer
PHPMailer decorator

```php
use HalimonAlexander\MailTemplater\Template;
use HalimonAlexander\Mailer\Mailer;
use HalimonAlexander\Mailer\Persons\Recipient;

$config = [
    'secure' => 'ssl',
    'host' => 'localhost',
    'port' => '25',
    'address' => 'admin@example.com',
    'username' => 'Admin',
    'password' => 'mypassword',
];

$mailer = new Mailer($config);

$recipient = new Recipient('recipient@example.com', 'John Smith');

/**
 * @var Template $template
 */
$template = ...;

$mailer->doSend($recipient, $template);
```