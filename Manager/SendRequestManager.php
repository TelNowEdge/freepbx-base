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

class SendRequestManager
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    public function setConnection(\AGI_AsteriskManager $connection)
    {
        $this->connection = $connection;
    }

    public function sendRequest($request, array $command)
    {
        try {
            return $this->connection
                ->send_request($request, $command);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error with: %s', $request), 0, $e);
        }
    }

    public function showPjsipContact($endpoint)
    {
        $details = [];

        $this->connection->add_event_handler('ContactStatusDetail', function ($e, $d, $s, $p) use (&$details) {
            $details = $d;
        });

        $this->connection->add_event_handler('EndpointDetailComplete', function ($e, $d, $s, $p) {
            stream_set_timeout($this->connection->socket, 0, 1);
        });

        $response = $this->connection->send_request('PJSIPShowEndpoint', [ 'Endpoint' => $endpoint ]);
        if ($response['Response'] == 'Success') {
            $this->connection->wait_response(true);
            stream_set_timeout($this->connection->socket, 30);
        } else {
            return false;
        }

        usleep(1000);
        stream_set_blocking($this->connection->socket, false);
        while (fgets($this->connection->socket)) { /* do nothing */ }
        stream_set_blocking($this->connection->socket, true);
        unset($this->connection->event_handlers['ContactStatusDetail']);
        unset($this->connection->event_handlers['EndpointDetailComplete']);

        return $details;
    }
}
