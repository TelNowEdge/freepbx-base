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

class UserEventManager
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    public function setConnection(\AGI_AsteriskManager $connection)
    {
        $this->connection = $connection;
    }

    public function emit($name, $type, $channel, \Doctrine\Common\Collections\ArrayCollection $values)
    {
        $array = array(
            'UserEvent' => $name,
            'Type' => $type,
            'Channel' => $channel,
        );

        foreach ($values as $k => $v) {
            $array[$k] = $v;
        }

        try {
            $this->connection
                ->send_request('UserEvent', $array);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error with: [%s, %s, %s]', $name, $type, $channel), 0, $e);
        }

        return true;
    }
}
