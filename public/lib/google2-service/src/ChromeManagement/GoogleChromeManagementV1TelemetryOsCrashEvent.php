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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1TelemetryOsCrashEvent extends \Google\Model
{
  /**
   * Crash type unknown.
   */
  public const CRASH_TYPE_CRASH_TYPE_UNSPECIFIED = 'CRASH_TYPE_UNSPECIFIED';
  /**
   * Kernel crash.
   */
  public const CRASH_TYPE_CRASH_TYPE_KERNEL = 'CRASH_TYPE_KERNEL';
  /**
   * Embedded controller crash.
   */
  public const CRASH_TYPE_CRASH_TYPE_EMBEDDED_CONTROLLER = 'CRASH_TYPE_EMBEDDED_CONTROLLER';
  /**
   * Session type unknown.
   */
  public const SESSION_TYPE_SESSION_TYPE_UNSPECIFIED = 'SESSION_TYPE_UNSPECIFIED';
  /**
   * Signed in user.
   */
  public const SESSION_TYPE_SESSION_TYPE_SIGNED_IN_USER = 'SESSION_TYPE_SIGNED_IN_USER';
  /**
   * Kiosk.
   */
  public const SESSION_TYPE_SESSION_TYPE_KIOSK = 'SESSION_TYPE_KIOSK';
  /**
   * Managed guest session.
   */
  public const SESSION_TYPE_SESSION_TYPE_MANAGED_GUEST = 'SESSION_TYPE_MANAGED_GUEST';
  /**
   * Active directory session.
   */
  public const SESSION_TYPE_SESSION_TYPE_ACTIVE_DIRECTORY = 'SESSION_TYPE_ACTIVE_DIRECTORY';
  /**
   * Crash id.
   *
   * @var string
   */
  public $crashId;
  /**
   * Crash type.
   *
   * @var string
   */
  public $crashType;
  /**
   * Session type.
   *
   * @var string
   */
  public $sessionType;

  /**
   * Crash id.
   *
   * @param string $crashId
   */
  public function setCrashId($crashId)
  {
    $this->crashId = $crashId;
  }
  /**
   * @return string
   */
  public function getCrashId()
  {
    return $this->crashId;
  }
  /**
   * Crash type.
   *
   * Accepted values: CRASH_TYPE_UNSPECIFIED, CRASH_TYPE_KERNEL,
   * CRASH_TYPE_EMBEDDED_CONTROLLER
   *
   * @param self::CRASH_TYPE_* $crashType
   */
  public function setCrashType($crashType)
  {
    $this->crashType = $crashType;
  }
  /**
   * @return self::CRASH_TYPE_*
   */
  public function getCrashType()
  {
    return $this->crashType;
  }
  /**
   * Session type.
   *
   * Accepted values: SESSION_TYPE_UNSPECIFIED, SESSION_TYPE_SIGNED_IN_USER,
   * SESSION_TYPE_KIOSK, SESSION_TYPE_MANAGED_GUEST,
   * SESSION_TYPE_ACTIVE_DIRECTORY
   *
   * @param self::SESSION_TYPE_* $sessionType
   */
  public function setSessionType($sessionType)
  {
    $this->sessionType = $sessionType;
  }
  /**
   * @return self::SESSION_TYPE_*
   */
  public function getSessionType()
  {
    return $this->sessionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryOsCrashEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryOsCrashEvent');
