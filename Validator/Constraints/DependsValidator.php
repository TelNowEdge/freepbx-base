<?php

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

    public function validate($obj, Constraint $consraint)
    {
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

        $reflModel = new \ReflectionClass($obj);
        if (false === $reflModel->hasMethod(sprintf('get%s', ucfirst($consraint->field)))
        || false === $reflModel->hasMethod(sprintf('get%s', ucfirst($consraint->depends)))
        ) {
            $this->context
                ->buildViolation('Unable to find a least one of this methods: {{ methods }}')
                ->setParameter('{{ methods }}', sprintf('%s or %s', $consraint->field, $consraint->depends))
                ->addViolation()
                ;
        }

        $method = $reflector->getMethod($consraint->service[1]);
        $dependsMethod = $reflModel->getMethod(sprintf('get%s', ucfirst($consraint->depends)));
        $fieldMethod = $reflModel->getMethod(sprintf('get%s', ucfirst($consraint->field)));

        if (false === in_array($fieldMethod->invoke($obj), $method->invoke($service, $dependsMethod->invoke($obj)), true)) {
            $this->context
                ->buildViolation($consraint->message)
                ->atPath($consraint->field)
                ->addViolation()
                ;
        }

        return true;
    }
}
