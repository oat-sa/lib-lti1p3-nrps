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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Serializer;

use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializerInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class MembershipSerializerTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var MembershipSerializerInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new MembershipSerializer();
    }

    public function testSerialize(): void
    {
        $membership = $this->createTestMembership();

        $this->assertEquals(json_encode($membership), $this->subject->serialize($membership));
    }

    public function testDeserializeSuccessWithoutGroups(): void
    {
        $data = [
            'id' => 'identifier',
            'context' => [
                'id' => 'contextIdentifier'
            ],
            'members' => [
                [
                    'user_id' => 'userIdentifier',
                    'roles' => ['Learner']
                ]
            ]
        ];

        $result = $this->subject->deserialize(json_encode($data));

        $this->assertInstanceOf(MembershipInterface::class, $result);

        $this->assertEquals('identifier', $result->getIdentifier());
        $this->assertEquals('contextIdentifier', $result->getContext()->getIdentifier());
        $this->assertEquals(
            'userIdentifier',
            $result->getMembers()->get('userIdentifier')->getUserIdentity()->getIdentifier()
        );
        $this->assertEquals(['Learner'], $result->getMembers()->get('userIdentifier')->getRoles());
        $this->assertNull($result->getMembers()->get('userIdentifier')->getGroups());
    }

    public function testDeserializeSuccessWithGroups(): void
    {
        $data = [
            'id' => 'identifier',
            'context' => [
                'id' => 'contextIdentifier'
            ],
            'members' => [
                [
                    'user_id' => 'userIdentifier',
                    'roles' => ['Learner'],
                    'group_enrollments' => [
                        ['group_id' => 'group1'],
                        ['group_id' => 'group2'],
                    ]
                ]
            ]
        ];

        $result = $this->subject->deserialize(json_encode($data));

        $this->assertInstanceOf(MembershipInterface::class, $result);

        $this->assertEquals('identifier', $result->getIdentifier());
        $this->assertEquals('contextIdentifier', $result->getContext()->getIdentifier());
        $this->assertEquals(
            'userIdentifier',
            $result->getMembers()->get('userIdentifier')->getUserIdentity()->getIdentifier()
        );
        $this->assertEquals(['Learner'], $result->getMembers()->get('userIdentifier')->getRoles());
        $this->assertTrue($result->getMembers()->get('userIdentifier')->getGroups()->has('group1'));
        $this->assertTrue($result->getMembers()->get('userIdentifier')->getGroups()->has('group2'));
    }

    public function testDeserializeError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Error during membership deserialization: Syntax error');

        $this->subject->deserialize('invalid');
    }
}
