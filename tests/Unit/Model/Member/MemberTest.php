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

    /** @var MemberInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->userIdentity = $this->createTestUserIdentity();
        $this->message = $this->createTestMessage();
        $this->subject = $this->createTestMember();
    }

    public function testUserIdentity(): void
    {
        $this->assertEquals($this->userIdentity, $this->subject->getUserIdentity());

        $this->subject->setUserIdentity($this->createTestUserIdentity('newUserIdentifier'));

        $this->assertEquals('newUserIdentifier', $this->subject->getUserIdentity()->getIdentifier());
    }

    public function testStatus(): void
    {
        $this->assertEquals(MemberInterface::STATUS_ACTIVE, $this->subject->getStatus());

        $this->subject->setStatus(MemberInterface::STATUS_DELETED);

        $this->assertEquals(MemberInterface::STATUS_DELETED, $this->subject->getStatus());
    }

    public function testRoles(): void
    {
        $this->assertEquals(['Learner'], $this->subject->getRoles());

        $this->subject->setRoles(['Instructor']);

        $this->assertEquals(['Instructor'], $this->subject->getRoles());
    }

    public function testGetProperties(): void
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
                'message' =>[$this->message->getData()]
            ],
            $this->subject->getProperties()->all()
        );
    }

    public function testGetProperty(): void
    {
        $this->assertEquals('propertyValue', $this->subject->getProperties()->get('propertyName'));
        $this->assertEquals('default', $this->subject->getProperties()->get('invalid', 'default'));
        $this->assertNull($this->subject->getProperties()->get('invalid'));
    }

    public function testHasProperty(): void
    {
        $this->assertTrue($this->subject->getProperties()->has('propertyName'));
        $this->assertFalse($this->subject->getProperties()->has('invalid'));
    }

    public function testMessage(): void
    {
        $this->assertEquals($this->message, $this->subject->getMessage());

        $this->subject->setMessage($this->createTestMessage(['newData']));

        $this->assertEquals(['newData'], $this->subject->getMessage()->getData());
    }

    public function testJsonSerialize()
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
}
