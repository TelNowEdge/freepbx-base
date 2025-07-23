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

namespace TelNowEdge\FreePBX\Base\Session\Flash;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class NullFlashBag implements FlashBagInterface
{
    public function add(string $type, mixed $message): void {}
    public function peek(string $type, array $default = []): array { return []; }
    public function peekAll(): array { return []; }
    public function get(string $type, array $default = []): array { return []; }
    public function all(): array { return []; }
    public function setAll(array $messages): void {}
    public function set(string $type, string|array $messages): void {}
    public function has(string $type): bool { return false; }
    public function keys(): array { return []; }
    public function setName(string $name): void {}
    public function getName(): string { return 'null_flash_bag'; }
    public function initialize(array &$array): void {}
    public function getStorageKey(): string { return '_null_flash_bag'; }
    public function clear(): array { return []; }
}
