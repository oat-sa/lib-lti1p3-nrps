# NRPS Platform - Membership service server

> How to use the [MembershipServiceServerRequestHandler](../src/Service/Server/Handler/MembershipServiceServerRequestHandler.php) (with the core [LtiServiceServer](https://github.com/oat-sa/lib-lti1p3-core/blob/master/src/Service/Server/LtiServiceServer.php)) to serve authenticated NRPS service calls as a platform.

## Table of contents

- [Features](#features)
- [Usage](#usage)

## Features

This library provides a [MembershipServiceServerRequestHandler](../src/Service/Server/Handler/MembershipServiceServerRequestHandler.php) ready to be use with the core [LtiServiceServer](https://github.com/oat-sa/lib-lti1p3-core/blob/master/src/Service/Server/LtiServiceServer.php) to handle context and resource link membership requests.

- it accepts a [PSR7 ServerRequestInterface](https://www.php-fig.org/psr/psr-7/#321-psrhttpmessageserverrequestinterface),
- leverages the [required IMS LTI 1.3 service authentication](https://www.imsglobal.org/spec/security/v1p0/#securing_web_services),
- and returns a [PSR7 ResponseInterface](https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface) containing the `membership` representation

## Usage

First, you need to provide a [MembershipServiceServerBuilderInterface](../src/Service/Server/Builder/MembershipServiceServerBuilderInterface.php) implementation, in charge to build memberships on tools context or resource link requests.

```php
<?php

use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Builder\MembershipServiceServerBuilderInterface;

/** @var MembershipServiceServerBuilderInterface $builder */
$builder = new class() implements MembershipServiceServerBuilderInterface 
{
    public function buildContextMembership(
        RegistrationInterface $registration,
        ?string $role = null,
        ?int $limit = null,
        ?int $offset = null
    ): MembershipInterface {
        // Logic for building context membership for a given registration
    }

    public function buildResourceLinkMembership(
        RegistrationInterface $registration,
        string $resourceLinkIdentifier,
        ?string $role = null,
        ?int $limit = null,
        ?int $offset = null
    ): MembershipInterface {
        // Logic for building resource link membership for a given registration and resource link identifier
    }
};
```

Then:
- you can construct the [MembershipServiceServerRequestHandler](../src/Service/Server/Handler/MembershipServiceServerRequestHandler.php) (constructed with your [MembershipServiceServerBuilderInterface](../src/Service/Server/Builder/MembershipServiceServerBuilderInterface.php) implementation)
- to finally expose it to requests using the core [LtiServiceServer](https://github.com/oat-sa/lib-lti1p3-core/blob/master/src/Service/Server/LtiServiceServer.php) (constructed with the [RequestAccessTokenValidator](https://github.com/oat-sa/lib-lti1p3-core/blob/master/src/Security/OAuth2/Validator/RequestAccessTokenValidator.php), from core library)

```php
<?php

use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;
use OAT\Library\Lti1p3Nrps\Service\Server\Builder\MembershipServiceServerBuilderInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Handler\MembershipServiceServerRequestHandler;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = ...

/** @var RegistrationRepositoryInterface $repository */
$repository = ...

/** @var MembershipServiceServerBuilderInterface $builder */
$builder = ...

$validator = new RequestAccessTokenValidator($repository);

$handler = new MembershipServiceServerRequestHandler($builder);

$server = new LtiServiceServer($validator, $handler);

// Generates an authenticated response containing the built membership representation
$response = $server->handle($request);
```