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

namespace Google\Service\BigtableAdmin;

class StandardIsolation extends \Google\Model
{
  /**
   * Default value. Mapped to PRIORITY_HIGH (the legacy behavior) on creation.
   */
  public const PRIORITY_PRIORITY_UNSPECIFIED = 'PRIORITY_UNSPECIFIED';
  public const PRIORITY_PRIORITY_LOW = 'PRIORITY_LOW';
  public const PRIORITY_PRIORITY_MEDIUM = 'PRIORITY_MEDIUM';
  public const PRIORITY_PRIORITY_HIGH = 'PRIORITY_HIGH';
  /**
   * The priority of requests sent using this app profile.
   *
   * @var string
   */
  public $priority;

  /**
   * The priority of requests sent using this app profile.
   *
   * Accepted values: PRIORITY_UNSPECIFIED, PRIORITY_LOW, PRIORITY_MEDIUM,
   * PRIORITY_HIGH
   *
   * @param self::PRIORITY_* $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return self::PRIORITY_*
   */
  public function getPriority()
  {
    return $this->priority;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StandardIsolation::class, 'Google_Service_BigtableAdmin_StandardIsolation');
