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

namespace Google\Service\Pubsub;

class PlatformLogsSettings extends \Google\Model
{
  /**
   * Default value. Logs level is unspecified. Logs will be disabled.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Logs will be disabled.
   */
  public const SEVERITY_DISABLED = 'DISABLED';
  /**
   * Debug logs and higher-severity logs will be written.
   */
  public const SEVERITY_DEBUG = 'DEBUG';
  /**
   * Info logs and higher-severity logs will be written.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Warning logs and higher-severity logs will be written.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Only error logs will be written.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Optional. The minimum severity level of Platform Logs that will be written.
   *
   * @var string
   */
  public $severity;

  /**
   * Optional. The minimum severity level of Platform Logs that will be written.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, DISABLED, DEBUG, INFO, WARNING,
   * ERROR
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformLogsSettings::class, 'Google_Service_Pubsub_PlatformLogsSettings');
