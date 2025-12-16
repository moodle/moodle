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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1NetworkSpec extends \Google\Model
{
  /**
   * Whether to enable public internet access. Default false.
   *
   * @var bool
   */
  public $enableInternetAccess;
  /**
   * The full name of the Google Compute Engine
   * [network](https://cloud.google.com//compute/docs/networks-and-
   * firewalls#networks)
   *
   * @var string
   */
  public $network;
  /**
   * The name of the subnet that this instance is in. Format: `projects/{project
   * _id_or_number}/regions/{region}/subnetworks/{subnetwork_id}`
   *
   * @var string
   */
  public $subnetwork;

  /**
   * Whether to enable public internet access. Default false.
   *
   * @param bool $enableInternetAccess
   */
  public function setEnableInternetAccess($enableInternetAccess)
  {
    $this->enableInternetAccess = $enableInternetAccess;
  }
  /**
   * @return bool
   */
  public function getEnableInternetAccess()
  {
    return $this->enableInternetAccess;
  }
  /**
   * The full name of the Google Compute Engine
   * [network](https://cloud.google.com//compute/docs/networks-and-
   * firewalls#networks)
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
  /**
   * The name of the subnet that this instance is in. Format: `projects/{project
   * _id_or_number}/regions/{region}/subnetworks/{subnetwork_id}`
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NetworkSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NetworkSpec');
