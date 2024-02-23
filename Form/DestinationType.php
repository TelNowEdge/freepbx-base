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

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                ->add('category', ChoiceType::class, array(
                    'choices' => array_combine(
                        $destinationHelper->getCategories(),
                        $destinationHelper->getCategories()
                    ),
                    'placeholder' => '-',
                ))
                ->add('destination', ChoiceType::class, array(
                    'choices' => $destinationHelper->getDestinations(),
                    'attr' => array(
                        'data-prototype' => $this->container->get('serializer')->serialize($destinationHelper->getRaw(), 'json'),
                        'data-type' => 'tne-destination',
                    ),
                    'placeholder' => '-',
                ));
        });
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(array(
                'data_class' => Destination::class,
            ));
    }
}
