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

namespace Google\Service\MigrationCenterAPI;

class ReportSummaryVmwareNodeAllocation extends \Google\Model
{
  /**
   * Count of assets allocated to these nodes
   *
   * @var string
   */
  public $allocatedAssetCount;
  /**
   * Count of this node type to be provisioned
   *
   * @var string
   */
  public $nodeCount;
  protected $vmwareNodeType = ReportSummaryVmwareNode::class;
  protected $vmwareNodeDataType = '';

  /**
   * Count of assets allocated to these nodes
   *
   * @param string $allocatedAssetCount
   */
  public function setAllocatedAssetCount($allocatedAssetCount)
  {
    $this->allocatedAssetCount = $allocatedAssetCount;
  }
  /**
   * @return string
   */
  public function getAllocatedAssetCount()
  {
    return $this->allocatedAssetCount;
  }
  /**
   * Count of this node type to be provisioned
   *
   * @param string $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return string
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * VMWare node type, e.g. "ve1-standard-72"
   *
   * @param ReportSummaryVmwareNode $vmwareNode
   */
  public function setVmwareNode(ReportSummaryVmwareNode $vmwareNode)
  {
    $this->vmwareNode = $vmwareNode;
  }
  /**
   * @return ReportSummaryVmwareNode
   */
  public function getVmwareNode()
  {
    return $this->vmwareNode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryVmwareNodeAllocation::class, 'Google_Service_MigrationCenterAPI_ReportSummaryVmwareNodeAllocation');
