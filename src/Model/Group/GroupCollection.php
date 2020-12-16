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

namespace OAT\Library\Lti1p3Nrps\Model\Group;

use ArrayIterator;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use phpDocumentor\Reflection\Types\Static_;

class GroupCollection implements GroupCollectionInterface
{
    /** @var GroupInterface[] */
    private $groups = [];

    public function __construct(iterable $groups = [])
    {
        foreach ($groups as $group) {
            $this->add($group);
        }
    }

    public function add(GroupInterface $group): GroupCollectionInterface
    {
        $this->groups[$group->getIdentifier()] = $group;

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function get(string $identifier): GroupInterface
    {
        if (!$this->has($identifier)) {
            throw new LtiException(sprintf('Group with group_id %s not found', $identifier));
        }

        return $this->groups[$identifier];
    }

    public function has(string $identifier): bool
    {
        return array_key_exists($identifier, $this->groups);
    }

    public function count(): int
    {
        return $this->getIterator()->count();
    }

    /**
     * @return GroupInterface[]|ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->groups);
    }

    public function jsonSerialize(): array
    {
        $serialization = [];

        foreach ($this->groups as $group) {
            $serialization[]  = ['group_id' => $group->getIdentifier()];
        }

        return $serialization;
    }
}
