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

class ContextFactoryTest extends TestCase
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

        $this->assertEquals(
            '{"id":"identifier","context":{"id":"identifier","label":"label","title":"title"},"members":[{"propertyName":"propertyValue","message":{"https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/message_type":"LtiResourceLinkRequest","https:\/\/purl.imsglobal.org\/spec\/lti-bo\/claim\/basicoutcome":{"lis_result_sourcedid":"sourcedId","lis_outcome_service_url":"http:\/\/example.com\/outcome"}}},{"propertyName":"propertyValue","message":{"https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/message_type":"LtiResourceLinkRequest","https:\/\/purl.imsglobal.org\/spec\/lti-bo\/claim\/basicoutcome":{"lis_result_sourcedid":"sourcedId","lis_outcome_service_url":"http:\/\/example.com\/outcome"}}},{"propertyName":"propertyValue","message":{"https:\/\/purl.imsglobal.org\/spec\/lti\/claim\/message_type":"LtiResourceLinkRequest","https:\/\/purl.imsglobal.org\/spec\/lti-bo\/claim\/basicoutcome":{"lis_result_sourcedid":"sourcedId","lis_outcome_service_url":"http:\/\/example.com\/outcome"}}}]}',
            $this->subject->serialize($membership)
        );
    }

    public function testDeserializeSuccess(): void
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
    }

    public function testDeserializeError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Error during membership deserialization: Syntax error');

        $this->subject->deserialize('invalid');
    }
}
