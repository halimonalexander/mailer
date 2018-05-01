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

abstract class Person
{
    private $email;
    private $name;

    function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return $this->name;
    }
}
