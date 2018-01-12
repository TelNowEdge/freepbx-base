<?php

namespace TelNowEdge\FreePBX\Base\Form;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\ChoiceList\RepositoryChoiceLoader;

class RepositoryType extends AbstractType implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceLoader = function (Options $options) {
            if (null !== $options['choices']) {
                return;
            }

            $collection = $this->container->get($options['repository'])->{$options['caller']}();

            return new RepositoryChoiceLoader($collection);
        };

        $resolver->setRequired(array(
            'repository',
            'caller',
        ));

        $resolver->setDefaults(array(
            'repository' => null,
            'caller' => null,
            'choices' => null,
            'choice_loader' => $choiceLoader,
        ));
    }
}
