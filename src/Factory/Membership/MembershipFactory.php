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

namespace OAT\Library\Lti1p3Nrps\Factory\Membership;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Nrps\Factory\Context\ContextFactory;
use OAT\Library\Lti1p3Nrps\Factory\Context\ContextFactoryInterface;
use OAT\Library\Lti1p3Nrps\Factory\Member\MemberFactory;
use OAT\Library\Lti1p3Nrps\Factory\Member\MemberFactoryInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollection;
use OAT\Library\Lti1p3Nrps\Model\Membership\Membership;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use Throwable;

class MembershipFactory implements MembershipFactoryInterface
{
    /** @var ContextFactoryInterface */
    private $contextFactory;

    /** @var MemberFactoryInterface */
    private $memberFactory;

    public function __construct(
        ContextFactoryInterface $contextFactory = null,
        MemberFactoryInterface $memberFactory = null
    ) {
        $this->contextFactory = $contextFactory ?? new ContextFactory();
        $this->memberFactory = $memberFactory ?? new MemberFactory();
    }

    /**
     * @throws LtiException
     */
    public function create(array $data, string $relationLink = null): MembershipInterface
    {
        try {
            $memberCollection = new MemberCollection();

            foreach ($data['members'] ?? [] as $memberData) {
                $memberCollection->add($this->memberFactory->create($memberData));
            }

            $membership = new Membership(
                $data['id'],
                $this->contextFactory->create($data['context']),
                $memberCollection
            );

            return $membership->setRelationLink($relationLink);

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Error during membership creation: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }
}
