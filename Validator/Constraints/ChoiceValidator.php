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
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function validate($value, Constraint $consraint)
    {
        if (true === is_array($consraint->service)) {
            if (true === $consraint->nullable) {
                return true;
            }

            if (false === $this->container->has($consraint->service[0])) {
                $this->context
                    ->buildViolation('Unable to find service: {{ service }}')
                    ->setParameter('{{ service }}', $consraint->service[0])
                    ->addViolation();
            }

            $service = $this->container->get($consraint->service[0]);

            $reflector = new ReflectionClass($service);

            if (false === $reflector->hasMethod($consraint->service[1])) {
                $this->context
                    ->buildViolation('Unable to find method: {{ method }}')
                    ->setParameter('{{ method }}', $consraint->service[1])
                    ->addViolation();
            }

            $method = $reflector->getMethod($consraint->service[1]);

            if (false === in_array($value, $method->invoke($service), true)) {
                $this->context->addViolation($consraint->message);
            }

            return true;
        }

        parent::validate($value, $consraint);
    }
}
