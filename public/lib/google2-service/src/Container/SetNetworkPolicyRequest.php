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

namespace Google\Service\Container;

class SetNetworkPolicyRequest extends \Google\Model
{
  /**
   * Deprecated. The name of the cluster. This field has been deprecated and
   * replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $clusterId;
  /**
   * The name (project, location, cluster name) of the cluster to set networking
   * policy. Specified in the format `projects/locations/clusters`.
   *
   * @var string
   */
  public $name;
  protected $networkPolicyType = NetworkPolicy::class;
  protected $networkPolicyDataType = '';
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $projectId;
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the name
   * field.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Deprecated. The name of the cluster. This field has been deprecated and
   * replaced by the name field.
   *
   * @deprecated
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * The name (project, location, cluster name) of the cluster to set networking
   * policy. Specified in the format `projects/locations/clusters`.
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
   * Required. Configuration options for the NetworkPolicy feature.
   *
   * @param NetworkPolicy $networkPolicy
   */
  public function setNetworkPolicy(NetworkPolicy $networkPolicy)
  {
    $this->networkPolicy = $networkPolicy;
  }
  /**
   * @return NetworkPolicy
   */
  public function getNetworkPolicy()
  {
    return $this->networkPolicy;
  }
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the name
   * field.
   *
   * @deprecated
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetNetworkPolicyRequest::class, 'Google_Service_Container_SetNetworkPolicyRequest');
