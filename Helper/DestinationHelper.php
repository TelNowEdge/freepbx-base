<?php

/*
 * Copyright [2016] [TelNowEdge]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace TelNowEdge\FreePBX\Base\Helper;

use FreePBX;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;

class DestinationHelper
{
    private array $destinations = [];

    public function __construct()
    {
        $destinations = FreePBX::Modules()->getDestinations();

        foreach ($destinations as $destination) {
            $category = isset($destination['category']) ? $destination['category'] : $destination['name'];
            $destination['category'] = $category;
            $this->destinations[$category][] = $destination;
        }
    }

    public function getDestinations(): array
    {
        $out = [];

        foreach ($this->destinations as $destinations) {
            foreach ($destinations as $destination) {
                $out[] = $destination['destination'];
            }
        }

        return $out;
    }

    public function getCategories(): array
    {
        return array_keys($this->destinations);
    }

    public function addFake(Destination $destination): static
    {
        if ($this->destinationExists($destination->getDestination())) {
            return $this;
        }

        if (null === $destination->getDestination()) {
            return $this;
        }

        $this->destinations['Error'] = [
            [
                'destination' => $destination->getDestination(),
                'description' => $destination->getDestination(),
                'category' => 'Error',
            ]

        ];

        return $this;
    }

    private function destinationExists(string $t): bool
    {
        foreach ($this->destinations as $category) {
            foreach ($category as $destination) {
                if ($destination['destination'] !== $t) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    public function getRaw(): array
    {
        return $this->destinations;
    }

    public function getDestinationsByCategory($category)
    {
        return $this->destinations[$category];
    }

    public function getFlatDestinationsByCategory($category): array
    {
        if (null === $category) {
            return array();
        }

        return array_map(function (array $x) {
            return $x['destination'];
        }, $this->destinations[$category]);
    }
}
