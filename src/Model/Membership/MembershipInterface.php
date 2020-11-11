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

namespace OAT\Library\Lti1p3Nrps\Model\Membership;

use OAT\Library\Lti1p3Nrps\Model\Member\Member;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;
use JsonSerializable;

interface MembershipInterface extends JsonSerializable
{
    public const REL_NEXT = 'next';
    public const REL_DIFFERENCES = 'differences';

    public function getIdentifier(): string;

    /** @return Member[]|MemberCollectionInterface */
    public function getMembers(): MemberCollectionInterface;

    public function getContext(): ContextInterface;

    public function getRelationLink(): ?string;

    public function setRelationLink(?string $relationLink): MembershipInterface;

    public function getRelationLinkUrl(): ?string;

    public function hasNext(): bool;

    public function hasDifferences(): bool;
}
