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

use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;

class Membership implements MembershipInterface
{
    /** @var string */
    private $identifier;

    /** @var ContextInterface */
    private $context;

    /** @var MemberCollectionInterface */
    private $members;

    /** @var string|null */
    private $relationLink;

    public function __construct(
        string $identifier,
        ContextInterface $context,
        MemberCollectionInterface $members,
        string $relationLink = null
    ) {
        $this->identifier = $identifier;
        $this->context = $context;
        $this->members = $members;
        $this->relationLink = $relationLink;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getContext(): ContextInterface
    {
        return $this->context;
    }

    public function getMembers(): MemberCollectionInterface
    {
        return $this->members;
    }

    public function getRelationLink(): ?string
    {
        return $this->relationLink;
    }

    public function setRelationLink(string $relationLink): MembershipInterface
    {
        $this->relationLink = $relationLink;

        return $this;
    }

    public function hasNext(): bool
    {
        return (bool) strpos($this->relationLink, sprintf('rel="%s"', static::REL_NEXT));
    }

    public function hasDifferences(): bool
    {
        return (bool) strpos($this->relationLink, sprintf('rel="%s"', static::REL_DIFFERENCES));
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->identifier,
            'context' => $this->context,
            'members' => $this->members
        ]);
    }
}
