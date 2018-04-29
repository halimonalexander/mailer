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

    function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
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
