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

namespace Google\Service\OSConfig;

class OSPolicyAssignmentInstanceFilter extends \Google\Collection
{
  protected $collection_key = 'inventories';
  /**
   * Target all VMs in the project. If true, no other criteria is permitted.
   *
   * @var bool
   */
  public $all;
  protected $exclusionLabelsType = OSPolicyAssignmentLabelSet::class;
  protected $exclusionLabelsDataType = 'array';
  protected $inclusionLabelsType = OSPolicyAssignmentLabelSet::class;
  protected $inclusionLabelsDataType = 'array';
  protected $inventoriesType = OSPolicyAssignmentInstanceFilterInventory::class;
  protected $inventoriesDataType = 'array';

  /**
   * Target all VMs in the project. If true, no other criteria is permitted.
   *
   * @param bool $all
   */
  public function setAll($all)
  {
    $this->all = $all;
  }
  /**
   * @return bool
   */
  public function getAll()
  {
    return $this->all;
  }
  /**
   * List of label sets used for VM exclusion. If the list has more than one
   * label set, the VM is excluded if any of the label sets are applicable for
   * the VM.
   *
   * @param OSPolicyAssignmentLabelSet[] $exclusionLabels
   */
  public function setExclusionLabels($exclusionLabels)
  {
    $this->exclusionLabels = $exclusionLabels;
  }
  /**
   * @return OSPolicyAssignmentLabelSet[]
   */
  public function getExclusionLabels()
  {
    return $this->exclusionLabels;
  }
  /**
   * List of label sets used for VM inclusion. If the list has more than one
   * `LabelSet`, the VM is included if any of the label sets are applicable for
   * the VM.
   *
   * @param OSPolicyAssignmentLabelSet[] $inclusionLabels
   */
  public function setInclusionLabels($inclusionLabels)
  {
    $this->inclusionLabels = $inclusionLabels;
  }
  /**
   * @return OSPolicyAssignmentLabelSet[]
   */
  public function getInclusionLabels()
  {
    return $this->inclusionLabels;
  }
  /**
   * List of inventories to select VMs. A VM is selected if its inventory data
   * matches at least one of the following inventories.
   *
   * @param OSPolicyAssignmentInstanceFilterInventory[] $inventories
   */
  public function setInventories($inventories)
  {
    $this->inventories = $inventories;
  }
  /**
   * @return OSPolicyAssignmentInstanceFilterInventory[]
   */
  public function getInventories()
  {
    return $this->inventories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyAssignmentInstanceFilter::class, 'Google_Service_OSConfig_OSPolicyAssignmentInstanceFilter');
