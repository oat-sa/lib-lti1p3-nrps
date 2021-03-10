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

namespace OAT\Library\Lti1p3Nrps\Model\Message;

use OAT\Library\Lti1p3Core\Message\Payload\Claim\MessagePayloadClaimInterface;

class Message implements MessageInterface
{
    /** @var string[] */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): MessageInterface
    {
        $this->data = $data;

        return $this;
    }

    public function getClaim(string $claim)
    {
        if (is_a($claim, MessagePayloadClaimInterface::class, true)) {
            /**  @var MessagePayloadClaimInterface $claim */
            return $claim::denormalize($this->data[$claim::getClaimName()]);
        }

        return $this->data[$claim] ?? null;
    }

    public function hasClaim(string $claim): bool
    {
        return array_key_exists($claim, $this->data);
    }

    public function jsonSerialize(): array
    {
        return array_filter($this->data);
    }
}
