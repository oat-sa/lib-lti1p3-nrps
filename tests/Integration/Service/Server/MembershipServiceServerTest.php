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

namespace OAT\Library\Lti1p3Nrps\Tests\Integration\Service\Server;

use Exception;
use OAT\Library\Lti1p3Core\Service\Server\Validator\AccessTokenRequestValidationResult;
use OAT\Library\Lti1p3Core\Service\Server\Validator\AccessTokenRequestValidator;
use OAT\Library\Lti1p3Core\Tests\Resource\Logger\TestLogger;
use OAT\Library\Lti1p3Core\Tests\Traits\NetworkTestingTrait;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;
use OAT\Library\Lti1p3Nrps\Service\MembershipServiceInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Builder\MembershipServiceServerBuilderInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\MembershipServiceServer;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;

class MembershipServiceServerTest extends TestCase
{
    use NrpsDomainTestingTrait;
    use NetworkTestingTrait;

    /** @var AccessTokenRequestValidator|MockObject */
    private $validatorMock;

    /** @var MembershipServiceServerBuilderInterface|MockObject */
    private $builderMock;

    /** @var MembershipSerializer */
    private $serializer;

    /** @var TestLogger */
    private $logger;

    /** @var MembershipServiceServer */
    private $subject;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(AccessTokenRequestValidator::class);
        $this->builderMock = $this->createMock(MembershipServiceServerBuilderInterface::class);
        $this->serializer = new MembershipSerializer();
        $this->logger = new TestLogger();

        $this->subject = new MembershipServiceServer(
            $this->validatorMock,
            $this->builderMock,
            $this->serializer,
            null,
            $this->logger
        );
    }

    public function testContextMembershipRequestHandling(): void
    {
        $registration = $this->createTestRegistration();
        $membership = $this->createTestMembership();

        $request = $this->createServerRequest(
            'GET',
            'http://example.com/membership',
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $this->builderMock
            ->expects($this->once())
            ->method('buildContextMembership')
            ->willReturn($membership);

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $result = $this->serializer->deserialize($response->getBody()->__toString());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());

        $this->assertEmpty($this->logger->getLogs());
    }

    public function testContextMembershipRequestHandlingWithRoleAndLimitAndOffset(): void
    {
        $registration = $this->createTestRegistration();
        $membership = $this->createTestMembership();
        $role = 'Learner';
        $limit = 99;
        $offset = 10;

        $request = $this->createServerRequest(
            'GET',
            sprintf('http://example.com/membership?role=%s&limit=%s&offset=%s', $role, $limit, $offset),
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $this->builderMock
            ->expects($this->once())
            ->method('buildContextMembership')
            ->with($role, $limit, $offset)
            ->willReturn($membership);

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $result = $this->serializer->deserialize($response->getBody()->__toString());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());

        $this->assertEmpty($this->logger->getLogs());
    }

    public function testResourceLinkMembershipRequestHandling(): void
    {
        $registration = $this->createTestRegistration();
        $membership = $this->createTestMembership();
        $resourceIdentifier = 'resourceIdentifier';

        $request = $this->createServerRequest(
            'GET',
            sprintf('http://example.com/membership?rlid=%s', $resourceIdentifier),
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $this->builderMock
            ->expects($this->once())
            ->method('buildResourceLinkMembership')
            ->with($resourceIdentifier)
            ->willReturn($membership);

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $result = $this->serializer->deserialize($response->getBody()->__toString());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());

        $this->assertEmpty($this->logger->getLogs());
    }

    public function testResourceLinkMembershipRequestHandlingWithRoleAndLimit(): void
    {
        $registration = $this->createTestRegistration();
        $membership = $this->createTestMembership();
        $resourceIdentifier = 'resourceIdentifier';
        $role = 'Learner';
        $limit = 99;

        $request = $this->createServerRequest(
            'GET',
            sprintf('http://example.com/membership?rlid=%s&role=%s&limit=%s', $resourceIdentifier, $role, $limit),
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $this->builderMock
            ->expects($this->once())
            ->method('buildResourceLinkMembership')
            ->with($resourceIdentifier, $role, $limit)
            ->willReturn($membership);

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $result = $this->serializer->deserialize($response->getBody()->__toString());

        $this->assertInstanceOf(MembershipInterface::class, $result);
        $this->assertEquals($membership->getIdentifier(), $result->getIdentifier());
        $this->assertEquals($membership->getContext(), $result->getContext());
        $this->assertEquals($membership->getMembers(), $result->getMembers());

        $this->assertEmpty($this->logger->getLogs());
    }

    public function testHttpMethodError(): void
    {
        $request = $this->createServerRequest(
            'POST',
            'http://example.com/membership',
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $this->validatorMock
            ->expects($this->never())
            ->method('validate');

        $this->builderMock
            ->expects($this->never())
            ->method('buildResourceLinkMembership');

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(405, $response->getStatusCode());

        $this->assertTrue($this->logger->hasLog(LogLevel::ERROR, 'Not acceptable HTTP method, accepts: GET'));
    }

    public function testContentTypeError(): void
    {
        $request = $this->createServerRequest(
            'GET',
            'http://example.com/membership',
            [],
            [
                'Accept' => 'invalid'
            ]
        );

        $this->validatorMock
            ->expects($this->never())
            ->method('validate');

        $this->builderMock
            ->expects($this->never())
            ->method('buildResourceLinkMembership');

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(406, $response->getStatusCode());

        $this->assertTrue($this->logger->hasLog(LogLevel::ERROR, 'Not acceptable content type, accepts: application/vnd.ims.lti-nrps.v2.membershipcontainer+json'));
    }

    public function testValidationError(): void
    {
        $registration = $this->createTestRegistration();
        $error = 'validation error';

        $request = $this->createServerRequest(
            'GET',
            'http://example.com/membership',
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration, null, [], $error);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals($error, $response->getBody()->__toString());

        $this->assertTrue($this->logger->hasLog(LogLevel::ERROR, $error));
    }

    public function testBuilderError(): void
    {
        $registration = $this->createTestRegistration();
        $error = 'builder error';

        $request = $this->createServerRequest(
            'GET',
            'http://example.com/membership',
            [],
            [
                'Accept' => MembershipServiceInterface::CONTENT_TYPE_MEMBERSHIP
            ]
        );

        $validationResult = new AccessTokenRequestValidationResult($registration);

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willReturn($validationResult);

        $this->builderMock
            ->expects($this->once())
            ->method('buildContextMembership')
            ->willThrowException(new Exception($error));

        $response = $this->subject->handle($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Internal membership service error', $response->getBody()->__toString());

        $this->assertTrue($this->logger->hasLog(LogLevel::ERROR, $error));
    }
}
