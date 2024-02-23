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
use TelNowEdge\FreePBX\Base\Traits\LdapTrait;

abstract class AbstractLdapRepository
{
    use LdapTrait;

    protected Ldap $connection;

    protected AmpConfManager $ampConfManager;

    public function setAmpConfManager(AmpConfManager $ampConfManager): static
    {
        $this->ampConfManager = $ampConfManager;

        return $this;
    }

    public function setConnection(Ldap $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    protected function getFqdn()
    {
        return $this->ampConfManager->get('TNE_LDAPDN');
    }

    protected function getUserSchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_USER');
    }

    protected function getGroupSchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_GROUP');
    }

    protected function getDirectorySchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_DIRECTORY');
    }

    protected function getPrivateDirectorySchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_PRIVATE_DIRECTORY');
    }

    protected function getShareSchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_SHARE');
    }

    protected function ldapToArray(Entry $entry): array
    {
        $out = [];

        foreach ($entry->getAttributes() as $ldapField => $attribute) {
            $mapping = $this->getMapping($ldapField);

            if (null === $mapping) {
                continue;
            }

            foreach ($mapping as $i => $map) {
                foreach ($map as $model => $attr) {
                    if (1 < \count($attribute)) {
                        $out[$model][$attr] = $attribute;

                        continue;
                    }

                    $out[$model][$attr] = $attribute[$i];
                }
            }
        }

        $mappings = $this->getMapping('dn');
        $dnMapping = reset($mappings);
        $out[key($dnMapping)][reset($dnMapping)] = $entry->getDn();

        return $out;
    }

    abstract protected function getMapping($ldapField);

    /**
     * @param mixed $fqn
     *
     * @throws \ReflectionException
     */
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

    /**
     * @param mixed $fqdn
     *
     * @throws \ReflectionException
     */
    protected function uncontrolledObjectFromArray($fqdn, array $array)
    {
        $reflector = new \ReflectionClass($fqdn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            \call_user_func([$class, $method], $value);
        }

        return $class;
    }
}
