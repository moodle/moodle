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

namespace Google\Service\CloudComposer;

class ConfigConflict extends \Google\Model
{
  /**
   * Conflict type is unknown.
   */
  public const TYPE_CONFLICT_TYPE_UNSPECIFIED = 'CONFLICT_TYPE_UNSPECIFIED';
  /**
   * Conflict is blocking, the upgrade would fail.
   */
  public const TYPE_BLOCKING = 'BLOCKING';
  /**
   * Conflict is non-blocking. The upgrade would succeed, but the environment
   * configuration would be changed.
   */
  public const TYPE_NON_BLOCKING = 'NON_BLOCKING';
  /**
   * Conflict message.
   *
   * @var string
   */
  public $message;
  /**
   * Conflict type. It can be blocking or non-blocking.
   *
   * @var string
   */
  public $type;

  /**
   * Conflict message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Conflict type. It can be blocking or non-blocking.
   *
   * Accepted values: CONFLICT_TYPE_UNSPECIFIED, BLOCKING, NON_BLOCKING
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
class_alias(ConfigConflict::class, 'Google_Service_CloudComposer_ConfigConflict');
