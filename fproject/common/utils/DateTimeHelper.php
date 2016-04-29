<?php
///////////////////////////////////////////////////////////////////////////////
//
// © Copyright f-project.net 2010-present.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
///////////////////////////////////////////////////////////////////////////////

namespace fproject\common\utils;

/**
 * The Date-Time Helper class
 *
 * @author Bui Sy Nguyen <nguyenbs@gmail.com>
 */

class DateTimeHelper
{
    const DATE_ISO8601_UTC = "Y-m-d\TH:i:s";
    
    /**
     * Format a date-time using ISO 8601 UTC
     * @param mixed $d
     * @return string
     */
    public static function toISO8601UTC($d)
    {
        if($d instanceof \DateTime)
            return $d->format(self::DATE_ISO8601_UTC);
        return $d;
    }

    /**
     * Return current date-time as a ISO 8601 UTC serial
     * @return bool|string
     */
    public static function currentDateTime()
    {
        return date(self::DATE_ISO8601_UTC, time());
    }
}