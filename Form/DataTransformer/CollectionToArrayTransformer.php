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

namespace TelNowEdge\FreePBX\Base\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use function is_array;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CollectionToArrayTransformer implements DataTransformerInterface
{
    /**
     * Transforms a collection into an array.
     *
     * @return mixed An array of entities
     *
     * @throws TransformationFailedException
     */
    public function transform($collection): mixed
    {
        if (null === $collection) {
            return array();
        }
        // For cases when the collection getter returns $collection->toArray()
        // in order to prevent modifications of the returned collection
        if (is_array($collection)) {
            return $collection;
        }
        if (!$collection instanceof Collection) {
            throw new TransformationFailedException('Expected a Doctrine\Common\Collections\Collection object.');
        }

        return $collection->toArray();
    }

    /**
     * Transforms choice keys into entities.
     *
     * @param mixed $array An array of entities
     *
     * @return ArrayCollection|Collection A collection of entities
     */
    public function reverseTransform($array): ArrayCollection|Collection
    {
        if ('' === $array || null === $array) {
            $array = array();
        } else {
            $array = (array)$array;
        }

        return new ArrayCollection($array);
    }
}
