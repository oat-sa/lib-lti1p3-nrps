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

namespace OAT\Library\Lti1p3Nrps\Serializer;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Nrps\Factory\Membership\MembershipFactory;
use OAT\Library\Lti1p3Nrps\Factory\Membership\MembershipFactoryInterface;
use OAT\Library\Lti1p3Nrps\Model\Membership\MembershipInterface;

class MembershipSerializer implements MembershipSerializerInterface
{
    /** @var MembershipFactoryInterface */
    private $membershipFactory;

    public function __construct(MembershipFactoryInterface $membershipFactory = null)
    {
        $this->membershipFactory = $membershipFactory ?? new MembershipFactory();
    }

    public function serialize(MembershipInterface $membership): string
    {
        return json_encode($membership);
    }

    public function deserialize(string $data): MembershipInterface
    {
        $decodedData = json_decode($data, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new LtiException(
                sprintf('Error during membership deserialization: %s', json_last_error_msg())
            );
        }

        return $this->membershipFactory->create($decodedData);
    }
}