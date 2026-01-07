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

namespace Google\Service\Logging;

class LogLine extends \Google\Model
{
  /**
   * (0) The log entry has no assigned severity level.
   */
  public const SEVERITY_DEFAULT = 'DEFAULT';
  /**
   * (100) Debug or trace information.
   */
  public const SEVERITY_DEBUG = 'DEBUG';
  /**
   * (200) Routine information, such as ongoing status or performance.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * (300) Normal but significant events, such as start up, shut down, or a
   * configuration change.
   */
  public const SEVERITY_NOTICE = 'NOTICE';
  /**
   * (400) Warning events might cause problems.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * (500) Error events are likely to cause problems.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * (600) Critical events cause more severe problems or outages.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * (700) A person must take an action immediately.
   */
  public const SEVERITY_ALERT = 'ALERT';
  /**
   * (800) One or more systems are unusable.
   */
  public const SEVERITY_EMERGENCY = 'EMERGENCY';
  /**
   * App-provided log message.
   *
   * @var string
   */
  public $logMessage;
  /**
   * Severity of this log entry.
   *
   * @var string
   */
  public $severity;
  protected $sourceLocationType = SourceLocation::class;
  protected $sourceLocationDataType = '';
  /**
   * Approximate time when this log entry was made.
   *
   * @var string
   */
  public $time;

  /**
   * App-provided log message.
   *
   * @param string $logMessage
   */
  public function setLogMessage($logMessage)
  {
    $this->logMessage = $logMessage;
  }
  /**
   * @return string
   */
  public function getLogMessage()
  {
    return $this->logMessage;
  }
  /**
   * Severity of this log entry.
   *
   * Accepted values: DEFAULT, DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL,
   * ALERT, EMERGENCY
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
  /**
   * Where in the source code this log message was written.
   *
   * @param SourceLocation $sourceLocation
   */
  public function setSourceLocation(SourceLocation $sourceLocation)
  {
    $this->sourceLocation = $sourceLocation;
  }
  /**
   * @return SourceLocation
   */
  public function getSourceLocation()
  {
    return $this->sourceLocation;
  }
  /**
   * Approximate time when this log entry was made.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogLine::class, 'Google_Service_Logging_LogLine');
