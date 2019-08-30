<?php

/*
 * Copyright 2019 TelNowEdge
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

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

class RestApiClientFactory
{
    private $ampConfManager;

    public function __construct(AmpConfManager $ampConfManager)
    {
        $this->ampConfManager = $ampConfManager;
    }

    public function createClient($apiKey, $timeout = 15)
    {
        $uri = 'https://localhost/api/v1/';

        if ('yes' === $this->ampConfManager->get('TNE_API_URI_LOCALHOST')) {
            $uri = 'http://localhost/api/v1/';
        } elseif ('yes+debug' === $this->ampConfManager->get('TNE_API_URI_LOCALHOST')) {
            $uri = 'http://localhost/api/v1/app_dev.php/';
        } else {
            if (null !== $uri = $this->ampConfManager->get('TNE_API_URI')) {
                if (1 !== preg_match('/\/$/', $uri)) {
                    $uri = sprintf('%s/', $uri);
                }
            }
        }

        $stack = new HandlerStack();
        $stack->setHandler(new CurlMultiHandler());
        $stack->push(self::addApiKey($apiKey));
        $stack->push(self::addContentType());

        return new Client(array(
            'base_uri' => $uri,
            'handler' => $stack,
            'timeout' => $timeout,
        ));
    }

    private static function addApiKey($apiKey)
    {
        return function (callable $handler) use ($apiKey) {
            return function (RequestInterface $request, array $options) use ($handler, $apiKey) {
                $request = $request->withHeader('x-api-key', $apiKey);

                return $handler($request, $options);
            };
        };
    }

    private static function addContentType()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request->withHeader('Content-Type', 'application/json');

                return $handler($request, $options);
            };
        };
    }
}
