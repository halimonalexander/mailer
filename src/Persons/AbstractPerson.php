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

abstract class AbstractPerson
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @param string $email
     * @param string|null $name
     */
    function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
