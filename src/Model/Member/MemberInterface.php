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

use JsonSerializable;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;

interface MemberInterface extends JsonSerializable
{
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_INACTIVE = 'Inactive';
    public const STATUS_DELETED = 'Deleted';

    public function getUserIdentity(): UserIdentityInterface;

    public function setUserIdentity(UserIdentityInterface $userIdentity): MemberInterface;

    public function getStatus(): string;

    public function setStatus(string $status): MemberInterface;

    public function getRoles(): array;

    public function setRoles(array $roles): MemberInterface;

    public function getProperties(): CollectionInterface;

    public function getMessage(): ?MessageInterface;

    public function setMessage(?MessageInterface $message): MemberInterface;
}
