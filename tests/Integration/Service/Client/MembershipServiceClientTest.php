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

namespace OAT\Library\Lti1p3Nrps\Tests\Integration\Service\Client;

use Exception;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\ResourceLinkClaim;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Service\Client\LtiServiceClientInterface;
use OAT\Library\Lti1p3Core\Tests\Traits\NetworkTestingTrait;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Service\Client\MembershipServiceClient;
use OAT\Library\Lti1p3Nrps\Service\MembershipServiceInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MembershipServiceClientTest extends TestCase
{
    use NrpsDomainTestingTrait;
    use NetworkTestingTrait;

    /** @var LtiServiceClientInterface|MockObject */
    private $clientMock;

    /** @var MembershipServiceClient */
    private $subject;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(LtiServiceClientInterface::class);

        $this->subject = new MembershipServiceClient($this->clientMock);
    }

    public function testGetContextMembershipFromPayload(): void
    {
        $payload = $this->createMock(LtiMessagePayloadInterface::class);

        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();

        $payload
            ->expects($this->exactly(2))
            ->method('getNrps')
            ->willReturn($claim);

        $this->prepareClientMock(
            $registration,
            $claim->getContextMembershipsUrl(),
            json_encode($membership)
        );

        $result = $this->subject->getContextMembershipFromPayload($registration, $payload);

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetContextMembership(): void
    {
        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();

        $this->prepareClientMock(
            $registration,
            $claim->getContextMembershipsUrl(),
            json_encode($membership)
        );

        $result = $this->subject->getContextMembership($registration, $claim->getContextMembershipsUrl());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetContextMembershipWithRelationLink(): void
    {
        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();

        $this->prepareClientMock(
            $registration,
            $claim->getContextMembershipsUrl(),
            json_encode($membership),
            200,
            [
                'Link' => 'http://example.com/membership;rel="next"'
            ]
        );

        $result = $this->subject->getContextMembership($registration, $claim->getContextMembershipsUrl());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
        $this->assertEquals('http://example.com/membership;rel="next"', $membership->getRelationLink());
        $this->assertTrue($membership->hasNext());
        $this->assertFalse($membership->hasDifferences());
    }

    public function testGetContextMembershipWithRole(): void
    {
        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();
        $role = 'Learner';

        $this->prepareClientMock(
            $registration,
            sprintf('%s?role=%s', $claim->getContextMembershipsUrl(), $role),
            json_encode($membership)
        );

        $result = $this->subject->getContextMembership($registration, $claim->getContextMembershipsUrl(), $role);

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetContextMembershipWithLimit(): void
    {
        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();
        $limit = 99;

        $this->prepareClientMock(
            $registration,
            sprintf('%s?limit=%s', $claim->getContextMembershipsUrl(), $limit),
            json_encode($membership)
        );

        $result = $this->subject->getContextMembership($registration, $claim->getContextMembershipsUrl(), null, $limit);

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetContextMembershipWithRoleAndLimit(): void
    {
        $registration = $this->createTestRegistration();
        $claim = $this->createTestNrpsClaim();
        $membership = $this->createTestMembership();
        $role = 'Learner';
        $limit = 99;

        $this->prepareClientMock(
            $registration,
            sprintf('%s?role=%s&limit=%s', $claim->getContextMembershipsUrl(), $role, $limit),
            json_encode($membership)
        );

        $result = $this->subject->getContextMembership($registration, $claim->getContextMembershipsUrl(), $role, $limit);

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetResourceLinkMembershipFromPayload(): void
    {
        $payload = $this->createMock(LtiMessagePayloadInterface::class);

        $registration = $this->createTestRegistration();
        $nrpsClaim = $this->createTestNrpsClaim();
        $resourceLinkClaim = $this->createTestResourceLinkClaim();
        $membership = $this->createTestMembership();

        $payload
            ->expects($this->exactly(2))
            ->method('getNrps')
            ->willReturn($nrpsClaim);

        $payload
            ->expects($this->exactly(2))
            ->method('getResourceLink')
            ->willReturn($resourceLinkClaim);

        $this->prepareClientMock(
            $registration,
            sprintf(
                '%s?rlid=%s',
                $nrpsClaim->getContextMembershipsUrl(),
                $resourceLinkClaim->getIdentifier()
            ),
            json_encode($membership)
        );

        $result = $this->subject->getResourceLinkMembershipFromPayload($registration, $payload);

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetResourceLinkMembershipWithRoleAndLimit(): void
    {
        $registration = $this->createTestRegistration();
        $nrpsClaim = $this->createTestNrpsClaim();
        $resourceLinkClaim = $this->createTestResourceLinkClaim();
        $membership = $this->createTestMembership();
        $role = 'Learner';
        $limit = 99;

        $this->prepareClientMock(
            $registration,
            sprintf(
                '%s?rlid=%s&role=%s&limit=%s',
                $nrpsClaim->getContextMembershipsUrl(),
                $resourceLinkClaim->getIdentifier(),
                $role,
                $limit
            ),
            json_encode($membership)
        );

        $result = $this->subject->getResourceLinkMembership(
            $registration,
            $nrpsClaim->getContextMembershipsUrl(),
            $resourceLinkClaim->getIdentifier(),
            $role,
            $limit
        );

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());
    }

    public function testGetContextMembershipFromPayloadErrorOnMissingNRPSClaim(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Cannot get context membership from payload: Provided payload does not contain NRPS claim');

        $payload = $this->createMock(LtiMessagePayloadInterface::class);
        $payload
            ->expects($this->once())
            ->method('getNrps')
            ->willReturn(null);

        $this->subject->getContextMembershipFromPayload($this->createTestRegistration(), $payload);
    }

    public function testGetContextMembershipError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Cannot get context membership: custom error');

        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new Exception('custom error'));

        $this->subject->getContextMembership(
            $this->createTestRegistration(),
            $this->createTestNrpsClaim()->getContextMembershipsUrl()
        );
    }

    public function testGetResourceLinkMembershipFromPayloadErrorOnMissingResourceLinkClaim(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Cannot get resource link membership from payload: Provided payload does not contain ResourceLink claim');

        $payload = $this->createMock(LtiMessagePayloadInterface::class);
        $payload
            ->expects($this->once())
            ->method('getResourceLink')
            ->willReturn(null);

        $this->subject->getResourceLinkMembershipFromPayload($this->createTestRegistration(), $payload);
    }

    public function testGetResourceLinkMembershipFromPayloadErrorOnMissingNRPSClaim(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Cannot get resource link membership from payload: Provided payload does not contain NRPS claim');

        $payload = $this->createMock(LtiMessagePayloadInterface::class);
        $payload
            ->expects($this->once())
            ->method('getResourceLink')
            ->willReturn($this->createTestResourceLinkClaim());
        $payload
            ->expects($this->once())
            ->method('getNrps')
            ->willReturn(null);

        $this->subject->getResourceLinkMembershipFromPayload($this->createTestRegistration(), $payload);
    }

    public function testGetResourceLinkMembershipError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Cannot get resource link membership: custom error');

        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willThrowException(new Exception('custom error'));

        $this->subject->getResourceLinkMembership(
            $this->createTestRegistration(),
            $this->createTestNrpsClaim()->getContextMembershipsUrl(),
            $this->createTestResourceLinkClaim()->getIdentifier()
        );
    }

    private function prepareClientMock(
        RegistrationInterface $registration,
        string $url,
        string $content,
        int $statusCode = 200,
        array $headers = []
    ): void {
        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->with(
                $registration,
                'GET',
                $url,
                [
                    'headers' => ['Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP]
                ],
                [
                    MembershipServiceInterface::AUTHORIZATION_SCOPE_MEMBERSHIP
                ]
            )
            ->willReturn($this->createResponse($content, $statusCode, $headers));
    }

    private function createTestResourceLinkClaim(string $identifier = 'resourceLinkIdentifier'): ResourceLinkClaim
    {
        return new ResourceLinkClaim($identifier);
    }
}
