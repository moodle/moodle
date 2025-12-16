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

namespace Google\Service\ServiceControl;

class V2LogEntry extends \Google\Model
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
  protected $httpRequestType = V2HttpRequest::class;
  protected $httpRequestDataType = '';
  /**
   * A unique ID for the log entry used for deduplication. If omitted, the
   * implementation will generate one based on operation_id.
   *
   * @var string
   */
  public $insertId;
  /**
   * A set of user-defined (key, value) data that provides additional
   * information about the log entry.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A set of user-defined (key, value) data that provides additional
   * information about the moniotored resource that the log entry belongs to.
   *
   * @var string[]
   */
  public $monitoredResourceLabels;
  /**
   * Required. The log to which this log entry belongs. Examples: `"syslog"`,
   * `"book_log"`.
   *
   * @var string
   */
  public $name;
  protected $operationType = V2LogEntryOperation::class;
  protected $operationDataType = '';
  /**
   * The log entry payload, represented as a protocol buffer that is expressed
   * as a JSON object. The only accepted type currently is AuditLog.
   *
   * @var array[]
   */
  public $protoPayload;
  /**
   * The severity of the log entry. The default value is `LogSeverity.DEFAULT`.
   *
   * @var string
   */
  public $severity;
  protected $sourceLocationType = V2LogEntrySourceLocation::class;
  protected $sourceLocationDataType = '';
  /**
   * The log entry payload, represented as a structure that is expressed as a
   * JSON object.
   *
   * @var array[]
   */
  public $structPayload;
  /**
   * The log entry payload, represented as a Unicode string (UTF-8).
   *
   * @var string
   */
  public $textPayload;
  /**
   * The time the event described by the log entry occurred. If omitted,
   * defaults to operation start time.
   *
   * @var string
   */
  public $timestamp;
  /**
   * Optional. Resource name of the trace associated with the log entry, if any.
   * If this field contains a relative resource name, you can assume the name is
   * relative to `//tracing.googleapis.com`. Example: `projects/my-
   * projectid/traces/06796866738c859f2f19b7cfb3214824`
   *
   * @var string
   */
  public $trace;

  /**
   * Optional. Information about the HTTP request associated with this log
   * entry, if applicable.
   *
   * @param V2HttpRequest $httpRequest
   */
  public function setHttpRequest(V2HttpRequest $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return V2HttpRequest
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * A unique ID for the log entry used for deduplication. If omitted, the
   * implementation will generate one based on operation_id.
   *
   * @param string $insertId
   */
  public function setInsertId($insertId)
  {
    $this->insertId = $insertId;
  }
  /**
   * @return string
   */
  public function getInsertId()
  {
    return $this->insertId;
  }
  /**
   * A set of user-defined (key, value) data that provides additional
   * information about the log entry.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * A set of user-defined (key, value) data that provides additional
   * information about the moniotored resource that the log entry belongs to.
   *
   * @param string[] $monitoredResourceLabels
   */
  public function setMonitoredResourceLabels($monitoredResourceLabels)
  {
    $this->monitoredResourceLabels = $monitoredResourceLabels;
  }
  /**
   * @return string[]
   */
  public function getMonitoredResourceLabels()
  {
    return $this->monitoredResourceLabels;
  }
  /**
   * Required. The log to which this log entry belongs. Examples: `"syslog"`,
   * `"book_log"`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Information about an operation associated with the log entry, if
   * applicable.
   *
   * @param V2LogEntryOperation $operation
   */
  public function setOperation(V2LogEntryOperation $operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return V2LogEntryOperation
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The log entry payload, represented as a protocol buffer that is expressed
   * as a JSON object. The only accepted type currently is AuditLog.
   *
   * @param array[] $protoPayload
   */
  public function setProtoPayload($protoPayload)
  {
    $this->protoPayload = $protoPayload;
  }
  /**
   * @return array[]
   */
  public function getProtoPayload()
  {
    return $this->protoPayload;
  }
  /**
   * The severity of the log entry. The default value is `LogSeverity.DEFAULT`.
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
   * Optional. Source code location information associated with the log entry,
   * if any.
   *
   * @param V2LogEntrySourceLocation $sourceLocation
   */
  public function setSourceLocation(V2LogEntrySourceLocation $sourceLocation)
  {
    $this->sourceLocation = $sourceLocation;
  }
  /**
   * @return V2LogEntrySourceLocation
   */
  public function getSourceLocation()
  {
    return $this->sourceLocation;
  }
  /**
   * The log entry payload, represented as a structure that is expressed as a
   * JSON object.
   *
   * @param array[] $structPayload
   */
  public function setStructPayload($structPayload)
  {
    $this->structPayload = $structPayload;
  }
  /**
   * @return array[]
   */
  public function getStructPayload()
  {
    return $this->structPayload;
  }
  /**
   * The log entry payload, represented as a Unicode string (UTF-8).
   *
   * @param string $textPayload
   */
  public function setTextPayload($textPayload)
  {
    $this->textPayload = $textPayload;
  }
  /**
   * @return string
   */
  public function getTextPayload()
  {
    return $this->textPayload;
  }
  /**
   * The time the event described by the log entry occurred. If omitted,
   * defaults to operation start time.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * Optional. Resource name of the trace associated with the log entry, if any.
   * If this field contains a relative resource name, you can assume the name is
   * relative to `//tracing.googleapis.com`. Example: `projects/my-
   * projectid/traces/06796866738c859f2f19b7cfb3214824`
   *
   * @param string $trace
   */
  public function setTrace($trace)
  {
    $this->trace = $trace;
  }
  /**
   * @return string
   */
  public function getTrace()
  {
    return $this->trace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(V2LogEntry::class, 'Google_Service_ServiceControl_V2LogEntry');
