<?php

/*
 * Copyright 2018- [TelNowEdge]
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

class SipNotifyManager
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    public function setConnection(\AGI_AsteriskManager $connection)
    {
        $this->connection = $connection;
    }

    public function notify($event, $device, $tech='sip')
    {
        $cmd=sprintf('sip notify %s %d', $event, (int) $device);

        if ('pjsip' === $tech ){
            $cmd=sprintf('pjsip send notify %s endpoint %d', $event, (int) $device);
        }

        $command = array(
           'Command' => $cmd,
        );

        try {
            $this->connection
                ->send_request('Command', $command);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error with: [%s]', $command), 0, $e);
        }
    }
}
