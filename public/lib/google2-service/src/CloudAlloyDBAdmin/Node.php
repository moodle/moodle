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

namespace Google\Service\CloudAlloyDBAdmin;

class Node extends \Google\Model
{
  /**
   * Output only. The identifier of the VM e.g. "test-read-0601-407e52be-ms3l".
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The private IP address of the VM e.g. "10.57.0.34".
   *
   * @var string
   */
  public $ip;
  /**
   * Output only. Determined by state of the compute VM and postgres-service
   * health. Compute VM state can have values listed in
   * https://cloud.google.com/compute/docs/instances/instance-life-cycle and
   * postgres-service health can have values: HEALTHY and UNHEALTHY.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The Compute Engine zone of the VM e.g. "us-central1-b".
   *
   * @var string
   */
  public $zoneId;

  /**
   * Output only. The identifier of the VM e.g. "test-read-0601-407e52be-ms3l".
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
   * Output only. The private IP address of the VM e.g. "10.57.0.34".
   *
   * @param string $ip
   */
  public function setIp($ip)
  {
    $this->ip = $ip;
  }
  /**
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }
  /**
   * Output only. Determined by state of the compute VM and postgres-service
   * health. Compute VM state can have values listed in
   * https://cloud.google.com/compute/docs/instances/instance-life-cycle and
   * postgres-service health can have values: HEALTHY and UNHEALTHY.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The Compute Engine zone of the VM e.g. "us-central1-b".
   *
   * @param string $zoneId
   */
  public function setZoneId($zoneId)
  {
    $this->zoneId = $zoneId;
  }
  /**
   * @return string
   */
  public function getZoneId()
  {
    return $this->zoneId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Node::class, 'Google_Service_CloudAlloyDBAdmin_Node');
