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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TaskInfrastructureSpecVpcNetwork extends \Google\Collection
{
  protected $collection_key = 'networkTags';
  /**
   * Optional. The Cloud VPC network in which the job is run. By default, the
   * Cloud VPC network named Default within the project is used.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. List of network tags to apply to the job.
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * Optional. The Cloud VPC sub-network in which the job is run.
   *
   * @var string
   */
  public $subNetwork;

  /**
   * Optional. The Cloud VPC network in which the job is run. By default, the
   * Cloud VPC network named Default within the project is used.
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
   * Optional. List of network tags to apply to the job.
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * Optional. The Cloud VPC sub-network in which the job is run.
   *
   * @param string $subNetwork
   */
  public function setSubNetwork($subNetwork)
  {
    $this->subNetwork = $subNetwork;
  }
  /**
   * @return string
   */
  public function getSubNetwork()
  {
    return $this->subNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TaskInfrastructureSpecVpcNetwork::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TaskInfrastructureSpecVpcNetwork');
