<?php

namespace TelNowEdge\FreePBX\Base\Helper;

use TelNowEdge\FreePBX\Base\Form\Model\Destination;

class DestinationHelper
{
    private $destinations = array();

    public function __construct()
    {
        global $active_modules;

        $activeModules = $active_modules;

        foreach ($activeModules as $name => $module) {
            $function = sprintf('%s_destinations', $name);

            if (false === function_exists($function)) {
                continue;
            }

            $destinations = call_user_func($function, 0);

            if (false === is_array($destinations)) {
                $destinations = array();
                array_push($destinations, array(
                    'destination' => '',
                    'description' => _('No yet available destination'),
                ));
            }

            foreach ($destinations as $destination) {
                $category = true === isset($destination['category'])
                    ? $destination['category']
                    : $module['displayname']
                    ;

                $category = $this->sanitizeCategory($category);

                $this->destinations[$category][] = $destination;
            }
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

    private function sanitizeCategory($category)
    {
        $category = str_replace('|', '', $category);
        $category = str_replace('&', _('and'), $category);

        return $category;
    }
}
