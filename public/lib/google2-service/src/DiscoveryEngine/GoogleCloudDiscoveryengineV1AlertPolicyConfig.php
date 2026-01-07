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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AlertPolicyConfig extends \Google\Collection
{
  protected $collection_key = 'alertEnrollments';
  protected $alertEnrollmentsType = GoogleCloudDiscoveryengineV1AlertPolicyConfigAlertEnrollment::class;
  protected $alertEnrollmentsDataType = 'array';
  /**
   * Immutable. The fully qualified resource name of the AlertPolicy.
   *
   * @var string
   */
  public $alertPolicyName;

  /**
   * Optional. The enrollment states of each alert.
   *
   * @param GoogleCloudDiscoveryengineV1AlertPolicyConfigAlertEnrollment[] $alertEnrollments
   */
  public function setAlertEnrollments($alertEnrollments)
  {
    $this->alertEnrollments = $alertEnrollments;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AlertPolicyConfigAlertEnrollment[]
   */
  public function getAlertEnrollments()
  {
    return $this->alertEnrollments;
  }
  /**
   * Immutable. The fully qualified resource name of the AlertPolicy.
   *
   * @param string $alertPolicyName
   */
  public function setAlertPolicyName($alertPolicyName)
  {
    $this->alertPolicyName = $alertPolicyName;
  }
  /**
   * @return string
   */
  public function getAlertPolicyName()
  {
    return $this->alertPolicyName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AlertPolicyConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AlertPolicyConfig');
