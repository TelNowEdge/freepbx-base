<?php

namespace TelNowEdge\FreePBX\Base\Form;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;
use TelNowEdge\FreePBX\Base\Helper\DestinationHelper;

class DestinationType extends AbstractType implements ContainerAwareInterface
{
    private $container;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $destinationHelper = $this->container->get(DestinationHelper::class);

        // All in event because $builder->getData() isn't available on child form.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($destinationHelper) {
            $data = $event->getData();
            $form = $event->getForm();
            if (true === is_a($data, Destination::class, true)) {
                $destinationHelper->addFake($data);
            }

            $form
                ->add('Category', ChoiceType::class, array(
                    'choices' => array_combine(
                        $destinationHelper->getCategories(),
                        $destinationHelper->getCategories()
                    ),
                    'placeholder' => '-',
                ))
                ->add('Destination', ChoiceType::class, array(
                    'choices' => $destinationHelper->getDestinations(),
                    'attr' => array(
                        'data-prototype' => $this->container->get('serializer')->serialize($destinationHelper->getRaw(), 'json'),
                    ),
                    'placeholder' => '-',
                ))
                ;
        });
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => Destination::class,
            ));
    }
}
