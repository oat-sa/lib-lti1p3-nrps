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
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;

class Member implements MemberInterface
{
    /** @var UserIdentityInterface */
    private $userIdentity;

    /** @var string */
    private $status;

    /** @var string[] */
    private $roles;

    /** @var CollectionInterface|string[] */
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
        $this->properties = (new Collection())->add($properties);
        $this->message = $message;
    }

    public function getUserIdentity(): UserIdentityInterface
    {
        return $this->userIdentity;
    }

    public function setUserIdentity(UserIdentityInterface $userIdentity): MemberInterface
    {
        $this->userIdentity = $userIdentity;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): MemberInterface
    {
        $this->status = $status;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): MemberInterface
    {
        $this->roles = $roles;

        return $this;
    }

    public function getProperties(): CollectionInterface
    {
        return $this->properties;
    }


    public function getMessage(): ?MessageInterface
    {
        return $this->message;
    }

    public function setMessage(?MessageInterface $message): MemberInterface
    {
        $this->message = $message;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $properties = $this->properties->all();

        if (null !== $this->message) {
            $properties = array_merge(
                $properties,
                [
                    'message' => [$this->message->getData()]
                ]
            );
        }

        return array_filter($properties);
    }
}
