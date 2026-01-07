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

namespace Google\Service\GKEOnPrem;

class EnrollBareMetalClusterRequest extends \Google\Model
{
  /**
   * Required. The admin cluster this bare metal user cluster belongs to. This
   * is the full resource name of the admin cluster's fleet membership. In the
   * future, references to other resource types might be allowed if admin
   * clusters are modeled as their own resources.
   *
   * @var string
   */
  public $adminClusterMembership;
  /**
   * User provided OnePlatform identifier that is used as part of the resource
   * name. This must be unique among all bare metal clusters within a project
   * and location and will return a 409 if the cluster already exists.
   * (https://tools.ietf.org/html/rfc1123) format.
   *
   * @var string
   */
  public $bareMetalClusterId;
  /**
   * Optional. The object name of the bare metal cluster custom resource on the
   * associated admin cluster. This field is used to support conflicting
   * resource names when enrolling existing clusters to the API. When not
   * provided, this field will resolve to the bare_metal_cluster_id. Otherwise,
   * it must match the object name of the bare metal cluster custom resource. It
   * is not modifiable outside / beyond the enrollment operation.
   *
   * @var string
   */
  public $localName;
  /**
   * Optional. The namespace of the cluster.
   *
   * @var string
   */
  public $localNamespace;

  /**
   * Required. The admin cluster this bare metal user cluster belongs to. This
   * is the full resource name of the admin cluster's fleet membership. In the
   * future, references to other resource types might be allowed if admin
   * clusters are modeled as their own resources.
   *
   * @param string $adminClusterMembership
   */
  public function setAdminClusterMembership($adminClusterMembership)
  {
    $this->adminClusterMembership = $adminClusterMembership;
  }
  /**
   * @return string
   */
  public function getAdminClusterMembership()
  {
    return $this->adminClusterMembership;
  }
  /**
   * User provided OnePlatform identifier that is used as part of the resource
   * name. This must be unique among all bare metal clusters within a project
   * and location and will return a 409 if the cluster already exists.
   * (https://tools.ietf.org/html/rfc1123) format.
   *
   * @param string $bareMetalClusterId
   */
  public function setBareMetalClusterId($bareMetalClusterId)
  {
    $this->bareMetalClusterId = $bareMetalClusterId;
  }
  /**
   * @return string
   */
  public function getBareMetalClusterId()
  {
    return $this->bareMetalClusterId;
  }
  /**
   * Optional. The object name of the bare metal cluster custom resource on the
   * associated admin cluster. This field is used to support conflicting
   * resource names when enrolling existing clusters to the API. When not
   * provided, this field will resolve to the bare_metal_cluster_id. Otherwise,
   * it must match the object name of the bare metal cluster custom resource. It
   * is not modifiable outside / beyond the enrollment operation.
   *
   * @param string $localName
   */
  public function setLocalName($localName)
  {
    $this->localName = $localName;
  }
  /**
   * @return string
   */
  public function getLocalName()
  {
    return $this->localName;
  }
  /**
   * Optional. The namespace of the cluster.
   *
   * @param string $localNamespace
   */
  public function setLocalNamespace($localNamespace)
  {
    $this->localNamespace = $localNamespace;
  }
  /**
   * @return string
   */
  public function getLocalNamespace()
  {
    return $this->localNamespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnrollBareMetalClusterRequest::class, 'Google_Service_GKEOnPrem_EnrollBareMetalClusterRequest');
