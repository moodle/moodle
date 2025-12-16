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

namespace Google\Service\AppHub;

class Criticality extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Mission critical service, application or workload.
   */
  public const TYPE_MISSION_CRITICAL = 'MISSION_CRITICAL';
  /**
   * High impact.
   */
  public const TYPE_HIGH = 'HIGH';
  /**
   * Medium impact.
   */
  public const TYPE_MEDIUM = 'MEDIUM';
  /**
   * Low impact.
   */
  public const TYPE_LOW = 'LOW';
  /**
   * Required. Criticality Type.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Criticality Type.
   *
   * Accepted values: TYPE_UNSPECIFIED, MISSION_CRITICAL, HIGH, MEDIUM, LOW
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
class_alias(Criticality::class, 'Google_Service_AppHub_Criticality');
