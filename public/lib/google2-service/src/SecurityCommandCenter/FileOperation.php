<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\SecurityCommandCenter;

class FileOperation extends \Google\Model
{
  /**
   * The operation is unspecified.
   */
  public const TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * Represents an open operation.
   */
  public const TYPE_OPEN = 'OPEN';
  /**
   * Represents a read operation.
   */
  public const TYPE_READ = 'READ';
  /**
   * Represents a rename operation.
   */
  public const TYPE_RENAME = 'RENAME';
  /**
   * Represents a write operation.
   */
  public const TYPE_WRITE = 'WRITE';
  /**
   * Represents an execute operation.
   */
  public const TYPE_EXECUTE = 'EXECUTE';
  /**
   * The type of the operation
   *
   * @var string
   */
  public $type;

  /**
   * The type of the operation
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, OPEN, READ, RENAME, WRITE,
   * EXECUTE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FileOperation::class, 'Google_Service_SecurityCommandCenter_FileOperation');
