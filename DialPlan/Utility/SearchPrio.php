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

namespace TelNowEdge\FreePBX\Base\DialPlan\Utility;

class SearchPrio
{
    public static function forTag(&$ext, $section, $context, $tag)
    {
        $exts = $ext->_exts;
        $findTag = false;
        $context = ' '.trim($context).' ';  // Je ne sais pas pourquoi mais c'est comme cela dans extension.class.php de FreePBX
        if (isset($exts[$section][$context])) {
            $count = 0;
            foreach ($exts[$section][$context] as $pri => $curr_command) {
                if ($curr_command['tag'] === $tag) {
                    $new_priority = $count;
                    $findTag = true;
                    break;
                }
                ++$count;
            }
        }
        if ($findTag) {
            return $count;
        }

        return -1;
    }
}
