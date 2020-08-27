# NRPS Tool - Membership service client

> How to use the [MembershipServiceClient](../src/Service/Client/MembershipServiceClient.php) to perform authenticated NRPS service calls as a tool.

## Table of contents

- [Features](#features)
- [Usage](#usage)

## Features

This library provides a [MembershipServiceClient](../src/Service/Client/MembershipServiceClient.php) (based on the [core service client](https://github.com/oat-sa/lib-lti1p3-core/blob/master/doc/service/service-client.md)) that allow retrieving NRPS memberships exposed by a platform.

- `getContextMembership`: method to retrieve [context membership](https://www.imsglobal.org/spec/lti-nrps/v2p0#context-membership)
- `getResourcLinkMembership`: method to retrieve [resource link membership](https://www.imsglobal.org/spec/lti-nrps/v2p0#resource-link-membership-service)

## Usage

To get a context membership:
```php
<?php

use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Nrps\Service\Client\MembershipServiceClient;

// Related registration
/** @var RegistrationRepositoryInterface $registrationRepository */
$registration = $registrationRepository->find(...);

// Related LTI 1.3 message
/** @var LtiMessageInterface $ltiMessage */
$ltiMessage  = ...;

$membershipServiceClient = new MembershipServiceClient();

$membership = $membershipServiceClient->getContextMembership(
    $registration,           // [required] as the tool, it will call the platform of this registration
    $ltiMessage->getNrps(),  // [required] to the membership service url of the NRPS claim (got at LTI launch)
    'Learner',               // [optional] we can filter members for a role (default: no filter)
    10                       // [optional] and limit the number of presented members (default: no limit)
);

// Membership identifier
echo $membership->getIdentifier();

// Membership context
echo $membership->getContext()->getIdentifier();

// Membership members
foreach ($membership->getMembers() as $member) {
    echo $member->getUserIdentity()->getIdentifier();
}

// Membership analysed relation link (to know if next or differences)
if ($membership->hasNext()) {
    // handle retrieval of the next members
}

if ($membership->hasDifferences()) {
    // handle differences of the members
}
```

To get a resource link membership:
```php
<?php

use OAT\Library\Lti1p3Core\Message\LtiMessageInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Nrps\Service\Client\MembershipServiceClient;

// Related registration
/** @var RegistrationRepositoryInterface $registrationRepository */
$registration = $registrationRepository->find(...);

// Related LTI 1.3 message
/** @var LtiMessageInterface $ltiMessage */
$ltiMessage  = ...;

$membershipServiceClient = new MembershipServiceClient();

$membership = $membershipServiceClient->getResourceLinkMembership(
    $registration,                   // [required] as the tool, it will call the platform of this registration
    $ltiMessage->getNrps(),          // [required] to the membership service url of the NRPS claim (got at LTI launch)
    $ltiMessage->getResourceLink(),  // [required] for the identifier of the ResourceLink claim (got at LTI launch)
    'Learner',                       // [optional] we can filter members for a role (default: no filter)
    10                               // [optional] and limit the number of presented members (default: no limit)
);

// ...
```
