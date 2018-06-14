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

use TelNowEdge\FreePBX\Base\DependencyInjection\ContainerBuilderFactory;

abstract class Module extends \FreePBX_Helpers
{
    use ModuleTrait;

    protected $astman;
    protected $config;
    protected $database;
    protected $freepbx;

    /**
     * Symfony\Component\HttpFoundation\Request.
     */
    protected $request;

    /**
     * Symfony\Component\Form\FormFactory.
     */
    protected $formFactory;

    /**
     * \Twig_Environment.
     */
    protected $twig;

    /**
     * Symfony\Component\Validator\Validator\RecursiveValidator.php.
     */
    protected $validator;

    /**
     * Symfony\Component\DependencyInjection\ContainerBuilder.
     */
    protected $container;

    public function __construct($freepbx = null)
    {
        parent::__construct($freepbx);

        $this->astman = $freepbx->astman;
        $this->config = $freepbx->Config;
        $this->database = $freepbx->Database;
        $this->freepbx = $freepbx;
        $this->container = ContainerBuilderFactory::getInstance();
    }

    public function getContainer()
    {
        return $this->container;
    }
}
