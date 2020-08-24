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

namespace OAT\Library\Lti1p3Nrps\Tests\Unit\Factory\Member;

use OAT\Library\Lti1p3Core\Exception\LtiExceptionInterface;
use OAT\Library\Lti1p3Nrps\Factory\Member\MemberFactory;
use OAT\Library\Lti1p3Nrps\Factory\Member\MemberFactoryInterface;
use OAT\Library\Lti1p3Nrps\Model\Member\MemberInterface;
use PHPUnit\Framework\TestCase;

class MemberFactoryTest extends TestCase
{
    /** @var MemberFactoryInterface */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new MemberFactory();
    }

    public function testCreateSuccess(): void
    {
        $data = [
            'user_id' => 'identifier',
            'status' => MemberInterface::STATUS_ACTIVE,
            'roles' => [
                'Learner'
            ],
            'message' => [
                [
                    'claimName' => 'claimValue'
                ]
            ]
        ];

        $result = $this->subject->create($data);

        $this->assertInstanceOf(MemberInterface::class, $result);

        $this->assertEquals('identifier', $result->getUserIdentity()->getIdentifier());
        $this->assertEquals(MemberInterface::STATUS_ACTIVE, $result->getStatus());
        $this->assertEquals(['Learner'], $result->getRoles());
        $this->assertEquals('claimValue', $result->getMessage()->getClaim('claimName'));
    }

    public function testCreateSuccessWithDefaultStatus(): void
    {
        $data = [
            'user_id' => 'identifier',
            'roles' => [
                'Learner'
            ],
            'message' => [
                [
                    'propertyName' => 'propertyValue'
                ]
            ]
        ];

        $result = $this->subject->create($data);

        $this->assertEquals(MemberInterface::STATUS_ACTIVE, $result->getStatus());
    }

    public function testCreateError(): void
    {
        $this->expectException(LtiExceptionInterface::class);
        $this->expectExceptionMessage('Error during member creation: Undefined index: user_id');

        $this->subject->create([]);
    }
}
