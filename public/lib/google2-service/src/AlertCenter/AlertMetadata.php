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

namespace Google\Service\AlertCenter;

class AlertMetadata extends \Google\Model
{
  /**
   * Output only. The alert identifier.
   *
   * @var string
   */
  public $alertId;
  /**
   * The email address of the user assigned to the alert.
   *
   * @var string
   */
  public $assignee;
  /**
   * Output only. The unique identifier of the Google Workspace account of the
   * customer.
   *
   * @var string
   */
  public $customerId;
  /**
   * Optional. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of an alert metadata from overwriting
   * each other. It is strongly suggested that systems make use of the `etag` in
   * the read-modify-write cycle to perform metadata updates in order to avoid
   * race conditions: An `etag` is returned in the response which contains alert
   * metadata, and systems are expected to put that etag in the request to
   * update alert metadata to ensure that their change will be applied to the
   * same version of the alert metadata. If no `etag` is provided in the call to
   * update alert metadata, then the existing alert metadata is overwritten
   * blindly.
   *
   * @var string
   */
  public $etag;
  /**
   * The severity value of the alert. Alert Center will set this field at alert
   * creation time, default's to an empty string when it could not be
   * determined. The supported values for update actions on this field are the
   * following: * HIGH * MEDIUM * LOW
   *
   * @var string
   */
  public $severity;
  /**
   * The current status of the alert. The supported values are the following: *
   * NOT_STARTED * IN_PROGRESS * CLOSED
   *
   * @var string
   */
  public $status;
  /**
   * Output only. The time this metadata was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The alert identifier.
   *
   * @param string $alertId
   */
  public function setAlertId($alertId)
  {
    $this->alertId = $alertId;
  }
  /**
   * @return string
   */
  public function getAlertId()
  {
    return $this->alertId;
  }
  /**
   * The email address of the user assigned to the alert.
   *
   * @param string $assignee
   */
  public function setAssignee($assignee)
  {
    $this->assignee = $assignee;
  }
  /**
   * @return string
   */
  public function getAssignee()
  {
    return $this->assignee;
  }
  /**
   * Output only. The unique identifier of the Google Workspace account of the
   * customer.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Optional. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of an alert metadata from overwriting
   * each other. It is strongly suggested that systems make use of the `etag` in
   * the read-modify-write cycle to perform metadata updates in order to avoid
   * race conditions: An `etag` is returned in the response which contains alert
   * metadata, and systems are expected to put that etag in the request to
   * update alert metadata to ensure that their change will be applied to the
   * same version of the alert metadata. If no `etag` is provided in the call to
   * update alert metadata, then the existing alert metadata is overwritten
   * blindly.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The severity value of the alert. Alert Center will set this field at alert
   * creation time, default's to an empty string when it could not be
   * determined. The supported values for update actions on this field are the
   * following: * HIGH * MEDIUM * LOW
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The current status of the alert. The supported values are the following: *
   * NOT_STARTED * IN_PROGRESS * CLOSED
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The time this metadata was last updated.
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
class_alias(AlertMetadata::class, 'Google_Service_AlertCenter_AlertMetadata');
