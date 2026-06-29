<?php

/*
 * Copyright 2026 TelNowEdge
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

namespace TelNowEdge\FreePBX\Base\Client;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

class RestProvisionClientFactory
{
    public function __construct(
        private AmpConfManager $ampConfManager,
    ) {
    }

    public function createClient(?string $provisionKey = null, int $timeout = 15): HttpClientInterface
    {
        $uri = 'http://provision:8080/';

        $headers = [
            'Content-Type' => 'application/json',
        ];

        return HttpClient::create([
            'base_uri' => $uri,
            'headers' => $headers,
            'timeout' => $timeout,
        ]);
    }
}
