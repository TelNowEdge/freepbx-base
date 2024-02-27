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

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator as BaseChoiceValidator;
use function in_array;
use function is_array;

class ChoiceValidator extends BaseChoiceValidator implements ContainerAwareInterface
{
    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (is_array($constraint->service)) {
            if (true === $constraint->nullable) {
                return true;
            }

            if (false === $this->container->has($constraint->service[0])) {
                $this->context
                    ->buildViolation('Unable to find service: {{ service }}')
                    ->setParameter('{{ service }}', $constraint->service[0])
                    ->addViolation();
            }

            $service = $this->container->get($constraint->service[0]);

            $reflector = new ReflectionClass($service);

            if (false === $reflector->hasMethod($constraint->service[1])) {
                $this->context
                    ->buildViolation('Unable to find method: {{ method }}')
                    ->setParameter('{{ method }}', $constraint->service[1])
                    ->addViolation();
            }

            $method = $reflector->getMethod($constraint->service[1]);

            if (false === in_array($value, $method->invoke($service), true)) {
                $this->context->addViolation($constraint->message);
            }

            return true;
        }

        parent::validate($value, $constraint);
    }
}
