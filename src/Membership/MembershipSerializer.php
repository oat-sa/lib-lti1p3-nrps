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

namespace OAT\Library\TenantManagement\Factory;

use OAT\Library\Lti1p3Nrps\Membership\Membership;
use OAT\Library\Lti1p3Nrps\Membership\MembershipInterface;
use OAT\Library\Lti1p3Nrps\Membership\MembershipSerializerInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MembershipSerializer implements MembershipSerializerInterface
{
    /** @var Serializer */
    private $serializer;

    public function __construct()
    {
        $this->serializer = $this->buildSerializer();
    }

    public function serialize(MembershipInterface $membership): string
    {
        return $this->serializer->serialize($membership, 'json');
    }


    public function deserialize(string $data): MembershipInterface
    {
        return $this->serializer->deserialize($data, Membership::class, 'json');
    }

    private function buildSerializer(): Serializer
    {
        return new Serializer(
            [
                new ObjectNormalizer(null, null, null, new PhpDocExtractor()),
                new ArrayDenormalizer()
            ],
            [
                new JsonEncoder()
            ]
        );
    }
}