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

namespace OAT\Library\Lti1p3Nrps\Service\Server;

use Http\Message\ResponseFactory;
use Nyholm\Psr7\Factory\HttplugFactory;
use OAT\Library\Lti1p3Core\Service\Server\Validator\AccessTokenRequestValidator;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializerInterface;
use OAT\Library\Lti1p3Nrps\Service\MembershipServiceInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Builder\MembershipServiceServerBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * @see https://www.imsglobal.org/spec/lti-nrps/v2p0
 */
class MembershipServiceServer implements MembershipServiceInterface
{
    /** @var AccessTokenRequestValidator */
    private $validator;

    /** @var MembershipServiceServerBuilderInterface */
    private $builder;

    /** @var MembershipSerializerInterface */
    private $serializer;

    /** @var ResponseFactory */
    private $factory;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AccessTokenRequestValidator $validator,
        MembershipServiceServerBuilderInterface $builder,
        MembershipSerializerInterface $serializer = null,
        ResponseFactory $factory = null,
        LoggerInterface $logger = null
    ) {
        $this->validator = $validator;
        $this->builder = $builder;
        $this->serializer = $serializer ?? new MembershipSerializer();
        $this->factory = $factory ?? new HttplugFactory();
        $this->logger = $logger ?? new NullLogger();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (strtoupper($request->getMethod()) !== 'GET') {
            $message = 'Not acceptable HTTP method, accepts: GET';
            $this->logger->error($message);

            return $this->factory->createResponse(405, null, [], $message);
        }

        if (false === strpos($request->getHeaderLine('Accept'), static::CONTENT_TYPE_MEMBERSHIP)) {
            $message = sprintf('Not acceptable content type, accepts: %s', static::CONTENT_TYPE_MEMBERSHIP);
            $this->logger->error($message);

            return $this->factory->createResponse(406, null, [], $message);
        }

        $validationResult = $this->validator->validate($request, [static::AUTHORIZATION_SCOPE_MEMBERSHIP]);

        if ($validationResult->hasError()) {
            $this->logger->error($validationResult->getError());

            return $this->factory->createResponse(401, null, [], $validationResult->getError());
        }

        try {
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
                    $rlId,
                    $role,
                    $limit,
                    $offset
                );
            } else {
                $membership = $this->builder->buildContextMembership(
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
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());

            return $this->factory->createResponse(500, null, [], 'Internal membership service error');
        }
    }
}
