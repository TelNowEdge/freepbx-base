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

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DependsValidator extends ConstraintValidator implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function validate($obj, Constraint $constraint)
    {
        if (false === $this->container->has($constraint->service[0])) {
            $this->context
                ->buildViolation('Unable to find service: {{ service }}')
                ->setParameter('{{ service }}', $constraint->service[0])
                ->addViolation()
                ;
        }

        $service = $this->container->get($constraint->service[0]);

        $reflector = new \ReflectionClass($service);

        if (false === $reflector->hasMethod($constraint->service[1])) {
            $this->context
                ->buildViolation('Unable to find method: {{ method }}')
                ->setParameter('{{ method }}', $constraint->service[1])
                ->addViolation()
                ;
        }

        $reflModel = new \ReflectionClass($obj);
        if (false === $reflModel->hasMethod(sprintf('get%s', ucfirst($constraint->field)))
        || false === $reflModel->hasMethod(sprintf('get%s', ucfirst($constraint->depends)))
        ) {
            $this->context
                ->buildViolation('Unable to find a least one of this methods: {{ methods }}')
                ->setParameter('{{ methods }}', sprintf('%s or %s', $constraint->field, $constraint->depends))
                ->addViolation()
                ;
        }

        $method = $reflector->getMethod($constraint->service[1]);
        $dependsMethod = $reflModel->getMethod(sprintf('get%s', ucfirst($constraint->depends)));
        $fieldMethod = $reflModel->getMethod(sprintf('get%s', ucfirst($constraint->field)));

        $field = $fieldMethod->invoke($obj);

        if (true === is_object($field)) {
            $field = $field->getId();
        }

        if (false === in_array($field, $method->invoke($service, $dependsMethod->invoke($obj)), true)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->field)
                ->addViolation()
                ;
        }

        return true;
    }
}
