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

class LinkedVpcNetwork extends \Google\Collection
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
   * Output only. The list of Producer VPC spokes that this VPC spoke is a
   * service consumer VPC spoke for. These producer VPCs are connected through
   * VPC peering to this spoke's backing VPC network. Because they are directly
   * connected through VPC peering, NCC export filters do not apply between the
   * service consumer VPC spoke and any of its producer VPC spokes. This VPC
   * spoke cannot be deleted as long as any of these producer VPC spokes are
   * connected to the NCC Hub.
   *
   * @var string[]
   */
  public $producerVpcSpokes;
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
   * Required. The URI of the VPC network resource.
   *
   * @var string
   */
  public $uri;

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
   * Output only. The list of Producer VPC spokes that this VPC spoke is a
   * service consumer VPC spoke for. These producer VPCs are connected through
   * VPC peering to this spoke's backing VPC network. Because they are directly
   * connected through VPC peering, NCC export filters do not apply between the
   * service consumer VPC spoke and any of its producer VPC spokes. This VPC
   * spoke cannot be deleted as long as any of these producer VPC spokes are
   * connected to the NCC Hub.
   *
   * @param string[] $producerVpcSpokes
   */
  public function setProducerVpcSpokes($producerVpcSpokes)
  {
    $this->producerVpcSpokes = $producerVpcSpokes;
  }
  /**
   * @return string[]
   */
  public function getProducerVpcSpokes()
  {
    return $this->producerVpcSpokes;
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
   * Required. The URI of the VPC network resource.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkedVpcNetwork::class, 'Google_Service_Networkconnectivity_LinkedVpcNetwork');
