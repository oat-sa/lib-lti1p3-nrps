# NRPS Tool - Membership service client

> How to use the [MembershipServiceClient](../src/Service/Client/MembershipServiceClient.php) to perform authenticated NRPS service calls as a tool.

## Table of contents

- [Features](#features)
- [Usage](#usage)

## Features

This library provides a [MembershipServiceClient](../src/Service/Client/MembershipServiceClient.php) (based on the [core LtiServiceClient](https://github.com/oat-sa/lib-lti1p3-core/blob/master/doc/service/service-client.md)) that allow retrieving NRPS memberships exposed by a platform.

## Usage

### Get a context membership

```php
<?php

use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Nrps\Service\Client\MembershipServiceClient;

// Related registration
/** @var RegistrationRepositoryInterface $registrationRepository */
$registration = $registrationRepository->find(...);

// Related LTI 1.3 message payload
/** @var LtiMessagePayloadInterface $payload */
$payload  = ...;

$membershipServiceClient = new MembershipServiceClient();

$membership = $membershipServiceClient->getContextMembershipForPayload(
    $registration, // [required] as the tool, it will call the platform of this registration
    $payload,      // [required] for the LTI message payload containing the NRPS claim (got at LTI launch)
    'Learner',     // [optional] we can filter members for a role (default: no filter)
    10             // [optional] and limit the number of presented members (default: no limit)
);

// you also can call directly for a received NRPS claim
$membership = $membershipServiceClient->getContextMembershipForClaim(
    $registration,       // [required] as the tool, it will call the platform of this registration
    $payload->getNrps(), // [required] for a NRPS claim (got at LTI launch)
    'Learner',           // [optional] we can filter members for a role (default: no filter)
    10                   // [optional] and limit the number of presented members (default: no limit)
);

// or you also can call directly for an given URL (avoid claim construction)
$membership = $membershipServiceClient->getContextMembership(
    $registration,                     // [required] as the tool, it will call the platform of this registration
    'https://example.com/memberships', // [required] to a given membership service url
    'Learner',                         // [optional] we can filter members for a role (default: no filter)
    10                                 // [optional] and limit the number of presented members (default: no limit)
);

// Membership identifier
echo $membership->getIdentifier();

// Membership context
echo $membership->getContext()->getIdentifier();

// Membership members
foreach ($membership->getMembers() as $member) {
    echo $member->getUserIdentity()->getIdentifier();
}

// Membership analysed relation link (to know presence of next or differences)
echo $membership->getRelationLinkUrl();

if ($membership->hasNext()) {
    // handle retrieval of the next members
}

if ($membership->hasDifferences()) {
    // handle differences of the members
}
```

### Get a resource link membership

```php
<?php

use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Nrps\Service\Client\MembershipServiceClient;

// Related registration
/** @var RegistrationRepositoryInterface $registrationRepository */
$registration = $registrationRepository->find(...);

// Related LTI 1.3 message payload
/** @var LtiMessagePayloadInterface $payload */
$payload  = ...;

$membershipServiceClient = new MembershipServiceClient();

$membership = $membershipServiceClient->getResourceLinkMembershipForPayload(
    $registration, // [required] as the tool, it will call the platform of this registration
    $payload,      // [required] for the LTI message payload containing the NRPS and ResourceLink claims (got at LTI launch)
    'Learner',     // [optional] we can filter members for a role (default: no filter)
    10             // [optional] and limit the number of presented members (default: no limit)
);

// you also can call directly for a received NRPS claim
$membership = $membershipServiceClient->getResourceLinkMembershipForClaim(
    $registration,       // [required] as the tool, it will call the platform of this registration
    $payload->getNrps(), // [required] for a NRPS claim (got at LTI launch)
    'someIdentifier',    // [required] for a resource link identifier
    'Learner',           // [optional] we can filter members for a role (default: no filter)
    10                   // [optional] and limit the number of presented members (default: no limit)
);

// or you also can call directly for an given URL and resource link identifier (avoid claims construction)
$membership = $membershipServiceClient->getResourceLinkMembership(
    $registration,                     // [required] as the tool, it will call the platform of this registration
    'https://example.com/memberships', // [required] to a given membership service url
    'someIdentifier',                  // [required] for a given resource link identifier
    'Learner',                         // [optional] we can filter members for a role (default: no filter)
    10                                 // [optional] and limit the number of presented members (default: no limit)
);

// ...
```
