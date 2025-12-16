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

class WriteLogEntriesRequest extends \Google\Collection
{
  protected $collection_key = 'entries';
  /**
   * Optional. If true, the request should expect normal response, but the
   * entries won't be persisted nor exported. Useful for checking whether the
   * logging API endpoints are working properly before sending valuable data.
   *
   * @var bool
   */
  public $dryRun;
  protected $entriesType = LogEntry::class;
  protected $entriesDataType = 'array';
  /**
   * Optional. Default labels that are added to the labels field of all log
   * entries in entries. If a log entry already has a label with the same key as
   * a label in this parameter, then the log entry's label is not changed. See
   * LogEntry.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. A default log resource name that is assigned to all log entries
   * in entries that do not specify a value for log_name:
   * projects/[PROJECT_ID]/logs/[LOG_ID]
   * organizations/[ORGANIZATION_ID]/logs/[LOG_ID]
   * billingAccounts/[BILLING_ACCOUNT_ID]/logs/[LOG_ID]
   * folders/[FOLDER_ID]/logs/[LOG_ID][LOG_ID] must be URL-encoded. For example:
   * "projects/my-project-id/logs/syslog"
   * "organizations/123/logs/cloudaudit.googleapis.com%2Factivity" The
   * permission logging.logEntries.create is needed on each project,
   * organization, billing account, or folder that is receiving new log entries,
   * whether the resource is specified in logName or in an individual log entry.
   *
   * @var string
   */
  public $logName;
  /**
   * Optional. Whether a batch's valid entries should be written even if some
   * other entry failed due to a permanent error such as INVALID_ARGUMENT or
   * PERMISSION_DENIED. If any entry failed, then the response status is the
   * response status of one of the failed entries. The response will include
   * error details in WriteLogEntriesPartialErrors.log_entry_errors keyed by the
   * entries' zero-based index in the entries. Failed requests for which no
   * entries are written will not include per-entry errors.
   *
   * @var bool
   */
  public $partialSuccess;
  protected $resourceType = MonitoredResource::class;
  protected $resourceDataType = '';

  /**
   * Optional. If true, the request should expect normal response, but the
   * entries won't be persisted nor exported. Useful for checking whether the
   * logging API endpoints are working properly before sending valuable data.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * Required. The log entries to send to Logging. The order of log entries in
   * this list does not matter. Values supplied in this method's log_name,
   * resource, and labels fields are copied into those log entries in this list
   * that do not include values for their corresponding fields. For more
   * information, see the LogEntry type.If the timestamp or insert_id fields are
   * missing in log entries, then this method supplies the current time or a
   * unique identifier, respectively. The supplied values are chosen so that,
   * among the log entries that did not supply their own values, the entries
   * earlier in the list will sort before the entries later in the list. See the
   * entries.list method.Log entries with timestamps that are more than the logs
   * retention period (https://cloud.google.com/logging/quotas) in the past or
   * more than 24 hours in the future will not be available when calling
   * entries.list. However, those log entries can still be exported with
   * LogSinks (https://cloud.google.com/logging/docs/api/tasks/exporting-
   * logs).To improve throughput and to avoid exceeding the quota limit
   * (https://cloud.google.com/logging/quotas) for calls to entries.write, you
   * should try to include several log entries in this list, rather than calling
   * this method for each individual log entry.
   *
   * @param LogEntry[] $entries
   */
  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  /**
   * @return LogEntry[]
   */
  public function getEntries()
  {
    return $this->entries;
  }
  /**
   * Optional. Default labels that are added to the labels field of all log
   * entries in entries. If a log entry already has a label with the same key as
   * a label in this parameter, then the log entry's label is not changed. See
   * LogEntry.
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
   * Optional. A default log resource name that is assigned to all log entries
   * in entries that do not specify a value for log_name:
   * projects/[PROJECT_ID]/logs/[LOG_ID]
   * organizations/[ORGANIZATION_ID]/logs/[LOG_ID]
   * billingAccounts/[BILLING_ACCOUNT_ID]/logs/[LOG_ID]
   * folders/[FOLDER_ID]/logs/[LOG_ID][LOG_ID] must be URL-encoded. For example:
   * "projects/my-project-id/logs/syslog"
   * "organizations/123/logs/cloudaudit.googleapis.com%2Factivity" The
   * permission logging.logEntries.create is needed on each project,
   * organization, billing account, or folder that is receiving new log entries,
   * whether the resource is specified in logName or in an individual log entry.
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
   * Optional. Whether a batch's valid entries should be written even if some
   * other entry failed due to a permanent error such as INVALID_ARGUMENT or
   * PERMISSION_DENIED. If any entry failed, then the response status is the
   * response status of one of the failed entries. The response will include
   * error details in WriteLogEntriesPartialErrors.log_entry_errors keyed by the
   * entries' zero-based index in the entries. Failed requests for which no
   * entries are written will not include per-entry errors.
   *
   * @param bool $partialSuccess
   */
  public function setPartialSuccess($partialSuccess)
  {
    $this->partialSuccess = $partialSuccess;
  }
  /**
   * @return bool
   */
  public function getPartialSuccess()
  {
    return $this->partialSuccess;
  }
  /**
   * Optional. A default monitored resource object that is assigned to all log
   * entries in entries that do not specify a value for resource. Example: {
   * "type": "gce_instance", "labels": { "zone": "us-central1-a", "instance_id":
   * "00000000000000000000" }} See LogEntry.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteLogEntriesRequest::class, 'Google_Service_Logging_WriteLogEntriesRequest');
