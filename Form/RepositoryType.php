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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\FreePBX\Base\Form\ChoiceList\RepositoryChoiceLoader;
use TelNowEdge\FreePBX\Base\Form\DataTransformer\CollectionToArrayTransformer;

class RepositoryType extends AbstractType implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder
                ->addViewTransformer(new CollectionToArrayTransformer(), true)
            ;
        }
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

            try {
                $collection = call_user_func_array(
                    array($this->container->get($options['repository']), $options['caller']),
                    $options['parameters']
                );
            } catch (NoResultException $e) {
                $collection = new \Doctrine\Common\Collections\ArrayCollection();
            }

            return new RepositoryChoiceLoader($collection);
        };

        $resolver->setRequired(array(
            'parameters',
            'caller',
            'repository',
        ));

        $resolver->setDefaults(array(
            'parameters' => array(),
            'choice_loader' => $choiceLoader,
            'choices' => null,
        ));
    }
}
