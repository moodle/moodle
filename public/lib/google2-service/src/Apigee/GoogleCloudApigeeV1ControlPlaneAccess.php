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

class GoogleCloudApigeeV1ControlPlaneAccess extends \Google\Collection
{
  protected $collection_key = 'synchronizerIdentities';
  /**
   * Optional. Array of service accounts authorized to publish analytics data to
   * the control plane (for the Message Processor component).
   *
   * @var string[]
   */
  public $analyticsPublisherIdentities;
  /**
   * Identifier. The resource name of the ControlPlaneAccess. Format:
   * "organizations/{org}/controlPlaneAccess"
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Array of service accounts to grant access to control plane
   * resources (for the Synchronizer component). The service accounts must have
   * **Apigee Synchronizer Manager** role. See also [Create service
   * accounts](https://cloud.google.com/apigee/docs/hybrid/latest/sa-
   * about#create-the-service-accounts).
   *
   * @var string[]
   */
  public $synchronizerIdentities;

  /**
   * Optional. Array of service accounts authorized to publish analytics data to
   * the control plane (for the Message Processor component).
   *
   * @param string[] $analyticsPublisherIdentities
   */
  public function setAnalyticsPublisherIdentities($analyticsPublisherIdentities)
  {
    $this->analyticsPublisherIdentities = $analyticsPublisherIdentities;
  }
  /**
   * @return string[]
   */
  public function getAnalyticsPublisherIdentities()
  {
    return $this->analyticsPublisherIdentities;
  }
  /**
   * Identifier. The resource name of the ControlPlaneAccess. Format:
   * "organizations/{org}/controlPlaneAccess"
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
   * Optional. Array of service accounts to grant access to control plane
   * resources (for the Synchronizer component). The service accounts must have
   * **Apigee Synchronizer Manager** role. See also [Create service
   * accounts](https://cloud.google.com/apigee/docs/hybrid/latest/sa-
   * about#create-the-service-accounts).
   *
   * @param string[] $synchronizerIdentities
   */
  public function setSynchronizerIdentities($synchronizerIdentities)
  {
    $this->synchronizerIdentities = $synchronizerIdentities;
  }
  /**
   * @return string[]
   */
  public function getSynchronizerIdentities()
  {
    return $this->synchronizerIdentities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ControlPlaneAccess::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ControlPlaneAccess');
