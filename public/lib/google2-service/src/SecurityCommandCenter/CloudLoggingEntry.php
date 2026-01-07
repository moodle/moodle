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

namespace Google\Service\SecurityCommandCenter;

class CloudLoggingEntry extends \Google\Model
{
  /**
   * A unique identifier for the log entry.
   *
   * @var string
   */
  public $insertId;
  /**
   * The type of the log (part of `log_name`. `log_name` is the resource name of
   * the log to which this log entry belongs). For example:
   * `cloudresourcemanager.googleapis.com/activity`. Note that this field is not
   * URL-encoded, unlike the `LOG_ID` field in `LogEntry`.
   *
   * @var string
   */
  public $logId;
  /**
   * The organization, folder, or project of the monitored resource that
   * produced this log entry.
   *
   * @var string
   */
  public $resourceContainer;
  /**
   * The time the event described by the log entry occurred.
   *
   * @var string
   */
  public $timestamp;

  /**
   * A unique identifier for the log entry.
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
   * The type of the log (part of `log_name`. `log_name` is the resource name of
   * the log to which this log entry belongs). For example:
   * `cloudresourcemanager.googleapis.com/activity`. Note that this field is not
   * URL-encoded, unlike the `LOG_ID` field in `LogEntry`.
   *
   * @param string $logId
   */
  public function setLogId($logId)
  {
    $this->logId = $logId;
  }
  /**
   * @return string
   */
  public function getLogId()
  {
    return $this->logId;
  }
  /**
   * The organization, folder, or project of the monitored resource that
   * produced this log entry.
   *
   * @param string $resourceContainer
   */
  public function setResourceContainer($resourceContainer)
  {
    $this->resourceContainer = $resourceContainer;
  }
  /**
   * @return string
   */
  public function getResourceContainer()
  {
    return $this->resourceContainer;
  }
  /**
   * The time the event described by the log entry occurred.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudLoggingEntry::class, 'Google_Service_SecurityCommandCenter_CloudLoggingEntry');
