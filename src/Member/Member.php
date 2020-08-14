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

namespace OAT\Library\Lti1p3Nrps\Member;

use OAT\Library\Lti1p3Core\User\UserIdentityInterface;

class Member implements MemberInterface
{
    /** @var UserIdentityInterface */
    private $userIdentity;

    /** @var string|null */
    private $status;

    /** @var string[] */
    private $roles;

    /** @var string[] */
    private $additionalProperties;

    public function __construct(
        UserIdentityInterface $userIdentity,
        string $status = null,
        array $roles = [],
        array $additionalProperties = []
    ) {
        $this->userIdentity = $userIdentity;
        $this->status = $status;
        $this->roles = $roles;
        $this->additionalProperties = $additionalProperties;
    }

    public function getUserIdentity(): UserIdentityInterface
    {
        return $this->userIdentity;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getAdditionalProperties(): array
    {
        return $this->additionalProperties;
    }

    public function getAdditionalProperty(string $propertyName, ?string $default = null): ?string
    {
        return $this->additionalProperties[$propertyName] ?? $default;
    }
}
