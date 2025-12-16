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

namespace Google\Service\ServerlessVPCAccess;

class Subnet extends \Google\Model
{
  /**
   * Optional. Subnet name (relative, not fully qualified). E.g. if the full
   * subnet selfLink is https://compute.googleapis.com/compute/v1/projects/{proj
   * ect}/regions/{region}/subnetworks/{subnetName} the correct input for this
   * field would be {subnetName}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Project in which the subnet exists. If not set, this project is
   * assumed to be the project for which the connector create request was
   * issued.
   *
   * @var string
   */
  public $projectId;

  /**
   * Optional. Subnet name (relative, not fully qualified). E.g. if the full
   * subnet selfLink is https://compute.googleapis.com/compute/v1/projects/{proj
   * ect}/regions/{region}/subnetworks/{subnetName} the correct input for this
   * field would be {subnetName}
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
   * Optional. Project in which the subnet exists. If not set, this project is
   * assumed to be the project for which the connector create request was
   * issued.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subnet::class, 'Google_Service_ServerlessVPCAccess_Subnet');
