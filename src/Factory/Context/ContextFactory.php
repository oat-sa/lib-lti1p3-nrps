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

namespace OAT\Library\Lti1p3Nrps\Factory\Context;

use OAT\Library\Lti1p3Core\Exception\LtiException;
use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Model\Context\Context;
use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;
use Throwable;

class ContextFactory implements ContextFactoryInterface
{
    /**
     * @throws LtiExceptionInterface
     */
    public function create(array $data): ContextInterface
    {
        try {
            return new Context(
                $data['id'],
                $data['label'] ?? null,
                $data['title'] ?? null
            );

        } catch (Throwable $exception) {
            throw new LtiException(
                sprintf('Error during context creation: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }
}
