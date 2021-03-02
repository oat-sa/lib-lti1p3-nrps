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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Factory\Context;

use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Factory\Context\ContextFactory;
use OAT\Library\Lti1p3Nrps\Factory\Context\ContextFactoryInterface;
use OAT\Library\Lti1p3Nrps\Model\Context\ContextInterface;
use OAT\Library\Lti1p3Nrps\Tests\Traits\NrpsDomainTestingTrait;
use PHPUnit\Framework\TestCase;

class ContextFactoryTest extends TestCase
{
    use NrpsDomainTestingTrait;

    /** @var ContextFactoryInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new ContextFactory();
    }

    public function testCreateSuccess(): void
    {
        $context = $this->createTestContext();

        $result = $this->subject->create([
            'id' => $context->getIdentifier(),
            'label' => $context->getLabel(),
            'title' => $context->getTitle()
        ]);

        $this->assertInstanceOf(ContextInterface::class, $result);
        $this->assertEquals($context, $result);
    }

    public function testCreateSuccessWithIdStringCasting(): void
    {
        $result = $this->subject->create([
            'id' => 1
        ]);

        $this->assertInstanceOf(ContextInterface::class, $result);
        $this->assertSame('1', $result->getIdentifier());
    }

    public function testCreateError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Error during context creation');

        $this->subject->create([]);
    }
}
