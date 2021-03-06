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

namespace HalimonAlexander\Mailer\Persons;

use RuntimeException;

abstract class AbstractPerson
{
    private string $email;
    private ?string $name;

    function __construct(string $email, string $name = null)
    {
        $this->setEmail($email);
        $this->setName($name);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    private function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email is invalid');
        }

        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        if ($name !== null && empty($name)) {
            $name = null;
        }

        $this->name = $name;
    }
}
