<?php

namespace TelNowEdge\FreePBX\Base\Helper;

use TelNowEdge\FreePBX\Base\Form\Model\Destination;

class DestinationHelper
{
    private $destinations = array();

    public function __construct()
    {
        $destinations = \FreePBX::Modules()->getDestinations();

        foreach ($destinations as $destination) {
            $category = true === isset($destination['category']) ? $destination['category'] : $destination['name'];
            $this->destinations[$category][] = $destination;
        }
    }

    public function getCategories()
    {
        return array_keys($this->destinations);
    }

    public function getDestinations()
    {
        $out = array();

        foreach ($this->destinations as $destinations) {
            foreach ($destinations as $destination) {
                $out[$destination['description']] = $destination['destination'];
            }
        }

        return $out;
    }

    public function addFake(Destination $destination)
    {
        if (true === $this->destinationExists($destination->getDestination())) {
            return $this;
        }

        $this->destinations['Error'] = array(
            array(
                'destination' => $destination->getDestination(),
                'description' => $destination->getDestination(),
                'category' => 'Error',
            ),
        );

        return $this;
    }

    public function getRaw()
    {
        return $this->destinations;
    }

    public function getDestinationsByCategory($category)
    {
        return $this->destinations[$category];
    }

    public function getFlatDestinationsByCategory($category)
    {
        return array_map(function ($x) {
            return $x['destination'];
        }, $this->destinations[$category]);
    }

    private function destinationExists($t)
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
}
