<?php
/*
 * Copyright 2016-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\GridFS\Exception;

use MongoDB\Exception\RuntimeException;

use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function sprintf;

class FileNotFoundException extends RuntimeException
{
    /**
     * Thrown when a file cannot be found by its filename and revision.
     *
     * @param string  $filename  Filename
     * @param integer $revision  Revision
     * @param string  $namespace Namespace for the files collection
     * @return self
     */
    public static function byFilenameAndRevision(string $filename, int $revision, string $namespace)
    {
        return new static(sprintf('File with name "%s" and revision "%d" not found in "%s"', $filename, $revision, $namespace));
    }

    /**
     * Thrown when a file cannot be found by its ID.
     *
     * @param mixed  $id        File ID
     * @param string $namespace Namespace for the files collection
     * @return self
     */
    public static function byId($id, string $namespace)
    {
        $json = toJSON(fromPHP(['_id' => $id]));

        return new static(sprintf('File "%s" not found in "%s"', $json, $namespace));
    }
}
