<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

trait FromJSONTrait
{
    public static function fromJSON($jsonStr) {
        //
        // 2nd arg as true means return value is an assoc. array rather than object
        //
        $cfg = json_decode($jsonStr, true);

        if (is_null($cfg)) {
            throw new JSONParseErrorException($jsonStr, json_last_error(), json_last_error_msg());
        }
        $called_class = get_called_class();
        return new $called_class($cfg);
    }

}
