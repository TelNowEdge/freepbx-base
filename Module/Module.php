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

namespace TelNowEdge\FreePBX\Base\Module;

use Exception;
use FreePBX_Helpers;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use TelNowEdge\FreePBX\Base\DependencyInjection\ContainerBuilderFactory;
use TelNowEdgeCachedContainer;

abstract class Module extends FreePBX_Helpers
{
    use ModuleTrait;

    protected $astman;
    protected $config;
    protected $database;
    protected $freepbx;

    /**
     * Symfony\Component\HttpFoundation\Request.
     */
    protected Request $request;

    /**
     * Symfony\Component\Form\FormFactory.
     */
    protected FormFactory $formFactory;

    /**
     * \Twig_Environment.
     */
    protected $twig;

    /**
     * Symfony\Component\Validator\Validator\RecursiveValidator.php.
     */
    protected RecursiveValidator $validator;

    /**
     * Symfony\Component\DependencyInjection\ContainerBuilder.
     */
    protected ContainerBuilder|TelNowEdgeCachedContainer $container;

    /**
     * @throws Exception
     */
    public function __construct($freepbx = null, $disabledCache = false)
    {
        parent::__construct($freepbx);

        $this->astman = $freepbx->astman;
        $this->config = $freepbx->Config;
        $this->database = $freepbx->Database;
        $this->freepbx = $freepbx;
        $this->container = ContainerBuilderFactory::getInstance(
            false,
            true
        );
    }

    public static function dropCache(): void
    {
        ContainerBuilderFactory::dropCache();
    }

    public function getContainer(): ContainerBuilder|TelNowEdgeCachedContainer
    {
        return $this->container;
    }
}
