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

namespace Google\Service\GKEHub;

class Origin extends \Google\Model
{
  /**
   * Type is unknown or not set.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Per-Feature spec was inherited from the fleet-level default.
   */
  public const TYPE_FLEET = 'FLEET';
  /**
   * Per-Feature spec was inherited from the fleet-level default but is now out
   * of sync with the current default.
   */
  public const TYPE_FLEET_OUT_OF_SYNC = 'FLEET_OUT_OF_SYNC';
  /**
   * Per-Feature spec was inherited from a user specification.
   */
  public const TYPE_USER = 'USER';
  /**
   * Type specifies which type of origin is set.
   *
   * @var string
   */
  public $type;

  /**
   * Type specifies which type of origin is set.
   *
   * Accepted values: TYPE_UNSPECIFIED, FLEET, FLEET_OUT_OF_SYNC, USER
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
class_alias(Origin::class, 'Google_Service_GKEHub_Origin');
