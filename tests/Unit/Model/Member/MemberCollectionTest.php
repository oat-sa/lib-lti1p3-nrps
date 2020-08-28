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

use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class MemberCollectionTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var MemberCollectionInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createTestMemberCollection();
    }

    public function testCount(): void
    {
        $this->assertEquals(3, $this->subject->count());
    }

    public function testHas(): void
    {
        $this->assertTrue($this->subject->has('member1'));
        $this->assertTrue($this->subject->has('member2'));
        $this->assertTrue($this->subject->has('member3'));

        $this->assertFalse($this->subject->has('invalid'));
    }

    public function testAdd(): void
    {
        $this->assertEquals(3, $this->subject->count());

        $member = $this->createTestMember($this->createTestUserIdentity('member4'));

        $this->subject->add($member);

        $this->assertEquals(4, $this->subject->count());
        $this->assertTrue($this->subject->has('member4'));
        $this->assertEquals($member, $this->subject->get('member4'));
    }

    public function testGet(): void
    {
        $this->assertEquals('member1', $this->subject->get('member1')->getUserIdentity()->getIdentifier());
        $this->assertEquals('member2', $this->subject->get('member2')->getUserIdentity()->getIdentifier());
        $this->assertEquals('member3', $this->subject->get('member3')->getUserIdentity()->getIdentifier());

        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Member with user_id invalid not found');

        $this->subject->get('invalid');
    }

    public function testIterator(): void
    {
        foreach ($this->subject as $member) {
            $this->assertInstanceOf(MemberInterface::class, $member);
        }
    }

    public function testJsonSerialize(): void
    {
        $this->assertEquals(
            array_values($this->subject->getIterator()->getArrayCopy()),
            $this->subject->jsonSerialize()
        );
    }
}
