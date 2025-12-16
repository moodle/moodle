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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRevision extends \Google\Model
{
  /**
   * Percentage of MP replicas reporting this revision.
   *
   * @var int
   */
  public $percentage;
  /**
   * API proxy revision reported as deployed.
   *
   * @var string
   */
  public $revision;

  /**
   * Percentage of MP replicas reporting this revision.
   *
   * @param int $percentage
   */
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  /**
   * @return int
   */
  public function getPercentage()
  {
    return $this->percentage;
  }
  /**
   * API proxy revision reported as deployed.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRevision::class, 'Google_Service_Apigee_GoogleCloudApigeeV1InstanceDeploymentStatusDeployedRevision');
