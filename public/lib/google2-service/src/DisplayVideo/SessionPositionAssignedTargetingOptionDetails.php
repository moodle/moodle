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

namespace Google\Service\DisplayVideo;

class SessionPositionAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * This is a placeholder, does not indicate any positions.
   */
  public const SESSION_POSITION_SESSION_POSITION_UNSPECIFIED = 'SESSION_POSITION_UNSPECIFIED';
  /**
   * The first impression of the session.
   */
  public const SESSION_POSITION_SESSION_POSITION_FIRST_IMPRESSION = 'SESSION_POSITION_FIRST_IMPRESSION';
  /**
   * The position where the ad will show in a session.
   *
   * @var string
   */
  public $sessionPosition;

  /**
   * The position where the ad will show in a session.
   *
   * Accepted values: SESSION_POSITION_UNSPECIFIED,
   * SESSION_POSITION_FIRST_IMPRESSION
   *
   * @param self::SESSION_POSITION_* $sessionPosition
   */
  public function setSessionPosition($sessionPosition)
  {
    $this->sessionPosition = $sessionPosition;
  }
  /**
   * @return self::SESSION_POSITION_*
   */
  public function getSessionPosition()
  {
    return $this->sessionPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionPositionAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_SessionPositionAssignedTargetingOptionDetails');
