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

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use function is_object;

class UniqueIdValidator extends ConstraintValidator implements ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
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

        $reflModel = new ReflectionClass($value);

        if (false === $reflModel->hasMethod(sprintf('get%s', ucfirst($constraint->field)))) {
            $this->context
                ->buildViolation('Unable to find methods: {{ method }}')
                ->setParameter('{{ method }}', $constraint->field)
                ->addViolation();
        }

        $method = $reflector->getMethod($constraint->service[1]);
        $fieldMethod = $reflModel->getMethod(sprintf('get%s', ucfirst($constraint->field)));
        $fieldValue = $fieldMethod->invoke($value);

        if (null === $fieldValue && (bool)$constraint->nullable) {
            return;
        }

        if (is_object($fieldValue)) {
            $fieldValue = $fieldValue->getId();
        }

        try {
            $res = $method->invoke($service, $fieldValue);
        } catch (NoResultException $e) {
            return;
        }

        // Update mode
        if ($value->getId() === $res->getId()) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ item }}', $fieldValue)
            ->atPath($constraint->field)
            ->addViolation();
    }
}
