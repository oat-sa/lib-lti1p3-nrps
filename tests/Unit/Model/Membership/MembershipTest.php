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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Model\Membership;

use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class MembershipTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var MembershipInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createTestMembership();
    }

    public function testGetIdentifier(): void
    {
        $this->assertEquals('identifier', $this->subject->getIdentifier());
    }

    public function testGetContext(): void
    {
        $this->assertEquals('identifier', $this->subject->getContext()->getIdentifier());
        $this->assertEquals('label', $this->subject->getContext()->getLabel());
        $this->assertEquals('title', $this->subject->getContext()->getTitle());
    }

    public function testGetMembers(): void
    {
        $this->assertEquals(3, $this->subject->getMembers()->count());

        foreach ($this->subject->getMembers() as $member) {
            $this->assertInstanceOf(MemberInterface::class, $member);
        }

        $this->assertTrue($this->subject->getMembers()->has('member1'));
        $this->assertTrue($this->subject->getMembers()->has('member2'));
        $this->assertTrue($this->subject->getMembers()->has('member3'));
    }

    public function testGetRelationLink(): void
    {
        $this->assertEquals('http://example.com/membership;rel=next', $this->subject->getRelationLink());
    }

    public function testSetRelationLink(): void
    {
        $this->assertEquals('http://example.com/membership;rel=next', $this->subject->getRelationLink());

        $this->subject->setRelationLink('http://other.com/membership;rel=next');

        $this->assertEquals('http://other.com/membership;rel=next', $this->subject->getRelationLink());
    }

    public function testRelation(): void
    {
        $this->assertTrue($this->subject->hasNext());
        $this->assertFalse($this->subject->hasDifferences());

        $this->subject->setRelationLink('http://example.com/membership;rel=differences');

        $this->assertFalse($this->subject->hasNext());
        $this->assertTrue($this->subject->hasDifferences());
    }

    public function testEmptyRelation(): void
    {
        $subject = $this->createTestMembership('identifier', null, null, null);

        $this->assertFalse($subject->hasNext());
        $this->assertFalse($subject->hasDifferences());
    }

    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                'id' => $this->subject->getIdentifier(),
                'context' => $this->subject->getContext(),
                'members' => $this->subject->getMembers(),
            ],
            $this->subject->jsonSerialize()
        );
    }
}
