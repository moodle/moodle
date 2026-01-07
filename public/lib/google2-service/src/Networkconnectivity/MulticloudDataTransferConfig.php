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

class MulticloudDataTransferConfig extends \Google\Model
{
  /**
   * Output only. Time when the `MulticloudDataTransferConfig` resource was
   * created.
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
  /**
   * Output only. The number of `Destination` resources in use with the
   * `MulticloudDataTransferConfig` resource.
   *
   * @var int
   */
  public $destinationsActiveCount;
  /**
   * Output only. The number of `Destination` resources configured for the
   * `MulticloudDataTransferConfig` resource.
   *
   * @var int
   */
  public $destinationsCount;
  /**
   * The etag is computed by the server, and might be sent with update and
   * delete requests so that the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the `MulticloudDataTransferConfig` resource.
   * Format: `projects/{project}/locations/{location}/multicloudDataTransferConf
   * igs/{multicloud_data_transfer_config}`.
   *
   * @var string
   */
  public $name;
  protected $servicesType = StateTimeline::class;
  protected $servicesDataType = 'map';
  /**
   * Output only. The Google-generated unique ID for the
   * `MulticloudDataTransferConfig` resource. This value is unique across all
   * `MulticloudDataTransferConfig` resources. If a resource is deleted and
   * another with the same name is created, the new resource is assigned a
   * different and unique ID.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time when the `MulticloudDataTransferConfig` resource was
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Time when the `MulticloudDataTransferConfig` resource was
   * created.
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
   * Output only. The number of `Destination` resources in use with the
   * `MulticloudDataTransferConfig` resource.
   *
   * @param int $destinationsActiveCount
   */
  public function setDestinationsActiveCount($destinationsActiveCount)
  {
    $this->destinationsActiveCount = $destinationsActiveCount;
  }
  /**
   * @return int
   */
  public function getDestinationsActiveCount()
  {
    return $this->destinationsActiveCount;
  }
  /**
   * Output only. The number of `Destination` resources configured for the
   * `MulticloudDataTransferConfig` resource.
   *
   * @param int $destinationsCount
   */
  public function setDestinationsCount($destinationsCount)
  {
    $this->destinationsCount = $destinationsCount;
  }
  /**
   * @return int
   */
  public function getDestinationsCount()
  {
    return $this->destinationsCount;
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
   * Identifier. The name of the `MulticloudDataTransferConfig` resource.
   * Format: `projects/{project}/locations/{location}/multicloudDataTransferConf
   * igs/{multicloud_data_transfer_config}`.
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
   * Optional. Maps services to their current or planned states. Service names
   * are keys, and the associated values describe the state of the service. If a
   * state change is expected, the value is either `ADDING` or `DELETING`,
   * depending on the actions taken. Sample output: "services": { "big-query": {
   * "states": [ { "effectiveTime": "2024-12-12T08:00:00Z" "state": "ADDING", },
   * ] }, "cloud-storage": { "states": [ { "state": "ACTIVE", } ] } }
   *
   * @param StateTimeline[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return StateTimeline[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Output only. The Google-generated unique ID for the
   * `MulticloudDataTransferConfig` resource. This value is unique across all
   * `MulticloudDataTransferConfig` resources. If a resource is deleted and
   * another with the same name is created, the new resource is assigned a
   * different and unique ID.
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
   * Output only. Time when the `MulticloudDataTransferConfig` resource was
   * updated.
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
class_alias(MulticloudDataTransferConfig::class, 'Google_Service_Networkconnectivity_MulticloudDataTransferConfig');
