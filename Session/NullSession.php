<?php

/*
 * Copyright 2025 TelNowEdge
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

namespace TelNowEdge\FreePBX\Base\Session;

use TelNowEdge\FreePBX\Base\Session\Flash\NullFlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NullSession implements SessionInterface
{
    public function start(): bool { return true; }
    public function isStarted(): bool { return true; }
    public function getId(): string { return 'cli_null_session'; }
    public function setId(string $id): void {}
    public function getName(): string { return 'NULLSESSION'; }
    public function setName(string $name): void {}

    public function invalidate(int $lifetime = null): bool { return true; }
    public function migrate(bool $destroy = false, int $lifetime = null): bool { return true; }
    public function save(): void {}

    public function has(string $name): bool { return false; }
    public function get(string $name, mixed $default = null): mixed { return $default; }
    public function set(string $name, mixed $value): void {}
    public function all(): array { return []; }
    public function replace(array $attributes): void {}
    public function remove(string $name): mixed { return null; }
    public function clear(): void {}

    public function getFlashBag(): \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
    {
        return new NullFlashBag();
    }

    public function registerBag(\Symfony\Component\HttpFoundation\Session\SessionBagInterface $bag): void {}
    public function getBag(string $name): \Symfony\Component\HttpFoundation\Session\SessionBagInterface
    {
        throw new \RuntimeException("Session bags not supported in NullSession.");
    }
    public function getMetadataBag(): \Symfony\Component\HttpFoundation\Session\Storage\MetadataBag
    {
        return new \Symfony\Component\HttpFoundation\Session\Storage\MetadataBag();
    }
}
