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

class SystemEvent extends \Google\Model
{
  /**
   * The event type is unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The event is a consequence of a user account being deleted.
   */
  public const TYPE_USER_DELETION = 'USER_DELETION';
  /**
   * The event is due to the system automatically purging trash.
   */
  public const TYPE_TRASH_AUTO_PURGE = 'TRASH_AUTO_PURGE';
  /**
   * The type of the system event that may triggered activity.
   *
   * @var string
   */
  public $type;

  /**
   * The type of the system event that may triggered activity.
   *
   * Accepted values: TYPE_UNSPECIFIED, USER_DELETION, TRASH_AUTO_PURGE
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
class_alias(SystemEvent::class, 'Google_Service_DriveActivity_SystemEvent');
