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

namespace OAT\Library\Lti1p3Nrps\Service\Client;

use InvalidArgumentException;
use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Message\Token\LtiMessageTokenInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Service\Client\ServiceClient;
use OAT\Library\Lti1p3Core\Service\Client\ServiceClientInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializerInterface;
use OAT\Library\Lti1p3Nrps\Service\MembershipServiceInterface;
use Throwable;

/**
 * @see https://www.imsglobal.org/spec/lti-nrps/v2p0
 */
class MembershipServiceClient implements MembershipServiceInterface
{
    /** @var ServiceClientInterface */
    private $client;

    /** @var MembershipSerializerInterface */
    private $serializer;

    public function __construct(
        ServiceClientInterface $client = null,
        MembershipSerializerInterface $serializer = null
    ) {
        $this->client = $client ?? new ServiceClient();
        $this->serializer = $serializer ?? new MembershipSerializer();
    }

    /**
     * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#context-membership
     * @throws LtiException
     */
    public function getContextMembershipFromMessageToken(
        RegistrationInterface $registration,
        LtiMessageTokenInterface $messageToken,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        try {
            if (null === $messageToken->getNrps()) {
                throw new InvalidArgumentException('Provided message does not contain NRPS claim');
            }

            return $this->getMembership(
                $registration,
                $messageToken->getNrps()->getContextMembershipsUrl(),
                null,
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get context membership from message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#context-membership
     * @throws LtiException
     */
    public function getContextMembership(
        RegistrationInterface $registration,
        string $membershipServiceUrl,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        try {
            return $this->getMembership(
                $registration,
                $membershipServiceUrl,
                null,
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get context membership: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#resource-link-membership-service
     * @throws LtiException
     */
    public function getResourceLinkMembershipFromMessageToken(
        RegistrationInterface $registration,
        LtiMessageTokenInterface $messageToken,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        try {
            if (null === $messageToken->getNrps()) {
                throw new InvalidArgumentException('Provided message does not contain NRPS claim');
            }

            return $this->getMembership(
                $registration,
                $messageToken->getNrps()->getContextMembershipsUrl(),
                $messageToken->getResourceLink()->getId(),
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get resource link membership from message: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#resource-link-membership-service
     * @throws LtiException
     */
    public function getResourceLinkMembership(
        RegistrationInterface $registration,
        string $membershipServiceUrl,
        string $resourceLinkIdentifier,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        try {
            return $this->getMembership(
                $registration,
                $membershipServiceUrl,
                $resourceLinkIdentifier,
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get resource link membership: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function getMembership(
        RegistrationInterface $registration,
        string $membershipServiceUrl,
        string $resourceLinkIdentifier = null,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        $response = $this->client->request(
            $registration,
            'GET',
            $this->buildNrpsEndpointUrl($membershipServiceUrl, $resourceLinkIdentifier, $role, $limit),
            [
                'headers' => ['Accept' => static::CONTENT_TYPE_MEMBERSHIP]
            ],
            [
                static::AUTHORIZATION_SCOPE_MEMBERSHIP
            ]
        );

        $membership = $this->serializer->deserialize($response->getBody()->__toString());

        $relationLink = $response->getHeaderLine(static::HEADER_LINK);
        if (!empty($relationLink)) {
            $membership->setRelationLink($relationLink);
        }

        return $membership;
    }

    private function buildNrpsEndpointUrl(
        string $membershipServiceUrl,
        string $resourceLinkIdentifier = null,
        string $role = null,
        int $limit = null
    ): string {
        $parameters = array_filter([
            'rlid' => $resourceLinkIdentifier,
            'role' => $role,
            'limit' => $limit,
        ]);

        if (empty($parameters)) {
            return $membershipServiceUrl;
        }

        return sprintf(
            '%s%s%s',
            $membershipServiceUrl,
            strpos($membershipServiceUrl, '?') ? '&' : '?', http_build_query($parameters)
        );
    }
}
