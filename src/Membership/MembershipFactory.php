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

namespace OAT\Library\Lti1p3Nrps\Membership;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\User\UserIdentityFactory;
use OAT\Library\Lti1p3Core\User\UserIdentityFactoryInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Nrps\Context\Context;
use OAT\Library\Lti1p3Nrps\Member\Member;
use OAT\Library\Lti1p3Nrps\Member\MemberCollection;
use OAT\Library\Lti1p3Nrps\Member\MemberInterface;
use Throwable;

class MembershipFactory implements MembershipFactoryInterface
{
    /** @var UserIdentityFactoryInterface */
    private $userIdentityFactory;

    public function __construct(UserIdentityFactoryInterface $userIdentityFactory = null)
    {
        $this->userIdentityFactory = $userIdentityFactory ?? new UserIdentityFactory();
    }

    /**
     * @throws LtiException
     */
    public function create(array $data): MembershipInterface
    {
        try {
            $context = new Context(
                $data['context']['id'],
                $data['context']['label'] ?? null,
                $data['context']['title'] ?? null
            );

            $memberCollection = new MemberCollection();

            foreach ($data['members'] ?? [] as $memberData) {
                $memberUserIdentity = $this->createMemberUserIdentity($memberData);

                $member = new Member(
                    $memberUserIdentity,
                    $memberData['status'] ?? MemberInterface::STATUS_ACTIVE,
                    $memberData['roles'] ?? [],
                    array_diff_assoc($memberData, $memberUserIdentity->normalize())
                );

                $memberCollection->add($member);
            }

            return new Membership($data['id'], $context, $memberCollection);

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Error during membership creation: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function createMemberUserIdentity(array $memberData): UserIdentityInterface
    {
        return $this->userIdentityFactory->create(
            $memberData['user_id'],
            $memberData['name'] ?? null,
            $memberData['email'] ?? null,
            $memberData['given_name'] ?? null,
            $memberData['family_name'] ?? null,
            $memberData['middle_name'] ?? null,
            $memberData['locale'] ?? null,
            $memberData['picture'] ?? null
        );
    }
}
