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

namespace Google\Service\Networkconnectivity;

class Destination extends \Google\Collection
{
  protected $collection_key = 'endpoints';
  /**
   * Output only. Time when the `Destination` resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A description of this resource.
   *
   * @var string
   */
  public $description;
  protected $endpointsType = DestinationEndpoint::class;
  protected $endpointsDataType = 'array';
  /**
   * The etag is computed by the server, and might be sent with update and
   * delete requests so that the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Required. Immutable. The IP prefix that represents your workload on another
   * CSP.
   *
   * @var string
   */
  public $ipPrefix;
  /**
   * Optional. User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the `Destination` resource. Format: `projects/{proj
   * ect}/locations/{location}/multicloudDataTransferConfigs/{multicloud_data_tr
   * ansfer_config}/destinations/{destination}`.
   *
   * @var string
   */
  public $name;
  protected $stateTimelineType = StateTimeline::class;
  protected $stateTimelineDataType = '';
  /**
   * Output only. The Google-generated unique ID for the `Destination` resource.
   * This value is unique across all `Destination` resources. If a resource is
   * deleted and another with the same name is created, the new resource is
   * assigned a different and unique ID.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time when the `Destination` resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the `Destination` resource was created.
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
   * Optional. A description of this resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Unordered list. The list of `DestinationEndpoint` resources
   * configured for the IP prefix.
   *
   * @param DestinationEndpoint[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return DestinationEndpoint[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * The etag is computed by the server, and might be sent with update and
   * delete requests so that the client has an up-to-date value before
   * proceeding.
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
   * Required. Immutable. The IP prefix that represents your workload on another
   * CSP.
   *
   * @param string $ipPrefix
   */
  public function setIpPrefix($ipPrefix)
  {
    $this->ipPrefix = $ipPrefix;
  }
  /**
   * @return string
   */
  public function getIpPrefix()
  {
    return $this->ipPrefix;
  }
  /**
   * Optional. User-defined labels.
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
   * Identifier. The name of the `Destination` resource. Format: `projects/{proj
   * ect}/locations/{location}/multicloudDataTransferConfigs/{multicloud_data_tr
   * ansfer_config}/destinations/{destination}`.
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
   * Output only. The timeline of the expected `Destination` states or the
   * current rest state. If a state change is expected, the value is `ADDING`,
   * `DELETING` or `SUSPENDING`, depending on the action specified. Example:
   * "state_timeline": { "states": [ { // The time when the `Destination`
   * resource will be activated. "effectiveTime": "2024-12-01T08:00:00Z",
   * "state": "ADDING" }, { // The time when the `Destination` resource will be
   * suspended. "effectiveTime": "2024-12-01T20:00:00Z", "state": "SUSPENDING" }
   * ] }
   *
   * @param StateTimeline $stateTimeline
   */
  public function setStateTimeline(StateTimeline $stateTimeline)
  {
    $this->stateTimeline = $stateTimeline;
  }
  /**
   * @return StateTimeline
   */
  public function getStateTimeline()
  {
    return $this->stateTimeline;
  }
  /**
   * Output only. The Google-generated unique ID for the `Destination` resource.
   * This value is unique across all `Destination` resources. If a resource is
   * deleted and another with the same name is created, the new resource is
   * assigned a different and unique ID.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Time when the `Destination` resource was updated.
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
class_alias(Destination::class, 'Google_Service_Networkconnectivity_Destination');
