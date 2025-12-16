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

namespace Google\Service\NetworkServices;

class LoggingConfig extends \Google\Model
{
  /**
   * Log severity is not specified. This value is treated the same as NONE, but
   * is used to distinguish between no update and update to NONE in
   * update_masks.
   */
  public const LOG_SEVERITY_LOG_SEVERITY_UNSPECIFIED = 'LOG_SEVERITY_UNSPECIFIED';
  /**
   * Default value at resource creation, presence of this value must be treated
   * as no logging/disable logging.
   */
  public const LOG_SEVERITY_NONE = 'NONE';
  /**
   * Debug or trace level logging.
   */
  public const LOG_SEVERITY_DEBUG = 'DEBUG';
  /**
   * Routine information, such as ongoing status or performance.
   */
  public const LOG_SEVERITY_INFO = 'INFO';
  /**
   * Normal but significant events, such as start up, shut down, or a
   * configuration change.
   */
  public const LOG_SEVERITY_NOTICE = 'NOTICE';
  /**
   * Warning events might cause problems.
   */
  public const LOG_SEVERITY_WARNING = 'WARNING';
  /**
   * Error events are likely to cause problems.
   */
  public const LOG_SEVERITY_ERROR = 'ERROR';
  /**
   * Critical events cause more severe problems or outages.
   */
  public const LOG_SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * A person must take action immediately.
   */
  public const LOG_SEVERITY_ALERT = 'ALERT';
  /**
   * One or more systems are unusable.
   */
  public const LOG_SEVERITY_EMERGENCY = 'EMERGENCY';
  /**
   * Optional. The minimum severity of logs that will be sent to
   * Stackdriver/Platform Telemetry. Logs at severitiy ≥ this value will be
   * sent, unless it is NONE.
   *
   * @var string
   */
  public $logSeverity;

  /**
   * Optional. The minimum severity of logs that will be sent to
   * Stackdriver/Platform Telemetry. Logs at severitiy ≥ this value will be
   * sent, unless it is NONE.
   *
   * Accepted values: LOG_SEVERITY_UNSPECIFIED, NONE, DEBUG, INFO, NOTICE,
   * WARNING, ERROR, CRITICAL, ALERT, EMERGENCY
   *
   * @param self::LOG_SEVERITY_* $logSeverity
   */
  public function setLogSeverity($logSeverity)
  {
    $this->logSeverity = $logSeverity;
  }
  /**
   * @return self::LOG_SEVERITY_*
   */
  public function getLogSeverity()
  {
    return $this->logSeverity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoggingConfig::class, 'Google_Service_NetworkServices_LoggingConfig');
