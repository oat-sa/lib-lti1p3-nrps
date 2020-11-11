<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace OAT\Library\Lti1p3Nrps\Model\Member;

use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;

class Member implements MemberInterface
{
    /** @var UserIdentityInterface */
    private $userIdentity;

    /** @var string */
    private $status;

    /** @var string[] */
    private $roles;

    /** @var string[] */
    private $properties;

    /** @var MessageInterface|null */
    private $message;

    public function __construct(
        UserIdentityInterface $userIdentity,
        string $status,
        array $roles,
        array $properties = [],
        MessageInterface $message = null
    ) {
        $this->userIdentity = $userIdentity;
        $this->status = $status;
        $this->roles = $roles;
        $this->properties = $properties;
        $this->message = $message;
    }

    public function getUserIdentity(): UserIdentityInterface
    {
        return $this->userIdentity;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $propertyName, string $default = null): ?string
    {
        return $this->properties[$propertyName] ?? $default;
    }

    public function hasProperty(string $propertyName): bool
    {
        return array_key_exists($propertyName,  $this->properties);
    }

    public function getMessage(): ?MessageInterface
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        $properties = $this->properties;

        if (null !== $this->message) {
            $properties = $properties + ['message' => [$this->message]];
        }

        return array_filter($properties);
    }
}
