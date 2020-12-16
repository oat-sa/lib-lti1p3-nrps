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

namespace OAT\Library\Lti1p3Nrps\Tests\Traits;

use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\NrpsClaim;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Tests\Traits\DomainTestingTrait;
use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Nrps\Model\Context\Context;
use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\Group;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupCollection;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\Member;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollection;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\Membership;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\Message;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;

trait NrpsDomainTestingTrait
{
    use DomainTestingTrait;

    private function createTestContext(
        string $identifier = 'identifier',
        string $label = 'label',
        string $title = 'title'
    ): ContextInterface{
        return new Context($identifier, $label, $title);
    }

    private function createTestMessage(array $data = null): MessageInterface
    {
        return new Message($data ?? [
            LtiMessagePayloadInterface::CLAIM_LTI_MESSAGE_TYPE => LtiMessageInterface::LTI_MESSAGE_TYPE_RESOURCE_LINK_REQUEST,
            LtiMessagePayloadInterface::CLAIM_LTI_BASIC_OUTCOME => [
                'lis_result_sourcedid' => 'sourcedId',
                'lis_outcome_service_url' => 'http://example.com/outcome'
            ]
        ]);
    }

    private function createTestGroup(string $identifier = 'identifier'): GroupInterface
    {
        return new Group($identifier);
    }

    private function createTestGroupCollection(array $groups = null): GroupCollectionInterface
    {
        return new GroupCollection($groups ?? [
            $this->createTestGroup('group1'),
            $this->createTestGroup('group2'),
        ]);
    }

    private function createTestMember(
        UserIdentityInterface $userIdentity = null,
        string $status = MemberInterface::STATUS_ACTIVE,
        array $roles = ['Learner'],
        array $properties = ['propertyName' => 'propertyValue'],
        MessageInterface $message = null,
        GroupCollectionInterface $groups = null
    ): MemberInterface {

        $userIdentity = $userIdentity ?? $this->createTestUserIdentity();
        $message = $message ?? $this->createTestMessage();

        $properties = $properties + [
            'status' => $status,
            'roles' => $roles,
            'user_id' => $userIdentity->getIdentifier(),
            'name' => $userIdentity->getName(),
            'email' => $userIdentity->getEmail(),
            'given_name' => $userIdentity->getGivenName(),
            'family_name' => $userIdentity->getFamilyName(),
            'middle_name' => $userIdentity->getMiddleName(),
            'locale' => $userIdentity->getLocale(),
            'picture' => $userIdentity->getPicture(),
            'message' => [$message->getData()],
        ];

        if (null !== $groups) {
            $properties['group_enrollments'] = $groups->jsonSerialize();
        }

        return new Member($userIdentity, $status, $roles, $properties, $message, $groups);
    }

    private function createTestMemberCollection(array $members = null): MemberCollectionInterface
    {
        return new MemberCollection($members ?? [
            $this->createTestMember($this->createTestUserIdentity('member1')),
            $this->createTestMember($this->createTestUserIdentity('member2')),
            $this->createTestMember($this->createTestUserIdentity('member3')),
        ]);
    }

    private function createTestMembership(
        string $identifier = 'identifier',
        ContextInterface $context = null,
        MemberCollectionInterface $memberCollection = null,
        ?string $relationLink = 'http://example.com/membership;rel="next"'
    ): MembershipInterface {
        return new Membership(
            $identifier,
            $context ?? $this->createTestContext(),
            $memberCollection ?? $this->createTestMemberCollection(),
            $relationLink
        );
    }

    private function createTestNrpsClaim(
        string $url = 'http://example.com/membership',
        array $versions = ['1.0', '2.0']
    ): NrpsClaim {
        return new NrpsClaim($url, $versions);
    }
}
