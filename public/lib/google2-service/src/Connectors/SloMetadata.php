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

namespace Google\Service\Connectors;

class SloMetadata extends \Google\Collection
{
  protected $collection_key = 'nodes';
  protected $nodesType = NodeSloMetadata::class;
  protected $nodesDataType = 'array';
  protected $perSliEligibilityType = PerSliSloEligibility::class;
  protected $perSliEligibilityDataType = '';
  /**
   * Name of the SLO tier the Instance belongs to. This name will be expected to
   * match the tiers specified in the service SLO configuration. Field is
   * mandatory and must not be empty.
   *
   * @var string
   */
  public $tier;

  /**
   * Optional. List of nodes. Some producers need to use per-node metadata to
   * calculate SLO. This field allows such producers to publish per-node SLO
   * meta data, which will be consumed by SSA Eligibility Exporter and published
   * in the form of per node metric to Monarch.
   *
   * @param NodeSloMetadata[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return NodeSloMetadata[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Optional. Multiple per-instance SLI eligibilities which apply for
   * individual SLIs.
   *
   * @param PerSliSloEligibility $perSliEligibility
   */
  public function setPerSliEligibility(PerSliSloEligibility $perSliEligibility)
  {
    $this->perSliEligibility = $perSliEligibility;
  }
  /**
   * @return PerSliSloEligibility
   */
  public function getPerSliEligibility()
  {
    return $this->perSliEligibility;
  }
  /**
   * Name of the SLO tier the Instance belongs to. This name will be expected to
   * match the tiers specified in the service SLO configuration. Field is
   * mandatory and must not be empty.
   *
   * @param string $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return string
   */
  public function getTier()
  {
    return $this->tier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SloMetadata::class, 'Google_Service_Connectors_SloMetadata');
