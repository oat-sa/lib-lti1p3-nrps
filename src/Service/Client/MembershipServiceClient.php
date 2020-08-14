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

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Message\Claim\NrpsClaim;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Service\Client\ServiceClient;
use OAT\Library\Lti1p3Core\Service\Client\ServiceClientInterface;
use OAT\Library\Lti1p3Nrps\Membership\MembershipFactory;
use OAT\Library\Lti1p3Nrps\Membership\MembershipFactoryInterface;
use OAT\Library\Lti1p3Nrps\Membership\MembershipInterface;
use RuntimeException;
use Throwable;

/**
 * @see https://www.imsglobal.org/spec/lti-nrps/v2p0
 */
class MembershipServiceClient
{
    public const AUTHORIZATION_SCOPE_MEMBERSHIP = 'https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly';
    public const CONTENT_TYPE_MEMBERSHIP = 'application/vnd.ims.lti-nrps.v2.membershipcontainer+json';

    /** @var ServiceClientInterface */
    private $serviceClient;

    /** @var MembershipFactoryInterface */
    private $membershipFactory;

    public function __construct(
        ServiceClientInterface $serviceClient = null,
        MembershipFactoryInterface $membershipFactory = null
    ) {
        $this->serviceClient = $serviceClient ?? new ServiceClient();
        $this->membershipFactory = $membershipFactory ?? new MembershipFactory();
    }

    /**
     * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#context-membership
     * @throws LtiException
     */
    public function getContextMembership(
        RegistrationInterface $registration,
        NrpsClaim $nrpsClaim,
        string $role = null,
        int $limit = null
    ): MembershipInterface {
        try {
            $response = $this->serviceClient->request(
                $registration,
                'GET',
                $this->buildNrpsEndpointUrl($nrpsClaim, $role, $limit),
                [
                    'headers' => ['Content-Type' => static::CONTENT_TYPE_MEMBERSHIP]
                ],
                [
                    self::AUTHORIZATION_SCOPE_MEMBERSHIP
                ]
            );

            $responseData = json_decode($response->getBody()->__toString(), true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new RuntimeException(sprintf('json_decode error: %s', json_last_error_msg()));
            }

            return $this->membershipFactory->create($responseData);

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Cannot get context membership: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    private function buildNrpsEndpointUrl(NrpsClaim $nrpsClaim, string $role = null, int $limit = null): string
    {
        $url = $nrpsClaim->getContextMembershipsUrl();

        if (null !== $role) {
            $url = sprintf(
                '%s%s%s',
                $url,
                strpos($url, '?') ? '?' : '&',
                sprintf('role=%s', urlencode($role))
            );
        }

        if (null !== $limit) {
            $url = sprintf(
                '%s%s%s',
                $url,
                strpos($url, '?') ? '?' : '&',
                sprintf('limit=%s', $limit)
            );
        }

        return $url;
    }
}