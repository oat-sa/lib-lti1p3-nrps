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

    public function testGetProperties(): void
    {
        $this->assertEquals(['propertyName' => 'propertyValue'], $this->subject->getProperties());
    }

    public function testGetProperty(): void
    {
        $this->assertEquals('propertyValue', $this->subject->getProperty('propertyName'));
        $this->assertEquals('default', $this->subject->getProperty('invalid', 'default'));
        $this->assertNull($this->subject->getProperty('invalid'));
    }

    public function testGetMessage(): void
    {
        $this->assertEquals($this->message, $this->subject->getMessage());
    }

    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                'propertyName' => 'propertyValue',
                'message' => $this->subject->getMessage(),
            ],
            $this->subject->jsonSerialize()
        );
    }
}
