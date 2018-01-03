<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator as BaseChoiceValidator;

class ChoiceValidator extends BaseChoiceValidator implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

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
                    ->addViolation()
                    ;
            }

            $service = $this->container->get($consraint->service[0]);

            $reflector = new \ReflectionClass($service);

            if (false === $reflector->hasMethod($consraint->service[1])) {
                $this->context
                    ->buildViolation('Unable to find method: {{ method }}')
                    ->setParameter('{{ method }}', $consraint->service[1])
                    ->addViolation()
                    ;
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
