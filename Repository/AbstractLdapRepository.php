<?php

/*
 * Copyright [2018] [TelNowEdge]
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

namespace TelNowEdge\FreePBX\Base\Repository;

use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Ldap;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

abstract class AbstractLdapRepository
{
    protected $connection;

    protected $ampConfManager;

    public function setAmpConfManager(AmpConfManager $ampConfManager)
    {
        $this->ampConfManager = $ampConfManager;

        return $this;
    }

    public function setConnection(Ldap $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    abstract protected function getMapping($ldapField);

    protected function getFqdn()
    {
        return $this->ampConfManager->get('TNE_LDAPDN');
    }

    protected function ldapToArray(Entry $entry)
    {
        $out = array();

        foreach ($entry->getAttributes() as $ldapField => $attribute) {
            $mapping = $this->getMapping($ldapField);

            if (null === $mapping) {
                continue;
            }

            foreach ($mapping as $i => $map) {
                foreach ($map as $model => $attr) {
                    $out[$model][$attr] = $attribute[$i];
                }
            }
        }

        $mappings = $this->getMapping('dn');
        $dnMapping = reset($mappings);
        $out[key($dnMapping)][reset($dnMapping)] = $entry->getDn();

        return $out;
    }

    protected function objectFromArray($fqn, array $array)
    {
        $reflector = new \ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                throw new \Exception(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return $class;
    }

    protected function uncontrolledObjectFromArray($fqdn, array $array)
    {
        $reflector = new \ReflectionClass($fqdn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            call_user_func(array($class, $method), $value);
        }

        return $class;
    }
}
