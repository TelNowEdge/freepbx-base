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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\FreePBX\Base\Form\ChoiceList\RepositoryChoiceLoader;
use TelNowEdge\FreePBX\Base\Form\DataTransformer\CollectionToArrayTransformer;

class RepositoryType extends AbstractType implements ContainerAwareInterface
{
    private ?\Symfony\Component\DependencyInjection\ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder
                ->addViewTransformer(new CollectionToArrayTransformer(), true)
            ;
        }
    }

    public function getParent(): string
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choiceLoader = function (Options $options): ?RepositoryChoiceLoader {
            if (null !== $options['choices']) {
                return null;
            }

            try {
                $collection = \call_user_func_array(
                    [$this->container->get($options['repository']), $options['caller']],
                    $options['parameters']
                );
            } catch (NoResultException $e) {
                $collection = new ArrayCollection();
            }

            return new RepositoryChoiceLoader($collection);
        };

        $resolver->setRequired([
            'parameters',
            'caller',
            'repository',
        ]);

        $resolver->setDefaults([
            'parameters' => [],
            'choice_loader' => $choiceLoader,
            'choices' => null,
        ]);
    }
}
