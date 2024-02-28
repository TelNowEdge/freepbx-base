<?php

/*
 * Copyright [2018] [TelNowEdge]
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

namespace TelNowEdge\FreePBX\Base\Logger;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

readonly class LoggerFactory
{
    public function __construct(private AmpConfManager $ampConfManager) {}

    public function createLogger(): Logger
    {
        $logger = new Logger('App');

        if (1 === $this->ampConfManager->get('FPBXDBUGDISABLE')) {
            $nullHandler = new NullHandler();
            $logger->pushHandler($nullHandler);

            return $logger;
        }

        $file = $this->ampConfManager->exists('FPBXDBUGFILE')
            ? $this->ampConfManager->get('FPBXDBUGFILE')
            : '/var/log/asterisk/freepbx_dbug';

        if (1 === $this->ampConfManager->get('DEVEL')) {
            $streamHandler = new StreamHandler($file, Logger::DEBUG);
            $logger->pushHandler($streamHandler);

            return $logger;
        }

        $streamHandler = new StreamHandler($file, Logger::DEBUG);
        $fingersCrossedHandler = new FingersCrossedHandler($streamHandler, Logger::WARNING);
        $logger->pushHandler($fingersCrossedHandler);

        return $logger;
    }
}
