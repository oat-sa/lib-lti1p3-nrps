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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Model\Message;

use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\BasicOutcomeClaim;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Nrps\Model\Message\MessageInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var MessageInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createTestMessage();
    }

    public function testData(): void
    {
        $this->assertEquals(
            [
                LtiMessagePayloadInterface::CLAIM_LTI_MESSAGE_TYPE => LtiMessageInterface::LTI_MESSAGE_TYPE_RESOURCE_LINK_REQUEST,
                LtiMessagePayloadInterface::CLAIM_LTI_BASIC_OUTCOME => [
                    'lis_result_sourcedid' => 'sourcedId',
                    'lis_outcome_service_url' => 'http://example.com/outcome'
                ]
            ],
            $this->subject->getData()
        );

        $this->subject->setData(['newData']);

        $this->assertEquals(['newData'], $this->subject->getData());
    }

    public function testHasClaim(): void
    {
        $this->assertTrue($this->subject->hasClaim(LtiMessagePayloadInterface::CLAIM_LTI_MESSAGE_TYPE));
        $this->assertTrue($this->subject->hasClaim(LtiMessagePayloadInterface::CLAIM_LTI_BASIC_OUTCOME));
        $this->assertFalse($this->subject->hasClaim('invalid'));
    }

    public function testGetClaim(): void
    {
        $this->assertEquals(
            LtiMessageInterface::LTI_MESSAGE_TYPE_RESOURCE_LINK_REQUEST,
            $this->subject->getClaim(LtiMessagePayloadInterface::CLAIM_LTI_MESSAGE_TYPE)
        );

        $basicOutcomeClaim = $this->subject->getClaim(BasicOutcomeClaim::class);
        $this->assertInstanceOf(BasicOutcomeClaim::class, $basicOutcomeClaim);
        $this->assertEquals('sourcedId', $basicOutcomeClaim->getLisResultSourcedId());
        $this->assertEquals('http://example.com/outcome', $basicOutcomeClaim->getLisOutcomeServiceUrl());

        $this->assertNull($this->subject->getClaim('invalid'));
    }

    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                LtiMessagePayloadInterface::CLAIM_LTI_MESSAGE_TYPE => LtiMessageInterface::LTI_MESSAGE_TYPE_RESOURCE_LINK_REQUEST,
                LtiMessagePayloadInterface::CLAIM_LTI_BASIC_OUTCOME => [
                    'lis_result_sourcedid' => 'sourcedId',
                    'lis_outcome_service_url' => 'http://example.com/outcome'
                ]
            ],
            $this->subject->jsonSerialize()
        );
    }
}
