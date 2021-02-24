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

use ArrayIterator;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Util\Collection\Collection;
use OAT\Library\Lti1p3Core\Util\Collection\CollectionInterface;

class MemberCollection implements MemberCollectionInterface
{
    /** @var CollectionInterface|MemberInterface[] */
    private $members = [];

    public function __construct(iterable $members = [])
    {
        $this->members = new Collection();

        foreach ($members as $member) {
            $this->add($member);
        }
    }

    public function add(MemberInterface $member): MemberCollectionInterface
    {
        $this->members->set($member->getUserIdentity()->getIdentifier(), $member);

        return $this;
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function get(string $identifier): MemberInterface
    {
        if (!$this->has($identifier)) {
            throw new LtiException(sprintf('Member with user_id %s not found', $identifier));
        }

        return $this->members->getMandatory($identifier);
    }

    public function has(string $identifier): bool
    {
        return $this->members->has($identifier);
    }

    public function count(): int
    {
        return $this->members->count();
    }

    /**
     * @return MemberInterface[]|ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return $this->members->getIterator();
    }

    public function jsonSerialize(): array
    {
        return array_values($this->members->all());
    }
}
