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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Model\Group;

use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupCollectionInterface;
use OAT\Library\Lti1p3Nrps\Model\Group\GroupInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class GroupCollectionTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var GroupCollectionInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createTestGroupCollection();
    }

    public function testCount(): void
    {
        $this->assertEquals(2, $this->subject->count());
    }

    public function testHas(): void
    {
        $this->assertTrue($this->subject->has('group1'));
        $this->assertTrue($this->subject->has('group2'));
        $this->assertFalse($this->subject->has('invalid'));
    }

    public function testAdd(): void
    {
        $this->assertEquals(2, $this->subject->count());

        $group = $this->createTestGroup('group3');

        $this->subject->add($group);

        $this->assertEquals(3, $this->subject->count());
        $this->assertTrue($this->subject->has('group3'));
        $this->assertEquals($group, $this->subject->get('group3'));
    }

    public function testGet(): void
    {
        $this->assertEquals('group1', $this->subject->get('group1')->getIdentifier());
        $this->assertEquals('group2', $this->subject->get('group2')->getIdentifier());

        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Group with group_id invalid not found');

        $this->subject->get('invalid');
    }

    public function testIterator(): void
    {
        foreach ($this->subject as $member) {
            $this->assertInstanceOf(GroupInterface::class, $member);
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
