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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Model\Context;

use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var ContextInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createTestContext();
    }

    public function testIdentifier(): void
    {
        $this->assertEquals('identifier', $this->subject->getIdentifier());

        $this->subject->setIdentifier('newIdentifier');

        $this->assertEquals('newIdentifier', $this->subject->getIdentifier());
    }

    public function testLabel(): void
    {
        $this->assertEquals('label', $this->subject->getLabel());

        $this->subject->setLabel('newLabel');

        $this->assertEquals('newLabel', $this->subject->getLabel());
    }

    public function testTitle(): void
    {
        $this->assertEquals('title', $this->subject->getTitle());

        $this->subject->setTitle('newTitle');

        $this->assertEquals('newTitle', $this->subject->getTitle());
    }

    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                'id' => 'identifier',
                'label' => 'label',
                'title' => 'title',
            ],
            $this->subject->jsonSerialize()
        );
    }
}
