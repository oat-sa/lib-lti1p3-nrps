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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Model\Member;

use OAT\Library\Lti1p3Core\User\UserIdentityInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class MemberTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var UserIdentityInterface */
    private $userIdentity;

    /** @var MessageInterface */
    private $message;

    /** @var GroupCollectionInterface */
    private $groups;

    /** @var MemberInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->userIdentity = $this->createTestUserIdentity();
        $this->message = $this->createTestMessage();
        $this->groups = $this->createTestGroupCollection();

        $this->subject = $this->createTestMember();
    }

    public function testGetUserIdentity(): void
    {
        $this->assertEquals($this->userIdentity, $this->subject->getUserIdentity());
    }

    public function testGetStatus(): void
    {
        $this->assertEquals(MemberInterface::STATUS_ACTIVE, $this->subject->getStatus());
    }

    public function testGetRoles(): void
    {
        $this->assertEquals(['Learner'], $this->subject->getRoles());
    }

    public function testGetPropertiesWithoutGroups(): void
    {
        $this->assertEquals(
            [
                'status' => 'Active',
                'roles' => ['Learner'],
                'propertyName' => 'propertyValue',
                'user_id' => 'userIdentifier',
                'name' => 'userName',
                'email' => 'userEmail',
                'given_name' => 'userGivenName',
                'family_name' => 'userFamilyName',
                'middle_name' => 'userMiddleName',
                'locale' => 'userLocale',
                'picture' => 'userPicture',
                'message' => [$this->message->getData()],
            ],
            $this->subject->getProperties()
        );
    }

    public function testGetPropertiesWithGroups(): void
    {
        $subject = $this->createTestMember(
            null,
            MemberInterface::STATUS_ACTIVE,
            ['Learner'],
            ['propertyName' => 'propertyValue'],
            $this->message,
            $this->groups
        );

        $this->assertEquals(
            [
                'status' => 'Active',
                'roles' => ['Learner'],
                'propertyName' => 'propertyValue',
                'user_id' => 'userIdentifier',
                'name' => 'userName',
                'email' => 'userEmail',
                'given_name' => 'userGivenName',
                'family_name' => 'userFamilyName',
                'middle_name' => 'userMiddleName',
                'locale' => 'userLocale',
                'picture' => 'userPicture',
                'message' => [$this->message->getData()],
                'group_enrollments' => $this->groups->jsonSerialize()
            ],
            $subject->getProperties()
        );
    }

    public function testGetProperty(): void
    {
        $this->assertEquals('propertyValue', $this->subject->getProperty('propertyName'));
        $this->assertEquals('default', $this->subject->getProperty('invalid', 'default'));
        $this->assertNull($this->subject->getProperty('invalid'));
    }

    public function testHasProperty(): void
    {
        $this->assertTrue($this->subject->hasProperty('propertyName'));
        $this->assertFalse($this->subject->hasProperty('invalid'));
    }

    public function testGetMessage(): void
    {
        $this->assertEquals($this->message, $this->subject->getMessage());
    }

    public function testGetGroupsWithoutGroups(): void
    {
        $this->assertNull($this->subject->getGroups());
    }

    public function testGetGroupsWithGroups(): void
    {
        $subject = $this->createTestMember(
            null,
            MemberInterface::STATUS_ACTIVE,
            ['Learner'],
            ['propertyName' => 'propertyValue'],
            $this->message,
            $this->groups
        );

        $this->assertEquals($this->groups, $subject->getGroups());
    }

    public function testJsonSerializeWithoutGroups()
    {
        $this->assertEquals(
            [
                'propertyName' => 'propertyValue',
                'status' => 'Active',
                'roles' => ['Learner'],
                'user_id' => 'userIdentifier',
                'name' => 'userName',
                'email' => 'userEmail',
                'given_name' => 'userGivenName',
                'family_name' => 'userFamilyName',
                'middle_name' => 'userMiddleName',
                'locale' => 'userLocale',
                'picture' => 'userPicture',
                'message' => [$this->message->getData()],
            ],
            $this->subject->jsonSerialize()
        );
    }

    public function testJsonSerializeWithGroups()
    {
        $subject = $this->createTestMember(
            null,
            MemberInterface::STATUS_ACTIVE,
            ['Learner'],
            ['propertyName' => 'propertyValue'],
            $this->message,
            $this->groups
        );

        $this->assertEquals(
            [
                'propertyName' => 'propertyValue',
                'status' => 'Active',
                'roles' => ['Learner'],
                'user_id' => 'userIdentifier',
                'name' => 'userName',
                'email' => 'userEmail',
                'given_name' => 'userGivenName',
                'family_name' => 'userFamilyName',
                'middle_name' => 'userMiddleName',
                'locale' => 'userLocale',
                'picture' => 'userPicture',
                'message' => [$this->message->getData()],
                'group_enrollments' => $this->groups->jsonSerialize()
            ],
            $subject->jsonSerialize()
        );
    }
}
