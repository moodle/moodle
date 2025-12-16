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

namespace Google\Service\DriveActivity;

class Delete extends \Google\Model
{
  /**
   * Deletion type is not available.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * An object was put into the trash.
   */
  public const TYPE_TRASH = 'TRASH';
  /**
   * An object was deleted permanently.
   */
  public const TYPE_PERMANENT_DELETE = 'PERMANENT_DELETE';
  /**
   * The type of delete action taken.
   *
   * @var string
   */
  public $type;

  /**
   * The type of delete action taken.
   *
   * Accepted values: TYPE_UNSPECIFIED, TRASH, PERMANENT_DELETE
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
class_alias(Delete::class, 'Google_Service_DriveActivity_Delete');
