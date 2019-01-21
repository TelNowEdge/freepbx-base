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

namespace TelNowEdge\FreePBX\Base\Security\Role;

/*
 * Caution this file is duplicated with Symfony API.
 */
class StaticRole
{
    public static function getOrdered()
    {
        return array(
            1 => array(
                'role' => 'ROLE_EXPORT',
                'name' => 'Export',
            ),
            2 => array(
                'role' => 'ROLE_IMPORT',
                'name' => 'Import',
            ),
            4 => array(
                'role' => 'ROLE_MOBILE_APP',
                'name' => 'Mobile application',
            ),
        );
    }

    public static function getForForm()
    {
        $out = array();

        foreach (self::getOrdered() as $i => $x) {
            $out[$x['name']] = $i;
        }

        return $out;
    }
}