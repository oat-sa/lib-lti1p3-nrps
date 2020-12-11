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

namespace OAT\Library\Lti1p3Nrps\Factory\Member;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityFactory;
use OAT\Library\Lti1p3Core\User\UserIdentityFactoryInterface;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Nrps\Factory\Group\GroupFactoryInterface;
use OAT\Library\Lti1p3Nrps\Factory\Message\GroupFactory;
use OAT\Library\Lti1p3Nrps\Factory\Message\MessageFactory;
use OAT\Library\Lti1p3Nrps\Factory\Message\MessageFactoryInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupCollection;
use OAT\Library\Lti1p3Nrps\Model\Member\Member;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use Throwable;

class MemberFactory implements MemberFactoryInterface
{
    /** @var UserIdentityFactoryInterface */
    private $userIdentityFactory;

    /** @var MessageFactoryInterface */
    private $messageFactory;

    /** @var GroupFactoryInterface */
    private $groupFactory;

    public function __construct(
        UserIdentityFactoryInterface $userIdentityFactory = null,
        MessageFactoryInterface $messageFactory = null,
        GroupFactoryInterface $groupFactory = null
    ) {
        $this->userIdentityFactory = $userIdentityFactory ?? new UserIdentityFactory();
        $this->messageFactory = $messageFactory ?? new MessageFactory();
        $this->groupFactory = $groupFactory ?? new GroupFactory();
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function create(array $data): MemberInterface
    {
        try {
            $message = null;
            $groupCollection = null;

            if (isset($data['message'])) {
                $message = $this->messageFactory->create(current($data['message']));
            }

            if (isset($data['group_enrollments'])) {
                $groupCollection = new GroupCollection();

                foreach ($data['group_enrollments'] as $enrollment) {
                    $groupCollection->add($this->groupFactory->create($enrollment));
                }
            }

            return new Member(
                $this->createMemberUserIdentity($data),
                $data['status'] ?? MemberInterface::STATUS_ACTIVE,
                $data['roles'] ?? [],
                $data,
                $message,
                $groupCollection
            );

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Error during member creation: %s', $exception->getMessage()),
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
