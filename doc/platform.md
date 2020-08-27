# NRPS Platform - Membership service server

> How to use the [MembershipServiceServer](../src/Service/Server/MembershipServiceServer.php) to serve authenticated NRPS service calls as a platform.

## Table of contents

- [Features](#features)
- [Usage](#usage)

## Features

This library provides a [MembershipServiceServer](../src/Service/Server/MembershipServiceServer.php) ready to handle context and resource link membership requests.

- it accepts a [PSR7 ServerRequestInterface](https://www.php-fig.org/psr/psr-7/#321-psrhttpmessageserverrequestinterface),
- leverages the [required IMS LTI 1.3 service authentication](https://www.imsglobal.org/spec/security/v1p0/#securing_web_services),
- and returns a [PSR7 ResponseInterface](https://www.php-fig.org/psr/psr-7/#33-psrhttpmessageresponseinterface) containing the `membership` representation

## Usage

First, you need to provide a [MembershipServiceServerBuilderInterface](../src/Service/Server/Builder/MembershipServiceServerBuilderInterface.php) implementation, in charge to build memberships on tools context or resource link requests.

```php
<?php

use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Nrps\Service\Server\Repository\MembershipServiceServerBuilderInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @var MembershipServiceServerBuilderInterface $builder */
$builder = new class() implements MembershipServiceServerBuilderInterface 
{
    public function buildContextMembership(
        RegistrationInterface $registration,
        ServerRequestInterface $request,
        string $role = null,
        string $limit = null
    ): MembershipInterface {
        // Logic for building context membership for a given registration and request
    }

    public function buildResourceLinkMembership(
        RegistrationInterface $registration,
        ServerRequestInterface $request,
        string $resourceLinkIdentifier,
        string $role = null,
        string $limit = null
    ): MembershipInterface {
        // Logic for building resource link membership for a given registration, request and resource link
    }
}
```

You can then construct the [MembershipServiceServer](../src/Service/Server/MembershipServiceServer.php) with:
- the [AccessTokenRequestValidator](https://github.com/oat-sa/lib-lti1p3-core/blob/master/src/Service/Server/Validator/AccessTokenRequestValidator.php) (from lti1p3-core)
- your [MembershipServiceServerBuilderInterface](../src/Service/Server/Builder/MembershipServiceServerBuilderInterface.php) implementation

To finally expose it to requests:
```php
<?php

use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Service\Server\Validator\AccessTokenRequestValidator;
use OAT\Library\Lti1p3Nrps\Service\Server\MembershipServiceServer;
use Psr\Http\Message\ServerRequestInterface;

/** @var RegistrationRepositoryInterface $repository */
$repository = ...

$validator = new AccessTokenRequestValidator($repository);

$membershipServiceServer = new MembershipServiceServer($validator, $builder);

/** @var ServerRequestInterface $request */
$request = ...

// Generates a response containing the built membership representation
$response = $membershipServiceServer->handle($request);
```