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

class LogEntry extends \Google\Collection
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
  protected $collection_key = 'errorGroups';
  protected $apphubType = AppHub::class;
  protected $apphubDataType = '';
  protected $apphubDestinationType = AppHub::class;
  protected $apphubDestinationDataType = '';
  protected $errorGroupsType = LogErrorGroup::class;
  protected $errorGroupsDataType = 'array';
  protected $httpRequestType = HttpRequest::class;
  protected $httpRequestDataType = '';
  /**
   * Optional. A unique identifier for the log entry. If you provide a value,
   * then Logging considers other log entries in the same project, with the same
   * timestamp, and with the same insert_id to be duplicates which are removed
   * in a single query result. However, there are no guarantees of de-
   * duplication in the export of logs.If the insert_id is omitted when writing
   * a log entry, the Logging API assigns its own unique identifier in this
   * field.In queries, the insert_id is also used to order log entries that have
   * the same log_name and timestamp values.
   *
   * @var string
   */
  public $insertId;
  /**
   * The log entry payload, represented as a structure that is expressed as a
   * JSON object.
   *
   * @var array[]
   */
  public $jsonPayload;
  /**
   * Optional. A map of key, value pairs that provides additional information
   * about the log entry. The labels can be user-defined or system-defined.User-
   * defined labels are arbitrary key, value pairs that you can use to classify
   * logs.System-defined labels are defined by GCP services for platform logs.
   * They have two components - a service namespace component and the attribute
   * name. For example: compute.googleapis.com/resource_name.Cloud Logging
   * truncates label keys that exceed 512 B and label values that exceed 64 KB
   * upon their associated log entry being written. The truncation is indicated
   * by an ellipsis at the end of the character string.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The resource name of the log to which this log entry belongs:
   * "projects/[PROJECT_ID]/logs/[LOG_ID]"
   * "organizations/[ORGANIZATION_ID]/logs/[LOG_ID]"
   * "billingAccounts/[BILLING_ACCOUNT_ID]/logs/[LOG_ID]"
   * "folders/[FOLDER_ID]/logs/[LOG_ID]" A project number may be used in place
   * of PROJECT_ID. The project number is translated to its corresponding
   * PROJECT_ID internally and the log_name field will contain PROJECT_ID in
   * queries and exports.[LOG_ID] must be URL-encoded within log_name. Example:
   * "organizations/1234567890/logs/cloudresourcemanager.googleapis.com%2Factivi
   * ty".[LOG_ID] must be less than 512 characters long and can only include the
   * following characters: upper and lower case alphanumeric characters,
   * forward-slash, underscore, hyphen, and period.For backward compatibility,
   * if log_name begins with a forward-slash, such as /projects/..., then the
   * log entry is processed as usual, but the forward-slash is removed. Listing
   * the log entry will not show the leading slash and filtering for a log name
   * with a leading slash will never return any results.
   *
   * @var string
   */
  public $logName;
  protected $metadataType = MonitoredResourceMetadata::class;
  protected $metadataDataType = '';
  protected $operationType = LogEntryOperation::class;
  protected $operationDataType = '';
  /**
   * The log entry payload, represented as a protocol buffer. Some Google Cloud
   * Platform services use this field for their log entry payloads.The following
   * protocol buffer types are supported; user-defined types are not
   * supported:"type.googleapis.com/google.cloud.audit.AuditLog"
   * "type.googleapis.com/google.appengine.logging.v1.RequestLog"
   *
   * @var array[]
   */
  public $protoPayload;
  /**
   * Output only. The time the log entry was received by Logging.
   *
   * @var string
   */
  public $receiveTimestamp;
  protected $resourceType = MonitoredResource::class;
  protected $resourceDataType = '';
  /**
   * Optional. The severity of the log entry. The default value is
   * LogSeverity.DEFAULT.
   *
   * @var string
   */
  public $severity;
  protected $sourceLocationType = LogEntrySourceLocation::class;
  protected $sourceLocationDataType = '';
  /**
   * Optional. The ID of the Cloud Trace (https://cloud.google.com/trace) span
   * associated with the current operation in which the log is being written.
   * For example, if a span has the REST resource name of "projects/some-
   * project/traces/some-trace/spans/some-span-id", then the span_id field is
   * "some-span-id".A Span (https://cloud.google.com/trace/docs/reference/v2/res
   * t/v2/projects.traces/batchWrite#Span) represents a single operation within
   * a trace. Whereas a trace may involve multiple different microservices
   * running on multiple different machines, a span generally corresponds to a
   * single logical operation being performed in a single instance of a
   * microservice on one specific machine. Spans are the nodes within the tree
   * that is a trace.Applications that are instrumented for tracing
   * (https://cloud.google.com/trace/docs/setup) will generally assign a new,
   * unique span ID on each incoming request. It is also common to create and
   * record additional spans corresponding to internal processing elements as
   * well as issuing requests to dependencies.The span ID is expected to be a
   * 16-character, hexadecimal encoding of an 8-byte array and should not be
   * zero. It should be unique within the trace and should, ideally, be
   * generated in a manner that is uniformly random.Example values:
   * 000000000000004a 7a2190356c3fc94b 0000f00300090021 d39223e101960076
   *
   * @var string
   */
  public $spanId;
  protected $splitType = LogSplit::class;
  protected $splitDataType = '';
  /**
   * The log entry payload, represented as a Unicode string (UTF-8).
   *
   * @var string
   */
  public $textPayload;
  /**
   * Optional. The time the event described by the log entry occurred. This time
   * is used to compute the log entry's age and to enforce the logs retention
   * period. If this field is omitted in a new log entry, then Logging assigns
   * it the current time. Timestamps have nanosecond accuracy, but trailing
   * zeros in the fractional seconds might be omitted when the timestamp is
   * displayed.Incoming log entries must have timestamps that don't exceed the
   * logs retention period
   * (https://cloud.google.com/logging/quotas#logs_retention_periods) in the
   * past, and that don't exceed 24 hours in the future. Log entries outside
   * those time boundaries are rejected by Logging.
   *
   * @var string
   */
  public $timestamp;
  /**
   * Optional. The REST resource name of the trace being written to Cloud Trace
   * (https://cloud.google.com/trace) in association with this log entry. For
   * example, if your trace data is stored in the Cloud project "my-trace-
   * project" and if the service that is creating the log entry receives a trace
   * header that includes the trace ID "12345", then the service should use
   * "projects/my-trace-project/traces/12345".The trace field provides the link
   * between logs and traces. By using this field, you can navigate from a log
   * entry to a trace.
   *
   * @var string
   */
  public $trace;
  /**
   * Optional. The sampling decision of the span associated with the log entry
   * at the time the log entry was created. This field corresponds to the
   * sampled flag in the W3C trace-context specification
   * (https://www.w3.org/TR/trace-context/#sampled-flag). A non-sampled trace
   * value is still useful as a request correlation identifier. The default is
   * False.
   *
   * @var bool
   */
  public $traceSampled;

  /**
   * Output only. AppHub application metadata associated with this LogEntry. May
   * be empty if there is no associated AppHub application or multiple
   * associated applications (such as for VPC flow logs)
   *
   * @param AppHub $apphub
   */
  public function setApphub(AppHub $apphub)
  {
    $this->apphub = $apphub;
  }
  /**
   * @return AppHub
   */
  public function getApphub()
  {
    return $this->apphub;
  }
  /**
   * Output only. AppHub application metadata associated with the destination
   * application. This is only populated if the log represented "edge"-like data
   * (such as for VPC flow logs) with a source and destination.
   *
   * @param AppHub $apphubDestination
   */
  public function setApphubDestination(AppHub $apphubDestination)
  {
    $this->apphubDestination = $apphubDestination;
  }
  /**
   * @return AppHub
   */
  public function getApphubDestination()
  {
    return $this->apphubDestination;
  }
  /**
   * Output only. The Error Reporting (https://cloud.google.com/error-reporting)
   * error groups associated with this LogEntry. Error Reporting sets the values
   * for this field during error group creation.For more information, see View
   * error details( https://cloud.google.com/error-reporting/docs/viewing-
   * errors#view_error_details)This field isn't available during log routing
   * (https://cloud.google.com/logging/docs/routing/overview)
   *
   * @param LogErrorGroup[] $errorGroups
   */
  public function setErrorGroups($errorGroups)
  {
    $this->errorGroups = $errorGroups;
  }
  /**
   * @return LogErrorGroup[]
   */
  public function getErrorGroups()
  {
    return $this->errorGroups;
  }
  /**
   * Optional. Information about the HTTP request associated with this log
   * entry, if applicable.
   *
   * @param HttpRequest $httpRequest
   */
  public function setHttpRequest(HttpRequest $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return HttpRequest
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * Optional. A unique identifier for the log entry. If you provide a value,
   * then Logging considers other log entries in the same project, with the same
   * timestamp, and with the same insert_id to be duplicates which are removed
   * in a single query result. However, there are no guarantees of de-
   * duplication in the export of logs.If the insert_id is omitted when writing
   * a log entry, the Logging API assigns its own unique identifier in this
   * field.In queries, the insert_id is also used to order log entries that have
   * the same log_name and timestamp values.
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
   * The log entry payload, represented as a structure that is expressed as a
   * JSON object.
   *
   * @param array[] $jsonPayload
   */
  public function setJsonPayload($jsonPayload)
  {
    $this->jsonPayload = $jsonPayload;
  }
  /**
   * @return array[]
   */
  public function getJsonPayload()
  {
    return $this->jsonPayload;
  }
  /**
   * Optional. A map of key, value pairs that provides additional information
   * about the log entry. The labels can be user-defined or system-defined.User-
   * defined labels are arbitrary key, value pairs that you can use to classify
   * logs.System-defined labels are defined by GCP services for platform logs.
   * They have two components - a service namespace component and the attribute
   * name. For example: compute.googleapis.com/resource_name.Cloud Logging
   * truncates label keys that exceed 512 B and label values that exceed 64 KB
   * upon their associated log entry being written. The truncation is indicated
   * by an ellipsis at the end of the character string.
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
   * Required. The resource name of the log to which this log entry belongs:
   * "projects/[PROJECT_ID]/logs/[LOG_ID]"
   * "organizations/[ORGANIZATION_ID]/logs/[LOG_ID]"
   * "billingAccounts/[BILLING_ACCOUNT_ID]/logs/[LOG_ID]"
   * "folders/[FOLDER_ID]/logs/[LOG_ID]" A project number may be used in place
   * of PROJECT_ID. The project number is translated to its corresponding
   * PROJECT_ID internally and the log_name field will contain PROJECT_ID in
   * queries and exports.[LOG_ID] must be URL-encoded within log_name. Example:
   * "organizations/1234567890/logs/cloudresourcemanager.googleapis.com%2Factivi
   * ty".[LOG_ID] must be less than 512 characters long and can only include the
   * following characters: upper and lower case alphanumeric characters,
   * forward-slash, underscore, hyphen, and period.For backward compatibility,
   * if log_name begins with a forward-slash, such as /projects/..., then the
   * log entry is processed as usual, but the forward-slash is removed. Listing
   * the log entry will not show the leading slash and filtering for a log name
   * with a leading slash will never return any results.
   *
   * @param string $logName
   */
  public function setLogName($logName)
  {
    $this->logName = $logName;
  }
  /**
   * @return string
   */
  public function getLogName()
  {
    return $this->logName;
  }
  /**
   * Output only. Deprecated. This field is not used by Logging. Any value
   * written to it is cleared.
   *
   * @deprecated
   * @param MonitoredResourceMetadata $metadata
   */
  public function setMetadata(MonitoredResourceMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @deprecated
   * @return MonitoredResourceMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Optional. Information about an operation associated with the log entry, if
   * applicable.
   *
   * @param LogEntryOperation $operation
   */
  public function setOperation(LogEntryOperation $operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return LogEntryOperation
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The log entry payload, represented as a protocol buffer. Some Google Cloud
   * Platform services use this field for their log entry payloads.The following
   * protocol buffer types are supported; user-defined types are not
   * supported:"type.googleapis.com/google.cloud.audit.AuditLog"
   * "type.googleapis.com/google.appengine.logging.v1.RequestLog"
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
   * Output only. The time the log entry was received by Logging.
   *
   * @param string $receiveTimestamp
   */
  public function setReceiveTimestamp($receiveTimestamp)
  {
    $this->receiveTimestamp = $receiveTimestamp;
  }
  /**
   * @return string
   */
  public function getReceiveTimestamp()
  {
    return $this->receiveTimestamp;
  }
  /**
   * Required. The monitored resource that produced this log entry.Example: a
   * log entry that reports a database error would be associated with the
   * monitored resource designating the particular database that reported the
   * error.
   *
   * @param MonitoredResource $resource
   */
  public function setResource(MonitoredResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return MonitoredResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Optional. The severity of the log entry. The default value is
   * LogSeverity.DEFAULT.
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
   * @param LogEntrySourceLocation $sourceLocation
   */
  public function setSourceLocation(LogEntrySourceLocation $sourceLocation)
  {
    $this->sourceLocation = $sourceLocation;
  }
  /**
   * @return LogEntrySourceLocation
   */
  public function getSourceLocation()
  {
    return $this->sourceLocation;
  }
  /**
   * Optional. The ID of the Cloud Trace (https://cloud.google.com/trace) span
   * associated with the current operation in which the log is being written.
   * For example, if a span has the REST resource name of "projects/some-
   * project/traces/some-trace/spans/some-span-id", then the span_id field is
   * "some-span-id".A Span (https://cloud.google.com/trace/docs/reference/v2/res
   * t/v2/projects.traces/batchWrite#Span) represents a single operation within
   * a trace. Whereas a trace may involve multiple different microservices
   * running on multiple different machines, a span generally corresponds to a
   * single logical operation being performed in a single instance of a
   * microservice on one specific machine. Spans are the nodes within the tree
   * that is a trace.Applications that are instrumented for tracing
   * (https://cloud.google.com/trace/docs/setup) will generally assign a new,
   * unique span ID on each incoming request. It is also common to create and
   * record additional spans corresponding to internal processing elements as
   * well as issuing requests to dependencies.The span ID is expected to be a
   * 16-character, hexadecimal encoding of an 8-byte array and should not be
   * zero. It should be unique within the trace and should, ideally, be
   * generated in a manner that is uniformly random.Example values:
   * 000000000000004a 7a2190356c3fc94b 0000f00300090021 d39223e101960076
   *
   * @param string $spanId
   */
  public function setSpanId($spanId)
  {
    $this->spanId = $spanId;
  }
  /**
   * @return string
   */
  public function getSpanId()
  {
    return $this->spanId;
  }
  /**
   * Optional. Information indicating this LogEntry is part of a sequence of
   * multiple log entries split from a single LogEntry.
   *
   * @param LogSplit $split
   */
  public function setSplit(LogSplit $split)
  {
    $this->split = $split;
  }
  /**
   * @return LogSplit
   */
  public function getSplit()
  {
    return $this->split;
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
   * Optional. The time the event described by the log entry occurred. This time
   * is used to compute the log entry's age and to enforce the logs retention
   * period. If this field is omitted in a new log entry, then Logging assigns
   * it the current time. Timestamps have nanosecond accuracy, but trailing
   * zeros in the fractional seconds might be omitted when the timestamp is
   * displayed.Incoming log entries must have timestamps that don't exceed the
   * logs retention period
   * (https://cloud.google.com/logging/quotas#logs_retention_periods) in the
   * past, and that don't exceed 24 hours in the future. Log entries outside
   * those time boundaries are rejected by Logging.
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
   * Optional. The REST resource name of the trace being written to Cloud Trace
   * (https://cloud.google.com/trace) in association with this log entry. For
   * example, if your trace data is stored in the Cloud project "my-trace-
   * project" and if the service that is creating the log entry receives a trace
   * header that includes the trace ID "12345", then the service should use
   * "projects/my-trace-project/traces/12345".The trace field provides the link
   * between logs and traces. By using this field, you can navigate from a log
   * entry to a trace.
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
  /**
   * Optional. The sampling decision of the span associated with the log entry
   * at the time the log entry was created. This field corresponds to the
   * sampled flag in the W3C trace-context specification
   * (https://www.w3.org/TR/trace-context/#sampled-flag). A non-sampled trace
   * value is still useful as a request correlation identifier. The default is
   * False.
   *
   * @param bool $traceSampled
   */
  public function setTraceSampled($traceSampled)
  {
    $this->traceSampled = $traceSampled;
  }
  /**
   * @return bool
   */
  public function getTraceSampled()
  {
    return $this->traceSampled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LogEntry::class, 'Google_Service_Logging_LogEntry');
