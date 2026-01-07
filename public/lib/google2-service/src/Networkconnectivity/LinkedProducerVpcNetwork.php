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

namespace Google\Service\Networkconnectivity;

class LinkedProducerVpcNetwork extends \Google\Collection
{
  protected $collection_key = 'proposedIncludeExportRanges';
  /**
   * Optional. IP ranges encompassing the subnets to be excluded from peering.
   *
   * @var string[]
   */
  public $excludeExportRanges;
  /**
   * Optional. IP ranges allowed to be included from peering.
   *
   * @var string[]
   */
  public $includeExportRanges;
  /**
   * Immutable. The URI of the Service Consumer VPC that the Producer VPC is
   * peered with.
   *
   * @var string
   */
  public $network;
  /**
   * Immutable. The name of the VPC peering between the Service Consumer VPC and
   * the Producer VPC (defined in the Tenant project) which is added to the NCC
   * hub. This peering must be in ACTIVE state.
   *
   * @var string
   */
  public $peering;
  /**
   * Output only. The URI of the Producer VPC.
   *
   * @var string
   */
  public $producerNetwork;
  /**
   * Output only. The proposed exclude export IP ranges waiting for hub
   * administration's approval.
   *
   * @var string[]
   */
  public $proposedExcludeExportRanges;
  /**
   * Output only. The proposed include export IP ranges waiting for hub
   * administration's approval.
   *
   * @var string[]
   */
  public $proposedIncludeExportRanges;
  /**
   * Output only. The Service Consumer Network spoke.
   *
   * @var string
   */
  public $serviceConsumerVpcSpoke;

  /**
   * Optional. IP ranges encompassing the subnets to be excluded from peering.
   *
   * @param string[] $excludeExportRanges
   */
  public function setExcludeExportRanges($excludeExportRanges)
  {
    $this->excludeExportRanges = $excludeExportRanges;
  }
  /**
   * @return string[]
   */
  public function getExcludeExportRanges()
  {
    return $this->excludeExportRanges;
  }
  /**
   * Optional. IP ranges allowed to be included from peering.
   *
   * @param string[] $includeExportRanges
   */
  public function setIncludeExportRanges($includeExportRanges)
  {
    $this->includeExportRanges = $includeExportRanges;
  }
  /**
   * @return string[]
   */
  public function getIncludeExportRanges()
  {
    return $this->includeExportRanges;
  }
  /**
   * Immutable. The URI of the Service Consumer VPC that the Producer VPC is
   * peered with.
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
   * Immutable. The name of the VPC peering between the Service Consumer VPC and
   * the Producer VPC (defined in the Tenant project) which is added to the NCC
   * hub. This peering must be in ACTIVE state.
   *
   * @param string $peering
   */
  public function setPeering($peering)
  {
    $this->peering = $peering;
  }
  /**
   * @return string
   */
  public function getPeering()
  {
    return $this->peering;
  }
  /**
   * Output only. The URI of the Producer VPC.
   *
   * @param string $producerNetwork
   */
  public function setProducerNetwork($producerNetwork)
  {
    $this->producerNetwork = $producerNetwork;
  }
  /**
   * @return string
   */
  public function getProducerNetwork()
  {
    return $this->producerNetwork;
  }
  /**
   * Output only. The proposed exclude export IP ranges waiting for hub
   * administration's approval.
   *
   * @param string[] $proposedExcludeExportRanges
   */
  public function setProposedExcludeExportRanges($proposedExcludeExportRanges)
  {
    $this->proposedExcludeExportRanges = $proposedExcludeExportRanges;
  }
  /**
   * @return string[]
   */
  public function getProposedExcludeExportRanges()
  {
    return $this->proposedExcludeExportRanges;
  }
  /**
   * Output only. The proposed include export IP ranges waiting for hub
   * administration's approval.
   *
   * @param string[] $proposedIncludeExportRanges
   */
  public function setProposedIncludeExportRanges($proposedIncludeExportRanges)
  {
    $this->proposedIncludeExportRanges = $proposedIncludeExportRanges;
  }
  /**
   * @return string[]
   */
  public function getProposedIncludeExportRanges()
  {
    return $this->proposedIncludeExportRanges;
  }
  /**
   * Output only. The Service Consumer Network spoke.
   *
   * @param string $serviceConsumerVpcSpoke
   */
  public function setServiceConsumerVpcSpoke($serviceConsumerVpcSpoke)
  {
    $this->serviceConsumerVpcSpoke = $serviceConsumerVpcSpoke;
  }
  /**
   * @return string
   */
  public function getServiceConsumerVpcSpoke()
  {
    return $this->serviceConsumerVpcSpoke;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkedProducerVpcNetwork::class, 'Google_Service_Networkconnectivity_LinkedProducerVpcNetwork');
