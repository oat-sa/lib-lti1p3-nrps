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
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Core\Message\Payload\Claim\NrpsClaim;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Service\Client\LtiServiceClient;
use OAT\Library\Lti1p3Core\Service\Client\LtiServiceClientInterface;
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
    /** @var LtiServiceClientInterface */
    private $client;

    /** @var MembershipSerializerInterface */
    private $serializer;

    public function __construct(
        ?LtiServiceClientInterface $client = null,
        ?MembershipSerializerInterface $serializer = null
    ) {
        $this->client = $client ?? new LtiServiceClient();
        $this->serializer = $serializer ?? new MembershipSerializer();
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function getContextMembershipForPayload(
        RegistrationInterface $registration,
        LtiMessagePayloadInterface $payload,
        ?string $role = null,
        ?int $limit = null
    ): MembershipInterface {
        try {
            $claim = $payload->getNrps();

            if (null === $claim) {
                throw new InvalidArgumentException('Provided payload does not contain NRPS claim');
            }

            return $this->getContextMembershipForClaim(
                $registration,
                $claim,
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get context membership for payload: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function getContextMembershipForClaim(
        RegistrationInterface $registration,
        NrpsClaim $claim,
        ?string $role = null,
        ?int $limit = null
    ): MembershipInterface {
        try {
            return $this->getContextMembership(
                $registration,
                $claim->getContextMembershipsUrl(),
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get context membership for claim: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function getContextMembership(
        RegistrationInterface $registration,
        string $membershipServiceUrl,
        ?string $role = null,
        ?int $limit = null
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
     * @throws LtiExceptionInterface
     */
    public function getResourceLinkMembershipForPayload(
        RegistrationInterface $registration,
        LtiMessagePayloadInterface $payload,
        ?string $role = null,
        ?int $limit = null
    ): MembershipInterface {
        try {
            $resourceLinkClaim = $payload->getResourceLink();
            $nrpsClaim = $payload->getNrps();

            if (null === $resourceLinkClaim) {
                throw new InvalidArgumentException('Provided payload does not contain ResourceLink claim');
            }

            if (null === $nrpsClaim) {
                throw new InvalidArgumentException('Provided payload does not contain NRPS claim');
            }

            return $this->getResourceLinkMembershipForClaim(
                $registration,
                $nrpsClaim,
                $resourceLinkClaim->getIdentifier(),
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get resource link membership for payload: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function getResourceLinkMembershipForClaim(
        RegistrationInterface $registration,
        NrpsClaim $claim,
        string $resourceLinkIdentifier,
        ?string $role = null,
        ?int $limit = null
    ): MembershipInterface {
        try {
            return $this->getResourceLinkMembership(
                $registration,
                $claim->getContextMembershipsUrl(),
                $resourceLinkIdentifier,
                $role,
                $limit
            );
        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get resource link membership for claim: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws LtiExceptionInterface
     */
    public function getResourceLinkMembership(
        RegistrationInterface $registration,
        string $membershipServiceUrl,
        string $resourceLinkIdentifier,
        ?string $role = null,
        ?int $limit = null
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
        ?string $resourceLinkIdentifier = null,
        ?string $role = null,
        ?int $limit = null
    ): MembershipInterface {
        $response = $this->client->request(
            $registration,
            'GET',
            $this->buildNrpsEndpointUrl($membershipServiceUrl, $resourceLinkIdentifier, $role, $limit),
            [
                'headers' => [
                    'Accept' => static::CONTENT_TYPE_MEMBERSHIP,
                ]
            ],
            [
                static::AUTHORIZATION_SCOPE_MEMBERSHIP,
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
        ?string $resourceLinkIdentifier = null,
        ?string $role = null,
        ?int $limit = null
    ): string {
        $parameters = array_filter(
            [
                'rlid' => $resourceLinkIdentifier,
                'role' => $role,
                'limit' => $limit,
            ]
        );

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
