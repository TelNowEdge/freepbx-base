<?php

namespace TelNowEdge\FreePBX\Base\Event;

use Symfony\Component\EventDispatcher\Event;

class GuiHookEvent extends Event
{
    const GUI_HOOK_START = 'gui_hook.start';
    const GUI_HOOK_DONE = 'gui_hook.done';
    const GUI_HOOK_PRE_RENDERING = 'gui_hook.pre_rendering';

    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }
}
