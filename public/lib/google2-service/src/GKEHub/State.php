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

class State extends \Google\Model
{
  /**
   * Unknown or not set.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The MembershipFeature is operating normally.
   */
  public const CODE_OK = 'OK';
  /**
   * The MembershipFeature has encountered an issue, and is operating in a
   * degraded state. The MembershipFeature may need intervention to return to
   * normal operation. See the description and any associated MembershipFeature-
   * specific details for more information.
   */
  public const CODE_WARNING = 'WARNING';
  /**
   * The MembershipFeature is not operating or is in a severely degraded state.
   * The MembershipFeature may need intervention to return to normal operation.
   * See the description and any associated MembershipFeature-specific details
   * for more information.
   */
  public const CODE_ERROR = 'ERROR';
  /**
   * The high-level, machine-readable status of this MembershipFeature.
   *
   * @var string
   */
  public $code;
  /**
   * A human-readable description of the current status.
   *
   * @var string
   */
  public $description;
  /**
   * The time this status and any related Feature-specific details were updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The high-level, machine-readable status of this MembershipFeature.
   *
   * Accepted values: CODE_UNSPECIFIED, OK, WARNING, ERROR
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A human-readable description of the current status.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The time this status and any related Feature-specific details were updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(State::class, 'Google_Service_GKEHub_State');
