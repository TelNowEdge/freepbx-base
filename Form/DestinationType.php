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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Serializer;
use TelNowEdge\FreePBX\Base\Form\Model\Destination;
use TelNowEdge\FreePBX\Base\Helper\DestinationHelper;

class DestinationType extends AbstractType
{

    public function __construct(private readonly DestinationHelper $destinationHelper, private readonly Serializer $serializer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // All in event because $builder->getData() isn't available on child form.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            if (is_a($data, Destination::class, true)) {
                $this->destinationHelper->addFake($data);
            }

            $form
                ->add('category', ChoiceType::class, [
                    'choices' => array_combine(
                        $this->destinationHelper->getCategories(),
                        $this->destinationHelper->getCategories()
                    ),
                    'placeholder' => '-',
                ])
                ->add('destination', ChoiceType::class, [
                    'choices' => $this->destinationHelper->getDestinations(),
                    'attr' => [
                        'data-prototype' => $this->serializer->serialize($this->destinationHelper->getRaw(), 'json'),
                        'data-type' => 'tne-destination',
                    ],
                    'placeholder' => '-',
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Destination::class,
            ]);
    }
}
