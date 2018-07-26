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

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CompoundUniqueValidator extends ConstraintValidator implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function validate($class, Constraint $constraint)
    {
        $values = $this->getClassValues($class, $constraint);
        if (true === $this->container->has($constraint->service[0])) {
            $service = $this->container->get($constraint->service[0]);
            $reflector = new \ReflectionClass($service);

            if (true === $reflector->hasMethod($constraint->service[1])) {
                $valid = $reflector
                    ->getMethod($constraint->service[1])
                    ->invoke($service, $values)
                    ;

                if (true === $valid) {
                    return;
                }

                $errors = array();
                $i = 0;

                foreach ($values as $key => $val) {
                    ++$i;

                    $errors['{{ '.$i.' }}'] = true === is_object($val) ? sprintf('%s__%s', $key, $val->getId()) : sprintf('%s__%s', $key, $val);
                }

                $constraint->message = sprintf($constraint->message, implode(',', $errors));

                $this->context->addViolation($constraint->message);
            } else {
                $this->context->addViolation(sprintf("%s::%s isn't callable", $constraint->service[0], $constraint->service[1]));
            }
        } else {
            $this->context->addViolation(sprintf('serviceId [%s] is not a known service', $constraint->service[0]));
        }
    }

    public function getClassValues($class, Constraint $constraint)
    {
        $values = array();
        $reflector = new \ReflectionClass($class);

        foreach ($constraint->fields as $field) {
            if (false === $reflector->hasProperty($field)) {
                $this->context->addViolation(sprintf("%s::%s isn't a property", $reflector->name, $field));
            }

            /*
             * If the property is private
             */
            $property = $reflector->getProperty($field);
            $property->setAccessible(true);

            $values[$field] = $property->getValue($class);
        }

        return $values;
    }
}
