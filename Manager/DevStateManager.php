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

namespace TelNowEdge\FreePBX\Base\Manager;

use AGI_AsteriskManager;
use Exception;

class DevStateManager
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected AGI_AsteriskManager $connection;

    public function setConnection(AGI_AsteriskManager $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @param mixed $hint
     * @param mixed $state
     *
     * @throws Exception
     */
    public function update($hint, $state): true
    {
        $array = [
            'Command' => sprintf('devstate change Custom:%s %s', $hint, $state),
        ];

        try {
            $this->connection->send_request('Command', $array);
        } catch (Exception $e) {
            throw new Exception(sprintf('Error with: [%s]', $array['command']), 0, $e);
        }

        return true;
    }
}
