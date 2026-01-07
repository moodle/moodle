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

namespace Google\Service\BeyondCorp;

class GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo extends \Google\Collection
{
  /**
   * Health status is unknown: not initialized or failed to retrieve.
   */
  public const STATUS_HEALTH_STATUS_UNSPECIFIED = 'HEALTH_STATUS_UNSPECIFIED';
  /**
   * The resource is healthy.
   */
  public const STATUS_HEALTHY = 'HEALTHY';
  /**
   * The resource is unhealthy.
   */
  public const STATUS_UNHEALTHY = 'UNHEALTHY';
  /**
   * The resource is unresponsive.
   */
  public const STATUS_UNRESPONSIVE = 'UNRESPONSIVE';
  /**
   * Some sub-resources are UNHEALTHY.
   */
  public const STATUS_DEGRADED = 'DEGRADED';
  protected $collection_key = 'sub';
  /**
   * Required. Unique Id for the resource.
   *
   * @var string
   */
  public $id;
  /**
   * Specific details for the resource. This is for internal use only.
   *
   * @var array[]
   */
  public $resource;
  /**
   * Overall health status. Overall status is derived based on the status of
   * each sub level resources.
   *
   * @var string
   */
  public $status;
  protected $subType = GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo::class;
  protected $subDataType = 'array';
  /**
   * The timestamp to collect the info. It is suggested to be set by the topmost
   * level resource only.
   *
   * @var string
   */
  public $time;

  /**
   * Required. Unique Id for the resource.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Specific details for the resource. This is for internal use only.
   *
   * @param array[] $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return array[]
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Overall health status. Overall status is derived based on the status of
   * each sub level resources.
   *
   * Accepted values: HEALTH_STATUS_UNSPECIFIED, HEALTHY, UNHEALTHY,
   * UNRESPONSIVE, DEGRADED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * List of Info for the sub level resources.
   *
   * @param GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo[] $sub
   */
  public function setSub($sub)
  {
    $this->sub = $sub;
  }
  /**
   * @return GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo[]
   */
  public function getSub()
  {
    return $this->sub;
  }
  /**
   * The timestamp to collect the info. It is suggested to be set by the topmost
   * level resource only.
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
class_alias(GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpAppconnectorsV1ResourceInfo');
