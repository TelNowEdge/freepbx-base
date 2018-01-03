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

        if (false === in_array($fieldMethod->invoke($obj), $method->invoke($service, $dependsMethod->invoke($obj)), true)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->field)
                ->addViolation()
                ;
        }

        return true;
    }
}
