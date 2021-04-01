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

namespace OAT\Library\Lti1p3Nrps\Model\Context;

class Context implements ContextInterface
{
    /** @var string */
    private $identifier;

    /** @var string|null */
    private $label;

    /** @var string|null */
    private $title;

    public function __construct(string $identifier, ?string $label = null, ?string $title = null)
    {
        $this->identifier = $identifier;
        $this->label = $label;
        $this->title = $title;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): ContextInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): ContextInterface
    {
        $this->label = $label;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): ContextInterface
    {
        $this->title = $title;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->identifier,
            'label' => $this->label,
            'title' => $this->title,
        ]);
    }
}
