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

namespace TelNowEdge\FreePBX\Base\Event;

// TODO : deprecated
// use Symfony\Component\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\Event;

class GuiHookEvent extends Event
{
    public const GUI_HOOK_START = 'gui_hook.start';
    public const GUI_HOOK_DONE = 'gui_hook.done';
    public const GUI_HOOK_PRE_RENDERING = 'gui_hook.pre_rendering';

    public function __construct(protected $module)
    {
    }
}
