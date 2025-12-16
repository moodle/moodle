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

class EnrollBareMetalAdminClusterRequest extends \Google\Model
{
  /**
   * User provided OnePlatform identifier that is used as part of the resource
   * name. This must be unique among all GKE on-prem clusters within a project
   * and location and will return a 409 if the cluster already exists.
   * (https://tools.ietf.org/html/rfc1123) format.
   *
   * @var string
   */
  public $bareMetalAdminClusterId;
  /**
   * Required. This is the full resource name of this admin cluster's fleet
   * membership.
   *
   * @var string
   */
  public $membership;

  /**
   * User provided OnePlatform identifier that is used as part of the resource
   * name. This must be unique among all GKE on-prem clusters within a project
   * and location and will return a 409 if the cluster already exists.
   * (https://tools.ietf.org/html/rfc1123) format.
   *
   * @param string $bareMetalAdminClusterId
   */
  public function setBareMetalAdminClusterId($bareMetalAdminClusterId)
  {
    $this->bareMetalAdminClusterId = $bareMetalAdminClusterId;
  }
  /**
   * @return string
   */
  public function getBareMetalAdminClusterId()
  {
    return $this->bareMetalAdminClusterId;
  }
  /**
   * Required. This is the full resource name of this admin cluster's fleet
   * membership.
   *
   * @param string $membership
   */
  public function setMembership($membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return string
   */
  public function getMembership()
  {
    return $this->membership;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnrollBareMetalAdminClusterRequest::class, 'Google_Service_GKEOnPrem_EnrollBareMetalAdminClusterRequest');
