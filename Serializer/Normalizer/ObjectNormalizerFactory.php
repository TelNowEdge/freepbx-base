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

namespace TelNowEdge\FreePBX\Base\Serializer\Normalizer;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ObjectNormalizerFactory
{
    public function getObjectNormalizer(): ObjectNormalizer
    {
        // TODO AnnotationLoader.php is deprecated, use AttributeLoader instead
        // AttributeLoader : reader in constructor is deprecated. In future version, it's not in.
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        // Handle circular reference
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, array $context): string {
                return $object->getId();
            },
        ];
        // $normalizer->setCircularReferenceHandler(function ($object) {
        //     return $object->getId();
        // });

        return new ObjectNormalizer($classMetadataFactory,null, null, null, null, null, $defaultContext);
    }
}
