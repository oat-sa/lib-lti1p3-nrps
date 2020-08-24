<?php


use OAT\Library\Lti1p3Nrps\Factory\Membership\MembershipFactory;
use OAT\Library\Lti1p3Nrps\Serializer\MembershipSerializer;

require_once __DIR__ . '/vendor/autoload.php';

$factory = new MembershipFactory();
$serializer = new MembershipSerializer();

$data = '{
"id" : "https://lms.example.com/sections/2923/memberships",
"context": {
  "id": "2923-abc",
  "label": "CPS 435",
  "title": "CPS 435 Learning Analytics"
},
"members" : [
  {
    "status" : "Active",
    "name": "Jane Q. Public",
    "picture" : "https://platform.example.edu/jane.jpg",
    "given_name" : "Jane",
    "family_name" : "Doe",
    "middle_name" : "Marie",
    "email": "jane@platform.example.edu",
    "user_id" : "0ae836b9-7fc9-4060-006f-27b2066ac545",
    "lis_person_sourcedid": "59254-6782-12ab",
    "roles": [
      "http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor"
    ]
  }
]
}';

$memberShip = $serializer->deserialize($data);

var_export($memberShip->getRelationLink());

echo $serializer->serialize($memberShip);