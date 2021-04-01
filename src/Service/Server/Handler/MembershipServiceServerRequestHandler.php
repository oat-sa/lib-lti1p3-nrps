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

namespace OAT\Library\Lti1p3Nrps\Service\Server\Handler;

use Http\Message\ResponseFactory;
use Nyholm\Psr7\Factory\HttplugFactory;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Service\Server\Handler\LtiServiceServerRequestHandlerInterface;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializerInterface;
use OAT\Library\Lti1p3Nrps\Service\MembershipServiceInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Builder\MembershipServiceServerBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @see https://www.imsglobal.org/spec/lti-nrps/v2p0
 */
class MembershipServiceServerRequestHandler implements LtiServiceServerRequestHandlerInterface, MembershipServiceInterface
{
    /** @var MembershipServiceServerBuilderInterface */
    private $builder;

    /** @var MembershipSerializerInterface */
    private $serializer;

    /** @var ResponseFactory */
    private $factory;

    public function __construct(
        MembershipServiceServerBuilderInterface $builder,
        ?MembershipSerializerInterface $serializer = null,
        ?ResponseFactory $factory = null
    ) {
        $this->builder = $builder;
        $this->serializer = $serializer ?? new MembershipSerializer();
        $this->factory = $factory ?? new HttplugFactory();
    }

    public function getServiceName(): string
    {
        return static::NAME;
    }

    public function getAllowedContentType(): ?string
    {
        return static::CONTENT_TYPE_MEMBERSHIP;
    }

    public function getAllowedMethods(): array
    {
        return [
            'GET',
        ];
    }

    public function getAllowedScopes(): array
    {
        return [
            static::AUTHORIZATION_SCOPE_MEMBERSHIP,
        ];
    }

    public function handleServiceRequest(
        RegistrationInterface $registration,
        ServerRequestInterface $request
    ): ResponseInterface {
        parse_str($request->getUri()->getQuery(), $parameters);

        $rlId = $parameters['rlid'] ?? null;
        $role = $parameters['role'] ?? null;
        $limit = array_key_exists('limit', $parameters)
            ? intval($parameters['limit'])
            : null;
        $offset = array_key_exists('offset', $parameters)
            ? intval($parameters['offset'])
            : null;

        if (null !== $rlId) {
            $membership = $this->builder->buildResourceLinkMembership(
                $registration,
                $rlId,
                $role,
                $limit,
                $offset
            );
        } else {
            $membership = $this->builder->buildContextMembership(
                $registration,
                $role,
                $limit,
                $offset
            );
        }

        $responseBody = $this->serializer->serialize($membership);
        $responseHeaders = [
            'Content-Type' => static::CONTENT_TYPE_MEMBERSHIP,
            'Content-Length' => strlen($responseBody),
        ];

        if (null !== $membership->getRelationLink()) {
            $responseHeaders['Link'] = $membership->getRelationLink();
        }

        return $this->factory->createResponse(200, null, $responseHeaders, $responseBody);
    }
}
