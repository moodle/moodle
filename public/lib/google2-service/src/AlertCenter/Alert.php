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

class Alert extends \Google\Model
{
  /**
   * Output only. The unique identifier for the alert.
   *
   * @var string
   */
  public $alertId;
  /**
   * Output only. The time this alert was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The unique identifier of the Google Workspace account of the
   * customer.
   *
   * @var string
   */
  public $customerId;
  /**
   * Optional. The data associated with this alert, for example
   * google.apps.alertcenter.type.DeviceCompromised.
   *
   * @var array[]
   */
  public $data;
  /**
   * Output only. `True` if this alert is marked for deletion.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Optional. The time the event that caused this alert ceased being active. If
   * provided, the end time must not be earlier than the start time. If not
   * provided, it indicates an ongoing alert.
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of an alert from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform alert updates in order to avoid race
   * conditions: An `etag` is returned in the response which contains alerts,
   * and systems are expected to put that etag in the request to update alert to
   * ensure that their change will be applied to the same version of the alert.
   * If no `etag` is provided in the call to update alert, then the existing
   * alert is overwritten blindly.
   *
   * @var string
   */
  public $etag;
  protected $metadataType = AlertMetadata::class;
  protected $metadataDataType = '';
  /**
   * Output only. An optional [Security Investigation
   * Tool](https://support.google.com/a/answer/7575955) query for this alert.
   *
   * @var string
   */
  public $securityInvestigationToolLink;
  /**
   * Required. A unique identifier for the system that reported the alert. This
   * is output only after alert is created. Supported sources are any of the
   * following: * Google Operations * Mobile device management * Gmail phishing
   * * Data Loss Prevention * Domain wide takeout * State sponsored attack *
   * Google identity * Apps outage
   *
   * @var string
   */
  public $source;
  /**
   * Required. The time the event that caused this alert was started or
   * detected.
   *
   * @var string
   */
  public $startTime;
  /**
   * Required. The type of the alert. This is output only after alert is
   * created. For a list of available alert types see [Google Workspace Alert ty
   * pes](https://developers.google.com/workspace/admin/alertcenter/reference/al
   * ert-types).
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time this alert was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The unique identifier for the alert.
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
   * Output only. The time this alert was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
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
   * Optional. The data associated with this alert, for example
   * google.apps.alertcenter.type.DeviceCompromised.
   *
   * @param array[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return array[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Output only. `True` if this alert is marked for deletion.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Optional. The time the event that caused this alert ceased being active. If
   * provided, the end time must not be earlier than the start time. If not
   * provided, it indicates an ongoing alert.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of an alert from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform alert updates in order to avoid race
   * conditions: An `etag` is returned in the response which contains alerts,
   * and systems are expected to put that etag in the request to update alert to
   * ensure that their change will be applied to the same version of the alert.
   * If no `etag` is provided in the call to update alert, then the existing
   * alert is overwritten blindly.
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
   * Output only. The metadata associated with this alert.
   *
   * @param AlertMetadata $metadata
   */
  public function setMetadata(AlertMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return AlertMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. An optional [Security Investigation
   * Tool](https://support.google.com/a/answer/7575955) query for this alert.
   *
   * @param string $securityInvestigationToolLink
   */
  public function setSecurityInvestigationToolLink($securityInvestigationToolLink)
  {
    $this->securityInvestigationToolLink = $securityInvestigationToolLink;
  }
  /**
   * @return string
   */
  public function getSecurityInvestigationToolLink()
  {
    return $this->securityInvestigationToolLink;
  }
  /**
   * Required. A unique identifier for the system that reported the alert. This
   * is output only after alert is created. Supported sources are any of the
   * following: * Google Operations * Mobile device management * Gmail phishing
   * * Data Loss Prevention * Domain wide takeout * State sponsored attack *
   * Google identity * Apps outage
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Required. The time the event that caused this alert was started or
   * detected.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Required. The type of the alert. This is output only after alert is
   * created. For a list of available alert types see [Google Workspace Alert ty
   * pes](https://developers.google.com/workspace/admin/alertcenter/reference/al
   * ert-types).
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The time this alert was last updated.
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
class_alias(Alert::class, 'Google_Service_AlertCenter_Alert');
