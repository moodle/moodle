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

class NetworkConfig extends \Google\Model
{
  /**
   * Optional. Name of the allocated IP range for the private IP AlloyDB
   * cluster, for example: "google-managed-services-default". If set, the
   * instance IPs for this cluster will be created in the allocated range. The
   * range name must comply with RFC 1035. Specifically, the name must be 1-63
   * characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. Field name is intended to be consistent with
   * Cloud SQL.
   *
   * @var string
   */
  public $allocatedIpRange;
  /**
   * Optional. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: `projects/{project_number}/global/networks/{network_id}`. This is
   * required to create a cluster.
   *
   * @var string
   */
  public $network;

  /**
   * Optional. Name of the allocated IP range for the private IP AlloyDB
   * cluster, for example: "google-managed-services-default". If set, the
   * instance IPs for this cluster will be created in the allocated range. The
   * range name must comply with RFC 1035. Specifically, the name must be 1-63
   * characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?`. Field name is intended to be consistent with
   * Cloud SQL.
   *
   * @param string $allocatedIpRange
   */
  public function setAllocatedIpRange($allocatedIpRange)
  {
    $this->allocatedIpRange = $allocatedIpRange;
  }
  /**
   * @return string
   */
  public function getAllocatedIpRange()
  {
    return $this->allocatedIpRange;
  }
  /**
   * Optional. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: `projects/{project_number}/global/networks/{network_id}`. This is
   * required to create a cluster.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_CloudAlloyDBAdmin_NetworkConfig');
